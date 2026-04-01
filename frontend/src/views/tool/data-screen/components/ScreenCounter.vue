<template>
  <div class="counter-wrapper" :style="{ color: theme === 'dark' ? '#409eff' : '#409eff' }">
    <div class="counter-title" v-if="config.title">{{ config.title }}</div>
    <div class="counter-value">
      <span class="prefix" v-if="config.prefix">{{ config.prefix }}</span>
      <span class="number" ref="numberRef">{{ displayValue }}</span>
      <span class="suffix" v-if="config.suffix">{{ config.suffix }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const displayValue = ref(0)
const targetValue = ref(0)
let animationFrame = null

function animateTo(target) {
  const start = displayValue.value
  const diff = target - start
  const duration = 1500
  const startTime = Date.now()

  function step() {
    const elapsed = Date.now() - startTime
    const progress = Math.min(elapsed / duration, 1)
    const eased = 1 - Math.pow(1 - progress, 3) // easeOutCubic
    displayValue.value = Math.round(start + diff * eased)

    if (progress < 1) {
      animationFrame = requestAnimationFrame(step)
    }
  }

  if (animationFrame) {
    cancelAnimationFrame(animationFrame)
  }
  step()
}

function updateValue() {
  if (props.data.length > 0 && props.data[0].value !== undefined) {
    targetValue.value = Number(props.data[0].value)
  } else {
    targetValue.value = Number(props.config.defaultValue || 0)
  }
  animateTo(targetValue.value)
}

onMounted(() => {
  updateValue()
})

onUnmounted(() => {
  if (animationFrame) {
    cancelAnimationFrame(animationFrame)
  }
})

watch(() => props.data, updateValue, { deep: true })
</script>

<style scoped>
.counter-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
}

.counter-title {
  font-size: 16px;
  margin-bottom: 10px;
  opacity: 0.8;
}

.counter-value {
  font-size: 48px;
  font-weight: bold;
  display: flex;
  align-items: baseline;
}

.prefix, .suffix {
  font-size: 24px;
  margin: 0 5px;
}

.number {
  font-variant-numeric: tabular-nums;
}
</style>

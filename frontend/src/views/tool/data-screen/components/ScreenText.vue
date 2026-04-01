<template>
  <div class="text-wrapper" :style="textStyle">
    <div class="text-content" :style="contentStyle">{{ content }}</div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const content = computed(() => {
  if (props.config.text) return props.config.text
  if (props.data.length > 0) return props.data[0].value || '文本内容'
  return '文本内容'
})

const textStyle = computed(() => ({
  textAlign: props.config.textAlign || 'center',
  lineHeight: props.config.lineHeight || '1.6',
  fontSize: (props.config.fontSize || 14) + 'px'
}))

const contentStyle = computed(() => ({
  color: props.config.color || (props.theme === 'dark' ? '#fff' : '#333'),
  fontWeight: props.config.fontWeight || 'normal',
  fontStyle: props.config.fontStyle || 'normal',
  textDecoration: props.config.textDecoration || 'none'
}))
</script>

<style scoped>
.text-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  padding: 10px;
  box-sizing: border-box;
}

.text-content {
  word-break: break-word;
}
</style>

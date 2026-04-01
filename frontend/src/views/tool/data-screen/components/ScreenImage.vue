<template>
  <div class="image-wrapper">
    <img v-if="src" :src="src" :style="imgStyle" @error="handleError" />
    <div v-else class="image-placeholder">
      <el-icon :size="40"><Picture /></el-icon>
      <span>请配置图片</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const error = ref(false)

const src = computed(() => {
  if (error.value) return ''
  if (props.config.src) return props.config.src
  if (props.data.length > 0 && props.data[0].url) return props.data[0].url
  return ''
})

const imgStyle = computed(() => ({
  objectFit: props.config.fit || 'contain',
  borderRadius: (props.config.borderRadius || 0) + 'px'
}))

function handleError() {
  error.value = true
}
</script>

<style scoped>
.image-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-wrapper img {
  max-width: 100%;
  max-height: 100%;
}

.image-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  color: #999;
}
</style>

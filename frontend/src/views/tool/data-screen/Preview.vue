<template>
  <div class="screen-preview" :class="{ 'dark-theme': theme === 'dark' }">
    <div v-if="loading" class="loading">加载中...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="screen-container" :style="containerStyle">
      <div
        v-for="comp in components"
        :key="comp.id"
        class="screen-component"
        :style="getComponentStyle(comp)"
      >
        <component
          :is="getComponentType(comp.type)"
          :config="comp.config"
          :data="comp.data"
          :theme="theme"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { screenDetail } from '@/api/tool/dataScreen'
import ScreenLine from './components/ScreenLine.vue'
import ScreenBar from './components/ScreenBar.vue'
import ScreenPie from './components/ScreenPie.vue'
import ScreenMap from './components/ScreenMap.vue'
import ScreenGauge from './components/ScreenGauge.vue'
import ScreenCounter from './components/ScreenCounter.vue'
import ScreenTable from './components/ScreenTable.vue'
import ScreenRanking from './components/ScreenRanking.vue'
import ScreenText from './components/ScreenText.vue'
import ScreenImage from './components/ScreenImage.vue'

const route = useRoute()

const loading = ref(true)
const error = ref('')
const screenData = ref(null)
const theme = ref('dark')
const components = ref([])
const canvasWidth = ref(1920)
const canvasHeight = ref(1080)

const containerStyle = computed(() => ({
  width: canvasWidth.value + 'px',
  height: canvasHeight.value + 'px',
  transform: `scale(${getScale()})`,
  transformOrigin: 'top left'
}))

function getScale() {
  const width = window.innerWidth
  const height = window.innerHeight
  const scaleX = width / canvasWidth.value
  const scaleY = height / canvasHeight.value
  return Math.min(scaleX, scaleY)
}

const componentMap = {
  line: ScreenLine,
  bar: ScreenBar,
  pie: ScreenPie,
  map: ScreenMap,
  gauge: ScreenGauge,
  counter: ScreenCounter,
  table: ScreenTable,
  ranking: ScreenRanking,
  text: ScreenText,
  image: ScreenImage
}

function getComponentType(type) {
  return componentMap[type] || ScreenText
}

function getComponentStyle(comp) {
  return {
    left: comp.config.x + 'px',
    top: comp.config.y + 'px',
    width: comp.config.width + 'px',
    height: comp.config.height + 'px',
    backgroundColor: comp.config.backgroundColor || 'transparent',
    opacity: comp.config.opacity || 1,
    border: comp.config.border || 'none',
    borderRadius: (comp.config.borderRadius || 0) + 'px'
  }
}

async function loadData() {
  const id = route.params.id
  if (!id) {
    error.value = '参数错误'
    loading.value = false
    return
  }

  try {
    const res = await screenDetail(id)
    screenData.value = res
    theme.value = res.config?.theme || 'dark'
    canvasWidth.value = res.config?.width || 1920
    canvasHeight.value = res.config?.height || 1080
    components.value = (res.components || []).map(c => ({
      id: c.id,
      type: c.type,
      name: c.name,
      config: {
        x: c.config?.x || 100,
        y: c.config?.y || 100,
        width: c.config?.width || 400,
        height: c.config?.height || 300,
        backgroundColor: c.config?.backgroundColor || 'transparent',
        opacity: c.config?.opacity || 1,
        border: c.config?.border || '',
        borderRadius: c.config?.borderRadius || 0,
        ...c.config
      },
      data: c.data_source?.data ? JSON.parse(c.data_source?.data) : []
    }))
  } catch (e) {
    error.value = e.message || '加载失败'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
  window.addEventListener('resize', () => {
    // 触发响应式更新
  })
})
</script>

<style scoped>
.screen-preview {
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: #f0f2f5;
}

.screen-preview.dark-theme {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}

.loading, .error {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  font-size: 18px;
}

.error {
  color: #f56c6c;
}

.screen-container {
  position: relative;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}

.screen-component {
  position: absolute;
  overflow: hidden;
}
</style>

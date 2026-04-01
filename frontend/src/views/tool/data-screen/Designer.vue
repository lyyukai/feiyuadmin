<template>
  <div class="data-screen-designer" :class="{ 'dark-theme': theme === 'dark' }">
    <!-- 顶部工具栏 -->
    <div class="designer-header">
      <div class="header-left">
        <el-button @click="goBack">返回</el-button>
        <span class="screen-title">{{ screenData.name }}</span>
      </div>
      <div class="header-center">
        <el-button-group>
          <el-button :type="theme === 'light' ? 'primary' : ''" @click="theme = 'light'">浅色</el-button>
          <el-button :type="theme === 'dark' ? 'primary' : ''" @click="theme = 'dark'">深色</el-button>
        </el-button-group>
      </div>
      <div class="header-right">
        <el-button @click="handlePreview">预览</el-button>
        <el-button type="primary" @click="handleSave">保存</el-button>
      </div>
    </div>

    <div class="designer-body">
      <!-- 左侧组件面板 -->
      <div class="component-panel">
        <div class="panel-title">组件库</div>
        <div class="component-list">
          <div class="component-group">
            <div class="group-title">图表</div>
            <div class="component-items">
              <div class="component-item" draggable @dragstart="onDragStart($event, 'line')">
                <el-icon><TrendCharts /></el-icon>
                <span>折线图</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'bar')">
                <el-icon><BarChart /></el-icon>
                <span>柱状图</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'pie')">
                <el-icon><PieChart /></el-icon>
                <span>饼图</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'map')">
                <el-icon><MapLocation /></el-icon>
                <span>中国地图</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'gauge')">
                <el-icon><Odometer /></el-icon>
                <span>仪表盘</span>
              </div>
            </div>
          </div>
          <div class="component-group">
            <div class="group-title">数据展示</div>
            <div class="component-items">
              <div class="component-item" draggable @dragstart="onDragStart($event, 'counter')">
                <el-icon><Clock /></el-icon>
                <span>数字翻牌</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'table')">
                <el-icon><Grid /></el-icon>
                <span>表格</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'ranking')">
                <el-icon><Rank /></el-icon>
                <span>排行榜</span>
              </div>
            </div>
          </div>
          <div class="component-group">
            <div class="group-title">装饰</div>
            <div class="component-items">
              <div class="component-item" draggable @dragstart="onDragStart($event, 'text')">
                <el-icon><Text /></el-icon>
                <span>文本</span>
              </div>
              <div class="component-item" draggable @dragstart="onDragStart($event, 'image')">
                <el-icon><Picture /></el-icon>
                <span>图片</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 中间画布 -->
      <div class="canvas-container" ref="canvasContainer">
        <div class="canvas-wrapper" :style="canvasStyle">
          <div
            class="canvas"
            @dragover.prevent
            @drop="onDrop"
            @click="selectComponent(null)"
          >
            <div
              v-for="comp in components"
              :key="comp.id"
              class="canvas-component"
              :class="{ selected: selectedId === comp.id }"
              :style="getComponentStyle(comp)"
              @click.stop="selectComponent(comp)"
            >
              <div class="component-handle">
                <span class="component-name">{{ comp.name }}</span>
                <el-button link type="danger" size="small" @click.stop="deleteComponent(comp.id)">删除</el-button>
              </div>
              <component :is="getComponentType(comp.type)" :config="comp.config" :data="comp.data" :theme="theme" />
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧属性面板 -->
      <div class="property-panel">
        <div class="panel-title">属性配置</div>
        <div v-if="selectedComponent" class="property-content">
          <el-form label-width="80px" size="small">
            <el-form-item label="组件名称">
              <el-input v-model="selectedComponent.name" />
            </el-form-item>
            <el-form-item label="组件类型">
              <el-input :value="selectedComponent.type" disabled />
            </el-form-item>
            <el-divider>位置尺寸</el-divider>
            <el-form-item label="X位置">
              <el-input-number v-model="selectedComponent.config.x" :min="0" :step="10" />
            </el-form-item>
            <el-form-item label="Y位置">
              <el-input-number v-model="selectedComponent.config.y" :min="0" :step="10" />
            </el-form-item>
            <el-form-item label="宽度">
              <el-input-number v-model="selectedComponent.config.width" :min="100" :step="10" />
            </el-form-item>
            <el-form-item label="高度">
              <el-input-number v-model="selectedComponent.config.height" :min="100" :step="10" />
            </el-form-item>
            <el-divider>数据源</el-divider>
            <el-form-item label="数据类型">
              <el-select v-model="selectedComponent.dataSource.type" placeholder="选择数据类型">
                <el-option label="静态数据" value="static" />
                <el-option label="API接口" value="api" />
              </el-select>
            </el-form-item>
            <template v-if="selectedComponent.dataSource.type === 'static'">
              <el-form-item label="数据">
                <el-input
                  v-model="selectedComponent.dataSource.data"
                  type="textarea"
                  :rows="5"
                  placeholder="JSON格式数据"
                />
              </el-form-item>
            </template>
            <template v-if="selectedComponent.dataSource.type === 'api'">
              <el-form-item label="API地址">
                <el-input v-model="selectedComponent.dataSource.apiUrl" placeholder="请输入API地址" />
              </el-form-item>
              <el-form-item label="刷新间隔">
                <el-input-number v-model="selectedComponent.dataSource.refreshInterval" :min="0" :step="5" /> 秒
              </el-form-item>
              <el-form-item>
                <el-button size="small" @click="testApi">测试接口</el-button>
              </el-form-item>
            </template>
            <el-divider>样式配置</el-divider>
            <el-form-item label="背景色">
              <el-color-picker v-model="selectedComponent.config.backgroundColor" />
            </el-form-item>
            <el-form-item label="透明度">
              <el-slider v-model="selectedComponent.config.opacity" :min="0" :max="1" :step="0.1" show-input />
            </el-form-item>
            <el-form-item label="边框">
              <el-input v-model="selectedComponent.config.border" placeholder="如: 1px solid #333" />
            </el-form-item>
            <el-form-item label="圆角">
              <el-input-number v-model="selectedComponent.config.borderRadius" :min="0" />
            </el-form-item>
          </el-form>
        </div>
        <div v-else class="property-empty">
          <el-empty description="请选择组件" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { screenDetail, screenSaveConfig } from '@/api/tool/dataScreen'
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
const router = useRouter()

// 状态
const theme = ref('dark')
const screenData = ref({ id: null, name: '', config: {} })
const components = ref([])
const selectedId = ref(null)
const canvasContainer = ref(null)
const canvasWidth = ref(1920)
const canvasHeight = ref(1080)

// 计算属性
const selectedComponent = computed(() => {
  if (!selectedId.value) return null
  return components.value.find(c => c.id === selectedId.value)
})

const canvasStyle = computed(() => ({
  width: canvasWidth.value + 'px',
  height: canvasHeight.value + 'px',
  transform: `scale(${getScale()})`,
  transformOrigin: 'top left'
}))

function getScale() {
  if (!canvasContainer.value) return 1
  const container = canvasContainer.value
  const scaleX = container.clientWidth / canvasWidth.value
  const scaleY = container.clientHeight / canvasHeight.value
  return Math.min(scaleX, scaleY, 1)
}

// 组件类型映射
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

// 加载数据
async function loadData() {
  const id = route.query.id
  if (!id) {
    ElMessage.error('参数错误')
    return
  }
  try {
    const res = await screenDetail(id)
    screenData.value = {
      id: res.id,
      name: res.name,
      config: res.config || {}
    }
    theme.value = res.config?.theme || 'dark'
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
      dataSource: {
        type: c.data_source?.type || 'static',
        data: c.data_source?.data || '[]',
        apiUrl: c.data_source?.apiUrl || '',
        refreshInterval: c.data_source?.refreshInterval || 0
      },
      data: c.data_source?.data ? JSON.parse(c.data_source?.data) : []
    }))
  } catch (error) {
    ElMessage.error('加载失败')
  }
}

// 拖拽开始
function onDragStart(e, type) {
  e.dataTransfer.setData('componentType', type)
}

// 拖放
function onDrop(e) {
  const type = e.dataTransfer.getData('componentType')
  if (!type) return

  const rect = e.currentTarget.getBoundingClientRect()
  const scale = getScale()
  const x = Math.round((e.clientX - rect.left) / scale)
  const y = Math.round((e.clientY - rect.top) / scale)

  const newComponent = {
    id: 'comp_' + Date.now(),
    type,
    name: getDefaultName(type),
    config: {
      x,
      y,
      width: 400,
      height: 300,
      backgroundColor: 'transparent',
      opacity: 1,
      border: '',
      borderRadius: 0
    },
    dataSource: {
      type: 'static',
      data: '[]',
      apiUrl: '',
      refreshInterval: 0
    },
    data: []
  }

  components.value.push(newComponent)
  selectedId.value = newComponent.id
}

// 获取默认名称
function getDefaultName(type) {
  const names = {
    line: '折线图',
    bar: '柱状图',
    pie: '饼图',
    map: '中国地图',
    gauge: '仪表盘',
    counter: '数字翻牌',
    table: '数据表格',
    ranking: '排行榜',
    text: '文本',
    image: '图片'
  }
  return names[type] || '组件'
}

// 选择组件
function selectComponent(comp) {
  selectedId.value = comp?.id || null
}

// 删除组件
function deleteComponent(id) {
  const index = components.value.findIndex(c => c.id === id)
  if (index > -1) {
    components.value.splice(index, 1)
    if (selectedId.value === id) {
      selectedId.value = null
    }
  }
}

// 获取组件样式
function getComponentStyle(comp) {
  return {
    left: comp.config.x + 'px',
    top: comp.config.y + 'px',
    width: comp.config.width + 'px',
    height: comp.config.height + 'px',
    backgroundColor: comp.config.backgroundColor,
    opacity: comp.config.opacity,
    border: comp.config.border,
    borderRadius: comp.config.borderRadius + 'px'
  }
}

// 测试API
async function testApi() {
  if (!selectedComponent.value) return
  try {
    const response = await fetch(selectedComponent.value.dataSource.apiUrl)
    const data = await response.json()
    ElMessage.success('接口正常，返回数据：' + JSON.stringify(data).substring(0, 100))
  } catch (error) {
    ElMessage.error('接口请求失败')
  }
}

// 保存
async function handleSave() {
  if (!screenData.value.id) return
  try {
    const saveData = {
      id: screenData.value.id,
      config: {
        theme: theme.value,
        width: canvasWidth.value,
        height: canvasHeight.value
      },
      components: components.value.map((c, index) => ({
        type: c.type,
        name: c.name,
        config: c.config,
        data_source: {
          type: c.dataSource.type,
          data: c.dataSource.type === 'static' ? JSON.stringify(c.data) : '',
          apiUrl: c.dataSource.apiUrl,
          refreshInterval: c.dataSource.refreshInterval
        },
        sort: index + 1
      }))
    }
    await screenSaveConfig(saveData)
    ElMessage.success('保存成功')
  } catch (error) {
    ElMessage.error(error.message || '保存失败')
  }
}

// 预览
function handlePreview() {
  window.open(`/admin/data-screen/preview/${screenData.value.id}`, '_blank')
}

// 返回
function goBack() {
  router.push('/tool/data-screen')
}

// 监听窗口大小变化
function handleResize() {
  // 触发响应式更新
}

// 初始化
onMounted(() => {
  loadData()
  window.addEventListener('resize', handleResize)
})
</script>

<style scoped>
.data-screen-designer {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f0f2f5;
}

.data-screen-designer.dark-theme {
  background: #1a1a2e;
}

.designer-header {
  height: 60px;
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}

.dark-theme .designer-header {
  background: #16213e;
  border-color: #2a2a4a;
}

.header-left, .header-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.screen-title {
  font-size: 16px;
  font-weight: 600;
}

.dark-theme .screen-title {
  color: #fff;
}

.designer-body {
  flex: 1;
  display: flex;
  overflow: hidden;
}

/* 组件面板 */
.component-panel {
  width: 240px;
  background: #fff;
  border-right: 1px solid #e8e8e8;
  overflow-y: auto;
}

.dark-theme .component-panel {
  background: #16213e;
  border-color: #2a2a4a;
}

.panel-title {
  padding: 15px;
  font-weight: 600;
  border-bottom: 1px solid #e8e8e8;
}

.dark-theme .panel-title {
  border-color: #2a2a4a;
  color: #fff;
}

.component-list {
  padding: 10px;
}

.component-group {
  margin-bottom: 15px;
}

.group-title {
  font-size: 12px;
  color: #999;
  margin-bottom: 10px;
  padding-left: 5px;
}

.dark-theme .group-title {
  color: #888;
}

.component-items {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.component-item {
  background: #f5f7fa;
  border: 1px solid #e8e8e8;
  border-radius: 4px;
  padding: 10px 5px;
  text-align: center;
  cursor: move;
  transition: all 0.3s;
}

.component-item:hover {
  border-color: #409eff;
  background: #ecf5ff;
}

.dark-theme .component-item {
  background: #1a1a2e;
  border-color: #2a2a4a;
  color: #fff;
}

.dark-theme .component-item:hover {
  border-color: #409eff;
  background: #1e3a5f;
}

.component-item .el-icon {
  font-size: 24px;
  display: block;
  margin-bottom: 5px;
}

.component-item span {
  font-size: 12px;
}

/* 画布 */
.canvas-container {
  flex: 1;
  overflow: auto;
  background: #e8e8e8;
  position: relative;
}

.dark-theme .canvas-container {
  background: #0f0f23;
}

.canvas-wrapper {
  position: relative;
  margin: 20px auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.canvas {
  width: 100%;
  height: 100%;
  position: relative;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  overflow: hidden;
}

.canvas-component {
  position: absolute;
  border: 2px solid transparent;
  cursor: move;
  transition: border-color 0.3s;
}

.canvas-component:hover {
  border-color: #409eff;
}

.canvas-component.selected {
  border-color: #409eff;
}

.component-handle {
  position: absolute;
  top: -30px;
  left: 0;
  right: 0;
  height: 30px;
  background: #409eff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 10px;
  opacity: 0;
  transition: opacity 0.3s;
}

.canvas-component:hover .component-handle,
.canvas-component.selected .component-handle {
  opacity: 1;
}

.component-name {
  color: #fff;
  font-size: 12px;
}

/* 属性面板 */
.property-panel {
  width: 300px;
  background: #fff;
  border-left: 1px solid #e8e8e8;
  overflow-y: auto;
}

.dark-theme .property-panel {
  background: #16213e;
  border-color: #2a2a4a;
}

.dark-theme .property-panel .panel-title {
  color: #fff;
}

.property-content {
  padding: 15px;
}

.property-empty {
  padding: 60px 20px;
}

.dark-theme :deep(.el-form-item__label) {
  color: #fff;
}

.dark-theme :deep(.el-input__wrapper) {
  background: #1a1a2e;
}

.dark-theme :deep(.el-divider) {
  border-color: #2a2a4a;
}
</style>

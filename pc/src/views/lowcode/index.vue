<template>
  <div class="lowcode-page">
    <!-- 顶部导航 -->
    <header class="page-header">
      <div class="header-inner">
        <a href="/pc/" class="back-home">
          <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼 Admin</span>
        </a>
        <nav class="header-nav">
          <a href="/pc/doc#overview">技术文档</a>
          <a href="/pc/nl2sql">NL2SQL</a>
          <a href="/pc/lowcode" class="nav-active">低代码</a>
        </nav>
        <a href="/admin" target="_blank" class="btn-primary">进入后台</a>
      </div>
    </header>

    <div class="page-content">
      <!-- 标题 -->
      <div class="page-title-section">
        <div class="title-badge">🧩 低代码平台</div>
        <h1>可视化开发 <span>零代码搭建</span></h1>
        <p class="subtitle">通过拖拽组件、配置属性，无需编写代码即可快速构建业务页面</p>
        <div class="feature-chips">
          <span class="chip">🎨 可视化拖拽</span>
          <span class="chip">📋 表单设计器</span>
          <span class="chip">📊 列表设计器</span>
          <span class="chip">🔄 业务联动</span>
        </div>
      </div>

      <!-- 功能展示 -->
      <div class="feature-showcase">
        <div class="feature-panel builder">
          <div class="panel-header">
            <span class="panel-title">🎯 可视化页面构建器</span>
          </div>
          <div class="builder-area">
            <div class="component-palette">
              <div class="palette-title">组件库</div>
              <div
                v-for="comp in componentPalette"
                :key="comp.type"
                class="palette-item"
                :title="comp.name"
                @mousedown.prevent="onDragStart($event, comp)"
              >
                <span class="comp-icon">{{ comp.icon }}</span>
                <span class="comp-name">{{ comp.name }}</span>
              </div>
            </div>
            <div
              class="canvas-area"
            >
              <div v-if="droppedComponents.length === 0" class="canvas-placeholder">
                <span>👆 从左侧拖拽组件到此处</span>
              </div>
              <div
                v-for="(comp, idx) in droppedComponents"
                :key="idx"
                class="canvas-comp"
                :style="comp.style"
              >
                <span>{{ comp.icon }} {{ comp.name }}</span>
                <button class="remove-btn" @click="removeComp(idx)">×</button>
              </div>
            </div>
            <div class="property-panel">
              <div class="palette-title">属性配置</div>
              <div v-if="selectedComp" class="prop-list">
                <div class="prop-item">
                  <label>标签文字</label>
                  <input v-model="selectedComp.label" placeholder="输入标签" />
                </div>
                <div class="prop-item">
                  <label>占位符</label>
                  <input v-model="selectedComp.placeholder" placeholder="输入占位符" />
                </div>
                <div class="prop-item">
                  <label>必填</label>
                  <el-switch v-model="selectedComp.required" />
                </div>
                <div class="prop-item">
                  <label>宽度</label>
                  <el-select v-model="selectedComp.width" size="small">
                    <el-option label="100%" value="100%" />
                    <el-option label="50%" value="50%" />
                    <el-option label="33%" value="33%" />
                  </el-select>
                </div>
              </div>
              <div v-else class="prop-empty">
                <span>点击画布中的组件进行配置</span>
              </div>
              <div class="prop-actions">
                <el-button type="primary" size="small" @click="generateJson">生成 Schema</el-button>
                <el-button size="small" @click="clearCanvas">清空画布</el-button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 生成的Schema -->
      <div v-if="generatedSchema" class="schema-output">
        <div class="panel-header">
          <span class="panel-title">📄 生成的 Amis JSON Schema</span>
          <el-button size="small" @click="copySchema">复制</el-button>
        </div>
        <pre class="schema-code">{{ generatedSchema }}</pre>
      </div>

      <!-- 功能列表 -->
      <div class="capability-grid">
        <div class="cap-card" v-for="cap in capabilities" :key="cap.title">
          <div class="cap-icon">{{ cap.icon }}</div>
          <h3>{{ cap.title }}</h3>
          <p>{{ cap.desc }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ElMessage } from 'element-plus'

const componentPalette = [
  { type: 'input-text', name: '单行输入', icon: '📝', style: { width: '100%' }, label: '单行文本', placeholder: '请输入', required: false },
  { type: 'textarea', name: '多行文本', icon: '📄', style: { width: '100%' }, label: '多行文本', placeholder: '请输入', required: false },
  { type: 'select', name: '下拉选择', icon: '📋', style: { width: '100%' }, label: '下拉选择', placeholder: '请选择', required: false },
  { type: 'radio', name: '单选框', icon: '🔘', style: { width: '100%' }, label: '单选', required: false },
  { type: 'checkbox', name: '复选框', icon: '☑️', style: { width: '100%' }, label: '多选', required: false },
  { type: 'date', name: '日期选择', icon: '📅', style: { width: '50%' }, label: '日期', required: false },
  { type: 'datetime', name: '日期时间', icon: '⏰', style: { width: '50%' }, label: '日期时间', required: false },
  { type: 'number', name: '数字输入', icon: '🔢', style: { width: '50%' }, label: '数字', required: false },
  { type: 'switch', name: '开关', icon: '🔃', style: { width: '50%' }, label: '开关', required: false },
  { type: 'image', name: '图片上传', icon: '🖼️', style: { width: '100%' }, label: '图片', required: false },
  { type: 'file', name: '文件上传', icon: '📎', style: { width: '100%' }, label: '附件', required: false },
  { type: 'button', name: '按钮', icon: '🔵', style: { width: 'auto' }, label: '提交', required: false },
]

const droppedComponents = ref([])
const selectedComp = ref(null)
const generatedSchema = ref('')
let draggedComp = ref(null)
let dragGhost = null
let dragOffsetX = 0
let dragOffsetY = 0

// 创建拖拽影子元素
const createDragGhost = (comp, clientX, clientY) => {
  if (dragGhost) dragGhost.remove()
  dragGhost = document.createElement('div')
  dragGhost.textContent = comp.icon + ' ' + comp.name
  dragGhost.style.cssText = `
    position: fixed; z-index: 9999; pointer-events: none;
    background: #2563EB; color: #fff; padding: 8px 16px;
    border-radius: 8px; font-size: 14px; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    left: ${clientX - 60}px; top: ${clientY - 20}px;
    opacity: 0.95;
  `
  document.body.appendChild(dragGhost)
}

// 鼠标按下：开始拖拽
const onDragStart = (e, comp) => {
  e.preventDefault()
  draggedComp.value = { ...comp, id: Date.now() }
  dragOffsetX = e.clientX
  dragOffsetY = e.clientY
  createDragGhost(comp, e.clientX, e.clientY)
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

// 鼠标移动：移动影子
const onMouseMove = (e) => {
  if (!draggedComp.value || !dragGhost) return
  dragGhost.style.left = (e.clientX - 60) + 'px'
  dragGhost.style.top = (e.clientY - 20) + 'px'
}

// 鼠标释放：判断落点并添加组件
const onMouseUp = (e) => {
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
  if (dragGhost) {
    dragGhost.remove()
    dragGhost = null
  }
  if (!draggedComp.value) return

  // 查找 canvas-area 元素
  const canvas = document.querySelector('.canvas-area')
  if (!canvas) {
    draggedComp.value = null
    return
  }
  const rect = canvas.getBoundingClientRect()
  // 判断鼠标是否在 canvas 区域内
  if (
    e.clientX >= rect.left && e.clientX <= rect.right &&
    e.clientY >= rect.top && e.clientY <= rect.bottom
  ) {
    const newComp = { ...draggedComp.value, id: Date.now() + Math.random() }
    droppedComponents.value.push(newComp)
    selectedComp.value = newComp
  }
  draggedComp.value = null
}

const removeComp = (idx) => {
  droppedComponents.value.splice(idx, 1)
  if (selectedComp.value && droppedComponents.value.length === 0) {
    selectedComp.value = null
  }
}

const clearCanvas = () => {
  droppedComponents.value = []
  selectedComp.value = null
  generatedSchema.value = ''
}

const generateJson = () => {
  if (droppedComponents.value.length === 0) {
    ElMessage.warning('请先拖拽组件到画布')
    return
  }

  const schema = {
    type: 'page',
    title: '低代码页面',
    body: droppedComponents.value.map(comp => ({
      type: comp.type,
      name: comp.type + '_' + comp.id,
      label: comp.label,
      placeholder: comp.placeholder,
      required: comp.required,
    }))
  }

  generatedSchema.value = JSON.stringify(schema, null, 2)
}

const copySchema = () => {
  if (generatedSchema.value) {
    navigator.clipboard.writeText(generatedSchema.value).then(() => {
      ElMessage.success('Schema 已复制到剪贴板')
    })
  }
}

const capabilities = [
  { icon: '🎨', title: '可视化拖拽', desc: '通过拖拽组件构建页面，所见即所得' },
  { icon: '📋', title: '表单设计器', desc: '支持20+表单项类型，灵活配置验证规则' },
  { icon: '📊', title: '列表设计器', desc: '配置表格列、筛选条件、分页设置' },
  { icon: '🔄', title: '业务联动', desc: '支持字段联动、显示/隐藏、条件赋值' },
  { icon: '🔌', title: '自定义组件', desc: '注册Feiyu特有组件，扩展能力无上限' },
  { icon: '📤', title: '一键导出', desc: '导出amis JSON Schema，可直接用于生产环境' },
]
</script>

<style scoped>
.lowcode-page { min-height: 100vh; background: #f5f7fa; }

.page-header {
  position: sticky; top: 0; z-index: 100;
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}
.header-inner {
  max-width: 1400px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  height: 64px; padding: 0 40px;
}
.back-home {
  display: flex; align-items: center; gap: 10px;
  color: #fff; text-decoration: none; font-size: 17px; font-weight: 600;
}
.header-nav { display: flex; gap: 28px; }
.header-nav a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 14px; transition: color 0.2s; }
.header-nav a:hover { color: #fff; }
.nav-active { color: #fff !important; font-weight: 600; }
.btn-primary {
  background: rgba(255,255,255,0.15); color: #fff;
  border: 1px solid rgba(255,255,255,0.3);
  padding: 7px 18px; border-radius: 6px;
  text-decoration: none; font-size: 13px; font-weight: 500;
}
.btn-primary:hover { background: rgba(255,255,255,0.25); text-decoration: none; }

.page-content { max-width: 1400px; margin: 0 auto; padding: 48px 40px; }

.page-title-section { text-align: center; margin-bottom: 40px; }
.title-badge {
  display: inline-block;
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: #fff; padding: 4px 16px; border-radius: 20px;
  font-size: 13px; font-weight: 600; margin-bottom: 16px;
}
h1 { font-size: 40px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
h1 span { color: #2563EB; }
.subtitle { font-size: 16px; color: #606266; margin-bottom: 20px; }
.feature-chips { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
.chip { background: #fff0f6; color: #d63384; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; }

/* Builder */
.feature-panel { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 24px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.panel-title { font-size: 15px; font-weight: 600; color: #303133; }

.builder-area { display: grid; grid-template-columns: 160px 1fr 220px; gap: 16px; height: 420px; }

.component-palette {
  background: #f8fafc; border-radius: 10px; padding: 12px; overflow-y: auto;
  border: 1px solid #f0f0f0;
}
.palette-title { font-size: 11px; font-weight: 600; color: #909399; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-left: 4px; }
.palette-item {
  display: flex; align-items: center; gap: 8px;
  padding: 7px 8px; border-radius: 6px; cursor: grab;
  margin-bottom: 4px; transition: background 0.15s; border: 1px solid transparent;
}
.palette-item:hover { background: #e6f7ff; border-color: #91d5ff; }
.palette-item:active { cursor: grabbing; }
.comp-icon { font-size: 14px; }
.comp-name { font-size: 12px; color: #303133; }

.canvas-area {
  background: #fff; border: 2px dashed #dcdfe6; border-radius: 10px;
  padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;
}
.canvas-placeholder {
  flex: 1; display: flex; align-items: center; justify-content: center;
  color: #c0c4cc; font-size: 14px;
}
.canvas-comp {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px; background: #f8fafc; border: 1px solid #e5e7eb;
  border-radius: 6px; cursor: pointer; font-size: 13px; color: #303133;
  transition: all 0.15s;
}
.canvas-comp:hover { background: #e6f7ff; border-color: #91d5ff; }
.canvas-comp.selected { background: #e6f7ff; border-color: #2563EB; }
.remove-btn { background: none; border: none; color: #909399; cursor: pointer; font-size: 16px; padding: 0 4px; }
.remove-btn:hover { color: #f56c6c; }

.property-panel {
  background: #f8fafc; border-radius: 10px; padding: 12px;
  border: 1px solid #f0f0f0; display: flex; flex-direction: column; gap: 8px;
  overflow-y: auto;
}
.prop-list { display: flex; flex-direction: column; gap: 10px; }
.prop-item { display: flex; flex-direction: column; gap: 4px; }
.prop-item label { font-size: 11px; color: #606266; font-weight: 500; }
.prop-item input { border: 1px solid #dcdfe6; border-radius: 4px; padding: 5px 8px; font-size: 12px; width: 100%; }
.prop-item input:focus { outline: none; border-color: #2563EB; }
.prop-empty { flex: 1; display: flex; align-items: center; justify-content: center; color: #c0c4cc; font-size: 12px; text-align: center; }
.prop-actions { display: flex; flex-direction: column; gap: 6px; margin-top: auto; }

/* Schema output */
.schema-output {
  background: #fff; border-radius: 16px; padding: 24px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 24px;
}
.schema-code {
  background: #1a1a2e; color: #e6e6e6;
  padding: 16px 20px; border-radius: 8px;
  font-size: 12.5px; font-family: 'Monaco', 'Menlo', monospace;
  line-height: 1.7; overflow-x: auto; margin-top: 12px;
}

/* Capabilities */
.capability-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; }
.cap-card {
  background: #fff; border-radius: 12px; padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: all 0.2s;
}
.cap-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
.cap-icon { font-size: 28px; margin-bottom: 10px; }
.cap-card h3 { font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 6px; }
.cap-card p { font-size: 12px; color: #909399; line-height: 1.5; margin: 0; }

@media (max-width: 1100px) { .capability-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px) {
  .builder-area { grid-template-columns: 1fr; height: auto; }
  .capability-grid { grid-template-columns: repeat(2, 1fr); }
  .component-palette { height: 120px; }
  .property-panel { height: 200px; }
}
</style>

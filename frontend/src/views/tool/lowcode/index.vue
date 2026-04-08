<template>
  <div class="lowcode-builder">
    <!-- 顶部工具栏 -->
    <div class="builder-topbar">
      <div class="topbar-left">
        <span class="topbar-title">🧩 低代码表单设计器</span>
      </div>
      <div class="topbar-center">
        <span class="design-name">设计名称：</span>
        <el-input v-model="designName" placeholder="请输入表单名称" style="width: 200px" size="small" />
      </div>
      <div class="topbar-right">
        <el-button size="small" @click="clearCanvas">清空画布</el-button>
        <el-button size="small" @click="previewMode = true">预览</el-button>
        <el-button type="primary" size="small" @click="saveDesign">保存设计</el-button>
      </div>
    </div>

    <!-- 三栏主体 -->
    <div class="builder-body">
      <!-- 左侧：组件面板 -->
      <div class="panel-left">
        <el-tabs>
          <el-tab-pane label="基础">
            <template #label>
              <span class="tab-label">📦 基础</span>
            </template>
            <div class="comp-list">
              <div
                v-for="comp in basicComponents"
                :key="comp.type"
                class="comp-item"
                draggable="true"
                @dragstart="onDragStart($event, comp)"
              >
                <span class="comp-icon">{{ comp.icon }}</span>
                <span class="comp-label">{{ comp.label }}</span>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane label="高级">
            <template #label>
              <span class="tab-label">⚡ 高级</span>
            </template>
            <div class="comp-list">
              <div
                v-for="comp in advancedComponents"
                :key="comp.type"
                class="comp-item"
                draggable="true"
                @dragstart="onDragStart($event, comp)"
              >
                <span class="comp-icon">{{ comp.icon }}</span>
                <span class="comp-label">{{ comp.label }}</span>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane label="布局">
            <template #label>
              <span class="tab-label">📐 布局</span>
            </template>
            <div class="comp-list">
              <div
                v-for="comp in layoutComponents"
                :key="comp.type"
                class="comp-item"
                draggable="true"
                @dragstart="onDragStart($event, comp)"
              >
                <span class="comp-icon">{{ comp.icon }}</span>
                <span class="comp-label">{{ comp.label }}</span>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane label="表单">
            <template #label>
              <span class="tab-label">🔗 表单</span>
            </template>
            <div class="comp-list">
              <div
                v-for="comp in formComponents"
                :key="comp.type"
                class="comp-item"
                draggable="true"
                @dragstart="onDragStart($event, comp)"
              >
                <span class="comp-icon">{{ comp.icon }}</span>
                <span class="comp-label">{{ comp.label }}</span>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>

      <!-- 中间：画布 -->
      <div class="panel-canvas">
        <div class="canvas-toolbar">
          <span class="canvas-tip">📋 画布区域 — 从左侧拖入组件</span>
          <div class="canvas-actions">
            <el-button size="small" @click="moveUp" :disabled="!selectedIdx">↑ 上移</el-button>
            <el-button size="small" @click="moveDown" :disabled="!selectedIdx">↓ 下移</el-button>
            <el-button size="small" type="danger" @click="deleteSelected" :disabled="!selectedIdx">🗑 删除</el-button>
          </div>
        </div>
        <div
          class="canvas-dropzone"
          @dragover.prevent="onDragOver"
          @dragleave="onDragLeave"
          @drop="onDrop"
          :class="{ 'drag-over': isDragOver }"
        >
          <div v-if="canvasItems.length === 0" class="canvas-empty">
            <div class="empty-icon">📦</div>
            <div class="empty-text">从左侧拖拽组件到这里</div>
            <div class="empty-sub">组件将按顺序生成表单项</div>
          </div>

          <div v-else class="canvas-list">
            <div
              v-for="(item, idx) in canvasItems"
              :key="item.id"
              class="canvas-item"
              :class="{ 'is-selected': selectedIdx === idx }"
              @click="selectItem(idx)"
            >
              <div class="item-index">{{ idx + 1 }}</div>
              <div class="item-preview">
                <component :is="getPreviewComponent(item)" :item="item" />
              </div>
              <div class="item-name">{{ item.label || item.type }}</div>
              <div class="item-badge" v-if="item.required">必填</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧：属性配置 -->
      <div class="panel-right">
        <div class="panel-right-header">⚙️ 属性配置</div>
        <div v-if="selectedItem" class="prop-content">
          <!-- 基本属性 -->
          <div class="prop-section">
            <div class="prop-section-title">基本信息</div>
            <div class="prop-row">
              <label>组件类型</label>
              <el-tag size="small">{{ selectedItem.type }}</el-tag>
            </div>
            <div class="prop-row">
              <label>标签文字</label>
              <el-input v-model="selectedItem.label" size="small" placeholder="标签名称" />
            </div>
            <div class="prop-row" v-if="hasPlaceholder(selectedItem)">
              <label>占位文本</label>
              <el-input v-model="selectedItem.placeholder" size="small" placeholder="占位提示" />
            </div>
            <div class="prop-row" v-if="hasDefaultValue(selectedItem)">
              <label>默认值</label>
              <el-input v-model="selectedItem.defaultValue" size="small" placeholder="默认值" />
            </div>
            <div class="prop-row" v-if="hasOptions(selectedItem)">
              <label>选项配置</label>
              <el-input
                v-model="selectedItem.optionsStr"
                type="textarea"
                :rows="3"
                size="small"
                placeholder="每行一个选项，如&#10;选项1&#10;选项2&#10;选项3"
              />
            </div>
          </div>

          <!-- 校验属性 -->
          <div class="prop-section">
            <div class="prop-section-title">校验规则</div>
            <div class="prop-row">
              <label>是否必填</label>
              <el-switch v-model="selectedItem.required" />
            </div>
            <div class="prop-row" v-if="hasMaxlength(selectedItem)">
              <label>最大长度</label>
              <el-input-number v-model="selectedItem.maxlength" size="small" :min="1" :max="1000" />
            </div>
            <div class="prop-row" v-if="hasMinMax(selectedItem)">
              <label>最小值</label>
              <el-input-number v-model="selectedItem.min" size="small" />
            </div>
            <div class="prop-row" v-if="hasMinMax(selectedItem)">
              <label>最大值</label>
              <el-input-number v-model="selectedItem.max" size="small" />
            </div>
          </div>

          <!-- 样式属性 -->
          <div class="prop-section">
            <div class="prop-section-title">样式设置</div>
            <div class="prop-row">
              <label>宽度</label>
              <div class="flex-row">
                <el-input-number v-model="selectedItem.width" size="small" :min="1" :max="1200" style="flex:1" />
                <el-select v-model="selectedItem.widthUnit" size="small" style="width:80px;margin-left:4px">
                  <el-option label="px" value="px" />
                  <el-option label="%" value="%" />
                </el-select>
              </div>
            </div>
            <div class="prop-row">
              <label>是否禁用</label>
              <el-switch v-model="selectedItem.disabled" />
            </div>
            <div class="prop-row">
              <label>是否只读</label>
              <el-switch v-model="selectedItem.readonly" />
            </div>
            <div class="prop-row">
              <label>CSS类名</label>
              <el-input v-model="selectedItem.customClass" size="small" placeholder="自定义样式类" />
            </div>
          </div>

          <!-- 高级属性 -->
          <div class="prop-section" v-if="hasAdvanced(selectedItem)">
            <div class="prop-section-title">高级配置</div>
            <div class="prop-row" v-if="selectedItem.type === 'grid'">
              <label>列数</label>
              <el-input-number v-model="selectedItem.cols" size="small" :min="1" :max="12" />
            </div>
            <div class="prop-row" v-if="selectedItem.type === 'slider'">
              <label>步进值</label>
              <el-input-number v-model="selectedItem.step" size="small" :min="1" />
            </div>
            <div class="prop-row" v-if="selectedItem.type === 'rate'">
              <label>星星数量</label>
              <el-input-number v-model="selectedItem.count" size="small" :min="1" :max="10" />
            </div>
            <div class="prop-row" v-if="selectedItem.type === 'color'">
              <label>默认颜色</label>
              <el-color-picker v-model="selectedItem.defaultColor" size="small" />
            </div>
          </div>
        </div>
        <div v-else class="prop-empty">
          <div class="empty-icon">👈</div>
          <div>点击画布中的组件<br>进行属性配置</div>
        </div>
      </div>
    </div>

    <!-- 预览弹窗 -->
    <el-dialog v-model="previewMode" title="表单预览" width="600px" :close-on-click-modal="true">
      <div class="preview-form">
        <h3 style="margin-bottom:20px;text-align:center">{{ designName || '未命名表单' }}</h3>
        <el-form label-width="100px">
          <el-form-item
            v-for="item in canvasItems"
            :key="item.id"
            :label="item.label"
            :required="item.required"
          >
            <!-- Input -->
            <el-input
              v-if="item.type === 'input'"
              v-model="previewValues[item.id]"
              :placeholder="item.placeholder"
              :maxlength="item.maxlength"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Textarea -->
            <el-input
              v-else-if="item.type === 'textarea'"
              v-model="previewValues[item.id]"
              type="textarea"
              :placeholder="item.placeholder"
              :maxlength="item.maxlength"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Number -->
            <el-input-number
              v-else-if="item.type === 'number'"
              v-model="previewValues[item.id]"
              :min="item.min"
              :max="item.max"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Select -->
            <el-select
              v-else-if="item.type === 'select'"
              v-model="previewValues[item.id]"
              :placeholder="item.placeholder"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            >
              <el-option
                v-for="opt in parseOptions(item.optionsStr)"
                :key="opt"
                :label="opt"
                :value="opt"
              />
            </el-select>
            <!-- Radio -->
            <el-radio-group
              v-else-if="item.type === 'radio'"
              v-model="previewValues[item.id]"
              :disabled="item.disabled"
            >
              <el-radio v-for="opt in parseOptions(item.optionsStr)" :key="opt" :label="opt">{{ opt }}</el-radio>
            </el-radio-group>
            <!-- Checkbox -->
            <el-checkbox-group
              v-else-if="item.type === 'checkbox'"
              v-model="previewValues[item.id]"
              :disabled="item.disabled"
            >
              <el-checkbox v-for="opt in parseOptions(item.optionsStr)" :key="opt" :label="opt">{{ opt }}</el-checkbox>
            </el-checkbox-group>
            <!-- Switch -->
            <el-switch
              v-else-if="item.type === 'switch'"
              v-model="previewValues[item.id]"
              :disabled="item.disabled"
            />
            <!-- Date -->
            <el-date-picker
              v-else-if="item.type === 'date'"
              v-model="previewValues[item.id]"
              type="date"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Time -->
            <el-time-picker
              v-else-if="item.type === 'time'"
              v-model="previewValues[item.id]"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Cascader -->
            <el-cascader
              v-else-if="item.type === 'cascader'"
              v-model="previewValues[item.id]"
              :options="item.cascaderOptions || []"
              :placeholder="item.placeholder"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- RichText -->
            <div v-else-if="item.type === 'richtext'" class="richtext-preview">
              <div class="richtext-placeholder">富文本编辑器（需引入编辑器组件）</div>
            </div>
            <!-- File -->
            <el-upload
              v-else-if="item.type === 'file'"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            >
              <el-button size="small" type="primary">选择文件</el-button>
            </el-upload>
            <!-- Image -->
            <el-upload
              v-else-if="item.type === 'image'"
              list-type="picture-card"
              :disabled="item.disabled"
            >
              <el-icon><Plus /></el-icon>
            </el-upload>
            <!-- Rate -->
            <el-rate
              v-else-if="item.type === 'rate'"
              v-model="previewValues[item.id]"
              :count="item.count || 5"
              :disabled="item.disabled"
            />
            <!-- Slider -->
            <el-slider
              v-else-if="item.type === 'slider'"
              v-model="previewValues[item.id]"
              :min="item.min"
              :max="item.max"
              :step="item.step || 1"
              :disabled="item.disabled"
              :style="{ width: item.width + item.widthUnit }"
            />
            <!-- Color -->
            <el-color-picker
              v-else-if="item.type === 'color'"
              v-model="previewValues[item.id]"
              :disabled="item.disabled"
            />
            <!-- Grid -->
            <div v-else-if="item.type === 'grid'" class="grid-preview">
              <div class="grid-cell" v-for="c in (item.cols || 2)" :key="c">列 {{ c }}</div>
            </div>
            <!-- Card -->
            <div v-else-if="item.type === 'card'" class="card-preview">
              <div class="card-title">{{ item.label }}</div>
              <div class="card-body">卡片内容区域</div>
            </div>
            <!-- Divider -->
            <el-divider v-else-if="item.type === 'divider'" />
            <!-- Blank -->
            <div v-else-if="item.type === 'blank'" class="blank-preview">空白占位</div>
            <!-- Static Text -->
            <div v-else-if="item.type === 'static-text'" class="static-text-preview">{{ item.text || '静态文本' }}</div>
            <!-- Image Display -->
            <el-image
              v-else-if="item.type === 'static-image'"
              :src="item.src || 'https://via.placeholder.com/200x100'"
              style="width:150px;height:100px"
              fit="cover"
            />
            <!-- Button -->
            <el-button
              v-else-if="item.type === 'button'"
              :type="item.btnType || 'primary'"
              :disabled="item.disabled"
            >{{ item.label }}</el-button>
            <!-- Link -->
            <a
              v-else-if="item.type === 'link'"
              :href="item.href || '#'"
              class="form-link"
              :style="{ color: item.linkColor || '#409eff' }"
            >{{ item.label }}</a>
          </el-form-item>
        </el-form>
      </div>
      <template #footer>
        <el-button @click="previewMode = false">关闭</el-button>
        <el-button type="primary" @click="submitPreview">提交表单</el-button>
      </template>
    </el-dialog>

    <!-- Schema预览 -->
    <el-dialog v-model="schemaMode" title="生成的表单 Schema" width="700px">
      <pre class="schema-json">{{ schemaJson }}</pre>
      <template #footer>
        <el-button @click="copySchema">复制 JSON</el-button>
        <el-button @click="schemaMode = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'

// ==================== 组件定义 ====================
const basicComponents = [
  { type: 'input',    label: 'Input输入框',  icon: '📝', placeholder: '请输入', required: false },
  { type: 'textarea', label: 'Textarea文本', icon: '📄', placeholder: '请输入', required: false },
  { type: 'number',   label: 'Number数字',   icon: '🔢', placeholder: '请输入数字', required: false },
  { type: 'select',   label: 'Select下拉',   icon: '📋', placeholder: '请选择', required: false, optionsStr: '选项1\n选项2\n选项3' },
  { type: 'radio',    label: 'Radio单选',    icon: '🔘', label: '单选', required: false, optionsStr: '选项1\n选项2\n选项3' },
  { type: 'checkbox', label: 'Checkbox多选',  icon: '☑️', label: '多选', required: false, optionsStr: '选项1\n选项2\n选项3' },
  { type: 'switch',   label: 'Switch开关',   icon: '🔃', label: '开关', required: false },
  { type: 'date',     label: 'DatePicker日期',icon: '📅', label: '日期', required: false },
  { type: 'time',     label: 'TimePicker时间',icon: '⏰', label: '时间', required: false },
  { type: 'cascader', label: 'Cascader级联',  icon: '🌲', placeholder: '请选择', required: false },
]

const advancedComponents = [
  { type: 'richtext', label: '富文本编辑器', icon: '📰', placeholder: '请输入内容' },
  { type: 'file',     label: '文件上传',     icon: '📎', label: '附件上传', required: false },
  { type: 'image',    label: '图片上传',     icon: '🖼️', label: '图片', required: false },
  { type: 'rate',     label: 'Rating评分',    icon: '⭐', label: '评分', required: false, count: 5 },
  { type: 'slider',    label: 'Slider滑块',    icon: '🎚️', label: '滑块', required: false, min: 0, max: 100, step: 1 },
  { type: 'color',     label: 'ColorPicker颜色',icon: '🎨', label: '颜色', required: false, defaultColor: '#409eff' },
]

const layoutComponents = [
  { type: 'grid',    label: 'Grid栅格布局',  icon: '⊞', label: '栅格', cols: 2 },
  { type: 'card',    label: 'Card卡片容器',  icon: '📇', label: '卡片' },
  { type: 'divider', label: 'Divider分割线', icon: '➖', label: '分割线' },
  { type: 'blank',   label: 'Blank空白占位', icon: '⬜', label: '空白' },
]

const formComponents = [
  { type: 'static-text',  label: 'Static静态文本',  icon: '📃', label: '静态文本', text: '这是一段静态文本' },
  { type: 'static-image',label: 'Image图片',        icon: '🖼️', label: '图片', src: 'https://via.placeholder.com/200x100' },
  { type: 'button',      label: 'Button按钮',       icon: '🔵', label: '提交', btnType: 'primary' },
  { type: 'link',        label: 'Link链接',         icon: '🔗', label: '查看更多', href: '#', linkColor: '#409eff' },
]

const allComponents = [...basicComponents, ...advancedComponents, ...layoutComponents, ...formComponents]

// ==================== 状态 ====================
const designName = ref('')
const canvasItems = ref([])
const selectedIdx = ref(null)
const isDragOver = ref(false)
const previewMode = ref(false)
const schemaMode = ref(false)
const schemaJson = ref('')
const previewValues = ref({})
const draggedItem = ref(null)

// ==================== 拖拽 ====================
const onDragStart = (e, comp) => {
  draggedItem.value = { ...comp }
  e.dataTransfer.effectAllowed = 'copy'
}

const onDragOver = () => {
  isDragOver.value = true
}

const onDragLeave = () => {
  isDragOver.value = false
}

const onDrop = (e) => {
  isDragOver.value = false
  if (!draggedItem.value) return
  const newItem = {
    ...draggedItem.value,
    id: Date.now() + Math.random(),
    label: draggedItem.value.label || draggedItem.value.type,
    placeholder: draggedItem.value.placeholder || '',
    defaultValue: draggedItem.value.defaultValue || '',
    required: draggedItem.value.required || false,
    disabled: false,
    readonly: false,
    width: draggedItem.value.width || 100,
    widthUnit: draggedItem.value.widthUnit || '%',
    customClass: '',
    optionsStr: draggedItem.value.optionsStr || '',
    maxlength: draggedItem.value.maxlength || null,
    min: draggedItem.value.min ?? 0,
    max: draggedItem.value.max ?? 100,
    step: draggedItem.value.step || 1,
    count: draggedItem.value.count || 5,
    defaultColor: draggedItem.value.defaultColor || '#409eff',
    cols: draggedItem.value.cols || 2,
    text: draggedItem.value.text || '',
    src: draggedItem.value.src || '',
    href: draggedItem.value.href || '#',
    linkColor: draggedItem.value.linkColor || '#409eff',
    btnType: draggedItem.value.btnType || 'default',
  }
  canvasItems.value.push(newItem)
  selectedIdx.value = canvasItems.value.length - 1
  draggedItem.value = null
}

// ==================== 画布操作 ====================
const selectedItem = computed(() => {
  if (selectedIdx.value === null) return null
  return canvasItems.value[selectedIdx.value]
})

const selectItem = (idx) => {
  selectedIdx.value = idx
}

const deleteSelected = () => {
  if (selectedIdx.value === null) return
  canvasItems.value.splice(selectedIdx.value, 1)
  if (canvasItems.value.length === 0) {
    selectedIdx.value = null
  } else if (selectedIdx.value >= canvasItems.value.length) {
    selectedIdx.value = canvasItems.value.length - 1
  }
}

const moveUp = () => {
  if (selectedIdx.value === null || selectedIdx.value === 0) return
  const arr = canvasItems.value
  ;[arr[selectedIdx.value - 1], arr[selectedIdx.value]] = [arr[selectedIdx.value], arr[selectedIdx.value - 1]]
  selectedIdx.value--
}

const moveDown = () => {
  if (selectedIdx.value === null || selectedIdx.value === canvasItems.value.length - 1) return
  const arr = canvasItems.value
  ;[arr[selectedIdx.value], arr[selectedIdx.value + 1]] = [arr[selectedIdx.value + 1], arr[selectedIdx.value]]
  selectedIdx.value++
}

const clearCanvas = () => {
  canvasItems.value = []
  selectedIdx.value = null
}

// ==================== 属性辅助 ====================
const hasPlaceholder = (item) => ['input', 'textarea', 'select', 'cascader'].includes(item.type)
const hasDefaultValue = (item) => ['input', 'textarea', 'number', 'select'].includes(item.type)
const hasOptions = (item) => ['select', 'radio', 'checkbox'].includes(item.type)
const hasMaxlength = (item) => ['input', 'textarea'].includes(item.type)
const hasMinMax = (item) => ['number', 'slider'].includes(item.type)
const hasAdvanced = (item) => ['grid', 'slider', 'rate', 'color'].includes(item.type)

const parseOptions = (str) => {
  if (!str) return []
  return str.split('\n').filter(Boolean)
}

// ==================== 预览组件映射 ====================
const getPreviewComponent = (item) => {
  return 'div'
}

// ==================== 保存/加载 ====================
const saveDesign = async () => {
  if (!designName.value) {
    ElMessage.warning('请填写表单名称')
    return
  }
  try {
    const res = await fetch('/adminapi/form/design/save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: designName.value,
        schema: JSON.stringify(canvasItems.value),
      }),
    })
    const data = await res.json()
    if (data.code === 0 || data.code === 200) {
      ElMessage.success('保存成功')
    } else {
      ElMessage.error(data.msg || '保存失败')
    }
  } catch (e) {
    // 离线模式：生成schema展示
    generateSchema()
    schemaMode.value = true
    ElMessage.info('接口不可用，已生成Schema预览')
  }
}

const generateSchema = () => {
  schemaJson.value = JSON.stringify(canvasItems.value.map(item => {
    const { id, ...rest } = item
    return rest
  }), null, 2)
}

const loadDesign = async (id) => {
  try {
    const res = await fetch(`/adminapi/form/design/detail?id=${id}`)
    const data = await res.json()
    if (data.code === 0 || data.code === 200) {
      designName.value = data.data.name
      canvasItems.value = JSON.parse(data.data.schema)
    }
  } catch (e) {
    console.warn('加载设计失败', e)
  }
}

const submitPreview = () => {
  ElMessage.success('表单提交成功（预览模式）')
  previewMode.value = false
}

const copySchema = () => {
  navigator.clipboard.writeText(schemaJson.value).then(() => {
    ElMessage.success('Schema已复制到剪贴板')
  })
}
</script>

<style scoped>
.lowcode-builder {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #f0f2f5;
  font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
}

/* 顶部工具栏 */
.builder-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 52px;
  padding: 0 16px;
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  flex-shrink: 0;
}
.topbar-title { font-size: 15px; font-weight: 600; color: #1a1a2e; }
.topbar-left, .topbar-center, .topbar-right { display: flex; align-items: center; gap: 10px; }
.design-name { font-size: 13px; color: #606266; white-space: nowrap; }

/* 三栏主体 */
.builder-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* 左侧面板 */
.panel-left {
  width: 160px;
  flex-shrink: 0;
  background: #fff;
  border-right: 1px solid #e8e8e8;
  overflow-y: auto;
}
.panel-left :deep(.el-tabs__header) { margin: 0; }
.panel-left :deep(.el-tabs__nav-wrap) { padding: 0 8px; }
.panel-left :deep(.el-tabs__item) { font-size: 12px; padding: 0 8px; height: 36px; line-height: 36px; }
.panel-left :deep(.el-tabs__content) { padding: 8px; }

.tab-label { font-size: 12px; }

.comp-list { display: flex; flex-direction: column; gap: 4px; }
.comp-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 7px 8px;
  border-radius: 6px;
  cursor: grab;
  transition: all 0.15s;
  border: 1px solid transparent;
  background: #fafafa;
}
.comp-item:hover {
  background: #e6f0ff;
  border-color: #93b8ff;
}
.comp-item:active { cursor: grabbing; }
.comp-icon { font-size: 14px; flex-shrink: 0; }
.comp-label { font-size: 12px; color: #303133; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* 中间画布 */
.panel-canvas {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.canvas-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 16px;
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  flex-shrink: 0;
}
.canvas-tip { font-size: 12px; color: #909399; }
.canvas-actions { display: flex; gap: 6px; }

.canvas-dropzone {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f5f7fa;
  transition: background 0.15s;
}
.canvas-dropzone.drag-over {
  background: #e6f0ff;
  outline: 2px dashed #409eff;
  outline-offset: -4px;
}

.canvas-empty {
  height: 100%;
  min-height: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #c0c4cc;
}
.empty-icon { font-size: 48px; }
.empty-text { font-size: 15px; font-weight: 500; }
.empty-sub { font-size: 12px; color: #b1b5bd; }

.canvas-list { display: flex; flex-direction: column; gap: 8px; }
.canvas-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  background: #fff;
  border: 2px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  position: relative;
}
.canvas-item:hover { border-color: #b8d0fb; box-shadow: 0 2px 8px rgba(64,158,255,0.1); }
.canvas-item.is-selected { border-color: #409eff; background: #f0f7ff; box-shadow: 0 2px 10px rgba(64,158,255,0.15); }

.item-index {
  width: 22px; height: 22px;
  background: #409eff; color: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 600; flex-shrink: 0;
}
.item-preview { flex: 1; }
.item-name { font-size: 13px; color: #303133; font-weight: 500; }
.item-badge {
  background: #f56c6c; color: #fff;
  font-size: 10px; padding: 1px 6px;
  border-radius: 10px;
}

/* 右侧属性面板 */
.panel-right {
  width: 280px;
  flex-shrink: 0;
  background: #fff;
  border-left: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.panel-right-header {
  padding: 12px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #303133;
  border-bottom: 1px solid #e8e8e8;
  flex-shrink: 0;
}
.prop-content { flex: 1; overflow-y: auto; padding: 0; }
.prop-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #c0c4cc;
  font-size: 13px;
  text-align: center;
}
.prop-empty .empty-icon { font-size: 36px; }

.prop-section { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; }
.prop-section-title { font-size: 11px; font-weight: 600; color: #909399; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
.prop-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.prop-row:last-child { margin-bottom: 0; }
.prop-row label { font-size: 12px; color: #606266; width: 70px; flex-shrink: 0; }
.prop-row > * { flex: 1; }
.flex-row { display: flex; align-items: center; gap: 4px; }

/* 预览相关 */
.preview-form { max-height: 60vh; overflow-y: auto; }
.schema-json {
  background: #1e1e1e;
  color: #d4d4d4;
  padding: 16px;
  border-radius: 6px;
  font-size: 12px;
  line-height: 1.7;
  max-height: 50vh;
  overflow: auto;
  font-family: 'Monaco', 'Menlo', monospace;
}
.richtext-preview, .blank-preview { color: #909399; font-size: 13px; padding: 8px; background: #f5f7fa; border-radius: 4px; text-align: center; }
.grid-preview { display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 8px; }
.grid-cell { background: #e6f0ff; border: 1px dashed #93b8ff; border-radius: 4px; padding: 8px; text-align: center; font-size: 12px; color: #409eff; }
.card-preview { border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; }
.card-title { background: #f5f7fa; padding: 8px 12px; font-size: 13px; font-weight: 600; color: #303133; border-bottom: 1px solid #e8e8e8; }
.card-body { padding: 12px; font-size: 12px; color: #909399; }
.static-text-preview { font-size: 13px; color: #303133; line-height: 1.6; }
.form-link { font-size: 13px; text-decoration: underline; }
</style>

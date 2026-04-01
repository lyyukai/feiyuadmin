<template>
  <div class="form-design-container">
    <!-- 顶部工具栏 -->
    <div class="design-toolbar">
      <div class="toolbar-left">
        <el-button @click="goBack" icon="ArrowLeft">返回</el-button>
        <el-input v-model="formName" placeholder="请输入表单名称" style="width: 200px; margin-left: 10px" />
      </div>
      <div class="toolbar-center">
        <el-button type="primary" @click="saveForm" icon="Check">保存</el-button>
        <el-button @click="previewForm" icon="View">预览</el-button>
        <el-button @click="clearCanvas" icon="Delete">清空</el-button>
      </div>
      <div class="toolbar-right">
        <el-tag :type="formStatus === 1 ? 'success' : 'info'">
          {{ formStatus === 1 ? '已启用' : '已禁用' }}
        </el-tag>
        <el-switch v-model="formStatus" :active-value="1" :inactive-value="0" style="margin-left: 10px" />
      </div>
    </div>

    <!-- 主体设计区域 -->
    <div class="design-main">
      <!-- 左侧组件面板 -->
      <div class="component-panel">
        <div class="panel-title">组件库</div>
        <el-tabs v-model="activeTab" class="component-tabs">
          <el-tab-pane label="基础组件" name="basic">
            <div class="component-list">
              <div
                v-for="item in basicComponents"
                :key="item.type"
                class="component-item"
                draggable="true"
                @dragstart="onDragStart($event, item)"
              >
                <component :is="item.icon" class="component-icon" />
                <span>{{ item.label }}</span>
              </div>
            </div>
          </el-tab-pane>
          <el-tab-pane label="选择组件" name="select">
            <div class="component-list">
              <div
                v-for="item in selectComponents"
                :key="item.type"
                class="component-item"
                draggable="true"
                @dragstart="onDragStart($event, item)"
              >
                <component :is="item.icon" class="component-icon" />
                <span>{{ item.label }}</span>
              </div>
            </div>
          </el-tab-pane>
          <el-tab-pane label="高级组件" name="advanced">
            <div class="component-list">
              <div
                v-for="item in advancedComponents"
                :key="item.type"
                class="component-item"
                draggable="true"
                @dragstart="onDragStart($event, item)"
              >
                <component :is="item.icon" class="component-icon" />
                <span>{{ item.label }}</span>
              </div>
            </div>
          </el-tab-pane>
          <el-tab-pane label="布局组件" name="layout">
            <div class="component-list">
              <div
                v-for="item in layoutComponents"
                :key="item.type"
                class="component-item"
                draggable="true"
                @dragstart="onDragStart($event, item)"
              >
                <component :is="item.icon" class="component-icon" />
                <span>{{ item.label }}</span>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>

      <!-- 中间画布 -->
      <div
        class="design-canvas"
        @dragover.prevent="onDragOver"
        @drop="onDrop"
        @click="selectedComponent = null"
      >
        <div class="canvas-header">
          <h3>{{ formName || '未命名表单' }}</h3>
        </div>
        <div class="canvas-content">
          <div v-if="formComponents.length === 0" class="canvas-empty">
            <el-icon size="48"><DocumentAdd /></el-icon>
            <p>从左侧拖拽组件到此处</p>
          </div>
          <div
            v-for="(component, index) in formComponents"
            :key="component.id"
            class="canvas-component"
            :class="{ selected: selectedComponent?.id === component.id }"
            @click.stop="selectComponent(component)"
          >
            <!-- 单行文本 -->
            <template v-if="component.type === 'input'">
              <el-form-item :label="component.label" :required="component.required">
                <el-input
                  v-model="component.defaultValue"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 多行文本 -->
            <template v-else-if="component.type === 'textarea'">
              <el-form-item :label="component.label" :required="component.required">
                <el-input
                  v-model="component.defaultValue"
                  type="textarea"
                  :rows="component.rows || 3"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 数字 -->
            <template v-else-if="component.type === 'number'">
              <el-form-item :label="component.label" :required="component.required">
                <el-input-number
                  v-model="component.defaultValue"
                  :min="component.min"
                  :max="component.max"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 日期 -->
            <template v-else-if="component.type === 'date'">
              <el-form-item :label="component.label" :required="component.required">
                <el-date-picker
                  v-model="component.defaultValue"
                  type="date"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 时间 -->
            <template v-else-if="component.type === 'time'">
              <el-form-item :label="component.label" :required="component.required">
                <el-time-picker
                  v-model="component.defaultValue"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 下拉框 -->
            <template v-else-if="component.type === 'select'">
              <el-form-item :label="component.label" :required="component.required">
                <el-select
                  v-model="component.defaultValue"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                >
                  <el-option
                    v-for="opt in component.options"
                    :key="opt.value"
                    :label="opt.label"
                    :value="opt.value"
                  />
                </el-select>
              </el-form-item>
            </template>
            <!-- 单选 -->
            <template v-else-if="component.type === 'radio'">
              <el-form-item :label="component.label" :required="component.required">
                <el-radio-group v-model="component.defaultValue" :disabled="component.disabled">
                  <el-radio
                    v-for="opt in component.options"
                    :key="opt.value"
                    :value="opt.value"
                  >
                    {{ opt.label }}
                  </el-radio>
                </el-radio-group>
              </el-form-item>
            </template>
            <!-- 复选 -->
            <template v-else-if="component.type === 'checkbox'">
              <el-form-item :label="component.label" :required="component.required">
                <el-checkbox-group v-model="component.defaultValue" :disabled="component.disabled">
                  <el-checkbox
                    v-for="opt in component.options"
                    :key="opt.value"
                    :value="opt.value"
                  >
                    {{ opt.label }}
                  </el-checkbox>
                </el-checkbox-group>
              </el-form-item>
            </template>
            <!-- 开关 -->
            <template v-else-if="component.type === 'switch'">
              <el-form-item :label="component.label" :required="component.required">
                <el-switch v-model="component.defaultValue" :disabled="component.disabled" />
              </el-form-item>
            </template>
            <!-- 图片上传 -->
            <template v-else-if="component.type === 'image'">
              <el-form-item :label="component.label" :required="component.required">
                <el-upload
                  action="#"
                  :auto-upload="false"
                  :show-file-list="false"
                  :disabled="component.disabled"
                >
                  <img v-if="component.defaultValue" :src="component.defaultValue" class="upload-image" />
                  <el-icon v-else class="upload-icon"><Plus /></el-icon>
                </el-upload>
              </el-form-item>
            </template>
            <!-- 文件上传 -->
            <template v-else-if="component.type === 'file'">
              <el-form-item :label="component.label" :required="component.required">
                <el-upload
                  action="#"
                  :auto-upload="false"
                  :disabled="component.disabled"
                >
                  <el-button type="primary">点击上传</el-button>
                </el-upload>
              </el-form-item>
            </template>
            <!-- 富文本 -->
            <template v-else-if="component.type === 'editor'">
              <el-form-item :label="component.label" :required="component.required">
                <div class="editor-placeholder">[富文本编辑器]</div>
              </el-form-item>
            </template>
            <!-- 省市区 -->
            <template v-else-if="component.type === 'cascader'">
              <el-form-item :label="component.label" :required="component.required">
                <el-cascader
                  v-model="component.defaultValue"
                  :options="regionOptions"
                  :placeholder="component.placeholder"
                  :disabled="component.disabled"
                />
              </el-form-item>
            </template>
            <!-- 栅格布局 -->
            <template v-else-if="component.type === 'grid'">
              <div class="grid-layout" :style="{ gridTemplateColumns: `repeat(${component.cols || 2}, 1fr)` }">
                <div v-for="(col, colIndex) in (component.cols || 2)" :key="colIndex" class="grid-col">
                  <div
                    class="grid-col-content"
                    :data-parent-id="component.id"
                    :data-col="colIndex"
                    @dragover.prevent="onDragOver"
                    @drop="onDropInGrid($event, component, colIndex)"
                  >
                    <span class="grid-col-label">列{{ colIndex + 1 }}</span>
                  </div>
                </div>
              </div>
            </template>
            <!-- 分割线 -->
            <template v-else-if="component.type === 'divider'">
              <el-divider>{{ component.label || '' }}</el-divider>
            </template>
            <!-- 说明文字 -->
            <template v-else-if="component.type === 'text'">
              <div class="plain-text" :style="{ fontSize: component.fontSize + 'px', color: component.color }">
                {{ component.content }}
              </div>
            </template>

            <!-- 组件操作按钮 -->
            <div class="component-actions">
              <el-button
                type="primary"
                size="small"
                icon="Top"
                circle
                @click.stop="moveComponent(index, -1)"
                :disabled="index === 0"
              />
              <el-button
                type="primary"
                size="small"
                icon="Bottom"
                circle
                @click.stop="moveComponent(index, 1)"
                :disabled="index === formComponents.length - 1"
              />
              <el-button
                type="danger"
                size="small"
                icon="Delete"
                circle
                @click.stop="deleteComponent(index)"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧属性面板 -->
      <div class="property-panel" @click.stop>
        <div class="panel-title">属性配置</div>
        <div v-if="!selectedComponent" class="property-empty">
          <p>请选择组件进行配置</p>
        </div>
        <div v-else class="property-content">
          <el-form label-width="90px" size="small">
            <el-divider content-position="left">基本属性</el-divider>
            <el-form-item label="组件ID">
              <el-input :value="selectedComponent.id" disabled />
            </el-form-item>
            <el-form-item label="组件类型">
              <el-input :value="getTypeName(selectedComponent.type)" disabled />
            </el-form-item>
            <el-form-item label="标签名称">
              <el-input v-model="selectedComponent.label" placeholder="请输入标签名称" />
            </el-form-item>
            <el-form-item label="占位提示">
              <el-input v-model="selectedComponent.placeholder" placeholder="请输入占位提示" />
            </el-form-item>
            <el-form-item label="默认值">
              <el-input v-model="selectedComponent.defaultValue" placeholder="请输入默认值" />
            </el-form-item>
            <el-form-item label="是否必填">
              <el-switch v-model="selectedComponent.required" />
            </el-form-item>
            <el-form-item label="是否禁用">
              <el-switch v-model="selectedComponent.disabled" />
            </el-form-item>

            <template v-if="selectedComponent.type === 'number'">
              <el-divider content-position="left">数字属性</el-divider>
              <el-form-item label="最小值">
                <el-input-number v-model="selectedComponent.min" :min="0" />
              </el-form-item>
              <el-form-item label="最大值">
                <el-input-number v-model="selectedComponent.max" />
              </el-form-item>
            </template>

            <template v-if="selectedComponent.type === 'textarea'">
              <el-divider content-position="left">文本属性</el-divider>
              <el-form-item label="行数">
                <el-input-number v-model="selectedComponent.rows" :min="2" :max="10" />
              </el-form-item>
            </template>

            <template v-if="['select', 'radio', 'checkbox'].includes(selectedComponent.type)">
              <el-divider content-position="left">选项配置</el-divider>
              <el-form-item label="选项列表">
                <div class="options-list">
                  <div
                    v-for="(opt, optIndex) in selectedComponent.options"
                    :key="optIndex"
                    class="option-item"
                  >
                    <el-input v-model="opt.label" placeholder="选项标签" />
                    <el-input v-model="opt.value" placeholder="选项值" />
                    <el-button
                      type="danger"
                      size="small"
                      icon="Delete"
                      circle
                      @click="removeOption(optIndex)"
                    />
                  </div>
                  <el-button type="primary" size="small" @click="addOption">添加选项</el-button>
                </div>
              </el-form-item>
            </template>

            <template v-if="selectedComponent.type === 'grid'">
              <el-divider content-position="left">栅格配置</el-divider>
              <el-form-item label="列数">
                <el-input-number v-model="selectedComponent.cols" :min="2" :max="6" />
              </el-form-item>
            </template>

            <template v-if="selectedComponent.type === 'text'">
              <el-divider content-position="left">文本样式</el-divider>
              <el-form-item label="内容">
                <el-input v-model="selectedComponent.content" type="textarea" :rows="2" />
              </el-form-item>
              <el-form-item label="字号">
                <el-input-number v-model="selectedComponent.fontSize" :min="12" :max="48" />
              </el-form-item>
              <el-form-item label="颜色">
                <el-color-picker v-model="selectedComponent.color" />
              </el-form-item>
            </template>

            <template v-if="selectedComponent.type === 'divider'">
              <el-divider content-position="left">分割线</el-divider>
              <el-form-item label="标题">
                <el-input v-model="selectedComponent.label" placeholder="请输入标题" />
              </el-form-item>
            </template>
          </el-form>
        </div>
      </div>
    </div>

    <!-- 预览对话框 -->
    <el-dialog v-model="previewVisible" title="表单预览" width="800px" destroy-on-close>
      <div class="preview-form">
        <h3>{{ formName || '未命名表单' }}</h3>
        <el-form label-width="120px">
          <template v-for="component in formComponents" :key="component.id">
            <!-- 单行文本 -->
            <el-form-item v-if="component.type === 'input'" :label="component.label" :required="component.required">
              <el-input v-model="previewData[component.id]" :placeholder="component.placeholder" />
            </el-form-item>
            <!-- 多行文本 -->
            <el-form-item v-else-if="component.type === 'textarea'" :label="component.label" :required="component.required">
              <el-input v-model="previewData[component.id]" type="textarea" :rows="component.rows || 3" :placeholder="component.placeholder" />
            </el-form-item>
            <!-- 数字 -->
            <el-form-item v-else-if="component.type === 'number'" :label="component.label" :required="component.required">
              <el-input-number v-model="previewData[component.id]" :min="component.min" :max="component.max" />
            </el-form-item>
            <!-- 日期 -->
            <el-form-item v-else-if="component.type === 'date'" :label="component.label" :required="component.required">
              <el-date-picker v-model="previewData[component.id]" type="date" :placeholder="component.placeholder" />
            </el-form-item>
            <!-- 时间 -->
            <el-form-item v-else-if="component.type === 'time'" :label="component.label" :required="component.required">
              <el-time-picker v-model="previewData[component.id]" :placeholder="component.placeholder" />
            </el-form-item>
            <!-- 下拉框 -->
            <el-form-item v-else-if="component.type === 'select'" :label="component.label" :required="component.required">
              <el-select v-model="previewData[component.id]" :placeholder="component.placeholder">
                <el-option v-for="opt in component.options" :key="opt.value" :label="opt.label" :value="opt.value" />
              </el-select>
            </el-form-item>
            <!-- 单选 -->
            <el-form-item v-else-if="component.type === 'radio'" :label="component.label" :required="component.required">
              <el-radio-group v-model="previewData[component.id]">
                <el-radio v-for="opt in component.options" :key="opt.value" :value="opt.value">{{ opt.label }}</el-radio>
              </el-radio-group>
            </el-form-item>
            <!-- 复选 -->
            <el-form-item v-else-if="component.type === 'checkbox'" :label="component.label" :required="component.required">
              <el-checkbox-group v-model="previewData[component.id]">
                <el-checkbox v-for="opt in component.options" :key="opt.value" :value="opt.value">{{ opt.label }}</el-checkbox>
              </el-checkbox-group>
            </el-form-item>
            <!-- 开关 -->
            <el-form-item v-else-if="component.type === 'switch'" :label="component.label" :required="component.required">
              <el-switch v-model="previewData[component.id]" />
            </el-form-item>
            <!-- 图片上传 -->
            <el-form-item v-else-if="component.type === 'image'" :label="component.label" :required="component.required">
              <el-upload action="#" :auto-upload="false" :show-file-list="false">
                <img v-if="previewData[component.id]" :src="previewData[component.id]" class="upload-image" />
                <el-icon v-else class="upload-icon"><Plus /></el-icon>
              </el-upload>
            </el-form-item>
            <!-- 文件上传 -->
            <el-form-item v-else-if="component.type === 'file'" :label="component.label" :required="component.required">
              <el-upload action="#">
                <el-button type="primary">点击上传</el-button>
              </el-upload>
            </el-form-item>
            <!-- 省市区 -->
            <el-form-item v-else-if="component.type === 'cascader'" :label="component.label" :required="component.required">
              <el-cascader v-model="previewData[component.id]" :options="regionOptions" :placeholder="component.placeholder" />
            </el-form-item>
            <!-- 栅格 -->
            <el-form-item v-else-if="component.type === 'grid'">
              <template #label>
                <span>{{ component.label }}</span>
              </template>
              <div class="grid-layout" :style="{ gridTemplateColumns: `repeat(${component.cols || 2}, 1fr)` }">
                <div v-for="n in (component.cols || 2)" :key="n" class="grid-col">
                  <span>列{{ n }}</span>
                </div>
              </div>
            </el-form-item>
            <!-- 分割线 -->
            <el-form-item v-else-if="component.type === 'divider'">
              <el-divider>{{ component.label }}</el-divider>
            </el-form-item>
            <!-- 说明文字 -->
            <el-form-item v-else-if="component.type === 'text'">
              <template #label>
                <span></span>
              </template>
              <div :style="{ fontSize: component.fontSize + 'px', color: component.color }">
                {{ component.content }}
              </div>
            </el-form-item>
          </template>
        </el-form>
      </div>
      <template #footer>
        <el-button @click="previewVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import {
  Document,
  Edit,
  DocumentCopy,
  Calendar,
  Clock,
  ArrowDown,
  CircleCheck,
  Finished,
  Switch,
  Picture,
  Upload,
  DocumentAdd,
  Grid,
  Minus
} from '@element-plus/icons-vue'
import { getFormInfo, addForm, editForm } from '@/api/form'

const router = useRouter()
const route = useRoute()

// 表单基本信息
const formId = ref(route.query.id ? parseInt(route.query.id) : null)
const formName = ref('')
const formStatus = ref(1)
const formCode = ref('')
const formComponents = ref([])
const selectedComponent = ref(null)

// 预览相关
const previewVisible = ref(false)
const previewData = reactive({})

// 拖拽相关
const dragComponent = ref(null)

// 组件类型映射
const componentTypes = {
  input: '单行文本',
  textarea: '多行文本',
  number: '数字',
  date: '日期',
  time: '时间',
  select: '下拉框',
  radio: '单选',
  checkbox: '复选',
  switch: '开关',
  image: '图片上传',
  file: '文件上传',
  editor: '富文本',
  cascader: '省市区选择',
  grid: '栅格布局',
  divider: '分割线',
  text: '说明文字'
}

// 基础组件
const basicComponents = [
  { type: 'input', label: '单行文本', icon: Edit },
  { type: 'textarea', label: '多行文本', icon: DocumentCopy },
  { type: 'number', label: '数字', icon: Document },
  { type: 'date', label: '日期', icon: Calendar },
  { type: 'time', label: '时间', icon: Clock }
]

// 选择组件
const selectComponents = [
  { type: 'select', label: '下拉框', icon: ArrowDown },
  { type: 'radio', label: '单选', icon: CircleCheck },
  { type: 'checkbox', label: '复选', icon: Finished },
  { type: 'switch', label: '开关', icon: Switch }
]

// 高级组件
const advancedComponents = [
  { type: 'image', label: '图片上传', icon: Picture },
  { type: 'file', label: '文件上传', icon: Upload },
  { type: 'editor', label: '富文本', icon: DocumentAdd },
  { type: 'cascader', label: '省市区选择', icon: Grid }
]

// 布局组件
const layoutComponents = [
  { type: 'grid', label: '栅格布局', icon: Grid },
  { type: 'divider', label: '分割线', icon: Minus },
  { type: 'text', label: '说明文字', icon: Edit }
]

// Tab
const activeTab = ref('basic')

// 省市区数据（简化版）
const regionOptions = [
  {
    value: '110000',
    label: '北京市',
    children: [
      { value: '110100', label: '市辖区' }
    ]
  },
  {
    value: '310000',
    label: '上海市',
    children: [
      { value: '310100', label: '市辖区' }
    ]
  },
  {
    value: '440000',
    label: '广东省',
    children: [
      { value: '440100', label: '广州市' },
      { value: '440300', label: '深圳市' }
    ]
  }
]

// 获取类型名称
const getTypeName = (type) => {
  return componentTypes[type] || type
}

// 生成唯一ID
const generateId = () => {
  return 'comp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
}

// 拖拽开始
const onDragStart = (event, component) => {
  dragComponent.value = component
  event.dataTransfer.effectAllowed = 'copy'
}

// 拖拽经过
const onDragOver = (event) => {
  event.dataTransfer.dropEffect = 'copy'
}

// 拖拽释放 - 画布
const onDrop = (event) => {
  if (!dragComponent.value) return
  
  const newComponent = createComponent(dragComponent.value)
  formComponents.value.push(newComponent)
  selectedComponent.value = newComponent
  dragComponent.value = null
}

// 拖拽释放 - 栅格内
const onDropInGrid = (event, gridComponent, colIndex) => {
  event.stopPropagation()
  if (!dragComponent.value) return
  
  const newComponent = createComponent(dragComponent.value)
  
  // 如果栅格组件还没有columns属性，创建它
  if (!gridComponent.columns) {
    gridComponent.columns = []
    for (let i = 0; i < (gridComponent.cols || 2); i++) {
      gridComponent.columns.push([])
    }
  }
  
  // 添加到栅格的对应列
  if (gridComponent.columns[colIndex]) {
    gridComponent.columns[colIndex].push(newComponent)
  }
  
  dragComponent.value = null
}

// 创建组件
const createComponent = (template) => {
  const component = {
    id: generateId(),
    type: template.type,
    label: template.label || getTypeName(template.type),
    placeholder: '请输入',
    defaultValue: '',
    required: false,
    disabled: false
  }

  // 根据类型添加特定属性
  switch (template.type) {
    case 'number':
      component.min = 0
      component.max = 999999
      component.defaultValue = null
      break
    case 'textarea':
      component.rows = 3
      break
    case 'select':
    case 'radio':
    case 'checkbox':
      component.options = [
        { label: '选项1', value: 'option1' },
        { label: '选项2', value: 'option2' }
      ]
      if (template.type === 'checkbox') {
        component.defaultValue = []
      }
      break
    case 'grid':
      component.cols = 2
      component.columns = [[], []]
      break
    case 'text':
      component.content = '这是一段说明文字'
      component.fontSize = 14
      component.color = '#666666'
      break
    case 'cascader':
      component.defaultValue = []
      break
  }

  return component
}

// 选择组件
const selectComponent = (component) => {
  selectedComponent.value = component
}

// 移动组件
const moveComponent = (index, direction) => {
  const newIndex = index + direction
  if (newIndex < 0 || newIndex >= formComponents.value.length) return
  
  const temp = formComponents.value[index]
  formComponents.value[index] = formComponents.value[newIndex]
  formComponents.value[newIndex] = temp
}

// 删除组件
const deleteComponent = (index) => {
  formComponents.value.splice(index, 1)
  if (selectedComponent.value && formComponents.value.indexOf(selectedComponent.value) === -1) {
    selectedComponent.value = null
  }
}

// 添加选项
const addOption = () => {
  if (!selectedComponent.value.options) {
    selectedComponent.value.options = []
  }
  const count = selectedComponent.value.options.length + 1
  selectedComponent.value.options.push({
    label: `选项${count}`,
    value: `option${count}`
  })
}

// 删除选项
const removeOption = (index) => {
  selectedComponent.value.options.splice(index, 1)
}

// 清空画布
const clearCanvas = () => {
  formComponents.value = []
  selectedComponent.value = null
}

// 返回
const goBack = () => {
  router.push('/tool/form-list')
}

// 预览
const previewForm = () => {
  // 初始化预览数据
  formComponents.value.forEach(comp => {
    if (comp.type === 'checkbox') {
      previewData[comp.id] = []
    } else if (comp.type !== 'grid' && comp.type !== 'divider' && comp.type !== 'text') {
      previewData[comp.id] = comp.defaultValue || null
    }
  })
  previewVisible.value = true
}

// 保存表单
const saveForm = async () => {
  if (!formName.value) {
    ElMessage.warning('请输入表单名称')
    return
  }

  if (formComponents.value.length === 0) {
    ElMessage.warning('请添加至少一个组件')
    return
  }

  // 生成表单编码
  if (!formCode.value) {
    formCode.value = 'form_' + Date.now()
  }

  const config = {
    components: formComponents.value
  }

  try {
    if (formId.value) {
      await editForm({
        id: formId.value,
        name: formName.value,
        code: formCode.value,
        description: '',
        config: config,
        status: formStatus.value
      })
      ElMessage.success('保存成功')
    } else {
      const result = await addForm({
        name: formName.value,
        code: formCode.value,
        description: '',
        config: config,
        status: formStatus.value
      })
      ElMessage.success('创建成功')
      // 更新formId
      formId.value = result.data?.id
    }
  } catch (error) {
    ElMessage.error('保存失败')
  }
}

// 加载表单数据
const loadForm = async () => {
  if (!formId.value) return

  try {
    const result = await getFormInfo({ id: formId.value })
    if (result.code === 0 && result.data) {
      formName.value = result.data.name
      formCode.value = result.data.code
      formStatus.value = result.data.status
      if (result.data.config && result.data.config.components) {
        formComponents.value = result.data.config.components
      }
    }
  } catch (error) {
    console.error('加载表单失败:', error)
  }
}

onMounted(() => {
  loadForm()
})
</script>

<style scoped>
.form-design-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f5f5f5;
}

.design-toolbar {
  height: 60px;
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.toolbar-left,
.toolbar-center,
.toolbar-right {
  display: flex;
  align-items: center;
}

.design-main {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.component-panel {
  width: 280px;
  background: #fff;
  border-right: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;
}

.panel-title {
  height: 50px;
  line-height: 50px;
  padding: 0 16px;
  font-weight: 600;
  border-bottom: 1px solid #e8e8e8;
  background: #fafafa;
}

.component-tabs {
  flex: 1;
  overflow-y: auto;
}

.component-tabs :deep(.el-tabs__header) {
  margin: 0;
}

.component-tabs :deep(.el-tabs__nav-wrap) {
  padding: 0 12px;
}

.component-tabs :deep(.el-tabs__content) {
  padding: 12px;
}

.component-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.component-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 8px;
  background: #f5f7fa;
  border: 1px solid #e8e8e8;
  border-radius: 4px;
  cursor: move;
  transition: all 0.3s;
}

.component-item:hover {
  background: #ecf5ff;
  border-color: #409eff;
  color: #409eff;
}

.component-icon {
  font-size: 24px;
  margin-bottom: 8px;
}

.design-canvas {
  flex: 1;
  margin: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.canvas-header {
  padding: 20px;
  border-bottom: 1px solid #e8e8e8;
  text-align: center;
}

.canvas-header h3 {
  margin: 0;
  font-size: 18px;
  color: #333;
}

.canvas-content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  min-height: 400px;
}

.canvas-empty {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #999;
}

.canvas-empty p {
  margin-top: 16px;
  font-size: 14px;
}

.canvas-component {
  position: relative;
  padding: 8px;
  margin-bottom: 8px;
  border: 2px dashed transparent;
  border-radius: 4px;
  transition: all 0.3s;
}

.canvas-component:hover {
  border-color: #409eff;
  background: #f0f7ff;
}

.canvas-component.selected {
  border-color: #409eff;
  background: #ecf5ff;
}

.component-actions {
  position: absolute;
  top: -12px;
  right: 8px;
  display: none;
  gap: 4px;
  background: #fff;
  padding: 4px;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.canvas-component:hover .component-actions,
.canvas-component.selected .component-actions {
  display: flex;
}

.property-panel {
  width: 320px;
  background: #fff;
  border-left: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;
}

.property-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
}

.property-empty {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
}

.options-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.option-item {
  display: flex;
  gap: 8px;
  align-items: center;
}

.grid-layout {
  display: grid;
  gap: 12px;
  padding: 12px;
  background: #f5f7fa;
  border-radius: 4px;
}

.grid-col {
  min-height: 80px;
  background: #fff;
  border: 1px dashed #dcdfe6;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.grid-col-content {
  width: 100%;
  height: 100%;
  min-height: 80px;
}

.grid-col-label {
  font-size: 12px;
  color: #909399;
}

.upload-image {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
}

.upload-icon {
  font-size: 28px;
  color: #8c939d;
}

.editor-placeholder {
  min-height: 100px;
  background: #f5f7fa;
  border: 1px dashed #dcdfe6;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #909399;
}

.plain-text {
  line-height: 1.6;
}

.preview-form {
  padding: 20px;
}

.preview-form h3 {
  text-align: center;
  margin-bottom: 24px;
}

:deep(.el-divider--horizontal) {
  margin: 16px 0;
}
</style>

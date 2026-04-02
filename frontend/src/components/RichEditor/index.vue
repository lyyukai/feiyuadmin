<template>
  <div class="rich-editor-wrapper">
    <div ref="editorRef" class="rich-editor-container"></div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch, ref } from 'vue'
import E from '@wangeditor/editor'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  height: {
    type: String,
    default: '400px'
  },
  placeholder: {
    type: String,
    default: '请输入内容...'
  },
  // 上传图片的API地址
  uploadUrl: {
    type: String,
    default: '/adminapi/upload/image'
  },
  // 上传图片的headers
  uploadHeaders: {
    type: Object,
    default: () => ({})
  },
  // 禁用状态
  disabled: {
    type: Boolean,
    default: false
  },
  // 支持的最大图片大小(MB)
  maxImageSize: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['update:modelValue', 'change', 'focus', 'blur'])

const editorRef = ref(null)
let editor = null

onMounted(() => {
  editor = new E(editorRef.value)
  
  // 配置编辑器
  editor.config.height = parseInt(props.height) || 400
  editor.config.placeholder = props.placeholder
  editor.config.placeholderStyle = 'color: #999;'
  
  // 菜单配置
  editor.config.menus = [
    'head',        // 标题
    'bold',        // 加粗
    'italic',      // 斜体
    'underline',   // 下划线
    'strikeThrough', // 删除线
    'code',        // 行内代码
    'sub',         // 下标
    'sup',         // 上标
    'color',       // 文字颜色
    'bg',          // 背景色
    'quote',       // 引用
    'unorderedList', // 无序列表
    'orderedList',   // 有序列表
    'justify',     // 对齐
    'link',        // 链接
    'image',       // 图片
    'table',       // 表格
    'codeBlock',   // 代码块
    'undo',        // 撤销
    'redo'         // 重做
  ]
  
  // 粘贴板配置
  editor.config.pasteFilterStyle = true
  editor.config.pasteIgnoreImg = false
  
  // 链接配置
  editor.config.linkImgPrefix = ''
  editor.config.showLinkImg = true
  
  // 图片上传配置
  editor.config.uploadImgServer = props.uploadUrl
  editor.config.uploadImgHeaders = props.uploadHeaders
  editor.config.uploadImgMaxSize = props.maxImageSize * 1024 * 1024
  editor.config.uploadFileName = 'file'
  editor.config.uploadImgHooks = {
    customInsert: (insertImgFn, result) => {
      // 假设返回格式为 { url: '图片地址' }
      if (result.url) {
        insertImgFn(result.url)
      } else if (result.data && result.data.url) {
        insertImgFn(result.data.url)
      } else {
        insertImgFn(result)
      }
    },
    fail: (xhr, insertImgFn, reason) => {
      console.error('图片上传失败:', reason)
    }
  }
  
  // onchange 回调
  editor.config.onChange = (html) => {
    emit('update:modelValue', html)
    emit('change', html)
  }
  
  editor.config.onFocus = (newHtml) => {
    emit('focus', newHtml)
  }
  
  editor.config.onBlur = (newHtml) => {
    emit('blur', newHtml)
  }
  
  // 创建编辑器
  editor.create()
  
  // 初始化内容
  if (props.modelValue) {
    editor.txt.html(props.modelValue)
  }
  
  // 禁用状态
  if (props.disabled) {
    editor.disable()
  }
})

// 监听外部值变化
watch(() => props.modelValue, (newVal) => {
  if (editor && newVal !== editor.txt.html()) {
    editor.txt.html(newVal || '')
  }
})

// 监听禁用状态
watch(() => props.disabled, (newVal) => {
  if (editor) {
    if (newVal) {
      editor.disable()
    } else {
      editor.enable()
    }
  }
})

onBeforeUnmount(() => {
  if (editor) {
    editor.destroy()
    editor = null
  }
})

// 暴露方法
defineExpose({
  getHtml: () => editor ? editor.txt.html() : '',
  getText: () => editor ? editor.txt.text() : '',
  setHtml: (html) => editor && editor.txt.html(html),
  clear: () => editor && editor.txt.clear(),
  insertText: (text) => editor && editor.cmd.do('insertHTML', text),
  focus: () => editor && editor.focus()
})
</script>

<style scoped>
.rich-editor-wrapper {
  width: 100%;
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  overflow: hidden;
}

.rich-editor-container {
  width: 100%;
  min-height: v-bind(height);
}

/* 工具栏样式 */
:deep(.w-e-toolbar) {
  border-bottom: 1px solid #dcdfe6;
  background: #fafafa;
  flex-wrap: wrap;
}

:deep(.w-e-menu) {
  z-index: auto !important;
}

:deep(.w-e-panel-container) {
  z-index: 9999 !important;
}

/* 编辑区域样式 */
:deep(.w-e-text-container) {
  background: #fff;
}

:deep(.w-e-text) {
  padding: 12px 16px;
  min-height: v-bind(height);
}

/* 代码块样式 */
:deep(.w-e-text pre) {
  background: #282c34;
  color: #abb2bf;
  padding: 12px;
  border-radius: 4px;
  overflow-x: auto;
}

/* 表格样式 */
:deep(.w-e-text table) {
  border-collapse: collapse;
  width: 100%;
  margin: 10px 0;
}

:deep(.w-e-text table td),
:deep(.w-e-text table th) {
  border: 1px solid #dcdfe6;
  padding: 8px;
}

:deep(.w-e-text table th) {
  background: #fafafa;
  font-weight: 600;
}
</style>

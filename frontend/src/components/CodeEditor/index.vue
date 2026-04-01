<template>
  <div class="code-editor-wrapper" ref="wrapperRef">
    <div ref="editorRef" class="code-editor-container"></div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch, ref, nextTick } from 'vue'
import * as monaco from 'monaco-editor'
import editorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker'
import jsonWorker from 'monaco-editor/esm/vs/language/json/json.worker?worker'
import cssWorker from 'monaco-editor/esm/vs/language/css/css.worker?worker'
import htmlWorker from 'monaco-editor/esm/vs/language/html/html.worker?worker'
import tsWorker from 'monaco-editor/esm/vs/language/typescript/ts.worker?worker'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  language: {
    type: String,
    default: 'json'
  },
  theme: {
    type: String,
    default: 'vs-dark',
    validator: (val) => ['vs', 'vs-dark', 'hc-black', 'hc-light'].includes(val)
  },
  height: {
    type: String,
    default: '400px'
  },
  readonly: {
    type: Boolean,
    default: false
  },
  minHeight: {
    type: String,
    default: '200px'
  },
  maxHeight: {
    type: String,
    default: '800px'
  },
  lineNumbers: {
    type: String,
    default: 'on',
    validator: (val) => ['on', 'off', 'relative', 'interval'].includes(val)
  },
  folding: {
    type: Boolean,
    default: true
  },
  wordWrap: {
    type: String,
    default: 'off',
    validator: (val) => ['off', 'on', 'wordWrapColumn', 'bounded'].includes(val)
  },
  formatOnPaste: {
    type: Boolean,
    default: true
  },
  autoFormat: {
    type: Boolean,
    default: false
  },
  tabSize: {
    type: Number,
    default: 2
  }
})

const emit = defineEmits(['update:modelValue', 'change', 'focus', 'blur', 'format'])

const editorRef = ref(null)
const wrapperRef = ref(null)
let editor = null
let monacoEditor = null

// Monaco Editor worker 配置
self.MonacoEnvironment = {
  getWorker(_, label) {
    if (label === 'json') {
      return new jsonWorker()
    }
    if (label === 'css' || label === 'scss' || label === 'less') {
      return new cssWorker()
    }
    if (label === 'html' || label === 'handlebars' || label === 'razor') {
      return new htmlWorker()
    }
    if (label === 'typescript' || label === 'javascript') {
      return new tsWorker()
    }
    return new editorWorker()
  }
}

const supportedLanguages = [
  'json', 'yaml', 'sql', 'xml', 'html', 'css', 'javascript', 'typescript', 
  'php', 'python', 'java', 'csharp', 'c', 'cpp', 'go', 'rust', 'markdown', 'shell'
]

onMounted(async () => {
  await nextTick()
  
  monacoEditor = monaco.editor.create(editorRef.value, {
    value: props.modelValue,
    language: props.language,
    theme: props.theme,
    height: props.height,
    minHeight: parseInt(props.minHeight) || 200,
    maxHeight: parseInt(props.maxHeight) || 800,
    readOnly: props.readonly,
    lineNumbers: props.lineNumbers,
    folding: props.folding,
    wordWrap: props.wordWrap,
    formatOnPaste: props.formatOnPaste,
    tabSize: props.tabSize,
    automaticLayout: true,
    scrollBeyondLastLine: false,
    fontSize: 14,
    fontFamily: "'Fira Code', 'Consolas', 'Monaco', monospace",
    fontLigatures: true,
    renderWhitespace: 'selection',
    bracketPairColorization: {
      enabled: true
    },
    matchBrackets: 'always',
    cursorBlinking: 'smooth',
    cursorSmoothCaretAnimation: 'on',
    smoothScrolling: true,
    contextmenu: true,
    mouseWheelZoom: true,
    padding: { top: 12, bottom: 12 },
    suggestOnTriggerCharacters: true,
    acceptSuggestionOnEnter: 'on',
    quickSuggestions: {
      other: true,
      comments: false,
      strings: true
    }
  })
  
  // 内容变化监听
  monacoEditor.onDidChangeModelContent(() => {
    const value = monacoEditor.getValue()
    emit('update:modelValue', value)
    emit('change', value)
  })
  
  // 焦点事件
  monacoEditor.onDidFocusEditorText(() => {
    emit('focus')
  })
  
  monacoEditor.onDidBlurEditorText(() => {
    emit('blur')
  })
  
  // 自动格式化
  if (props.autoFormat && props.modelValue) {
    setTimeout(() => {
      format()
    }, 100)
  }
})

// 监听外部值变化
watch(() => props.modelValue, (newVal) => {
  if (monacoEditor && newVal !== monacoEditor.getValue()) {
    const position = monacoEditor.getPosition()
    monacoEditor.setValue(newVal || '')
    if (position) {
      monacoEditor.setPosition(position)
    }
  }
})

// 监听语言变化
watch(() => props.language, (newLang) => {
  if (monacoEditor) {
    const model = monacoEditor.getModel()
    if (model) {
      monaco.editor.setModelLanguage(model, newLang)
    }
  }
})

// 监听主题变化
watch(() => props.theme, (newTheme) => {
  if (monacoEditor) {
    monaco.editor.setTheme(newTheme)
  }
})

// 监听只读状态
watch(() => props.readonly, (newVal) => {
  if (monacoEditor) {
    monacoEditor.updateOptions({ readOnly: newVal })
  }
})

onBeforeUnmount(() => {
  if (monacoEditor) {
    monacoEditor.dispose()
    monacoEditor = null
  }
})

// 暴露方法
defineExpose({
  getValue: () => monacoEditor ? monacoEditor.getValue() : '',
  getLanguage: () => monacoEditor ? monacoEditor.getModel()?.getLanguageId() : '',
  setValue: (value) => monacoEditor && monacoEditor.setValue(value || ''),
  setLanguage: (lang) => {
    if (monacoEditor) {
      const model = monacoEditor.getModel()
      if (model) {
        monaco.editor.setModelLanguage(model, lang)
      }
    }
  },
  setTheme: (theme) => monacoEditor && monaco.editor.setTheme(theme),
  format: () => {
    if (monacoEditor) {
      monacoEditor.getAction('editor.action.formatDocument')?.run()
      emit('format')
    }
  },
  formatJSON: () => {
    if (monacoEditor && props.language === 'json') {
      try {
        const value = monacoEditor.getValue()
        const parsed = JSON.parse(value)
        monacoEditor.setValue(JSON.stringify(parsed, null, props.tabSize))
        emit('format')
      } catch (e) {
        console.error('JSON格式化失败:', e)
      }
    }
  },
  formatSQL: () => {
    // SQL格式化调用
    emit('format')
  },
  getEditor: () => monacoEditor,
  focus: () => monacoEditor && monacoEditor.focus(),
  goToLine: (lineNumber) => monacoEditor && monacoEditor.revealLine(lineNumber),
  setScrollTop: (scrollTop) => monacoEditor && monacoEditor.setScrollTop(scrollTop),
  getScrollHeight: () => monacoEditor ? monacoEditor.getScrollHeight() : 0
})
</script>

<style scoped>
.code-editor-wrapper {
  width: 100%;
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  overflow: hidden;
  background: #1e1e1e;
}

.code-editor-container {
  width: 100%;
  height: v-bind(height);
  min-height: v-bind(minHeight);
  max-height: v-bind(maxHeight);
}

/* 暗色主题覆盖 */
:deep(.monaco-editor) {
  border-radius: 0;
}

:deep(.monaco-editor .margin) {
  background: #1e1e1e !important;
}
</style>

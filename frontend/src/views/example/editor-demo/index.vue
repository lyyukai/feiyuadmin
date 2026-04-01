<template>
  <div class="editor-demo-container">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>示例</el-breadcrumb-item>
      <el-breadcrumb-item>编辑器示例</el-breadcrumb-item>
    </el-breadcrumb>

    <el-card class="mt-4">
      <template #header>
        <div class="card-header">
          <span>编辑器示例</span>
          <el-button type="primary" @click="handleSubmit">提交内容</el-button>
        </div>
      </template>

      <el-tabs v-model="activeTab" class="editor-tabs">
        <!-- 富文本编辑器 -->
        <el-tab-pane label="富文本编辑器" name="rich">
          <div class="editor-section">
            <h3 class="section-title">富文本编辑器 (wangEditor)</h3>
            <p class="section-desc">支持文字格式化、标题、列表、代码块、图片上传、表格等功能</p>
            
            <div class="toolbar-row">
              <el-button size="small" @click="setRichContent('<h1>一级标题</h1>')">H1</el-button>
              <el-button size="small" @click="setRichContent('<h2>二级标题</h2>')">H2</el-button>
              <el-button size="small" @click="setRichContent('<p>段落文本</p>')">段落</el-button>
              <el-button size="small" @click="setRichContent('<blockquote>引用文本</blockquote>')">引用</el-button>
              <el-button size="small" @click="setRichContent('<pre><code>代码块内容</code></pre>')">代码块</el-button>
              <el-button size="small" type="danger" @click="clearRich">清空</el-button>
            </div>

            <rich-editor
              ref="richEditorRef"
              v-model="richContent"
              height="400px"
              placeholder="请输入富文本内容..."
              @change="handleRichChange"
              @focus="handleRichFocus"
              @blur="handleRichBlur"
            />

            <div class="preview-box mt-4">
              <h4>内容预览：</h4>
              <div class="preview-content" v-html="richContent"></div>
            </div>
          </div>
        </el-tab-pane>

        <!-- 代码编辑器 -->
        <el-tab-pane label="代码编辑器" name="code">
          <div class="editor-section">
            <h3 class="section-title">代码编辑器 (Monaco Editor)</h3>
            <p class="section-desc">支持语法高亮、行号、代码折叠、格式化等功能</p>
            
            <div class="toolbar-row">
              <el-select v-model="codeLanguage" size="small" style="width: 150px;" placeholder="选择语言" @change="handleLanguageChange">
                <el-option label="JSON" value="json" />
                <el-option label="YAML" value="yaml" />
                <el-option label="SQL" value="sql" />
                <el-option label="XML" value="xml" />
                <el-option label="HTML" value="html" />
                <el-option label="CSS" value="css" />
                <el-option label="JavaScript" value="javascript" />
                <el-option label="TypeScript" value="typescript" />
                <el-option label="PHP" value="php" />
                <el-option label="Python" value="python" />
                <el-option label="Java" value="java" />
                <el-option label="Shell" value="shell" />
              </el-select>
              
              <el-select v-model="codeTheme" size="small" style="width: 150px; margin-left: 10px;" placeholder="选择主题">
                <el-option label="浅色主题" value="vs" />
                <el-option label="深色主题" value="vs-dark" />
                <el-option label="高对比浅色" value="hc-light" />
                <el-option label="高对比深色" value="hc-black" />
              </el-select>

              <el-button size="small" type="primary" style="margin-left: 10px;" @click="formatCode">
                <el-icon><MagicStick /></el-icon> 格式化
              </el-button>
              <el-button size="small" type="success" @click="validateCode">
                <el-icon><Check /></el-icon> 校验
              </el-button>
              <el-button size="small" @click="copyCode">
                <el-icon><DocumentCopy /></el-icon> 复制
              </el-button>
              <el-button size="small" type="danger" @click="clearCode">
                <el-icon><Delete /></el-icon> 清空
              </el-button>
            </div>

            <div class="editor-info">
              <span>语言: {{ codeLanguage }}</span>
              <span>行数: {{ codeLineCount }}</span>
              <span>字符数: {{ codeCharCount }}</span>
            </div>

            <code-editor
              ref="codeEditorRef"
              v-model="codeContent"
              :language="codeLanguage"
              :theme="codeTheme"
              height="450px"
              @change="handleCodeChange"
              @format="handleFormat"
            />

            <div v-if="codeError" class="error-box mt-4">
              <el-alert :title="codeError" type="error" show-icon :closable="false" />
            </div>
          </div>
        </el-tab-pane>

        <!-- 综合示例 -->
        <el-tab-pane label="综合示例" name="combined">
          <div class="editor-section">
            <h3 class="section-title">综合使用示例</h3>
            <p class="section-desc">文章编辑：使用富文本编辑器编写正文，代码编辑器编写配置示例</p>
            
            <el-form :model="articleForm" label-width="80px">
              <el-form-item label="文章标题">
                <el-input v-model="articleForm.title" placeholder="请输入文章标题" />
              </el-form-item>
              
              <el-form-item label="文章摘要">
                <el-input v-model="articleForm.summary" type="textarea" :rows="2" placeholder="请输入文章摘要" />
              </el-form-item>
              
              <el-form-item label="正文内容">
                <rich-editor
                  v-model="articleForm.content"
                  height="350px"
                  placeholder="请输入正文内容..."
                />
              </el-form-item>
              
              <el-form-item label="代码示例">
                <code-editor
                  v-model="articleForm.codeExample"
                  language="json"
                  height="250px"
                />
              </el-form-item>
            </el-form>

            <div class="form-actions mt-4">
              <el-button type="primary" @click="submitArticle">保存文章</el-button>
              <el-button @click="resetArticle">重置</el-button>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import { MagicStick, Check, DocumentCopy, Delete } from '@element-plus/icons-vue'

const activeTab = ref('rich')
const richEditorRef = ref(null)
const codeEditorRef = ref(null)

// 富文本编辑器
const richContent = ref('<p>欢迎使用<strong>富文本编辑器</strong>！</p><p>这是一个示例内容，包含以下功能：</p><ul><li>文字格式化（加粗、斜体、下划线）</li><li>标题（H1-H6）</li><li>有序和无序列表</li><li>代码块</li><li>图片上传</li><li>表格编辑</li><li>链接插入</li></ul><blockquote>这是一段引用文本</blockquote>')

const handleRichChange = (html) => {
  console.log('富文本内容变化:', html)
}

const handleRichFocus = () => {
  console.log('富文本编辑器获得焦点')
}

const handleRichBlur = () => {
  console.log('富文本编辑器失去焦点')
}

const setRichContent = (html) => {
  richEditorRef.value?.setHtml(html)
}

const clearRich = () => {
  richEditorRef.value?.clear()
}

// 代码编辑器
const codeLanguage = ref('json')
const codeTheme = ref('vs-dark')
const codeContent = ref(`{
  "name": "feiyuadmin",
  "version": "2.0.0",
  "description": "通用开源后台管理系统",
  "database": {
    "host": "localhost",
    "port": 3306,
    "name": "feiyuadmin"
  },
  "features": [
    "代码生成器",
    "多租户模式",
    "定时任务",
    "工作流引擎"
  ]
}`)

const codeError = ref('')

const codeLineCount = computed(() => {
  return codeContent.value.split('\n').length
})

const codeCharCount = computed(() => {
  return codeContent.value.length
})

const handleCodeChange = (value) => {
  console.log('代码内容变化:', value.substring(0, 50) + '...')
  codeError.value = ''
}

const handleLanguageChange = (lang) => {
  console.log('语言切换:', lang)
}

const handleFormat = () => {
  ElMessage.success('代码格式化完成')
}

const formatCode = () => {
  if (codeLanguage.value === 'json') {
    codeEditorRef.value?.formatJSON()
    ElMessage.success('JSON格式化完成')
  } else {
    codeEditorRef.value?.format()
    ElMessage.success('格式化完成')
  }
}

const validateCode = () => {
  codeError.value = ''
  if (codeLanguage.value === 'json') {
    try {
      JSON.parse(codeContent.value)
      ElMessage.success('JSON格式正确')
    } catch (e) {
      codeError.value = `JSON解析错误: ${e.message}`
    }
  } else {
    ElMessage.info('该语言的语法校验功能开发中')
  }
}

const copyCode = async () => {
  try {
    await navigator.clipboard.writeText(codeContent.value)
    ElMessage.success('代码已复制到剪贴板')
  } catch (e) {
    ElMessage.error('复制失败')
  }
}

const clearCode = () => {
  codeEditorRef.value?.setValue('')
}

// 综合示例
const articleForm = reactive({
  title: 'Vue 3 Composition API 实战指南',
  summary: '本文详细介绍Vue 3 Composition API的使用方法和最佳实践',
  content: '<h2>简介</h2><p>Vue 3引入了全新的Composition API，为组件逻辑组织提供了更灵活的方式。</p><h2>核心概念</h2><p>Composition API主要包含以下核心概念：</p><ul><li><strong>setup()</strong> - 组件选项的入口点</li><li><strong>reactive</strong> - 创建响应式状态</li><li><strong>ref</strong> - 创建响应式引用</li><li><strong>computed</strong> - 计算属性</li></ul>',
  codeExample: `// 使用 Composition API
import { ref, reactive, computed } from 'vue'

export default {
  setup() {
    // 响应式状态
    const count = ref(0)
    const state = reactive({
      name: 'FeiyuAdmin'
    })
    
    // 计算属性
    const doubleCount = computed(() => count.value * 2)
    
    // 方法
    const increment = () => {
      count.value++
    }
    
    return { count, state, doubleCount, increment }
  }
}`
})

const submitArticle = () => {
  ElMessage.success('文章保存成功')
  console.log('文章数据:', articleForm)
}

const resetArticle = () => {
  articleForm.title = ''
  articleForm.summary = ''
  articleForm.content = ''
  articleForm.codeExample = ''
}

const handleSubmit = () => {
  console.log('富文本内容:', richContent.value)
  console.log('代码内容:', codeContent.value)
  ElMessage.success('内容已提交')
}
</script>

<style scoped>
.editor-demo-container {
  padding: 20px;
}

.mt-4 {
  margin-top: 16px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.editor-section {
  padding: 10px 0;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #303133;
}

.section-desc {
  color: #909399;
  font-size: 14px;
  margin-bottom: 16px;
}

.toolbar-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
  align-items: center;
}

.editor-info {
  display: flex;
  gap: 20px;
  margin-bottom: 12px;
  font-size: 13px;
  color: #909399;
}

.preview-box {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  padding: 16px;
  background: #fafafa;
}

.preview-box h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: #606266;
}

.preview-content {
  background: #fff;
  padding: 12px;
  border-radius: 4px;
  min-height: 100px;
  line-height: 1.8;
}

.preview-content :deep(h1) {
  font-size: 24px;
  margin: 16px 0;
}

.preview-content :deep(h2) {
  font-size: 20px;
  margin: 14px 0;
}

.preview-content :deep(pre) {
  background: #282c34;
  color: #abb2bf;
  padding: 12px;
  border-radius: 4px;
  overflow-x: auto;
}

.preview-content :deep(blockquote) {
  border-left: 4px solid #409eff;
  padding-left: 16px;
  margin: 12px 0;
  color: #606266;
}

.preview-content :deep(code) {
  background: #f1f1f1;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: 'Fira Code', monospace;
}

.error-box {
  border-radius: 4px;
}

.form-actions {
  text-align: center;
}

.editor-tabs :deep(.el-tabs__header) {
  margin-bottom: 20px;
}
</style>

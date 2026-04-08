<template>
  <div class="ai-chat-container">
    <!-- 页面头部 -->
    <div class="chat-header">
      <div class="header-left">
        <el-icon class="header-icon"><MagicStick /></el-icon>
        <span class="header-title">AI 助手</span>
      </div>
      <div class="header-right">
        <el-button size="small" @click="showConfigDialog = true">
          <el-icon><Setting /></el-icon>
          API配置
        </el-button>
        <el-select v-model="currentProvider" placeholder="选择AI提供商" size="default" style="width: 160px">
          <el-option v-for="(name, key) in providers" :key="key" :label="name" :value="key" />
        </el-select>
      </div>
    </div>

    <!-- API配置弹窗 -->
    <el-dialog v-model="showConfigDialog" title="AI 接口配置" width="500px" :close-on-click-modal="false">
      <el-form label-width="90px" label-position="left">
        <el-form-item label="Provider">
          <el-select v-model="apiConfig.provider" placeholder="选择AI提供商" style="width: 100%">
            <el-option label="文心一言 (wenxin)" value="wenxin" />
            <el-option label="通义千问 (qwen)" value="qwen" />
            <el-option label="OpenAI GPT" value="openai" />
          </el-select>
        </el-form-item>
        <el-form-item label="API Key">
          <el-input v-model="apiConfig.apiKey" placeholder="请输入 API Key" show-password />
        </el-form-item>
        <el-form-item label="Base URL">
          <el-input v-model="apiConfig.baseUrl" placeholder="如 https://api.openai.com" />
        </el-form-item>
        <el-form-item label="Model">
          <el-input v-model="apiConfig.model" placeholder="如 ernie-4.0 / gpt-4" />
        </el-form-item>
        <el-form-item label="Temperature">
          <el-slider v-model="apiConfig.temperature" :min="0" :max="1" :step="0.1" show-stops :marks="{0:'0', 0.5:'0.5', 1:'1'}" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showConfigDialog = false">取消</el-button>
        <el-button type="primary" @click="saveApiConfig">保存配置</el-button>
      </template>
    </el-dialog>

    <!-- 消息列表 -->
    <div class="chat-messages" ref="messagesRef">
      <!-- 空状态 -->
      <div v-if="messages.length === 0" class="empty-state">
        <div class="empty-icon">
          <el-icon :size="64"><ChatDotRound /></el-icon>
        </div>
        <h3>欢迎使用 AI 助手</h3>
        <p>我可以帮你完成各种任务，比如查询数据、编写代码、分析问题等</p>
        <div class="example-questions">
          <div class="example-title">试试这样问我：</div>
          <div
            v-for="(example, index) in exampleQuestions"
            :key="index"
            class="example-item"
            @click="selectExample(example)"
          >
            {{ example }}
          </div>
        </div>
      </div>

      <!-- 消息列表 -->
      <div v-else>
        <div
          v-for="(msg, index) in messages"
          :key="index"
          :class="['message-item', msg.role === 'user' ? 'user-message' : 'ai-message']"
        >
          <div class="message-avatar">
            <div :class="msg.role === 'user' ? 'avatar-user' : 'avatar-ai'">
              <el-icon v-if="msg.role === 'user'"><User /></el-icon>
              <el-icon v-else><MagicStick /></el-icon>
            </div>
          </div>
          <div class="message-content">
            <div class="message-bubble" v-html="formatContent(msg.content)"></div>
          </div>
        </div>
      </div>

      <!-- 加载中 -->
      <div v-if="loading" class="message-item ai-message">
        <div class="message-avatar">
          <div class="avatar-ai">
            <el-icon><MagicStick /></el-icon>
          </div>
        </div>
        <div class="message-content">
          <div class="message-bubble thinking">
            <span class="thinking-dot"></span>
            <span class="thinking-dot"></span>
            <span class="thinking-dot"></span>
            AI 正在思考...
          </div>
        </div>
      </div>
    </div>

    <!-- 输入区域 -->
    <div class="chat-input-area">
      <div class="input-wrapper">
        <el-input
          v-model="inputText"
          type="textarea"
          :rows="3"
          placeholder="输入消息，Ctrl+Enter 发送..."
          :disabled="loading"
          @keydown="handleKeyDown"
          resize="none"
        />
        <el-button
          type="primary"
          :loading="loading"
          :disabled="!inputText.trim()"
          class="send-btn"
          @click="sendMessage"
        >
          <el-icon><Promotion /></el-icon>
          发送
        </el-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted } from 'vue'
import { MagicStick, ChatDotRound, User, Promotion, Setting } from '@element-plus/icons-vue'
import { chat, getAiProviders } from '@/api/ai'
import { ElMessage } from 'element-plus'

// API配置
const apiConfig = reactive({
  provider: localStorage.getItem('ai_provider') || 'wenxin',
  apiKey: localStorage.getItem('ai_api_key') || '',
  baseUrl: localStorage.getItem('ai_base_url') || '',
  model: localStorage.getItem('ai_model') || ''
})

const showConfigDialog = ref(false)

const saveApiConfig = () => {
  localStorage.setItem('ai_provider', apiConfig.provider)
  localStorage.setItem('ai_api_key', apiConfig.apiKey)
  localStorage.setItem('ai_base_url', apiConfig.baseUrl)
  localStorage.setItem('ai_model', apiConfig.model)
  currentProvider.value = apiConfig.provider
  showConfigDialog.value = false
  ElMessage.success('API配置已保存')
}

// 状态
const messages = ref([])
const inputText = ref('')
const loading = ref(false)
const currentProvider = ref('wenxin')
const providers = reactive({})
const messagesRef = ref(null)

// 示例问题
const exampleQuestions = [
  '查一下今天的新增用户',
  '帮我写一个请假流程',
  '介绍一下公司的组织架构',
  '如何创建定时任务？'
]

// 加载 providers
const loadProviders = async () => {
  try {
    const res = await getAiProviders()
    if (res.code === 0) {
      Object.assign(providers, res.data)
    }
  } catch (error) {
    console.error('加载AI提供商失败:', error)
  }
}

// 发送消息
const sendMessage = async () => {
  const text = inputText.value.trim()
  if (!text || loading.value) return

  // 添加用户消息
  messages.value.push({
    role: 'user',
    content: text
  })
  inputText.value = ''
  loading.value = true
  scrollToBottom()

  try {
    const chatMessages = messages.value.map(msg => ({
      role: msg.role,
      content: msg.content
    }))

    const res = await chat({
      messages: chatMessages,
      provider: currentProvider.value,
      api_key: apiConfig.apiKey,
      base_url: apiConfig.baseUrl,
      model: apiConfig.model
    })

    if (res.code === 0) {
      messages.value.push({
        role: 'assistant',
        content: res.data.content
      })
    } else {
      ElMessage.error(res.msg || 'AI 回复失败')
      messages.value.push({
        role: 'assistant',
        content: '抱歉，AI 处理失败了，请稍后重试。'
      })
    }
  } catch (error) {
    console.error('AI对话失败:', error)
    ElMessage.error('AI 对话失败，请检查网络或API配置')
    messages.value.push({
      role: 'assistant',
      content: '抱歉，AI 服务暂时不可用，请稍后重试。'
    })
  } finally {
    loading.value = false
    scrollToBottom()
  }
}

// 选择示例问题
const selectExample = (question) => {
  inputText.value = question
  sendMessage()
}

// 格式化内容（支持换行）
const formatContent = (content) => {
  if (!content) return ''
  return content.replace(/\n/g, '<br>')
}

// 滚动到底部
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesRef.value) {
      messagesRef.value.scrollTop = messagesRef.value.scrollHeight
    }
  })
}

// 键盘事件
const handleKeyDown = (e) => {
  if (e.ctrlKey && e.key === 'Enter') {
    e.preventDefault()
    sendMessage()
  }
}

onMounted(() => {
  // 从 localStorage 恢复配置
  currentProvider.value = localStorage.getItem('ai_provider') || 'wenxin'
  loadProviders()
})
</script>

<style scoped>
.ai-chat-container {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 140px);
  background: #f5f7fa;
  border-radius: 8px;
  overflow: hidden;
}

/* 头部 */
.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #fff;
  border-bottom: 1px solid #e4e7ed;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-icon {
  font-size: 20px;
  color: #409eff;
}

.header-title {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

/* 消息列表 */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

/* 空状态 */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
  color: #606266;
}

.empty-icon {
  color: #409eff;
  margin-bottom: 16px;
  opacity: 0.8;
}

.empty-state h3 {
  margin: 0 0 8px;
  font-size: 20px;
  color: #303133;
}

.empty-state p {
  margin: 0 0 24px;
  font-size: 14px;
  color: #909399;
}

.example-questions {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  width: 400px;
}

.example-title {
  font-size: 14px;
  color: #909399;
  margin-bottom: 12px;
}

.example-item {
  padding: 10px 16px;
  background: #f5f7fa;
  border-radius: 6px;
  margin-bottom: 8px;
  cursor: pointer;
  font-size: 14px;
  color: #606266;
  transition: all 0.3s;
}

.example-item:last-child {
  margin-bottom: 0;
}

.example-item:hover {
  background: #e4e7ed;
  color: #409eff;
}

/* 消息样式 */
.message-item {
  display: flex;
  margin-bottom: 20px;
}

.user-message {
  flex-direction: row-reverse;
}

.ai-message {
  flex-direction: row;
}

.message-avatar {
  flex-shrink: 0;
  margin: 0 10px;
}

.avatar-user,
.avatar-ai {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.avatar-user {
  background: #67c23a;
  color: #fff;
}

.avatar-ai {
  background: #409eff;
  color: #fff;
}

.message-content {
  max-width: 70%;
}

.message-bubble {
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 14px;
  line-height: 1.6;
  word-break: break-word;
}

.user-message .message-bubble {
  background: #67c23a;
  color: #fff;
  border-bottom-right-radius: 4px;
}

.ai-message .message-bubble {
  background: #fff;
  color: #303133;
  border-bottom-left-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* 思考动画 */
.thinking {
  display: flex;
  align-items: center;
  gap: 4px;
}

.thinking-dot {
  width: 6px;
  height: 6px;
  background: #909399;
  border-radius: 50%;
  animation: thinking 1.4s infinite ease-in-out;
}

.thinking-dot:nth-child(1) {
  animation-delay: 0s;
}

.thinking-dot:nth-child(2) {
  animation-delay: 0.2s;
}

.thinking-dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes thinking {
  0%,
  80%,
  100% {
    transform: scale(0.6);
    opacity: 0.4;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* 输入区域 */
.chat-input-area {
  padding: 16px 20px;
  background: #fff;
  border-top: 1px solid #e4e7ed;
}

.input-wrapper {
  display: flex;
  gap: 12px;
  align-items: flex-end;
}

.input-wrapper :deep(.el-textarea__inner) {
  border-radius: 8px;
  font-size: 14px;
}

.send-btn {
  flex-shrink: 0;
  height: 68px;
  border-radius: 8px;
}
</style>

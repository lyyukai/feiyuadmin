<template>
  <div class="ai-chat-wrapper">
    <!-- 悬浮按钮 -->
    <transition name="float-btn">
      <div v-if="!isOpen" class="float-btn" @click="toggleOpen" title="AI助手">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z" fill="currentColor"/>
          <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z" fill="none"/>
          <circle cx="9" cy="13" r="1.5" fill="currentColor"/>
          <circle cx="12" cy="13" r="1.5" fill="currentColor"/>
          <circle cx="15" cy="13" r="1.5" fill="currentColor"/>
        </svg>
        <span class="btn-label">AI助手</span>
      </div>
    </transition>

    <!-- 聊天窗口 -->
    <transition name="chat-slide">
      <div v-if="isOpen" class="chat-window">
        <!-- 头部 -->
        <div class="chat-header">
          <div class="header-left">
            <div class="ai-avatar">
              <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
                <rect width="48" height="48" rx="12" fill="#2563EB"/>
                <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
                <circle cx="24" cy="24" r="5" fill="#2563EB"/>
              </svg>
            </div>
            <div class="header-info">
              <span class="ai-name">飞鱼 AI 助手</span>
              <span class="ai-status">在线</span>
            </div>
          </div>
          <div class="header-actions">
            <el-tooltip content="清空对话" placement="bottom">
              <el-icon @click="clearChat" class="action-icon"><Delete /></el-icon>
            </el-tooltip>
            <el-icon @click="toggleOpen" class="action-icon close-icon"><Close /></el-icon>
          </div>
        </div>

        <!-- 消息列表 -->
        <div class="chat-messages" ref="messagesRef">
          <div v-if="messages.length === 0" class="empty-state">
            <div class="empty-icon">🤖</div>
            <p>你好，我是飞鱼 AI 助手</p>
            <p class="empty-hint">我可以帮你：</p>
            <ul class="empty-list">
              <li>📝 解答飞鱼 Admin 使用问题</li>
              <li>💻 提供开发建议和代码示例</li>
              <li>🔧 辅助进行 NL2SQL 自然语言查询</li>
              <li>📖 解读系统功能和工作流</li>
            </ul>
          </div>
          <div
            v-for="(msg, idx) in messages"
            :key="idx"
            :class="['message', msg.role]"
          >
            <div v-if="msg.role === 'ai'" class="msg-avatar ai">
              <svg width="18" height="18" viewBox="0 0 48 48" fill="none">
                <rect width="48" height="48" rx="12" fill="#2563EB"/>
                <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
                <circle cx="24" cy="24" r="5" fill="#2563EB"/>
              </svg>
            </div>
            <div class="msg-content">
              <div v-if="msg.role === 'ai'" class="ai-bubble">
                <span v-if="msg.loading" class="typing-dots">
                  <span></span><span></span><span></span>
                </span>
                <span v-else v-html="formatMarkdown(msg.content)"></span>
              </div>
              <div v-else class="user-bubble">{{ msg.content }}</div>
              <div v-if="msg.role === 'ai'" class="ai-actions">
                <span class="copy-btn" @click="copyMsg(msg.content)">复制</span>
              </div>
            </div>
            <div v-if="msg.role === 'user'" class="msg-avatar user">
              <el-icon><UserFilled /></el-icon>
            </div>
          </div>
        </div>

        <!-- 快捷问题 -->
        <div v-if="messages.length === 0" class="quick-questions">
          <span class="quick-label">试试问我：</span>
          <div class="quick-btns">
            <el-button
              v-for="q in quickQuestions"
              :key="q"
              size="small"
              @click="sendQuickQuestion(q)"
            >{{ q }}</el-button>
          </div>
        </div>

        <!-- 输入区域 -->
        <div class="chat-input">
          <div class="input-row">
            <el-input
              v-model="inputText"
              :rows="2"
              type="textarea"
              resize="none"
              placeholder="输入问题，Shift+Enter换行，Enter发送..."
              @keydown.enter.exact.prevent="handleSend"
              @keydown.enter.shift.exact="e => e.preventDefault()"
            />
            <el-button
              type="primary"
              class="send-btn"
              :disabled="!inputText.trim() || sending"
              :loading="sending"
              @click="handleSend"
            >
              <el-icon v-if="!sending"><Promotion /></el-icon>
              <span v-if="!sending">发送</span>
            </el-button>
          </div>
          <div class="input-hint">Enter 发送 · Shift+Enter 换行</div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, nextTick, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Delete, Close, Promotion, UserFilled } from '@element-plus/icons-vue'

const isOpen = ref(false)
const inputText = ref('')
const sending = ref(false)
const messages = ref([])
const messagesRef = ref(null)

const quickQuestions = [
  '如何新增一个用户？',
  '代码生成器怎么用？',
  '工作流如何配置？',
  '多租户模式怎么开启？',
]

const toggleOpen = () => {
  isOpen.value = !isOpen.value
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesRef.value) {
      messagesRef.value.scrollTop = messagesRef.value.scrollHeight
    }
  })
}

const sendMessage = async (text) => {
  if (!text.trim() || sending.value) return
  const userMsg = { role: 'user', content: text.trim() }
  messages.value.push(userMsg)
  inputText.value = ''
  sending.value = true
  scrollToBottom()

  // 添加AI加载中消息
  const aiMsg = { role: 'ai', content: '', loading: true }
  messages.value.push(aiMsg)

  try {
    // 调用后端API
    const res = await fetch('/pcapi/ai/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('token') || ''}` },
      body: JSON.stringify({
        message: userMsg.content,
        history: messages.value.filter(m => !m.loading).map(m => ({ role: m.role, content: m.content }))
      })
    }).catch(() => null)

    let reply = ''
    if (res && res.ok) {
      const data = await res.json()
      reply = data?.data?.reply || data?.reply || ''
    }

    if (!reply) {
      // Mock响应
      reply = getMockReply(userMsg.content)
    }

    messages.value = messages.value.filter(m => !m.loading)
    messages.value.push({ role: 'ai', content: reply })
  } catch {
    messages.value = messages.value.filter(m => !m.loading)
    messages.value.push({ role: 'ai', content: '抱歉，服务暂时不可用，请稍后重试。' })
  } finally {
    sending.value = false
    scrollToBottom()
    saveHistory()
  }
}

const getMockReply = (question) => {
  const q = question.toLowerCase()
  if (q.includes('用户') || q.includes('新增')) {
    return `**用户管理操作指南**\n\n1. 进入 **系统管理 → 用户管理**\n2. 点击 **新增用户** 按钮\n3. 填写用户名、密码、昵称\n4. 选择所属部门和岗位\n5. 分配对应角色\n6. 点击 **确定** 保存\n\n如需批量导入，可使用 **代码生成器** 导出模板后批量导入。`
  }
  if (q.includes('代码生成') || q.includes('crud')) {
    return `**代码生成器使用流程**\n\n1. 进入 **系统管理 → 代码生成器**\n2. 选择要生成的数据表\n3. 配置基本信息（作者、模块名）\n4. 设置字段映射和验证规则\n5. 点击 **生成代码**\n6. 下载并解压覆盖到对应目录\n\n生成后会自动创建：Model、Controller、路由、前端API文件。`
  }
  if (q.includes('工作流') || q.includes('审批')) {
    return `**工作流配置步骤**\n\n1. 进入 **工作流 → 流程设计**\n2. 拖拽节点绘制流程图\n3. 配置每个节点的：\n   - 审批人（用户/角色/部门）\n   - 审批动作（同意/拒绝/转交）\n   - 表单字段权限\n4. 设置开始节点和结束节点\n5. 保存并发布流程\n\n实例管理中可查看所有流程实例和待办任务。`
  }
  if (q.includes('租户') || q.includes('多租户')) {
    return `**多租户模式开启**\n\n1. 进入 **系统设置 → 系统配置**\n2. 找到 **多租户模式** 配置项\n3. 开启开关并保存\n4. 进入 **租户管理** 新增租户\n5. 为租户分配套餐（基础版/专业版/旗舰版）\n6. 各租户数据完全隔离，登录入口独立\n\n建议生产环境开启 Redis 缓存以提升多租户查询性能。`
  }
  return `感谢你的提问！飞鱼 Admin 是一个功能完备的企业级后台框架。\n\n你可以尝试问我：\n- 如何新增用户？\n- 代码生成器怎么用？\n- 工作流如何配置？\n- 多租户模式怎么开启？\n\n或者前往 **技术文档** 页面获取更详细的开发指南。`
}

const handleSend = () => {
  sendMessage(inputText.value)
}

const sendQuickQuestion = (q) => {
  sendMessage(q)
}

const clearChat = () => {
  messages.value = []
  localStorage.removeItem('ai_chat_history')
}

const copyMsg = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    ElMessage.success('已复制到剪贴板')
  })
}

const formatMarkdown = (text) => {
  // 简单Markdown渲染
  return text
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/\n/g, '<br>')
    .replace(/- (.+)/g, '<span style="display:block;margin-left:8px">• $1</span>')
}

const saveHistory = () => {
  try {
    localStorage.setItem('ai_chat_history', JSON.stringify(messages.value))
  } catch {}
}

const loadHistory = () => {
  try {
    const saved = localStorage.getItem('ai_chat_history')
    if (saved) {
      messages.value = JSON.parse(saved).filter(m => !m.loading)
    }
  } catch {}
}

onMounted(() => {
  loadHistory()
})
</script>

<style scoped>
.ai-chat-wrapper {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
}

/* 悬浮按钮 */
.float-btn {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #2563EB, #1d4ed8);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
  color: #fff;
  transition: all 0.2s;
}

.float-btn:hover {
  transform: scale(1.08);
  box-shadow: 0 6px 24px rgba(37, 99, 235, 0.5);
}

.btn-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* 聊天窗口 */
.chat-window {
  width: 400px;
  height: 560px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

/* 头部 */
.chat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: linear-gradient(135deg, #1e3a5f, #2563EB);
  color: #fff;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ai-avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
}

.header-info {
  display: flex;
  flex-direction: column;
}

.ai-name {
  font-size: 14px;
  font-weight: 600;
}

.ai-status {
  font-size: 11px;
  color: #a8f0a8;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.action-icon {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  cursor: pointer;
  color: rgba(255,255,255,0.8);
  transition: all 0.15s;
}

.action-icon:hover {
  background: rgba(255,255,255,0.15);
  color: #fff;
}

/* 消息区域 */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f5f7fa;
}

.empty-state {
  text-align: center;
  padding: 32px 20px;
  color: #606266;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.empty-state p {
  font-size: 14px;
  margin-bottom: 8px;
}

.empty-hint {
  font-size: 12px;
  color: #909399;
  margin-top: 16px;
}

.empty-list {
  list-style: none;
  text-align: left;
  display: inline-block;
  font-size: 12px;
}

.empty-list li {
  margin-bottom: 6px;
  color: #606266;
}

/* 消息 */
.message {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
  align-items: flex-start;
}

.message.ai {
  flex-direction: row;
}

.message.user {
  flex-direction: row-reverse;
}

.msg-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
}

.msg-avatar.ai {
  background: #2563EB;
}

.msg-avatar.user {
  background: #e6f7ff;
  color: #2563EB;
}

.msg-content {
  max-width: 75%;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.user-bubble {
  background: #2563EB;
  color: #fff;
  padding: 10px 14px;
  border-radius: 12px 12px 4px 12px;
  font-size: 13px;
  line-height: 1.6;
  word-break: break-all;
}

.ai-bubble {
  background: #fff;
  color: #303133;
  padding: 10px 14px;
  border-radius: 4px 12px 12px 12px;
  font-size: 13px;
  line-height: 1.7;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  word-break: break-all;
}

.ai-bubble :deep(strong) {
  color: #2563EB;
}

.ai-actions {
  display: flex;
  gap: 8px;
  padding-left: 4px;
}

.copy-btn {
  font-size: 11px;
  color: #909399;
  cursor: pointer;
}

.copy-btn:hover {
  color: #2563EB;
}

/* 打字动画 */
.typing-dots {
  display: inline-flex;
  gap: 4px;
  align-items: center;
  padding: 4px 0;
}

.typing-dots span {
  width: 6px;
  height: 6px;
  background: #909399;
  border-radius: 50%;
  animation: typing 1.2s infinite;
}

.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
  0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
  30% { transform: translateY(-6px); opacity: 1; }
}

/* 快捷问题 */
.quick-questions {
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #f0f0f0;
}

.quick-label {
  font-size: 11px;
  color: #909399;
  margin-bottom: 8px;
  display: block;
}

.quick-btns {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.quick-btns .el-button {
  font-size: 12px;
  padding: 4px 10px;
}

/* 输入区域 */
.chat-input {
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #f0f0f0;
}

.input-row {
  display: flex;
  gap: 8px;
  align-items: flex-end;
}

.input-row .el-textarea {
  flex: 1;
}

.input-row .el-textarea :deep(.el-textarea__inner) {
  border-radius: 10px;
  font-size: 13px;
  line-height: 1.5;
  padding: 8px 12px;
  border-color: #dcdfe6;
  resize: none;
}

.input-row .el-textarea :deep(.el-textarea__inner:focus) {
  border-color: #2563EB;
}

.send-btn {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
}

.input-hint {
  font-size: 10px;
  color: #c0c4cc;
  margin-top: 4px;
  text-align: right;
}

/* 过渡动画 */
.float-btn-enter-active,
.float-btn-leave-active {
  transition: all 0.3s ease;
}

.float-btn-enter-from,
.float-btn-leave-to {
  opacity: 0;
  transform: scale(0.5);
}

.chat-slide-enter-active,
.chat-slide-leave-active {
  transition: all 0.3s ease;
}

.chat-slide-enter-from,
.chat-slide-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>

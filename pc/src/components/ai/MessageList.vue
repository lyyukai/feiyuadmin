<template>
  <div class="message-list" ref="messageListRef">
    <template v-if="store.messages.length > 0">
      <div
        v-for="(message, index) in store.messages"
        :key="message.id"
        class="message-wrapper"
        :class="message.role"
      >
        <!-- 用户头像 -->
        <div v-if="message.role === 'user'" class="avatar user-avatar">
          <el-icon><User /></el-icon>
        </div>

        <!-- AI头像 -->
        <div v-else class="avatar ai-avatar">
          <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="var(--fe-primary)"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="var(--fe-primary)"/>
          </svg>
        </div>

        <div class="message-content">
          <!-- 消息气泡 -->
          <div class="message-bubble" :class="message.role">
            <!-- 加载状态 -->
            <div v-if="message.loading" class="loading-dots">
              <span></span><span></span><span></span>
            </div>

            <!-- 普通文本消息 -->
            <template v-else-if="message.type === 'text' || !message.type">
              <div class="text-content" v-html="formatContent(message.content)"></div>
            </template>

            <!-- SQL结果消息 -->
            <template v-else-if="message.type === 'sql'">
              <div class="sql-result">
                <div v-if="message.explanation" class="sql-explanation">
                  {{ message.explanation }}
                </div>
                <pre class="sql-code"><code>{{ message.sql }}</code></pre>
                <div class="sql-actions">
                  <el-button size="small" @click="handleCopySql(message.sql)">
                    <el-icon><DocumentCopy /></el-icon>复制
                  </el-button>
                  <el-button
                    v-if="message.executionResult === undefined"
                    size="small"
                    type="primary"
                    :loading="message.loading"
                    @click="handleExecuteSql(message)"
                  >
                    执行
                  </el-button>
                </div>
                <div v-if="message.executionResult" class="execution-result">
                  <div class="result-tip">执行结果：{{ message.executionResult }}</div>
                </div>
              </div>
            </template>

            <!-- 错误消息 -->
            <template v-else-if="message.type === 'error'">
              <div class="error-content">
                <el-icon><CircleClose /></el-icon>
                <span>{{ message.content }}</span>
              </div>
            </template>
          </div>

          <!-- 时间戳 -->
          <div class="message-time">{{ formatTime(message.createdAt) }}</div>
        </div>
      </div>
    </template>

    <!-- 空状态 -->
    <div v-else class="empty-state">
      <div class="empty-icon">🤖</div>
      <h3>你好，我是飞鱼 AI 助手</h3>
      <p>我可以帮你完成以下任务：</p>
      <ul class="empty-list">
        <li>📝 解答系统使用问题</li>
        <li>💻 提供开发建议和代码示例</li>
        <li>🔧 辅助 NL2SQL 自然语言查询</li>
        <li>📖 解读系统功能和工作流</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'
import { useAiStore } from '@/store/ai'
import { ElMessage } from 'element-plus'
import { User, DocumentCopy, CircleClose } from '@element-plus/icons-vue'

const store = useAiStore()
const messageListRef = ref(null)

async function scrollToBottom() {
  await nextTick()
  if (messageListRef.value) {
    messageListRef.value.scrollTop = messageListRef.value.scrollHeight
  }
}

watch(
  () => store.messages.length,
  () => scrollToBottom(),
  { immediate: true }
)

function formatContent(content) {
  if (!content) return ''
  // 简单的markdown-like格式化
  return content
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\n/g, '<br/>')
}

function formatTime(isoString) {
  if (!isoString) return ''
  return new Date(isoString).toLocaleTimeString('zh-CN', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function handleCopySql(sql) {
  try {
    await navigator.clipboard.writeText(sql)
    ElMessage.success('SQL已复制')
  } catch {
    ElMessage.error('复制失败')
  }
}

function handleExecuteSql(message) {
  // Mock执行
  message.loading = true
  setTimeout(() => {
    message.executionResult = '查询成功，返回 12 条记录'
    message.loading = false
  }, 1000)
}
</script>

<style scoped>
.message-list {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
  background: var(--fe-bg-page);
}

.message-wrapper {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  align-items: flex-start;
}

.message-wrapper.user {
  flex-direction: row-reverse;
}

/* Avatars */
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 18px;
}

.user-avatar {
  background: var(--fe-primary-light);
  color: var(--fe-primary);
}

.ai-avatar {
  background: var(--fe-primary);
  color: #fff;
}

/* Content */
.message-content {
  max-width: 70%;
  min-width: 0;
}

@media screen and (max-width: 767px) {
  .message-content {
    max-width: 85%;
  }
}

.message-bubble {
  padding: 12px 16px;
  border-radius: var(--fe-radius-lg);
  font-size: var(--fe-font-size-sm);
  line-height: var(--fe-line-height-base);
  word-break: break-word;
}

.message-bubble.user {
  background: var(--fe-primary);
  color: #fff;
  border-bottom-right-radius: 4px;
}

.message-bubble.ai {
  background: var(--fe-bg-card);
  color: var(--fe-text-primary);
  border-bottom-left-radius: 4px;
  box-shadow: var(--fe-shadow-sm);
}

.message-time {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
  margin-top: 4px;
  padding: 0 4px;
}

.message-wrapper.user .message-time {
  text-align: right;
}

/* Loading dots */
.loading-dots {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 4px 0;
}

.loading-dots span {
  width: 6px;
  height: 6px;
  background: var(--fe-text-secondary);
  border-radius: 50%;
  animation: dot-bounce 1.4s infinite ease-in-out both;
}

.loading-dots span:nth-child(1) { animation-delay: -0.32s; }
.loading-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes dot-bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

/* SQL result */
.sql-result {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sql-explanation {
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-regular);
  margin-bottom: 4px;
}

.sql-code {
  background: #1e1e1e;
  color: #d4d4d4;
  padding: 12px;
  border-radius: var(--fe-radius-md);
  overflow-x: auto;
  font-size: var(--fe-font-size-xs);
  font-family: 'Courier New', monospace;
  margin: 0;
}

.sql-actions {
  display: flex;
  gap: 8px;
}

/* Error */
.error-content {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--fe-danger);
}

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
  color: var(--fe-text-secondary);
  padding: 40px 20px;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.empty-state h3 {
  margin: 0 0 8px;
  font-size: var(--fe-font-size-xl);
  color: var(--fe-text-primary);
}

.empty-state p {
  margin: 0 0 12px;
  font-size: var(--fe-font-size-sm);
}

.empty-list {
  list-style: none;
  padding: 0;
  margin: 0;
  text-align: left;
}

.empty-list li {
  font-size: var(--fe-font-size-sm);
  margin-bottom: 6px;
  color: var(--fe-text-regular);
}
</style>

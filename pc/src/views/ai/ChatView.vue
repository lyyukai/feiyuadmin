<template>
  <div class="ai-chat-view">
    <!-- PC端：左侧会话面板 + 右侧聊天 -->
    <div class="chat-layout">
      <!-- 侧边栏（PC端显示，移动端隐藏） -->
      <div class="chat-sidebar">
        <ChatPanel />
      </div>

      <!-- 主聊天区域 -->
      <div class="chat-main">
        <!-- 消息列表 -->
        <div class="chat-messages">
          <MessageList />
        </div>

        <!-- 输入区域 -->
        <div class="chat-input-area">
          <div class="input-wrapper">
            <el-input
              v-model="inputMessage"
              type="textarea"
              :rows="inputRows"
              placeholder="输入你的问题... (Ctrl+Enter 发送)"
              resize="none"
              @keydown.enter.ctrl="handleSend"
              @input="handleInputChange"
              @focus="inputRows = isMobileDevice ? 2 : 3"
              @blur="inputRows = isMobileDevice ? 2 : 3"
            />
            <div class="input-actions">
              <div class="input-left">
                <el-select
                  v-model="selectedModel"
                  placeholder="选择模型"
                  size="small"
                  style="width: 140px"
                  class="fe-hide-mobile"
                >
                  <el-option
                    v-for="model in store.aiModels"
                    :key="model.id"
                    :label="model.name"
                    :value="model.id"
                  />
                </el-select>
                <el-tooltip content="使用Prompt模板">
                  <el-button :icon="Document" circle @click="showPromptSelector = !showPromptSelector" />
                </el-tooltip>
              </div>
              <div class="input-right">
                <el-button
                  type="primary"
                  :loading="store.sendingMessage"
                  :disabled="!inputMessage.trim()"
                  @click="handleSend"
                >
                  <el-icon><Promotion /></el-icon>
                  <span class="fe-hide-mobile">发送</span>
                </el-button>
              </div>
            </div>

            <!-- Prompt选择器 -->
            <div v-if="showPromptSelector" class="prompt-selector">
              <div class="selector-header">
                <span>选择Prompt模板</span>
                <el-button link @click="showPromptSelector = false">
                  <el-icon><Close /></el-icon>
                </el-button>
              </div>
              <div class="selector-list">
                <div
                  v-for="prompt in store.activePrompts"
                  :key="prompt.id"
                  class="selector-item"
                  :class="{ active: selectedPrompt?.id === prompt.id }"
                  @click="selectPrompt(prompt)"
                >
                  <div class="item-name">{{ prompt.name }}</div>
                  <div class="item-desc">{{ prompt.description || '无描述' }}</div>
                </div>
                <el-empty v-if="store.activePrompts.length === 0" description="暂无可用模板" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 移动端：底部快捷按钮（FAB） -->
    <div class="mobile-fab fe-hide-desktop" @click="$router.push('/ai/chat')">
      <el-icon><ChatDotRound /></el-icon>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAiStore } from '@/store/ai'
import ChatPanel from '@/components/ai/ChatPanel.vue'
import MessageList from '@/components/ai/MessageList.vue'
import { ElMessage } from 'element-plus'
import { Document, Promotion, Close, ChatDotRound } from '@element-plus/icons-vue'
import { isMobile } from '@/utils/responsive'

const store = useAiStore()

const inputMessage = ref('')
const selectedModel = ref('')
const selectedPrompt = ref(null)
const showPromptSelector = ref(false)
const inputRows = ref(isMobile() ? 2 : 3)
const isMobileDevice = isMobile()

onMounted(async () => {
  try {
    await store.fetchAiModels()
    if (store.aiModels.length > 0) {
      selectedModel.value = store.aiModels[0].id
    }
  } catch (error) {
    console.error('加载AI模型失败', error)
  }

  if (store.currentSessionId) {
    await store.fetchMessages(store.currentSessionId)
  }
})

function handleInputChange(value) {
  inputMessage.value = value
}

async function handleSend() {
  if (!inputMessage.value.trim()) {
    ElMessage.warning('请输入消息内容')
    return
  }

  if (store.sendingMessage) return

  try {
    await store.sendMessage(inputMessage.value, store.currentSessionId)
    inputMessage.value = ''
  } catch (error) {
    ElMessage.error('发送消息失败')
  }
}

function selectPrompt(prompt) {
  selectedPrompt.value = prompt
  showPromptSelector.value = false
  if (prompt.userPromptTemplate) {
    inputMessage.value = prompt.userPromptTemplate
  }
}
</script>

<style scoped>
.ai-chat-view {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-layout {
  display: flex;
  height: 100%;
  overflow: hidden;
}

/* Sidebar */
.chat-sidebar {
  width: var(--fe-sidebar-width);
  flex-shrink: 0;
  height: 100%;
  border-right: 1px solid var(--fe-border);
  overflow: hidden;
}

/* Main */
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  min-width: 0;
}

.chat-messages {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

/* Input area */
.chat-input-area {
  padding: 12px 16px;
  background: var(--fe-bg-card);
  border-top: 1px solid var(--fe-border);
  flex-shrink: 0;
}

.input-wrapper {
  position: relative;
}

.input-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  gap: 8px;
}

.input-left {
  display: flex;
  align-items: center;
  gap: 8px;
}

.input-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.prompt-selector {
  position: absolute;
  bottom: calc(100% + 8px);
  left: 0;
  right: 0;
  background: var(--fe-bg-card);
  border: 1px solid var(--fe-border);
  border-radius: var(--fe-radius-lg);
  box-shadow: var(--fe-shadow-lg);
  max-height: 300px;
  overflow: hidden;
  z-index: var(--fe-z-dropdown);
}

.selector-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid var(--fe-border-lighter);
  font-weight: 600;
  color: var(--fe-text-primary);
  font-size: var(--fe-font-size-sm);
}

.selector-list {
  max-height: 240px;
  overflow-y: auto;
  padding: 8px;
}

.selector-item {
  padding: 10px 12px;
  border-radius: var(--fe-radius-md);
  cursor: pointer;
  transition: background var(--fe-transition-fast);
}

.selector-item:hover {
  background: var(--fe-bg-hover);
}

.selector-item.active {
  background: var(--fe-primary-light);
}

.item-name {
  font-size: var(--fe-font-size-sm);
  font-weight: 500;
  color: var(--fe-text-primary);
}

.item-desc {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
  margin-top: 4px;
}

/* Mobile FAB */
.mobile-fab {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--fe-primary);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  box-shadow: var(--fe-shadow-lg);
  cursor: pointer;
  z-index: 50;
}

/* ---- Responsive ---- */
@media screen and (max-width: 767px) {
  .chat-layout {
    flex-direction: column;
  }

  .chat-sidebar {
    display: none;
  }

  .chat-input-area {
    padding: 10px 12px;
  }
}

@media screen and (min-width: 768px) and (max-width: 1023px) {
  .chat-sidebar {
    width: 200px;
  }
}
</style>

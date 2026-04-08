<template>
  <div class="chat-panel">
    <!-- 顶部搜索和新会话按钮 -->
    <div class="panel-header">
      <el-input
        v-model="searchKeyword"
        placeholder="搜索会话..."
        prefix-icon="Search"
        clearable
        class="search-input"
      />
      <el-button type="primary" :icon="Plus" circle @click="handleNewChat" />
    </div>

    <!-- 会话列表 -->
    <div class="sessions-container">
      <div class="sessions-list" v-loading="store.loading">
        <template v-if="filteredSessions.length > 0">
          <div
            v-for="session in filteredSessions"
            :key="session.id"
            class="session-item"
            :class="{ active: session.id === store.currentSessionId }"
            @click="handleSelectSession(session)"
          >
            <div class="session-icon">
              <el-icon><ChatDotRound /></el-icon>
            </div>
            <div class="session-info">
              <div class="session-name">{{ session.name || '新对话' }}</div>
              <div class="session-time">{{ formatTime(session.updatedAt) }}</div>
            </div>
            <el-dropdown trigger="click" @command="(cmd) => handleCommand(cmd, session)">
              <el-icon class="session-more"><MoreFilled /></el-icon>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item command="rename">
                    <el-icon><Edit /></el-icon>
                    <span>重命名</span>
                  </el-dropdown-item>
                  <el-dropdown-item command="duplicate">
                    <el-icon><CopyDocument /></el-icon>
                    <span>复制</span>
                  </el-dropdown-item>
                  <el-dropdown-item command="delete" divided>
                    <el-icon><Delete /></el-icon>
                    <span>删除</span>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </div>
        </template>
        <el-empty v-else-if="!store.loading" description="暂无会话记录">
          <el-button type="primary" @click="handleNewChat">开始新对话</el-button>
        </el-empty>
      </div>
    </div>

    <!-- 底部快捷操作 -->
    <div class="panel-footer">
      <el-button link @click="$router.push('/ai/prompts')">
        <el-icon><Document /></el-icon>
        <span>Prompt管理</span>
      </el-button>
      <el-button link @click="$router.push('/ai/nl2sql')">
        <el-icon><DataAnalysis /></el-icon>
        <span>NL2SQL</span>
      </el-button>
    </div>

    <!-- 重命名对话框 -->
    <el-dialog v-model="renameDialogVisible" title="重命名会话" width="400px">
      <el-input v-model="renameName" placeholder="请输入会话名称" />
      <template #footer>
        <el-button @click="renameDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleRename">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAiStore } from '@/store/ai'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Plus,
  ChatDotRound,
  MoreFilled,
  Edit,
  Delete,
  CopyDocument,
  Document,
  DataAnalysis,
  Search,
} from '@element-plus/icons-vue'

const router = useRouter()
const store = useAiStore()

const searchKeyword = ref('')
const renameDialogVisible = ref(false)
const renameName = ref('')
const currentRenameSession = ref(null)

const filteredSessions = computed(() => {
  if (!searchKeyword.value) return store.sortedSessions
  const key = searchKeyword.value.toLowerCase()
  return store.sortedSessions.filter(s =>
    (s.name || '').toLowerCase().includes(key)
  )
})

async function handleNewChat() {
  await store.createSession()
  store.clearMessages()
}

async function handleSelectSession(session) {
  store.currentSessionId = session.id
  await store.fetchMessages(session.id)
}

async function handleCommand(command, session) {
  switch (command) {
    case 'rename':
      currentRenameSession.value = session
      renameName.value = session.name || ''
      renameDialogVisible.value = true
      break
    case 'duplicate':
      await store.createSession(`${session.name || '新对话'} (副本)`)
      ElMessage.success('会话已复制')
      break
    case 'delete':
      try {
        await ElMessageBox.confirm('确定删除该会话？', '提示', { type: 'warning' })
        await store.deleteSession(session.id)
        ElMessage.success('会话已删除')
      } catch {}
      break
  }
}

async function handleRename() {
  if (!renameName.value.trim()) {
    ElMessage.warning('会话名称不能为空')
    return
  }
  await store.renameSession(currentRenameSession.value.id, renameName.value)
  renameDialogVisible.value = false
  ElMessage.success('重命名成功')
}

function formatTime(isoString) {
  if (!isoString) return ''
  const date = new Date(isoString)
  const now = new Date()
  const diff = now - date
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}分钟前`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}小时前`
  return date.toLocaleDateString('zh-CN')
}

onMounted(async () => {
  await store.fetchSessions()
})
</script>

<style scoped>
.chat-panel {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--fe-bg-card);
}

.panel-header {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  border-bottom: 1px solid var(--fe-border-lighter);
  flex-shrink: 0;
}

.search-input {
  flex: 1;
}

.sessions-container {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.sessions-list {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
}

.session-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--fe-radius-md);
  cursor: pointer;
  transition: background var(--fe-transition-fast);
  margin-bottom: 2px;
}

.session-item:hover {
  background: var(--fe-bg-hover);
}

.session-item.active {
  background: var(--fe-primary-light);
}

.session-icon {
  width: 32px;
  height: 32px;
  border-radius: var(--fe-radius-md);
  background: var(--fe-primary-light);
  color: var(--fe-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.session-info {
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.session-name {
  font-size: var(--fe-font-size-sm);
  font-weight: 500;
  color: var(--fe-text-primary);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.session-time {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
  margin-top: 2px;
}

.session-more {
  font-size: 14px;
  color: var(--fe-text-secondary);
  cursor: pointer;
  padding: 4px;
  border-radius: var(--fe-radius-sm);
  transition: background var(--fe-transition-fast);
}

.session-more:hover {
  background: var(--fe-bg-hover);
  color: var(--fe-text-primary);
}

.panel-footer {
  display: flex;
  justify-content: space-around;
  padding: 12px;
  border-top: 1px solid var(--fe-border-lighter);
  flex-shrink: 0;
}

.panel-footer .el-button {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
}
</style>

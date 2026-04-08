<template>
  <div class="prompt-manager">
    <!-- 顶部操作栏 -->
    <div class="manager-header">
      <div class="header-left">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索Prompt"
          prefix-icon="Search"
          clearable
          class="search-input"
          style="width: 240px"
        />
        <el-select v-model="filterStatus" placeholder="状态筛选" clearable style="width: 120px" class="fe-hide-mobile">
          <el-option label="启用" :value="1" />
          <el-option label="禁用" :value="0" />
        </el-select>
      </div>
      <div class="header-right">
        <el-button type="primary" :icon="Plus" @click="handleCreate">
          新建Prompt
        </el-button>
      </div>
    </div>

    <!-- Prompt列表 -->
    <div class="prompt-list" v-loading="store.promptsLoading">
      <template v-if="filteredPrompts.length > 0">
        <PromptList
          :prompts="filteredPrompts"
          @edit="handleEdit"
          @delete="handleDelete"
          @toggle="handleToggle"
          @duplicate="handleDuplicate"
        />
      </template>
      <el-empty v-else description="暂无Prompt">
        <el-button type="primary" @click="handleCreate">创建第一个Prompt</el-button>
      </el-empty>
    </div>

    <!-- 编辑/创建对话框 -->
    <PromptEdit
      v-model="editDialogVisible"
      :prompt="currentPrompt"
      :mode="editMode"
      @success="handleEditSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAiStore } from '@/store/ai'
import PromptList from './PromptList.vue'
import PromptEdit from './PromptEdit.vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search } from '@element-plus/icons-vue'

const store = useAiStore()

const searchKeyword = ref('')
const filterStatus = ref(null)
const editDialogVisible = ref(false)
const currentPrompt = ref(null)
const editMode = ref('create')

const filteredPrompts = computed(() => {
  let list = store.activePrompts
  if (searchKeyword.value) {
    const key = searchKeyword.value.toLowerCase()
    list = list.filter(p =>
      (p.name || '').toLowerCase().includes(key) ||
      (p.description || '').toLowerCase().includes(key)
    )
  }
  if (filterStatus.value !== null) {
    list = list.filter(p => p.status === filterStatus.value)
  }
  return list
})

function handleCreate() {
  editMode.value = 'create'
  currentPrompt.value = null
  editDialogVisible.value = true
}

function handleEdit(prompt) {
  editMode.value = 'edit'
  currentPrompt.value = { ...prompt }
  editDialogVisible.value = true
}

async function handleDelete(prompt) {
  try {
    await ElMessageBox.confirm('确定删除该Prompt？', '提示', { type: 'warning' })
    const idx = store.activePrompts.findIndex(p => p.id === prompt.id)
    if (idx !== -1) store.activePrompts.splice(idx, 1)
    ElMessage.success('删除成功')
  } catch {}
}

function handleToggle(prompt) {
  prompt.status = prompt.status === 1 ? 0 : 1
  ElMessage.success(`已${prompt.status === 1 ? '启用' : '禁用'}`)
}

function handleDuplicate(prompt) {
  const copy = {
    ...prompt,
    id: undefined,
    name: `${prompt.name} (副本)`,
  }
  store.activePrompts.unshift(copy)
  ElMessage.success('已复制')
}

function handleEditSuccess() {
  editDialogVisible.value = false
  ElMessage.success(editMode.value === 'create' ? '创建成功' : '更新成功')
}

onMounted(async () => {
  await store.fetchPrompts()
})
</script>

<style scoped>
.prompt-manager {
  padding: 16px 20px;
  height: 100%;
  overflow-y: auto;
  background: var(--fe-bg-page);
}

.manager-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 12px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.header-right {
  display: flex;
  gap: 8px;
}

.prompt-list {
  background: var(--fe-bg-card);
  border-radius: var(--fe-radius-lg);
  border: 1px solid var(--fe-border);
  overflow: hidden;
  min-height: 200px;
}

@media screen and (max-width: 767px) {
  .prompt-manager {
    padding: 12px;
  }

  .manager-header {
    flex-direction: column;
    align-items: stretch;
  }

  .header-left {
    flex-direction: column;
    align-items: stretch;
  }

  .search-input {
    width: 100% !important;
  }
}
</style>

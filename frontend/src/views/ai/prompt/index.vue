<template>
  <div class="prompt-container">
    <!-- 顶部标题栏 -->
    <div class="top-bar">
      <span class="page-title">Prompt 助手</span>
      <el-button type="primary" @click="openDialog(null)">新建模板</el-button>
    </div>

    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-input
        v-model="searchKeyword"
        placeholder="搜索名称/描述"
        style="width: 240px"
        clearable
        @clear="fetchList"
        @keyup.enter="fetchList"
      />
      <el-button type="primary" @click="fetchList">搜索</el-button>
    </div>

    <!-- 表格 -->
    <el-table :data="tableData" border stripe style="width: 100%; margin-top: 16px">
      <el-table-column prop="id" label="ID" width="80" />
      <el-table-column prop="name" label="名称" min-width="150" />
      <el-table-column prop="description" label="描述" min-width="200" show-overflow-tooltip />
      <el-table-column prop="type" label="类型" width="100">
        <template #default="{ row }">
          <el-tag :type="row.type === 'system' ? 'success' : 'warning'" size="small">
            {{ row.type === 'system' ? 'system' : 'user' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" width="100">
        <template #default="{ row }">
          <el-switch
            v-model="row.status"
            :active-value="1"
            :inactive-value="0"
            @change="handleStatusChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="create_time" label="创建时间" width="180" />
      <el-table-column label="操作" width="150" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link size="small" @click="openDialog(row)">编辑</el-button>
          <el-button type="danger" link size="small" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- 分页 -->
    <div class="pagination-wrap">
      <el-pagination
        v-model:current-page="page"
        v-model:page-size="pageSize"
        :total="total"
        :page-sizes="[10, 20, 50, 100]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="fetchList"
        @current-change="fetchList"
      />
    </div>

    <!-- 新建/编辑弹窗 -->
    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑模板' : '新建模板'" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80">
        <el-form-item label="名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入模板名称" />
        </el-form-item>
        <el-form-item label="类型" prop="type">
          <el-radio-group v-model="form.type">
            <el-radio value="system">system</el-radio>
            <el-radio value="user">user</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="描述" prop="description">
          <el-input v-model="form.description" type="textarea" :rows="3" placeholder="请输入描述" />
        </el-form-item>
        <el-form-item label="内容" prop="content">
          <el-input v-model="form.content" type="textarea" :rows="6" placeholder="请输入 Prompt 内容" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { getPromptList, addPrompt, editPrompt, deletePrompt } from '@/api/ai'

const searchKeyword = ref('')
const page = ref(1)
const pageSize = ref(10)
const total = ref(0)
const tableData = ref([])

const dialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref(null)

const form = reactive({
  id: null,
  name: '',
  type: 'user',
  description: '',
  content: '',
  status: 1
})

const rules = {
  name: [{ required: true, message: '请输入模板名称', trigger: 'blur' }],
  type: [{ required: true, message: '请选择类型', trigger: 'change' }],
  content: [{ required: true, message: '请输入 Prompt 内容', trigger: 'blur' }]
}

// Mock 数据兜底
const mockData = [
  { id: 1, name: 'SQL 生成助手', description: '将自然语言转换为标准 SQL 语句', type: 'system', status: 1, create_time: '2026-01-15 10:30:00' },
  { id: 2, name: '代码审查助手', description: '辅助审查代码质量与安全问题', type: 'system', status: 1, create_time: '2026-02-01 14:22:00' },
  { id: 3, name: '翻译助手', description: '多语言互译，支持中英日韩等', type: 'user', status: 1, create_time: '2026-02-20 09:15:00' },
  { id: 4, name: '周报生成器', description: '根据工作内容自动生成周报', type: 'user', status: 0, create_time: '2026-03-05 16:45:00' },
  { id: 5, name: '会议纪要整理', description: '整理会议内容，提炼关键决议', type: 'user', status: 1, create_time: '2026-03-18 11:00:00' }
]

const fetchList = async () => {
  try {
    const res = await getPromptList({
      page: page.value,
      page_size: pageSize.value,
      keyword: searchKeyword.value
    })
    if (res.code === 1 && res.data?.list) {
      tableData.value = res.data.list
      total.value = res.data.total || 0
    } else {
      // API 错误时用 mock 兜底
      tableData.value = mockData
      total.value = mockData.length
    }
  } catch {
    tableData.value = mockData
    total.value = mockData.length
  }
}

const openDialog = (row) => {
  if (row) {
    isEdit.value = true
    Object.assign(form, {
      id: row.id,
      name: row.name,
      type: row.type,
      description: row.description || '',
      content: row.content || '',
      status: row.status
    })
  } else {
    isEdit.value = false
    Object.assign(form, { id: null, name: '', type: 'user', description: '', content: '', status: 1 })
  }
  dialogVisible.value = true
}

const submitForm = async () => {
  if (!formRef.value) return
  await formRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      const api = isEdit.value ? editPrompt : addPrompt
      const res = await api(form)
      if (res.code === 1) {
        ElMessage.success(isEdit.value ? '编辑成功' : '新建成功')
        dialogVisible.value = false
        fetchList()
      } else {
        ElMessage.error(res.msg || '操作失败')
      }
    } catch {
      ElMessage.error('操作失败')
    }
  })
}

const handleStatusChange = async (row) => {
  try {
    const res = await editPrompt({ id: row.id, status: row.status })
    if (res.code !== 1) {
      ElMessage.error('切换状态失败')
      row.status = row.status === 1 ? 0 : 1
    }
  } catch {
    ElMessage.error('切换状态失败')
  }
}

const handleDelete = (row) => {
  ElMessageBox.confirm(`确定删除模板「${row.name}」吗？`, '提示', { type: 'warning' })
    .then(async () => {
      try {
        const res = await deletePrompt(row.id)
        if (res.code === 1) {
          ElMessage.success('删除成功')
          fetchList()
        } else {
          ElMessage.error(res.msg || '删除失败')
        }
      } catch {
        ElMessage.error('删除失败')
      }
    })
    .catch(() => {})
}

fetchList()
</script>

<style scoped>
.prompt-container {
  padding: 20px;
}
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.page-title {
  font-size: 18px;
  font-weight: 600;
}
.search-bar {
  display: flex;
  gap: 10px;
  align-items: center;
}
.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>

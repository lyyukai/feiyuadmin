<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增文章</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="标题 / 作者" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.status" placeholder="状态" style="width: 120px" clearable>
          <el-option label="已发布" value="已发布" />
          <el-option label="草稿" value="草稿" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="title" label="标题" min-width="200" show-overflow-tooltip />
        <el-table-column prop="category" label="分类" width="120" />
        <el-table-column prop="author" label="作者" width="120" />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="statusType[row.status] || 'info'" size="small">{{ row.status }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="publishTime" label="发布时间" width="180" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑文章' : '新增文章'" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="标题" prop="title">
          <el-input v-model="form.title" placeholder="请输入标题" />
        </el-form-item>
        <el-form-item label="分类" prop="category">
          <el-input v-model="form.category" placeholder="请输入分类" />
        </el-form-item>
        <el-form-item label="作者" prop="author">
          <el-input v-model="form.author" placeholder="请输入作者" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio value="已发布">已发布</el-radio>
            <el-radio value="草稿">草稿</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)

const statusType = { '已发布': 'success', '草稿': 'info' }

const searchForm = reactive({ keyword: '', status: '' })

const form = reactive({ id: null, title: '', category: '', author: '', status: '草稿' })
const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  category: [{ required: true, message: '请输入分类', trigger: 'blur' }],
  author: [{ required: true, message: '请输入作者', trigger: 'blur' }]
}

const mockData = [
  { id: 1, title: 'Vue3 新特性解析', category: '技术', author: '张三', status: '已发布', publishTime: '2026-03-28 10:00:00' },
  { id: 2, title: 'Element Plus 使用指南', category: '教程', author: '李四', status: '草稿', publishTime: '-' },
  { id: 3, title: 'Vite 性能优化实践', category: '技术', author: '王五', status: '已发布', publishTime: '2026-03-25 14:30:00' }
]

const loadData = () => {
  loading.value = true
  let list = [...mockData]
  if (searchForm.keyword) {
    const kw = searchForm.keyword.toLowerCase()
    list = list.filter(item =>
      (item.title && item.title.toLowerCase().includes(kw)) ||
      (item.author && item.author.toLowerCase().includes(kw))
    )
  }
  if (searchForm.status) {
    list = list.filter(item => item.status === searchForm.status)
  }
  tableData.value = list
  loading.value = false
}

const resetSearch = () => { searchForm.keyword = ''; searchForm.status = ''; loadData() }

const openForm = (row) => {
  if (row) Object.assign(form, { id: row.id, title: row.title, category: row.category, author: row.author, status: row.status })
  else Object.assign(form, { id: null, title: '', category: '', author: '', status: '草稿' })
  formVisible.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    if (form.id) {
      const idx = mockData.findIndex(d => d.id === form.id)
      if (idx !== -1) mockData[idx] = { ...mockData[idx], ...form }
      ElMessage.success('编辑成功')
    } else {
      form.id = mockData.length ? Math.max(...mockData.map(d => d.id)) + 1 : 1
      mockData.push({ ...form, publishTime: form.status === '已发布' ? new Date().toLocaleString() : '-' })
      ElMessage.success('新增成功')
    }
    formVisible.value = false
    loadData()
  } catch { ElMessage.error('操作失败') } finally { submitLoading.value = false }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除文章"${row.title}"吗？`, '提示', { type: 'warning' })
    const idx = mockData.findIndex(d => d.id === row.id)
    if (idx !== -1) mockData.splice(idx, 1)
    ElMessage.success('删除成功')
    loadData()
  } catch {}
}

onMounted(() => loadData())
</script>

<style scoped>
</style>

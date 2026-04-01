<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增分类</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="分类名称 / 别名" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="分类名称" min-width="150" />
        <el-table-column prop="alias" label="别名" min-width="120" />
        <el-table-column prop="sort" label="排序" width="100" align="center" />
        <el-table-column prop="articleCount" label="文章数量" width="120" align="center" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑分类' : '新增分类'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="分类名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="别名" prop="alias">
          <el-input v-model="form.alias" placeholder="请输入别名" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
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

const searchForm = reactive({ keyword: '' })

const form = reactive({ id: null, name: '', alias: '', sort: 0 })
const rules = {
  name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
  alias: [{ required: true, message: '请输入别名', trigger: 'blur' }]
}

const mockData = [
  { id: 1, name: '技术', alias: 'tech', sort: 1, articleCount: 25 },
  { id: 2, name: '教程', alias: 'tutorial', sort: 2, articleCount: 18 },
  { id: 3, name: '资讯', alias: 'news', sort: 3, articleCount: 12 }
]

const loadData = () => {
  loading.value = true
  let list = [...mockData]
  if (searchForm.keyword) {
    const kw = searchForm.keyword.toLowerCase()
    list = list.filter(item =>
      (item.name && item.name.toLowerCase().includes(kw)) ||
      (item.alias && item.alias.toLowerCase().includes(kw))
    )
  }
  tableData.value = list
  loading.value = false
}

const resetSearch = () => { searchForm.keyword = ''; loadData() }

const openForm = (row) => {
  if (row) Object.assign(form, { id: row.id, name: row.name, alias: row.alias, sort: row.sort })
  else Object.assign(form, { id: null, name: '', alias: '', sort: 0 })
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
      mockData.push({ ...form, articleCount: 0 })
      ElMessage.success('新增成功')
    }
    formVisible.value = false
    loadData()
  } catch { ElMessage.error('操作失败') } finally { submitLoading.value = false }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除分类"${row.name}"吗？`, '提示', { type: 'warning' })
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

<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增支付方式</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="支付方式名称" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="支付方式" min-width="120" />
        <el-table-column prop="appId" label="应用ID" min-width="150" show-overflow-tooltip />
        <el-table-column prop="sort" label="排序" width="100" align="center" />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑支付方式' : '新增支付方式'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="支付方式" prop="name">
          <el-input v-model="form.name" placeholder="如：微信支付、支付宝" />
        </el-form-item>
        <el-form-item label="应用ID" prop="appId">
          <el-input v-model="form.appId" placeholder="请输入应用ID" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :value="1">启用</el-radio>
            <el-radio :value="0">禁用</el-radio>
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
import { ref, reactive } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)

const searchForm = reactive({ keyword: '' })

const form = reactive({ id: null, name: '', appId: '', sort: 0, status: 1 })
const rules = {
  name: [{ required: true, message: '请输入支付方式', trigger: 'blur' }],
  appId: [{ required: true, message: '请输入应用ID', trigger: 'blur' }]
}

const mockData = ref([
  { id: 1, name: '微信支付', appId: 'wx123456', status: 1, sort: 1 },
  { id: 2, name: '支付宝', appId: 'ali20260330', status: 1, sort: 2 }
])

const loadData = () => {
  loading.value = true
  let list = [...mockData.value]
  if (searchForm.keyword) {
    const kw = searchForm.keyword.toLowerCase()
    list = list.filter(item => item.name && item.name.toLowerCase().includes(kw))
  }
  tableData.value = list
  loading.value = false
}

const resetSearch = () => { searchForm.keyword = ''; loadData() }

const openForm = (row) => {
  if (row) Object.assign(form, { id: row.id, name: row.name, appId: row.appId, sort: row.sort, status: row.status })
  else Object.assign(form, { id: null, name: '', appId: '', sort: 0, status: 1 })
  formVisible.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    if (form.id) {
      const idx = mockData.value.findIndex(d => d.id === form.id)
      if (idx !== -1) mockData.value[idx] = { ...mockData.value[idx], ...form }
      ElMessage.success('编辑成功')
    } else {
      form.id = mockData.value.length ? Math.max(...mockData.value.map(d => d.id)) + 1 : 1
      mockData.value.push({ ...form })
      ElMessage.success('新增成功')
    }
    formVisible.value = false
    loadData()
  } catch { ElMessage.error('操作失败') } finally { submitLoading.value = false }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除支付方式"${row.name}"吗？`, '提示', { type: 'warning' })
    const idx = mockData.value.findIndex(d => d.id === row.id)
    if (idx !== -1) mockData.value.splice(idx, 1)
    ElMessage.success('删除成功')
    loadData()
  } catch {}
}

const handleStatusChange = (row) => {
  ElMessage.success(`已${row.status === 1 ? '启用' : '禁用'}"${row.name}"`)
}

loadData()
</script>

<style scoped>
</style>

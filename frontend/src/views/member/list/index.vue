<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="昵称 / 手机号" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.status" placeholder="状态" style="width: 120px" clearable>
          <el-option label="正常" :value="1" />
          <el-option label="禁用" :value="0" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="nickname" label="昵称" min-width="120" show-overflow-tooltip />
        <el-table-column prop="phone" label="手机号" min-width="130" />
        <el-table-column prop="level" label="会员等级" width="120" align="center">
          <template #default="{ row }">
            <el-tag>{{ row.level }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="balance" label="余额" width="120" align="right">
          <template #default="{ row }">¥{{ row.balance }}</template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">{{ row.status === 1 ? '正常' : '禁用' }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="注册时间" width="180" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDisable(row)">{{ row.status === 1 ? '禁用' : '启用' }}</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 编辑弹窗 -->
    <el-dialog v-model="formVisible" title="编辑会员" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="昵称" prop="nickname">
          <el-input v-model="form.nickname" placeholder="请输入昵称" />
        </el-form-item>
        <el-form-item label="手机号" prop="phone">
          <el-input v-model="form.phone" placeholder="请输入手机号" />
        </el-form-item>
        <el-form-item label="会员等级" prop="level">
          <el-select v-model="form.level" placeholder="请选择等级" style="width: 100%">
            <el-option label="普通会员" value="普通会员" />
            <el-option label="白银会员" value="白银会员" />
            <el-option label="黄金会员" value="黄金会员" />
            <el-option label="钻石会员" value="钻石会员" />
          </el-select>
        </el-form-item>
        <el-form-item label="余额" prop="balance">
          <el-input-number v-model="form.balance" :min="0" :precision="2" style="width: 100%" />
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
import { Search, Refresh } from '@element-plus/icons-vue'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)

const searchForm = reactive({ keyword: '', status: '' })

const form = reactive({ id: null, nickname: '', phone: '', level: '普通会员', balance: 0 })
const rules = {
  nickname: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
  phone: [{ required: true, message: '请输入手机号', trigger: 'blur' }]
}

const mockData = ref([
  { id: 1, nickname: '张三', phone: '13800138001', level: '黄金会员', balance: 2999.00, status: 1, createTime: '2026-01-15 10:00:00' },
  { id: 2, nickname: '李四', phone: '13800138002', level: '白银会员', balance: 599.00, status: 1, createTime: '2026-02-20 14:30:00' },
  { id: 3, nickname: '王五', phone: '13800138003', level: '普通会员', balance: 0.00, status: 0, createTime: '2026-03-10 09:15:00' }
])

const loadData = () => {
  loading.value = true
  let list = [...mockData.value]
  if (searchForm.keyword) {
    const kw = searchForm.keyword.toLowerCase()
    list = list.filter(item =>
      (item.nickname && item.nickname.toLowerCase().includes(kw)) ||
      (item.phone && item.phone.includes(kw))
    )
  }
  if (searchForm.status !== '') {
    list = list.filter(item => item.status === searchForm.status)
  }
  tableData.value = list
  loading.value = false
}

const resetSearch = () => { searchForm.keyword = ''; searchForm.status = ''; loadData() }

const openForm = (row) => {
  Object.assign(form, { id: row.id, nickname: row.nickname, phone: row.phone, level: row.level, balance: row.balance })
  formVisible.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    const idx = mockData.value.findIndex(d => d.id === form.id)
    if (idx !== -1) mockData.value[idx] = { ...mockData.value[idx], ...form }
    ElMessage.success('编辑成功')
    formVisible.value = false
    loadData()
  } catch { ElMessage.error('操作失败') } finally { submitLoading.value = false }
}

const handleDisable = async (row) => {
  try {
    const action = row.status === 1 ? '禁用' : '启用'
    await ElMessageBox.confirm(`确定${action}会员"${row.nickname}"吗？`, '提示', { type: 'warning' })
    const idx = mockData.value.findIndex(d => d.id === row.id)
    if (idx !== -1) mockData.value[idx].status = row.status === 1 ? 0 : 1
    ElMessage.success(`${action}成功`)
    loadData()
  } catch {}
}

onMounted(() => loadData())
</script>

<style scoped>
</style>

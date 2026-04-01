<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增等级</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="等级名称" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="等级名称" min-width="120" />
        <el-table-column prop="icon" label="等级图标" width="120" align="center">
          <template #default="{ row }">
            <el-icon :size="24"><component :is="row.icon" /></el-icon>
          </template>
        </el-table-column>
        <el-table-column prop="discount" label="折扣" width="100" align="center">
          <template #default="{ row }">{{ row.discount }}折</template>
        </el-table-column>
        <el-table-column prop="minPoints" label="最低积分" width="120" align="center" />
        <el-table-column prop="sort" label="排序" width="100" align="center" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑等级' : '新增等级'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="等级名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入等级名称" />
        </el-form-item>
        <el-form-item label="等级图标">
          <el-input v-model="form.icon" placeholder="Element Plus 图标名称，如：User, Star, Gem" />
        </el-form-item>
        <el-form-item label="折扣" prop="discount">
          <el-input-number v-model="form.discount" :min="0" :max="10" :precision="1" />
          <span style="margin-left: 8px; color: #909399">折</span>
        </el-form-item>
        <el-form-item label="最低积分" prop="minPoints">
          <el-input-number v-model="form.minPoints" :min="0" />
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

const form = reactive({ id: null, name: '', icon: 'User', discount: 10, minPoints: 0, sort: 0 })
const rules = {
  name: [{ required: true, message: '请输入等级名称', trigger: 'blur' }],
  discount: [{ required: true, message: '请输入折扣', trigger: 'blur' }],
  minPoints: [{ required: true, message: '请输入最低积分', trigger: 'blur' }]
}

const mockData = ref([
  { id: 1, name: '普通会员', icon: 'User', discount: 10, minPoints: 0, sort: 1 },
  { id: 2, name: '白银会员', icon: 'Medal', discount: 9, minPoints: 1000, sort: 2 },
  { id: 3, name: '黄金会员', icon: 'Star', discount: 8, minPoints: 5000, sort: 3 },
  { id: 4, name: '钻石会员', icon: 'Gem', discount: 7, minPoints: 20000, sort: 4 }
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
  if (row) Object.assign(form, { id: row.id, name: row.name, icon: row.icon, discount: row.discount, minPoints: row.minPoints, sort: row.sort })
  else Object.assign(form, { id: null, name: '', icon: 'User', discount: 10, minPoints: 0, sort: 0 })
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
    await ElMessageBox.confirm(`确定删除等级"${row.name}"吗？`, '提示', { type: 'warning' })
    const idx = mockData.value.findIndex(d => d.id === row.id)
    if (idx !== -1) mockData.value.splice(idx, 1)
    ElMessage.success('删除成功')
    loadData()
  } catch {}
}

onMounted(() => loadData())
</script>

<style scoped>
</style>

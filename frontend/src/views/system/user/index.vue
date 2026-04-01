<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增
      </el-button>
      <el-dropdown trigger="click" @command="handleBatchCommand" :disabled="selectedRows.length === 0">
        <el-button>
          批量操作 <el-icon class="el-icon--right"><ArrowDown /></el-icon>
        </el-button>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item command="enable">批量启用</el-dropdown-item>
            <el-dropdown-item command="disable">批量禁用</el-dropdown-item>
            <el-dropdown-item command="delete" divided>批量删除</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
      <div class="search-actions">
        <div class="search-item">
          <span class="label">关键词</span>
          <el-input v-model="searchForm.keyword" placeholder="用户名/昵称" style="width: 160px" clearable @keyup.enter="loadData" />
        </div>
        <div class="search-item">
          <span class="label">状态</span>
          <el-select v-model="searchForm.status" placeholder="请选择" style="width: 120px" clearable>
            <el-option label="正常" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </div>
        <el-button type="primary" @click="loadData">
          <el-icon><Search /></el-icon> 搜索
        </el-button>
        <el-button @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">用户列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading" @selection-change="handleSelectionChange">
        <el-table-column type="selection" width="50" align="center" />
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="username" label="用户名" min-width="120" />
        <el-table-column prop="nickname" label="昵称" min-width="120" />
        <el-table-column prop="email" label="邮箱" min-width="160" show-overflow-tooltip />
        <el-table-column prop="mobile" label="手机号" min-width="120" />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <span :class="['status-tag', row.status === 1 ? 'success' : 'danger']">
              {{ row.status === 1 ? '正常' : '禁用' }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="login_time" label="最后登录" min-width="160" />
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" class="action-btn" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="warning" class="action-btn" @click="handleResetPwd(row)">重置密码</el-button>
            <el-button link type="danger" class="action-btn" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-wrap">
        <div class="selection-info" v-if="selectedRows.length > 0">
          <span>已选择 <strong>{{ selectedRows.length }}</strong> 项</span>
          <el-button type="primary" link @click="selectedRows = []">清空</el-button>
        </div>
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          background
        />
      </div>
    </div>

    <!-- 弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="用户名" prop="username">
          <el-input v-model="form.username" :disabled="!!form.id" placeholder="请输入用户名" />
        </el-form-item>
        <el-form-item label="密码" :prop="form.id ? '' : 'password'">
          <el-input v-model="form.password" type="password" :placeholder="form.id ? '留空则不修改' : '请输入密码'" show-password />
        </el-form-item>
        <el-form-item label="昵称" prop="nickname">
          <el-input v-model="form.nickname" placeholder="请输入昵称" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="form.email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-form-item label="手机号" prop="mobile">
          <el-input v-model="form.mobile" placeholder="请输入手机号" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitLoading">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus, Edit, Delete, ArrowDown } from '@element-plus/icons-vue'
import { getUserList, addUser, editUser, deleteUser, resetUserPassword, batchUser } from '@/api'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)
const selectedRows = ref([])

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const searchForm = reactive({
  keyword: '',
  status: ''
})

const form = reactive({
  id: '',
  username: '',
  password: '',
  nickname: '',
  email: '',
  mobile: '',
  status: 1
})

const rules = {
  username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
  nickname: [{ required: true, message: '请输入昵称', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.pageSize,
      keyword: searchForm.keyword,
      status: searchForm.status
    }
    const res = await getUserList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchForm.keyword = ''
  searchForm.status = ''
  pagination.page = 1
  loadData()
}

const handleAdd = () => {
  dialogTitle.value = '新增用户'
  Object.assign(form, { id: '', username: '', password: '', nickname: '', email: '', mobile: '', status: 1 })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑用户'
  Object.assign(form, { ...row, password: '', status: row.status || 1 })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除用户"${row.username}"吗？`, '提示', { type: 'warning' })
    await deleteUser({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleSelectionChange = (selection) => {
  selectedRows.value = selection
}

const handleBatchCommand = async (command) => {
  const ids = selectedRows.value.map(row => row.id)
  const actionMap = { enable: 1, disable: 0, delete: 'delete' }
  const action = actionMap[command]

  if (command === 'delete') {
    try {
      await ElMessageBox.confirm(`确定删除选中的 ${ids.length} 个用户吗？`, '提示', { type: 'warning' })
      await batchUser(ids, action)
      ElMessage.success('批量删除成功')
      selectedRows.value = []
      loadData()
    } catch (e) {
      if (e !== 'cancel') ElMessage.error('批量删除失败')
    }
  } else {
    try {
      await batchUser(ids, action)
      ElMessage.success(`已批量${command === 'enable' ? '启用' : '禁用'}`)
      selectedRows.value = []
      loadData()
    } catch (e) {
      ElMessage.error('批量操作失败')
    }
  }
}

const handleResetPwd = async (row) => {
  try {
    await ElMessageBox.confirm(`确定重置用户"${row.username}"的密码吗？`, '提示', { type: 'warning' })
    await resetUserPassword(row.id)
    ElMessage.success('密码重置成功')
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('重置密码失败')
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    
    if (form.id) {
      await editUser(form)
      ElMessage.success('编辑成功')
    } else {
      await addUser(form)
      ElMessage.success('新增成功')
    }
    
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证失败或请求失败
  } finally {
    submitLoading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.card-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-top: 1px solid #f0f0f0;
}

.selection-info {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #606266;
  font-size: 13px;
}

.selection-info strong {
  color: #409eff;
}
</style>

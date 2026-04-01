<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增流程
      </el-button>
      <div class="search-actions">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索流程名称/编码"
          style="width: 200px; margin-right: 10px"
          clearable
          @keyup.enter="loadData"
        />
        <el-select v-model="searchStatus" placeholder="状态" style="width: 120px; margin-right: 10px" clearable>
          <el-option label="启用" :value="1" />
          <el-option label="禁用" :value="0" />
        </el-select>
        <el-button type="primary" @click="loadData">
          <el-icon><Search /></el-icon> 搜索
        </el-button>
        <el-button @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">流程列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="name" label="流程名称" min-width="150" />
        <el-table-column prop="code" label="流程编码" min-width="120">
          <template #default="{ row }">
            <code class="code-text">{{ row.code }}</code>
          </template>
        </el-table-column>
        <el-table-column prop="description" label="描述" min-width="150" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'" size="small">
              {{ row.status === 1 ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="is_published" label="发布状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.is_published === 1 ? 'success' : 'info'" size="small">
              {{ row.is_published === 1 ? '已发布' : '未发布' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="version" label="版本" width="70" align="center">
          <template #default="{ row }">
            v{{ row.version }}
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" min-width="160" />
        <el-table-column label="操作" width="300" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleDesign(row)">设计</el-button>
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="success" @click="handlePublish(row)" v-if="row.is_published !== 1">发布</el-button>
            <el-button link type="warning" @click="handleUnpublish(row)" v-else>取消发布</el-button>
            <el-button link type="info" @click="handleToggleStatus(row)">
              {{ row.status === 1 ? '禁用' : '启用' }}
            </el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          background
          @current-change="loadData"
          @size-change="loadData"
        />
      </div>
    </div>

    <!-- 流程弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="流程名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入流程名称" />
        </el-form-item>
        <el-form-item label="流程编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入流程编码，如: leave_approval" :disabled="!!form.id" />
          <div class="form-tip">只能是字母、数字、下划线，以字母开头</div>
        </el-form-item>
        <el-form-item label="描述" prop="description">
          <el-input v-model="form.description" type="textarea" :rows="3" placeholder="请输入流程描述" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">启用</el-radio>
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
import { Plus, Search } from '@element-plus/icons-vue'
import { useRouter } from 'vue-router'
import workflowApi from '@/api/workflow'

const router = useRouter()
const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)
const searchKeyword = ref('')
const searchStatus = ref('')

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const form = reactive({
  id: '',
  name: '',
  code: '',
  description: '',
  status: 1,
  flow_data: { nodes: [], edges: [] }
})

const rules = {
  name: [{ required: true, message: '请输入流程名称', trigger: 'blur' }],
  code: [
    { required: true, message: '请输入流程编码', trigger: 'blur' },
    { pattern: /^[a-z][a-z0-9_]*$/, message: '编码格式错误，只能包含小写字母、数字和下划线，且以字母开头', trigger: 'blur' }
  ]
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      page_size: pagination.pageSize
    }
    if (searchKeyword.value) params.keyword = searchKeyword.value
    if (searchStatus.value !== '') params.status = searchStatus.value

    const res = await workflowApi.lists(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchKeyword.value = ''
  searchStatus.value = ''
  pagination.page = 1
  loadData()
}

const handleAdd = () => {
  dialogTitle.value = '新增流程'
  Object.assign(form, {
    id: '',
    name: '',
    code: '',
    description: '',
    status: 1,
    flow_data: { nodes: [], edges: [] }
  })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑流程'
  Object.assign(form, {
    id: row.id,
    name: row.name,
    code: row.code,
    description: row.description,
    status: row.status,
    flow_data: row.flow_data || { nodes: [], edges: [] }
  })
  dialogVisible.value = true
}

const handleDesign = (row) => {
  router.push(`/workflow/design?id=${row.id}`)
}

const handlePublish = async (row) => {
  try {
    await ElMessageBox.confirm(`确定发布流程"${row.name}"吗？发布后将不能直接编辑。`, '提示', { type: 'warning' })
    await workflowApi.publish(row.id)
    ElMessage.success('发布成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('发布失败')
  }
}

const handleUnpublish = async (row) => {
  try {
    await ElMessageBox.confirm(`确定取消发布流程"${row.name}"吗？`, '提示', { type: 'warning' })
    await workflowApi.unpublish(row.id)
    ElMessage.success('取消发布成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('取消发布失败')
  }
}

const handleToggleStatus = async (row) => {
  const action = row.status === 1 ? '禁用' : '启用'
  try {
    await ElMessageBox.confirm(`确定${action}流程"${row.name}"吗？`, '提示', { type: 'warning' })
    await workflowApi.toggleStatus(row.id)
    ElMessage.success(`${action}成功`)
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(`${action}失败`)
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除流程"${row.name}"吗？此操作不可恢复！`, '提示', { type: 'warning' })
    await workflowApi.delete(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    if (form.id) {
      await workflowApi.edit(form)
      ElMessage.success('编辑成功')
    } else {
      await workflowApi.add(form)
      ElMessage.success('新增成功')
    }
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证失败
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

.search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.search-actions {
  display: flex;
  align-items: center;
}

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

.code-text {
  font-family: Consolas, Monaco, monospace;
  font-size: 12px;
  background: #f5f7fa;
  padding: 2px 6px;
  border-radius: 4px;
  color: #409eff;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
</style>

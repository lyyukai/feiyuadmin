<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增
      </el-button>
      <div class="search-actions">
        <div class="search-item">
          <span class="label">关键词</span>
          <el-input v-model="searchForm.keyword" placeholder="字典名称/类型" style="width: 160px" clearable @keyup.enter="loadData" />
        </div>
        <div class="search-item">
          <span class="label">状态</span>
          <el-select v-model="searchForm.status" placeholder="请选择" style="width: 120px" clearable>
            <el-option label="正常" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </div>
        <el-button type="primary" @click="loadData"><el-icon><Search /></el-icon> 搜索</el-button>
        <el-button @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">字典类型列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading" @selection-change="handleSelectionChange">
        <el-table-column type="selection" width="50" align="center" />
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="name" label="字典名称" min-width="150" show-overflow-tooltip />
        <el-table-column prop="type" label="字典类型" min-width="150" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-switch
              v-model="row.status"
              :active-value="1"
              :inactive-value="0"
              @change="handleStatusChange(row)"
            />
          </template>
        </el-table-column>
        <el-table-column prop="remark" label="备注" min-width="200" show-overflow-tooltip />
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="160" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
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
      <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
        <el-form-item label="字典名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入字典名称" />
        </el-form-item>
        <el-form-item label="字典类型" prop="type">
          <el-input v-model="form.type" placeholder="请输入字典类型" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入备注" />
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
import { Search, Plus } from '@element-plus/icons-vue'
import { getDictTypeLists, addDictType, editDictType, deleteDictType } from '@/api'

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
  name: '',
  type: '',
  status: 1,
  remark: ''
})

const rules = {
  name: [{ required: true, message: '请输入字典名称', trigger: 'blur' }],
  type: [{ required: true, message: '请输入字典类型', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getDictTypeLists({
      page: pagination.page,
      limit: pagination.pageSize,
      keyword: searchForm.keyword,
      status: searchForm.status
    })
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || res.total || 0
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
  dialogTitle.value = '新增字典类型'
  Object.assign(form, { id: '', name: '', type: '', status: 1, remark: '' })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑字典类型'
  Object.assign(form, { ...row })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除字典类型"${row.name}"吗？`, '提示', { type: 'warning' })
    await deleteDictType(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editDictType({ id: row.id, status: row.status })
    ElMessage.success('状态更新成功')
  } catch (e) {
    row.status = row.status === 1 ? 0 : 1
    ElMessage.error('状态更新失败')
  }
}

const handleSelectionChange = (selection) => {
  selectedRows.value = selection
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    if (form.id) {
      await editDictType(form)
      ElMessage.success('编辑成功')
    } else {
      await addDictType(form)
      ElMessage.success('新增成功')
    }
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证或请求失败
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

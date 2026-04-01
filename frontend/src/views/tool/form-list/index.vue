<template>
  <div class="page-container">
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">表单管理</span>
        <div class="header-actions">
          <el-button type="primary" @click="createForm" icon="Plus">创建表单</el-button>
        </div>
      </div>

      <!-- 搜索栏 -->
      <div class="search-bar">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索表单名称/编码"
          style="width: 250px"
          clearable
          @clear="handleSearch"
        >
          <template #prefix>
            <el-icon><Search /></el-icon>
          </template>
        </el-input>
        <el-button type="primary" @click="handleSearch" icon="Search">搜索</el-button>
      </div>

      <!-- 数据表格 -->
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="表单名称" min-width="150" />
        <el-table-column prop="code" label="表单编码" min-width="120" />
        <el-table-column prop="description" label="描述" min-width="150" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
              {{ row.status === 1 ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="280" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" link @click="editForm(row)">编辑</el-button>
            <el-button type="primary" size="small" link @click="viewData(row)">数据</el-button>
            <el-button type="primary" size="small" link @click="toggleStatus(row)">
              {{ row.status === 1 ? '禁用' : '启用' }}
            </el-button>
            <el-button type="danger" size="small" link @click="deleteForm(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
          v-model:current-page="currentPage"
          v-model:page-size="pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handlePageChange"
        />
      </div>
    </div>

    <!-- 数据查看对话框 -->
    <el-dialog v-model="dataDialogVisible" title="表单数据" width="900px" destroy-on-close>
      <div v-if="currentFormData.length === 0" class="empty-data">
        <el-empty description="暂无数据" />
      </div>
      <el-table v-else :data="currentFormData" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column label="提交数据" min-width="300">
          <template #default="{ row }">
            <pre class="data-preview">{{ formatData(row.data) }}</pre>
          </template>
        </el-table-column>
        <el-table-column prop="ip" label="提交IP" width="130" />
        <el-table-column prop="create_time" label="提交时间" width="180" />
        <el-table-column label="操作" width="100">
          <template #default="{ row }">
            <el-button type="danger" size="small" link @click="deleteFormData(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div v-if="dataTotal > pageSize" class="pagination" style="margin-top: 16px">
        <el-pagination
          v-model:current-page="dataPage"
          v-model:page-size="dataPageSize"
          :total="dataTotal"
          layout="prev, pager, next"
          @current-change="loadFormData"
        />
      </div>
      <template #footer>
        <el-button @click="dataDialogVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { getFormLists, toggleFormStatus, deleteForm, getFormDataList, deleteFormData } from '@/api/form'

const router = useRouter()

// 表格数据
const tableData = ref([])
const loading = ref(false)
const currentPage = ref(1)
const pageSize = ref(15)
const total = ref(0)
const searchKeyword = ref('')

// 数据查看
const dataDialogVisible = ref(false)
const currentFormId = ref(null)
const currentFormData = ref([])
const dataPage = ref(1)
const dataPageSize = ref(10)
const dataTotal = ref(0)

// 搜索
const handleSearch = () => {
  currentPage.value = 1
  loadTableData()
}

// 分页
const handleSizeChange = () => {
  currentPage.value = 1
  loadTableData()
}

const handlePageChange = () => {
  loadTableData()
}

// 加载表格数据
const loadTableData = async () => {
  loading.value = true
  try {
    const result = await getFormLists({
      page: currentPage.value,
      limit: pageSize.value,
      keyword: searchKeyword.value
    })
    tableData.value = result.data || []
    total.value = result.total || 0
  } catch (error) {
    console.error('加载失败:', error)
  } finally {
    loading.value = false
  }
}

// 创建表单
const createForm = () => {
  router.push('/tool/form-design')
}

// 编辑表单
const editForm = (row) => {
  router.push(`/tool/form-design?id=${row.id}`)
}

// 查看数据
const viewData = async (row) => {
  currentFormId.value = row.id
  dataPage.value = 1
  dataDialogVisible.value = true
  await loadFormData()
}

// 加载表单数据
const loadFormData = async () => {
  try {
    const result = await getFormDataList({
      form_id: currentFormId.value,
      page: dataPage.value,
      limit: dataPageSize.value
    })
    currentFormData.value = result.data || []
    dataTotal.value = result.total || 0
  } catch (error) {
    console.error('加载数据失败:', error)
  }
}

// 格式化数据
const formatData = (data) => {
  if (!data) return ''
  if (typeof data === 'string') {
    try {
      data = JSON.parse(data)
    } catch (e) {
      return data
    }
  }
  return JSON.stringify(data, null, 2)
}

// 切换状态
const toggleStatus = async (row) => {
  try {
    await toggleFormStatus({ id: row.id })
    ElMessage.success('操作成功')
    loadTableData()
  } catch (error) {
    ElMessage.error('操作失败')
  }
}

// 删除表单
const deleteFormHandler = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该表单吗？删除后无法恢复。', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    await deleteForm({ id: row.id })
    ElMessage.success('删除成功')
    loadTableData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

// 删除表单数据
const deleteFormDataHandler = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该条数据吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    await deleteFormData({ id: row.id })
    ElMessage.success('删除成功')
    loadFormData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

// 页面加载
onMounted(() => {
  loadTableData()
})
</script>

<style scoped>
.page-container {
  padding: 20px;
}

.page-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.search-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}

.empty-data {
  padding: 40px 0;
}

.data-preview {
  margin: 0;
  font-size: 12px;
  background: #f5f7fa;
  padding: 8px;
  border-radius: 4px;
  max-height: 200px;
  overflow-y: auto;
}
</style>

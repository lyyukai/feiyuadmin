<template>
  <div class="data-screen-list">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>数据大屏管理</span>
          <el-button type="primary" @click="handleCreate">创建大屏</el-button>
        </div>
      </template>

      <!-- 搜索栏 -->
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item label="关键词">
          <el-input v-model="searchForm.keyword" placeholder="搜索名称/编码" clearable @clear="handleSearch" />
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="searchForm.status" placeholder="全部" clearable @change="handleSearch">
            <el-option label="启用" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">搜索</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 数据表格 -->
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="大屏名称" min-width="150" />
        <el-table-column prop="code" label="编码" width="150" />
        <el-table-column prop="description" label="描述" min-width="200" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="280" fixed="right">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleDesign(row)">设计</el-button>
            <el-button link type="primary" @click="handlePreview(row)">预览</el-button>
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </el-card>

    <!-- 创建/编辑弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="大屏名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入大屏名称" />
        </el-form-item>
        <el-form-item label="大屏编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入大屏编码" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="描述" prop="description">
          <el-input v-model="form.description" type="textarea" :rows="3" placeholder="请输入描述" />
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
        <el-button type="primary" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useRouter } from 'vue-router'
import { screenLists, screenCreate, screenUpdate, screenDelete } from '@/api/tool/dataScreen'

const router = useRouter()

// 搜索表单
const searchForm = reactive({
  keyword: '',
  status: ''
})

// 分页
const pagination = reactive({
  page: 1,
  limit: 20,
  total: 0
})

// 表格数据
const tableData = ref([])
const loading = ref(false)

// 弹窗
const dialogVisible = ref(false)
const dialogTitle = ref('创建大屏')
const formRef = ref(null)
const form = reactive({
  id: null,
  name: '',
  code: '',
  description: '',
  status: 1
})

// 表单验证
const rules = {
  name: [{ required: true, message: '请输入大屏名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入大屏编码', trigger: 'blur' }]
}

// 加载数据
async function loadData() {
  loading.value = true
  try {
    const res = await screenLists({
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    })
    tableData.value = res.list || []
    pagination.total = res.total || 0
  } catch (error) {
    ElMessage.error('加载失败')
  } finally {
    loading.value = false
  }
}

// 搜索
function handleSearch() {
  pagination.page = 1
  loadData()
}

// 重置
function handleReset() {
  searchForm.keyword = ''
  searchForm.status = ''
  handleSearch()
}

// 创建
function handleCreate() {
  dialogTitle.value = '创建大屏'
  Object.assign(form, { id: null, name: '', code: '', description: '', status: 1 })
  dialogVisible.value = true
}

// 编辑
function handleEdit(row) {
  dialogTitle.value = '编辑大屏'
  Object.assign(form, {
    id: row.id,
    name: row.name,
    code: row.code,
    description: row.description || '',
    status: row.status
  })
  dialogVisible.value = true
}

// 提交
async function handleSubmit() {
  await formRef.value.validate()
  try {
    if (form.id) {
      await screenUpdate(form)
      ElMessage.success('更新成功')
    } else {
      await screenCreate(form)
      ElMessage.success('创建成功')
    }
    dialogVisible.value = false
    loadData()
  } catch (error) {
    ElMessage.error(error.message || '操作失败')
  }
}

// 设计
function handleDesign(row) {
  router.push(`/tool/data-screen/design?id=${row.id}`)
}

// 预览
function handlePreview(row) {
  window.open(`/admin/data-screen/preview/${row.id}`, '_blank')
}

// 删除
async function handleDelete(row) {
  try {
    await ElMessageBox.confirm('确定要删除该大屏吗？', '提示', { type: 'warning' })
    await screenDelete(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error.message || '删除失败')
    }
  }
}

// 初始化
loadData()
</script>

<style scoped>
.data-screen-list {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-form {
  margin-bottom: 20px;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}
</style>

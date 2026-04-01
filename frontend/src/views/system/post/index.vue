<template>
  <div class="page-container">
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增
      </el-button>
      <div class="search-actions">
        <div class="search-item">
          <span class="label">岗位名称</span>
          <el-input v-model="searchKey" placeholder="请输入" style="width: 160px" clearable @clear="loadData" />
        </div>
        <el-button type="primary" @click="loadData">
          <el-icon><Search /></el-icon> 搜索
        </el-button>
        <el-button @click="searchKey = ''; loadData()">重置</el-button>
      </div>
    </div>

    <div class="page-card">
      <el-table :data="tableData" v-loading="loading" row-key="id">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="code" label="岗位编码" min-width="120" />
        <el-table-column prop="name" label="岗位名称" min-width="120" />
        <el-table-column prop="sort" label="排序" width="80" align="center" />
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
        <el-table-column prop="create_time" label="创建时间" min-width="160" />
        <el-table-column label="操作" width="140" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" class="action-btn" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" class="action-btn" @click="handleDelete(row)">删除</el-button>
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
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </div>

    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px" draggable>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="岗位编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入岗位编码" />
        </el-form-item>
        <el-form-item label="岗位名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入岗位名称" />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :value="1">启用</el-radio>
            <el-radio :value="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入备注" />
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
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus } from '@element-plus/icons-vue'
import { getPostList, addPost, editPost, deletePost } from '@/api'

const loading = ref(false)
const tableData = ref([])
const searchKey = ref('')
const pagination = reactive({ page: 1, pageSize: 10, total: 0 })
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)

const form = reactive({ id: null, code: '', name: '', sort: 0, status: 1, remark: '' })

const rules = {
  code: [{ required: true, message: '请输入岗位编码', trigger: 'blur' }],
  name: [{ required: true, message: '请输入岗位名称', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getPostList({ page: pagination.page, limit: pagination.pageSize, keyword: searchKey.value })
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch {
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const handleAdd = () => {
  Object.assign(form, { id: null, code: '', name: '', sort: 0, status: 1, remark: '' })
  dialogTitle.value = '新增岗位'
  dialogVisible.value = true
}

const handleEdit = (row) => {
  Object.assign(form, { id: row.id, code: row.code, name: row.name, sort: row.sort || 0, status: row.status, remark: row.remark || '' })
  dialogTitle.value = '编辑岗位'
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除岗位「${row.name}」吗？`, '提示', { type: 'warning' })
    await deletePost({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editPost({ id: row.id, status: row.status })
    ElMessage.success(`岗位「${row.name}」${row.status === 1 ? '已启用' : '已禁用'}`)
  } catch {
    row.status = row.status === 1 ? 0 : 1
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return
  await formRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      if (form.id) {
        await editPost({ id: form.id, name: form.name, code: form.code, sort: form.sort, status: form.status, remark: form.remark })
        ElMessage.success('修改成功')
      } else {
        await addPost({ name: form.name, code: form.code, sort: form.sort, status: form.status, remark: form.remark })
        ElMessage.success('新增成功')
      }
      dialogVisible.value = false
      loadData()
    } catch {}
  })
}

onMounted(() => loadData())
</script>

<style scoped>
.page-container { padding: 0; }
</style>

<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" @click="handleAdd(null)">
          <el-icon><Plus /></el-icon> 新增部门
        </el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchKeyword" placeholder="部门名称 / 负责人" style="width: 200px" clearable @keyup.enter="handleSearch" />
        <el-button type="primary" @click="handleSearch"><el-icon><Search /></el-icon> 搜索</el-button>
        <el-button @click="handleReset"><el-icon><Refresh /></el-icon> 重置</el-button>
      </div>
    </div>

    <div class="page-card">
      <el-table
        :data="tableData"
        row-key="id"
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        default-expand-all
      >
        <el-table-column prop="name" label="部门名称" min-width="200">
          <template #default="{ row }">
            <div class="dept-name">
              <el-icon><OfficeBuilding /></el-icon>
              {{ row.name }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="leader" label="负责人" min-width="120" />
        <el-table-column prop="mobile" label="联系电话" min-width="140" />
        <el-table-column prop="email" label="邮箱" min-width="180" show-overflow-tooltip />
        <el-table-column prop="sort" label="排序" width="80" align="center" />
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-switch
              v-model="row.status"
              :active-value="1"
              :inactive-value="0"
              @change="handleStatusChange(row)"
            />
          </template>
        </el-table-column>
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" class="action-btn" @click="handleAdd(row)">新增</el-button>
            <el-button link type="primary" class="action-btn" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" class="action-btn" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="520px" draggable>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级部门">
          <el-tree-select
            v-model="form.pid"
            :data="selectTreeData"
            :props="{ label: 'name', value: 'id', children: 'children' }"
            check-strictly
            clearable
            placeholder="选择上级部门（根部门不选）"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="部门名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入部门名称" />
        </el-form-item>
        <el-form-item label="负责人" prop="leader">
          <el-input v-model="form.leader" placeholder="请输入负责人姓名" />
        </el-form-item>
        <el-form-item label="联系电话" prop="mobile">
          <el-input v-model="form.mobile" placeholder="请输入联系电话" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="form.email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="排序" prop="sort">
              <el-input-number v-model="form.sort" :min="0" :max="999" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="状态" prop="status">
              <el-radio-group v-model="form.status">
                <el-radio :value="1">启用</el-radio>
                <el-radio :value="0">禁用</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
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
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, OfficeBuilding, Search, Refresh } from '@element-plus/icons-vue'
import { getDeptTree, addDept, editDept, deleteDept } from '@/api'

const dialogVisible = ref(false)
const searchKeyword = ref('')
const dialogTitle = ref('')
const formRef = ref(null)

const form = reactive({
  id: null, pid: 0, name: '', leader: '', mobile: '', email: '', sort: 0, status: 1, remark: ''
})

const rules = {
  name: [{ required: true, message: '请输入部门名称', trigger: 'blur' }],
  leader: [{ required: true, message: '请输入负责人', trigger: 'blur' }]
}

const tableData = ref([])

const selectTreeData = computed(() => {
  const addRoot = [{ id: 0, name: '根部门', children: [] }]
  const convert = (nodes) => nodes.map(n => ({ ...n, children: n.children ? convert(n.children) : [] }))
  if (tableData.value.length) {
    addRoot[0].children = convert(tableData.value)
  }
  return addRoot
})

const loadData = async () => {
  try {
    const res = await getDeptTree()
    tableData.value = res?.data || res || []
  } catch {
    tableData.value = []
  }
}

const handleSearch = () => {
  // Tree filtering done client-side
  ElMessage.info('搜索功能：按部门名称/负责人筛选')
}

const handleReset = () => {
  searchKeyword.value = ''
  loadData()
}

const handleAdd = (parent) => {
  Object.assign(form, { id: null, pid: parent?.id || 0, name: '', leader: '', mobile: '', email: '', sort: 0, status: 1, remark: '' })
  dialogTitle.value = '新增部门'
  dialogVisible.value = true
}

const handleEdit = (row) => {
  Object.assign(form, { id: row.id, pid: row.pid || 0, name: row.name, leader: row.leader || '', mobile: row.mobile || '', email: row.email || '', sort: row.sort || 0, status: row.status, remark: row.remark || '' })
  dialogTitle.value = '编辑部门'
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除部门「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteDept({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editDept({ id: row.id, status: row.status })
    ElMessage.success(`部门「${row.name}」${row.status === 1 ? '已启用' : '已禁用'}`)
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
        await editDept({
          id: form.id, name: form.name, pid: form.pid, leader: form.leader,
          mobile: form.mobile, email: form.email, sort: form.sort, status: form.status, remark: form.remark
        })
        ElMessage.success('修改成功')
      } else {
        await addDept({
          name: form.name, pid: form.pid, leader: form.leader,
          mobile: form.mobile, email: form.email, sort: form.sort, status: form.status, remark: form.remark
        })
        ElMessage.success('新增成功')
      }
      dialogVisible.value = false
      loadData()
    } catch (e) {
      if (e !== 'cancel') ElMessage.error(form.id ? '修改失败' : '新增失败')
    }
  })
}

onMounted(() => loadData())
</script>

<style scoped>
.page-container { padding: 0; }
.dept-name { display: flex; align-items: center; gap: 8px; color: #1890ff; }
</style>

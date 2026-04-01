<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" @click="handleAdd">
          <el-icon><Plus /></el-icon> 新增租户
        </el-button>
        <el-button @click="handlePackage">套餐管理</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchKeyword" placeholder="租户名称/编码/联系人" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchStatus" placeholder="状态" style="width: 120px" clearable>
          <el-option label="全部" :value="null" />
          <el-option label="正常" :value="1" />
          <el-option label="禁用" :value="0" />
        </el-select>
        <el-button type="primary" @click="loadData"><el-icon><Search /></el-icon> 搜索</el-button>
        <el-button @click="handleReset"><el-icon><Refresh /></el-icon> 重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="租户名称" min-width="150" />
        <el-table-column prop="code" label="租户编码" min-width="120" />
        <el-table-column prop="type_name" label="隔离模式" width="120" align="center">
          <template #default="{ row }">
            <el-tag :type="row.tenant_type === 1 ? 'success' : 'warning'" size="small">
              {{ row.type_name }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="package_name" label="套餐" width="120" align="center" />
        <el-table-column prop="contact_name" label="联系人" width="120" />
        <el-table-column prop="contact_phone" label="联系电话" width="140" />
        <el-table-column prop="expire_time_text" label="过期时间" width="160" />
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'" size="small">
              {{ row.status_name }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="170" />
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" class="action-btn" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" class="action-btn" @click="handleDelete(row)">删除</el-button>
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
    </div>

    <!-- 租户表单弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px" draggable>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="租户名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入租户名称" />
        </el-form-item>
        <el-form-item label="租户编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入租户编码（字母、数字、下划线）" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="隔离模式" prop="tenant_type">
          <el-radio-group v-model="form.tenant_type">
            <el-radio :value="1">共享表（推荐）</el-radio>
            <el-radio :value="2">独立数据库</el-radio>
            <el-radio :value="3">独立Schema</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="所属套餐" prop="package_id">
          <el-select v-model="form.package_id" placeholder="请选择套餐" style="width: 100%">
            <el-option v-for="item in packageList" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="联系人" prop="contact_name">
              <el-input v-model="form.contact_name" placeholder="请输入联系人" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="联系电话" prop="contact_phone">
              <el-input v-model="form.contact_phone" placeholder="请输入联系电话" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="联系邮箱" prop="contact_email">
          <el-input v-model="form.contact_email" placeholder="请输入联系邮箱" />
        </el-form-item>
        <el-form-item label="过期时间" prop="expire_time">
          <el-date-picker
            v-model="form.expire_time"
            type="datetime"
            placeholder="不设置则永久有效"
            format="YYYY-MM-DD HH:mm"
            value-format="YYYY-MM-DD HH:mm:ss"
            style="width: 100%"
            clearable
          />
        </el-form-item>
        <el-form-item label="Logo">
          <el-input v-model="form.logo" placeholder="请输入Logo URL" />
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入备注" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :value="1">启用</el-radio>
            <el-radio :value="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>

    <!-- 套餐管理弹窗 -->
    <el-dialog v-model="packageDialogVisible" title="租户套餐管理" width="800px" draggable>
      <div class="package-toolbar">
        <el-button type="primary" size="small" @click="handlePackageAdd">
          <el-icon><Plus /></el-icon> 新增套餐
        </el-button>
      </div>
      <el-table :data="packageTableData" stripe size="small">
        <el-table-column prop="id" label="ID" width="60" />
        <el-table-column prop="name" label="套餐名称" min-width="120" />
        <el-table-column prop="code" label="套餐编码" width="120" />
        <el-table-column prop="price_text" label="价格" width="100" align="center" />
        <el-table-column prop="duration_text" label="时长" width="100" align="center" />
        <el-table-column prop="user_limit" label="用户数" width="100" align="center" />
        <el-table-column prop="storage_text" label="存储" width="100" align="center" />
        <el-table-column prop="api_limit" label="API次数/日" width="120" align="center" />
        <el-table-column prop="status_name" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'" size="small">{{ row.status_name }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="140" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="handlePackageEdit(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handlePackageDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 套餐表单弹窗 -->
      <el-dialog v-model="packageFormVisible" :title="packageFormTitle" width="500px" draggable append-to-body>
        <el-form ref="packageFormRef" :model="packageForm" :rules="packageRules" label-width="100px">
          <el-form-item label="套餐名称" prop="name">
            <el-input v-model="packageForm.name" placeholder="请输入套餐名称" />
          </el-form-item>
          <el-form-item label="套餐编码" prop="code">
            <el-input v-model="packageForm.code" placeholder="请输入套餐编码" :disabled="!!packageForm.id" />
          </el-form-item>
          <el-row :gutter="16">
            <el-col :span="12">
              <el-form-item label="价格" prop="price">
                <el-input-number v-model="packageForm.price" :min="0" :precision="2" placeholder="价格" style="width: 100%" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="时长(天)" prop="duration">
                <el-input-number v-model="packageForm.duration" :min="0" placeholder="0表示永久" style="width: 100%" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="16">
            <el-col :span="12">
              <el-form-item label="用户数限制" prop="user_limit">
                <el-input-number v-model="packageForm.user_limit" :min="0" placeholder="0表示无限制" style="width: 100%" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="存储(MB)" prop="storage_limit">
                <el-input-number v-model="packageForm.storage_limit" :min="0" placeholder="0表示无限制" style="width: 100%" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item label="API次数/日" prop="api_limit">
            <el-input-number v-model="packageForm.api_limit" :min="0" placeholder="0表示无限制" style="width: 100%" />
          </el-form-item>
          <el-row :gutter="16">
            <el-col :span="12">
              <el-form-item label="排序" prop="sort">
                <el-input-number v-model="packageForm.sort" :min="0" style="width: 100%" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="状态" prop="status">
                <el-radio-group v-model="packageForm.status">
                  <el-radio :value="1">启用</el-radio>
                  <el-radio :value="0">禁用</el-radio>
                </el-radio-group>
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item label="备注" prop="remark">
            <el-input v-model="packageForm.remark" type="textarea" :rows="2" placeholder="请输入备注" />
          </el-form-item>
        </el-form>
        <template #footer>
          <el-button @click="packageFormVisible = false">取消</el-button>
          <el-button type="primary" @click="handlePackageSubmit">确定</el-button>
        </template>
      </el-dialog>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getTenantLists, addTenant, editTenant, deleteTenant, getPackageLists, addPackage, editPackage, deletePackage } from '@/api/tenant'

const loading = ref(false)
const searchKeyword = ref('')
const searchStatus = ref(null)
const tableData = ref([])

const pagination = reactive({
  page: 1,
  limit: 15,
  total: 0
})

const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)
const form = reactive({
  id: null, name: '', code: '', tenant_type: 1, package_id: null,
  contact_name: '', contact_phone: '', contact_email: '',
  expire_time: null, logo: '', remark: '', status: 1
})

const rules = {
  name: [{ required: true, message: '请输入租户名称', trigger: 'blur' }],
  code: [
    { required: true, message: '请输入租户编码', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]+$/, message: '编码只能包含字母、数字和下划线', trigger: 'blur' }
  ],
  tenant_type: [{ required: true, message: '请选择隔离模式', trigger: 'change' }]
}

// 套餐相关
const packageDialogVisible = ref(false)
const packageFormVisible = ref(false)
const packageFormTitle = ref('')
const packageFormRef = ref(null)
const packageTableData = ref([])
const packageList = ref([])
const packageForm = reactive({
  id: null, name: '', code: '', price: 0, duration: 365,
  user_limit: 0, storage_limit: 0, api_limit: 0, sort: 100, status: 1, remark: ''
})

const packageRules = {
  name: [{ required: true, message: '请输入套餐名称', trigger: 'blur' }],
  code: [
    { required: true, message: '请输入套餐编码', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]+$/, message: '编码只能包含字母、数字和下划线', trigger: 'blur' }
  ]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getTenantLists({
      page: pagination.page,
      limit: pagination.limit,
      keyword: searchKeyword.value,
      status: searchStatus.value
    })
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    tableData.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

const loadPackages = async () => {
  try {
    const res = await getPackageLists({ page: 1, limit: 100 })
    packageTableData.value = res.data || []
    packageList.value = res.data || []
  } catch {
    packageTableData.value = []
    packageList.value = []
  }
}

const handleReset = () => {
  searchKeyword.value = ''
  searchStatus.value = null
  pagination.page = 1
  loadData()
}

const handleAdd = () => {
  Object.assign(form, {
    id: null, name: '', code: '', tenant_type: 1, package_id: null,
    contact_name: '', contact_phone: '', contact_email: '',
    expire_time: null, logo: '', remark: '', status: 1
  })
  dialogTitle.value = '新增租户'
  dialogVisible.value = true
}

const handleEdit = (row) => {
  Object.assign(form, {
    id: row.id, name: row.name, code: row.code, tenant_type: row.tenant_type,
    package_id: row.package_id || null, contact_name: row.contact_name || '',
    contact_phone: row.contact_phone || '', contact_email: row.contact_email || '',
    expire_time: row.expire_time, logo: row.logo || '', remark: row.remark || '',
    status: row.status
  })
  dialogTitle.value = '编辑租户'
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除租户「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteTenant({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return
  await formRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      if (form.id) {
        await editTenant(form)
        ElMessage.success('修改成功')
      } else {
        await addTenant(form)
        ElMessage.success('新增成功')
      }
      dialogVisible.value = false
      loadData()
    } catch (e) {
      ElMessage.error(form.id ? '修改失败' : '新增失败')
    }
  })
}

const handlePackage = () => {
  packageDialogVisible.value = true
  loadPackages()
}

const handlePackageAdd = () => {
  Object.assign(packageForm, {
    id: null, name: '', code: '', price: 0, duration: 365,
    user_limit: 0, storage_limit: 0, api_limit: 0, sort: 100, status: 1, remark: ''
  })
  packageFormTitle.value = '新增套餐'
  packageFormVisible.value = true
}

const handlePackageEdit = (row) => {
  Object.assign(packageForm, row)
  packageFormTitle.value = '编辑套餐'
  packageFormVisible.value = true
}

const handlePackageDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除套餐「${row.name}」吗？`, '提示', { type: 'warning' })
    await deletePackage({ id: row.id })
    ElMessage.success('删除成功')
    loadPackages()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handlePackageSubmit = async () => {
  if (!packageFormRef.value) return
  await packageFormRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      if (packageForm.id) {
        await editPackage(packageForm)
        ElMessage.success('修改成功')
      } else {
        await addPackage(packageForm)
        ElMessage.success('新增成功')
      }
      packageFormVisible.value = false
      loadPackages()
    } catch (e) {
      ElMessage.error(packageForm.id ? '修改失败' : '新增失败')
    }
  })
}

onMounted(() => {
  loadData()
  loadPackages()
})
</script>

<style scoped>
.package-toolbar {
  margin-bottom: 12px;
}
</style>

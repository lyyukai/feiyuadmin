<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增小程序</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.name" placeholder="小程序名称" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="name" label="小程序名称" min-width="150" />
        <el-table-column prop="appid" label="AppID" min-width="180" show-overflow-tooltip />
        <el-table-column prop="appsecret" label="AppSecret" min-width="150">
          <template #default="{ row }">
            {{ row.appsecret || '****' }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="280" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="primary" size="small" @click="openVersion(row)">版本管理</el-button>
            <el-button link type="primary" size="small" @click="openMember(row)">成员管理</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next"
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑小程序' : '新增小程序'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="小程序名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入小程序名称" />
        </el-form-item>
        <el-form-item label="AppID" prop="appid">
          <el-input v-model="form.appid" placeholder="请输入AppID" />
        </el-form-item>
        <el-form-item label="AppSecret" prop="appsecret">
          <el-input v-model="form.appsecret" placeholder="请输入AppSecret" show-password />
        </el-form-item>
        <el-form-item label="描述">
          <el-input v-model="form.description" type="textarea" rows="3" placeholder="请输入描述" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>

    <!-- 版本管理弹窗 -->
    <el-dialog v-model="versionVisible" title="版本管理" width="700px" destroy-on-close>
      <div style="margin-bottom: 15px;">
        <el-button type="primary" size="small" @click="openVersionForm()">添加版本</el-button>
      </div>
      <el-table :data="versionList" stripe>
        <el-table-column prop="version" label="版本号" width="120" />
        <el-table-column prop="version_desc" label="版本描述" min-width="200" show-overflow-tooltip />
        <el-table-column prop="audit_status" label="审核状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="getAuditStatusType(row.audit_status)" size="small">
              {{ getAuditStatusText(row.audit_status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="发布状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
              {{ row.status === 1 ? '已发布' : '未发布' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="操作" width="100" align="center">
          <template #default="{ row }">
            <el-button link type="danger" size="small" @click="deleteVersion(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>

    <!-- 版本表单弹窗 -->
    <el-dialog v-model="versionFormVisible" title="添加版本" width="400px" destroy-on-close>
      <el-form :model="versionForm" label-width="100px">
        <el-form-item label="版本号">
          <el-input v-model="versionForm.version" placeholder="如：1.0.0" />
        </el-form-item>
        <el-form-item label="版本描述">
          <el-input v-model="versionForm.version_desc" type="textarea" rows="3" placeholder="请输入版本描述" />
        </el-form-item>
        <el-form-item label="模板ID">
          <el-input v-model="versionForm.template_id" placeholder="请输入模板ID" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="versionFormVisible = false">取消</el-button>
        <el-button type="primary" @click="saveVersion">确定</el-button>
      </template>
    </el-dialog>

    <!-- 成员管理弹窗 -->
    <el-dialog v-model="memberVisible" title="成员管理" width="700px" destroy-on-close>
      <div style="margin-bottom: 15px;">
        <el-button type="primary" size="small" @click="openMemberForm()">添加成员</el-button>
      </div>
      <el-table :data="memberList" stripe>
        <el-table-column prop="username" label="微信号" min-width="150" />
        <el-table-column prop="role" label="角色" width="120" align="center">
          <template #default="{ row }">
            <el-tag :type="row.role === 'developer' ? 'primary' : 'success'" size="small">
              {{ row.role === 'developer' ? '开发者' : '体验者' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="添加时间" width="160" />
        <el-table-column label="操作" width="100" align="center">
          <template #default="{ row }">
            <el-button link type="danger" size="small" @click="deleteMember(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>

    <!-- 成员表单弹窗 -->
    <el-dialog v-model="memberFormVisible" title="添加成员" width="400px" destroy-on-close>
      <el-form :model="memberForm" label-width="100px">
        <el-form-item label="微信号">
          <el-input v-model="memberForm.username" placeholder="请输入微信号" />
        </el-form-item>
        <el-form-item label="角色">
          <el-radio-group v-model="memberForm.role">
            <el-radio value="developer">开发者</el-radio>
            <el-radio value="experience">体验者</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="memberFormVisible = false">取消</el-button>
        <el-button type="primary" @click="saveMember">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getMiniProgramList, addMiniProgram, editMiniProgram, deleteMiniProgram, getMiniProgramVersionList, addMiniProgramVersion, deleteMiniProgramVersion, getMiniProgramMemberList, addMiniProgramMember, deleteMiniProgramMember } from '@/api/wechat'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])

const searchForm = reactive({
  name: ''
})

const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

const formVisible = ref(false)
const form = reactive({
  id: 0,
  name: '',
  appid: '',
  appsecret: '',
  description: ''
})

const rules = {
  name: [{ required: true, message: '请输入小程序名称', trigger: 'blur' }],
  appid: [{ required: true, message: '请输入AppID', trigger: 'blur' }]
}

// 版本管理
const versionVisible = ref(false)
const versionList = ref([])
const currentMiniProgram = ref(null)

const versionFormVisible = ref(false)
const versionForm = reactive({
  version: '',
  version_desc: '',
  template_id: ''
})

// 成员管理
const memberVisible = ref(false)
const memberList = ref([])

const memberFormVisible = ref(false)
const memberForm = reactive({
  username: '',
  role: 'developer'
})

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    }
    const res = await getMiniProgramList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 重置搜索
const resetSearch = () => {
  searchForm.name = ''
  pagination.page = 1
  loadData()
}

// 打开表单
const openForm = (row) => {
  if (row) {
    Object.assign(form, {
      id: row.id,
      name: row.name,
      appid: row.appid,
      appsecret: '',
      description: row.description || ''
    })
  } else {
    Object.assign(form, { id: 0, name: '', appid: '', appsecret: '', description: '' })
  }
  formVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
  submitLoading.value = true
  try {
    if (form.id) {
      await editMiniProgram(form)
    } else {
      await addMiniProgram(form)
    }
    ElMessage.success('保存成功')
    formVisible.value = false
    loadData()
  } catch (e) {
    console.error(e)
  } finally {
    submitLoading.value = false
  }
}

// 删除
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定删除该小程序？', '提示')
    await deleteMiniProgram(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

// 状态切换
const handleStatusChange = async (row) => {
  try {
    await editMiniProgram({ id: row.id, status: row.status })
    ElMessage.success('设置成功')
  } catch (e) {
    console.error(e)
  }
}

// 版本管理
const openVersion = async (row) => {
  currentMiniProgram.value = row
  versionVisible.value = true
  await loadVersionList()
}

const loadVersionList = async () => {
  try {
    const res = await getMiniProgramVersionList({ mini_program_id: currentMiniProgram.value.id })
    versionList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const openVersionForm = () => {
  Object.assign(versionForm, { version: '', version_desc: '', template_id: '' })
  versionFormVisible.value = true
}

const saveVersion = async () => {
  try {
    await addMiniProgramVersion({
      mini_program_id: currentMiniProgram.value.id,
      ...versionForm
    })
    ElMessage.success('添加成功')
    versionFormVisible.value = false
    loadVersionList()
  } catch (e) {
    console.error(e)
  }
}

const deleteVersion = async (row) => {
  try {
    await ElMessageBox.confirm('确定删除该版本？', '提示')
    await deleteMiniProgramVersion(row.id)
    ElMessage.success('删除成功')
    loadVersionList()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

const getAuditStatusText = (status) => {
  const map = { 0: '未提交', 1: '审核中', 2: '通过', 3: '拒绝' }
  return map[status] || '未知'
}

const getAuditStatusType = (status) => {
  const map = { 0: 'info', 1: 'warning', 2: 'success', 3: 'danger' }
  return map[status] || 'info'
}

// 成员管理
const openMember = async (row) => {
  currentMiniProgram.value = row
  memberVisible.value = true
  await loadMemberList()
}

const loadMemberList = async () => {
  try {
    const res = await getMiniProgramMemberList({ mini_program_id: currentMiniProgram.value.id })
    memberList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const openMemberForm = () => {
  Object.assign(memberForm, { username: '', role: 'developer' })
  memberFormVisible.value = true
}

const saveMember = async () => {
  try {
    await addMiniProgramMember({
      mini_program_id: currentMiniProgram.value.id,
      ...memberForm
    })
    ElMessage.success('添加成功')
    memberFormVisible.value = false
    loadMemberList()
  } catch (e) {
    console.error(e)
  }
}

const deleteMember = async (row) => {
  try {
    await ElMessageBox.confirm('确定删除该成员？', '提示')
    await deleteMiniProgramMember(row.id)
    ElMessage.success('删除成功')
    loadMemberList()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.pagination-container {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}
</style>

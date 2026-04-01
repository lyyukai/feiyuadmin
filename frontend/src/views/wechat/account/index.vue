<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增账号</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="账号名称 / AppID" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="name" label="账号名称" min-width="120" />
        <el-table-column prop="appid" label="AppID" min-width="180" show-overflow-tooltip />
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.type === 1 ? 'success' : 'info'" size="small">
              {{ row.type === 1 ? '服务号' : '订阅号' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑账号' : '新增账号'" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="账号名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入账号名称" />
        </el-form-item>
        <el-form-item label="AppID" prop="appid">
          <el-input v-model="form.appid" placeholder="请输入AppID" />
        </el-form-item>
        <el-form-item label="AppSecret" prop="appsecret">
          <el-input v-model="form.appsecret" placeholder="请输入AppSecret" show-password />
        </el-form-item>
        <el-form-item label="Token" prop="token">
          <el-input v-model="form.token" placeholder="请输入Token" />
        </el-form-item>
        <el-form-item label="EncodingAESKey" prop="encoding_aes_key">
          <el-input v-model="form.encoding_aes_key" placeholder="请输入EncodingAESKey" />
        </el-form-item>
        <el-form-item label="公众号类型" prop="type">
          <el-radio-group v-model="form.type">
            <el-radio :value="1">服务号</el-radio>
            <el-radio :value="2">订阅号</el-radio>
          </el-radio-group>
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
import { getWechatAccountList, addWechatAccount, editWechatAccount, deleteWechatAccount } from '@/api/wechat'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)

const searchForm = reactive({ keyword: '' })

const form = reactive({
  id: null, name: '', appid: '', appsecret: '', token: '',
  encoding_aes_key: '', type: 1
})

const rules = {
  name: [{ required: true, message: '请输入账号名称', trigger: 'blur' }],
  appid: [{ required: true, message: '请输入AppID', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getWechatAccountList()
    let list = res.data || []
    if (searchForm.keyword) {
      const kw = searchForm.keyword.toLowerCase()
      list = list.filter(item =>
        (item.name && item.name.toLowerCase().includes(kw)) ||
        (item.appid && item.appid.toLowerCase().includes(kw))
      )
    }
    tableData.value = list
  } catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}

const resetSearch = () => { searchForm.keyword = ''; loadData() }

const openForm = (row) => {
  if (row) Object.assign(form, row)
  else { form.id = null; form.name = ''; form.appid = ''; form.appsecret = ''; form.token = ''; form.encoding_aes_key = ''; form.type = 1 }
  formVisible.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    if (form.id) { await editWechatAccount(form); ElMessage.success('编辑成功') }
    else { await addWechatAccount(form); ElMessage.success('新增成功') }
    formVisible.value = false; loadData()
  } catch (e) { ElMessage.error(e.message || '操作失败') } finally { submitLoading.value = false }
}

const handleDelete = async (row) => {
  try { await ElMessageBox.confirm(`确定删除账号"${row.name}"吗？`, '提示', { type: 'warning' }); await deleteWechatAccount(row.id); ElMessage.success('删除成功'); loadData() } catch {}
}

const handleStatusChange = async (row) => {
  try { await editWechatAccount({ id: row.id, status: row.status }); ElMessage.success('状态更新成功') }
  catch { row.status = row.status === 1 ? 0 : 1; ElMessage.error('更新失败') }
}

onMounted(() => loadData())
</script>

<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增平台</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.name" placeholder="平台名称" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="name" label="平台名称" min-width="150" />
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
            <el-button link type="primary" size="small" @click="openAuth(row)">授权列表</el-button>
            <el-button link type="primary" size="small" @click="getPreAuthUrl(row)">预授权</el-button>
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
    <el-dialog v-model="formVisible" :title="form.id ? '编辑平台' : '新增平台'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="平台名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入平台名称" />
        </el-form-item>
        <el-form-item label="AppID" prop="appid">
          <el-input v-model="form.appid" placeholder="请输入AppID" />
        </el-form-item>
        <el-form-item label="AppSecret" prop="appsecret">
          <el-input v-model="form.appsecret" placeholder="请输入AppSecret" show-password />
        </el-form-item>
        <el-form-item label="Token">
          <el-input v-model="form.token" placeholder="请输入Token" />
        </el-form-item>
        <el-form-item label="EncodingAESKey">
          <el-input v-model="form.encoding_aeskey" placeholder="请输入EncodingAESKey" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>

    <!-- 授权列表弹窗 -->
    <el-dialog v-model="authVisible" title="授权公众号/小程序" width="800px" destroy-on-close>
      <el-table :data="authList" stripe>
        <el-table-column prop="nick_name" label="名称" min-width="120" />
        <el-table-column prop="authorizer_appid" label="AppID" min-width="180" show-overflow-tooltip />
        <el-table-column prop="authorizer_type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.authorizer_type === 1 ? 'success' : 'primary'" size="small">
              {{ row.authorizer_type === 1 ? '公众号' : '小程序' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="principal_name" label="主体" min-width="120" show-overflow-tooltip />
        <el-table-column prop="alias" label="别名" width="120" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
              {{ row.status === 1 ? '已授权' : '已取消' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="授权时间" width="160" />
      </el-table>
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="authPagination.page"
          v-model:page-size="authPagination.limit"
          :total="authPagination.total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next"
          @size-change="loadAuthList"
          @current-change="loadAuthList"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getOpenPlatformList, addOpenPlatform, editOpenPlatform, deleteOpenPlatform, getOpenPlatformAuthList, getOpenPlatformPreAuthUrl } from '@/api/wechat'

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
  token: '',
  encoding_aeskey: ''
})

const rules = {
  name: [{ required: true, message: '请输入平台名称', trigger: 'blur' }],
  appid: [{ required: true, message: '请输入AppID', trigger: 'blur' }],
  appsecret: [{ required: true, message: '请输入AppSecret', trigger: 'blur' }]
}

// 授权列表
const authVisible = ref(false)
const authList = ref([])
const currentPlatform = ref(null)

const authPagination = reactive({
  page: 1,
  limit: 10,
  total: 0
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
    const res = await getOpenPlatformList(params)
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
      token: row.token || '',
      encoding_aeskey: row.encoding_aeskey || ''
    })
  } else {
    Object.assign(form, { id: 0, name: '', appid: '', appsecret: '', token: '', encoding_aeskey: '' })
  }
  formVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
  submitLoading.value = true
  try {
    if (form.id) {
      await editOpenPlatform(form)
    } else {
      await addOpenPlatform(form)
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
    await ElMessageBox.confirm('确定删除该平台？', '提示')
    await deleteOpenPlatform(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

// 状态切换
const handleStatusChange = async (row) => {
  try {
    await editOpenPlatform({ id: row.id, status: row.status })
    ElMessage.success('设置成功')
  } catch (e) {
    console.error(e)
  }
}

// 授权列表
const openAuth = async (row) => {
  currentPlatform.value = row
  authVisible.value = true
  await loadAuthList()
}

const loadAuthList = async () => {
  try {
    const params = {
      page: authPagination.page,
      limit: authPagination.limit,
      platform_id: currentPlatform.value.id
    }
    const res = await getOpenPlatformAuthList(params)
    authList.value = res.data || []
    authPagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  }
}

// 获取预授权URL
const getPreAuthUrl = async (row) => {
  try {
    const res = await getOpenPlatformPreAuthUrl({ platform_id: row.id })
    if (res.data && res.data.url) {
      ElMessage.success('预授权链接已生成')
      // 可以复制URL或者打开新窗口
      window.open(res.data.url, '_blank')
    } else {
      ElMessage.warning('暂无可用的预授权链接')
    }
  } catch (e) {
    console.error(e)
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

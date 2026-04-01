<template>
  <div class="page-container">
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">消息模板管理</span>
        <div class="header-actions">
          <el-select v-model="searchForm.channel_id" placeholder="筛选渠道" style="width: 150px" clearable @change="loadData">
            <el-option v-for="ch in channels" :key="ch.id" :label="ch.name" :value="ch.id" />
          </el-select>
          <el-input v-model="searchForm.keyword" placeholder="搜索模板名称/编码" style="width: 200px" clearable @clear="loadData" @keyup.enter="loadData">
            <template #prefix><el-icon><Search /></el-icon></template>
          </el-input>
          <el-button type="primary" @click="handleAdd">
            <el-icon><Plus /></el-icon> 新增模板
          </el-button>
        </div>
      </div>

      <el-table :data="tableData" v-loading="loading" row-key="id">
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="name" label="模板名称" min-width="140" />
        <el-table-column prop="code" label="模板编码" width="140">
          <template #default="{ row }">
            <el-tag size="small" type="info">{{ row.code }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="channel_name" label="所属渠道" width="120" align="center">
          <template #default="{ row }">
            <span>{{ row.channel_name || '-' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="标题模板" min-width="150" show-overflow-tooltip />
        <el-table-column prop="vars" label="变量" width="150">
          <template #default="{ row }">
            <span v-if="row.vars && row.vars.length">
              <el-tag v-for="v in row.vars.slice(0, 3)" :key="v" size="small" style="margin-right: 4px">{{ v }}</el-tag>
              <span v-if="row.vars.length > 3">...</span>
            </span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="160" align="center" />
        <el-table-column label="操作" width="160" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next"
          background
          @current-change="loadData"
          @size-change="loadData"
        />
      </div>
    </div>

    <!-- 模板编辑弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="640px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="formRules" label-width="100px">
        <el-form-item label="模板名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入模板名称" />
        </el-form-item>
        <el-form-item label="模板编码" prop="code">
          <el-input v-model="form.code" placeholder="如: verify_code, order_notify" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="通知渠道" prop="channel_id">
          <el-select v-model="form.channel_id" placeholder="请选择通知渠道" style="width: 100%">
            <el-option v-for="ch in channels" :key="ch.id" :label="`${ch.name} (${typeMap[ch.type]})`" :value="ch.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="标题模板" prop="title">
          <el-input v-model="form.title" placeholder="如: 您好，${username}，您的验证码是${code}" />
          <div class="form-tip">支持变量: ${变量名}</div>
        </el-form-item>
        <el-form-item label="内容模板" prop="content">
          <el-input v-model="form.content" type="textarea" :rows="5" placeholder="请输入消息内容，支持变量替换" />
          <div class="form-tip">支持变量: ${变量名}，如: 尊敬的用户，您的验证码是${code}，有效期${minutes}分钟</div>
        </el-form-item>
        <el-form-item label="变量列表">
          <el-input v-model="form.vars" placeholder="逗号分隔，如: username,code,minutes" />
          <div class="form-tip">定义内容中使用的变量，用于发送时传参</div>
        </el-form-item>
        <el-form-item label="状态">
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
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search } from '@element-plus/icons-vue'
import { getNoticeTemplateLists, addNoticeTemplate, editNoticeTemplate, deleteNoticeTemplate, getNoticeChannelLists } from '@/api'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const channels = ref([])
const dialogVisible = ref(false)
const formRef = ref(null)
const isEdit = ref(false)

const pagination = reactive({ page: 1, pageSize: 20, total: 0 })
const searchForm = reactive({ keyword: '', channel_id: '' })

const form = reactive({
  id: null,
  name: '',
  code: '',
  channel_id: null,
  title: '',
  content: '',
  vars: '',
  status: 1
})

const formRules = {
  name: [{ required: true, message: '请输入模板名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入模板编码', trigger: 'blur' }],
  channel_id: [{ required: true, message: '请选择通知渠道', trigger: 'change' }],
  content: [{ required: true, message: '请输入内容模板', trigger: 'blur' }]
}

const typeMap = { 1: '邮件', 2: '短信', 3: '企微', 4: '站内信' }
const dialogTitle = computed(() => isEdit.value ? '编辑模板' : '新增模板')

const loadChannels = async () => {
  try {
    const res = await getNoticeChannelLists({ page: 1, page_size: 100 })
    channels.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getNoticeTemplateLists({
      page: pagination.page,
      page_size: pagination.pageSize,
      keyword: searchForm.keyword,
      channel_id: searchForm.channel_id
    })
    tableData.value = res.data || []
    pagination.total = parseInt(res.headers?.['x-total'] || 0)
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleAdd = () => {
  isEdit.value = false
  Object.assign(form, { id: null, name: '', code: '', channel_id: null, title: '', content: '', vars: '', status: 1 })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  Object.assign(form, {
    id: row.id,
    name: row.name,
    code: row.code,
    channel_id: row.channel_id,
    title: row.title || '',
    content: row.content,
    vars: Array.isArray(row.vars) ? row.vars.join(',') : row.vars || '',
    status: row.status
  })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除模板「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteNoticeTemplate(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editNoticeTemplate({ id: row.id, status: row.status })
    ElMessage.success(row.status === 1 ? '已启用' : '已禁用')
  } catch (e) {
    row.status = row.status === 1 ? 0 : 1
    ElMessage.error('操作失败')
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    const data = { ...form }
    if (isEdit.value) {
      await editNoticeTemplate(data)
    } else {
      await addNoticeTemplate(data)
    }
    ElMessage.success(isEdit.value ? '编辑成功' : '添加成功')
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证失败
  } finally {
    submitLoading.value = false
  }
}

onMounted(() => { loadChannels().then(loadData) })
</script>

<style scoped>
.header-actions { display: flex; gap: 10px; align-items: center; }
.form-tip { font-size: 12px; color: #909399; margin-top: 4px; line-height: 1.4; }
</style>

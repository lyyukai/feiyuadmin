<template>
  <div class="page-container">
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">通知渠道配置</span>
        <div class="header-actions">
          <el-input v-model="searchForm.keyword" placeholder="搜索渠道名称/编码" style="width: 200px" clearable @clear="loadData" @keyup.enter="loadData">
            <template #prefix><el-icon><Search /></el-icon></template>
          </el-input>
          <el-button type="primary" @click="handleAdd">
            <el-icon><Plus /></el-icon> 新增渠道
          </el-button>
        </div>
      </div>

      <el-table :data="tableData" v-loading="loading" row-key="id">
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="name" label="渠道名称" min-width="120" />
        <el-table-column prop="code" label="渠道编码" width="120">
          <template #default="{ row }">
            <el-tag size="small" type="info">{{ row.code }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag size="small" :type="typeTagType[row.type]">{{ typeMap[row.type] }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="remark" label="备注" min-width="150" show-overflow-tooltip />
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

    <!-- 渠道编辑弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="560px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="formRules" label-width="100px">
        <el-form-item label="渠道名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入渠道名称" />
        </el-form-item>
        <el-form-item label="渠道编码" prop="code">
          <el-input v-model="form.code" placeholder="如: email, sms, wechat, message" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="渠道类型" prop="type">
          <el-select v-model="form.type" placeholder="请选择类型" style="width: 100%">
            <el-option v-for="item in typeOptions" :key="item.value" :label="item.label" :value="item.value" />
          </el-select>
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">启用</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注">
          <el-input v-model="form.remark" type="textarea" :rows="2" placeholder="请输入备注" />
        </el-form-item>

        <!-- 邮件配置 -->
        <template v-if="form.type === 1">
          <el-divider content-position="left">邮件配置</el-divider>
          <el-form-item label="SMTP服务器">
            <el-input v-model="form.config.host" placeholder="如: smtp.qq.com" />
          </el-form-item>
          <el-form-item label="SMTP端口">
            <el-input-number v-model="form.config.port" :min="1" :max="65535" />
          </el-form-item>
          <el-form-item label="用户名">
            <el-input v-model="form.config.username" placeholder="邮箱账号" />
          </el-form-item>
          <el-form-item label="密码">
            <el-input v-model="form.config.password" type="password" placeholder="授权码" />
          </el-form-item>
          <el-form-item label="加密方式">
            <el-select v-model="form.config.secure" style="width: 100%">
              <el-option label="TLS" value="tls" />
              <el-option label="SSL" value="ssl" />
            </el-select>
          </el-form-item>
          <el-form-item label="发件人邮箱">
            <el-input v-model="form.config.from_email" placeholder="如: admin@example.com" />
          </el-form-item>
          <el-form-item label="发件人名称">
            <el-input v-model="form.config.from_name" placeholder="如: 飞鱼系统" />
          </el-form-item>
        </template>

        <!-- 短信配置 -->
        <template v-if="form.type === 2">
          <el-divider content-position="left">短信配置</el-divider>
          <el-form-item label="服务商">
            <el-select v-model="form.config.provider" style="width: 100%">
              <el-option label="阿里云短信" value="aliyun" />
              <el-option label="腾讯云短信" value="tencent" />
            </el-select>
          </el-form-item>
          <template v-if="form.config.provider === 'aliyun'">
            <el-form-item label="AccessKey ID">
              <el-input v-model="form.config.access_key_id" placeholder="阿里云AccessKey ID" />
            </el-form-item>
            <el-form-item label="AccessKey Secret">
              <el-input v-model="form.config.access_key_secret" type="password" placeholder="阿里云AccessKey Secret" />
            </el-form-item>
            <el-form-item label="签名名称">
              <el-input v-model="form.config.sign_name" placeholder="短信签名名称" />
            </el-form-item>
            <el-form-item label="模板CODE">
              <el-input v-model="form.config.template_code" placeholder="短信模板CODE" />
            </el-form-item>
          </template>
          <template v-if="form.config.provider === 'tencent'">
            <el-form-item label="SecretId">
              <el-input v-model="form.config.secret_id" placeholder="腾讯云SecretId" />
            </el-form-item>
            <el-form-item label="SecretKey">
              <el-input v-model="form.config.secret_key" type="password" placeholder="腾讯云SecretKey" />
            </el-form-item>
            <el-form-item label="AppId">
              <el-input v-model="form.config.app_id" placeholder="腾讯云AppId" />
            </el-form-item>
            <el-form-item label="签名名称">
              <el-input v-model="form.config.sign_name" placeholder="短信签名" />
            </el-form-item>
            <el-form-item label="模板ID">
              <el-input v-model="form.config.template_id" placeholder="短信模板ID" />
            </el-form-item>
          </template>
        </template>

        <!-- 企微机器人配置 -->
        <template v-if="form.type === 3">
          <el-divider content-position="left">企微机器人配置</el-divider>
          <el-form-item label="Webhook地址">
            <el-input v-model="form.config.webhook_url" type="textarea" :rows="3" placeholder="企业微信群机器人Webhook地址" />
          </el-form-item>
        </template>
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
import { getNoticeChannelLists, addNoticeChannel, editNoticeChannel, deleteNoticeChannel } from '@/api'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const formRef = ref(null)
const isEdit = ref(false)

const pagination = reactive({ page: 1, pageSize: 20, total: 0 })
const searchForm = reactive({ keyword: '' })

const form = reactive({
  id: null,
  name: '',
  code: '',
  type: 1,
  status: 1,
  remark: '',
  config: {}
})

const formRules = {
  name: [{ required: true, message: '请输入渠道名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入渠道编码', trigger: 'blur' }],
  type: [{ required: true, message: '请选择渠道类型', trigger: 'change' }]
}

const typeMap = { 1: '邮件', 2: '短信', 3: '企微机器人', 4: '站内信' }
const typeTagType = { 1: 'primary', 2: 'success', 3: 'warning', 4: 'info' }
const typeOptions = [
  { value: 1, label: '邮件' },
  { value: 2, label: '短信' },
  { value: 3, label: '企微机器人' },
  { value: 4, label: '站内信' }
]

const dialogTitle = computed(() => isEdit.value ? '编辑渠道' : '新增渠道')

const loadData = async () => {
  loading.value = true
  try {
    const res = await getNoticeChannelLists({
      page: pagination.page,
      page_size: pagination.pageSize,
      keyword: searchForm.keyword
    })
    // 字段标准化：后端返回 channel_name/channel_type，前端用 name/type
    tableData.value = (res.data || []).map(item => ({
      ...item,
      name: item.channel_name ?? item.name,
      type: item.channel_type ?? item.type
    }))
    pagination.total = parseInt(res.headers?.['x-total'] || 0)
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleAdd = () => {
  isEdit.value = false
  Object.assign(form, { id: null, name: '', code: '', type: 1, status: 1, remark: '', config: {} })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  Object.assign(form, {
    id: row.id,
    name: row.name,
    code: row.code,
    type: row.type,
    status: row.status,
    remark: row.remark || '',
    config: row.config || {}
  })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除渠道「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteNoticeChannel(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editNoticeChannel({ id: row.id, status: row.status })
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
      await editNoticeChannel(data)
    } else {
      await addNoticeChannel(data)
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

onMounted(() => { loadData() })
</script>

<style scoped>
.header-actions { display: flex; gap: 10px; align-items: center; }
</style>

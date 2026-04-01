<template>
  <div class="send-message">
    <el-form ref="formRef" :model="form" :rules="formRules" label-width="100px" style="max-width: 680px">
      <el-form-item label="发送渠道" prop="channel_code">
        <el-select v-model="form.channel_code" placeholder="请选择发送渠道" style="width: 100%" @change="onChannelChange">
          <el-option v-for="ch in channels" :key="ch.id" :label="`${ch.name} (${typeMap[ch.type]})`" :value="ch.code" />
        </el-select>
      </el-form-item>

      <el-form-item label="消息模板" prop="template_code">
        <el-select v-model="form.template_code" placeholder="可选，选择后自动填充标题和内容" style="width: 100%" clearable @change="onTemplateChange">
          <el-option v-for="t in templates" :key="t.id" :label="t.name" :value="t.code" />
        </el-select>
      </el-form-item>

      <el-form-item label="接收者" prop="receiver">
        <el-input v-model="form.receiver" :placeholder="receiverPlaceholder" />
        <div class="form-tip">{{ receiverTip }}</div>
      </el-form-item>

      <el-form-item label="标题" prop="title">
        <el-input v-model="form.title" placeholder="请输入消息标题" />
        <div v-if="form.template_code && currentTemplate" class="form-tip">
          模板变量: {{ (currentTemplate.vars || []).join(', ') }}
        </div>
      </el-form-item>

      <el-form-item label="内容" prop="content">
        <el-input v-model="form.content" type="textarea" :rows="5" placeholder="请输入消息内容，支持变量替换" />
      </el-form-item>

      <!-- 变量填充区 -->
      <template v-if="form.template_code && currentTemplate && currentTemplate.vars && currentTemplate.vars.length">
        <el-divider>变量填充</el-divider>
        <el-form-item v-for="v in currentTemplate.vars" :key="v" :label="v">
          <el-input v-model="form.vars[v]" :placeholder="`请输入 ${v} 的值`" />
        </el-form-item>
      </template>

      <el-form-item>
        <el-button type="primary" @click="submitForm" :loading="submitLoading">发送消息</el-button>
        <el-button @click="resetForm">重置</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { getNoticeChannelLists, getNoticeTemplateLists, sendNoticeMessage } from '@/api'

const submitLoading = ref(false)
const formRef = ref(null)
const channels = ref([])
const templates = ref([])

const form = reactive({
  channel_code: '',
  template_code: '',
  receiver: '',
  title: '',
  content: '',
  vars: {}
})

const formRules = {
  channel_code: [{ required: true, message: '请选择发送渠道', trigger: 'change' }],
  receiver: [{ required: true, message: '请输入接收者', trigger: 'blur' }],
  title: [{ required: true, message: '请输入消息标题', trigger: 'blur' }],
  content: [{ required: true, message: '请输入消息内容', trigger: 'blur' }]
}

const typeMap = { 1: '邮件', 2: '短信', 3: '企微', 4: '站内信' }

const receiverPlaceholder = computed(() => {
  const map = {
    email: '请输入邮箱地址',
    sms: '请输入手机号',
    wechat: '请输入企微用户ID',
    message: '请输入用户ID'
  }
  return map[form.channel_code] || '请输入接收者'
})

const receiverTip = computed(() => {
  const map = {
    email: '多个邮箱用逗号分隔',
    sms: '多个手机号用逗号分隔',
    wechat: '企微机器人的webhook或用户ID',
    message: '多个用户ID用逗号分隔'
  }
  return map[form.channel_code] || ''
})

const currentTemplate = computed(() => {
  if (!form.template_code) return null
  return templates.value.find(t => t.code === form.template_code)
})

const onChannelChange = async () => {
  form.template_code = ''
  form.title = ''
  form.content = ''
  form.vars = {}
  if (form.channel_code) {
    await loadTemplates()
  } else {
    templates.value = []
  }
}

const onTemplateChange = () => {
  if (currentTemplate.value) {
    form.title = currentTemplate.value.title || ''
    form.content = currentTemplate.value.content || ''
    const vars = currentTemplate.value.vars || []
    form.vars = {}
    vars.forEach(v => { form.vars[v] = '' })
  }
}

const loadChannels = async () => {
  try {
    const res = await getNoticeChannelLists({ page: 1, page_size: 100, status: 1 })
    channels.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const loadTemplates = async () => {
  try {
    const channel = channels.value.find(c => c.code === form.channel_code)
    const params = { page: 1, page_size: 100, status: 1 }
    if (channel) params.channel_id = channel.id
    const res = await getNoticeTemplateLists(params)
    templates.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    const vars = {}
    Object.keys(form.vars).forEach(k => { if (form.vars[k]) vars[k] = form.vars[k] })
    await sendNoticeMessage({
      channel_code: form.channel_code,
      receiver: form.receiver,
      template_code: form.template_code || '',
      title: form.title,
      content: form.content,
      vars
    })
    ElMessage.success('发送成功')
    resetForm()
  } catch (e) {
    // 验证失败
  } finally {
    submitLoading.value = false
  }
}

const resetForm = () => {
  form.channel_code = ''
  form.template_code = ''
  form.receiver = ''
  form.title = ''
  form.content = ''
  form.vars = {}
  templates.value = []
  formRef.value?.resetFields()
}

onMounted(() => { loadChannels() })
</script>

<style scoped>
.form-tip { font-size: 12px; color: #909399; margin-top: 4px; }
</style>

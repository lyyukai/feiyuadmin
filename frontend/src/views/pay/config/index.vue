<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <span style="font-size: 15px; font-weight: 600; color: #303133;">支付配置</span>
      </div>
      <div class="search-bar-right">
        <el-tabs v-model="activeTab" @tab-change="handleTabChange" style="margin: 0">
          <el-tab-pane label="微信支付" name="wechat" />
          <el-tab-pane label="支付宝" name="alipay" />
        </el-tabs>
      </div>
    </div>

    <!-- 配置表单 -->
    <el-card class="table-card" shadow="never" v-loading="loading">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="140px" style="max-width: 700px">
        <el-form-item label="配置名称" prop="name">
          <el-input v-model="form.name" placeholder="如：微信支付-主账号" />
        </el-form-item>
        
        <template v-if="activeTab === 'wechat'">
          <el-form-item label="AppID" prop="appid">
            <el-input v-model="form.appid" placeholder="微信公众号AppID" />
          </el-form-item>
          <el-form-item label="商户号" prop="mchid">
            <el-input v-model="form.mchid" placeholder="微信商户号" />
          </el-form-item>
          <el-form-item label="APIv3密钥" prop="api_key">
            <el-input v-model="form.api_key" placeholder="APIv3密钥" show-password />
          </el-form-item>
          <el-form-item label="证书序列号" prop="api_secret">
            <el-input v-model="form.api_secret" placeholder="证书序列号" />
          </el-form-item>
          <el-form-item label="证书文件路径" prop="cert_path">
            <el-input v-model="form.cert_path" placeholder="apiclient_cert.pem 完整路径" />
          </el-form-item>
          <el-form-item label="密钥文件路径" prop="key_path">
            <el-input v-model="form.key_path" placeholder="apiclient_key.pem 完整路径" />
          </el-form-item>
          <el-form-item label="分账开关">
            <el-switch v-model="form.profit_sharing" active-value="on" inactive-value="off" />
            <span style="margin-left: 10px; color: #909399; font-size: 12px;">开启后支持分账功能</span>
          </el-form-item>
        </template>
        
        <template v-else>
          <el-form-item label="AppID" prop="appid">
            <el-input v-model="form.appid" placeholder="支付宝AppID" />
          </el-form-item>
          <el-form-item label="应用私钥(RSA2)" prop="api_key">
            <el-input v-model="form.api_key" type="textarea" :rows="4" placeholder="RSA2应用私钥" show-password />
          </el-form-item>
          <el-form-item label="支付宝公钥" prop="alipay_public_key">
            <el-input v-model="form.alipay_public_key" type="textarea" :rows="4" placeholder="RSA2支付宝公钥" />
          </el-form-item>
          <el-form-item label="分账开关">
            <el-switch v-model="form.profit_sharing" active-value="on" inactive-value="off" />
            <span style="margin-left: 10px; color: #909399; font-size: 12px;">开启后支持分账功能</span>
          </el-form-item>
        </template>
        
        <el-form-item label="回调地址" prop="notify_url">
          <el-input v-model="form.notify_url" placeholder="支付回调通知地址" />
        </el-form-item>
        
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">启用</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        
        <el-form-item label="备注">
          <el-input v-model="form.remark" type="textarea" :rows="2" placeholder="备注信息" />
        </el-form-item>
        
        <el-form-item>
          <el-button type="primary" :loading="saveLoading" @click="handleSave">
            保存{{ activeTab === 'wechat' ? '微信支付' : '支付宝' }}配置
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { getPayConfigInfo, savePayConfig } from '@/api/pay'

const activeTab = ref('wechat')
const loading = ref(false)
const saveLoading = ref(false)
const formRef = ref(null)

const form = reactive({
  id: 0,
  channel: 'wechat',
  name: '',
  appid: '',
  mchid: '',
  api_key: '',
  api_secret: '',
  cert_path: '',
  key_path: '',
  alipay_public_key: '',
  notify_url: '',
  profit_sharing: 'off',
  status: 1,
  remark: ''
})

const rules = {
  name: [{ required: true, message: '请输入配置名称', trigger: 'blur' }],
  appid: [{ required: true, message: '请输入AppID', trigger: 'blur' }],
  mchid: [{ required: true, message: '请输入商户号', trigger: 'blur' }],
  api_key: [{ required: true, message: '请输入密钥', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getPayConfigInfo({ channel: activeTab.value })
    const data = res.data || {}
    Object.keys(form).forEach(key => {
      form[key] = data[key] || (key === 'status' ? 1 : key === 'profit_sharing' ? 'off' : '')
    })
    form.channel = activeTab.value
  } catch (e) {
    console.error('加载配置失败', e)
  } finally {
    loading.value = false
  }
}

const handleTabChange = () => {
  form.id = 0
  form.channel = activeTab.value
  loadData()
}

const handleSave = async () => {
  await formRef.value?.validate(async (valid) => {
    if (!valid) return
    saveLoading.value = true
    try {
      await savePayConfig({ ...form })
      ElMessage.success('保存成功')
      loadData()
    } catch (e) {
      ElMessage.error(e.message || '保存失败')
    } finally {
      saveLoading.value = false
    }
  })
}

onMounted(() => loadData())
</script>

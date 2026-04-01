<template>
  <div class="page-container">
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">系统配置</span>
      </div>

      <el-tabs v-model="activeTab" class="config-tabs">
        <el-tab-pane label="基本设置" name="basic">
          <el-form :model="basicConfig" label-width="120px" style="max-width: 600px">
            <el-form-item label="系统名称">
              <el-input v-model="basicConfig.system_name" placeholder="请输入系统名称" />
            </el-form-item>
            <el-form-item label="系统Logo">
              <div class="logo-upload-wrap">
                <el-upload
                  class="logo-uploader"
                  :action="uploadUrl"
                  :headers="uploadHeaders"
                  :show-file-list="false"
                  :on-success="handleLogoSuccess"
                  :on-error="handleLogoError"
                  :before-upload="handleLogoBefore"
                  accept="image/*"
                >
                  <img v-if="basicConfig.system_logo" :src="basicConfig.system_logo" class="logo-preview" />
                  <div v-else class="logo-placeholder">
                    <el-icon class="upload-icon"><UploadFilled /></el-icon>
                    <span>点击上传Logo</span>
                  </div>
                </el-upload>
                <div class="form-tip">支持 JPG、PNG、GIF 格式，建议尺寸 200x60 像素</div>
              </div>
            </el-form-item>
            <el-form-item label="系统描述">
              <el-input v-model="basicConfig.system_description" type="textarea" :rows="3" placeholder="请输入系统描述" />
            </el-form-item>
            <el-form-item label="版权信息">
              <el-input v-model="basicConfig.copyright" placeholder="请输入版权信息" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveBasicConfig" :loading="loading">保存设置</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="用户设置" name="user">
          <el-form :model="userConfig" label-width="120px" style="max-width: 600px">
            <el-form-item label="默认密码">
              <el-input v-model="userConfig.default_password" type="password" placeholder="请输入默认密码" />
              <div class="form-tip">新用户默认密码</div>
            </el-form-item>
            <el-form-item label="密码强度">
              <el-select v-model="userConfig.password_strength" style="width: 200px">
                <el-option label="低" value="low" />
                <el-option label="中" value="medium" />
                <el-option label="高" value="high" />
              </el-select>
            </el-form-item>
            <el-form-item label="登录限制">
              <el-input-number v-model="userConfig.max_login_attempts" :min="1" :max="10" />
              <span style="margin-left: 10px">次后锁定</span>
            </el-form-item>
            <el-form-item label="Token过期">
              <el-input-number v-model="userConfig.token_expire" :min="1" :max="30" />
              <span style="margin-left: 10px">天</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveUserConfig" :loading="loading">保存设置</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="上传设置" name="upload">
          <el-form :model="uploadConfig" label-width="120px" style="max-width: 600px">
            <el-form-item label="允许格式">
              <el-input v-model="uploadConfig.allowed_types" placeholder="如: jpg,png,gif" />
              <div class="form-tip">多个用逗号分隔</div>
            </el-form-item>
            <el-form-item label="文件大小限制">
              <el-input-number v-model="uploadConfig.max_size" :min="1" :max="50" />
              <span style="margin-left: 10px">MB</span>
            </el-form-item>
            <el-form-item label="存储方式">
              <el-radio-group v-model="uploadConfig.storage_type">
                <el-radio label="local">本地存储</el-radio>
                <el-radio label="oss">OSS对象存储</el-radio>
                <el-radio label="cos">腾讯云COS</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveUploadConfig" :loading="loading">保存设置</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="邮件设置" name="email">
          <el-form :model="emailConfig" label-width="120px" style="max-width: 600px">
            <el-form-item label="SMTP服务器">
              <el-input v-model="emailConfig.smtp_host" placeholder="如: smtp.qq.com" />
            </el-form-item>
            <el-form-item label="SMTP端口">
              <el-input-number v-model="emailConfig.smtp_port" :min="1" :max="65535" />
            </el-form-item>
            <el-form-item label="用户名">
              <el-input v-model="emailConfig.smtp_user" placeholder="请输入邮箱账号" />
            </el-form-item>
            <el-form-item label="密码">
              <el-input v-model="emailConfig.smtp_password" type="password" placeholder="请输入邮箱密码或授权码" />
            </el-form-item>
            <el-form-item label="发件人">
              <el-input v-model="emailConfig.from_email" placeholder="如: admin@example.com" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveEmailConfig" :loading="loading">保存设置</el-button>
              <el-button @click="testEmailConfig">发送测试邮件</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { UploadFilled } from '@element-plus/icons-vue'
import request from '@/utils/request'

const activeTab = ref('basic')
const loading = ref(false)
const uploadUrl = '/api/upload/image'
const uploadHeaders = { Authorization: 'Bearer ' + localStorage.getItem('token') }

const handleLogoSuccess = (res) => {
  if (res.code === 0 || res.code === 200) {
    basicConfig.system_logo = res.data?.url || res.url || res.data
    ElMessage.success('Logo上传成功')
  } else {
    ElMessage.error(res.msg || '上传失败')
  }
}

const handleLogoError = () => {
  ElMessage.error('Logo上传失败，请重试')
}

const handleLogoBefore = (file) => {
  const isImage = file.type.startsWith('image/')
  const isLt2M = file.size / 1024 / 1024 < 2
  if (!isImage) {
    ElMessage.error('只能上传图片文件')
    return false
  }
  if (!isLt2M) {
    ElMessage.error('图片大小不能超过 2MB')
    return false
  }
  return true
}

const basicConfig = reactive({
  system_name: '',
  system_logo: '',
  system_description: '',
  copyright: ''
})

const userConfig = reactive({
  default_password: '',
  password_strength: 'medium',
  max_login_attempts: 5,
  token_expire: 7
})

const uploadConfig = reactive({
  allowed_types: '',
  max_size: 10,
  storage_type: 'local'
})

const emailConfig = reactive({
  smtp_host: '',
  smtp_port: 465,
  smtp_user: '',
  smtp_password: '',
  from_email: ''
})

// 加载配置
const loadConfig = async () => {
  try {
    const res = await request.get('/config/lists')
    if (res.code === 0 && res.data) {
      const data = res.data
      // 基本设置
      if (data.system_name) basicConfig.system_name = data.system_name
      if (data.system_logo) basicConfig.system_logo = data.system_logo
      if (data.system_description) basicConfig.system_description = data.system_description
      if (data.copyright) basicConfig.copyright = data.copyright
      // 用户设置
      if (data.default_password) userConfig.default_password = data.default_password
      if (data.password_strength) userConfig.password_strength = data.password_strength
      if (data.max_login_attempts) userConfig.max_login_attempts = parseInt(data.max_login_attempts)
      if (data.token_expire) userConfig.token_expire = parseInt(data.token_expire)
      // 上传设置
      if (data.allowed_types) uploadConfig.allowed_types = data.allowed_types
      if (data.max_size) uploadConfig.max_size = parseInt(data.max_size)
      if (data.storage_type) uploadConfig.storage_type = data.storage_type
      // 邮件设置
      if (data.smtp_host) emailConfig.smtp_host = data.smtp_host
      if (data.smtp_port) emailConfig.smtp_port = parseInt(data.smtp_port)
      if (data.smtp_user) emailConfig.smtp_user = data.smtp_user
      if (data.smtp_password) emailConfig.smtp_password = data.smtp_password
      if (data.from_email) emailConfig.from_email = data.from_email
    }
  } catch (e) {
    console.error('加载配置失败:', e)
  }
}

const saveBasicConfig = async () => {
  loading.value = true
  try {
    await request.post('/config/save', {
      system_name: basicConfig.system_name,
      system_logo: basicConfig.system_logo,
      system_description: basicConfig.system_description,
      copyright: basicConfig.copyright
    })
    ElMessage.success('基本设置已保存')
  } catch (e) {
    ElMessage.error('保存失败')
  } finally {
    loading.value = false
  }
}

const saveUserConfig = async () => {
  loading.value = true
  try {
    await request.post('/config/save', {
      default_password: userConfig.default_password,
      password_strength: userConfig.password_strength,
      max_login_attempts: userConfig.max_login_attempts,
      token_expire: userConfig.token_expire
    })
    ElMessage.success('用户设置已保存')
  } catch (e) {
    ElMessage.error('保存失败')
  } finally {
    loading.value = false
  }
}

const saveUploadConfig = async () => {
  loading.value = true
  try {
    await request.post('/config/save', {
      allowed_types: uploadConfig.allowed_types,
      max_size: uploadConfig.max_size,
      storage_type: uploadConfig.storage_type
    })
    ElMessage.success('上传设置已保存')
  } catch (e) {
    ElMessage.error('保存失败')
  } finally {
    loading.value = false
  }
}

const saveEmailConfig = async () => {
  loading.value = true
  try {
    await request.post('/config/save', {
      smtp_host: emailConfig.smtp_host,
      smtp_port: emailConfig.smtp_port,
      smtp_user: emailConfig.smtp_user,
      smtp_password: emailConfig.smtp_password,
      from_email: emailConfig.from_email
    })
    ElMessage.success('邮件设置已保存')
  } catch (e) {
    ElMessage.error('保存失败')
  } finally {
    loading.value = false
  }
}

const testEmailConfig = () => {
  ElMessage.info('测试邮件发送功能开发中...')
}

onMounted(() => {
  loadConfig()
})
</script>

<style scoped>
.page-container { padding: 0; }

.config-tabs {
  padding: 0 16px;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
.logo-upload-wrap { display: flex; flex-direction: column; gap: 8px; }
.logo-uploader { width: 160px; height: 60px; border: 1px dashed #d9d9d9; border-radius: 6px; overflow: hidden; cursor: pointer; transition: border-color 0.3s; }
.logo-uploader:hover { border-color: #409eff; }
.logo-preview { width: 160px; height: 60px; object-fit: contain; display: block; }
.logo-placeholder { width: 160px; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #909399; font-size: 12px; gap: 4px; }
.logo-placeholder .upload-icon { font-size: 20px; color: #409eff; }
</style>

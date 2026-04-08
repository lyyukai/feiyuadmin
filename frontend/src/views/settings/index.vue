<template>
  <div class="page-container">
    <el-card>
      <template #header>
        <span>系统设置</span>
      </template>
      <el-tabs v-model="activeTab">
        <el-tab-pane label="基本设置" name="basic">
          <el-form label-width="120px">
            <el-form-item label="网站名称">
              <el-input v-model="settings.siteName" />
            </el-form-item>
            <el-form-item label="网站Logo">
              <el-input v-model="settings.siteLogo" />
            </el-form-item>
            <el-form-item label="网站描述">
              <el-input v-model="settings.description" type="textarea" rows="3" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="handleSaveSettings">保存</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="上传设置" name="upload">
          <el-form :model="uploadForm" label-width="140px" class="settings-form">
            <el-form-item label="允许的文件类型">
              <el-input v-model="uploadForm.allow_types" type="textarea" :rows="2" placeholder="如：jpg,png,gif,zip,doc,pdf" />
              <span class="form-tip">多个类型用英文逗号分隔</span>
            </el-form-item>
            <el-form-item label="最大文件大小">
              <el-input-number v-model="uploadForm.max_file_size" :min="1" :max="100" />
              <span class="form-tip">单位：MB</span>
            </el-form-item>
            <el-form-item label="单次最多上传数">
              <el-input-number v-model="uploadForm.max_files" :min="1" :max="20" />
            </el-form-item>
            <el-form-item label="图片最大宽度">
              <el-input-number v-model="uploadForm.image_max_width" :min="100" :max="10000" :step="100" />
              <span class="form-tip">单位：像素，超过自动压缩</span>
            </el-form-item>
            <el-form-item label="图片最大高度">
              <el-input-number v-model="uploadForm.image_max_height" :min="100" :max="10000" :step="100" />
              <span class="form-tip">单位：像素，超过自动压缩</span>
            </el-form-item>
            <el-divider content-position="left">📦 对象存储配置</el-divider>
            <el-form-item label="存储驱动">
              <el-radio-group v-model="uploadForm.driver">
                <el-radio label="local">本地存储</el-radio>
                <el-radio label="oss">阿里云OSS</el-radio>
                <el-radio label="cos">腾讯云COS</el-radio>
                <el-radio label="qiniu">七牛云</el-radio>
              </el-radio-group>
            </el-form-item>

            <template v-if="uploadForm.driver === 'oss'">
              <el-alert type="info" :closable="false" style="margin-bottom:16px">请前往 <a href="https://oss.console.aliyun.com/" target="_blank">阿里云OSS控制台</a> 创建 Bucket 并获取 AccessKey</el-alert>
              <el-form-item label="Bucket 名称"><el-input v-model="uploadForm.oss_bucket" placeholder="例如：my-app-assets" /></el-form-item>
              <el-form-item label="AccessKey ID"><el-input v-model="uploadForm.oss_access_key_id" placeholder="AccessKey ID" /></el-form-item>
              <el-form-item label="AccessKey Secret"><el-input v-model="uploadForm.oss_access_key_secret" placeholder="AccessKey Secret" show-password /></el-form-item>
              <el-form-item label="OSS 地域节点"><el-input v-model="uploadForm.oss_region" placeholder="例如：oss-cn-hangzhou" /></el-form-item>
              <el-form-item label="CDN 加速域名"><el-input v-model="uploadForm.oss_domain" placeholder="选填" /></el-form-item>
              <el-form-item label="默认路径"><el-input v-model="uploadForm.oss_path" placeholder="选填" /></el-form-item>
            </template>

            <template v-if="uploadForm.driver === 'cos'">
              <el-alert type="info" :closable="false" style="margin-bottom:16px">请前往 <a href="https://console.cloud.tencent.com/cos5" target="_blank">腾讯云COS控制台</a> 创建 Bucket</el-alert>
              <el-form-item label="Bucket 名称"><el-input v-model="uploadForm.cos_bucket" placeholder="例如：my-app-1250000000" /></el-form-item>
              <el-form-item label="SecretId"><el-input v-model="uploadForm.cos_secret_id" placeholder="SecretId" /></el-form-item>
              <el-form-item label="SecretKey"><el-input v-model="uploadForm.cos_secret_key" placeholder="SecretKey" show-password /></el-form-item>
              <el-form-item label="COS 地域节点"><el-input v-model="uploadForm.cos_region" placeholder="例如：ap-guangzhou" /></el-form-item>
              <el-form-item label="CDN 加速域名"><el-input v-model="uploadForm.cos_domain" placeholder="选填" /></el-form-item>
            </template>

            <template v-if="uploadForm.driver === 'qiniu'">
              <el-alert type="info" :closable="false" style="margin-bottom:16px">请前往 <a href="https://portal.qiniu.com/" target="_blank">七牛云控制台</a> 获取 AccessKey/SecretKey</el-alert>
              <el-form-item label="Bucket 名称"><el-input v-model="uploadForm.qiniu_bucket" placeholder="七牛云空间名称" /></el-form-item>
              <el-form-item label="AccessKey"><el-input v-model="uploadForm.qiniu_access_key" placeholder="AccessKey" /></el-form-item>
              <el-form-item label="SecretKey"><el-input v-model="uploadForm.qiniu_secret_key" placeholder="SecretKey" show-password /></el-form-item>
              <el-form-item label="访问域名"><el-input v-model="uploadForm.qiniu_domain" placeholder="必须已绑定七牛空间" /></el-form-item>
            </template>

            <template v-if="uploadForm.driver === 'local'">
              <el-alert type="success" :closable="false" style="margin-bottom:16px">本地存储已启用，无需额外配置</el-alert>
            </template>

            <el-form-item>
              <el-button type="primary" :loading="saving" @click="saveConfig('upload', uploadForm)">保存配置</el-button>
              <el-button v-if="uploadForm.driver !== 'local'" @click="testStorage" :loading="testingStorage">测试连接</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="安全设置" name="security">
          <el-form label-width="120px">
            <el-form-item label="修改密码">
              <el-button type="primary" @click="passwordDialogVisible = true">修改密码</el-button>
            </el-form-item>
            <el-form-item label="两步验证">
              <el-switch />
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </el-card>

    <!-- 修改密码弹窗 -->
    <el-dialog v-model="passwordDialogVisible" title="修改密码" width="460px" draggable>
      <el-form ref="pwdFormRef" :model="pwdForm" :rules="pwdRules" label-width="110px">
        <el-form-item label="旧密码" prop="oldPassword">
          <el-input
            v-model="pwdForm.oldPassword"
            type="password"
            placeholder="请输入旧密码"
            show-password
          />
        </el-form-item>
        <el-form-item label="新密码" prop="newPassword">
          <el-input
            v-model="pwdForm.newPassword"
            type="password"
            placeholder="请输入新密码"
            show-password
          />
        </el-form-item>
        <el-form-item label="确认密码" prop="confirmPassword">
          <el-input
            v-model="pwdForm.confirmPassword"
            type="password"
            placeholder="请再次输入新密码"
            show-password
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="passwordDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="pwdLoading" @click="handleChangePassword">确认修改</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import request from '@/utils/request'

const activeTab = ref('basic')
const passwordDialogVisible = ref(false)
const pwdLoading = ref(false)
const pwdFormRef = ref(null)

const settings = reactive({
  siteName: '飞鱼后台管理系统',
  siteLogo: '',
  description: '一个简洁高效的后台管理系统'
})

// 上传配置
const saving = ref(false)
const testingStorage = ref(false)
const uploadForm = reactive({
  allow_types: 'jpg,jpeg,png,gif,zip,rar,doc,docx,xls,xlsx,pdf',
  max_file_size: 10,
  max_files: 10,
  image_max_width: 1920,
  image_max_height: 1080,
  driver: 'local',
  // 阿里云OSS
  oss_bucket: '',
  oss_access_key_id: '',
  oss_access_key_secret: '',
  oss_region: '',
  oss_domain: '',
  oss_path: '',
  // 腾讯云COS
  cos_bucket: '',
  cos_secret_id: '',
  cos_secret_key: '',
  cos_region: '',
  cos_domain: '',
  // 七牛云
  qiniu_bucket: '',
  qiniu_access_key: '',
  qiniu_secret_key: '',
  qiniu_domain: ''
})

const saveConfig = async (group, form) => {
  saving.value = true
  try {
    const res = await request.post('/adminapi/system_config/save', { group, config: form })
    if (res.code === 0) {
      ElMessage.success('保存成功')
    } else {
      ElMessage.error('保存失败：' + (res.msg || '未知错误'))
    }
  } catch (e) {
    ElMessage.error('保存异常：' + (e.message || '网络错误'))
  } finally {
    saving.value = false
  }
}

const testStorage = async () => {
  testingStorage.value = true
  try {
    const res = await request.post('/adminapi/system_config/testStorage', uploadForm)
    if (res.code === 0) {
      ElMessage.success('连接测试成功！')
    } else {
      ElMessage.error('连接失败：' + (res.msg || '未知错误'))
    }
  } catch (e) {
    ElMessage.error('连接异常：' + (e.message || '网络错误'))
  } finally {
    testingStorage.value = false
  }
}

const pwdForm = reactive({
  oldPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const validateConfirmPassword = (rule, value, callback) => {
  if (value !== pwdForm.newPassword) {
    callback(new Error('两次输入的密码不一致'))
  } else {
    callback()
  }
}

const pwdRules = {
  oldPassword: [
    { required: true, message: '请输入旧密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能少于6位', trigger: 'blur' }
  ],
  newPassword: [
    { required: true, message: '请输入新密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能少于6位', trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: '请再次输入新密码', trigger: 'blur' },
    { validator: validateConfirmPassword, trigger: 'blur' }
  ]
}

const handleSaveSettings = () => {
  ElMessage.success('设置保存成功')
}

const handleChangePassword = async () => {
  if (!pwdFormRef.value) return
  await pwdFormRef.value.validate(async (valid) => {
    if (!valid) return
    pwdLoading.value = true
    try {
      await request.post('/user/password', {
        oldPassword: pwdForm.oldPassword,
        newPassword: pwdForm.newPassword
      })
      ElMessage.success('密码修改成功')
      passwordDialogVisible.value = false
      pwdForm.oldPassword = ''
      pwdForm.newPassword = ''
      pwdForm.confirmPassword = ''
    } catch {
      // error handled by interceptor
    } finally {
      pwdLoading.value = false
    }
  })
}
</script>

<style scoped>
</style>

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
  siteName: '飞羽后台管理系统',
  siteLogo: '',
  description: '一个简洁高效的后台管理系统'
})

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

<template>
  <div class="page-container">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>个人中心</span>
          <el-button type="primary" size="small" @click="handleEditProfile">
            <el-icon><Edit /></el-icon> 编辑资料
          </el-button>
        </div>
      </template>
      <div v-loading="loading">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="用户名">{{ form.username }}</el-descriptions-item>
          <el-descriptions-item label="昵称">{{ form.nickname || '-' }}</el-descriptions-item>
          <el-descriptions-item label="邮箱">{{ form.email || '-' }}</el-descriptions-item>
          <el-descriptions-item label="手机号">{{ form.phone || '-' }}</el-descriptions-item>
          <el-descriptions-item label="部门">{{ form.dept_name || '-' }}</el-descriptions-item>
          <el-descriptions-item label="岗位">{{ form.post_name || '-' }}</el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="form.status === 1 ? 'success' : 'danger'">
              {{ form.status === 1 ? '正常' : '禁用' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="创建时间">{{ form.create_time || '-' }}</el-descriptions-item>
        </el-descriptions>
      </div>
    </el-card>

    <!-- 编辑资料弹窗 -->
    <el-dialog v-model="editDialogVisible" title="编辑资料" width="500px" draggable>
      <el-form ref="editFormRef" :model="editForm" :rules="editRules" label-width="100px">
        <el-form-item label="用户名" prop="username">
          <el-input v-model="editForm.username" disabled />
        </el-form-item>
        <el-form-item label="昵称" prop="nickname">
          <el-input v-model="editForm.nickname" placeholder="请输入昵称" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="editForm.email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-form-item label="手机号" prop="phone">
          <el-input v-model="editForm.phone" placeholder="请输入手机号" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSave">保存</el-button>
      </template>
    </el-dialog>

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
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Edit } from '@element-plus/icons-vue'
import { getUserInfo, editUser } from '@/api'
import request from '@/utils/request'

const loading = ref(false)
const submitLoading = ref(false)
const pwdLoading = ref(false)
const editDialogVisible = ref(false)
const passwordDialogVisible = ref(false)
const editFormRef = ref(null)
const pwdFormRef = ref(null)

const form = reactive({
  id: null,
  username: '',
  nickname: '',
  email: '',
  phone: '',
  dept_name: '',
  post_name: '',
  status: 1,
  create_time: ''
})

const editForm = reactive({
  username: '',
  nickname: '',
  email: '',
  phone: ''
})

const editRules = {
  nickname: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
  email: [
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ],
  phone: [
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号', trigger: 'blur' }
  ]
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

// 加载用户信息
const loadUserInfo = async () => {
  loading.value = true
  try {
    const res = await getUserInfo()
    const data = res.data || {}
    Object.assign(form, {
      id: data.id,
      username: data.username || '',
      nickname: data.nickname || '',
      email: data.email || '',
      phone: data.phone || '',
      dept_name: data.dept_name || '',
      post_name: data.post_name || '',
      status: data.status ?? 1,
      create_time: data.create_time || ''
    })
  } catch {
    ElMessage.error('获取用户信息失败')
  } finally {
    loading.value = false
  }
}

// 打开编辑弹窗
const handleEditProfile = () => {
  Object.assign(editForm, {
    username: form.username,
    nickname: form.nickname,
    email: form.email,
    phone: form.phone
  })
  editDialogVisible.value = true
}

// 保存资料
const handleSave = async () => {
  if (!editFormRef.value) return
  await editFormRef.value.validate(async (valid) => {
    if (!valid) return
    submitLoading.value = true
    try {
      await editUser({
        id: form.id,
        nickname: editForm.nickname,
        email: editForm.email,
        phone: editForm.phone
      })
      ElMessage.success('资料保存成功')
      editDialogVisible.value = false
      loadUserInfo()
    } catch {
      ElMessage.error('保存失败，请重试')
    } finally {
      submitLoading.value = false
    }
  })
}

// 修改密码
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
      ElMessage.success('密码修改成功，请重新登录')
      passwordDialogVisible.value = false
      pwdForm.oldPassword = ''
      pwdForm.newPassword = ''
      pwdForm.confirmPassword = ''
    } catch (e) {
      // error already shown by interceptor
    } finally {
      pwdLoading.value = false
    }
  })
}

// 暴露给外部调用
defineExpose({ openPasswordDialog: () => { passwordDialogVisible.value = true } })

onMounted(() => loadUserInfo())
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>

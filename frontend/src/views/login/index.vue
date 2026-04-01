<template>
  <div class="login-container">
    <!-- 背景粒子动画 -->
    <div class="bg-particles">
      <div class="particle p1"></div>
      <div class="particle p2"></div>
      <div class="particle p3"></div>
      <div class="particle p4"></div>
      <div class="particle p5"></div>
    </div>

    <div class="login-left">
      <div class="brand">
        <div class="brand-logo">
          <div class="logo-icon">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
              <defs>
                <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#ffffff" stop-opacity="0.9"/>
                  <stop offset="100%" stop-color="#ffffff" stop-opacity="0.6"/>
                </linearGradient>
              </defs>
              <rect width="64" height="64" rx="16" fill="rgba(255,255,255,0.15)"/>
              <path d="M18 32L32 18L46 32L32 46L18 32Z" fill="url(#logoGrad)"/>
              <circle cx="32" cy="32" r="8" fill="rgba(255,255,255,0.3)"/>
              <circle cx="32" cy="32" r="4" fill="#ffffff"/>
            </svg>
          </div>
        </div>
        <h1 class="brand-name">飞羽后台管理系统</h1>
        <p class="brand-slogan">安全 · 高效 · 稳定</p>
      </div>

      <div class="features">
        <div class="feature-item">
          <div class="feature-icon"><el-icon><Key /></el-icon></div>
          <div class="feature-text">
            <h4>安全可靠</h4>
            <p>多重防护，数据安全</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><el-icon><Connection /></el-icon></div>
          <div class="feature-text">
            <h4>高效处理</h4>
            <p>秒级响应，流畅体验</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><el-icon><DataAnalysis /></el-icon></div>
          <div class="feature-text">
            <h4>数据可视化</h4>
            <p>数据驱动，决策明智</p>
          </div>
        </div>
      </div>
    </div>

    <div class="login-right">
      <div class="login-box">
        <div class="login-header">
          <h2>欢迎回来</h2>
          <p>请登录您的账号继续使用</p>
        </div>

        <el-form
          ref="loginFormRef"
          :model="loginForm"
          :rules="loginRules"
          class="login-form"
          size="large"
          @keyup.enter="handleLogin"
        >
          <el-form-item prop="username">
            <div class="input-wrapper">
              <el-icon class="input-icon"><User /></el-icon>
              <el-input
                v-model="loginForm.username"
                placeholder="请输入用户名 / 手机号"
                clearable
                autofocus
              />
            </div>
          </el-form-item>

          <el-form-item prop="password">
            <div class="input-wrapper">
              <el-icon class="input-icon"><Lock /></el-icon>
              <el-input
                v-model="loginForm.password"
                type="password"
                placeholder="请输入密码"
                show-password
                clearable
              />
            </div>
          </el-form-item>

          <div class="form-options">
            <el-checkbox v-model="loginForm.remember">记住密码</el-checkbox>
            <el-link type="primary" :underline="false">忘记密码？</el-link>
          </div>

          <el-button
            type="primary"
            class="login-btn"
            :loading="loading"
            @click="handleLogin"
          >
            <span v-if="!loading">登 录</span>
            <span v-else>登录中...</span>
          </el-button>
        </el-form>

        <div class="login-footer">
          <p>默认账号：<span>admin</span> / <span>admin123</span></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { User, Lock, Key, Connection, DataAnalysis } from '@element-plus/icons-vue'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const userStore = useUserStore()

const loginFormRef = ref(null)
const loading = ref(false)

const loginForm = reactive({
  username: 'admin',
  password: 'admin123',
  remember: false
})

const loginRules = {
  username: [
    { required: true, message: '请输入用户名', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能少于6位', trigger: 'blur' }
  ]
}

const handleLogin = async () => {
  if (!loginFormRef.value) return

  try {
    await loginFormRef.value.validate()
  } catch {
    return
  }

  loading.value = true

  try {
    const success = await userStore.login(loginForm)

    if (success) {
      // 登录成功后获取用户信息和菜单
      await userStore.getUserInfo()
      await userStore.getMenus()

      ElMessage.success('登录成功')
      router.push('/')
    }
  } catch (err) {
    ElMessage.error(err.message || '登录失败，请检查账号密码')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  background: linear-gradient(135deg, #0F172A 0%, #1D4ED8 100%);
  position: relative;
  overflow: hidden;
}

/* 粒子背景 */
.bg-particles {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.particle {
  position: absolute;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(37, 99, 235, 0.05));
  animation: float 20s infinite ease-in-out;
}

.p1 { width: 400px; height: 400px; top: -150px; right: -100px; animation-delay: 0s; }
.p2 { width: 300px; height: 300px; bottom: -100px; left: -80px; animation-delay: -5s; }
.p3 { width: 200px; height: 200px; top: 50%; left: 20%; animation-delay: -10s; }
.p4 { width: 150px; height: 150px; top: 20%; right: 30%; animation-delay: -15s; }
.p5 { width: 100px; height: 100px; bottom: 30%; right: 10%; animation-delay: -7s; }

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(20px, -20px) scale(1.05); }
  50% { transform: translate(-10px, 20px) scale(0.95); }
  75% { transform: translate(-20px, -10px) scale(1.02); }
}

/* 左侧品牌区 */
.login-left {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  position: relative;
  z-index: 1;
}

.brand {
  text-align: center;
  margin-bottom: 60px;
}

.brand-logo {
  margin-bottom: 32px;
}

.logo-icon {
  display: flex;
  justify-content: center;
}

.brand-name {
  font-size: 36px;
  font-weight: 600;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: 3px;
}

.brand-slogan {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  letter-spacing: 8px;
}

.features {
  display: flex;
  gap: 40px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.feature-item:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-4px);
}

.feature-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(37, 99, 235, 0.2);
  border-radius: 10px;
  color: #2563EB;
  font-size: 20px;
}

.feature-text h4 {
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  margin: 0 0 4px;
}

.feature-text p {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  margin: 0;
}

/* 右侧登录区 */
.login-right {
  width: 520px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  position: relative;
  z-index: 1;
}

.login-box {
  width: 360px;
  padding: 48px 40px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 24px;
  box-shadow:
    0 25px 50px -12px rgba(0, 0, 0, 0.5),
    0 0 0 1px rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
}

.login-header {
  text-align: center;
  margin-bottom: 40px;
}

.login-header h2 {
  font-size: 28px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 8px;
}

.login-header p {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.login-form {
  margin-top: 0;
}

.input-wrapper {
  position: relative;
  width: 100%;
}

.input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  z-index: 1;
  font-size: 18px;
}

:deep(.el-input__wrapper) {
  padding-left: 44px;
  padding-right: 14px;
  height: 48px;
  border-radius: 12px;
  box-shadow: 0 0 0 1px #e5e7eb inset;
  background: #f9fafb;
  transition: all 0.3s ease;
}

:deep(.el-input__wrapper:hover) {
  box-shadow: 0 0 0 1px #2563EB inset;
}

:deep(.el-input__wrapper.is-focus) {
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) inset, 0 0 0 1px #2563EB inset;
  background: #fff;
}

:deep(.el-input__inner) {
  font-size: 15px;
  color: #1f2937;
}

:deep(.el-input__inner::placeholder) {
  color: #9ca3af;
}

:deep(.el-form-item) {
  margin-bottom: 24px;
}

:deep(.el-form-item__error) {
  padding-top: 4px;
  font-size: 12px;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

:deep(.el-checkbox__label) {
  color: #6b7280;
  font-size: 13px;
}

.login-btn {
  width: 100%;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 12px;
  background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
  border: none;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
  transition: all 0.3s ease;
  letter-spacing: 4px;
}

.login-btn:hover {
  background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
  transform: translateY(-2px);
}

.login-btn:active {
  transform: translateY(0);
}

.login-footer {
  margin-top: 32px;
  text-align: center;
  padding-top: 24px;
  border-top: 1px solid #f0f0f0;
}

.login-footer p {
  font-size: 12px;
  color: #9ca3af;
  margin: 0;
}

.login-footer span {
  color: #2563EB;
  font-weight: 500;
}

/* 响应式 */
@media (max-width: 1024px) {
  .login-left {
    display: none;
  }

  .login-right {
    width: 100%;
  }

  .login-box {
    width: 100%;
    max-width: 420px;
  }
}

@media (max-width: 480px) {
  .login-box {
    padding: 32px 24px;
    border-radius: 16px;
  }

  .login-header h2 {
    font-size: 24px;
  }
}
</style>

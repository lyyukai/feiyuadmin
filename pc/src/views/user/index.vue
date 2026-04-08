<template>
  <div class="user-center">
    <!-- 顶部导航 -->
    <header class="uc-header">
      <div class="header-inner">
        <div class="logo" @click="$router.push('/')">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼</span>
        </div>
        <div class="header-search">
          <el-input placeholder="搜索商品" prefix-icon="Search" v-model="keyword" />
        </div>
        <div class="header-right">
          <a href="/" class="header-link">首页</a>
          <a href="/article" class="header-link">文章</a>
        </div>
      </div>
    </header>

    <!-- 未登录状态 -->
    <div v-if="!isLoggedIn" class="login-prompt">
      <div class="login-box">
        <h2>登录后可查看您的专属信息</h2>
        <p>登录后享受更多专属权益</p>
        <el-button type="primary" size="large" @click="showLoginDialog = true">立即登录</el-button>
      </div>
    </div>

    <!-- 已登录 -->
    <div v-else class="uc-main">
      <!-- 左侧菜单 -->
      <aside class="uc-sidebar">
        <!-- 用户信息卡片 -->
        <div class="user-card">
          <el-avatar :size="64" :src="userInfo.avatar">
            <el-icon :size="32"><User /></el-icon>
          </el-avatar>
          <div class="user-name">{{ userInfo.nickname || userInfo.username }}</div>
          <div class="user-level">
            <el-tag :type="levelTagType" size="small">{{ userInfo.level_name || '普通会员' }}</el-tag>
          </div>
          <div class="user-stats">
            <div class="stat-item">
              <span class="stat-num">{{ userInfo.balance || 0 }}</span>
              <span class="stat-label">余额</span>
            </div>
            <div class="stat-item">
              <span class="stat-num">{{ userInfo.points || 0 }}</span>
              <span class="stat-label">积分</span>
            </div>
          </div>
        </div>

        <!-- 菜单列表 -->
        <el-menu :default-active="activeMenu" @select="handleMenuSelect" class="uc-menu">
          <el-menu-item index="profile">
            <el-icon><User /></el-icon>
            <span>个人资料</span>
          </el-menu-item>
          <el-menu-item index="password">
            <el-icon><Lock /></el-icon>
            <span>修改密码</span>
          </el-menu-item>
          <el-menu-item index="orders">
            <el-icon><ShoppingCart /></el-icon>
            <span>我的订单</span>
          </el-menu-item>
          <el-menu-item index="favorites">
            <el-icon><Star /></el-icon>
            <span>我的收藏</span>
          </el-menu-item>
          <el-menu-item index="feedback">
            <el-icon><ChatLineRound /></el-icon>
            <span>意见反馈</span>
          </el-menu-item>
        </el-menu>
      </aside>

      <!-- 右侧内容区 -->
      <main class="uc-content">
        <!-- 个人资料 -->
        <div v-show="activeMenu === 'profile'" class="content-panel">
          <div class="panel-header">
            <h3>个人资料</h3>
          </div>
          <div class="panel-body">
            <el-form :model="profileForm" label-width="100px" class="profile-form">
              <el-form-item label="头像">
                <div class="avatar-upload">
                  <el-avatar :size="80" :src="profileForm.avatar">
                    <el-icon :size="32"><User /></el-icon>
                  </el-avatar>
                  <el-button size="small" style="margin-left:12px" @click="uploadAvatar">更换头像</el-button>
                </div>
              </el-form-item>
              <el-form-item label="昵称">
                <el-input v-model="profileForm.nickname" maxlength="20" show-word-limit />
              </el-form-item>
              <el-form-item label="手机号">
                <el-input v-model="profileForm.mobile" disabled />
                <span class="form-tip">手机号不可修改</span>
              </el-form-item>
              <el-form-item label="邮箱">
                <el-input v-model="profileForm.email" />
              </el-form-item>
              <el-form-item label="性别">
                <el-radio-group v-model="profileForm.gender">
                  <el-radio :label="1">男</el-radio>
                  <el-radio :label="2">女</el-radio>
                  <el-radio :label="0">保密</el-radio>
                </el-radio-group>
              </el-form-item>
              <el-form-item label="个人简介">
                <el-input v-model="profileForm.bio" type="textarea" :rows="3" maxlength="200" show-word-limit />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" :loading="profileLoading" @click="saveProfile">保存修改</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>

        <!-- 修改密码 -->
        <div v-show="activeMenu === 'password'" class="content-panel">
          <div class="panel-header">
            <h3>修改密码</h3>
          </div>
          <div class="panel-body">
            <el-form ref="pwdFormRef" :model="pwdForm" :rules="pwdRules" label-width="120px" class="pwd-form">
              <el-form-item label="旧密码" prop="oldPassword">
                <el-input v-model="pwdForm.oldPassword" type="password" show-password placeholder="请输入旧密码" />
              </el-form-item>
              <el-form-item label="新密码" prop="newPassword">
                <el-input v-model="pwdForm.newPassword" type="password" show-password placeholder="请输入新密码" />
              </el-form-item>
              <el-form-item label="确认密码" prop="confirmPassword">
                <el-input v-model="pwdForm.confirmPassword" type="password" show-password placeholder="请再次输入新密码" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" :loading="pwdLoading" @click="changePassword">确认修改</el-button>
                <el-button @click="resetPwdForm">重置</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>

        <!-- 我的订单 -->
        <div v-show="activeMenu === 'orders'" class="content-panel">
          <div class="panel-header">
            <h3>我的订单</h3>
            <div class="order-tabs">
              <span v-for="tab in orderTabs" :key="tab.value"
                :class="['order-tab', { active: orderStatus === tab.value }]"
                @click="orderStatus = tab.value; loadOrders()">{{ tab.label }}</span>
            </div>
          </div>
          <div class="panel-body">
            <div v-if="orders.length === 0" class="empty-state">
              <el-empty description="暂无订单" />
            </div>
            <div v-else class="order-list">
              <div v-for="order in orders" :key="order.id" class="order-item">
                <div class="order-header">
                  <span class="order-no">订单号：{{ order.order_no }}</span>
                  <span class="order-time">{{ order.create_time }}</span>
                  <span :class="['order-status', 'status-' + order.status]">{{ order.status_text }}</span>
                </div>
                <div class="order-body">
                  <div v-for="goods in order.goods" :key="goods.id" class="order-goods">
                    <el-image :src="goods.image" fit="cover" class="goods-img" />
                    <div class="goods-info">
                      <div class="goods-name">{{ goods.name }}</div>
                      <div class="goods-price">¥{{ goods.price }} × {{ goods.num }}</div>
                    </div>
                  </div>
                </div>
                <div class="order-footer">
                  <span class="order-total">合计：<strong>¥{{ order.total_price }}</strong></span>
                  <el-button size="small" @click="viewOrder(order.id)">查看详情</el-button>
                </div>
              </div>
            </div>
            <div v-if="orders.length > 0" class="pagination-wrap">
              <el-pagination
                background layout="prev, pager, next"
                :total="orderTotal"
                :page-size="orderPageSize"
                v-model:current-page="orderPage"
                @current-change="loadOrders"
              />
            </div>
          </div>
        </div>

        <!-- 我的收藏 -->
        <div v-show="activeMenu === 'favorites'" class="content-panel">
          <div class="panel-header">
            <h3>我的收藏</h3>
          </div>
          <div class="panel-body">
            <div v-if="favorites.length === 0" class="empty-state">
              <el-empty description="暂无收藏" />
            </div>
            <div v-else class="goods-grid">
              <div v-for="item in favorites" :key="item.id" class="goods-card">
                <el-image :src="item.image" fit="cover" class="goods-img" @click="$router.push('/goods/' + item.id)" />
                <div class="goods-info">
                  <div class="goods-name" @click="$router.push('/goods/' + item.id)">{{ item.name }}</div>
                  <div class="goods-price-row">
                    <span class="goods-price">¥{{ item.price }}</span>
                    <el-button type="danger" size="small" text @click="cancelFavorite(item.id)">取消收藏</el-button>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="favorites.length > 0" class="pagination-wrap">
              <el-pagination
                background layout="prev, pager, next"
                :total="favTotal"
                :page-size="favPageSize"
                v-model:current-page="favPage"
                @current-change="loadFavorites"
              />
            </div>
          </div>
        </div>

        <!-- 意见反馈 -->
        <div v-show="activeMenu === 'feedback'" class="content-panel">
          <div class="panel-header">
            <h3>意见反馈</h3>
          </div>
          <div class="panel-body">
            <el-form :model="feedbackForm" label-width="100px" class="feedback-form">
              <el-form-item label="反馈类型">
                <el-select v-model="feedbackForm.type" style="width:100%">
                  <el-option label="功能建议" value="suggest" />
                  <el-option label="系统问题" value="bug" />
                  <el-option label="内容纠错" value="error" />
                  <el-option label="其他" value="other" />
                </el-select>
              </el-form-item>
              <el-form-item label="反馈内容">
                <el-input v-model="feedbackForm.content" type="textarea" :rows="6"
                  maxlength="500" show-word-limit placeholder="请详细描述您的问题或建议..." />
              </el-form-item>
              <el-form-item label="联系方式">
                <el-input v-model="feedbackForm.contact" placeholder="手机号或邮箱" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" :loading="feedbackLoading" @click="submitFeedback">提交反馈</el-button>
                <el-button @click="feedbackForm.content = ''; feedbackForm.type = 'suggest'; feedbackForm.contact = ''">重置</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </main>
    </div>

    <!-- 底部导航栏 -->
    <nav class="bottom-nav">
      <div class="nav-item" @click="$router.push('/')">
        <el-icon :size="22"><House /></el-icon>
        <span>首页</span>
      </div>
      <div class="nav-item" @click="$router.push('/category')">
        <el-icon :size="22"><Grid /></el-icon>
        <span>分类</span>
      </div>
      <div class="nav-item" @click="$router.push('/cart')">
        <el-icon :size="22"><ShoppingCart /></el-icon>
        <span>购物车</span>
        <em v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</em>
      </div>
      <div class="nav-item active" @click="$router.push('/user')">
        <el-icon :size="22"><User /></el-icon>
        <span>我的</span>
      </div>
    </nav>

    <!-- 登录弹窗 -->
    <el-dialog v-model="showLoginDialog" title="登录" width="400px" center>
      <el-form :model="loginForm" label-width="60px">
        <el-form-item label="账号">
          <el-input v-model="loginForm.username" placeholder="请输入账号" />
        </el-form-item>
        <el-form-item label="密码">
          <el-input v-model="loginForm.password" type="password" show-password placeholder="请输入密码" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showLoginDialog = false">取消</el-button>
        <el-button type="primary" :loading="loginLoading" @click="handleLogin">登录</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { User, Lock, ShoppingCart, Star, ChatLineRound, House, Grid } from '@element-plus/icons-vue'
import request from '@/utils/request'

const isLoggedIn = ref(false)
const showLoginDialog = ref(false)
const loginLoading = ref(false)
const profileLoading = ref(false)
const pwdLoading = ref(false)
const feedbackLoading = ref(false)
const keyword = ref('')
const cartCount = ref(0)

const userInfo = reactive({
  id: null,
  username: '',
  nickname: '',
  avatar: '',
  mobile: '',
  email: '',
  gender: 0,
  bio: '',
  level_name: '普通会员',
  balance: 0,
  points: 0
})

const profileForm = reactive({
  nickname: '',
  avatar: '',
  mobile: '',
  email: '',
  gender: 0,
  bio: ''
})

const pwdFormRef = ref(null)
const pwdForm = reactive({ oldPassword: '', newPassword: '', confirmPassword: '' })
const validateConfirm = (rule, value, callback) => {
  if (value !== pwdForm.newPassword) callback(new Error('两次密码不一致'))
  else callback()
}
const pwdRules = {
  oldPassword: [{ required: true, message: '请输入旧密码', trigger: 'blur' }],
  newPassword: [{ required: true, message: '请输入新密码', trigger: 'blur' }, { min: 6, message: '密码至少6位', trigger: 'blur' }],
  confirmPassword: [{ required: true, message: '请确认密码', trigger: 'blur' }, { validator: validateConfirm, trigger: 'blur' }]
}

const loginForm = reactive({ username: '', password: '' })

const activeMenu = ref('profile')

// 订单
const orderTabs = [
  { label: '全部', value: '' },
  { label: '待支付', value: 'pending' },
  { label: '待发货', value: 'paid' },
  { label: '待收货', value: 'shipped' },
  { label: '已完成', value: 'completed' }
]
const orderStatus = ref('')
const orders = ref([])
const orderPage = ref(1)
const orderPageSize = ref(10)
const orderTotal = ref(0)

// 收藏
const favorites = ref([])
const favPage = ref(1)
const favPageSize = ref(12)
const favTotal = ref(0)

// 反馈
const feedbackForm = reactive({ type: 'suggest', content: '', contact: '' })

const levelTagType = computed(() => {
  const name = userInfo.level_name || ''
  if (name.includes('钻石')) return 'danger'
  if (name.includes('金牌')) return 'warning'
  if (name.includes('银牌')) return 'info'
  return ''
})

const checkLogin = () => {
  const token = localStorage.getItem('token')
  if (token) {
    isLoggedIn.value = true
    loadUserInfo()
  }
}

const loadUserInfo = async () => {
  try {
    const res = await request.get('/user/info')
    Object.assign(userInfo, res.data || {})
    Object.assign(profileForm, res.data || {})
  } catch {}
}

const handleLogin = async () => {
  if (!loginForm.username || !loginForm.password) {
    ElMessage.warning('请输入账号和密码')
    return
  }
  loginLoading.value = true
  try {
    const res = await request.post('/login', loginForm)
    localStorage.setItem('token', res.data?.token || '')
    ElMessage.success('登录成功')
    showLoginDialog.value = false
    isLoggedIn.value = true
    loadUserInfo()
  } catch {} finally {
    loginLoading.value = false
  }
}

const saveProfile = async () => {
  profileLoading.value = true
  try {
    await request.post('/user/update', profileForm)
    ElMessage.success('保存成功')
    loadUserInfo()
  } catch {} finally {
    profileLoading.value = false
  }
}

const changePassword = async () => {
  if (!pwdFormRef.value) return
  await pwdFormRef.value.validate(async (valid) => {
    if (!valid) return
    pwdLoading.value = true
    try {
      await request.post('/user/changePassword', pwdForm)
      ElMessage.success('密码修改成功')
      resetPwdForm()
    } catch {} finally {
      pwdLoading.value = false
    }
  })
}

const resetPwdForm = () => {
  pwdForm.oldPassword = ''
  pwdForm.newPassword = ''
  pwdForm.confirmPassword = ''
}

const loadOrders = async () => {
  try {
    const res = await request.get('/user/orders', {
      params: { status: orderStatus.value, page: orderPage.value, page_size: orderPageSize.value }
    })
    orders.value = res.data?.list || []
    orderTotal.value = res.data?.total || 0
  } catch {}
}

const viewOrder = (id) => ElMessage.info('订单详情：' + id)

const cancelFavorite = async (id) => {
  try {
    await request.post('/user/favorite/cancel', { id })
    ElMessage.success('已取消收藏')
    loadFavorites()
  } catch {}
}

const loadFavorites = async () => {
  try {
    const res = await request.get('/user/favorites', {
      params: { page: favPage.value, page_size: favPageSize.value }
    })
    favorites.value = res.data?.list || []
    favTotal.value = res.data?.total || 0
  } catch {}
}

const submitFeedback = async () => {
  if (!feedbackForm.content.trim()) {
    ElMessage.warning('请输入反馈内容')
    return
  }
  feedbackLoading.value = true
  try {
    await request.post('/user/feedback', feedbackForm)
    ElMessage.success('反馈已提交，感谢您的建议')
    feedbackForm.content = ''
    feedbackForm.type = 'suggest'
    feedbackForm.contact = ''
  } catch {} finally {
    feedbackLoading.value = false
  }
}

const uploadAvatar = () => ElMessage.info('头像上传功能')

const handleMenuSelect = (key) => {
  activeMenu.value = key
  if (key === 'orders') loadOrders()
  if (key === 'favorites') loadFavorites()
}

onMounted(() => {
  checkLogin()
  cartCount.value = parseInt(localStorage.getItem('cartCount') || '0')
})
</script>

<style scoped>
.user-center {
  min-height: 100vh;
  background: #f5f6f8;
  padding-bottom: 70px;
}

/* 顶部导航 */
.uc-header {
  background: #fff;
  border-bottom: 1px solid #ebeef5;
  position: sticky;
  top: 0;
  z-index: 100;
}
.header-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  height: 60px;
  display: flex;
  align-items: center;
  gap: 20px;
}
.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 18px;
  color: #2563EB;
  cursor: pointer;
  flex-shrink: 0;
}
.header-search {
  flex: 1;
  max-width: 400px;
}
.header-right {
  display: flex;
  gap: 16px;
  align-items: center;
}
.header-link {
  color: #606266;
  text-decoration: none;
  font-size: 14px;
}
.header-link:hover { color: #2563EB; }

/* 未登录 */
.login-prompt {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}
.login-box {
  text-align: center;
  background: #fff;
  padding: 60px 80px;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
.login-box h2 { color: #303133; margin-bottom: 12px; }
.login-box p { color: #909399; margin-bottom: 30px; }

/* 主内容 */
.uc-main {
  max-width: 1200px;
  margin: 20px auto;
  display: flex;
  gap: 20px;
  padding: 0 20px;
  align-items: flex-start;
}

/* 侧边栏 */
.uc-sidebar {
  width: 240px;
  flex-shrink: 0;
}
.user-card {
  background: linear-gradient(135deg, #2563EB, #3b82f6);
  border-radius: 12px;
  padding: 24px 20px;
  text-align: center;
  color: #fff;
  margin-bottom: 12px;
}
.user-name {
  font-size: 18px;
  font-weight: 600;
  margin: 12px 0 6px;
}
.user-level { margin-bottom: 16px; }
.user-stats {
  display: flex;
  justify-content: center;
  gap: 24px;
}
.stat-item { text-align: center; }
.stat-num {
  display: block;
  font-size: 20px;
  font-weight: 700;
}
.stat-label {
  font-size: 12px;
  opacity: 0.8;
}
.uc-menu {
  border-right: none;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}

/* 内容区 */
.uc-content {
  flex: 1;
  min-width: 0;
}
.content-panel {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}
.panel-header {
  padding: 20px 24px;
  border-bottom: 1px solid #ebeef5;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.panel-header h3 { font-size: 16px; color: #303133; margin: 0; }
.panel-body { padding: 24px; }

/* 个人资料 */
.profile-form { max-width: 500px; }
.avatar-upload { display: flex; align-items: center; }
.form-tip { font-size: 12px; color: #909399; margin-left: 8px; }

/* 订单tabs */
.order-tabs { display: flex; gap: 16px; }
.order-tab { cursor: pointer; color: #606266; font-size: 14px; padding-bottom: 2px; }
.order-tab.active { color: #2563EB; border-bottom: 2px solid #2563EB; }
.order-list { display: flex; flex-direction: column; gap: 16px; }
.order-item {
  border: 1px solid #ebeef5;
  border-radius: 8px;
  overflow: hidden;
}
.order-header {
  background: #fafafa;
  padding: 10px 16px;
  display: flex;
  gap: 16px;
  font-size: 13px;
  color: #606266;
}
.order-status { margin-left: auto; }
.status-pending { color: #E6A23C; }
.status-paid { color: #2563EB; }
.status-shipped { color: #409EFF; }
.status-completed { color: #67C23A; }
.order-body { padding: 12px 16px; }
.order-goods { display: flex; gap: 12px; padding: 8px 0; }
.goods-img { width: 60px; height: 60px; border-radius: 4px; flex-shrink: 0; }
.goods-info { flex: 1; }
.goods-name { font-size: 14px; color: #303133; cursor: pointer; }
.goods-price { font-size: 13px; color: #909399; margin-top: 4px; }
.order-footer {
  padding: 10px 16px;
  border-top: 1px solid #ebeef5;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
}
.order-total { font-size: 14px; color: #606266; }

/* 收藏 */
.goods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 16px;
}
.goods-card {
  border: 1px solid #ebeef5;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
}
.goods-card .goods-img {
  width: 100%;
  height: 180px;
  display: block;
  cursor: pointer;
}
.goods-card .goods-info { padding: 10px; }
.goods-card .goods-name {
  font-size: 14px;
  color: #303133;
  cursor: pointer;
  margin-bottom: 8px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.goods-price-row { display: flex; justify-content: space-between; align-items: center; }
.goods-price { font-size: 16px; color: #F56C6C; font-weight: 600; }

/* 反馈 */
.feedback-form { max-width: 600px; }

/* 空状态 */
.empty-state { padding: 40px 0; }

/* 分页 */
.pagination-wrap { display: flex; justify-content: center; margin-top: 20px; }

/* 底部导航 */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  border-top: 1px solid #ebeef5;
  display: flex;
  z-index: 200;
  padding: 6px 0;
  padding-bottom: env(safe-area-inset-bottom, 6px);
}
.nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  color: #909399;
  font-size: 11px;
  cursor: pointer;
  position: relative;
  padding: 4px 0;
}
.nav-item.active { color: #2563EB; }
.cart-badge {
  position: absolute;
  top: 0;
  right: calc(50% - 20px);
  background: #F56C6C;
  color: #fff;
  font-size: 10px;
  border-radius: 10px;
  padding: 0 5px;
  min-width: 16px;
  text-align: center;
  line-height: 16px;
}

@media (max-width: 768px) {
  .uc-sidebar { display: none; }
  .uc-main { padding: 0; }
  .header-search { display: none; }
}
</style>

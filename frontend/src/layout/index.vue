<template>
  <div class="layout-container">
    <!-- 侧边栏 -->
    <aside :class="['sidebar', { collapsed: isCollapsed }]">
      <div class="sidebar-logo">
        <div class="logo-icon">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="var(--fe-primary)"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="var(--fe-primary)"/>
          </svg>
        </div>
        <span v-if="!isCollapsed" class="logo-text">飞鱼后台</span>
      </div>

      <el-scrollbar class="menu-scrollbar">
        <el-menu
          :default-active="activeMenu"
          :collapse="isCollapsed"
          :collapse-transition="false"
          class="sidebar-menu"
          background-color="transparent"
          :text-color="vars.sidebarText"
          :active-text-color="vars.sidebarActive"
          :unique-opened="true"
          @open="onMenuOpen"
          :router="true"
        >
          <template v-for="item in menuList" :key="item.url || item.path">
            <!-- 有子菜单的一级菜单 -->
            <el-sub-menu v-if="item.children && item.children.length" :index="item.url || item.path || String(item.id)">
              <template #title>
                <el-icon v-if="item.icon"><component :is="item.icon" /></el-icon>
                <span>{{ item.name }}</span>
              </template>
              <!-- 二级菜单 -->
              <template v-for="child in item.children" :key="child.path">
                <el-sub-menu v-if="child.children && child.children.length" :index="child.url || child.path || String(child.id)">
                  <template #title>
                    <el-icon v-if="child.icon"><component :is="child.icon" /></el-icon>
                    <span>{{ child.name }}</span>
                  </template>
                  <!-- 三级菜单 -->
                  <el-menu-item
                    v-for="grandson in child.children"
                    :key="grandson.path"
                    :index="grandson.url || grandson.path || String(grandson.id)"
                  >
                    <el-icon v-if="grandson.icon"><component :is="grandson.icon" /></el-icon>
                    <span>{{ grandson.name }}</span>
                  </el-menu-item>
                </el-sub-menu>
                <el-menu-item v-else :index="child.url || child.path || String(child.id)">
                  <el-icon v-if="child.icon"><component :is="child.icon" /></el-icon>
                  <span>{{ child.name }}</span>
                </el-menu-item>
              </template>
            </el-sub-menu>
            <!-- 无子菜单的一级菜单 -->
            <el-menu-item v-else :index="item.url || item.path || String(item.id)">
              <el-icon v-if="item.icon"><component :is="item.icon" /></el-icon>
              <span>{{ item.name }}</span>
            </el-menu-item>
          </template>
        </el-menu>
      </el-scrollbar>
    </aside>

    <div class="main-wrapper">
      <!-- 头部 -->
      <header class="header">
        <div class="header-left">
          <el-button
            class="collapse-btn"
            :icon="isCollapsed ? Expand : Fold"
            text
            @click="toggleCollapse"
          />
          <span class="current-path">{{ currentPath }}</span>
        </div>

        <!-- 全局菜单搜索 - 居中 -->
        <div class="global-search">
          <el-input
            v-model="menuSearchKey"
            placeholder="搜索菜单..."
            clearable
            @input="handleMenuSearch"
            @focus="showSearchResult = true"
            @blur="hideSearchResult"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
          <!-- 搜索结果下拉 -->
          <div v-if="showSearchResult && searchResults.length > 0" class="search-results">
            <div
              v-for="item in searchResults"
              :key="item.url || item.path"
              class="search-result-item"
              @mousedown.prevent="goToMenu(item)"
            >
              <el-icon><component :is="item.icon || 'Document'" /></el-icon>
              <span class="result-path">{{ item.pathText }}</span>
            </div>
          </div>
        </div>

        <div class="header-right">
          <!-- 消息通知 -->
          <div class="header-icon-btn" title="消息通知" style="display: flex; align-items: center; justify-content: center;">
            <el-badge :value="3" :hidden="true" style="display: flex; align-items: center;">
              <el-icon style="display: flex; align-items: center;"><Bell /></el-icon>
            </el-badge>
          </div>

          <!-- 全屏切换 -->
          <div class="header-icon-btn" title="全屏" @click="toggleFullscreen">
            <el-icon><FullScreen /></el-icon>
          </div>

          <!-- 布局配置 -->
          <div class="header-icon-btn" title="布局配置" @click="openLayoutConfig">
            <el-icon><Setting /></el-icon>
          </div>
          <!-- 主题切换 -->
          <el-tooltip content="切换主题" placement="bottom">
            <el-dropdown @command="handleThemeChange" trigger="click">
              <el-icon class="header-icon-btn" style="font-size: 18px; color: var(--fe-text-primary);"><Brush /></el-icon>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item command="light">
                    <span class="theme-option">
                      <span class="theme-dot" style="background: #ffffff; border: 1px solid #dcdfe6;"></span>
                      亮色主题
                    </span>
                  </el-dropdown-item>
                  <el-dropdown-item command="dark">
                    <span class="theme-option">
                      <span class="theme-dot" style="background: #0d1117;"></span>
                      暗黑主题
                    </span>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </el-tooltip>

          <el-dropdown @command="handleCommand" trigger="click">
            <span class="user-info">
              <el-avatar :size="32" style="background: linear-gradient(135deg, var(--fe-primary), var(--fe-primary-hover)); font-size: 12px; line-height: 32px;">
                {{ nickname ? nickname.slice(0, 1) : '管' }}
              </el-avatar>
              <span class="username">{{ nickname || '管理员' }}</span>
              <el-icon class="arrow-icon"><ArrowDown /></el-icon>
            </span>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item command="profile">
                  <el-icon><User /></el-icon>个人中心
                </el-dropdown-item>
                <el-dropdown-item command="logout" divided>
                  <el-icon><SwitchButton /></el-icon>退出登录
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </header>

      <!-- 标签导航 -->
      <div class="tab-nav">
        <!-- 标签列表 -->
        <div class="tab-list" ref="tabListRef">
          <div
            v-for="tab in tabs"
            :key="tab.path"
            :class="['tab-item', { active: activeTab === tab.path }]"
            @click="switchTab(tab.path)"
          >
            <span class="tab-title">{{ tab.title }}</span>
            <el-icon
              v-if="tab.closable !== false"
              class="tab-close"
              @click.stop="closeTab(tab.path)"
            >
              <Close />
            </el-icon>
          </div>
        </div>
      </div>

      <!-- 内容区 -->
      <main class="main-content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Fold, Expand, ArrowDown, SwitchButton, User, Brush, Close, MoreFilled, Search, Odometer, Setting, Key, Document, Tickets, List, ShoppingCart, DataLine, Bell, FullScreen, Upload, Memo, Grid } from '@element-plus/icons-vue'
import request from '@/utils/request'

// 获取菜单列表
const getMenuList = async () => {
  try {
    const res = await request.get('/menu/nav')
    if (res.code === 0 && res.data) {
      return res.data
    }
    return []
  } catch (e) {
    console.error('获取菜单失败:', e)
    return []
  }
}

const route = useRoute()
const router = useRouter()

const isCollapsed = ref(false)
const activeTab = ref('/dashboard')
const nickname = ref('管理员')
const tabListRef = ref(null)
const menuSearchKey = ref('')
const showSearchResult = ref(false)
const searchResults = ref([])

const tabs = ref([
  { path: '/dashboard', title: '工作台', pathText: '工作台', closable: false }
])

// 计算当前路径
const currentPath = computed(() => {
  const current = tabs.value.find(t => t.path === activeTab.value)
  return current?.pathText || ''
})

// 扁平化所有菜单项
const getAllMenuItems = () => {
  const items = []
  const traverse = (list, parentPath = '') => {
    list.forEach(item => {
      const pathText = parentPath ? `${parentPath}/${item.name}` : item.name
      if (item.path) {
        items.push({ ...item, pathText })
      }
      if (item.children) {
        traverse(item.children, pathText)
      }
    })
  }
  traverse(menuList.value)
  return items
}

// 搜索菜单
const handleMenuSearch = () => {
  if (!menuSearchKey.value) {
    searchResults.value = []
    return
  }
  const key = menuSearchKey.value.toLowerCase()
  const allItems = getAllMenuItems()
  searchResults.value = allItems.filter(item =>
    item.name.toLowerCase().includes(key) ||
    item.pathText.toLowerCase().includes(key)
  ).slice(0, 8)
}

// 跳转到菜单
const goToMenu = (item) => {
  router.push(item.path)
  menuSearchKey.value = ''
  searchResults.value = []
  showSearchResult.value = false
}

// 切换搜索（聚焦搜索框）
const toggleSearch = () => {
  const searchInput = document.querySelector('.global-search .el-input__inner')
  if (searchInput) {
    searchInput.focus()
  }
}

// 全屏切换
const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}

// 打开布局配置
const openLayoutConfig = () => {
  ElMessage.info('布局配置功能开发中...')
}

// 隐藏搜索结果
const hideSearchResult = () => {
  setTimeout(() => {
    showSearchResult.value = false
  }, 200)
}

const vars = reactive({
  sidebarText: '#606266',
  sidebarActive: '#409EFF'
})

const menuList = ref([
  { path: '/dashboard', name: '工作台', icon: 'Odometer' },
  {
    path: '/system',
    name: '系统管理',
    icon: 'Setting',
    children: [
      { path: '/system/user', name: '用户管理', icon: 'User' },
      { path: '/system/role', name: '角色管理', icon: 'Stamp' },
      { path: '/system/menu', name: '菜单管理', icon: 'Menu' },
      { path: '/system/dept', name: '部门管理', icon: 'OfficeBuilding' },
      { path: '/system/post', name: '岗位管理', icon: 'Briefcase' },
      { path: '/system/log', name: '操作日志', icon: 'Document' },
      { path: '/system/loginlog', name: '登录日志', icon: 'Key' },
      { path: '/system/config', name: '系统配置', icon: 'Tools' },
      { path: '/system/upload', name: '文件管理', icon: 'Folder' },
      { path: '/system/notice', name: '消息通知', icon: 'Bell' },
      { path: '/system/crontab', name: '定时任务', icon: 'Clock' },
      {
        path: '/system/dict',
        name: '字典管理',
        icon: 'Collection',
        children: [
          { path: '/system/dict/type', name: '字典类型' },
          { path: '/system/dict/data', name: '字典数据' }
        ]
      }
    ]
  },
  {
    path: '/expand',
    name: '扩展功能',
    icon: 'List',
    children: [
      { path: '/system/generator', name: '代码生成器', icon: 'Monitor' },
      { path: '/system/tenant', name: '多租户管理', icon: 'OfficeBuilding' },
      { path: '/system/crontab', name: '定时任务', icon: 'Clock' },
      { path: '/system/notice', name: '消息通知', icon: 'Bell' },
      { path: '/system/upload', name: '文件管理', icon: 'Folder' }
    ]
  },
  {
    path: '/channel',
    name: '渠道管理',
    icon: 'Connection',
    children: [
      { path: '/wechat', name: '微信渠道', icon: 'ChatDotRound' },
      { path: '/wechat/account', name: '公众号管理', icon: 'Avatar' },
      { path: '/wechat/menu', name: '菜单管理', icon: 'Menu' },
      { path: '/wechat/reply', name: '自动回复', icon: 'ChatDotRound' },
      { path: '/wechat/material', name: '素材中心', icon: 'Picture' },
      { path: '/wechat/fans', name: '粉丝管理', icon: 'User' },
      { path: '/wechat/mini_program', name: '小程序', icon: 'Grid' },
      { path: '/wechat/open_platform', name: '开放平台', icon: 'Cloud' }
    ]
  },
  {
    path: '/pay',
    name: '支付管理',
    icon: 'CreditCard',
    children: [
      { path: '/pay/config', name: '支付配置', icon: 'Setting' },
      { path: '/pay/order', name: '订单管理', icon: 'ShoppingCart' },
      { path: '/pay/refund', name: '退款管理', icon: 'Coin' },
      { path: '/pay/statement', name: '分账管理', icon: 'PieChart' }
    ]
  },
  {
    path: '/tool',
    name: '系统工具',
    icon: 'Tools',
    children: [
      { path: '/tool/form-design', name: '表单设计器', icon: 'Tickets' },
      { path: '/tool/form-list', name: '表单列表', icon: 'List' },
      { path: '/tool/data-screen', name: '数据大屏', icon: 'DataBoard' }
    ]
  },
  {
    path: '/workflow',
    name: '工作流',
    icon: 'Guide',
    children: [
      { path: '/workflow/list', name: '流程管理', icon: 'List' },
      { path: '/workflow/design', name: '流程设计', icon: 'Operation' },
      { path: '/workflow/instance', name: '流程实例', icon: 'Guide' },
      { path: '/workflow/todo', name: '我的待办', icon: 'Box' }
    ]
  },
  {
    path: '/content',
    name: '内容管理',
    icon: 'Tickets',
    children: [
      { path: '/content/article', name: '文章管理' },
      { path: '/content/category', name: '分类管理' }
    ]
  },
  {
    path: '/member',
    name: '会员管理',
    icon: 'List',
    children: [
      { path: '/member/list', name: '会员列表' },
      { path: '/member/level', name: '等级管理' }
    ]
  },
  {
    path: '/finance',
    name: '财务管理',
    icon: 'ShoppingCart',
    children: [
      { path: '/finance/order', name: '订单管理' },
      { path: '/finance/payment', name: '支付管理' }
    ]
  },
  {
    path: '/statistics',
    name: '数据统计',
    icon: 'DataLine',
    children: [
      { path: '/statistics/overview', name: '数据概览' },
      { path: '/statistics/report', name: '报表中心' }
    ]
  }
])

const activeMenu = computed(() => route.path)



const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const switchTab = (path) => {
  router.push(path)
}

const closeTab = (path) => {
  if (path === '/dashboard') return
  const index = tabs.value.findIndex(t => t.path === path)
  tabs.value.splice(index, 1)
  if (activeTab.value === path) {
    const next = tabs.value[index] || tabs.value[index - 1]
    if (next) router.push(next.path)
  }
}

const handleTabAction = (command) => {
  if (command === 'closeOther') {
    tabs.value = tabs.value.filter(t => t.path === activeTab.value || t.closable === false)
  } else if (command === 'closeAll') {
    tabs.value = [{ path: '/dashboard', title: '工作台', closable: false }]
    router.push('/dashboard')
  }
}

const handleThemeChange = (theme) => {
  document.documentElement.setAttribute('data-theme', theme)
  localStorage.setItem('fe-theme', theme)
  ElMessage.success(`已切换到${theme === 'dark' ? '暗黑' : '亮色'}主题`)
}

const handleCommand = async (command) => {
  if (command === 'logout') {
    try {
      await ElMessageBox.confirm('确定要退出登录吗？', '提示', { type: 'warning' })
      localStorage.removeItem('token')
      ElMessage.success('已退出')
      router.push('/login')
    } catch {}
  } else if (command === 'profile') {
    router.push('/profile')
  }
}

// 根据路径查找菜单项的完整路径文本
const findPathText = (path) => {
  const allItems = getAllMenuItems()
  const item = allItems.find(i => i.path === path)
  return item?.pathText || path
}

watch(() => route.path, (newPath) => {
  activeTab.value = newPath
  // 添加新标签
  if (!tabs.value.find(t => t.path === newPath)) {
    const title = route.meta?.title || newPath.split('/').pop()
    const pathText = findPathText(newPath)
    tabs.value.push({
      path: newPath,
      title,
      pathText,
      closable: newPath !== '/dashboard'
    })
  }
}, { immediate: true })

onMounted(async () => {
  nickname.value = localStorage.getItem('nickname') || '管理员'
  const savedTheme = localStorage.getItem('fe-theme')
  if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme)
  }
  // 从后端获取菜单
  const menus = await getMenuList()
  if (menus && menus.length > 0) {
    menuList.value = menus
  }
})
</script>

<style scoped>
.layout-container {
  display: flex;
  height: 100vh;
  background: var(--fe-bg-page);
}

/* 侧边栏 */
.sidebar {
  width: 220px;
  background: var(--fe-bg-sidebar) !important;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  overflow: hidden;
  box-shadow: var(--fe-shadow-sm);
}

.sidebar.collapsed {
  width: 64px;
}

.sidebar-logo {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  background: var(--fe-bg-card);
  border-bottom: 1px solid var(--fe-border);
}

.logo-text {
  font-size: 16px;
  font-weight: 600;
  color: var(--fe-text-primary);
  white-space: nowrap;
}

.menu-scrollbar {
  flex: 1;
  height: calc(100vh - 60px);
}

.sidebar-menu {
  border: none !important;
  background: transparent !important;
}

:deep(.el-menu) {
  background: transparent !important;
}



/* 侧边栏基础样式 */
.sidebar-menu :deep(.el-sub-menu__title) {
  height: 50px;
  line-height: 50px;
  padding-left: 16px;
}
.sidebar-menu :deep(.el-menu-item) {
  height: 44px;
  line-height: 44px;
  padding-left: 44px;
}

/* 主内容区域 */
.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* 头部 */
.header {
  height: 56px;
  background: var(--fe-bg-header);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  box-shadow: var(--fe-shadow);
  z-index: 10;
}


/* 主内容区域 */
.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0; /* 防止flex子元素溢出 */
}

/* 头部 */
.header {
  height: 60px;
  background: var(--fe-bg-header);
  display: flex;
  align-items: center;
  padding: 0 16px;
  box-shadow: var(--fe-shadow);
  z-index: 10;
  flex-shrink: 0;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.current-path {
  font-size: 14px;
  color: var(--fe-text-secondary);
}

.global-search {
  position: relative;
  width: 200px;
  margin-left: auto;
  margin-right: 16px;
}

.global-search .el-input__wrapper {
  background: rgba(0, 0, 0, 0.06) !important;
  border-radius: 8px;
  box-shadow: none !important;
  border: 1px solid var(--fe-border) !important;
}

.global-search .el-input__wrapper:hover,
.global-search .el-input__wrapper:focus-within {
  border-color: var(--fe-primary) !important;
  background: rgba(0, 0, 0, 0.08) !important;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.header-icon-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
  color: var(--fe-text-primary);
  font-size: 18px;
  flex-shrink: 0;
}

.header-icon-btn:hover {
  background: var(--fe-bg-hover);
  color: var(--fe-primary);
}

/* 用户信息 */
.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 8px;
  transition: background 0.2s;
}

.user-info:hover {
  background: var(--fe-bg-hover);
}

.username {
  font-size: 14px;
  color: var(--fe-text-primary);
  max-width: 80px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.arrow-icon {
  font-size: 12px;
  color: var(--fe-text-secondary);
}

/* 搜索结果下拉 */
.search-results {
  position: absolute;
  top: calc(100% + 4px);
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 400px;
  background: var(--fe-bg-card);
  border-radius: 8px;
  box-shadow: var(--fe-shadow-lg);
  max-height: 400px;
  overflow-y: auto;
  z-index: 1000;
}

.search-result-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.search-result-item:hover {
  background: var(--fe-bg-hover);
}

.result-path {
  font-size: 13px;
  color: var(--fe-text-primary);
}

/* 主内容 */
.main-content {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  background: var(--fe-bg-page);
}

/* 折叠按钮 */
.collapse-btn {
  font-size: 18px;
}

/* logo图标 */
.logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

</style>

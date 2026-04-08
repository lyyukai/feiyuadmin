<template>
  <div class="app-layout">
    <!-- 顶部导航栏 -->
    <header class="layout-header">
      <div class="header-left">
        <!-- 移动端菜单按钮 -->
        <el-button
          class="menu-toggle-btn fe-hide-desktop"
          :icon="isDrawerOpen ? Close : 'Menu'"
          text
          @click="toggleDrawer"
        />
        <div class="logo" @click="$router.push('/')">
          <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="var(--fe-primary)"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="var(--fe-primary)"/>
          </svg>
          <span class="logo-text fe-hide-mobile">飞鱼后台</span>
        </div>
      </div>

      <div class="header-center fe-hide-mobile">
        <el-menu
          mode="horizontal"
          :default-active="currentMenuIndex"
          class="header-menu"
          @select="handleMenuSelect"
        >
          <el-menu-item index="/ai">
            <el-icon><MagicStick /></el-icon>
            <span>AI助手</span>
          </el-menu-item>
        </el-menu>
      </div>

      <div class="header-right">
        <!-- 移动端全屏切换 -->
        <div class="header-icon-btn fe-hide-mobile" title="全屏" @click="toggleFullscreen">
          <el-icon><FullScreen /></el-icon>
        </div>
        <!-- 移动端主题切换 -->
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
            <el-avatar :size="32" style="background: linear-gradient(135deg, var(--fe-primary), var(--fe-primary-hover)); font-size: 12px;">
              {{ nickname ? nickname.slice(0, 1) : '管' }}
            </el-avatar>
            <span class="username fe-hide-mobile">{{ nickname || '管理员' }}</span>
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

    <!-- 主体内容区 -->
    <div class="layout-body">
      <!-- PC端侧边栏 -->
      <aside
        v-if="showSidebar"
        :class="['layout-sidebar fe-hide-mobile', { collapsed: isCollapsed }]"
      >
        <el-scrollbar class="sidebar-scrollbar">
          <el-menu
            :default-active="currentRoute"
            class="sidebar-menu"
            @select="handleSidebarSelect"
          >
            <el-menu-item index="/ai/chat">
              <el-icon><ChatDotRound /></el-icon>
              <span>AI对话</span>
            </el-menu-item>
            <el-menu-item index="/ai/nl2sql">
              <el-icon><DataAnalysis /></el-icon>
              <span>NL2SQL</span>
            </el-menu-item>
            <el-menu-item index="/ai/prompts">
              <el-icon><Document /></el-icon>
              <span>Prompt管理</span>
            </el-menu-item>
          </el-menu>
        </el-scrollbar>
      </aside>

      <!-- 移动端抽屉 -->
      <el-drawer
        v-model="isDrawerOpen"
        direction="ltr"
        :size="drawerSize"
        :show-close="false"
        :with-header="false"
        class="mobile-drawer"
        @closed="onDrawerClosed"
      >
        <div class="drawer-logo">
          <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="var(--fe-primary)"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="var(--fe-primary)"/>
          </svg>
          <span class="logo-text">飞鱼后台</span>
        </div>
        <el-scrollbar class="drawer-scrollbar">
          <el-menu
            :default-active="currentRoute"
            class="sidebar-menu"
            @select="handleSidebarSelect"
          >
            <el-menu-item index="/ai/chat">
              <el-icon><ChatDotRound /></el-icon>
              <span>AI对话</span>
            </el-menu-item>
            <el-menu-item index="/ai/nl2sql">
              <el-icon><DataAnalysis /></el-icon>
              <span>NL2SQL</span>
            </el-menu-item>
            <el-menu-item index="/ai/prompts">
              <el-icon><Document /></el-icon>
              <span>Prompt管理</span>
            </el-menu-item>
          </el-menu>
        </el-scrollbar>
      </el-drawer>

      <!-- 主内容 -->
      <div class="layout-main">
        <router-view v-slot="{ Component }">
          <keep-alive>
            <component :is="Component" />
          </keep-alive>
        </router-view>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  MagicStick,
  ChatDotRound,
  DataAnalysis,
  Document,
  User,
  Setting,
  SwitchButton,
  ArrowDown,
  FullScreen,
  Brush,
  Close,
} from '@element-plus/icons-vue'
import { isMobile, getCurrentBreakpoint, onBreakpointChange } from '@/utils/responsive'

const route = useRoute()
const router = useRouter()

// ---- State ----
const nickname = ref('管理员')
const isCollapsed = ref(false)
const isDrawerOpen = ref(false)
const currentBreakpoint = ref(getCurrentBreakpoint())

// ---- Computed ----
const currentRoute = computed(() => route.path)

const currentMenuIndex = computed(() => {
  const path = route.path
  if (path.startsWith('/ai')) return '/ai'
  return '/'
})

const showSidebar = computed(() => {
  return route.path.startsWith('/ai')
})

const drawerSize = computed(() => {
  // Mobile uses vw, tablet uses px
  if (currentBreakpoint.value === 'mobile') return '85vw'
  return '280px'
})

// ---- Methods ----
function toggleDrawer() {
  isDrawerOpen.value = !isDrawerOpen.value
}

function onDrawerClosed() {
  isDrawerOpen.value = false
}

function handleMenuSelect(index) {
  if (index === '/ai') {
    router.push('/ai/chat')
  }
}

function handleSidebarSelect(index) {
  router.push(index)
  // Close drawer on mobile after navigation
  if (currentBreakpoint.value === 'mobile') {
    isDrawerOpen.value = false
  }
}

function toggleFullscreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}

function handleThemeChange(theme) {
  document.documentElement.setAttribute('data-theme', theme)
  localStorage.setItem('fe-theme', theme)
  ElMessage.success(`已切换到${theme === 'dark' ? '暗黑' : '亮色'}主题`)
}

async function handleCommand(command) {
  switch (command) {
    case 'logout':
      try {
        await ElMessageBox.confirm('确定要退出登录吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        localStorage.removeItem('token')
        ElMessage.success('已退出登录')
        router.push('/login')
      } catch {
        // cancelled
      }
      break
    case 'profile':
      router.push('/profile')
      break
  }
}

// ---- Breakpoint watcher ----
let unsubscribeBreakpoint = null

onMounted(() => {
  nickname.value = localStorage.getItem('nickname') || '管理员'
  const savedTheme = localStorage.getItem('fe-theme')
  if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme)
  }

  // Watch breakpoint changes
  unsubscribeBreakpoint = onBreakpointChange((bp) => {
    currentBreakpoint.value = bp
    // Close drawer when switching from mobile to desktop
    if (bp !== 'mobile') {
      isDrawerOpen.value = false
    }
  })
})

onUnmounted(() => {
  if (unsubscribeBreakpoint) unsubscribeBreakpoint()
})
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

/* ---- Header ---- */
.layout-header {
  display: flex;
  align-items: center;
  height: var(--fe-header-height);
  padding: 0 16px;
  background: var(--fe-bg-header);
  border-bottom: 1px solid var(--fe-border);
  box-shadow: var(--fe-shadow-sm);
  z-index: var(--fe-z-header);
  flex-shrink: 0;
  gap: 12px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.menu-toggle-btn {
  font-size: 20px;
  color: var(--fe-text-primary);
  width: 36px;
  height: 36px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.logo-text {
  font-size: 16px;
  font-weight: 600;
  color: var(--fe-text-primary);
  white-space: nowrap;
}

.header-center {
  flex: 1;
  overflow: hidden;
}

.header-menu {
  border-bottom: none;
  background: transparent;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}

.header-icon-btn {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--fe-radius-md);
  cursor: pointer;
  color: var(--fe-text-primary);
  font-size: 18px;
  transition: background var(--fe-transition-fast);
}

.header-icon-btn:hover {
  background: var(--fe-bg-hover);
  color: var(--fe-primary);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: var(--fe-radius-md);
  transition: background var(--fe-transition-fast);
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

/* ---- Body ---- */
.layout-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* ---- PC Sidebar ---- */
.layout-sidebar {
  width: var(--fe-sidebar-width);
  flex-shrink: 0;
  background: var(--fe-bg-sidebar);
  border-right: 1px solid var(--fe-border);
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  overflow: hidden;
}

.layout-sidebar.collapsed {
  width: var(--fe-sidebar-collapsed-width);
}

.sidebar-scrollbar {
  flex: 1;
  height: calc(100vh - var(--fe-header-height));
}

.sidebar-menu {
  border-right: none;
  background: transparent;
}

:deep(.el-menu-item),
:deep(.el-sub-menu__title) {
  height: 48px;
  line-height: 48px;
}

/* ---- Mobile Drawer ---- */
.mobile-drawer {
  --el-drawer-bg-color: var(--fe-bg-sidebar) !important;
}

.drawer-logo {
  height: var(--fe-header-height);
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 16px;
  border-bottom: 1px solid var(--fe-border);
  flex-shrink: 0;
}

.drawer-scrollbar {
  height: calc(100vh - var(--fe-header-height));
}

/* ---- Main Content ---- */
.layout-main {
  flex: 1;
  overflow: hidden;
  background: var(--fe-bg-page);
}

/* ---- Theme Option ---- */
.theme-option {
  display: flex;
  align-items: center;
  gap: 8px;
}

.theme-dot {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: inline-block;
}
</style>

import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/index.vue')
  },
  {
    path: '/doc',
    name: 'Doc',
    component: () => import('@/views/doc/index.vue')
  },
  {
    path: '/user',
    name: 'User',
    component: () => import('@/views/user/index.vue')
  },
  {
    path: '/article',
    name: 'Article',
    component: () => import('@/views/article/index.vue')
  },
  // AI 相关页面
  {
    path: '/ai/chat',
    name: 'AiChat',
    component: () => import('@/views/ai/ChatView.vue')
  },
  {
    path: '/ai/nl2sql',
    name: 'Nl2Sql',
    component: () => import('@/views/ai/Nl2SqlView.vue')
  },
  {
    path: '/ai/prompts',
    name: 'PromptList',
    component: () => import('@/views/ai/PromptList.vue')
  },
  {
    path: '/ai/prompts/edit/:id?',
    name: 'PromptEdit',
    component: () => import('@/views/ai/PromptEdit.vue')
  },
  {
    path: '/ai/prompts/manager',
    name: 'PromptManager',
    component: () => import('@/views/ai/PromptManager.vue')
  }
]

const router = createRouter({
  history: createWebHistory('/pc/'),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    }
    return { top: 0 }
  }
})

export default router

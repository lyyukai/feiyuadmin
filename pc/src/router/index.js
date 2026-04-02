import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/demo/index.vue')
  },
  {
    path: '/doc',
    name: 'Doc',
    component: () => import('@/views/doc/index.vue')
  },
  {
    path: '/nl2sql',
    name: 'NL2SQL',
    component: () => import('@/views/nl2sql/index.vue')
  },
  {
    path: '/lowcode',
    name: 'LowCode',
    component: () => import('@/views/lowcode/index.vue')
  }
]

const router = createRouter({
  history: createWebHistory('/pc/'),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth', top: 80 }
    }
    return { top: 0, behavior: 'smooth' }
  }
})

export default router

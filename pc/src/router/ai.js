import { RouteRecordRaw } from 'vue-router'

const aiRoutes: RouteRecordRaw[] = [
  {
    path: '/ai',
    name: 'AI',
    redirect: '/ai/chat',
    children: [
      {
        path: 'chat',
        name: 'AIChat',
        component: () => import('@/views/ai/ChatView.vue')
      },
      {
        path: 'nl2sql',
        name: 'AINl2Sql',
        component: () => import('@/views/ai/Nl2SqlView.vue')
      },
      {
        path: 'prompt-list',
        name: 'PromptList',
        component: () => import('@/views/ai/PromptList.vue')
      },
      {
        path: 'prompt-edit',
        name: 'PromptEdit',
        component: () => import('@/views/ai/PromptEdit.vue')
      },
      {
        path: 'prompt-manager',
        name: 'PromptManager',
        component: () => import('@/views/ai/PromptManager.vue')
      }
    ]
  }
]

export default aiRoutes

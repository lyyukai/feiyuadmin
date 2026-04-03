import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/login/index.vue'),
    meta: { title: '登录', hidden: true }
  },
  {
    path: '/demo',
    name: 'Demo',
    component: () => import('@/views/demo/index.vue'),
    meta: { title: '演示站', hidden: true, requiresAuth: false }
  },
  {
    path: '/',
    component: () => import('@/layout/index.vue'),
    redirect: '/dashboard',
    children: [
      { path: '/dashboard', name: 'Dashboard', component: () => import('@/views/dashboard/index.vue'), meta: { title: '工作台', icon: 'Odometer', breadcrumb: '工作台' } },
      // 系统管理
      { path: '/system/user', name: 'UserManage', component: () => import('@/views/system/user/index.vue'), meta: { title: '用户管理', icon: 'User', breadcrumb: '系统管理 / 用户管理' } },
      { path: '/system/role', name: 'RoleManage', component: () => import('@/views/system/role/index.vue'), meta: { title: '角色管理', icon: 'Stamp', breadcrumb: '系统管理 / 角色管理' } },
      { path: '/system/menu', name: 'MenuManage', component: () => import('@/views/system/menu/index.vue'), meta: { title: '菜单管理', icon: 'Menu', breadcrumb: '系统管理 / 菜单管理' } },
      { path: '/system/dept', name: 'DeptManage', component: () => import('@/views/system/dept/index.vue'), meta: { title: '部门管理', icon: 'OfficeBuilding', breadcrumb: '系统管理 / 部门管理' } },
      { path: '/system/post', name: 'PostManage', component: () => import('@/views/system/post/index.vue'), meta: { title: '岗位管理', icon: 'Briefcase', breadcrumb: '系统管理 / 岗位管理' } },
      { path: '/system/config', name: 'ConfigManage', component: () => import('@/views/system/config/index.vue'), meta: { title: '系统配置', icon: 'Tools', breadcrumb: '系统管理 / 系统配置' } },
      { path: '/system/log', name: 'LogManage', component: () => import('@/views/system/log/index.vue'), meta: { title: '操作日志', icon: 'Document', breadcrumb: '系统管理 / 操作日志' } },
      { path: '/system/loginlog', name: 'LoginLogManage', component: () => import('@/views/system/loginlog/index.vue'), meta: { title: '登录日志', icon: 'Key', breadcrumb: '系统管理 / 登录日志' } },
      { path: '/system/login_log', redirect: '/system/loginlog' },
      { path: '/system/dict', redirect: '/system/dict/type' },
      { path: '/system/upload', name: 'UploadManage', component: () => import('@/views/system/upload/index.vue'), meta: { title: '文件管理', icon: 'Upload', breadcrumb: '系统管理 / 文件管理' } },
      { path: '/system/notice', name: 'NoticeManage', component: () => import('@/views/system/notice/index.vue'), meta: { title: '消息通知', icon: 'Bell', breadcrumb: '系统管理 / 消息通知' } },
      { path: '/system/notice/channel', name: 'NoticeChannel', component: () => import('@/views/system/notice/channel.vue'), meta: { title: '渠道配置', icon: 'Bell', breadcrumb: '系统管理 / 消息通知 / 渠道配置' } },
      { path: '/system/notice/template', name: 'NoticeTemplate', component: () => import('@/views/system/notice/template.vue'), meta: { title: '模板管理', icon: 'Bell', breadcrumb: '系统管理 / 消息通知 / 模板管理' } },
      { path: '/system/notice/record', name: 'NoticeRecord', component: () => import('@/views/system/notice/record.vue'), meta: { title: '发送记录', icon: 'Bell', breadcrumb: '系统管理 / 消息通知 / 发送记录' } },
      { path: '/system/crontab', name: 'CrontabManage', component: () => import('@/views/system/crontab/index.vue'), meta: { title: '定时任务', icon: 'Timer', breadcrumb: '系统管理 / 定时任务' } },
      { path: '/system/tenant', name: 'TenantManage', component: () => import('@/views/system/tenant/index.vue'), meta: { title: '租户管理', icon: 'Office', breadcrumb: '系统管理 / 租户管理' } },
      { path: '/system/generator', name: 'GeneratorManage', component: () => import('@/views/system/generator/index.vue'), meta: { title: '代码生成器', icon: 'Cpu', breadcrumb: '系统管理 / 代码生成器' } },
      { path: '/system/dict/type', name: 'DictTypeManage', component: () => import('@/views/system/dict/type/index.vue'), meta: { title: '字典类型', icon: 'Memo', breadcrumb: '系统管理 / 字典类型' } },
      { path: '/system/dict/data', name: 'DictDataManage', component: () => import('@/views/system/dict/data/index.vue'), meta: { title: '字典数据', icon: 'Grid', breadcrumb: '系统管理 / 字典数据' } },
      // 工具管理
      { path: '/tool/form-list', name: 'FormList', component: () => import('@/views/tool/form-list/index.vue'), meta: { title: '表单管理', icon: 'Tickets', breadcrumb: '工具管理 / 表单管理' } },
      { path: '/tool/form-design', name: 'FormDesign', component: () => import('@/views/tool/form-design/index.vue'), meta: { title: '表单设计', icon: 'Edit', breadcrumb: '工具管理 / 表单设计' } },
      { path: '/tool/lowcode', name: 'LowCode', component: () => import('@/views/tool/lowcode/index.vue'), meta: { title: '低代码平台', icon: 'Grid', breadcrumb: '工具管理 / 低代码平台' } },
      { path: '/tool/nl2sql', name: 'NL2SQL', component: () => import('@/views/tool/nl2sql/index.vue'), meta: { title: 'NL2SQL', icon: 'ChatDotRound', breadcrumb: '工具管理 / NL2SQL' } },
      // 内容管理
      { path: '/content/article', name: 'ArticleManage', component: () => import('@/views/content/article/index.vue'), meta: { title: '文章管理', icon: 'Document', breadcrumb: '内容管理 / 文章管理' } },
      { path: '/content/category', name: 'CategoryManage', component: () => import('@/views/content/category/index.vue'), meta: { title: '分类管理', icon: 'Folder', breadcrumb: '内容管理 / 分类管理' } },
      // 会员管理
      { path: '/member/list', name: 'MemberList', component: () => import('@/views/member/list/index.vue'), meta: { title: '会员列表', icon: 'User', breadcrumb: '会员管理 / 会员列表' } },
      { path: '/member/level', name: 'MemberLevel', component: () => import('@/views/member/level/index.vue'), meta: { title: '等级管理', icon: 'Star', breadcrumb: '会员管理 / 等级管理' } },
      // 数据统计
      { path: '/statistics/overview', name: 'StatisticsOverview', component: () => import('@/views/statistics/overview/index.vue'), meta: { title: '统计概览', icon: 'DataLine', breadcrumb: '数据统计 / 统计概览' } },
      { path: '/statistics/report', name: 'StatisticsReport', component: () => import('@/views/statistics/report/index.vue'), meta: { title: '数据报表', icon: 'TrendCharts', breadcrumb: '数据统计 / 数据报表' } },
      // 数据可视化大屏
      { path: '/tool/data-screen', name: 'DataScreen', component: () => import('@/views/tool/data-screen/index.vue'), meta: { title: '数据大屏', icon: 'DataBoard', breadcrumb: '工具 / 数据大屏' } },
      { path: '/tool/data-screen/design', name: 'DataScreenDesign', component: () => import('@/views/tool/data-screen/Designer.vue'), meta: { title: '大屏设计', hidden: true } },
      { path: '/data-screen/preview/:id', name: 'DataScreenPreview', component: () => import('@/views/tool/data-screen/Preview.vue'), meta: { title: '大屏预览', hidden: true } },
      // 微信渠道
      { path: '/wechat/account', name: 'WechatAccount', component: () => import('@/views/wechat/account/index.vue'), meta: { title: '公众号管理', icon: 'ChatDotRound', breadcrumb: '微信渠道 / 公众号管理' } },
      { path: '/wechat/menu', name: 'WechatMenu', component: () => import('@/views/wechat/menu/index.vue'), meta: { title: '菜单管理', icon: 'Menu', breadcrumb: '微信渠道 / 菜单管理' } },
      { path: '/wechat/menu/designer', name: 'WechatMenuDesigner', component: () => import('@/views/wechat/menu/designer.vue'), meta: { title: '菜单设计器', hidden: true } },
      { path: '/wechat/reply', name: 'WechatReply', component: () => import('@/views/wechat/reply/index.vue'), meta: { title: '自动回复', icon: 'ChatLineRound', breadcrumb: '微信渠道 / 自动回复' } },
      { path: '/wechat/material', name: 'WechatMaterial', component: () => import('@/views/wechat/material/index.vue'), meta: { title: '素材管理', icon: 'Picture', breadcrumb: '微信渠道 / 素材管理' } },
      { path: '/wechat/fans', name: 'WechatFans', component: () => import('@/views/wechat/fans/index.vue'), meta: { title: '粉丝管理', icon: 'User', breadcrumb: '微信渠道 / 粉丝管理' } },
      { path: '/wechat/mini_program', name: 'MiniProgram', component: () => import('@/views/wechat/mini_program/index.vue'), meta: { title: '小程序管理', icon: 'Monitor', breadcrumb: '微信渠道 / 小程序管理' } },
      { path: '/wechat/open_platform', name: 'OpenPlatform', component: () => import('@/views/wechat/open_platform/index.vue'), meta: { title: '开放平台', icon: 'Connection', breadcrumb: '微信渠道 / 开放平台' } },
      // 支付渠道
      { path: '/pay/config', name: 'PayConfig', component: () => import('@/views/pay/config/index.vue'), meta: { title: '支付配置', icon: 'Money', breadcrumb: '支付渠道 / 支付配置' } },
      { path: '/pay/order', name: 'PayOrder', component: () => import('@/views/pay/order/index.vue'), meta: { title: '订单管理', icon: 'Tickets', breadcrumb: '支付渠道 / 订单管理' } },
      { path: '/pay/refund', name: 'PayRefund', component: () => import('@/views/pay/refund/index.vue'), meta: { title: '退款管理', icon: 'Refund', breadcrumb: '支付渠道 / 退款管理' } },
      { path: '/pay/statement', name: 'PayStatement', component: () => import('@/views/pay/statement/index.vue'), meta: { title: '分账管理', icon: 'Coin', breadcrumb: '支付渠道 / 分账管理' } },
      // 工作流管理
      { path: '/workflow/list', name: 'WorkflowList', component: () => import('@/views/workflow/list/index.vue'), meta: { title: '流程管理', icon: 'Connection', breadcrumb: '工作流 / 流程管理' } },
      { path: '/workflow/design', name: 'WorkflowDesign', component: () => import('@/views/workflow/design/index.vue'), meta: { title: '流程设计', hidden: true } },
      { path: '/workflow/instance', name: 'WorkflowInstance', component: () => import('@/views/workflow/instance/index.vue'), meta: { title: '实例管理', icon: 'Document', breadcrumb: '工作流 / 实例管理' } },
      { path: '/workflow/todo', name: 'WorkflowTodo', component: () => import('@/views/workflow/todo/index.vue'), meta: { title: '我的待办', icon: 'List', breadcrumb: '工作流 / 我的待办' } },
      // 个人中心
      { path: '/profile', name: 'Profile', component: () => import('@/views/profile/index.vue'), meta: { title: '个人中心', icon: 'User', breadcrumb: '个人中心' } },
      { path: '/settings', name: 'Settings', component: () => import('@/views/settings/index.vue'), meta: { title: '系统设置', icon: 'Setting', breadcrumb: '系统设置' } },
      // 示例页面
      { path: '/example/editor-demo', name: 'EditorDemo', component: () => import('@/views/example/editor-demo/index.vue'), meta: { title: '编辑器示例', icon: 'Edit', breadcrumb: '示例 / 编辑器示例' } }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL || '/'),
  routes
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? to.meta.title + ' - 飞鱼后台' : '飞鱼后台'
  const token = localStorage.getItem('token')
  if (to.path !== '/login' && to.path !== '/demo' && !token) {
    next('/login')
  } else {
    next()
  }
})

export default router

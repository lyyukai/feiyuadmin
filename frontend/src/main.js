import { createApp } from 'vue'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import en from 'element-plus/dist/locale/en.mjs'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import { createI18n } from 'vue-i18n'

import App from './App.vue'
import router from './router'
import permissionDirective from './directive/permission'
import './assets/styles/index.css'

// 语言包
import zhCN from './locales/zh-CN.js'
import enLocale from './locales/en.js'

// 全局组件
import RichEditor from './components/RichEditor/index.vue'
import CodeEditor from './components/CodeEditor/index.vue'

// 获取浏览器语言，默认为中文
const getDefaultLocale = () => {
  const lang = navigator.language || navigator.userLanguage || 'zh-CN'
  return lang.startsWith('en') ? 'en' : 'zh-CN'
}

// 创建 i18n 实例
const i18n = createI18n({
  legacy: false, // 使用 Composition API 模式
  locale: getDefaultLocale(),
  fallbackLocale: 'zh-CN',
  messages: {
    'zh-CN': zhCN,
    en: enLocale,
  },
})

const app = createApp(App)

// Register Element Plus Icons globally
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component)
}

app.use(createPinia())
app.use(router)
app.use(i18n)
app.use(ElementPlus, {
  locale: getDefaultLocale() === 'en' ? en : zhCn,
})

// Register permission directive
app.directive('permission', permissionDirective)

// 注册全局编辑器组件
app.component('RichEditor', RichEditor)
app.component('CodeEditor', CodeEditor)

// 导出 i18n 供其他模块使用（如切换语言）
export { i18n }

app.mount('#app')

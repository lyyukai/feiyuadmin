import { createApp } from 'vue'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

import App from './App.vue'
import router from './router'
import permissionDirective from './directive/permission'
import './assets/styles/index.css'

// 全局组件
import RichEditor from './components/RichEditor/index.vue'
import CodeEditor from './components/CodeEditor/index.vue'

const app = createApp(App)

// Register Element Plus Icons globally
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component)
}

app.use(createPinia())
app.use(router)
app.use(ElementPlus, { locale: zhCn })

// Register permission directive
app.directive('permission', permissionDirective)

// 注册全局编辑器组件
app.component('RichEditor', RichEditor)
app.component('CodeEditor', CodeEditor)

app.mount('#app')

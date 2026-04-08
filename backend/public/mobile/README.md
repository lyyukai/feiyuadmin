# 飞鱼移动端 H5

基于 Vue3 + 原生 HTML5 的飞鱼后台管理系统移动端 SPA。

## 访问地址

- 开发访问: `http://localhost:5173/` 或直接打开 `index.html`
- 线上访问: `http://demo.fydev.cn/mobile/`
- 后台API: `http://demo.fydev.cn/adminapi/`

## 技术栈

- **Vue 3** (CDN引入, Composition API)
- **原生 HTML5** + CSS3 (Flexbox布局, 移动端适配)
- **SPA路由**: Hash模式 (window.location.hash)
- **HTTP**: Fetch API
- **本地存储**: localStorage (Token & 用户信息)

## 页面说明

| 页面 | 路由 | 说明 |
|------|------|------|
| 首页 | `#page-index` | Banner轮播、功能入口、最新文章列表 |
| AI助手 | `#page-ai` | 接入 `/adminapi/ai/chat/chat` |
| 文章列表 | `#page-article` | 接入 `/adminapi/mobile/article/lists` |
| 文章详情 | `#page-article-detail` | 接入 `/adminapi/mobile/article/detail` |
| 个人中心 | `#page-user` | 登录状态管理 |

## 核心功能

- ✅ Hash路由切换页面 (无刷新)
- ✅ Banner自动轮播 (3秒间隔)
- ✅ 文章列表下拉/上拉加载
- ✅ AI助手对话 (POST /ai/chat/chat)
- ✅ 文章详情展示
- ✅ 个人中心登录/登出
- ✅ 外部链接跳转提示
- ✅ 移动端安全区域适配 (env safe-area-inset-bottom)
- ✅ 骨架屏/加载状态

## 目录结构

```
feiyu-mobile/
├── index.html       # 主入口 (SPA全部页面)
├── manifest.json    # UniApp H5配置
└── README.md
```

## API 接口

| 接口 | 方法 | 说明 |
|------|------|------|
| `/mobile/article/lists` | GET | 文章列表 |
| `/mobile/article/detail?id=` | GET | 文章详情 |
| `/ai/chat/chat` | POST | AI对话 |
| `/admin/login` | GET | 登录页 |

## 开发说明

如需正式 UniApp CLI 项目, 可执行:

```bash
cd /www/wwwroot/feiyu-mobile
npm init -y
npm install @dcloudio/uni-app vue@^3.5 vite@^5 @vitejs/plugin-vue --legacy-peer-deps
```

然后将 `index.html` 拆分为 `src/pages/` 下的各 `.vue` 文件, 配置 `pages.json` 路由。

## 部署

```bash
# 复制到后台public目录
cp -r /www/wwwroot/feiyu-mobile/* /www/wwwroot/feiyuadmin/backend/public/mobile/
```

确保 Nginx 配置了 `/mobile/` 路径指向 `public/mobile/` 目录。

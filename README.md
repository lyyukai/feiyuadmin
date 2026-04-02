# 飞鱼后台管理系统（FeiyuAdmin）

<div align="center">

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0+-green.svg)](https://www.php.net/)
[![Vue](https://img.shields.io/badge/Vue-3.5-green.svg)](https://vuejs.org/)
[![ThinkPHP](https://img.shields.io/badge/ThinkPHP-8.0-green.svg)](https://www.thinkphp.cn/)
[![Element Plus](https://img.shields.io/badge/Element%20Plus-2.0-blue.svg)](https://element-plus.org/)

**🚀 高性能 · 🔐 安全可靠 · 🎨 简洁美观 · 📦 开源免费**

*A high-performance, secure, and beautiful admin dashboard built with Vue3 + ThinkPHP8*

[在线演示](http://demo.fydev.cn/demo) · [技术文档](http://demo.fydev.cn/doc) · [后端API](http://demo.fydev.cn/api.html)

技术交流QQ：767677830

</div>

---

## 📖 项目简介

飞鱼后台管理系统（FeiyuAdmin）是一款基于 **Vue3 + Vite + Element Plus** 前端架构，搭配 **ThinkPHP 8** 后端框架的通用型后台管理系统。

完全免费开源，采用 MIT 协议，可商用可二次开发。适用于各类管理系统的快速搭建，如：

- 企业内部管理系统
- SaaS 管理后台
- 电商后台管理
- 内容管理系统
- OA 办公系统

---

## 🎯 核心特性

| 特性 | 说明 |
|------|------|
| 🎨 美观易用 | Element Plus 组件库，深色/浅色主题切换 |
| 🚀 高性能 | Vue3 + Vite 构建，毫秒级响应 |
| 🔐 安全可靠 | JWT 认证、RBAC 权限、验证码防护 |
| 📱 响应式 | 适配 PC、平板、手机多终端 |
| ⚡ 代码生成 | 一键生成 CRUD，开发效率提升 80% |
| 🔄 热更新 | Vite HMR 开发体验极佳 |
| 📦 开箱即用 | Docker 一键部署，5 分钟上线 |
| 🌐 多语言 | 支持国际化配置 |

---

## 🖼️ 系统截图

### 演示站首页
![Demo](docs/images/demo-home.png)

### 工作台
![Dashboard](docs/images/dashboard.png)

### 用户管理
![User Management](docs/images/user-management.png)

### 角色权限
![Role Permission](docs/images/role-permission.png)

### 菜单管理
![Menu Management](docs/images/menu-management.png)

### 数据大屏
![Data Screen](docs/images/data-screen.png)

### 工作流设计器
![Workflow Designer](docs/images/workflow-designer.png)

---

## ✨ 功能模块

### V1.0 基础功能 ✅

| 模块 | 功能 |
|------|------|
| 👤 用户管理 | 登录/登出、个人信息、头像修改、密码修改 |
| 👥 管理员管理 | CRUD、状态启用/禁用、批量操作 |
| 🔐 角色权限 | RBAC 权限模型、菜单权限、数据权限 |
| 📑 菜单管理 | 树形菜单、可视化配置、图标选择 |
| 🏢 部门管理 | 组织架构树、隶属关系 |
| 💼 岗位管理 | 岗位 CRUD、岗位人员查询 |
| 📝 操作日志 | 详细操作记录、IP/时间追踪 |
| 🔑 登录日志 | 登录历史、异常登录告警 |
| ⚙️ 参数配置 | 系统参数、短信/邮件配置 |
| 📊 数据字典 | 静态数据管理、类型+数据两级 |
| 📁 文件上传 | 本地/OSS上传、图片预览、权限控制 |

### V2.0 企业增强功能 🚧

| 模块 | 功能 | 状态 |
|------|------|------|
| 🔄 工作流 | 可视化流程设计器、审批流、条件分支 | 🔄 开发中 |
| 📊 数据大屏 | 拖拽式大屏设计器、ECharts 图表库 | 🔄 开发中 |
| 🏢 多租户 | SaaS 模式、租户隔离、数据独立 | 🔄 开发中 |
| 📝 表单设计 | 拖拽式表单设计器、表单权限控制 | 🔄 开发中 |
| 💳 支付渠道 | 微信/支付宝支付集成、订单管理、退款 | 🔄 开发中 |
| 📱 微信渠道 | 公众号管理、菜单设计、自动回复、素材管理 | 🔄 开发中 |
| ⏰ 定时任务 | Crontab 可视化配置、任务日志 | 🔄 开发中 |
| 🔔 消息通知 | 多渠道通知（短信/邮件/站内信）、模板管理 | 🔄 开发中 |

---

## 🛠️ 技术栈

### 后端技术

| 技术 | 说明 |
|------|------|
| PHP 8.0+ | 后端核心语言 |
| ThinkPHP 8 | 高性能 PHP 框架 |
| MySQL 5.7+ | 关系型数据库 |
| Redis | 缓存、Session 存储 |
| JWT | 用户认证 Token |
| RBAC | 基于角色的权限控制 |

### 前端技术

| 技术 | 说明 |
|------|------|
| Vue 3.5 | 渐进式前端框架 |
| Vite 5 | 下一代前端构建工具 |
| Element Plus 2.0 | Vue3 UI 组件库 |
| Pinia | 新一代状态管理 |
| Vue Router 4 | 前端路由管理 |
| ECharts 5 | 数据可视化图表库 |
| Monaco Editor | 代码编辑器 |
| WangEditor | 富文本编辑器 |

---

## 📁 项目结构

```
feiyuadmin/
├── backend/                      # ThinkPHP8 后端
│   ├── app/
│   │   ├── adminapi/           # 管理后台 API
│   │   │   ├── controller/     # 控制器
│   │   │   │   ├── admin/     # 系统管理
│   │   │   │   ├── auth/      # 认证模块
│   │   │   │   ├── captcha/   # 验证码
│   │   │   │   ├── dict/      # 数据字典
│   │   │   │   ├── form/      # 表单设计
│   │   │   │   ├── generator/  # 代码生成
│   │   │   │   ├── pay/        # 支付渠道
│   │   │   │   ├── pc/         # PC端API
│   │   │   │   ├── mobile/     # 移动端API
│   │   │   │   ├── notice/     # 消息通知
│   │   │   │   ├── tenant/     # 租户管理
│   │   │   │   ├── upload/     # 文件上传
│   │   │   │   ├── wechat/     # 微信渠道
│   │   │   │   ├── workflow/    # 工作流
│   │   │   │   └── ...
│   │   │   ├── logic/          # 业务逻辑层
│   │   │   ├── validate/       # 验证器
│   │   │   └── http/
│   │   │       └── middleware/  # 中间件
│   │   ├── api/                # 开放 API（微信回调等）
│   │   ├── common/             # 公共类
│   │   │   └── service/        # 服务类
│   │   ├── model/               # 数据模型
│   │   └── service/             # 核心服务
│   ├── config/                 # 配置文件
│   ├── database/               # 数据库脚本
│   ├── route/                  # 路由配置
│   ├── public/                  # Web 入口
│   │   ├── index.php           # API 入口
│   │   ├── admin.php          # 后台入口
│   │   └── install.php        # 安装向导
│   └── think                    # 命令行工具
│
├── frontend/                   # Vue3 前端
│   ├── src/
│   │   ├── api/               # API 接口定义
│   │   ├── assets/            # 静态资源
│   │   ├── components/        # 公共组件
│   │   │   ├── common/        # 通用组件
│   │   │   └── form/          # 表单组件
│   │   ├── layout/            # 布局组件
│   │   ├── router/            # 路由配置
│   │   ├── stores/            # 状态管理
│   │   ├── utils/             # 工具函数
│   │   ├── views/             # 页面视图
│   │   │   ├── dashboard/     # 工作台
│   │   │   ├── system/        # 系统管理
│   │   │   ├── content/        # 内容管理
│   │   │   ├── member/         # 会员管理
│   │   │   ├── statistics/     # 数据统计
│   │   │   ├── tool/           # 工具模块
│   │   │   ├── wechat/         # 微信渠道
│   │   │   ├── pay/            # 支付渠道
│   │   │   ├── workflow/        # 工作流
│   │   │   ├── demo/           # 演示站
│   │   │   ├── doc/            # 文档中心
│   │   │   └── pc/             # PC端文档
│   │   ├── App.vue             # 根组件
│   │   └── main.js             # 入口文件
│   ├── .env.admin             # 管理后台环境变量
│   ├── .env.pc                 # PC端环境变量
│   ├── .env.mobile             # 移动端环境变量
│   ├── vite.config.js          # Vite 配置
│   └── package.json
│
├── docker/                     # Docker 部署
│   ├── docker-compose.yml
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
│
├── docs/                       # 开发文档
│   └── images/                 # 截图
│
└── README.md
```

---

## 🚀 快速开始

### 环境要求

| 环境 | 版本 |
|------|------|
| PHP | ≥ 8.0 |
| MySQL | ≥ 5.7 |
| Node.js | ≥ 16 |
| npm/pnpm | ≥ 7/7 |

### 后端部署

```bash
cd backend

# 安装依赖
composer install

# 配置数据库
# 编辑 config/database.php

# 导入数据库
mysql -u root -p < database/feiyuadmin.sql

# 启动服务（开发模式）
php think run
# 或使用 PHP 内置服务器
php -S 0.0.0.0:8088 -t public/
```

### 前端部署

```bash
cd frontend

# 安装依赖
npm install
# 或使用 pnpm
pnpm install

# 开发模式
npm run dev

# 生产构建
npm run build
```

### Docker 部署（推荐）

```bash
cd docker

# 构建并启动所有服务
docker-compose up -d

# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f
```

访问地址：`http://localhost:8088`

---

## 🔐 API 文档

### 接口规范

所有接口返回统一 JSON 格式：

```json
{
  "code": 0,        // 状态码，0=成功，其他=失败
  "msg": "success",  // 提示信息
  "data": {}         // 返回数据
}
```

### 认证方式

登录后获取 Token，通过 Header 携带：

```
Authorization: Bearer {token}
```

### 常用接口

| 接口 | 方法 | 说明 |
|------|------|------|
| `/adminapi/login` | POST | 用户登录 |
| `/adminapi/captcha/generate` | GET | 获取验证码 |
| `/adminapi/user/lists` | GET | 用户列表 |
| `/adminapi/role/lists` | GET | 角色列表 |
| `/adminapi/menu/lists` | GET | 菜单列表 |
| `/adminapi/menu/nav` | GET | 用户菜单 |
| `/pcapi/index/banner` | GET | 首页轮播图 |
| `/pcapi/article/lists` | GET | 文章列表 |

详细 API 文档：[在线 API 文档](http://demo.fydev.cn/api.html)

---

## 📦 相关项目

| 项目 | 说明 |
|------|------|
| [feiyuadmin](https://gitee.com/gynet/feiyuadmin) | 主项目仓库 |

---

## 🤝 参与贡献

1. Fork 本仓库
2. 新建 `feat_xxx` 分支
3. 提交代码
4. 新建 Pull Request

---

## 📄 开源协议

[MIT License](LICENSE) - 永久免费，可商用可二次开发。

---

## 📞 联系方式

- **项目演示**: http://demo.fydev.cn/demo
- **技术文档**: http://demo.fydev.cn/doc
- **后台管理**: http://demo.fydev.cn/admin
- **默认账号**: admin / admin123
- **反馈问题**: https://gitee.com/gynet/feiyuadmin/issues

---

<div align="center">

**如果这个项目对您有帮助，请 star ⭐ 支持一下！**

</div>

# feiyuadmin

#### 介绍

飞鱼后台管理系统（feiyuadmin）是一款基于 Vue3 + ThinkPHP8 开发的高性能通用后台管理框架。简洁、轻量、易扩展，适用于快速搭建企业级管理后台。

#### 软件架构

**后端**：ThinkPHP8
- MVC 架构，职责清晰
- ORM 数据库操作
- RESTful API 设计
- JWT 认证

**前端**：Vue3 + Vite
- Composition API
- Element Plus UI
- Pinia 状态管理
- Vue Router 路由

#### 技术栈

| 角色 | 技术 |
|------|------|
| 后端框架 | ThinkPHP 8.0 |
| 前端框架 | Vue 3.5 |
| UI 组件 | Element Plus |
| 构建工具 | Vite 5 |
| 状态管理 | Pinia |
| 数据库 | MySQL 5.7+ |
| PHP版本 | ≥8.0 |

#### 功能模块（V1.0）

- ✅ 用户管理（登录/登出/个人信息）
- ✅ 管理员 CRUD
- ✅ 角色权限管理
- ✅ 菜单管理
- ✅ 部门管理
- ✅ 岗位管理
- ✅ 操作日志
- ✅ 登录日志
- ✅ 参数配置
- ✅ 数据字典
- ✅ 文件上传

#### 安装教程

**环境要求**
- PHP ≥ 8.0
- MySQL ≥ 5.7
- Node.js ≥ 18
- Composer ≥ 2.0

**后端部署**

```bash
cd backend

# 安装依赖
composer install

# 配置数据库
# 编辑 config/database.php

# 导入数据库
mysql -u root -p < database/feiyuadmin.sql

# 启动服务
php think run
```

**前端部署**

```bash
cd frontend

# 安装依赖
npm install

# 开发模式
npm run dev

# 生产构建
npm run build
```

**Docker 部署**

```bash
cd docker

# 构建并启动
docker-compose up -d
```

#### 目录结构

```
feiyuadmin/
├── backend/                 # ThinkPHP8 后端
│   ├── app/
│   │   ├── controller/     # 控制器
│   │   ├── model/          # 数据模型
│   │   ├── validate/       # 验证器
│   │   └── service/        # 业务逻辑层
│   ├── config/             # 配置文件
│   ├── database/           # 数据库脚本
│   ├── route/              # 路由配置
│   └── public/              # Web 入口
│
├── frontend/               # Vue3 前端
│   ├── src/
│   │   ├── api/           # 接口调用
│   │   ├── components/     # 通用组件
│   │   ├── layout/        # 布局组件
│   │   ├── router/        # 路由配置
│   │   ├── stores/        # 状态管理
│   │   └── views/         # 页面视图
│   └── element-plus/      # UI 组件库
│
├── docker/                 # Docker 部署配置
└── docs/                   # 开发文档
```

#### 使用说明

1. 访问 `http://your-domain/admin` 进入后台登录页
2. 默认超级管理员账号：`admin` / `123456`
3. 首次登录后请修改默认密码
4. 通过「系统管理」模块管理用户、角色、菜单

#### 参与贡献

1. Fork 本仓库
2. 新建 `Feat_xxx` 分支
3. 提交代码
4. 新建 Pull Request

#### 开源协议

Apache-2.0 License

#### 联系方式

- 项目地址：https://gitee.com/gynet/feiyuadmin
- 问题反馈：https://gitee.com/gynet/feiyuadmin/issues

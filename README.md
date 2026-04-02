# 飞鱼 Admin（FeiyuAdmin）

> 🔥 **完全免费开源 · MIT 协议可商用 · 配套完整开发文档**

[![Stars](https://img.shields.io/github/stars/feiyuadmin/feiyuadmin?style=flat-square)](https://github.com/feiyuadmin/feiyuadmin)
[![License](https://img.shields.io/github/license/feiyuadmin/feiyuadmin?style=flat-square)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0+-blue?style=flat-square)](https://php.net)
[![Vue](https://img.shields.io/badge/Vue-3.5-green?style=flat-square)](https://vuejs.org)
[![ThinkPHP](https://img.shields.io/badge/ThinkPHP-8.0-orange?style=flat-square)](https://thinkphp.cn)

---

## 产品简介

飞鱼 Admin 是一款基于 **Vue3 + ThinkPHP8** 开发的高性能通用后台管理框架。简洁、轻量、易扩展，适用于独立开发者、小团队快速搭建企业级管理后台。

**在线演示：** http://39.105.173.6:8088/admin  
**技术文档：** http://39.105.173.6:8088/doc

---

## 技术架构

```
┌─────────────────────────────────────────────────────────────┐
│                        飞鱼 Admin                             │
├──────────────────────────┬────────────────────────────────┤
│        前端 Vue3         │          后端 ThinkPHP8          │
│                          │                                  │
│  Vue 3.5 + Composition  │  MVC + RESTful API              │
│  Element Plus            │  ORM 数据库操作                  │
│  Pinia 状态管理          │  JWT Token 认证                  │
│  Vite 5 构建             │  ThinkORM                       │
└──────────────────────────┴────────────────────────────────┘
                          │
                    MySQL 5.7+
```

### 技术栈

| 层级 | 技术 | 版本 |
|------|------|------|
| 后端框架 | ThinkPHP | 8.0+ |
| 前端框架 | Vue | 3.5+ |
| UI 组件 | Element Plus | 2.x |
| 构建工具 | Vite | 5.x |
| 状态管理 | Pinia | 2.x |
| 数据库 | MySQL | 5.7+ |
| PHP 版本 | PHP | 8.0+ |

---

## 功能模块

### V1.0 系统管理（已上线）

| 模块 | 说明 |
|------|------|
| 管理员管理 | 增删改查、状态启禁用 |
| 角色权限 | RBAC 权限模型、数据权限隔离 |
| 菜单管理 | 前端路由+后端权限联动 |
| 部门管理 | 树形组织架构 |
| 岗位管理 | 岗位级别与用户绑定 |
| 操作日志 | 全局操作审计记录 |
| 登录日志 | 登录时间/IP/设备记录 |
| 参数配置 | 键值对系统配置 |
| 数据字典 | 枚举类型统一管理 |
| 文件上传 | 本地/云存储驱动 |

### V2.0 扩展功能（开发中）

| 模块 | 说明 | 状态 |
|------|------|------|
| 代码生成器 | 一键生成 CRUD，开发效率提升 80% | 🔨 开发中 |
| 多租户模式 | SaaS 化租户隔离 | 🔨 开发中 |
| 定时任务 | Cron 任务调度与管理 | 🔨 开发中 |
| 工作流引擎 | 可视化流程编排（审批/填写/条件分支） | 🔨 开发中 |
| 消息通知 | 站内信/短信/邮件/钉钉/飞书 | 🔨 开发中 |
| 在线表单 | 无代码表单构建 | 🔨 开发中 |
| 数据大屏 | 可视化数据展示 | 🔨 开发中 |
| 微信渠道 | 公众号/小程序/开放平台管理 | 🔨 开发中 |
| 支付渠道 | 微信/支付宝支付配置与对账 | 🔨 开发中 |
| 富文本编辑 | WangEditor 集成 | 🔨 开发中 |

### V3.0 企业级增强（规划中）

| 模块 | 优先级 | 说明 |
|------|--------|------|
| AI 智能增强 | P0 | NL2SQL / AI 助手 / 智能报表 |
| 低代码平台 | P0 | 可视化页面构建 / 流程编排 |
| 数据中台 | P1 | ETL / 数据同步 / 数据治理 |
| SSO 单点登录 | P1 | SAML 2.0 / OIDC / LDAP |
| API 开放平台 | P1 | API 管理 / 流量控制 / 文档中心 |
| 监控告警中心 | P1 | Prometheus + Grafana |
| 审计中心 | P2 | 数据变更追踪 / 合规报表 |
| 移动端支持 | P2 | iOS/Android App / 钉钉/企微集成 |

---

## 系统截图

> 截图持续更新中，欢迎提交 PR

### V1.0 工作台

### V1.0 角色权限

### V1.0 菜单管理

---

## 快速开始

### 环境要求

- PHP ≥ 8.0
- MySQL ≥ 5.7
- Node.js ≥ 18
- Composer ≥ 2.0

### 安装步骤

**1. 克隆代码**
```bash
git clone https://github.com/feiyuadmin/feiyuadmin.git
cd feiyuadmin
```

**2. 后端部署**
```bash
cd backend
composer install
cp .env.example .env
# 修改数据库配置
php think migrate
php think seed
php think run
```

**3. 前端部署**
```bash
cd frontend
pnpm install
pnpm run dev      # 开发模式
pnpm run build     # 生产构建
```

**4. Docker 部署（推荐）**
```bash
docker-compose up -d
```

访问 http://your-domain/admin 进入管理后台。

---

## 项目结构

```
feiyuadmin/
├── backend/                  # ThinkPHP8 后端
│   ├── app/
│   │   ├── adminapi/         # 管理后台 API
│   │   ├── common/           # 公共模型
│   │   └── service/          # 业务服务层
│   ├── config/               # 配置文件
│   ├── route/                # 路由定义
│   └── public/               # Web 根目录
├── frontend/                  # Vue3 前端
│   ├── src/
│   │   ├── views/            # 页面组件
│   │   ├── router/           # 路由配置
│   │   ├── stores/           # Pinia 状态
│   │   └── utils/            # 工具函数
│   └── public/               # 静态资源
├── docs/                      # 文档
└── docker/                   # Docker 配置
```

---

## API 接口

完整 API 文档：http://39.105.173.6:8088/doc

**基础接口：**

| 接口 | 方法 | 说明 |
|------|------|------|
| `/adminapi/login` | POST | 管理员登录 |
| `/adminapi/admin/lists` | GET | 管理员列表 |
| `/adminapi/role/lists` | GET | 角色列表 |
| `/adminapi/menu/lists` | GET | 菜单列表 |
| `/adminapi/upload/image` | POST | 图片上传 |

---

## 开发指南

详细文档：http://39.105.173.6:8088/doc

### 添加新模块

**1. 创建数据表**
```sql
CREATE TABLE `fy_demo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL COMMENT '标题',
  `status` TINYINT(1) DEFAULT 1 COMMENT '状态',
  `create_time` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**2. 代码生成器**
进入「系统管理 → 代码生成器」，选择数据表，一键生成前后端代码。

**3. 注册菜单**
在「系统管理 → 菜单管理」中添加前端路由和后端接口权限。

---

## 参与贡献

1. Fork 本仓库
2. 新建 Feature 分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

---

## 联系方式

- QQ 交流群：767677830
- GitHub Issues：https://github.com/feiyuadmin/feiyuadmin/issues

---

## 许可证

本项目采用 [MIT License](LICENSE) 开源，欢迎免费商用。

---

## 版本规划

| 版本 | 定位 | 目标用户 | 状态 |
|------|------|----------|------|
| V1.0 | 系统管理核心模块 | 独立开发者、小团队 | ✅ 已上线 |
| V2.0 | 扩展功能模块 | 有一定规模的团队 | 🔨 开发中 |
| V3.0 | 企业级增强模块 | 中大型企业、SaaS | 📋 规划中 |

# V3.0 技术方案 — SSO 单点登录

**模块负责人：** 王坚（运维专员）
**评审时间：** 预计周五评审
**版本：** V3.0 P1

---

## 1. 背景与目标

当前 feiyuadmin 系统使用本地用户名/密码认证，存在以下问题：
- 多个系统需要重复注册账号，用户体验差
- 密码策略、账号安全无法统一管控
- 员工离职后需手动在各系统注销账号

**本模块目标：**
- 引入 **Keycloak** 作为统一身份提供商（IdP）
- 实现 **SSO 单点登录**：一次登录，全系统访问
- 支持 **钉钉、企业微信、Active Directory（AD）** 账号登录
- 统一管理用户生命周期（入职 → 在职 → 离职）

---

## 2. 技术选型

### 2.1 Keycloak — 统一身份认证平台

| 维度 | 说明 |
|------|------|
| 开发方 | Red Hat 开源，JBoss 社区支撑，Apache 2.0 许可证 |
| 定位 | 企业级 Identity Provider（IdP）+ Access Management |
| 协议支持 | OIDC（OAuth 2.0）、SAML 2.0、OAuth 2.0、LADP |
| 原生功能 | 用户注册、密码找回、双因素认证（OTP）、社交登录、角色管理、MFA |
| 前端集成 | 支持 JS、Java、Python、Go、Node 等主流语言 SDK |
| 部署方式 | 传统 WAR（WildFly）、Docker Compose、K8s Operator |
| 优势 | 功能完整、配置灵活、界面友好、社区活跃、国产项目可用中文文档 |
| 国产化 | 支持国产化适配（麒麟 OS + 达梦 DB） |

### 2.2 第三方身份源对接

| 身份源 | 协议 | 说明 |
|--------|------|------|
| 钉钉 | OIDC / 扫码登录 | 适用于移动办公场景 |
| 企业微信 | OIDC / 扫码登录 | 适用于企业微信组织架构同步 |
| Active Directory（AD） | LDAP | 适用于企业内部 Windows 域环境 |

---

## 3. 系统架构

```
┌──────────────────────────────────────────────────────────────┐
│                  FeiyuAdmin 用户浏览器                        │
│   访问 /admin → 未登录 → 重定向到 Keycloak 登录页            │
└──────────────────────┬───────────────────────────────────────┘
                       │
              ┌────────▼─────────┐
              │    Keycloak      │
              │  (Identity Hub)  │
              └────┬──────┬──────┘
                   │      │         ▲
         ┌────────┘      └────────┐│
         ▼                             │  OIDC / SAML
┌─────────────────┐   ┌─────────────────────┐   ┌────────────┐
│   企业微信        │   │     钉钉             │   │    AD      │
│  (WeCom)        │   │   (DingTalk)        │   │  (LDAP)    │
└─────────────────┘   └─────────────────────┘   └────────────┘
```

**登录流程：**
```
用户访问 feiyuadmin → 跳转 Keycloak → 选择身份源（本地/钉钉/企微/AD）
→ Keycloak 验证 → 颁发 JWT Token → feiyuadmin 解析 Token → 登录成功
```

---

## 4. 功能设计

### 4.1 Keycloak 部署与配置

| 功能 | 说明 |
|------|------|
| Realm 配置 | 创建 `feiyuadmin` Realm，隔离不同环境 |
| Client 配置 | 配置 feiyuadmin 为 Client（OIDC） |
| 角色映射 | feiyuadmin 角色（admin/editor/viewer）→ Keycloak Roles |
| 用户联邦 | 接入 AD / 钉钉 / 企业微信身份源 |
| 密码策略 | 复杂度、有效期、登录失败锁定 |
| 会话管理 | 单点登出（Single Logout）、会话超时 |

### 4.2 身份源集成

#### 4.2.1 钉钉集成

| 配置项 | 说明 |
|--------|------|
| 应用类型 | 企业内部开发 → PC 端网站应用 |
| OAuth2 授权 | 获取 corpId、agentId、appSecret |
| 用户信息 | 获取 userId、手机号、部门 ID |
| 同步组织架构 | 通过钉钉 API 同步部门数据 |

#### 4.2.2 企业微信集成

| 配置项 | 说明 |
|--------|------|
| 应用类型 | 网页应用 |
| OAuth2 授权 | 获取 corpId、agentId、secret |
| 用户信息 | 获取 userId、手机号、部门 ID |
| 通讯录同步 | 获取部门成员列表 |

#### 4.2.3 AD / LDAP 集成

| 配置项 | 说明 |
|--------|------|
| LDAP 服务器 | `ldap://your-ad-server:389` |
| 搜索基础 DN | `DC=company,DC=com` |
| 绑定 DN | `CN=admin,CN=Users,DC=company,DC=com` |
| 用户过滤器 | `(&(objectClass=user)(memberOf=CN=feiyuadmin,OU=Groups,DC=company,DC=com))` |

### 4.3 FeiyuAdmin 集成

| 功能 | 说明 |
|------|------|
| OIDC 登录 | 改造现有登录页面，支持 Keycloak OIDC 授权码流程 |
| Token 解析 | 解析 JWT，获取用户名、邮箱、角色、部门 |
| 角色映射 | Keycloak Roles → feiyuadmin 权限体系 |
| 单点登出 | 登出时同步 Keycloak 会话销毁 |
| 会话续期 | Access Token 过期自动刷新 |

### 4.4 用户生命周期管理

| 场景 | 处理方式 |
|------|----------|
| 新员工入职 | Keycloak 自动创建账号（或手动），分配默认角色 |
| 员工转岗 | 更新 Keycloak 角色，权限自动跟随 |
| 员工离职 | Keycloak 禁用账号，自动同步到 feiyuadmin（会话立即失效） |
| 密码找回 | Keycloak 自助服务，邮件/短信验证码 |

---

## 5. 部署方案

### 5.1 Keycloak 部署（Docker Compose）

```yaml
keycloak:
  image: quay.io/keycloak/keycloak:latest
  container_name: feiyu_keycloak
  environment:
    KEYCLOAK_ADMIN: admin
    KEYCLOAK_ADMIN_PASSWORD: ${KEYCLOAK_ADMIN_PASSWORD:-Admin@123}
    KC_DB: postgres
    KC_DB_URL: jdbc:postgresql://postgres:5432/keycloak
    KC_DB_USERNAME: keycloak
    KC_DB_PASSWORD: ${KC_DB_PASSWORD:-keycloak123}
    KC_PROXY: edge
    KC_HTTPS_ENABLED: "true"
    KC_HTTPS_CERTIFICATE_FILE: /opt/keycloak/conf/server.crt
    KC_HTTPS_CERTIFICATE_KEY_FILE: /opt/keycloak/conf/server.key
  command: start-dev
  ports:
    - "8180:8080"   # HTTP（开发模式）
  volumes:
    - ./keycloak/themes:/opt/keycloak/themes
    - ./keycloak/conf:/opt/keycloak/conf
  depends_on:
    postgres:
      condition: service_healthy
  networks:
    - feiyu_network

postgres:
  image: postgres:15
  container_name: feiyu_keycloak_db
  environment:
    POSTGRES_DB: keycloak
    POSTGRES_USER: keycloak
    POSTGRES_PASSWORD: ${KC_DB_PASSWORD:-keycloak123}
  volumes:
    - keycloak_data:/var/lib/postgresql/data
  healthcheck:
    test: ["CMD-SHELL", "pg_isready -U keycloak"]
    interval: 10s
    timeout: 5s
    retries: 5
  networks:
    - feiyu_network
```

### 5.2 Keycloak 生产模式配置

> ⚠️ **生产环境要求：**
> - 使用正式 HTTPS（Let's Encrypt 或商业证书）
> - 数据库改为 PostgreSQL（主备）
> - 去掉 `start-dev`，使用 `start`
> - 配置 `KC_HOSTNAME_STRICT` 和反向代理（Nginx）

### 5.3 Keycloak 初始化脚本

首次部署后通过 API 配置 Realm 和 Client：

```bash
# 获取 Admin Access Token
curl -X POST "http://localhost:8180/realms/master/protocol/openid-connect/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin" \
  -d "password=Admin@123" \
  -d "grant_type=password" \
  -d "client_id=admin-cli"

# 创建 feiyuadmin Realm
# （通过 Keycloak UI 操作，详见部署文档）
```

---

## 6. 与现有系统集成

### 6.1 FeiyuAdmin 后端改造

**ThinkPHP 端集成 OIDC：**

```bash
# 安装 OIDC 库
composer require league/oauth2-client
# 或使用 OpenID Connect PHP 库
```

**新增 API：**

| 接口 | 方法 | 说明 |
|------|------|------|
| `/api/auth/oidc/login` | GET | OIDC 登录入口（跳转 Keycloak） |
| `/api/auth/oidc/callback` | GET | OIDC 回调，交换 Token |
| `/api/auth/oidc/userinfo` | GET | 获取当前用户信息 |
| `/api/auth/logout` | POST | 单点登出（销毁 Keycloak 会话） |
| `/api/auth/sso/status` | GET | SSO 登录状态查询 |

### 6.2 FeiyuAdmin 前端改造

**登录页改造：**

```
原有：用户名 + 密码 表单
改为：用户名 + 密码 表单  +  [钉钉扫码] [企微扫码] [AD登录]
```

---

## 7. 实施计划

| 阶段 | 内容 | 工期 |
|------|------|------|
| Phase 1 | Keycloak 部署 + PostgreSQL + 基础配置 | 2 天 |
| Phase 2 | FeiyuAdmin OIDC 登录改造（后端） | 3 天 |
| Phase 3 | FeiyuAdmin 前端 SSO 登录界面 | 2 天 |
| Phase 4 | 钉钉身份源集成 | 3 天 |
| Phase 5 | 企业微信身份源集成 | 3 天 |
| Phase 6 | AD/LDAP 身份源集成 | 3 天 |
| Phase 7 | 角色映射 + 用户生命周期联调 | 2 天 |
| Phase 8 | 全流程测试 + 安全测试 | 2 天 |
| **合计** | | **20 天** |

---

## 8. 风险与对策

| 风险 | 影响 | 对策 |
|------|------|------|
| 第三方身份源 API 变更 | 高 | 使用稳定版 API，预留接口适配层 |
| Keycloak 版本升级破坏兼容性 | 中 | 锁定版本，升级前完整测试 |
| SSO 登录失败无兜底 | 高 | 保留本地账号密码登录作为降级方案 |
| AD 同步延迟导致权限更新不及时 | 中 | 同步周期设置为 5 分钟，紧急场景支持手动刷新 |
| Keycloak 本身单点故障 | 高 | 生产环境配置 Keycloak 集群（2+ 主节点） |

---

## 9. 依赖关系

```
SSO（Keycloak） ──→ DolphinScheduler 统一认证
                ──→ FeiyuAdmin 单点登录
```

**Keycloak 为基础设施，需优先于其他模块部署。**

---

## 10. 安全设计

| 安全项 | 实现方式 |
|--------|----------|
| 通信加密 | 全站 HTTPS，Keycloak 与 feiyuadmin 之间 TLS 1.3 |
| Token 安全 | Access Token 15min，Refresh Token 24h，存储在 HttpOnly Cookie |
| CSRF 防护 | Keycloak 内置 CSRF Token |
| 会话劫持 | Keycloak 检测异常登录（IP/设备变更） |
| 密码存储 | Keycloak 内置（bcrypt + salt），不碰明文 |
| 审计日志 | Keycloak 完整操作审计，支持导出 |

---

## 11. 交付件

- [ ] Keycloak 部署文档（含 Docker Compose）
- [ ] 钉钉/企微/AD 集成配置手册
- [ ] FeiyuAdmin SSO 登录改造（前后端）
- [ ] 用户生命周期管理功能
- [ ] Keycloak 管理员操作手册
- [ ] 安全测试报告

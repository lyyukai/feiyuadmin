# FeiyuAdmin V3.0 — 基础设施部署指南

> 包含：Keycloak SSO | Prometheus | Grafana | Alertmanager | Canal

---

## 📋 环境要求

- **Docker**: 20.10+
- **Docker Compose**: 2.0+
- **服务器配置**: ≥ 4核8G（生产环境建议 8核16G）
- **网络**: 所有服务互通（同一 Docker 网络）

---

## 🚀 快速启动

```bash
# 1. 进入 V3.0 配置目录
cd /www/wwwroot/feiyuadmin/docker/v3.0

# 2. 复制环境变量文件（修改密码）
cp docker-compose.env .env
vim .env   # 修改 KEYCLOAK_ADMIN_PASSWORD 等敏感密码

# 3. 一键启动所有服务
docker-compose up -d

# 4. 查看服务状态
docker-compose ps

# 5. 查看所有日志
docker-compose logs -f
```

---

## 🔐 服务端口

| 服务 | 端口 | 访问地址 | 默认账号 |
|------|------|----------|----------|
| Keycloak | 8180 | http://39.105.173.6:8180 | admin / Admin@123 |
| Grafana | 3000 | http://39.105.173.6:3000 | admin / Admin@123 |
| Prometheus | 9090 | http://39.105.173.6:9090 | 无认证 |
| Alertmanager | 9093 | http://39.105.173.6:9093 | 无认证 |
| node_exporter | 9100 | http://39.105.173.6:9100/metrics | 无认证 |
| mysqld_exporter | 9104 | http://39.105.173.6:9104/metrics | 无认证 |
| redis_exporter | 9121 | http://39.105.173.6:9121/metrics | 无认证 |
| Canal Admin | 8089 | http://39.105.173.6:8089 | admin / CanalAdmin@2026 |
| Canal Server | 11110/11111 | 11110:管理, 11111:数据 | — |

---

## 🔧 详细配置

### 一、Keycloak — SSO 配置

#### 1.1 初始化 Realm

1. 访问 http://39.105.173.6:8180
2. 使用 `admin / Admin@123` 登录
3. 左上角 → **Create Realm** → 名称填写 `feiyuadmin`
4. 创建 Realm 后配置 Client（OIDC）

#### 1.2 配置 feiyuadmin Client

- **Client ID**: `feiyuadmin`
- **Client Protocol**: `openid-connect`
- **Access Type**: `public`（SPA）或 `confidential`（后端）
- **Valid Redirect URIs**: `http://demo.fydev.cn/*`
- **Web Origins**: `http://demo.fydev.cn`

#### 1.3 配置 Grafana SSO（Keycloak OIDC）

在 Keycloak 创建 Grafana Client：
- **Client ID**: `grafana`
- **Access Type**: `confidential`
- 保存 Client Secret，填入 `.env` 的 `GRAFANA_OIDC_SECRET`

#### 1.4 配置钉钉/企微身份源

Keycloak 界面 → **User Federation** → 添加 LDAP/钉钉/企微 provider

---

### 二、Prometheus 配置

#### 2.1 验证采集目标

访问 http://39.105.173.6:9090/targets

应看到：
- prometheus（自身）
- node（服务器资源）
- mysql（MySQL）
- redis（Redis）

#### 2.2 查看告警规则

访问 http://39.105.173.6:9090/rules

---

### 三、Grafana 配置

#### 3.1 导入 Dashboard

1. 访问 http://39.105.173.6:3000
2. 左侧菜单 → **Dashboards** → **Import**
3. 推荐 Dashboard ID：
   - **Node Exporter**: 1860（服务器资源）
   - **MySQL Overview**: 7362
   - **Redis**: 11835

#### 3.2 配置告警通知渠道

1. **Grafana** → **Alerting** → **Contact points**
2. 添加钉钉机器人 Webhook URL
3. 配置告警路由规则

---

### 四、Alertmanager 配置

#### 4.1 钉钉机器人配置

1. 钉钉群 → 群设置 → 智能群助手 → 添加机器人
2. 选择「自定义」机器人
3. 安全设置 → 加签（填入 `secret`）
4. 复制 Webhook URL 到 `alertmanager.yml`

```yaml
# alertmanager.yml 修改
webhook_configs:
  - url: 'https://oapi.dingtalk.com/robot/send?access_token=YOUR_TOKEN'
    send_resolved: true
```

#### 4.2 验证告警

```bash
# 触发测试告警
curl -X POST http://localhost:9093/api/v1/alerts \
  -H 'Content-Type: application/json' \
  -d '[{"labels":{"alertname":"TestAlert","severity":"critical"},"annotations":{"summary":"Test alert"}}]'
```

---

### 五、Canal 配置

#### 5.1 创建 MySQL Canal Manager 数据库

```sql
CREATE DATABASE IF NOT EXISTS canal_manager DEFAULT CHARACTER SET utf8mb4;
```

#### 5.2 Canal Server 注册到 Admin

Canal Server 启动后自动注册到 Canal Admin（通过 `canal.admin.manager` 配置）。

#### 5.3 配置数据源实例（Admin UI）

1. 访问 http://39.105.173.6:8089
2. 添加 MySQL 数据源实例
3. 配置 binlog 订阅规则

#### 5.4 Canal 权限要求

Canal 连接 MySQL 需要以下权限：

```sql
GRANT SELECT, REPLICATION SLAVE, REPLICATION CLIENT ON *.* TO 'canal'@'%' IDENTIFIED BY 'canal123';
FLUSH PRIVILEGES;
```

---

## 🛑 停止与清理

```bash
# 停止服务（保留数据）
docker-compose stop

# 完全清理（删除数据卷）
docker-compose down -v

# 删除镜像
docker-compose down --rmi local
```

---

## 🔄 升级

```bash
# 拉取最新镜像
docker-compose pull

# 重启服务
docker-compose up -d
```

---

## ⚠️ 生产环境注意事项

1. **修改所有默认密码**
2. **配置 HTTPS**（Nginx 反向代理）
3. **数据卷定期备份**
4. **Prometheus 数据保留策略**（默认15天，可调）
5. **Alertmanager 钉钉/企微 Token** 加密存储
6. **Keycloak 生产模式**：去掉 `start-dev`，配置 HTTPS + PostgreSQL 主备
7. **资源限制**：生产环境为每个容器配置 `deploy.resources.limits`

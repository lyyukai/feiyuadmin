# FeiyuAdmin V3.0 — P1 模块总览

**版本：** V3.0 | **负责人：** 王坚（运维专员）

---

## 📊 模块概览

| 模块 | 技术栈 | 优先级 | 工作量 | 预计工期 |
|------|--------|--------|--------|----------|
| 数据中台 | DataX + DolphinScheduler | P1 | 中 | 15 天 |
| SSO 单点登录 | Keycloak + 钉钉/企微/AD | P1 | 高 | 20 天 |
| 监控告警中心 | Prometheus + Grafana | P1 | 中 | 15 天 |

---

## 🔗 模块依赖关系

```
Keycloak（SSO） ─────→ DolphinScheduler 统一认证
                 ─────→ Grafana 统一认证（SSO）
                 ─────→ FeiyuAdmin 单点登录

Prometheus ──────────→ DolphinScheduler 任务状态监控
     │
     └────────────────→ Alertmanager 告警触发

Canal ────────────────→ DataX 增量数据同步基础
```

**建议并行推进顺序：**
1. **第一周**：Keycloak（SSO）+ Prometheus（监控）并行部署
2. **第二周**：Grafana + Alertmanager + Canal
3. **第三周**：DataX + DolphinScheduler 集成

---

## 📁 文档清单

| 文档 | 路径 |
|------|------|
| 数据中台技术方案 | `docs/V3.0/P1-数据中台技术方案.md` |
| SSO 单点登录技术方案 | `docs/V3.0/P1-SSO单点登录技术方案.md` |
| 监控告警中心技术方案 | `docs/V3.0/P1-监控告警中心技术方案.md` |
| Docker Compose 模板 | `docker/v3.0/docker-compose.yml` |
| 环境变量配置 | `docker/v3.0/docker-compose.env` |
| 基础设施部署指南 | `docker/v3.0/README.md` |
| Prometheus 告警规则 | `docker/v3.0/prometheus/rules/alerts.yml` |
| Alertmanager 配置 | `docker/v3.0/alertmanager/alertmanager.yml` |
| Canal Server 配置 | `docker/v3.0/canal/conf/canal.properties` |

---

## 🎯 周五评审议程

1. **各模块技术方案讲解**（每个模块 5 分钟）
2. **技术选型评审**（可行性、风险）
3. **实施计划讨论**（资源分配、工期确认）
4. **决策事项确认**（关键技术选型、优先级调整）

# FeiyuAdmin V4.0 详细规划文档

> 文档版本：V4.0 详细规划  
> 产品经理：马化腾  
> 创建时间：2026-04-05  
> 状态：**待技术评审**  
> 文档路径：`/www/wwwroot/feiyuadmin/docs/V4.0/`

---

## 目录

1. [产品愿景与战略定位](#1-产品愿景与战略定位)
2. [市场需求分析](#2-市场需求分析)
3. [AI Agent 智能助手（ P0）](#3-ai-agent-智能助手-p0)
4. [低代码建表（ P0）](#4-低代码建表-p0)
5. [工作流引擎（ P1）](#5-工作流引擎-p1)
6. [数据大屏（ P1）](#6-数据大屏-p1)
7. [定时任务可视化（ P1）](#7-定时任务可视化-p1)
8. [技术架构设计](#8-技术架构设计)
9. [数据库设计](#9-数据库设计)
10. [API 接口设计](#10-api-接口设计)
11. [里程碑计划](#11-里程碑计划)
12. [资源分配](#12-资源分配)
13. [风险评估](#13-风险评估)
14. [验收标准](#14-验收标准)

---

## 1. 产品愿景与战略定位

### 1.1 产品愿景

**让管理员"说出来就能做到"——用自然语言驱动整个后台。**

V4 是 FeiyuAdmin 从"后台管理工具"升级为"智能化管理平台"的关键版本。核心逻辑：

```
传统方式：打开菜单 → 找到页面 → 填写表单 → 点击提交（平均 5-7 步）
AI 方式：   输入"帮我新建一个管理员" → 确认执行（2 步）
```

### 1.2 战略定位

| 维度 | 定位 |
|------|------|
| **目标用户** | 中小企业技术负责人、创业团队、甲方信息化负责人 |
| **使用场景** | 私有化部署的内部管理系统（SaaS 运营商、企业自用） |
| **竞争策略** | 开源免费 + AI 智能化 + 私有化部署，差异化于钉钉/飞书/简道云 |
| **商业模式** | V4 基础能力开源免费，高级功能（多租户 SaaS、工作流企业版、数据分析增强）商业授权 |

### 1.3 版本定位

```
V1.0：基础 RBAC  →  证明系统能跑通
V2.0：企业增强   →  证明功能全
V3.0：基础设施   →  证明架构稳
V4.0：智能化     →  证明产品聪明 ← 【当前版本】
```

---

## 2. 市场需求分析

### 2.1 目标客户痛点

| 痛点 | 现状 | V4 解决 |
|------|------|---------|
| 会用但记不住菜单路径 | 常用功能藏在三级菜单里 | 自然语言直达 |
| 临时统计要写 SQL | 找开发/ DBA 协助 | NL2SQL 自助分析 |
| 业务变更需要改代码 | 开发排期等待 3-7 天 | 低代码自助建表 |
| 审批流程靠微信/纸质 | 效率低、无记录 | 工作流引擎 |

### 2.2 竞品功能对比

| 功能 | 织信 | 简道云 | 伙伴云 | FeiyuAdmin V4 |
|------|------|--------|--------|----------------|
| 开源免费 | ❌ | ❌ | ❌ | ✅ |
| 私有化部署 | ✅ | ❌ | ❌ | ✅ |
| NL2SQL 查询 | ❌ | ✅ | ❌ | ✅ |
| 自然语言 CRUD | ❌ | ❌ | ❌ | ✅ |
| 图形建表 | 表单级 | 表单级 | 表级 | ✅ 表级+CRUD |
| 拖拽工作流 | ✅ | 审批流 | 审批流 | ✅ DAG 设计器 |
| 数据大屏 | ✅ | ❌ | ✅ | ✅ |

---

## 3. AI Agent 智能助手（ P0）

### 3.1 产品原型描述

#### 3.1.1 界面布局

```
┌─────────────────────────────────────────────────────────────────┐
│  AI 助手                                              [历史] [设置] │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─ 用户 ─────────────────────────────┐                          │
│  │ 帮我查询所有管理员账号               │                          │
│  └────────────────────────────────────┘                          │
│                                                                   │
│  ┌─ AI ─────────────────────────────────────────────────────────┐ │
│  │ ✓ 已为您查询到 12 个管理员账号：                                │ │
│  │                                                             │ │
│  │  账号        姓名     角色      状态    最后登录               │ │
│  │  admin      管理员    超级管理员  启用   2026-04-05 10:30     │ │
│  │  zhangsan   张三     运营       启用   2026-04-04 15:22      │ │
│  │  ...                                                        │ │
│  │                                                             │ │
│  │ [导出 Excel]  [查看详情]                                     │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌─ 用户 ─────────────────────────────┐                          │
│  │ 再新建一个，账号 test001             │                          │
│  └────────────────────────────────────┘                          │
│                                                                   │
│  ┌─ AI ─────────────────────────────────────────────────────────┐ │
│  │ 📋 即将执行以下操作：                                          │ │
│  │                                                             │ │
│  │  INSERT INTO sys_user (username, nickname, role_id, status)   │ │
│  │  VALUES ('test001', 'test001', 2, 1)                         │ │
│  │                                                             │ │
│  │  ⚠️ 确认执行？ [取消] [确认执行]                               │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│  [🤖 输入问题，或选择快捷操作 ▼]                            [发送] │
└─────────────────────────────────────────────────────────────────┘
```

#### 3.1.2 快捷操作面板（下拉展开）

```
快捷操作：
  📋 查询类
    · 查询所有管理员
    · 查看操作日志
    · 统计今日新增用户

  ➕ 新建类
    · 新建管理员账号
    · 新建角色
    · 新建菜单

  ✏️ 修改类
    · 批量启用/禁用用户
    · 修改角色权限

  🗑️ 删除类
    · 删除过期日志
```

### 3.2 功能详情

#### 3.2.1 意图识别

| 意图类型 | 关键词示例 | 系统行为 |
|----------|-----------|----------|
| 查询 SELECT | "查询"、"看看"、"有哪些"、"统计" | NL2SQL 执行 → 展示结果 |
| 新增 INSERT | "新建"、"创建"、"增加"、"添加" | 生成 SQL → 预览确认 → 执行 |
| 更新 UPDATE | "修改"、"改成"、"更新"、"启用/禁用" | 生成 SQL → 预览确认 → 执行 |
| 删除 DELETE | "删除"、"清理"、"移除" | 禁止自动执行 → 提示手动操作 |
| 导出 EXPORT | "导出"、"下载" | 生成文件 → 浏览器下载 |
| 解释 EXPLAIN | "解释一下"、"为什么"、"什么意思" | LLM 自然语言回复 |

#### 3.2.2 高危操作拦截规则

```
🚫 以下操作 AI 不自动执行，需用户手动操作：
   - DELETE 语句（无论单条还是批量）
   - TRUNCATE / DROP TABLE
   - 修改管理员自己的密码
   - 修改系统核心配置

⚠️ 以下操作 AI 预览确认后执行：
   - INSERT（新增数据）
   - UPDATE（修改数据，金额/密码字段需二次确认）
   - 批量操作（影响 > 10 条数据）
```

#### 3.2.3 多轮对话上下文

```
Session 存储结构（Redis）：
{
  session_id: "uuid-xxx",
  user_id: 1,
  context: [
    { role: "user", content: "帮我新建管理员 test001", sql_preview: "...", timestamp: ... },
    { role: "assistant", content: "已执行成功", sql: "...", timestamp: ... }
  ],
  last_table: "sys_user",
  last_record_id: 15,
  expires_at: timestamp + 30min
}
```

### 3.3 技术方案

#### 3.3.1 系统架构

```
┌─────────────────────────────────────────────────────────────────┐
│                        前端（Vue3）                              │
│   ChatWindow │ ContextPanel │ ResultTable │ ConfirmDialog        │
└────────────────────────────┬────────────────────────────────────┘
                             │ WebSocket + HTTP
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    AI Gateway（路由层）                           │
│   IntentClassifier │ ActionRouter │ ResponseFormatter            │
│                                                            ▲    │
│   - LLM 调用（OpenAI/Claude/通义/文心/Kimi）                    │    │
│   - Prompt Engineering（System Prompt 模板）                     │    │
│   - 意图分类模型（小模型做粗筛，大模型做精排）                   │    │
└────┬──────────┬──────────┬──────────┬──────────┬───────────────┘    │
     │          │          │          │          │                     │
     ▼          ▼          ▼          ▼          ▼                     │
┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐               │
│NL2Sql   │ │LowCode  │ │Export   │ │Search   │ │Workflow │               │
│Logic    │ │Logic    │ │Logic    │ │Logic    │ │Logic    │               │
└────┬────┘ └────┬────┘ └────┬────┘ └────┬────┘ └────┬────┘               │
     │          │          │          │          │                     │
     ▼          ▼          ▼          ▼          ▼                     │
┌─────────────────────────────────────────────────────────────────┐
│                      现有 FeiyuAdmin 后端                          │
│   Nl2SqlLogic │ LowCodeEngine │ FileExport │ MenuService │ ...     │
└─────────────────────────────────────────────────────────────────┘
```

#### 3.3.2 Prompt 模板（系统级）

```yaml
system_prompt: |
  你是 FeiyuAdmin 的 AI 助手，专注于帮助管理员操作后台系统。

  数据库表结构（已知）：
  - sys_user: id, username, nickname, password, salt, role_id, dept_id, status, create_time, last_login_time
  - sys_role: id, name, remark, status, create_time
  - sys_menu: id, pid, name, path, icon, component, menu_type, status, sort
  - sys_dept: id, pid, name, leader, phone, status, create_time
  - sys_post: id, name, code, status, create_time
  - sys_dict: id, type, label, value, sort, status
  - sys_config: id, name, key, value, type, remark
  - sys_log_operation: id, module, operation, method, url, ip, user_id, param, result, cost_time, create_time
  - sys_log_login: id, username, ip, user_agent, status, msg, create_time

  角色说明：
  - role_id=1: 超级管理员（拥有所有权限）
  - role_id=2: 运营（运营相关模块）
  - role_id=3: 财务（财务相关模块）
  - role_id=4: 普通员工（基础权限）

  操作规则：
  1. 只生成 SELECT/UPDATE/INSERT 查询，不生成 DELETE/DROP/TRUNCATE
  2. UPDATE 操作需列出所有字段的变更内容
  3. INSERT 操作需列出所有插入的字段和值
  4. 时间字段使用 DATETIME 格式：YYYY-MM-DD HH:mm:ss
  5. 金额/数字字段不加引号
  6. 返回格式为 JSON：{"sql": "...", "explanation": "...", "type": "SELECT|UPDATE|INSERT"}

  示例：
  用户: 查询所有启用的管理员
  响应: {"sql": "SELECT u.*, r.name as role_name FROM sys_user u LEFT JOIN sys_role r ON u.role_id=r.id WHERE u.status=1 AND u.role_id=1", "explanation": "查询所有状态为启用的超级管理员账号", "type": "SELECT"}
```

### 3.4 LLM 选型（待凯哥确认）

| 模型 | 优点 | 缺点 | 推荐场景 |
|------|------|------|----------|
| **OpenAI GPT-4o** | 意图识别准、中文好 | 需翻墙、成本高 | 追求效果的首选 |
| **Claude 3.5 Sonnet** | 代码能力强、幻觉少 | 国内访问受限 | 复杂 SQL 生成 |
| **通义千问 2.5** | 国内合规、免费额度大 | 复杂意图识别一般 | 国内首选 |
| **文心一言 4.0** | 百度生态集成 | 费用较高 | 已用百度云的企业 |
| **Kimi Moonshot** | 长上下文、免费 | 复杂推理一般 | 查询分析为主 |

**推荐方案（凯哥确认后执行）：**
- 正式环境：通义千问 2.5（国内合规）或 OpenAI GPT-4o（效果优先）
- 测试环境：通义千问免费额度
- 降级策略：API 超时 5s → 自动切换备选模型

---

## 4. 低代码建表（ P0）

### 4.1 产品原型描述

#### 4.1.1 建表流程（5 步）

```
步骤 1：基本信息                步骤 2：字段设计              步骤 3：关联关系
┌──────────────────────┐       ┌────────────────────────┐    ┌─────────────────────┐
│  表名称：[订单管理    ]│       │  ┌────┐ ┌────┐ ┌────┐ │    │  主表：order         │
│  表注释：[订单主表    ]│       │  │+文本│ │+数字│ │+日期│ │    │                      │
│  表前缀：[order_    ] │       │  └────┘ └────┘ └────┘ │    │  关联：order_item    │
│                      │       │  ┌────┐ ┌────┐ ┌────┐ │    │  关系：一对多         │
│  字符集：[utf8mb4  ▼]│       │  │+下拉│ │+图片│ │+富文本││    │  关联键：order_id    │
│                      │       │  └────┘ └────┘ └────┘ │    │                      │
│  [下一步]            │       │                         │    │  [添加关联]          │
└──────────────────────┘       │  ─────────────────────  │    │  [下一步]            │
                                │  order_no  订单号  📝   │    └─────────────────────┘
步骤 4：预览确认                │  amount    金额    💰   │
┌──────────────────────┐       │  status    状态    📋   │    步骤 5：生成完成
┌──────────────────────┐       │  create_time 创建时间 🕐 │    ┌─────────────────────┐
│ CREATE TABLE ...     │       │  + 添加更多字段          │    │  ✅ 生成成功！        │
│   order_no VARCHAR   │       │                         │    │                      │
│   amount DECIMAL     │       │  [保存草稿] [下一步]    │    │  已生成文件：        │
│   status TINYINT     │       └────────────────────────┘    │  - order/index.vue   │
│   create_time DATETIME│                                      │  - order/add.vue     │
│ ) ENGINE=InnoDB      │                                       │  - order/edit.vue    │
│                      │                                       │                      │
│ [上一步] [确认创建]  │                                       │  [预览代码] [直接添加到菜单]│
└──────────────────────┘                                       └─────────────────────┘
```

#### 4.1.2 字段类型说明

| 字段类型 | 适用场景 | 示例 |
|----------|-----------|------|
| 文本 (VARCHAR) | 短文本 | 用户名、手机号、邮箱 |
| 长文本 (TEXT) | 长文本 | 备注、描述 |
| 数字 (DECIMAL/INT) | 金额、数量 | 价格、库存、年龄 |
| 日期时间 (DATETIME) | 时间字段 | 创建时间、更新时间 |
| 日期 (DATE) | 日期 | 生日、到期日期 |
| 开关 (TINYINT) | 状态 | 启用/禁用 |
| 单选 (ENUM) | 固定选项 | 性别、订单状态 |
| 图片 (VARCHAR) | 图片路径 | 头像、商品图 |
| 富文本 (TEXT) | 富文本内容 | 文章详情 |
| 外键关联 | 关联其他表 | 关联用户、关联部门 |

### 4.2 功能详情

#### 4.2.1 CRUD 页面生成规范

每张表生成以下文件：

```
backend/app/adminapi/controller/自动生成/{TableName}Controller.php
backend/app/adminapi/logic/自动生成/{TableName}Logic.php
backend/app/adminapi/validate/自动生成/{TableName}Validate.php
backend/app/adminapi/model/自动生成/{TableName}Model.php

frontend/src/views/auto/{table_name}/index.vue      （列表页）
frontend/src/views/auto/{table_name}/add.vue        （新增页）
frontend/src/views/auto/{table_name}/edit.vue       （编辑页）
```

**列表页标准功能：**
- 分页列表（每页 20 条，可配置）
- 搜索栏（根据字段配置搜索条件）
- 筛选器（状态下拉、日期范围）
- 批量操作（批量删除、批量启用/禁用）
- 行内操作（查看、编辑、删除）
- 导出 Excel（可选）

**新增/编辑页标准功能：**
- 表单验证（根据字段类型自动生成规则）
- 图片上传（自动集成七牛/本地存储）
- 富文本编辑（集成 wangeditor）
- 关联记录选择（弹窗选择关联数据）

#### 4.2.2 代码生成模板

```
后端 Controller 模板：
- 继承 BaseAdminController
- 包含：lists（列表）、info（详情）、save（新增）、update（更新）、delete（删除）
- 自动注入 Logic 层
- 自动注入 Validate 验证

前端页面模板：
- 使用 Element Plus 组件
- 使用 FeiyuAdmin 统一 API 调用（request.js）
- 使用 FeiyuAdmin 统一表单组件（fs-form）
- 使用 FeiyuAdmin 统一表格组件（fs-table）
- 自动注册路由（router/addRoutes）
```

### 4.3 数据库设计

#### 4.3.1 核心数据表

```sql
-- 低代码建表配置表
CREATE TABLE `sys_lowcode_table` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL COMMENT '表名（不含前缀）',
  `table_comment` varchar(255) NOT NULL COMMENT '表注释',
  `table_prefix` varchar(50) NOT NULL DEFAULT 'sys_' COMMENT '表前缀',
  `columns` json NOT NULL COMMENT '字段配置JSON',
  `relations` json DEFAULT NULL COMMENT '关联关系JSON',
  `generate_type` tinyint NOT NULL DEFAULT 1 COMMENT '生成方式：1=生成并执行，2=仅生成代码',
  `menu_pid` bigint unsigned DEFAULT NULL COMMENT '上级菜单ID',
  `menu_name` varchar(100) DEFAULT NULL COMMENT '菜单名称',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：0=禁用，1=启用',
  `version` int NOT NULL DEFAULT 1 COMMENT '版本号',
  `creator_id` bigint unsigned DEFAULT NULL COMMENT '创建者ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_table_name` (`table_name`)
) ENGINE=InnoDB COMMENT='低代码建表配置';

-- 字段配置 JSON 结构
-- columns 字段内容示例：
[
  {
    "name": "order_no",
    "comment": "订单号",
    "type": "varchar",
    "length": 50,
    "default": null,
    "required": true,
    "unique": true,
    "searchable": true,
    "list_show": true,
    "form_show": true,
    "form_type": "text",
    "validation_rule": "/^[A-Z]{4}\\d{10}$/",
    "ui_component": "input"
  },
  {
    "name": "amount",
    "comment": "订单金额",
    "type": "decimal",
    "length": "10,2",
    "default": "0.00",
    "required": true,
    "searchable": false,
    "list_show": true,
    "form_show": true,
    "form_type": "number",
    "ui_component": "input-number"
  }
]
```

### 4.4 技术实现

#### 4.4.1 代码生成器架构

```
┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│  TableConfig │ ──→ │ CodeGenerator    │ ──→ │ 生成文件列表      │
│  (PHP 对象)   │     │ (PHP 模板引擎)    │     │ (.vue/.php 文件) │
└──────────────┘     └──────────────────┘     └──────────────────┘
                             │
         ┌───────────────────┼───────────────────┐
         ▼                   ▼                   ▼
  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
  │ Vue模板     │    │ PHP模板      │    │ SQL模板      │
  │ (blade)     │    │ (blade)     │    │ (建表语句)   │
  └─────────────┘    └─────────────┘    └─────────────┘
```

#### 4.4.2 模板引擎选型

使用 `Topetta\Blade` 或自研简易模板引擎，核心替换规则：

```
{{CONTROLLER_NAME}}  →  OrderController
{{TABLE_NAME}}        →  order
{{MODEL_NAME}}        →  OrderModel
{{COLUMNS}}           →  字段列表渲染
{{FORM_FIELDS}}       →  表单 HTML
{{TABLE_HEADERS}}     →  表格列定义
```

---

## 5. 工作流引擎（ P1）

### 5.1 产品原型描述

#### 5.1.1 流程设计器（ DAG 可视化）

```
┌──────────────────────────────────────────────────────────────────────────┐
│  流程设计器 - 请假申请流程                              [保存] [发布] [预览] │
├──────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐                                                       │
│  │ 组件库      │   ┌─────────────┐                                      │
│  │             │   │ 开始 ○      │                                      │
│  │ ○ 开始节点  │   └──────┬──────┘                                      │
│  │ ○ 审批节点  │          │                                               │
│  │ ○ 条件节点  │          ▼                                               │
│  │ ○ 处理节点  │   ┌─────────────┐      ┌─────────────┐                  │
│  │ ○ 通知节点  │   │ 审批人:     │ YES  │ 审批人:     │                  │
│  │ ○ 结束节点  │   │ 部门主管    ├─────→│ 总经理      │                  │
│  │             │   └──────┬──────┘      └──────┬──────┘                  │
│  │             │          │ NO                   │                        │
│  │             │          ▼                      ▼                        │
│  │             │   ┌─────────────┐         ┌─────────────┐               │
│  │             │   │ 条件:       │         │ 结束 ◉       │               │
│  │             │   │ 天数 <= 3   │         └─────────────┘               │
│  │             │   └─────────────┘                                      │
│  └─────────────┘                                                        │
│                                                                          │
│  ──────────────────────────────────────────────────────────────────────  │
│  属性面板                                                               │
│  当前选中: [审批人: 部门主管]                                             │
│                                                                          │
│  审批人设置:                                                             │
│    ○ 指定人员   ○ 角色    ● 部门主管   ○ 表单字段                        │
│                                                                          │
│  审批方式:                                                               │
│    ● 会签（所有人审批通过）  ○ 或签（一人通过即可）                         │
│                                                                          │
│  处理期限: [3] 天，超时提醒  [✓]                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

#### 5.1.2 审批中心（用户视角）

```
┌────────────────────────────────────────────────────────────────┐
│  我的审批                                    [全部] [待办] [已办] [我发起的] │
├────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 📋 请假申请 - 张三申请年假 5 天                           │  │
│  │                                                          │  │
│  │  申请人：张三   部门：运营部   申请时间：2026-04-05 10:00 │  │
│  │  ─────────────────────────────────────────────────────── │  │
│  │  请假类型：年假        请假天数：5 天                      │  │
│  │  开始日期：2026-04-10  结束日期：2026-04-15              │  │
│  │  请假事由：家庭旅行                                            │  │
│  │  ───────────────────────────────────────────────────────  │  │
│  │  审批流程：部门主管(待审批) → 总经理(未开始)                 │  │
│  │                                                          │  │
│  │              [查看详情]  [拒绝]  [通过]                    │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 📋 报销申请 - 李四差旅费报销 ¥3000                        │  │
│  │  ...                                                       │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────┘
```

### 5.2 功能详情

#### 5.2.1 节点类型

| 节点类型 | 图标 | 功能描述 |
|----------|------|----------|
| **开始节点** | ○ | 流程入口，每个流程有且仅有一个开始节点 |
| **审批节点** | ◇ | 指定审批人，审批人可为：指定人员/角色/部门主管/表单字段 |
| **条件节点** | ◇ | 条件分支，支持：等于/不等于/大于/小于/包含/为空 |
| **处理节点** | □ | 自动执行动作，如：修改字段值、发送通知 |
| **通知节点** | □ | 发送站内消息/邮件/短信 |
| **结束节点** | ◉ | 流程结束，每个流程可有一个或多个结束节点 |

#### 5.2.2 条件表达式

```
支持的条件：
  - 数值比较：amount > 10000, days <= 3
  - 字符串：status == 'pending', name.contains('经理')
  - 日期：create_time > '2026-01-01'
  - 组合：amount > 10000 AND status == 'pending' OR role_id == 1

条件组（嵌套）：
  IF (金额 > 10000) THEN
    IF (是特殊客户) THEN 总经理审批
    ELSE 财务总监审批
  ELSE
    部门主管审批
```

#### 5.2.3 审批规则

```
会签（All）：所有审批人全部通过，流程才到下一步
或签（Any）：任一审批人通过，流程就到下一步
顺序审批：按配置的顺序，一步步审批
自动通过：审批人在一定期限未处理，自动通过（可选）
驳回：审批人可以驳回到起点或指定节点
委托：审批人可以委托他人代为审批
```

### 5.3 数据库设计

```sql
-- 流程定义表
CREATE TABLE `sys_workflow_definition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '流程名称',
  `key` varchar(50) NOT NULL COMMENT '流程标识（唯一）',
  `category` varchar(50) DEFAULT NULL COMMENT '流程分类',
  `description` text COMMENT '流程描述',
  `form_id` bigint unsigned DEFAULT NULL COMMENT '关联表单ID',
  `form_fields` json DEFAULT NULL COMMENT '表单字段定义',
  `nodes` json NOT NULL COMMENT '节点配置JSON',
  `edges` json NOT NULL COMMENT '连线配置JSON',
  `variables` json DEFAULT NULL COMMENT '流程变量定义',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '状态：0=草稿，1=已发布，2=已禁用',
  `version` int NOT NULL DEFAULT 1 COMMENT '版本号',
  `creator_id` bigint unsigned DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key_version` (`key`,`version`)
) ENGINE=InnoDB COMMENT='流程定义';

-- 流程节点配置（nodes 字段 JSON 结构）
-- [
--   { "id": "node_1", "type": "start", "name": "开始", "x": 100, "y": 200 },
--   { "id": "node_2", "type": "task", "name": "部门主管审批", "assignee": "dept_leader", "assigneeType": "role" },
--   { "id": "node_3", "type": "condition", "name": "条件判断", "conditions": [...] },
--   { "id": "node_4", "type": "end", "name": "结束" }
-- ]

-- 流程实例表
CREATE TABLE `sys_workflow_instance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `definition_id` bigint unsigned NOT NULL COMMENT '流程定义ID',
  `definition_version` int NOT NULL DEFAULT 1 COMMENT '流程定义版本',
  `business_key` varchar(100) DEFAULT NULL COMMENT '业务键（如 order_id）',
  `business_id` bigint unsigned DEFAULT NULL COMMENT '业务ID',
  `title` varchar(255) NOT NULL COMMENT '流程标题',
  `form_data` json DEFAULT NULL COMMENT '表单数据快照',
  `current_node_id` varchar(50) DEFAULT NULL COMMENT '当前节点ID',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1=进行中，2=已完成，3=已取消，4=已驳回',
  `start_user_id` bigint unsigned NOT NULL COMMENT '发起人',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_definition_id` (`definition_id`),
  KEY `idx_business_key` (`business_key`,`business_id`)
) ENGINE=InnoDB COMMENT='流程实例';

-- 流程任务表（待审批任务）
CREATE TABLE `sys_workflow_task` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` bigint unsigned NOT NULL COMMENT '流程实例ID',
  `node_id` varchar(50) NOT NULL COMMENT '节点ID',
  `node_name` varchar(100) NOT NULL COMMENT '节点名称',
  `assignee_type` varchar(20) NOT NULL COMMENT '办理类型：user/role/dept_head/form_field',
  `assignee_id` varchar(50) DEFAULT NULL COMMENT '办理人ID或角色ID',
  `task_type` tinyint NOT NULL DEFAULT 1 COMMENT '任务类型：1=正常，2=转发，3=委托',
  `parent_task_id` bigint unsigned DEFAULT NULL COMMENT '父任务ID（转发/委托）',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1=待处理，2=处理中，3=已处理，4=已取消',
  `action` varchar(20) DEFAULT NULL COMMENT '处理动作：agree/disagree/transfer/delegated/reject',
  `comment` text COMMENT '审批意见',
  `claim_time` datetime DEFAULT NULL COMMENT '签收时间',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `due_date` datetime DEFAULT NULL COMMENT '到期时间',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_instance_id` (`instance_id`),
  KEY `idx_assignee` (`assignee_type`,`assignee_id`)
) ENGINE=InnoDB COMMENT='流程任务';
```

### 5.4 工作流引擎技术选型

**自研 DAG 引擎，不依赖第三方**（BPMN.js 方案讨论中）

| 方案 | 优点 | 缺点 | 推荐 |
|------|------|------|------|
| **自研（轻量 DAG）** | 简单、可控、不引入重依赖 | 需要开发时间 | ✅ **推荐** |
| **flowable** | 功能完善、BPMN 标准 | 重量级、学习成本 | ❌ |
| **ruoyi-flowable** | 集成好的 | 依赖重 | ❌ |
| **钉钉审批 API** | 体验好 | 需钉钉账号 | ❌ |

**轻量 DAG 引擎设计：**
```
核心概念：
  - Node（节点）：每种类型有对应 Handler
  - Edge（连线）：包含 source/target/condition
  - Context（上下文）：流程变量存储
  - Token（令牌）：追踪流程执行位置

执行器：
  WorkflowEngine::start($definitionId, $formData) → 创建实例 → 推进 Token
  WorkflowEngine::approve($taskId, $action, $comment) → 处理任务 → 推进 Token
  WorkflowEngine::reject($taskId, $comment) → 驳回到起点或指定节点
```

---

## 6. 数据大屏（ P1）

### 6.1 产品原型描述

#### 6.1.1 大屏设计器

```
┌──────────────────────────────────────────────────────────────────────────┐
│  数据大屏 - 运营监控看板                              [预览] [保存] [发布] │
├──────────────────────────────────────────────────────────────────────────┤
│  ┌───────────┐  ┌─────────────────────────────────────────────────────┐  │
│  │ 组件库    │  │                                                     │  │
│  │           │  │     ┌─────────────┐   ┌─────────────┐              │  │
│  │ 📊 折线图 │  │     │ 累计用户数  │   │ 今日订单    │              │  │
│  │ 📊 柱状图 │  │     │   128,456   │   │   2,847     │              │  │
│  │ 📊 饼图   │  │     └─────────────┘   └─────────────┘              │  │
│  │ 📊 环形图 │  │                                                     │  │
│  │ 🔢 数字   │  │     ┌───────────────────────┐                     │  │
│  │ 📋 表格   │  │     │     销售趋势折线图      │                     │  │
│  │ 📍 地图   │  │     │  ▂▃▅▆▇█▇▆▅▃▂▃▅▆     │                     │  │
│  │ 🖼️ 图片   │  │     └───────────────────────┘                     │  │
│  │ 📝 文字   │  │                                                     │  │
│  │ 🔄 轮播   │  │     ┌──────────┐  ┌──────────┐                     │  │
│  │           │  │     │ 订单状态  │  │ 热销商品 │                     │  │
│  │ ─────────  │  │     │  饼图    │  │  TOP5   │                     │  │
│  │ 全局设置   │  │     └──────────┘  └──────────┘                     │  │
│  │ 尺寸:1920×1080▼                                                     │  │
│  └───────────┘  └─────────────────────────────────────────────────────┘  │
│                                                                          │
│  ──────────────────────────────────────────────────────────────────────  │
│  属性面板                                                               │
│  当前选中: [销售趋势折线图]                                               │
│  数据源: [SQL查询 ▼]   SELECT date,amount FROM order GROUP BY date     │
│  刷新频率: [10秒 ▼]    [刷新数据]                                        │
└──────────────────────────────────────────────────────────────────────────┘
```

#### 6.1.2 大屏展示模式

```
┌────────────────────────────────────────────────────────────────────────┐
│████████████████████████████████████████████████████████████████████████│
│████████                                                          ████████│
│████                    运营监控大屏                                  ██████│
│████████                     2026-04-05 13:36:20                     ████████│
│████████████████████████████████████████████████████████████████████████│
│                                                                        │
│   ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐  │
│   │  累计用户    │ │  今日订单    │ │  今日营收    │ │  转化率      │  │
│   │  128,456     │ │   2,847      │ │  ¥1,287,430  │ │   23.5%      │  │
│   │   ↑12.5%    │ │   ↑8.3%     │ │   ↑15.2%    │ │   ↑2.1%     │  │
│   └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘  │
│                                                                        │
│   ┌─────────────────────────────────┐  ┌─────────────────────────┐   │
│   │        销售趋势（近30天）        │  │      订单状态分布        │   │
│   │                                 │  │                           │   │
│   │  300k ┤      ╱╲    ╱╲         │  │    ┌───┐                 │   │
│   │  200k ┤   ╱╲╱  ╲╱╱  ╲╱        │  │    │   │ 已完成 65%      │   │
│   │  100k ┤  ╱        ╲            │  │    │   │ 进行中 20%      │   │
│   │       ┼─────────────────────  │  │    │   │ 已取消 15%      │   │
│   │       1   5   10   15   20  25 │  │    └───┘                 │   │
│   └─────────────────────────────────┘  └─────────────────────────┘   │
│                                                                        │
│   ┌──────────────────────────────────────────────────────────────┐   │
│   │                     实时订单滚动                             │   │
│   │  订单号        客户         金额        时间                 │   │
│   │  ORD2026040501 张三        ¥299.00     13:36:01            │   │
│   │  ORD2026040502 李四        ¥1,299.00   13:35:58            │   │
│   └──────────────────────────────────────────────────────────────┘   │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

### 6.2 功能详情

#### 6.2.1 组件库

| 组件 | 配置参数 | 数据绑定 |
|------|----------|----------|
| **数字翻牌器** | 前缀、后缀、颜色、数值位数 | SQL 聚合结果 |
| **折线图** | X轴字段、Y轴字段、线条颜色、区域填充 | SQL 结果集 |
| **柱状图** | X轴字段、Y轴字段、柱状颜色 | SQL 结果集 |
| **饼图/环形图** | 占比字段、标签字段、颜色列表 | SQL 结果集 |
| **排行榜** | 排名字段、名称字段、数值字段 | SQL 结果集 |
| **中国地图** | 省份字段、颜色字段、值字段 | SQL 结果集 |
| **轮播列表** | 标题字段、描述字段、循环方向 | SQL 结果集 |
| **倒计时** | 目标时间、样式 | 静态配置 |
| **实时时钟** | 格式、样式 | 系统时间 |
| **图片** | 图片地址、链接 | 静态配置 |

#### 6.2.2 数据源绑定

```sql
-- 支持的查询类型：
-- 1. 单值查询（绑定到数字翻牌器）
SELECT COUNT(*) as value FROM sys_user WHERE status=1

-- 2. 列表查询（绑定到折线图、柱状图）
SELECT DATE(create_time) as date, COUNT(*) as value
FROM sys_order
WHERE create_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(create_time)

-- 3. 占比查询（绑定到饼图）
SELECT status as name, COUNT(*) as value
FROM sys_order
GROUP BY status
```

---

## 7. 定时任务可视化（ P1）

### 7.1 产品原型描述

#### 7.1.1 任务配置界面

```
┌────────────────────────────────────────────────────────────────┐
│  定时任务                                            [新建任务] │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  任务名称：清理7天前的登录日志                                   │
│  ──────────────────────────────────────────────────────────   │
│  命令类型：Shell脚本 ▼                                           │
│  执行命令：php think clear:loginLog --days=7                    │
│                                                                │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  执行周期配置（已自动生成 Cron 表达式）                   │    │
│  │                                                         │    │
│  │  [每天 ▼]  [早上 9:00 ▼]                                │    │
│  │                                                         │    │
│  │  Cron: 0 9 * * *     （每天 09:00 执行）                  │    │
│  │  [生成表达式]  [手动编辑]                                 │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                │
│  执行配置：                                                     │
│    ☑ 超时中断（超时时间 [30] 秒）                                │
│    ☑ 失败重试（重试 [3] 次，间隔 [5] 分钟）                       │
│    ☐ 失败告警（通知人：[管理员 ▼]）                              │
│                                                                │
│  [保存]  [保存并立即执行一次]                                    │
└────────────────────────────────────────────────────────────────┘
```

#### 7.1.2 Cron 可视化配置

```
┌──────────────────────────────────────────────────────────────┐
│  Cron 表达式可视化配置                                          │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  执行周期：                                                   │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐        │
│  │ ● 每天   │ │ ○ 每周   │ │ ○ 每月   │ │ ○ 自定义  │        │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘        │
│                                                              │
│  （选择"每天"时显示）                                         │
│  执行时间：                                                   │
│  小时：[09 ▼]  分钟：[00 ▼]                                   │
│                                                              │
│  表达式预览：                                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  0 9 * * *   ←→   每天 09:00 执行                      │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  下次执行时间预览：2026-04-06 09:00:00                        │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 7.2 内置任务模板

| 模板名称 | 执行周期 | 执行内容 |
|----------|----------|----------|
| 清理登录日志 | 每天 | 删除 30 天前的登录日志 |
| 清理操作日志 | 每天 | 删除 90 天前的操作日志 |
| 备份数据库 | 每天 | 执行 mysqldump 备份 |
| 更新缓存 | 每小时 | 刷新热点数据缓存 |
| 统计日报 | 每天 | 计算昨日关键指标 |
| 检查过期会话 | 每 30 分钟 | 清理 Redis 过期 Session |
| 发送订阅邮件 | 每天 | 发送订阅内容汇总邮件 |

---

## 8. 技术架构设计

### 8.1 整体架构

```
┌─────────────────────────────────────────────────────────────────────┐
│                          用户层                                      │
│   PC浏览器（Chrome/Firefox/Safari）   移动端（站内消息推送）          │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       前端层（Vue3 + Vite）                          │
│                                                                      │
│   @/views/ai/chat/index.vue        AI 对话窗口                        │
│   @/views/lowcode/table/index.vue  低代码建表                        │
│   @/views/workflow/design/index.vue 工作流设计器                      │
│   @/views/bigscreen/editor/index.vue 数据大屏编辑                    │
│   @/views/task/index.vue           定时任务管理                      │
│                                                                      │
│   依赖：element-plus, echarts, @vue-flow, wangeditor                │
└────────────────────────────┬────────────────────────────────────────┘
                             │ Router + WebSocket + REST API
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       网关层（Gateway）                              │
│                                                                      │
│   AI Gateway：意图分类 │ LLM 调用 │ 结果格式化 │ 降级处理            │
│   统一入口：/pcapi/* (PC端)  /adminapi/* (管理端)                     │
└────────────────────────────┬────────────────────────────────────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
          ▼                  ▼                  ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│  AI 服务         │ │  业务服务        │ │  工作流服务      │
│                  │ │                  │ │                  │
│  Nl2SqlLogic     │ │  LowCodeLogic    │ │  WorkflowEngine  │
│  AiChatLogic    │ │  CodeGenerator   │ │  NodeHandler     │
│  IntentClassifier│ │  TaskScheduler  │ │  TokenExecutor   │
└─────────────────┘ └─────────────────┘ └─────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    数据层（MySQL + Redis）                           │
│                                                                      │
│   MySQL：业务数据（sys_* 表）+ 低代码配置 + 工作流数据               │
│   Redis：AI 会话上下文 + 任务队列 + 缓存                            │
└─────────────────────────────────────────────────────────────────────┘
```

### 8.2 目录结构

```
backend/app/adminapi/
├── controller/
│   ├── ai/
│   │   ├── AiController.php        （对话入口）
│   │   ├── ChatController.php       （聊天历史）
│   │   └── Nl2SqlController.php     （NL2SQL）
│   ├── lowcode/
│   │   ├── TableController.php      （建表配置）
│   │   ├── ColumnController.php     （字段管理）
│   │   └── CodeGenController.php    （代码生成）
│   ├── workflow/
│   │   ├── DefinitionController.php （流程定义）
│   │   ├── InstanceController.php   （流程实例）
│   │   ├── TaskController.php       （待办任务）
│   │   └── BatchController.php      （批量审批）
│   ├── bigscreen/
│   │   ├── ScreenController.php     （大屏管理）
│   │   ├── ComponentController.php  （组件配置）
│   │   └── DataSourceController.php （数据源）
│   └── task/
│       ├── TaskController.php       （任务管理）
│       └── TaskLogController.php    （执行日志）
│
├── logic/
│   ├── ai/
│   │   ├── Nl2SqlLogic.php          （NL2SQL 核心）
│   │   ├── IntentLogic.php          （意图识别）
│   │   ├── ActionRouter.php         （动作路由）
│   │   └── LLMClient.php            （LLM 调用封装）
│   ├── lowcode/
│   │   ├── TableLogic.php
│   │   ├── CodeGenerator.php         （代码生成器）
│   │   └── TableBuilder.php         （DDL 构建）
│   ├── workflow/
│   │   ├── WorkflowEngine.php       （引擎核心）
│   │   ├── NodeHandler.php          （节点处理器）
│   │   ├── TokenManager.php         （令牌管理）
│   │   └── HistoryLogger.php        （历史记录）
│   └── task/
│       ├── TaskScheduler.php         （任务调度）
│       └── CronExpression.php       （Cron 解析）
│
├── model/
│   ├── LowcodeTable.php
│   ├── WorkflowDefinition.php
│   ├── WorkflowInstance.php
│   ├── WorkflowTask.php
│   ├── BigscreenScreen.php
│   ├── BigscreenComponent.php
│   ├── TaskSchedule.php
│   └── TaskLog.php
│
└── validate/
    ├── ai/
    │   └── ChatValidate.php
    ├── lowcode/
    │   └── TableValidate.php
    └── workflow/
        └── DefinitionValidate.php
```

---

## 9. 数据库设计

### 9.1 新增数据表清单

| 序号 | 表名 | 说明 | 所属模块 |
|------|------|------|----------|
| 1 | sys_lowcode_table | 低代码建表配置 | 低代码 |
| 2 | sys_lowcode_column | 低代码字段配置 | 低代码 |
| 3 | sys_lowcode_relation | 低代码表关联关系 | 低代码 |
| 4 | sys_code_history | 代码生成历史 | 低代码 |
| 5 | sys_workflow_definition | 流程定义 | 工作流 |
| 6 | sys_workflow_instance | 流程实例 | 工作流 |
| 7 | sys_workflow_task | 流程任务 | 工作流 |
| 8 | sys_workflow_history | 流程历史 | 工作流 |
| 9 | sys_workflow_comment | 审批意见 | 工作流 |
| 10 | sys_bigscreen | 大屏配置 | 数据大屏 |
| 11 | sys_bigscreen_component | 大屏组件 | 数据大屏 |
| 12 | sys_bigscreen_datasource | 大屏数据源 | 数据大屏 |
| 13 | sys_task_schedule | 定时任务 | 定时任务 |
| 14 | sys_task_log | 任务执行日志 | 定时任务 |
| 15 | sys_ai_session | AI 会话记录 | AI Agent |
| 16 | sys_ai_message | AI 消息记录 | AI Agent |

### 9.2 ER 关系图

```
                    ┌─────────────────┐
                    │  AI Agent       │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │ sys_ai_session  │
                    │ (会话表)        │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │ sys_ai_message │
                    │ (消息表)        │
                    └────────────────┘

┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│ 低代码建表    │────→│ sys_lowcode_table│────→│ 代码生成历史      │
└──────────────┘     └────────┬─────────┘     └──────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │ sys_lowcode_column │
                    │ (字段配置)          │
                    └────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│                     工作流引擎                                 │
│  ┌──────────────┐  1:N  ┌───────────────┐  1:N  ┌─────────┐ │
│  │ 流程定义      │──────→│ 流程实例       │──────→│ 流程任务 │ │
│  │ (definition) │       │ (instance)    │       │ (task)  │ │
│  └──────────────┘       └───────────────┘       └─────────┘ │
│                                     │                          │
│                                     ▼                          │
│                          ┌─────────────────┐                   │
│                          │ 流程历史记录     │                   │
│                          │ (history)       │                   │
│                          └─────────────────┘                   │
└──────────────────────────────────────────────────────────────┘
```

---

## 10. API 接口设计

### 10.1 AI Agent 接口

#### 10.1.1 发送对话消息

```
POST /adminapi/ai/chat/send
Authorization: Bearer {token}

Request:
{
  "content": "帮我新建一个管理员账号 test001",
  "session_id": "uuid-xxx"  // 可选，不传则创建新会话
}

Response:
{
  "code": 0,
  "msg": "success",
  "data": {
    "type": "confirm",       // confirm=待确认, success=成功, error=失败, text=文本回复
    "session_id": "uuid-xxx",
    "content": "即将执行以下操作：\nINSERT INTO sys_user (username, role_id, status) VALUES ('test001', 2, 1)",
    "sql": "INSERT INTO sys_user (username, role_id, status, create_time) VALUES ('test001', 2, 1, '2026-04-05 13:36:00')",
    "action_required": "confirm",
    "tables_affected": ["sys_user"],
    "rows_affected": 1
  }
}
```

#### 10.1.2 确认执行

```
POST /adminapi/ai/chat/confirm
Authorization: Bearer {token}

Request:
{
  "session_id": "uuid-xxx",
  "confirmed": true,         // true=确认执行, false=取消
  "sql": "INSERT INTO ..."    // 重复传入防篡改
}

Response:
{
  "code": 0,
  "msg": "执行成功",
  "data": {
    "rows_affected": 1,
    "record_id": 15,
    "tables_affected": ["sys_user"]
  }
}
```

#### 10.1.3 获取会话历史

```
GET /adminapi/ai/chat/history?session_id=uuid-xxx
Authorization: Bearer {token}

Response:
{
  "code": 0,
  "data": {
    "session_id": "uuid-xxx",
    "messages": [
      { "role": "user", "content": "帮我新建管理员", "time": "..." },
      { "role": "assistant", "content": "即将执行...", "time": "..." },
      { "role": "user", "content": "确认", "time": "..." },
      { "role": "assistant", "content": "执行成功", "time": "..." }
    ]
  }
}
```

### 10.2 低代码建表接口

#### 10.2.1 创建表配置

```
POST /adminapi/lowcode/table/create
Authorization: Bearer {token}

Request:
{
  "table_name": "order",
  "table_comment": "订单管理",
  "table_prefix": "sys_",
  "columns": [
    { "name": "order_no", "comment": "订单号", "type": "varchar", "length": 50, "required": true, "unique": true },
    { "name": "amount", "comment": "金额", "type": "decimal", "length": "10,2", "required": true },
    { "name": "status", "comment": "状态", "type": "tinyint", "length": 1, "default": 1 }
  ],
  "generate_type": 1,       // 1=生成并执行，2=仅生成代码
  "menu_name": "订单管理"   // 为空则不生成菜单
}
```

#### 10.2.2 生成代码预览

```
POST /adminapi/lowcode/codegen/preview
Authorization: Bearer {token}

Request:
{
  "table_id": 1
}

Response:
{
  "code": 0,
  "data": {
    "files": [
      { "path": "backend/app/adminapi/controller/OrderController.php", "size": 2048 },
      { "path": "backend/app/adminapi/logic/OrderLogic.php", "size": 1536 },
      { "path": "frontend/src/views/auto/order/index.vue", "size": 4096 }
    ]
  }
}
```

### 10.3 工作流接口

#### 10.3.1 发布流程

```
POST /adminapi/workflow/definition/publish
Authorization: Bearer {token}

Request:
{
  "name": "请假申请流程",
  "key": "leave_apply",
  "category": "office",
  "nodes": [...],
  "edges": [...]
}

Response:
{
  "code": 0,
  "msg": "发布成功",
  "data": {
    "definition_id": 1,
    "version": 1
  }
}
```

#### 10.3.2 发起流程

```
POST /adminapi/workflow/instance/start
Authorization: Bearer {token}

Request:
{
  "definition_key": "leave_apply",
  "business_id": 123,
  "title": "张三-请假申请-5天",
  "form_data": {
    "leave_type": "年假",
    "days": 5,
    "start_date": "2026-04-10",
    "end_date": "2026-04-15",
    "reason": "家庭旅行"
  }
}

Response:
{
  "code": 0,
  "data": {
    "instance_id": 1,
    "current_task_id": 10,
    "current_assignee": "部门主管"
  }
}
```

#### 10.3.3 审批任务

```
POST /adminapi/workflow/task/approve
Authorization: Bearer {token}

Request:
{
  "task_id": 10,
  "action": "agree",        // agree/disagree/transfer/delegated
  "comment": "同意申请",
  "transfer_to": null        // 转发时填写
}
```

---

## 11. 里程碑计划

### 11.1 详细开发计划

```
V4.0 开发周期：12 周（3 个月）

┌─────────────────────────────────────────────────────────────────────┐
│ 第1-2周  | AI Agent 核心                                          │
├─────────────────────────────────────────────────────────────────────┤
│ W1      │ ① AI Gateway 搭建（意图分类 + LLM 调用封装）              │
│         │ ② Prompt 模板编写                                        │
│         │ ③ Chat 对话前端                                          │
│ W2      │ ④ NL2SQL 升级（新增 UPDATE/INSERT 意图）                │
│         │ ⑤ 执行确认流程（预览 + 确认）                            │
│         │ ⑥ 多轮对话上下文（Redis Session）                        │
│         │ ⑦ 单元测试 + 集成测试                                   │
├─────────────────────────────────────────────────────────────────────┤
│ 第3-4周  | 低代码建表                                              │
├─────────────────────────────────────────────────────────────────────┤
│ W3      │ ① 数据库表设计（sys_lowcode_*）                          │
│         │ ② 建表向导前端（拖拽字段 + 配置属性）                    │
│         │ ③ 字段类型处理（VARCHAR/DECIMAL/DATE/图片/富文本）        │
│ W4      │ ④ 代码生成器后端（Controller/Logic/Model 生成）         │
│         │ ⑤ 前端页面生成（index.vue/add.vue/edit.vue）            │
│         │ ⑥ 菜单自动注册                                          │
│         │ ⑦ 联调测试                                              │
├─────────────────────────────────────────────────────────────────────┤
│ 第5-7周  | 工作流引擎                                              │
├─────────────────────────────────────────────────────────────────────┤
│ W5      │ ① 数据库表设计（sys_workflow_*）                        │
│         │ ② DAG 设计器前端（BPMN.js 或自研）                       │
│         │ ③ 节点配置面板（审批人/条件/通知）                       │
│ W6      │ ④ 工作流引擎核心（Token 推进 + 节点 Handler）           │
│         │ ⑤ 审批中心前端（待办/已办/发起）                        │
│ W7      │ ⑥ 审批动作（通过/拒绝/转发/委托）                        │
│         │ ⑦ 站内消息通知                                           │
│         │ ⑧ 联调测试                                              │
├─────────────────────────────────────────────────────────────────────┤
│ 第8-9周  | 数据大屏                                                │
├─────────────────────────────────────────────────────────────────────┤
│ W8      │ ① 数据库表设计（sys_bigscreen_*）                       │
│         │ ② 组件库开发（ECharts 封装 10 种组件）                  │
│         │ ③ 大屏设计器前端（拖拽 + 调整大小）                      │
│ W9      │ ④ 数据源绑定（SQL 查询配置）                            │
│         │ ⑤ 大屏预览 + 全屏投影模式                               │
│         │ ⑥ 联调测试                                              │
├─────────────────────────────────────────────────────────────────────┤
│ 第10-11周| 定时任务可视化                                          │
├─────────────────────────────────────────────────────────────────────┤
│ W10     │ ① 数据库表设计（sys_task_*）                            │
│         │ ② Cron 可视化配置前端                                    │
│         │ ③ 任务调度后端（think queue 或 workerman）              │
│ W11     │ ④ 执行日志记录                                          │
│         │ ⑤ 失败告警机制                                           │
│         │ ⑥ 内置任务模板                                          │
│         │ ⑦ 联调测试                                              │
├─────────────────────────────────────────────────────────────────────┤
│ 第12周   | 整体联调 + 文档 + 发布                                   │
├─────────────────────────────────────────────────────────────────────┤
│ W12     │ ① 各模块集成联调                                        │
│         │ ② 性能优化（AI 缓存、大屏懒加载）                        │
│         │ ③ 编写用户手册 + 开发文档                               │
│         │ ④ 发布 V4.0 到 Gitee/GitHub                            │
│         │ ⑤ 更新 Demo 站                                          │
└─────────────────────────────────────────────────────────────────────┘
```

### 11.2 发布检查清单

```
发布前检查：
  □ 所有 P0 功能通过验收测试
  □ 所有 P1 功能基本可用
  □ 文档齐全（用户手册 + 开发文档）
  □ Demo 站更新到 V4.0
  □ Docker 镜像更新
  □ Gitee/GitHub Release 打标签
  □ 内部通知公告
```

---

## 12. 资源分配

### 12.1 团队配置

| 角色 | 人数 | 负责模块 | 工时估算 |
|------|------|----------|----------|
| 后端开发（李彦宏） | 1 人 | AI Agent + 工作流引擎 + 定时任务后端 | 8 周 × 5 = 40 人天 |
| 前端开发（雷军） | 1 人 | AI 聊天 + 低代码前端 + 工作流设计器 + 大屏 | 8 周 × 5 = 40 人天 |
| 测试（周鸿祎） | 1 人 | 全模块测试 + 回归测试 | 3 周 × 5 = 15 人天 |
| 运维（王坚） | 0.5 人 | 部署 + 监控 + Docker 维护 | 2 周 × 5 = 10 人天 |
| 产品（马化腾） | 0.2 人 | 需求评审 + PRD 维护 + 验收 | 贯穿全程 |

### 12.2 技术资源

| 资源 | 说明 | 预算 |
|------|------|------|
| LLM API 调用 | 通义千问或 GPT-4o，按量计费 | 预估 ¥500/月（初期） |
| 服务器 | Demo 站当前服务器（39.105.173.6） | 已有的 |
| 域名 | demo.fydev.cn | 已有的 |
| 数据库 | MySQL（当前 Docker 部署） | 已有的 |

---

## 13. 风险评估

### 13.1 技术风险

| 风险 | 概率 | 影响 | 应对措施 |
|------|------|------|----------|
| LLM API 响应不稳定 | 高 | 中 | 降级策略（超时切换模型）+ 本地缓存 |
| 工作流 DAG 自研复杂度高 | 中 | 高 | 优先实现审批流 MVP，简单条件分支，二期扩展 |
| 代码生成器覆盖不全 | 中 | 中 | 先覆盖 80% 常用场景，特殊场景提示手动调整 |
| 大屏 ECharts 性能问题 | 低 | 低 | 使用 canvas 渲染，数据量大切片加载 |

### 13.2 进度风险

| 风险 | 概率 | 影响 | 应对措施 |
|------|------|------|----------|
| 凯哥 LLM 选型延迟 | 高 | 高 | 先用通义千问免费版开发，正式环境留接口即可 |
| 需求变更 | 中 | 中 | PRD 评审锁定范围，变更走变更流程 |
| 联调发现问题多 | 中 | 中 | 预留 1 周 buffer，提前集成 |

### 13.3 依赖关系

```
LLM 选型 ──────────→ AI Agent 开发
                         │
低代码建表 ─────────────┘
      │
      └──────→ 代码生成 ──→ 工作流引擎（表单绑定）
                              │
数据大屏 ─────────────────────┘
```

---

## 14. 验收标准

### 14.1 AI Agent

| 用例 | 验收条件 | 测试方法 |
|------|----------|----------|
| 查询统计 | 输入"查询所有管理员"返回正确用户列表 | 手动测试 |
| 新增确认 | 输入"新建管理员 test"，预览 SQL 正确，确认后执行成功 | 手动测试 |
| 上下文保持 | 连续 3 轮对话，系统能记住前文 | 手动测试 |
| 高危拦截 | 输入"删除所有用户"，系统拒绝执行 | 手动测试 |
| 错误处理 | 输入无效请求，系统返回友好错误提示 | 手动测试 |

### 14.2 低代码建表

| 用例 | 验收条件 | 测试方法 |
|------|----------|----------|
| 建表 | 创建"订单管理"表（含 5 个字段），SQL 正确执行 | 手动测试 |
| CRUD 生成 | 生成代码无语法错误，可正常访问 | 手动测试 |
| 菜单注册 | 新页面自动出现在后台菜单 | 手动测试 |
| 字段覆盖 | 支持 VARCHAR/DECIMAL/DATE/TINYINT/ENUM/图片/富文本 | 各类型测试 |

### 14.3 工作流

| 用例 | 验收条件 | 测试方法 |
|------|----------|----------|
| 流程发布 | 设计请假审批流程并成功发布 | 手动测试 |
| 发起流程 | 填写表单后流程实例创建成功 | 手动测试 |
| 审批流转 | 审批人可看到待办，点击通过/拒绝 | 手动测试 |
| 条件分支 | 金额 > 10000 走总监，else 走主管 | 条件分支测试 |
| 消息通知 | 审批到达时站内消息提醒 | 手动测试 |

### 14.4 数据大屏

| 用例 | 验收条件 | 测试方法 |
|------|----------|----------|
| 组件拖拽 | 可拖拽折线图到画布并调整大小 | 手动测试 |
| 数据绑定 | 绑定 SQL 后图表正确渲染 | 手动测试 |
| 全屏投影 | 按 F11 全屏后布局正常 | 手动测试 |
| 实时刷新 | 设置 10s 刷新，数据正确更新 | 手动测试 |

### 14.5 定时任务

| 用例 | 验收条件 | 测试方法 |
|------|----------|----------|
| Cron 可视化 | 选择"每天 9 点"生成正确 Cron | 手动测试 |
| 手动执行 | 点击"立即执行"任务正常触发 | 手动测试 |
| 执行日志 | 执行后可在日志中查看结果 | 手动测试 |
| 失败告警 | 任务失败后站内通知管理员 | 手动测试 |

---

## 附录

### A. 术语表

| 术语 | 说明 |
|------|------|
| NL2SQL | Natural Language to SQL，自然语言转 SQL |
| DAG | Directed Acyclic Graph，有向无环图 |
| LLM | Large Language Model，大语言模型 |
| CRUD | Create/Read/Update/Delete，增删改查 |
| BPMN | Business Process Model and Notation，业务流程模型标注 |
| Cron | 时间调度表达式，如 `0 9 * * *` 表示每天 9 点 |
| Token | 工作流引擎中的执行令牌，追踪流程位置 |

### B. 参考资料

| 资料 | 链接 |
|------|------|
| FeiyuAdmin 主仓库 | https://gitee.com/gynet/feiyuadmin |
| V1.0 开发计划 | `/www/wwwroot/feiyuadmin/V1.0开发计划.md` |
| V3.0 技术方案 | `/www/wwwroot/feiyuadmin/docs/V3.0/` |
| ThinkPHP8 文档 | https://www.kancloud.cn/manual/thinkphp8/ |
| Vue3 文档 | https://cn.vuejs.org/ |
| Element Plus 文档 | https://element-plus.org/ |
| ECharts 文档 | https://echarts.apache.org/ |

### C. 决策待确认项

| 序号 | 决策项 | 负责人 | 状态 | 说明 |
|------|--------|--------|------|------|
| 1 | LLM 模型选型（通义/GPT/其他） | 凯哥 | ⏳ 待确认 | 影响 AI Agent 效果 |
| 2 | 工作流引擎自研 vs 引入第三方 | 刘强东 | ⏳ 待确认 | 推荐自研轻量 DAG |
| 3 | 大屏组件库封装方式 | 雷军 | ⏳ 待确认 | ECharts 原生 vs 封装层 |
| 4 | 定时任务队列选型 | 王坚 | ⏳ 待确认 | think-queue vs workerman |
| 5 | V4 发布方式（Git Tag + Docker） | 王坚 | ⏳ 待确认 | 影响用户升级体验 |

---

*文档生成时间：2026-04-05*
*最后更新：2026-04-05*
*产品经理：马化腾*

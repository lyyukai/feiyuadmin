# 飞鱼 Admin 低代码架构设计

> 版本：V1.0 | 日期：2026-04-02 | 设计者：刘强东

---

## 一、整体架构

```
┌─────────────────────────────────────────────────────────┐
│                     用户层（前端）                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────────┐  │
│  │amis渲染器 │  │表单设计器 │  │ 飞鱼Vue3组件映射层   │  │
│  └────┬─────┘  └────┬─────┘  └──────────┬───────────┘  │
│       │              │                      │              │
│       └──────────────┴──────────────────────┘              │
│                       ↓                                     │
│              ┌────────────────┐                              │
│              │ amis Schema   │                              │
│              │  (JSON配置)   │                              │
│              └───────────────┘                              │
└───────────────────────────┬─────────────────────────────────┘
                            ↓
┌───────────────────────────────────────────────────────────┐
│                    后端层（ThinkPHP）                        │
│  ┌────────────┐  ┌────────────┐  ┌──────────────────┐    │
│  │amis页面API │  │Schema存储  │  │amis代码生成器    │    │
│  └────────────┘  └────────────┘  └──────────────────┘    │
└───────────────────────────────────────────────────────────┘
```

---

## 二、amis 核心原理

amis 是百度开源的低代码前端框架，核心理念：**用 JSON 配置代替代码编写**。

### 2.1 工作流程

```
设计者(可视化编辑器) → 生成 JSON Schema → amis 渲染器解析 → 页面
```

### 2.2 Schema 结构

```json
{
  "type": "page",           // 页面类型
  "title": "用户管理",       // 页面标题
  "body": [                 // 页面内容
    {
      "type": "form",      // 表单
      "mode": "normal",
      "body": [
        {
          "type": "input-text",   // 输入框组件
          "name": "username",
          "label": "用户名",
          "placeholder": "请输入用户名"
        }
      ]
    }
  ],
  "actions": [              // 操作按钮
    { "type": "submit", "label": "提交" }
  ]
}
```

### 2.3 渲染器机制

amis 的核心是 `Renderer` 系统：
- 每个 `type` 对应一个渲染器
- 渲染器负责把 JSON 配置渲染成真实 DOM
- 支持组件间嵌套（form 包含 input-text 等）

---

## 三、飞鱼组件映射表

### 3.1 基础映射

| amis type | 飞鱼/Element组件 | 说明 |
|----------|----------------|------|
| `page` | `div.page` | 页面容器 |
| `crud` | `el-table` + `el-pagination` | 增删改查表格 |
| `form` | `el-form` | 表单容器 |
| `input-text` | `el-input` | 单行文本 |
| `input-number` | `el-input-number` | 数字输入 |
| `textarea` | `el-input[type=textarea]` | 多行文本 |
| `select` | `el-select` | 下拉选择 |
| `checkbox` | `el-checkbox` | 复选框 |
| `switch` | `el-switch` | 开关 |
| `date` | `el-date-picker` | 日期选择 |
| `datetime` | `el-date-picker[type=datetime]` | 日期时间 |
| `time` | `el-time-picker` | 时间选择 |
| `input-file` | `el-upload` | 文件上传 |
| `input-image` | `el-upload[accept=image]` | 图片上传 |
| `radios` | `el-radio-group` | 单选组 |
| `checkboxes` | `el-checkbox-group` | 多选组 |
| `button` | `el-button` | 按钮 |
| `submit` | `el-button[type=submit]` | 提交按钮 |
| `reset` | `el-button[type=reset]` | 重置按钮 |
| `dialog` | `el-dialog` | 对话框 |
| `drawer` | `el-drawer` | 抽屉 |
| `tabs` | `el-tabs` | 标签页 |
| `carousel` | `el-carousel` | 轮播 |
| `progress` | `el-progress` | 进度条 |
| `tag` | `el-tag` | 标签 |
| `badge` | `el-badge` | 徽章 |
| `divider` | `el-divider` | 分隔线 |

### 3.2 飞鱼特有组件映射

| 飞鱼业务组件 | amis type（自定义） | 说明 |
|------------|------------------|------|
| 权限树 | `feiyu-permission-tree` | RBAC 权限树选择 |
| 多租户选择 | `feiyu-tenant-select` | 租户下拉选择 |
| 用户选择器 | `feiyu-user-picker` | 弹窗选择用户 |
| 部门选择器 | `feiyu-dept-picker` | 树形部门选择 |
| 代码编辑器 | `feiyu-code-editor` | Monaco Editor 集成 |
| 富文本 | `feiyu-editor` | WangEditor 集成 |
| 数据大屏 | `feiyu-data-screen` | ECharts 图表 |
| 工作流节点 | `feiyu-workflow-node` | 工作流节点 |

---

## 四、自定义渲染器注册

### 4.1 渲染器注册文件

```typescript
// src/lowcode/amis-renderers.ts

import { registerRenderer } from 'amis'
import { ElTreeSelect } from 'element-plus'

// 注册飞鱼权限树组件
registerRenderer('feiyu-permission-tree', {
  render: (schema: any, props: any, store: any) => {
    return h(ElTreeSelect, {
      data: schema.data || [],
      props: { label: 'name', children: 'children' },
      placeholder: schema.placeholder || '请选择权限',
      clearable: true,
    })
  }
})

// 注册飞鱼用户选择器
registerRenderer('feiyu-user-picker', {
  render: (schema: any, props: any, store: any) => {
    return h(UserPickerComponent, {
      modelValue: store[schema.name],
      onUpdate: (val: any) => { store[schema.name] = val },
      multiple: schema.multiple || false,
    })
  }
})
```

### 4.2 入口文件

```typescript
// src/lowcode/index.ts

import 'amis/lib/themes/default.css'
import 'amis/sdk/helper.js'

import { registerRenderers } from './amis-renderers'
import { renderAmisSchema } from './amis-schema'

export { registerRenderers, renderAmisSchema }

// 渲染 schema 到指定容器
export function renderToDOM(container: string | Element, schema: object) {
  const utils = amisRequire('amis') as typeof import('amis')
  return utils.renderers(schema, {
    ...registerRenderers(),
  }).mountTo(container)
}
```

---

## 五、与现有系统集成

### 5.1 数据流

```
amis渲染器 → 用户操作 → 事件触发 → API请求 → 飞鱼后端 → 响应 → amis更新状态
```

### 5.2 API 适配

```typescript
// 替换 amis 默认的 fetcher，使用飞鱼 request 工具
import request from '@/utils/request'

const fetcher = async (config: any) => {
  const res = await request({
    url: config.url,
    method: config.method || 'get',
    data: config.data,
    params: config.query,
    headers: config.headers,
  })
  return {
    status: res.code === 0 ? 200 : 400,
    data: res,
    headers: {},
  }
}
```

### 5.3 权限控制集成

```typescript
// 在amis配置中使用飞鱼权限
{
  "type": "button",
  "label": "删除",
  "visibleOn": "hasPermission('user:delete')",  // 飞鱼权限表达式
  "disabledOn": "hasRole('super_admin')"         // 飞鱼角色表达式
}
```

---

## 六、可视化编辑器集成

### 6.1 amis-editor

amis 自带可视化编辑器，可嵌入飞鱼后台：

```typescript
// src/components/AmisEditor.vue
import { Editor } from 'amis-editor'
import 'amis-editor/lib/themes/default.css'

// 注册飞鱼自定义组件到编辑器
Editor.registerRenderer('feiyu-permission-tree', FeiyuPermissionTree)
Editor.registerRenderer('feiyu-user-picker', FeiyuUserPicker)
```

### 6.2 Schema 预览

编辑器页面包含：
- 左侧：组件面板（amis标准 + 飞鱼特有）
- 中间：画布
- 右侧：属性配置面板
- 顶部：预览 / 保存 / 导出按钮

---

## 七、Schema 生成器

后端提供 API，将amis Schema编译成可运行的Vue代码：

```
POST /api/amis/generate
{
  "schema": { ... },
  "target": "vue3-component"
}

Response:
{
  "code": 0,
  "data": {
    "vue_code": "export default { ... }",
    "file_path": "/src/views/generated/User.vue"
  }
}
```

---

## 八、开发计划

| 阶段 | 内容 | 优先级 |
|------|------|--------|
| P0 | amis核心集成（渲染+API适配） | 高 |
| P0 | 飞鱼基础组件映射（input/select/table） | 高 |
| P1 | 自定义飞鱼组件（权限树/用户选择器） | 高 |
| P1 | amis-editor可视化编辑器集成 | 中 |
| P2 | Schema代码生成器（PHP端） | 中 |
| P2 | 业务联动规则配置 | 低 |

---

## 九、参考资源

- [amis官方文档](https://aisuda.github.io/amis/zh-CN/docs/index)
- [amis-editor GitHub](https://github.com/baidu/amis/tree/master/packages/amis-editor)
- 飞鱼Admin源码：https://gitee.com/gynet/feiyuadmin

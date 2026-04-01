# 会员管理 API

## 模块说明
会员（member）与管理端用户（admin）是独立的系统，会员用于前端用户体系，与后台管理员分开管理。

---

## 1. 会员列表
- **接口**: `/api/member/list`
- **方法**: GET
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | keyword | string | 否 | 搜索关键词（账号/昵称） |
  | level_id | int | 否 | 会员等级ID |
  | status | int | 否 | 状态（0禁用 1正常） |
  | page | int | 否 | 页码（默认1） |
  | limit | int | 否 | 每页条数（默认20） |
- **响应**:
```json
{
  "code": 0,
  "msg": "success",
  "data": {
    "list": [
      {
        "id": 1,
        "username": "user001",
        "nickname": "张三",
        "avatar": "https://example.com/avatar.jpg",
        "mobile": "13800138000",
        "email": "user@example.com",
        "level_id": 1,
        "level_name": "VIP会员",
        "status": 1,
        "last_login_ip": "127.0.0.1",
        "last_login_time": "2026-03-30 12:00:00",
        "create_time": "2026-01-01 10:00:00"
      }
    ],
    "total": 100,
    "page": 1,
    "limit": 20
  }
}
```

---

## 2. 会员详情
- **接口**: `/api/member/detail`
- **方法**: GET
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | id | int | 是 | 会员ID |
- **响应**:
```json
{
  "code": 0,
  "msg": "success",
  "data": {
    "id": 1,
    "username": "user001",
    "nickname": "张三",
    "avatar": "https://example.com/avatar.jpg",
    "mobile": "13800138000",
    "email": "user@example.com",
    "level_id": 1,
    "level_name": "VIP会员",
    "status": 1,
    "last_login_ip": "127.0.0.1",
    "last_login_time": "2026-03-30 12:00:00",
    "create_time": "2026-01-01 10:00:00"
  }
}
```

---

## 3. 新增会员
- **接口**: `/api/member/add`
- **方法**: POST
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | username | string | 是 | 会员账号 |
  | nickname | string | 否 | 会员昵称 |
  | password | string | 是 | 密码 |
  | mobile | string | 否 | 手机号 |
  | email | string | 否 | 邮箱 |
  | level_id | int | 否 | 会员等级ID |
- **响应**:
```json
{
  "code": 0,
  "msg": "新增成功",
  "data": {
    "id": 1
  }
}
```

---

## 4. 编辑会员
- **接口**: `/api/member/edit`
- **方法**: POST
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | id | int | 是 | 会员ID |
  | nickname | string | 否 | 会员昵称 |
  | mobile | string | 否 | 手机号 |
  | email | string | 否 | 邮箱 |
  | level_id | int | 否 | 会员等级ID |
- **响应**:
```json
{
  "code": 0,
  "msg": "编辑成功"
}
```

---

## 5. 删除会员
- **接口**: `/api/member/delete`
- **方法**: POST
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | id | int | 是 | 会员ID |
- **响应**:
```json
{
  "code": 0,
  "msg": "删除成功"
}
```

---

## 6. 批量删除会员
- **接口**: `/api/member/batchDelete`
- **方法**: POST
- **认证**: 需要管理员Token
- **参数**:
  | 参数名 | 类型 | 必填 | 说明 |
  |--------|------|------|------|
  | ids | array | 是 | 会员ID数组 |
- **响应**:
```json
{
  "code": 0,
  "msg": "批量删除成功"
}
```

---

## 7. 会员等级列表
- **接口**: `/api/member/levelList`
- **方法**: GET
- **认证**: 需要管理员Token
- **响应**:
```json
{
  "code": 0,
  "msg": "success",
  "data": [
    {
      "id": 1,
      "level_name": "普通会员",
      "level_icon": "",
      "level_color": "#999999",
      "min_points": 0,
      "max_points": 1000,
      "discount": 1.00,
      "sort": 1,
      "status": 1
    },
    {
      "id": 2,
      "level_name": "VIP会员",
      "level_icon": "",
      "level_color": "#FF6600",
      "min_points": 1000,
      "max_points": 5000,
      "discount": 0.95,
      "sort": 2,
      "status": 1
    }
  ]
}
```

---

## 8. 会员等级管理
- **接口**: `/api/member/level/{action}`
- **方法**: POST
- **认证**: 需要管理员Token
- **操作**:
  - `add` - 新增等级
  - `edit` - 编辑等级
  - `delete` - 删除等级

---

## 错误码
| 错误码 | 说明 |
|--------|------|
| 1001 | 会员不存在 |
| 1002 | 账号已存在 |
| 1003 | 会员已禁用 |
| 1004 | 等级不存在 |
| 1005 | 等级下有会员，无法删除 |

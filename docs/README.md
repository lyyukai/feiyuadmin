# 飞鱼后台管理系统 API 文档

## 超级管理员说明

系统内置超级管理员账号：

| 字段 | 值 |
|------|-----|
| ID | 1 |
| 用户名 | admin |
| 角色 | 超级管理员 |

**超级管理员特权：**
- ✅ 拥有系统所有权限
- ❌ 不可被删除
- ❌ 不可被禁用
- ❌ 权限不可被修改
- ❌ 不可分配角色

所有权限检查逻辑需先判断是否为超级管理员（ID=1），是则直接放行。

---

## 基础信息

| 项目 | 说明 |
|------|------|
| 基础URL | `/api` |
| 数据格式 | JSON |
| 字符编码 | UTF-8 |
| 认证方式 | Bearer Token（登录后获取） |

## 认证说明

除登录接口外，所有接口需在请求头携带 Token：

```
Authorization: Bearer <token>
```

## 通用请求格式

### GET 请求
```
GET /api/user/list?page=1&limit=20&keyword=admin
```

### POST 请求
```json
POST /api/user
Content-Type: application/json

{
    "username": "admin",
    "password": "123456",
    "nickname": "管理员"
}
```

### PUT 请求
```json
PUT /api/user/1
Content-Type: application/json

{
    "nickname": "超级管理员",
    "mobile": "13800138000"
}
```

### DELETE 请求
```
DELETE /api/user/1
```

## 通用响应格式

### 成功响应
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": {}
}
```

### 分页响应
```json
{
    "code": 0,
    "msg": "success",
    "total": 100,
    "data": []
}
```

### 错误响应
```json
{
    "code": 400,
    "msg": "错误描述",
    "data": {}
}
```

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 400 | 请求参数错误 |
| 401 | 未授权 / Token失效 |
| 403 | 禁止访问 / 无权限 |
| 404 | 资源不存在 |
| 422 | 数据验证失败 |
| 500 | 服务器内部错误 |

## 分页格式

### 请求参数
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页条数，默认20，最大100 |

### 响应格式
```json
{
    "code": 0,
    "msg": "success",
    "total": 100,
    "data": []
}
```

## 接口目录

- [管理员管理](./admin.md)
- [角色权限](./role.md)
- [菜单管理](./menu.md)
- [部门管理](./dept.md)
- [岗位管理](./post.md)
- [操作日志](./log_operation.md)
- [登录日志](./log_login.md)
- [参数配置](./config.md)
- [数据字典](./dict.md)
- [文件上传](./file.md)

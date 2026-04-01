# 管理员管理 /api/admin

## 获取当前用户信息

**接口路径:** `/api/admin/info`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "id": 1,
        "username": "admin",
        "nickname": "超级管理员",
        "realname": "张三",
        "email": "admin@example.com",
        "mobile": "13800138000",
        "avatar": "/uploads/avatar/1.jpg",
        "dept_id": 1,
        "dept_name": "技术部",
        "post_id": 1,
        "post_name": "技术总监",
        "roles": ["admin"],
        "permissions": ["*"]
    }
}
```

---

## 更新当前用户信息

**接口路径:** `/api/admin/updateInfo`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| nickname | string | 否 | 昵称 |
| email | string | 否 | 邮箱 |
| mobile | string | 否 | 手机号 |
| avatar | string | 否 | 头像URL |

**响应示例:**
```json
{
    "code": 0,
    "msg": "更新成功",
    "data": {}
}
```

---

## 修改密码

**接口路径:** `/api/admin/updatePassword`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| old_password | string | 是 | 原密码 |
| new_password | string | 是 | 新密码（6-20位） |
| confirm_password | string | 是 | 确认密码 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "密码修改成功",
    "data": {}
}
```

**错误码:**
| code | 说明 |
|------|------|
| 400 | 原密码错误 |
| 400 | 两次密码不一致 |

---

## 管理员列表

**接口路径:** `/api/admin/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词（用户名/昵称/手机） |
| dept_id | int | 否 | 部门ID |
| status | int | 否 | 状态：0=禁用，1=正常 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 50,
    "data": [
        {
            "id": 1,
            "username": "admin",
            "nickname": "超级管理员",
            "realname": "张三",
            "email": "admin@example.com",
            "mobile": "13800138000",
            "avatar": "",
            "dept_id": 1,
            "dept_name": "技术部",
            "post_name": "技术总监",
            "status": 1,
            "login_ip": "127.0.0.1",
            "login_time": "2024-01-01 10:00:00",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 新增管理员

**接口路径:** `/api/admin`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| username | string | 是 | 用户名（4-20位） |
| password | string | 是 | 密码（6-20位） |
| nickname | string | 是 | 昵称 |
| realname | string | 否 | 真实姓名 |
| email | string | 否 | 邮箱 |
| mobile | string | 否 | 手机号 |
| dept_id | int | 否 | 部门ID |
| post_id | int | 否 | 岗位ID |
| role_ids | array | 否 | 角色ID数组 |
| status | int | 否 | 状态，默认1 |
| remark | string | 否 | 备注 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "新增成功",
    "data": { "id": 2 }
}
```

---

## 编辑管理员

**接口路径:** `/api/admin/:id`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| nickname | string | 否 | 昵称 |
| email | string | 否 | 邮箱 |
| mobile | string | 否 | 手机号 |
| dept_id | int | 否 | 部门ID |
| post_id | int | 否 | 岗位ID |
| role_ids | array | 否 | 角色ID数组 |
| status | int | 否 | 状态 |
| remark | string | 否 | 备注 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "更新成功",
    "data": {}
}
```

---

## 删除管理员

**接口路径:** `/api/admin/:id`  
**请求方式:** `DELETE`

**响应示例:**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": {}
}
```

**错误码:**
| code | 说明 |
|------|------|
| 400 | 不能删除当前登录用户 |
| 400 | 超级管理员不可删除 |

---

## 重置密码

**接口路径:** `/api/admin/:id/resetPassword`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| password | string | 否 | 新密码（默认123456） |

**响应示例:**
```json
{
    "code": 0,
    "msg": "密码重置成功",
    "data": { "password": "123456" }
}
```

---

## 分配角色

**接口路径:** `/api/admin/:id/roles`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [1, 2, 3]
}
```

---

**接口路径:** `/api/admin/:id/roles`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| role_ids | array | 是 | 角色ID数组 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "角色分配成功",
    "data": {}
}
```

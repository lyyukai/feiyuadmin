# 飞羽后台管理系统 API 接口文档

> 文档版本：v1.0  
> 更新日期：2026-03-31  
> 基础地址：`http://39.105.173.6:8088/api/`

---

## 一、接口规范

### 1.1 通信协议

| 项目 | 说明 |
|------|------|
| 协议 | HTTP/HTTPS |
| 数据格式 | JSON |
| 字符编码 | UTF-8 |

### 1.2 请求格式

| 类型 | 说明 | 示例 |
|------|------|------|
| GET | 查询参数 | `/api/user/lists?page=1&limit=10` |
| POST | JSON Body | `Content-Type: application/json` |

### 1.3 认证方式

```http
Authorization: Bearer {token}
```

### 1.4 响应格式

**成功响应：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": {},
    "total": 100
}
```

**失败响应：**
```json
{
    "code": 400,
    "msg": "错误信息",
    "data": null
}
```

### 1.5 状态码说明

| code | 说明 |
|------|------|
| 0 | 成功 |
| 400 | 请求参数错误 |
| 401 | 未登录或登录失效 |
| 403 | 无权限 |
| 404 | 资源不存在 |
| 500 | 服务器内部错误 |

---

## 二、认证模块

### 2.1 账号登录

**接口地址：** `POST /api/login`

**请求参数：**
```json
{
    "username": "admin",
    "password": "admin123"
}
```

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| username | string | 是 | 用户名 |
| password | string | 是 | 密码（明文） |

**响应示例：**
```json
{
    "code": 0,
    "msg": "登录成功",
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "expires_in": 7200
    }
}
```

---

### 2.2 退出登录

**接口地址：** `POST /api/logout`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "退出成功",
    "data": null
}
```

---

## 三、用户管理

### 3.1 获取当前用户信息

**接口地址：** `GET /api/user/info`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": {
        "id": 1,
        "username": "admin",
        "nickname": "超级管理员",
        "avatar": "",
        "email": "admin@example.com",
        "mobile": "13800138000",
        "dept_id": 1,
        "dept_name": "技术部",
        "post_id": 1,
        "post_name": "技术总监",
        "status": 1,
        "create_time": "2026-03-01 10:00:00"
    }
}
```

---

### 3.2 用户列表

**接口地址：** `GET /api/user/lists`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页条数，默认10 |
| keyword | string | 否 | 搜索关键词（用户名/昵称） |
| status | int | 否 | 状态：1正常 0禁用 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "username": "admin",
            "nickname": "超级管理员",
            "avatar": "",
            "email": "admin@example.com",
            "mobile": "13800138000",
            "dept_id": 1,
            "dept_name": "技术部",
            "post_id": 1,
            "post_name": "技术总监",
            "status": 1,
            "create_time": "2026-03-01 10:00:00"
        }
    ],
    "total": 4
}
```

---

### 3.3 添加用户

**接口地址：** `POST /api/user/add`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| username | string | 是 | 用户名 |
| password | string | 是 | 密码 |
| nickname | string | 是 | 昵称 |
| email | string | 否 | 邮箱 |
| mobile | string | 否 | 手机号 |
| dept_id | int | 否 | 部门ID |
| post_id | int | 否 | 岗位ID |
| status | int | 否 | 状态，默认1 |

**请求示例：**
```json
{
    "username": "zhangsan",
    "password": "123456",
    "nickname": "张三",
    "email": "zhangsan@example.com",
    "mobile": "13900139000",
    "dept_id": 2,
    "post_id": 3,
    "status": 1
}
```

**响应示例：**
```json
{
    "code": 0,
    "msg": "添加成功",
    "data": null
}
```

---

### 3.4 编辑用户

**接口地址：** `POST /api/user/edit`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 用户ID |
| nickname | string | 否 | 昵称 |
| email | string | 否 | 邮箱 |
| mobile | string | 否 | 手机号 |
| dept_id | int | 否 | 部门ID |
| post_id | int | 否 | 岗位ID |
| status | int | 否 | 状态 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "编辑成功",
    "data": null
}
```

---

### 3.5 删除用户

**接口地址：** `POST /api/user/delete`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 用户ID |

**响应示例：**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": null
}
```

---

### 3.6 修改密码

**接口地址：** `POST /api/user/password`

> ⚠️ 待开发

---

## 四、角色管理

### 4.1 角色列表

**接口地址：** `GET /api/role/lists`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页条数，默认10 |
| keyword | string | 否 | 搜索关键词 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "name": "超级管理员",
            "code": "admin",
            "sort": 1,
            "status": 1,
            "remark": "拥有所有权限",
            "create_time": "2026-03-01 10:00:00"
        },
        {
            "id": 2,
            "name": "运营主管",
            "code": "operator",
            "sort": 2,
            "status": 1,
            "remark": "运营相关权限",
            "create_time": "2026-03-01 10:00:00"
        }
    ],
    "total": 2
}
```

---

### 4.2 获取角色菜单

**接口地址：** `GET /api/role/menus`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 角色ID |

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
}
```

---

### 4.3 保存角色权限

**接口地址：** `POST /api/role/menus`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 角色ID |
| menu_ids | array | 是 | 菜单ID数组 |

**请求示例：**
```json
{
    "id": 2,
    "menu_ids": [1, 2, 3, 4, 5]
}
```

**响应示例：**
```json
{
    "code": 0,
    "msg": "权限配置成功",
    "data": null
}
```

---

### 4.4 添加角色

**接口地址：** `POST /api/role/add`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 角色名称 |
| code | string | 是 | 角色编码 |
| sort | int | 否 | 排序，默认0 |
| status | int | 否 | 状态，默认1 |
| remark | string | 否 | 备注 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "添加成功",
    "data": null
}
```

---

### 4.5 编辑角色

**接口地址：** `POST /api/role/edit`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 角色ID |
| name | string | 否 | 角色名称 |
| sort | int | 否 | 排序 |
| status | int | 否 | 状态 |
| remark | string | 否 | 备注 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "编辑成功",
    "data": null
}
```

---

### 4.6 删除角色

**接口地址：** `POST /api/role/delete`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 角色ID |

**响应示例：**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": null
}
```

---

## 五、菜单管理

### 5.1 菜单列表

**接口地址：** `GET /api/menu/lists`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "pid": 0,
            "name": "工作台",
            "path": "/dashboard",
            "url": "",
            "icon": "Odometer",
            "menu_type": "menu",
            "sort": 1,
            "status": 1
        },
        {
            "id": 2,
            "pid": 0,
            "name": "系统管理",
            "path": "/system",
            "url": "",
            "icon": "Setting",
            "menu_type": "directory",
            "sort": 10,
            "status": 1
        }
    ]
}
```

**字段说明：**
| 字段 | 说明 |
|------|------|
| id | 菜单ID |
| pid | 父级ID，0为顶级 |
| name | 菜单名称 |
| path | 路由路径 |
| url | 外部链接 |
| icon | 图标名称 |
| menu_type | 类型：menu/directory/button |
| sort | 排序 |
| status | 状态 |

---

### 5.2 菜单树

**接口地址：** `GET /api/menu/tree`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "pid": 0,
            "name": "工作台",
            "path": "/dashboard",
            "icon": "Odometer",
            "menu_type": "menu",
            "children": []
        },
        {
            "id": 2,
            "pid": 0,
            "name": "系统管理",
            "path": "/system",
            "icon": "Setting",
            "menu_type": "directory",
            "children": [
                {
                    "id": 3,
                    "pid": 2,
                    "name": "用户管理",
                    "path": "/system/user",
                    "icon": "User",
                    "menu_type": "menu",
                    "children": []
                }
            ]
        }
    ]
}
```

---

### 5.3 导航菜单

**接口地址：** `GET /api/menu/nav`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "path": "/dashboard",
            "name": "工作台",
            "icon": "Odometer"
        },
        {
            "path": "/system",
            "name": "系统管理",
            "icon": "Setting",
            "children": [
                {
                    "path": "/system/user",
                    "name": "用户管理",
                    "icon": "User"
                }
            ]
        }
    ]
}
```

---

### 5.4 添加菜单

**接口地址：** `POST /api/menu/add`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| pid | int | 否 | 父级ID，默认0 |
| name | string | 是 | 菜单名称 |
| path | string | 否 | 路由路径 |
| url | string | 否 | 外部链接 |
| icon | string | 否 | 图标 |
| menu_type | string | 是 | 类型：menu/directory/button |
| sort | int | 否 | 排序，默认0 |
| perms | string | 否 | 权限标识 |
| status | int | 否 | 状态，默认1 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "添加成功",
    "data": null
}
```

---

### 5.5 编辑菜单

**接口地址：** `POST /api/menu/edit`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 菜单ID |
| pid | int | 否 | 父级ID |
| name | string | 否 | 菜单名称 |
| path | string | 否 | 路由路径 |
| url | string | 否 | 外部链接 |
| icon | string | 否 | 图标 |
| menu_type | string | 否 | 类型 |
| sort | int | 否 | 排序 |
| perms | string | 否 | 权限标识 |
| status | int | 否 | 状态 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "编辑成功",
    "data": null
}
```

---

### 5.6 删除菜单

**接口地址：** `POST /api/menu/delete`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 菜单ID |

**响应示例：**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": null
}
```

---

## 六、部门管理

### 6.1 部门列表

**接口地址：** `GET /api/dept/lists`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "pid": 0,
            "name": "总公司",
            "leader": "张三",
            "mobile": "13800138000",
            "sort": 1,
            "status": 1,
            "create_time": "2026-03-01 10:00:00"
        },
        {
            "id": 2,
            "pid": 1,
            "name": "技术部",
            "leader": "李四",
            "mobile": "13800138001",
            "sort": 1,
            "status": 1,
            "create_time": "2026-03-01 10:00:00"
        }
    ]
}
```

---

### 6.2 部门树

**接口地址：** `GET /api/dept/tree`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "pid": 0,
            "name": "总公司",
            "children": [
                {
                    "id": 2,
                    "pid": 1,
                    "name": "技术部",
                    "children": []
                }
            ]
        }
    ]
}
```

---

## 七、岗位管理

### 7.1 岗位列表

**接口地址：** `GET /api/post/lists`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "name": "技术总监",
            "code": "tech_director",
            "sort": 1,
            "status": 1,
            "create_time": "2026-03-01 10:00:00"
        },
        {
            "id": 2,
            "name": "前端开发",
            "code": "frontend",
            "sort": 2,
            "status": 1,
            "create_time": "2026-03-01 10:00:00"
        }
    ]
}
```

---

## 八、日志管理

### 8.1 操作日志

**接口地址：** `GET /api/log/lists`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页条数，默认10 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "module": "系统管理",
            "oper_type": "新增",
            "oper_name": "用户管理",
            "oper_desc": "新增用户：张三",
            "ip": "192.168.1.100",
            "user_id": 1,
            "username": "admin",
            "method": "POST /api/user/add",
            "create_time": "2026-03-31 10:00:00"
        }
    ],
    "total": 100
}
```

---

### 8.2 登录日志

**接口地址：** `GET /api/login_log/lists`

**请求参数：**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码，默认1 |
| limit | int | 否 | 每页条数，默认10 |

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": [
        {
            "id": 1,
            "username": "admin",
            "ip": "192.168.1.100",
            "ip_location": "内网IP",
            "os": "Windows 10",
            "browser": "Chrome 120",
            "status": 1,
            "msg": "登录成功",
            "login_time": "2026-03-31 10:00:00"
        }
    ],
    "total": 100
}
```

---

## 九、系统配置

### 9.1 获取配置

**接口地址：** `GET /api/config/lists`

**请求参数：** 无

**响应示例：**
```json
{
    "code": 0,
    "msg": "操作成功",
    "data": {
        "site_name": "飞羽后台管理系统",
        "site_logo": "/uploads/logo.png",
        "site_icp": "",
        "site_copyright": "© 2026 飞羽科技",
        "user_explain": "用户须知内容...",
        "upload_image_size": 5,
        "upload_file_size": 10,
        "upload_image_ext": "jpg,png,gif",
        "upload_file_ext": "doc,docx,xls,xlsx,pdf",
        "smtp_host": "smtp.example.com",
        "smtp_port": 465,
        "smtp_user": "notice@example.com",
        "smtp_password": "",
        "smtp_from": "notice@example.com"
    }
}
```

---

### 9.2 保存配置

**接口地址：** `POST /api/config/save`

**请求参数：** 见上方配置项

**响应示例：**
```json
{
    "code": 0,
    "msg": "保存成功",
    "data": null
}
```

---

## 十、版本记录

| 版本 | 日期 | 说明 |
|------|------|------|
| v1.0 | 2026-03-31 | 初始版本，完成基础功能 |

---

## 十一、待开发接口

| 模块 | 接口 | 说明 |
|------|------|------|
| 用户 | POST /api/user/password | 修改密码 |
| 用户 | GET /api/role/select | 获取角色下拉列表 |
| 部门 | POST /api/dept/add | 添加部门 |
| 部门 | POST /api/dept/edit | 编辑部门 |
| 部门 | POST /api/dept/delete | 删除部门 |
| 岗位 | POST /api/post/add | 添加岗位 |
| 岗位 | POST /api/post/edit | 编辑岗位 |
| 岗位 | POST /api/post/delete | 删除岗位 |

---

*文档最后更新：2026-03-31*

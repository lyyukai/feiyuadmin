# 菜单管理 /api/menu

## 菜单列表（树形）

**接口路径:** `/api/menu/tree`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "系统管理",
            "pid": 0,
            "path": "/system",
            "component": "Layout",
            "icon": "Setting",
            "menu_type": "menu",
            "is_hidden": 0,
            "is_full": 0,
            "is_cache": 0,
            "permission": "",
            "sort": 1,
            "status": 1,
            "children": [
                {
                    "id": 2,
                    "name": "管理员列表",
                    "pid": 1,
                    "path": "user",
                    "component": "system/user/index",
                    "icon": "User",
                    "menu_type": "menu",
                    "permission": "system:user:list"
                }
            ]
        }
    ]
}
```

---

## 菜单列表（平铺）

**接口路径:** `/api/menu/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| keyword | string | 否 | 关键词 |
| status | int | 否 | 状态 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "系统管理",
            "pid": 0,
            "path": "/system",
            "component": "Layout",
            "icon": "Setting",
            "menu_type": "menu",
            "is_hidden": 0,
            "sort": 1,
            "status": 1
        }
    ]
}
```

---

## 新增菜单

**接口路径:** `/api/menu`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 菜单名称 |
| pid | int | 否 | 父级ID，默认0 |
| path | string | 否 | 路由路径 |
| component | string | 否 | 组件路径 |
| redirect | string | 否 | 重定向路径 |
| icon | string | 否 | 菜单图标 |
| menu_type | string | 否 | 类型：menu/iframe/link/button，默认menu |
| is_hidden | int | 否 | 是否隐藏，默认0 |
| is_full | int | 否 | 是否全屏，默认0 |
| is_cache | int | 否 | 是否缓存，默认0 |
| permission | string | 否 | 权限标识（按钮必填） |
| sort | int | 否 | 排序，默认0 |
| status | int | 否 | 状态，默认1 |
| remark | string | 否 | 备注 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "新增成功",
    "data": { "id": 10 }
}
```

---

## 编辑菜单

**接口路径:** `/api/menu/:id`  
**请求方式:** `PUT`

**请求参数:** 同新增

**响应示例:**
```json
{
    "code": 0,
    "msg": "更新成功",
    "data": {}
}
```

---

## 删除菜单

**接口路径:** `/api/menu/:id`  
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
| 400 | 该菜单下存在子菜单，请先删除 |

# 角色权限 /api/role

## 特殊说明

**超级管理员角色（ID=1）：**
- 系统内置，不可删除
- 权限不可修改（前端不显示权限分配按钮）
- 该角色的权限等同于拥有所有权限

---

## 角色列表

**接口路径:** `/api/role/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词 |
| status | int | 否 | 状态：0=禁用，1=正常 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 10,
    "data": [
        {
            "id": 1,
            "name": "超级管理员",
            "code": "admin",
            "status": 1,
            "sort": 1,
            "data_scope": "all",
            "remark": "系统内置角色",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 新增角色

**接口路径:** `/api/role`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 角色名称 |
| code | string | 是 | 角色代码（唯一） |
| status | int | 否 | 状态，默认1 |
| sort | int | 否 | 排序，默认0 |
| data_scope | string | 否 | 数据范围：all=全部, dept=本部门, self=本人 |
| menu_ids | array | 否 | 菜单ID数组 |
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

## 编辑角色

**接口路径:** `/api/role/:id`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 否 | 角色名称 |
| status | int | 否 | 状态 |
| sort | int | 否 | 排序 |
| data_scope | string | 否 | 数据范围 |
| menu_ids | array | 否 | 菜单ID数组 |
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

## 删除角色

**接口路径:** `/api/role/:id`  
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
| 400 | 该角色下存在用户，请先解绑 |
| 400 | 超级管理员角色不可删除 |

---

## 获取角色菜单权限

**接口路径:** `/api/role/:id/menus`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [1, 2, 3, 4, 5]
}
```

---

## 保存角色菜单权限

**接口路径:** `/api/role/:id/menus`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| menu_ids | array | 是 | 菜单ID数组 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "权限保存成功",
    "data": {}
}
```

---

## 获取所有角色（下拉框用）

**接口路径:** `/api/role/all`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        { "id": 1, "name": "超级管理员" },
        { "id": 2, "name": "普通管理员" }
    ]
}
```

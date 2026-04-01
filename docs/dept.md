# 部门管理 /api/dept

## 部门列表（树形）

**接口路径:** `/api/dept/tree`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "总公司",
            "pid": 0,
            "path": "0",
            "leader": "张三",
            "mobile": "13800138000",
            "email": "zhangsan@example.com",
            "sort": 0,
            "status": 1,
            "children": [
                {
                    "id": 2,
                    "name": "技术部",
                    "pid": 1,
                    "leader": "李四",
                    "status": 1
                }
            ]
        }
    ]
}
```

---

## 部门列表（平铺）

**接口路径:** `/api/dept/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| keyword | string | 否 | 关键词 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        {
            "id": 1,
            "name": "总公司",
            "pid": 0,
            "path": "0",
            "leader": "张三",
            "mobile": "13800138000",
            "email": "zhangsan@example.com",
            "sort": 0,
            "status": 1
        }
    ]
}
```

---

## 新增部门

**接口路径:** `/api/dept`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 部门名称 |
| pid | int | 否 | 父级ID，默认0 |
| leader | string | 否 | 负责人 |
| mobile | string | 否 | 联系电话 |
| email | string | 否 | 邮箱 |
| sort | int | 否 | 排序，默认0 |
| status | int | 否 | 状态，默认1 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "新增成功",
    "data": { "id": 3 }
}
```

---

## 编辑部门

**接口路径:** `/api/dept/:id`  
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

## 删除部门

**接口路径:** `/api/dept/:id`  
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
| 400 | 该部门下存在子部门，请先删除 |
| 400 | 该部门下存在用户，请先转移 |

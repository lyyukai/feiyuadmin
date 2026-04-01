# 岗位管理 /api/post

## 岗位列表

**接口路径:** `/api/post/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词 |
| status | int | 否 | 状态 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 5,
    "data": [
        {
            "id": 1,
            "name": "技术总监",
            "code": "tech_director",
            "sort": 1,
            "status": 1,
            "remark": "技术部门负责人",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 新增岗位

**接口路径:** `/api/post`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 岗位名称 |
| code | string | 是 | 岗位代码（唯一） |
| sort | int | 否 | 排序，默认0 |
| status | int | 否 | 状态，默认1 |
| remark | string | 否 | 备注 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "新增成功",
    "data": { "id": 3 }
}
```

---

## 编辑岗位

**接口路径:** `/api/post/:id`  
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

## 删除岗位

**接口路径:** `/api/post/:id`  
**请求方式:** `DELETE`

**响应示例:**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": {}
}
```

---

## 获取所有岗位（下拉框用）

**接口路径:** `/api/post/all`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        { "id": 1, "name": "技术总监" },
        { "id": 2, "name": "高级工程师" }
    ]
}
```

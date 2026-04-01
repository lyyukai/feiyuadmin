# 数据字典 /api/dict

## 字典类型列表

**接口路径:** `/api/dict/type/list`  
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
    "total": 10,
    "data": [
        {
            "id": 1,
            "name": "用户状态",
            "type": "user_status",
            "status": 1,
            "remark": "",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 新增字典类型

**接口路径:** `/api/dict/type`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 字典名称 |
| type | string | 是 | 字典类型（唯一） |
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

## 编辑字典类型

**接口路径:** `/api/dict/type/:id`  
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

## 删除字典类型

**接口路径:** `/api/dict/type/:id`  
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

## 字典数据列表

**接口路径:** `/api/dict/data/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| type | string | 否 | 字典类型 |
| keyword | string | 否 | 关键词 |
| status | int | 否 | 状态 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 20,
    "data": [
        {
            "id": 1,
            "dict_type": "user_status",
            "label": "正常",
            "value": "1",
            "sort": 1,
            "status": 1,
            "remark": "",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 按类型获取字典数据

**接口路径:** `/api/dict/data/:type`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        { "label": "正常", "value": "1" },
        { "label": "禁用", "value": "0" }
    ]
}
```

---

## 新增字典数据

**接口路径:** `/api/dict/data`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| dict_type | string | 是 | 字典类型 |
| label | string | 是 | 字典标签 |
| value | string | 是 | 字典值 |
| sort | int | 否 | 排序，默认0 |
| status | int | 否 | 状态，默认1 |
| remark | string | 否 | 备注 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "新增成功",
    "data": { "id": 5 }
}
```

---

## 编辑字典数据

**接口路径:** `/api/dict/data/:id`  
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

## 删除字典数据

**接口路径:** `/api/dict/data/:id`  
**请求方式:** `DELETE`

**响应示例:**
```json
{
    "code": 0,
    "msg": "删除成功",
    "data": {}
}
```

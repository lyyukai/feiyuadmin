# 参数配置 /api/config

## 配置列表

**接口路径:** `/api/config/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| group | string | 否 | 配置分组 |
| keyword | string | 否 | 关键词 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 20,
    "data": [
        {
            "id": 1,
            "name": "网站名称",
            "group": "basic",
            "key": "site_name",
            "value": "飞鱼后台",
            "type": "text",
            "sort": 1,
            "remark": "",
            "create_time": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## 按分组获取配置

**接口路径:** `/api/config/:group`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "site_name": "飞鱼后台",
        "site_logo": "/uploads/logo.png",
        "site_icp": "京ICP备XXXX号"
    }
}
```

---

## 获取配置分组列表

**接口路径:** `/api/config/groups`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": [
        { "key": "basic", "name": "基础配置" },
        { "key": "upload", "name": "上传配置" },
        { "key": "email", "name": "邮件配置" },
        { "key": "sms", "name": "短信配置" }
    ]
}
```

---

## 新增配置

**接口路径:** `/api/config`  
**请求方式:** `POST`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| name | string | 是 | 配置名称 |
| group | string | 否 | 配置分组，默认basic |
| key | string | 是 | 配置键（唯一） |
| value | string | 否 | 配置值 |
| type | string | 否 | 类型：text/textarea/password/number/radio/checkbox/select/switch/json，默认text |
| options | string | 否 | 选项JSON（radio/checkbox/select用） |
| sort | int | 否 | 排序，默认0 |
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

## 编辑配置

**接口路径:** `/api/config/:id`  
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

## 删除配置

**接口路径:** `/api/config/:id`  
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

## 批量保存配置

**接口路径:** `/api/config/batch`  
**请求方式:** `PUT`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| group | string | 是 | 配置分组 |
| configs | object | 是 | 配置键值对 { "key": "value" } |

**响应示例:**
```json
{
    "code": 0,
    "msg": "保存成功",
    "data": {}
}
```

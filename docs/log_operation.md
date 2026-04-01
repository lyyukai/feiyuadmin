# 操作日志 /api/log/operation

## 操作日志列表

**接口路径:** `/api/log/operation/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词（用户名/URL） |
| user_id | int | 否 | 用户ID |
| type | string | 否 | 操作类型 |
| start_time | string | 否 | 开始时间 YYYY-MM-DD |
| end_time | string | 否 | 结束时间 YYYY-MM-DD |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 1000,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "username": "admin",
            "method": "POST",
            "url": "/api/user",
            "ip": "127.0.0.1",
            "location": "内网IP",
            "param": "{\"username\":\"test\",\"nickname\":\"测试\"}",
            "result": "{\"code\":0,\"msg\":\"新增成功\"}",
            "duration": 45,
            "create_time": "2024-01-01 10:00:00"
        }
    ]
}
```

---

## 操作日志详情

**接口路径:** `/api/log/operation/:id`  
**请求方式:** `GET`

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "username": "admin",
        "method": "POST",
        "url": "/api/user",
        "ip": "127.0.0.1",
        "location": "内网IP",
        "user_agent": "Mozilla/5.0...",
        "param": "{\"username\":\"test\"}",
        "result": "{\"code\":0}",
        "duration": 45,
        "create_time": "2024-01-01 10:00:00"
    }
}
```

---

## 清空操作日志

**接口路径:** `/api/log/operation/clean`  
**请求方式:** `DELETE`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| days | int | 否 | 保留天数，默认30天 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "清空成功",
    "data": { "count": 100 }
}
```

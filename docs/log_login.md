# 登录日志 /api/log/login

## 登录日志列表

**接口路径:** `/api/log/login/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词（用户名） |
| status | string | 否 | 状态：success/fail |
| ip | string | 否 | IP地址 |
| start_time | string | 否 | 开始时间 YYYY-MM-DD |
| end_time | string | 否 | 结束时间 YYYY-MM-DD |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 500,
    "data": [
        {
            "id": 1,
            "username": "admin",
            "status": "success",
            "ip": "127.0.0.1",
            "location": "内网IP",
            "user_agent": "Mozilla/5.0...",
            "msg": "登录成功",
            "login_time": "2024-01-01 10:00:00"
        }
    ]
}
```

---

## 清空登录日志

**接口路径:** `/api/log/login/clean`  
**请求方式:** `DELETE`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| days | int | 否 | 保留天数，默认90天 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "清空成功",
    "data": { "count": 50 }
}
```

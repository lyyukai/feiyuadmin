# 文件上传 /api/file

## 文件列表

**接口路径:** `/api/file/list`  
**请求方式:** `GET`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| page | int | 否 | 页码 |
| limit | int | 否 | 每页条数 |
| keyword | string | 否 | 关键词（文件名） |
| type | string | 否 | 类型：image/video/audio/file |
| start_time | string | 否 | 开始时间 YYYY-MM-DD |
| end_time | string | 否 | 结束时间 YYYY-MM-DD |

**响应示例:**
```json
{
    "code": 0,
    "msg": "success",
    "total": 100,
    "data": [
        {
            "id": 1,
            "name": "avatar.jpg",
            "original": "用户头像.jpg",
            "type": "image",
            "size": 102400,
            "size_format": "100 KB",
            "path": "/uploads/2024/01/avatar.jpg",
            "url": "https://example.com/uploads/2024/01/avatar.jpg",
            "extension": "jpg",
            "mime_type": "image/jpeg",
            "user_id": 1,
            "storage": "local",
            "create_time": "2024-01-01 10:00:00"
        }
    ]
}
```

---

## 上传文件

**接口路径:** `/api/file/upload`  
**请求方式:** `POST`  
**Content-Type:** `multipart/form-data`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| file | file | 是 | 文件（不超过10MB） |
| type | string | 否 | 文件类型：image/video/audio/file（自动识别可省略） |

**响应示例:**
```json
{
    "code": 0,
    "msg": "上传成功",
    "data": {
        "id": 10,
        "name": "document.pdf",
        "original": "项目文档.pdf",
        "type": "file",
        "size": 1024000,
        "size_format": "1000 KB",
        "url": "https://example.com/uploads/2024/01/document.pdf",
        "extension": "pdf"
    }
}
```

**错误码:**
| code | 说明 |
|------|------|
| 400 | 文件大小超出限制 |
| 400 | 不允许的文件类型 |

---

## 下载文件

**接口路径:** `/api/file/:id/download`  
**请求方式:** `GET`

**响应:** 文件流

---

## 删除文件

**接口路径:** `/api/file/:id`  
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

## 批量删除文件

**接口路径:** `/api/file/batch`  
**请求方式:** `DELETE`

**请求参数:**
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| ids | array | 是 | 文件ID数组 |

**响应示例:**
```json
{
    "code": 0,
    "msg": "批量删除成功",
    "data": { "count": 5 }
}
```

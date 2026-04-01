# 飞羽后台管理系统 - 工程规范

## 1. 问题根因分析

### MIME Type 错误的根本原因

**错误现象：** 浏览器控制台报 `MIME type mismatch`（期望 `application/javascript` 但得到 `text/html`）

**根因：** 前端构建产物累积导致文件哈希不匹配

| 指标 | 修复前 | 修复后 |
|------|--------|--------|
| assets/ 文件数 | 3010 个 | 9 个 |
| assets/ 目录大小 | 160 MB | 6 MB |
| 有效构建文件 | ~10 个 | ~9 个 |
| 废弃文件 | 3000 个 | 0 |

**具体机制：**
1. `npm run build` 每次生成新哈希文件名（如 `index-新Hash.js`）
2. 旧构建文件从未清理（`index-旧Hash.js` 仍存在）
3. 浏览器缓存旧 `index.html`（引用旧哈希文件）
4. 请求旧哈希文件 → nginx 找不到 → fallback 到 `index.html`（Content-Type: text/html）
5. 浏览器将 HTML 当 JS 解析 → MIME type 错误

---

## 2. 构建标准

### 2.1 部署前必须执行 `rebuild`

```bash
# 一键清理+构建（强制）
npm run rebuild

# 或手动两步
npm run clean
npm run build
```

**禁止** 直接执行 `npm run build`（不会清理旧文件）。

### 2.2 package.json scripts 规范

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "clean": "rm -rf <outDir>/assets <outDir>/*.html",
    "rebuild": "npm run clean && npm run build"
  }
}
```

### 2.3 vite.config.js 构建配置规范

```javascript
build: {
  outDir: '/www/wwwroot/feiyuadmin/backend/public/admin',
  emptyOutDir: true,          // 构建前自动清理（关键！）
  sourcemap: false,           // 生产环境关闭 sourcemap
  rollupOptions: {
    output: {
      manualChunks: {           // 大库独立打包，避免单文件过大
        'element-plus': ['element-plus'],
        'echarts': ['echarts'],
        'monaco-editor': ['monaco-editor'],
        'wang-editor': ['@wangeditor/editor', '@wangeditor/editor-for-vue'],
      },
      chunkSizeWarningLimit: 500  // 单文件超过 500KB 警告
    }
  }
}
```

### 2.4 构建产物归属

- **前端构建输出** → `backend/public/admin/`（与后端同目录部署）
- **禁止** 把构建产物提交到 Git
- **禁止** 在服务器上多次执行 `build` 而不清理

---

## 3. API 路径规范

### 3.1 路径前缀约定

| 端 | 前缀 | 示例 |
|----|------|------|
| PC 管理端 | `/admin/api/` | `/admin/api/user/list` |
| Mobile/第三方 | `/mobile/api/` | `/mobile/api/notice/lists` |
| 微信回调 | `/api/` | `/api/wechat/callback/:id` |

### 3.2 后端路由注册

```
route/adminapi.php  → Route::group('admin/api', ...)   # PC 管理端
route/api.php      → Route::group('mobile/api', ...)   # Mobile 端
```

### 3.3 前端 axios baseURL 配置

| 环境 | baseURL |
|------|---------|
| PC 前端 | `/admin/api` |
| Mobile 前端 | `/mobile/api` |

**禁止** 不同端共用同一个 baseURL。

---

## 4. Git 规范

### 4.1 .gitignore 必须包含

```
# 前端构建产物
backend/public/admin/
frontend/dist/

# 依赖
frontend/node_modules/
backend/vendor/

# 运行时
backend/runtime/
*.log
.env
```

### 4.2 禁止提交到 Git 的内容

- `node_modules/`（用 `npm install` 安装）
- `vendor/`（用 `composer install` 安装）
- `public/admin/assets/`（前端构建产物）
- `runtime/`（ThinkPHP 运行时缓存）

### 4.3 正确部署流程

```bash
# 1. 克隆代码
git clone https://gitee.com/gynet/feiyuadmin.git
cd feiyuadmin

# 2. 安装后端依赖
cd backend && composer install && cd ..

# 3. 安装前端依赖
cd frontend && npm install && cd ..

# 4. 前端构建（清理+构建）
cd frontend && npm run rebuild && cd ..

# 5. 配置 .env
cp backend/.env.example backend/.env
# 编辑 .env 填入数据库连接等配置
```

---

## 5. nginx 配置规范

### 5.1 静态文件路径

```
URL: /admin/          → 文件系统: /backend/public/admin/
URL: /admin/api/*     → PHP (ThinkPHP 路由)
```

### 5.2 禁止事项

- **禁止** 在 `location /admin/` 之后添加冲突的 API location
- **禁止** 将 `/admin/api/` 路径的请求交给静态文件处理器
- API 路由由 ThinkPHP 处理，nginx 只做透传

### 5.3 正确示例

```nginx
server {
    listen 80;
    server_name feiyu.com;
    root /www/wwwroot/feiyuadmin/backend/public;
    index index.php;

    # 静态前端（/admin/ 路径）
    location /admin/ {
        alias /www/wwwroot/feiyuadmin/backend/public/admin/;
        try_files $uri $uri/ /admin/index.html;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # PHP 处理（所有其他路径）
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-80.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 6. 部署检查清单

每次部署前逐项确认：

- [ ] 执行了 `npm run rebuild` 而不是 `npm run build`
- [ ] `.env` 文件已配置且包含正确的数据库连接
- [ ] `backend/vendor/` 已通过 `composer install` 生成
- [ ] `frontend/node_modules/` 已通过 `npm install` 生成
- [ ] nginx 配置已重载（`nginx -s reload`）
- [ ] 浏览器清除缓存或强制刷新（Ctrl+Shift+R）
- [ ] 访问首页验证无 JS 加载错误

---

## 7. 常见问题处理

### Q: 浏览器仍然报 MIME type 错误
**原因：** 浏览器缓存了旧的 `index.html`  
**解决：** 强制刷新（Ctrl+Shift+R）或清除浏览器缓存

### Q: `npm run build` 报错找不到模块
**原因：** `node_modules/` 未安装  
**解决：** `npm install`

### Q: API 返回 404
**原因：** 路由未正确注册  
**解决：** `cd backend && php think clear` 清除路由缓存

### Q: 后端报错 "控制器不存在"
**原因：** 路由中控制器路径写错  
**解决：** 检查 `route/*.php` 中控制器命名空间是否正确（如 `app\adminapi\controller\NoticeController`）

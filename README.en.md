# FeiyuAdmin

<div align="center">

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0+-green.svg)](https://www.php.net/)
[![Vue](https://img.shields.io/badge/Vue-3.5-green.svg)](https://vuejs.org/)
[![ThinkPHP](https://img.shields.io/badge/ThinkPHP-8.0-green.svg)](https://www.thinkphp.cn/)
[![Element Plus](https://img.shields.io/badge/Element%20Plus-2.0-blue.svg)](https://element-plus.org/)

**🚀 High Performance · 🔐 Secure & Reliable · 🎨 Clean & Beautiful · 📦 Open Source Free**

*A modern admin dashboard built with Vue3 + ThinkPHP8*

[Live Demo](http://demo.fydev.cn/pc) · [Documentation](http://demo.fydev.cn/doc) · [API Docs](http://demo.fydev.cn/api.html)

QQ Group: 767677830

</div>

---

## 📖 Introduction

FeiyuAdmin is a universal admin management system built with **Vue3 + Vite + Element Plus** frontend and **ThinkPHP 8** backend framework.

Completely free and open source under MIT license. Suitable for rapidly building:

- Enterprise internal management systems
- SaaS admin dashboards
- E-commerce backends
- Content management systems
- OA office systems

---

## 🎯 Core Features

| Feature | Description |
|---------|-------------|
| 🎨 Clean & User-Friendly | Element Plus components, dark/light theme |
| 🚀 High Performance | Vue3 + Vite, millisecond-level response |
| 🔐 Secure & Reliable | JWT auth, RBAC permissions, captcha protection |
| 📱 Responsive | Adapts to PC, tablet, and mobile |
| ⚡ Code Generator | One-click CRUD, 80% efficiency boost |
| 🔄 Hot Reload | Vite HMR for excellent dev experience |
| 📦 Ready to Use | Docker one-click deploy, launch immediately |
| 🌐 Multi-language | Internationalization support |

---

## 🖼️ Screenshots

### Demo Homepage
![Demo](docs/images/demo-home.png)

### Dashboard
![Dashboard](docs/images/dashboard.png)

### User Management
![User Management](docs/images/user-management.png)

### Role Permission
![Role Permission](docs/images/role-permission.png)

### Menu Management
![Menu Management](docs/images/menu-management.png)

### Data Screen
![Data Screen](docs/images/data-screen.png)

### Workflow Designer
![Workflow Designer](docs/images/workflow-designer.png)

---

## ✨ Modules

### V1.0 Core Features ✅

| Module | Features |
|--------|----------|
| 👤 User | Login/Logout, Profile, Avatar, Password |
| 👥 Admin | CRUD, Status Enable/Disable, Batch Operations |
| 🔐 Role & Permission | RBAC model, menu & data permissions |
| 📑 Menu | Tree menu, visual config, icon picker |
| 🏢 Department | Org tree, hierarchy |
| 💼 Position | CRUD, position-user query |
| 📝 Operation Log | Detailed records, IP/time tracking |
| 🔑 Login Log | Login history, anomaly alerts |
| ⚙️ Config | System params, SMS/email config |
| 📊 Dictionary | Static data, type+value两级 |
| 📁 File Upload | Local/OSS, image preview, permission |

### V2.0 Enterprise Features 🚧

| Module | Status |
|--------|--------|
| 🔄 Workflow | Visual flow designer, approvals, conditions | 🔄 In Dev |
| 📊 Data Screen | Drag-drop designer, ECharts | 🔄 In Dev |
| 🏢 Multi-Tenant | SaaS mode, tenant isolation | 🔄 In Dev |
| 📝 Form Builder | Drag-drop form designer | 🔄 In Dev |
| 💳 Payment | WeChat/Alipay integration | 🔄 In Dev |
| 📱 WeChat | Official account, menu, auto-reply | 🔄 In Dev |
| ⏰ Cron Jobs | Visual config, task logs | 🔄 In Dev |
| 🔔 Notifications | SMS/email/in-app, templates | 🔄 In Dev |

---

## 🛠️ Tech Stack

### Backend

| Tech | Description |
|------|-------------|
| PHP 8.0+ | Core language |
| ThinkPHP 8 | High-performance PHP framework |
| MySQL 5.7+ | Relational database |
| Redis | Cache, Session |
| JWT | Token authentication |
| RBAC | Role-based access control |

### Frontend

| Tech | Description |
|------|-------------|
| Vue 3.5 | Progressive framework |
| Vite 5 | Next-gen build tool |
| Element Plus 2.0 | Vue3 UI library |
| Pinia | State management |
| Vue Router 4 | Routing |
| ECharts 5 | Data visualization |
| Monaco Editor | Code editor |
| WangEditor | Rich text editor |

---

## 📁 Project Structure

```
feiyuadmin/
├── backend/                      # ThinkPHP8 Backend
│   ├── app/
│   │   ├── adminapi/           # Admin API
│   │   │   ├── controller/     # Controllers
│   │   │   ├── logic/          # Business Logic
│   │   │   ├── validate/       # Validators
│   │   │   └── model/          # Models
│   │   ├── api/                # Open API
│   │   ├── common/             # Common
│   │   └── service/            # Services
│   ├── config/                 # Config
│   ├── database/               # Database scripts
│   ├── public/                  # Web entry
│   │   ├── index.php           # API entry
│   │   ├── admin.php          # Admin entry
│   │   └── install.php        # Installer
│   └── think                    # CLI
│
├── frontend/                   # Vue3 Admin
│   ├── src/
│   │   ├── api/               # API definitions
│   │   ├── assets/            # Static assets
│   │   ├── components/        # Components
│   │   ├── layout/            # Layout
│   │   ├── router/            # Routes
│   │   ├── stores/            # Pinia stores
│   │   ├── utils/             # Utils
│   │   └── views/             # Views
│   └── ...
│
├── pc/                        # Vue3 Public Website (Marketing Site)
│   ├── src/
│   │   ├── views/             # Landing pages
│   │   └── ...
│   └── ...
│
├── docker/                     # Docker deployment
│   └── docker-compose.yml
│
├── docs/                       # Documentation
│   └── images/                 # Screenshots
│
└── README.md
```

---

## 🚀 Quick Start

### Requirements

| Env | Version |
|-----|---------|
| PHP | ≥ 8.0 |
| MySQL | ≥ 5.7 |
| Node.js | ≥ 16 |
| npm/pnpm | ≥ 7 |

### Backend

```bash
cd backend

# Install dependencies
composer install

# Configure database
# Edit config/database.php

# Import database
mysql -u root -p < database/feiyuadmin.sql

# Start dev server
php think run
# Or PHP built-in server
php -S 0.0.0.0:8088 -t public/
```

### Frontend

```bash
cd frontend

# Install dependencies
npm install
# or
pnpm install

# Dev mode
npm run dev

# Production build
npm run build
```

### Docker (Recommended)

```bash
cd docker

# Build and start
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

Visit: `http://localhost:8088`

---

## 🔐 API Reference

### Response Format

```json
{
  "code": 0,        // 0 = success, others = failed
  "msg": "success",
  "data": {}
}
```

### Authentication

Include token in header after login:

```
Authorization: Bearer {token}
```

### Common Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/adminapi/login` | POST | User login |
| `/adminapi/captcha/generate` | GET | Generate captcha |
| `/adminapi/user/lists` | GET | User list |
| `/adminapi/role/lists` | GET | Role list |
| `/adminapi/menu/lists` | GET | Menu list |
| `/adminapi/menu/nav` | GET | User menu |
| `/pcapi/index/banner` | GET | Homepage banner |
| `/pcapi/article/lists` | GET | Article list |

Full API docs: [Online API Docs](http://demo.fydev.cn/api.html)

---

## 🤝 Contributing

1. Fork this repository
2. Create `feat_xxx` branch
3. Commit your code
4. Create Pull Request

---

## 📄 License

[MIT License](LICENSE) - Free forever, commercial use allowed.

---

## 📞 Contact

- **Demo**: http://demo.fydev.cn/pc
- **Docs**: http://demo.fydev.cn/doc
- **Admin**: http://demo.fydev.cn/admin
- **Default Account**: admin / admin123
- **Issues**: https://gitee.com/gynet/feiyuadmin/issues

---

<div align="center">

**If this project helps you, please give it a star ⭐**

</div>

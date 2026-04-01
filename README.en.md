# feiyuadmin

#### Description

feiyuadmin is a high-performance, general-purpose admin management framework built with Vue3 + ThinkPHP8. Lightweight, clean, and easily extensible, it is ideal for quickly building enterprise-level admin backends.

#### Software Architecture

**Backend**: ThinkPHP8
- MVC architecture with clear responsibilities
- ORM database operations
- RESTful API design
- JWT authentication

**Frontend**: Vue3 + Vite
- Composition API
- Element Plus UI
- Pinia state management
- Vue Router

#### Tech Stack

| Role | Technology |
|------|------------|
| Backend Framework | ThinkPHP 8.0 |
| Frontend Framework | Vue 3.5 |
| UI Components | Element Plus |
| Build Tool | Vite 5 |
| State Management | Pinia |
| Database | MySQL 5.7+ |
| PHP Version | ≥8.0 |

#### Features (V1.0)

- ✅ User Management (Login/Logout/Profile)
- ✅ Admin CRUD
- ✅ Role & Permission Management
- ✅ Menu Management
- ✅ Department Management
- ✅ Position Management
- ✅ Operation Logs
- ✅ Login Logs
- ✅ System Parameters
- ✅ Data Dictionary
- ✅ File Upload

#### Installation

**Requirements**
- PHP ≥ 8.0
- MySQL ≥ 5.7
- Node.js ≥ 18
- Composer ≥ 2.0

**Backend Setup**

```bash
cd backend

# Install dependencies
composer install

# Configure database
# Edit config/database.php

# Import database
mysql -u root -p < database/feiyuadmin.sql

# Start server
php think run
```

**Frontend Setup**

```bash
cd frontend

# Install dependencies
npm install

# Development mode
npm run dev

# Production build
npm run build
```

**Docker Deployment**

```bash
cd docker

# Build and start
docker-compose up -d
```

#### Project Structure

```
feiyuadmin/
├── backend/                 # ThinkPHP8 Backend
│   ├── app/
│   │   ├── controller/     # Controllers
│   │   ├── model/          # Models
│   │   ├── validate/       # Validators
│   │   └── service/        # Business Logic
│   ├── config/             # Config Files
│   ├── database/           # Database Scripts
│   ├── route/              # Routes
│   └── public/              # Web Entry
│
├── frontend/               # Vue3 Frontend
│   ├── src/
│   │   ├── api/           # API Calls
│   │   ├── components/     # Common Components
│   │   ├── layout/        # Layout Components
│   │   ├── router/        # Router
│   │   ├── stores/        # State Management
│   │   └── views/         # Page Views
│   └── element-plus/      # UI Library
│
├── docker/                 # Docker Config
└── docs/                   # Documentation
```

#### Usage

1. Access `http://your-domain/admin` to open the login page
2. Default super admin: `admin` / `123456`
3. Please change the default password after first login
4. Manage users, roles, and menus via the "System Management" module

#### Contributing

1. Fork the repository
2. Create `Feat_xxx` branch
3. Commit your code
4. Create a Pull Request

#### License

Apache-2.0 License

#### Links

- Project: https://gitee.com/gynet/feiyuadmin
- Issues: https://gitee.com/gynet/feiyuadmin/issues

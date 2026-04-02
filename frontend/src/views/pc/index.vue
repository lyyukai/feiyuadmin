<template>
  <div class="pc-doc-container">
    <!-- 顶部导航 -->
    <header class="pc-header">
      <div class="header-left">
        <div class="logo">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼Admin - 技术文档</span>
        </div>
      </div>
      <div class="header-right">
        <a href="/demo" class="header-link">演示站</a>
        <a href="#api-docs" class="header-link">接口文档</a>
        <el-button type="primary" size="small" style="min-width: 100px;" @click="scrollTo('overview')">查看文档</el-button>
      </div>
    </header>

    <!-- Hero 区域 -->
    <section class="pc-hero">
      <div class="hero-content">
        <h1>飞鱼Admin管理系统</h1>
        <p class="hero-subtitle">Vue3 + ThinkPHP8 企业级通用后台开发框架</p>
        <div class="hero-badges">
          <span class="badge">🎉 v2.0 全新发布</span>
          <span class="badge">🔥 持续更新中</span>
          <span class="badge">📚 开源免费</span>
        </div>
      </div>
    </section>

    <!-- 主体内容 -->
    <div class="pc-content">
      <!-- 左侧导航 -->
      <aside class="pc-sidebar">
        <div class="sidebar-section">
          <div class="sidebar-title">目录</div>
          <ul class="sidebar-menu">
            <li><a href="#overview" :class="{ active: activeSection === 'overview' }" @click.prevent="scrollTo('overview')">项目概览</a></li>
            <li><a href="#quick-start" :class="{ active: activeSection === 'quick-start' }" @click.prevent="scrollTo('quick-start')">快速开始</a></li>
            <li><a href="#crud-example" :class="{ active: activeSection === 'crud-example' }" @click.prevent="scrollTo('crud-example')">CRUD示例</a></li>
            <li><a href="#api-reference" :class="{ active: activeSection === 'api-reference' }" @click.prevent="scrollTo('api-reference')">接口文档</a></li>
            <li><a href="#project-structure" :class="{ active: activeSection === 'project-structure' }" @click.prevent="scrollTo('project-structure')">项目结构</a></li>
            <li><a href="#deployment" :class="{ active: activeSection === 'deployment' }" @click.prevent="scrollTo('deployment')">部署说明</a></li>
            <li><a href="#faq" :class="{ active: activeSection === 'faq' }" @click.prevent="scrollTo('faq')">常见问题</a></li>
          </ul>
        </div>
      </aside>

      <!-- 右侧文档 -->
      <main class="pc-main">
        <!-- 项目概览 -->
        <section id="overview" class="doc-section">
          <h2>📋 项目概览</h2>
          <p>飞鱼Admin（FeiyuAdmin）是一款基于 <strong>Vue3 + Vite + Element Plus</strong> 前端架构，搭配 <strong>ThinkPHP 8</strong> 后端框架的通用型后台管理系统。</p>
          <div class="info-box success">
            <p>✅ <strong>完全免费开源</strong> - 采用 MIT 开源协议，可商用可二次开发</p>
            <p>✅ <strong>功能完备</strong> - 用户管理、角色权限、菜单管理、数据字典、定时任务等</p>
            <p>✅ <strong>易于扩展</strong> - 代码结构清晰，一键生成 CRUD，效率提升 80%</p>
          </div>
          <div class="project-stats">
            <div class="stat-card">
              <div class="stat-value">1000+</div>
              <div class="stat-label">企业用户</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">50+</div>
              <div class="stat-label">功能模块</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">99.9%</div>
              <div class="stat-label">运行稳定性</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">MIT</div>
              <div class="stat-label">开源协议</div>
            </div>
          </div>
        </section>

        <!-- 技术栈 -->
        <section id="tech-stack" class="doc-section">
          <h2>🛠 技术栈</h2>
          <div class="tech-grid">
            <div class="tech-category">
              <h3>前端技术</h3>
              <div class="tech-items">
                <div class="tech-item">
                  <span class="tech-name">Vue 3.5</span>
                  <span class="tech-desc">渐进式前端框架，组合式 API</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">Vite 5</span>
                  <span class="tech-desc">下一代前端构建工具，HMR 热更新</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">Element Plus</span>
                  <span class="tech-desc">Vue3 UI 组件库</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">Pinia</span>
                  <span class="tech-desc">新一代状态管理</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">Vue Router 4</span>
                  <span class="tech-desc">前端路由管理</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">ECharts 5</span>
                  <span class="tech-desc">数据可视化图表库</span>
                </div>
              </div>
            </div>
            <div class="tech-category">
              <h3>后端技术</h3>
              <div class="tech-items">
                <div class="tech-item">
                  <span class="tech-name">PHP 8.0+</span>
                  <span class="tech-desc">后端核心语言</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">ThinkPHP 8</span>
                  <span class="tech-desc">高性能 PHP 框架</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">MySQL 5.7+</span>
                  <span class="tech-desc">关系型数据库</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">JWT</span>
                  <span class="tech-desc">用户认证 Token</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">RBAC</span>
                  <span class="tech-desc">基于角色的权限控制</span>
                </div>
                <div class="tech-item">
                  <span class="tech-name">RESTful API</span>
                  <span class="tech-desc">标准化接口设计</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- 快速开始 -->
        <section id="quick-start" class="doc-section">
          <h2>🚀 快速开始</h2>
          <h3>环境要求</h3>
          <ul>
            <li>Node.js >= 18.0.0</li>
            <li>PHP >= 8.0</li>
            <li>MySQL >= 5.7</li>
            <li>Redis >= 6.0</li>
          </ul>
          <h3>安装步骤</h3>
          <pre><code># 1. 克隆代码
git clone https://github.com/your-repo/feiyuadmin.git

# 2. 安装前端依赖
cd frontend
npm install

# 3. 配置环境
cp .env.example .env
# 编辑 .env 配置数据库连接

# 4. 启动开发服务器
npm run dev

# 5. 启动后端
cd ../backend
composer install
php think run</code></pre>
          <div class="info-box warning">
            <p>⚠️ 首次使用需要配置数据库连接，请编辑 <code>.env</code> 文件</p>
          </div>
        </section>

        <!-- CRUD开发示例 -->
        <section id="crud-example" class="doc-section">
          <h2>📝 CRUD 开发示例</h2>
          <p>通过代码生成器，可以一键生成完整的 CRUD 功能。以下是手动开发流程：</p>
          <h3>1. 创建数据表</h3>
          <pre><code>CREATE TABLE `feiyu_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `category_id` int(11) DEFAULT NULL COMMENT '分类',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
          <h3>2. 配置路由</h3>
          <pre><code>// backend/route/app.php
use think\facade\Route;

Route::resource('article', 'Article');</code></pre>
          <h3>3. 创建模型</h3>
          <pre><code>// backend/app/model/Article.php
namespace app\model;
use think\Model;

class Article extends Model
{
    protected $name = 'article';
    protected $pk = 'id';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}</code></pre>
          <h3>4. 创建控制器</h3>
          <pre><code>// backend/app/admin/controller/Article.php
namespace app\admin\controller;
use app\model\Article as ArticleModel;

class Article
{
    // 获取列表
    public function lists()
    {
        $list = ArticleModel::select();
        return json(['code' => 0, 'msg' => 'success', 'data' => $list]);
    }
    
    // 保存数据
    public function save()
    {
        $data = request()->post();
        $result = ArticleModel::create($data);
        return json(['code' => 0, 'msg' => 'success', 'data' => $result]);
    }
}</code></pre>
          <div class="info-box success">
            <p>💡 <strong>提示</strong>：使用代码生成器可以一键完成以上步骤，只需在后台填写表名即可</p>
          </div>
        </section>

        <!-- 接口文档 -->
        <section id="api-reference" class="doc-section">
          <h2>📡 API 接口文档</h2>
          <p>系统提供完整的 RESTful API，支持在线调试。</p>
          <h3>认证方式</h3>
          <p>登录后获取 Token，在请求 Header 中携带：</p>
          <pre><code>Authorization: Bearer {token}</code></pre>
          <h3>通用响应格式</h3>
          <pre><code>{
  "code": 0,        // 状态码，0=成功，其他=失败
  "msg": "success", // 提示信息
  "data": {}        // 返回数据
}</code></pre>
          <h3>错误码说明</h3>
          <table class="doc-table">
            <thead>
              <tr><th>code</th><th>说明</th></tr>
            </thead>
            <tbody>
              <tr><td>0</td><td>成功</td></tr>
              <tr><td>401</td><td>未授权，Token 无效或过期</td></tr>
              <tr><td>403</td><td>无权限访问</td></tr>
              <tr><td>404</td><td>资源不存在</td></tr>
              <tr><td>422</td><td>参数验证失败</td></tr>
              <tr><td>500</td><td>服务器内部错误</td></tr>
            </tbody>
          </table>
        </section>

        <!-- 项目结构 -->
        <section id="project-structure" class="doc-section">
          <h2>📁 项目结构</h2>
          <h3>前端目录结构</h3>
          <pre><code>frontend/
├── src/
│   ├── api/                 # API 接口定义
│   │   ├── index.js         # 通用接口
│   │   ├── user.js          # 用户接口
│   │   ├── menu.js          # 菜单接口
│   │   └── ...
│   ├── assets/              # 静态资源
│   │   ├── images/          # 图片资源
│   │   └── styles/          # 样式文件
│   ├── components/          # 公共组件
│   │   ├── common/          # 通用组件
│   │   └── form/            # 表单组件
│   ├── layout/              # 布局组件
│   │   └── index.vue        # 主布局
│   ├── router/              # 路由配置
│   │   └── index.js
│   ├── stores/              # 状态管理
│   │   ├── user.js          # 用户状态
│   │   └── settings.js      # 设置状态
│   ├── utils/               # 工具函数
│   │   └── request.js       # Axios 封装
│   ├── views/               # 页面视图
│   │   ├── dashboard/       # 工作台
│   │   ├── system/          # 系统管理
│   │   └── ...
│   ├── App.vue              # 根组件
│   └── main.js              # 入口文件
├── .env.admin               # 管理后台环境变量
├── .env.pc                  # PC端环境变量
├── .env.mobile              # 移动端环境变量
└── package.json</code></pre>
          <h3>后端目录结构</h3>
          <pre><code>backend/
├── app/
│   ├── adminapi/            # 管理后台 API
│   │   ├── controller/      # 控制器
│   │   │   ├── admin/       # 系统管理
│   │   │   ├── auth/        # 认证
│   │   │   └── ...
│   │   ├── logic/          # 业务逻辑
│   │   ├── validate/       # 验证器
│   │   └── http/
│   │       └── middleware/  # 中间件
│   ├── common/              # 公共类
│   │   └── service/         # 服务类
│   ├── model/               # 数据模型
│   └── service/             # 服务层
├── config/                  # 配置文件
├── route/                   # 路由文件
│   ├── app.php             # 主路由
│   └── api.php             # API 路由
├── public/                  # WEB 目录
│   └── index.php            # 入口文件
└── think                    # 命令行工具</code></pre>
        </section>

        <!-- API 文档 -->
        <section id="api-docs" class="doc-section">
          <h2>📚 API 文档</h2>
          <h3>接口规范</h3>
          <p>系统采用 RESTful API 设计，所有接口返回统一 JSON 格式：</p>
          <pre><code>{
  "code": 0,        // 状态码：0=成功，其他=失败
  "msg": "success",  // 提示信息
  "data": {}         // 返回数据
}</code></pre>

          <h3>认证方式</h3>
          <p>登录成功后，后端返回 <code>token</code>，前端通过以下方式携带：</p>
          <pre><code>Authorization: Bearer {token}</code></pre>

          <h3>接口列表</h3>
          <div class="api-table">
            <table>
              <thead>
                <tr>
                  <th>接口</th>
                  <th>方法</th>
                  <th>说明</th>
                  <th>参数</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>/adminapi/login</td>
                  <td><span class="method post">POST</span></td>
                  <td>用户登录</td>
                  <td>username, password, captcha</td>
                </tr>
                <tr>
                  <td>/adminapi/captcha/generate</td>
                  <td><span class="method get">GET</span></td>
                  <td>获取验证码</td>
                  <td>key (可选，默认 login)</td>
                </tr>
                <tr>
                  <td>/adminapi/user/lists</td>
                  <td><span class="method get">GET</span></td>
                  <td>用户列表</td>
                  <td>page, page_size, keyword</td>
                </tr>
                <tr>
                  <td>/adminapi/role/lists</td>
                  <td><span class="method get">GET</span></td>
                  <td>角色列表</td>
                  <td>page, page_size</td>
                </tr>
                <tr>
                  <td>/adminapi/menu/lists</td>
                  <td><span class="method get">GET</span></td>
                  <td>菜单列表</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>/pcapi/index/banner</td>
                  <td><span class="method get">GET</span></td>
                  <td>首页轮播图</td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>/pcapi/article/lists</td>
                  <td><span class="method get">GET</span></td>
                  <td>文章列表</td>
                  <td>page, page_size</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- 部署说明 -->
        <section id="deployment" class="doc-section">
          <h2>🚀 部署说明</h2>
          <h3>环境要求</h3>
          <div class="requirements">
            <div class="req-item">
              <span class="req-label">前端</span>
              <span class="req-value">Node.js ≥ 16, pnpm ≥ 7</span>
            </div>
            <div class="req-item">
              <span class="req-label">后端</span>
              <span class="req-value">PHP ≥ 8.0, 扩展: gd, pdo, json, session</span>
            </div>
            <div class="req-item">
              <span class="req-label">数据库</span>
              <span class="req-value">MySQL ≥ 5.7</span>
            </div>
            <div class="req-item">
              <span class="req-label">Web 服务器</span>
              <span class="req-value">Nginx / Apache</span>
            </div>
          </div>

          <h3>前端部署</h3>
          <pre><code># 安装依赖
pnpm install

# 开发模式
pnpm run dev

# 管理后台构建
pnpm run build:admin

# PC端构建
pnpm run build:pc

# 移动端构建
pnpm run build:mobile</code></pre>

          <h3>后端部署</h3>
          <pre><code># 1. 创建数据库
CREATE DATABASE feiyuadmin DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. 导入 SQL 文件
mysql -u root -p feiyuadmin < database/feiyuadmin.sql

# 3. 修改数据库配置
# 编辑 backend/config/database.php

# 4. 配置伪静态 (Nginx)
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}

# 5. 访问安装向导
http://your-domain.com/install</code></pre>

          <h3>Docker 部署（推荐）</h3>
          <pre><code># 启动所有服务
docker-compose up -d

# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f</code></pre>
        </section>

        <!-- 常见问题 -->
        <section id="faq" class="doc-section">
          <h2>❓ 常见问题</h2>
          <div class="faq-list">
            <div class="faq-item">
              <h4>Q: 如何获取商业授权？</h4>
              <p>A: 本项目采用 MIT 开源协议，商用免费。如需定制开发或技术支持服务，请联系我们。</p>
            </div>
            <div class="faq-item">
              <h4>Q: 是否支持二开定制？</h4>
              <p>A: 支持。我们提供技术咨询、二次开发、部署运维等全套服务。</p>
            </div>
            <div class="faq-item">
              <h4>Q: 如何开启多租户模式？</h4>
              <p>A: 在系统配置中找到"多租户"开关，开启后系统将自动隔离各租户数据。</p>
            </div>
            <div class="faq-item">
              <h4>Q: 代码生成器如何使用？</h4>
              <p>A: 进入"系统管理 → 代码生成器"，选择数据表，配置生成选项，一键生成前后端代码。</p>
            </div>
            <div class="faq-item">
              <h4>Q: 如何配置微信支付/公众号？</h4>
              <p>A: 在"支付渠道"和"微信渠道"模块中配置对应的 AppID 和密钥。</p>
            </div>
          </div>
        </section>

        <!-- 底部 -->
        <footer class="pc-footer">
          <p>飞鱼Admin管理系统 · MIT License · 持续更新中</p>
          <p>基于 Vue3 + ThinkPHP8 构建</p>
        </footer>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeSection = ref('overview')

const goAdmin = () => {
  window.open('http://39.105.173.6:8088/admin', '_blank')
}

const scrollTo = (id) => {
  activeSection.value = id
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const handleScroll = () => {
  const sections = ['overview', 'tech-stack', 'project-structure', 'api-docs', 'deployment', 'faq']
  const scrollPosition = window.scrollY + 200

  for (let i = sections.length - 1; i >= 0; i--) {
    const element = document.getElementById(sections[i])
    if (element && element.offsetTop <= scrollPosition) {
      activeSection.value = sections[i]
      break
    }
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.pc-doc-container {
  min-height: 100vh;
  background: #f8fafc;
}

/* 顶部导航 */
.pc-header {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 40px;
  height: 64px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 18px;
  font-weight: 600;
  color: #1a1a2e;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 24px;
}

.header-link {
  color: #606266;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}

.header-link:hover {
  color: #2563EB;
}

/* Hero 区域 */
.pc-hero {
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
  color: #fff;
  padding: 80px 40px;
  text-align: center;
}

.hero-content h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 16px;
}

.hero-subtitle {
  font-size: 20px;
  opacity: 0.9;
  margin-bottom: 24px;
}

.hero-badges {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
}

/* 主体内容 */
.pc-content {
  display: flex;
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px;
  gap: 40px;
}

/* 左侧导航 */
.pc-sidebar {
  width: 220px;
  flex-shrink: 0;
  position: sticky;
  top: 104px;
  height: fit-content;
}

.sidebar-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.sidebar-title {
  font-size: 12px;
  font-weight: 600;
  color: #909399;
  text-transform: uppercase;
  margin-bottom: 16px;
  letter-spacing: 1px;
}

.sidebar-menu {
  list-style: none;
}

.sidebar-menu li {
  margin-bottom: 4px;
}

.sidebar-menu a {
  display: block;
  padding: 10px 12px;
  color: #606266;
  text-decoration: none;
  font-size: 14px;
  border-radius: 8px;
  transition: all 0.2s;
}

.sidebar-menu a:hover {
  background: #f0f7ff;
  color: #2563EB;
}

.sidebar-menu a.active {
  background: #2563EB;
  color: #fff;
}

/* 右侧文档 */
.pc-main {
  flex: 1;
  min-width: 0;
}

.doc-section {
  background: #fff;
  border-radius: 12px;
  padding: 40px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.doc-section h2 {
  font-size: 24px;
  color: #1a1a2e;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #2563EB;
}

.doc-section h3 {
  font-size: 16px;
  color: #303133;
  margin: 24px 0 12px;
}

.doc-section p {
  color: #606266;
  line-height: 1.8;
  margin-bottom: 12px;
}

.doc-section strong {
  color: #2563EB;
}

.info-box {
  padding: 20px;
  border-radius: 8px;
  margin: 20px 0;
}

.info-box.success {
  background: #f0f9eb;
  border: 1px solid #e1f3d8;
}

.info-box p {
  margin-bottom: 8px;
}

.info-box p:last-child {
  margin-bottom: 0;
}

.project-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 24px;
}

.stat-card {
  background: #f8fafc;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #2563EB;
}

.stat-label {
  font-size: 13px;
  color: #909399;
  margin-top: 4px;
}

/* 技术栈 */
.tech-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}

.tech-category {
  background: #f8fafc;
  padding: 24px;
  border-radius: 8px;
}

.tech-category h3 {
  margin-top: 0;
  color: #2563EB;
  font-size: 15px;
}

.tech-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tech-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px dashed #e5e7eb;
}

.tech-item:last-child {
  border-bottom: none;
}

.tech-name {
  font-weight: 500;
  color: #303133;
}

.tech-desc {
  font-size: 12px;
  color: #909399;
}

/* 代码块 */
pre {
  background: #1a1a2e;
  color: #e6e6e6;
  padding: 16px;
  border-radius: 8px;
  overflow-x: auto;
  font-size: 13px;
  line-height: 1.6;
  margin: 16px 0;
}

code {
  font-family: 'Monaco', 'Menlo', monospace;
}

/* API 表格 */
.api-table {
  overflow-x: auto;
  margin: 16px 0;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

th, td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #ebeef5;
}

th {
  background: #fafafa;
  font-weight: 600;
  color: #303133;
}

td {
  color: #606266;
}

.method {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.method.get {
  background: #e6f7ff;
  color: #1890ff;
}

.method.post {
  background: #f6ffed;
  color: #52c41a;
}

/* 部署说明 */
.requirements {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin: 16px 0;
}

.req-item {
  display: flex;
  gap: 12px;
  padding: 12px 16px;
  background: #f8fafc;
  border-radius: 8px;
}

.req-label {
  font-weight: 600;
  color: #2563EB;
  white-space: nowrap;
}

.req-value {
  color: #606266;
}

/* FAQ */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.faq-item {
  background: #f8fafc;
  padding: 20px;
  border-radius: 8px;
  border-left: 3px solid #2563EB;
}

.faq-item h4 {
  color: #2563EB;
  margin-bottom: 8px;
  font-size: 15px;
}

.faq-item p {
  margin: 0;
}

/* 底部 */
.pc-footer {
  text-align: center;
  padding: 40px;
  color: #909399;
  font-size: 13px;
}

.pc-footer p {
  margin-bottom: 8px;
}

/* 响应式 */
@media (max-width: 1024px) {
  .pc-content {
    flex-direction: column;
  }

  .pc-sidebar {
    width: 100%;
    position: static;
  }

  .tech-grid,
  .requirements {
    grid-template-columns: 1fr;
  }

  .project-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .pc-header {
    padding: 0 20px;
  }

  .pc-hero {
    padding: 40px 20px;
  }

  .hero-content h1 {
    font-size: 32px;
  }

  .pc-content {
    padding: 20px;
  }

  .doc-section {
    padding: 24px;
  }
}
</style>

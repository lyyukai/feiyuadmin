<template>
  <div class="doc-container">
    <!-- 顶部导航 -->
    <header class="doc-header">
      <div class="header-inner">
        <div class="header-brand">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <div>
            <h1>飞鱼 Admin</h1>
            <p>企业级后台管理系统 · 技术文档</p>
          </div>
        </div>
        <div class="header-badges">
          <span class="badge blue">Vue 3.5</span>
          <span class="badge green">免费开源</span>
          <span class="badge purple">ThinkPHP 8</span>
        </div>
      </div>
    </header>

    <div class="doc-body">
      <!-- 左侧目录 -->
      <nav class="doc-toc">
        <div class="toc-section">
          <div class="toc-label">📖 文档</div>
          <ul>
            <li><a href="#overview" :class="{ active: activeSection === 'overview' }" @click.prevent="scrollTo('overview')">项目概览</a></li>
            <li><a href="#tech-stack" :class="{ active: activeSection === 'tech-stack' }" @click.prevent="scrollTo('tech-stack')">技术栈</a></li>
            <li><a href="#features" :class="{ active: activeSection === 'features' }" @click.prevent="scrollTo('features')">功能特性</a></li>
            <li><a href="#quick-start" :class="{ active: activeSection === 'quick-start' }" @click.prevent="scrollTo('quick-start')">快速开始</a></li>
          </ul>
        </div>
        <div class="toc-section">
          <div class="toc-label">🔧 开发指南</div>
          <ul>
            <li><a href="#crud-example" :class="{ active: activeSection === 'crud-example' }" @click.prevent="scrollTo('crud-example')">CRUD开发示例</a></li>
            <li><a href="#api-reference" :class="{ active: activeSection === 'api-reference' }" @click.prevent="scrollTo('api-reference')">API接口文档</a></li>
            <li><a href="#error-codes" :class="{ active: activeSection === 'error-codes' }" @click.prevent="scrollTo('error-codes')">错误码说明</a></li>
          </ul>
        </div>
        <div class="toc-section">
          <div class="toc-label">🚀 部署运维</div>
          <ul>
            <li><a href="#deployment" :class="{ active: activeSection === 'deployment' }" @click.prevent="scrollTo('deployment')">生产环境部署</a></li>
            <li><a href="#nginx-config" :class="{ active: activeSection === 'nginx-config' }" @click.prevent="scrollTo('nginx-config')">Nginx配置</a></li>
            <li><a href="#troubleshooting" :class="{ active: activeSection === 'troubleshooting' }" @click.prevent="scrollTo('troubleshooting')">常见问题排查</a></li>
            <li><a href="#screenshots" :class="{ active: activeSection === 'screenshots' }" @click.prevent="scrollTo('screenshots')">系统截图</a></li>
          </ul>
        </div>
      </nav>

      <!-- 主内容 -->
      <main class="doc-content">

        <!-- ========== 项目概览 ========== -->
        <section id="overview" class="doc-section">
          <h2 class="section-title">项目概览</h2>
          <p class="lead">飞鱼 Admin 是一套基于 <strong>Vue3 + Vite + Element Plus</strong> 前端技术栈，配合 <strong>ThinkPHP 8</strong> 后端框架的企业级后台管理解决方案。完全免费开源，采用 MIT 许可证，可商用。</p>

          <div class="info-cards">
            <div class="info-card">
              <div class="card-icon blue">🌐</div>
              <div>
                <h4>前后端分离</h4>
                <p>标准 RESTful API 通信，支持多端复用</p>
              </div>
            </div>
            <div class="info-card">
              <div class="card-icon green">⚡</div>
              <div>
                <h4>代码生成器</h4>
                <p>一键生成 CRUD，开发效率提升 80%</p>
              </div>
            </div>
            <div class="info-card">
              <div class="card-icon purple">🔐</div>
              <div>
                <h4>RBAC 权限</h4>
                <p>菜单+按钮级别细粒度权限控制</p>
              </div>
            </div>
            <div class="info-card">
              <div class="card-icon orange">🏢</div>
              <div>
                <h4>多租户</h4>
                <p>数据完全隔离，一套系统服务多客户</p>
              </div>
            </div>
          </div>

          <div class="meta-table">
            <table>
              <tbody>
                <tr><td class="label">前端框架</td><td>Vue 3.5 + Vite 5 + Element Plus 2</td></tr>
                <tr><td class="label">后端框架</td><td>ThinkPHP 8 + PHP 8.0+</td></tr>
                <tr><td class="label">数据库</td><td>MySQL 5.7+ / 8.0</td></tr>
                <tr><td class="label">认证方式</td><td>JWT Bearer Token</td></tr>
                <tr><td class="label">权限模型</td><td>RBAC + 菜单+按钮级</td></tr>
                <tr><td class="label">开源协议</td><td>MIT License</td></tr>
                <tr><td class="label">源码地址</td><td><a href="https://gitee.com/gynet/feiyuadmin" target="_blank">Gitee 仓库</a></td></tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ========== 技术栈 ========== -->
        <section id="tech-stack" class="doc-section">
          <h2 class="section-title">技术栈</h2>

          <h3>前端技术</h3>
          <div class="stack-grid">
            <div class="stack-item" v-for="item in frontendStack" :key="item.name">
              <div class="stack-icon" :style="{ background: item.color }">{{ item.emoji }}</div>
              <div class="stack-info">
                <strong>{{ item.name }}</strong>
                <span>{{ item.desc }}</span>
              </div>
            </div>
          </div>

          <h3>后端技术</h3>
          <div class="stack-grid">
            <div class="stack-item" v-for="item in backendStack" :key="item.name">
              <div class="stack-icon" :style="{ background: item.color }">{{ item.emoji }}</div>
              <div class="stack-info">
                <strong>{{ item.name }}</strong>
                <span>{{ item.desc }}</span>
              </div>
            </div>
          </div>
        </section>

        <!-- ========== 功能特性 ========== -->
        <section id="features" class="doc-section">
          <h2 class="section-title">功能特性</h2>
          <div class="features-grid">
            <div class="feature-card" v-for="f in features" :key="f.title">
              <div class="feature-icon">{{ f.icon }}</div>
              <h3>{{ f.title }}</h3>
              <p>{{ f.desc }}</p>
            </div>
          </div>
        </section>

        <!-- ========== 快速开始 ========== -->
        <section id="quick-start" class="doc-section">
          <h2 class="section-title">快速开始</h2>

          <h3>环境要求</h3>
          <div class="req-grid">
            <div class="req-item"><span class="req-icon">🐘</span><div><strong>PHP</strong><p>≥ 8.0 （推荐 8.2）</p></div></div>
            <div class="req-item"><span class="req-icon">🐬</span><div><strong>MySQL</strong><p>≥ 5.7.36 / 8.0</p></div></div>
            <div class="req-item"><span class="req-icon">📦</span><div><strong>Node.js</strong><p>≥ 16，推荐 20 LTS</p></div></div>
            <div class="req-item"><span class="req-icon">⚡</span><div><strong>pnpm</strong><p>≥ 7.0</p></div></div>
          </div>

          <h3>1. 克隆项目</h3>
          <pre><code>git clone https://gitee.com/gynet/feiyuadmin.git
cd feiyuadmin</code></pre>

          <h3>2. 前端安装依赖</h3>
          <pre><code>cd frontend
pnpm install</code></pre>

          <h3>3. 修改后端配置</h3>
          <pre><code># 编辑 backend/config/database.php
# 配置数据库连接信息

# 编辑 backend/config/cache.php
# 配置 Redis 缓存（可选）</code></pre>

          <h3>4. 初始化数据库</h3>
          <pre><code># 访问安装向导
http://your-domain.com/install

# 或手动执行 SQL
mysql -u root -p feiyuadmin < backend/sql/install.sql</code></pre>

          <h3>5. 启动开发服务</h3>
          <pre><code># 前端开发模式（移动端）
pnpm run dev

# 前端开发模式（PC端演示）
pnpm run dev:pc

# 后端服务（PHP内置服务器）
cd backend
php think run -H 0.0.0.0 -p 8088</code></pre>

          <h3>6. 构建生产版本</h3>
          <pre><code># 移动端构建
pnpm run build
# 输出: backend/public/admin/

# PC端构建
pnpm run build:pc
# 输出: backend/public/pc/</code></pre>
        </section>

        <!-- ========== CRUD开发示例 ========== -->
        <section id="crud-example" class="doc-section">
          <h2 class="section-title">CRUD 开发示例</h2>
          <p class="lead">以下以"新闻管理"模块为例，演示从后端到前端的完整开发流程。</p>

          <div class="step-nav">
            <span class="step active">① 数据库</span>
            <span class="step">② 后端Model</span>
            <span class="step">③ 后端Controller</span>
            <span class="step">④ 路由配置</span>
            <span class="step">⑤ 前端API</span>
            <span class="step">⑥ 前端页面</span>
          </div>

          <h3>① 创建数据表</h3>
          <pre><code>CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `category_id` int unsigned NOT NULL DEFAULT 0 COMMENT '分类ID',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '作者',
  `cover` varchar(500) NOT NULL DEFAULT '' COMMENT '封面图',
  `content` longtext COMMENT '内容',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态:0禁用1启用',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `views` int NOT NULL DEFAULT 0 COMMENT '浏览量',
  `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
// DISABLED:   KEY `idx_status` (`status`)
// DISABLED: ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='新闻表';</code></pre>
// DISABLED: 
          <h3>② 后端 Model</h3>
          <pre><code>&lt;?php
// backend/app/model/News.php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

class News extends Model
{
    use SoftDelete;

    protected $name = 'news';
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $type = [
        'category_id' => 'integer',
        'status'      => 'integer',
        'views'       => 'integer',
    ];

    // 关联分类
    public function category(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}</code></pre>

          <h3>③ 后端 Controller</h3>
          <pre><code>&lt;?php
// backend/app/controller/admin/News.php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\News as NewsModel;
use think\Request;
use think\Response;

class News
{
    protected Request $request;
    protected int $userId = 0;

    public function __construct()
    {
        $this->request = request();
        $this->userId  = (int) ($this->request->userId ?? 0);
    }

    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function error(string $msg = '操作失败', int $code = 400): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => []]);
    }

    protected function page(int $total, array $list): Response
    {
        return json(['code' => 0, 'msg' => 'success', 'total' => $total, 'data' => $list]);
    }

    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    protected function getPageParam(): array
    {
        $page  = (int) $this->param('page', 1);
        $limit = (int) $this->param('limit', 20);
        $limit = $limit > 100 ? 100 : $limit;
        return [$page, $limit];
    }

    /** 列表 */
    public function list(): Response
    {
        [$page, $limit] = $this->getPageParam();
        $keyword = $this->param('keyword', '');
        $status  = $this->param('status', '');
        $cid     = (int) $this->param('category_id', 0);

        $query = NewsModel::with(['category'])->whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('title', "%{$keyword}%")
                  ->whereOr('author', 'like', "%{$keyword}%");
            });
        }
        if ($cid > 0) $query->where('category_id', $cid);
        if ($status !== '') $query->where('status', (int) $status);

        $total = $query->count();
        $list  = $query->page($page, $limit)->order('id', 'desc')->select()->toArray();

        return $this->page($total, $list);
    }

    /** 详情 */
    public function read(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) return $this->error('参数错误');

        $news = NewsModel::with(['category'])->find($id);
        if (!$news) return $this->error('记录不存在');

        return $this->success($news);
    }

    /** 新增 */
    public function save(): Response
    {
        $title = $this->param('title', '');
        if (empty($title)) return $this->error('标题不能为空');

        $news = new NewsModel();
        $news->title       = $title;
        $news->category_id = (int) $this->param('category_id', 0);
        $news->author      = $this->param('author', '');
        $news->cover       = $this->param('cover', '');
        $news->content     = $this->param('content', '');
        $news->status      = (int) $this->param('status', 1);
        $news->sort        = (int) $this->param('sort', 0);
        $news->save();

        return $this->success(['id' => $news->id], '新增成功');
    }

    /** 更新 */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) return $this->error('参数错误');

        $news = NewsModel::find($id);
        if (!$news) return $this->error('记录不存在');

        $title = $this->param('title', '');
        if ($title !== '') $news->title = $title;
        $news->category_id = (int) $this->param('category_id', $news->category_id);
        if ($this->param('author', '') !== '') $news->author = $this->param('author', '');
        if ($this->param('cover', '') !== '') $news->cover = $this->param('cover', '');
        if ($this->param('content', '') !== '') $news->content = $this->param('content', '');
        if ($this->param('status', '') !== '') $news->status = (int) $this->param('status');
        if ($this->param('sort', '') !== '') $news->sort = (int) $this->param('sort');
        $news->save();

        return $this->success([], '更新成功');
    }

    /** 删除 */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) return $this->error('参数错误');

        $news = NewsModel::find($id);
        if (!$news) return $this->error('记录不存在');

        $news->delete();
        return $this->success([], '删除成功');
    }
}</code></pre>

          <h3>④ 后端路由</h3>
          <pre><code># backend/route/admin.php 或 route/api.php
use think\facade\Route;

Route::group('news', function () {
    Route::get('list', 'admin.News/list');   // GET  /api/news/list
    Route::get('read', 'admin.News/read');    // GET  /api/news/read?id=1
    Route::post('save', 'admin.News/save');   // POST /api/news/save
    Route::post('update', 'admin.News/update'); // POST /api/news/update
    Route::post('delete', 'admin.News/delete'); // POST /api/news/delete
})->middleware('adminAuth');</code></pre>

          <h3>⑤ 前端 API 文件</h3>
          <pre><code>// frontend/src/api/news.js
import request from '@/utils/request'

export function getNewsList(params) {
  return request({ url: '/news/list', method: 'get', params })
}

export function getNews(id) {
  return request({ url: '/news/read', method: 'get', params: { id } })
}

export function createNews(data) {
  return request({ url: '/news/save', method: 'post', data })
}

export function updateNews(data) {
  return request({ url: '/news/update', method: 'post', data })
}

export function deleteNews(id) {
  return request({ url: '/news/delete', method: 'post', data: { id } })
}</code></pre>

          <h3>⑥ 前端页面（Vue3）</h3>
          <pre><code>&lt;template&gt;
  &lt;div class="news-page"&gt;
    &lt;!-- 搜索栏 --&gt;
    &lt;div class="search-bar"&gt;
      &lt;el-input v-model="query.keyword" placeholder="搜索标题/作者" clearable @keyup.enter="fetchList" /&gt;
      &lt;el-select v-model="query.status" placeholder="状态" clearable&gt;
        &lt;el-option label="启用" :value="1" /&gt;
        &lt;el-option label="禁用" :value="0" /&gt;
      &lt;/el-select&gt;
      &lt;el-button type="primary" @click="fetchList"&gt;查询&lt;/el-button&gt;
      &lt;el-button type="primary" @click="openDialog('create')"&gt;新增新闻&lt;/el-button&gt;
    &lt;/div&gt;

    &lt;!-- 表格 --&gt;
    &lt;el-table :data="list" v-loading="loading" stripe&gt;
      &lt;el-table-column prop="id" label="ID" width="80" /&gt;
      &lt;el-table-column prop="title" label="标题" min-width="200" /&gt;
      &lt;el-table-column prop="category.name" label="分类" width="120" /&gt;
      &lt;el-table-column prop="author" label="作者" width="120" /&gt;
      &lt;el-table-column prop="status" label="状态" width="100"&gt;
        &lt;template #default="{ row }"&gt;
          &lt;el-tag :type="row.status === 1 ? 'success' : 'danger'"&gt;
            {{ row.status === 1 ? '启用' : '禁用' }}
          &lt;/el-tag&gt;
        &lt;/template&gt;
      &lt;/el-table-column&gt;
      &lt;el-table-column prop="views" label="浏览" width="100" /&gt;
      &lt;el-table-column prop="create_time" label="创建时间" width="180" /&gt;
      &lt;el-table-column label="操作" width="200" fixed="right"&gt;
        &lt;template #default="{ row }"&gt;
          &lt;el-button type="primary" link @click="openDialog('edit', row)"&gt;编辑&lt;/el-button&gt;
          &lt;el-button type="danger" link @click="handleDelete(row.id)"&gt;删除&lt;/el-button&gt;
        &lt;/template&gt;
      &lt;/el-table-column&gt;
    &lt;/el-table&gt;

    &lt;!-- 分页 --&gt;
    &lt;el-pagination
      v-model:current-page="query.page"
      v-model:page-size="query.limit"
      :total="total"
      :page-sizes="[10, 20, 50]"
      layout="total, sizes, prev, pager, next"
      @change="fetchList"
      style="margin-top: 16px"
    /&gt;
  &lt;/div&gt;
&lt;/template&gt;

&lt;script setup&gt;
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { getNewsList, deleteNews } from '@/api/news'

const loading = ref(false)
const list = ref([])
const total = ref(0)
const query = reactive({ page: 1, limit: 20, keyword: '', status: '' })

const fetchList = async () => {
  loading.value = true
  try {
    const res = await getNewsList(query)
    list.value = res.data
    total.value = res.total
  } finally {
    loading.value = false
  }
}

const handleDelete = async (id) => {
  await ElMessageBox.confirm('确定删除该新闻？', '提示', { type: 'warning' })
  await deleteNews(id)
  ElMessage.success('删除成功')
  fetchList()
}

onMounted(() => fetchList())
&lt;/script&gt;</code></pre>
        </section>

        <!-- ========== API接口文档 ========== -->
        <section id="api-reference" class="doc-section">
          <h2 class="section-title">API 接口文档</h2>

          <h3>通用说明</h3>
          <p>所有接口采用 <strong>RESTful</strong> 设计规范，认证通过 <strong>JWT Bearer Token</strong> 实现。</p>

          <div class="callout warning">
            <strong>Base URL：</strong>生产环境为 <code>https://your-domain.com/api/</code>，开发环境为 <code>http://localhost:8088/api/</code>
          </div>

          <h3>统一响应格式</h3>
          <pre><code>{
  "code": 0,           // 状态码，0=成功，其他=失败（见错误码表）
  "msg": "success",   // 提示信息
  "total": 100,        // 分页时返回总数
  "data": {}           // 返回数据
}</code></pre>

          <h3>认证 Header</h3>
          <pre><code>Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json</code></pre>

          <h3>分页参数</h3>
          <div class="table-wrapper">
            <table class="param-table">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>page</td><td>int</td><td>否</td><td>页码，默认 1</td></tr>
                <tr><td>limit</td><td>int</td><td>否</td><td>每页条数，默认 20，最大 100</td></tr>
              </tbody>
            </table>
          </div>

          <h3>用户管理 /user</h3>

          <div class="api-group">
            <div class="api-method get">GET</div>
            <div class="api-path">/user/list — 用户列表</div>
          </div>
          <div class="table-wrapper">
            <table class="param-table">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>keyword</td><td>string</td><td>否</td><td>搜索：用户名/昵称/手机号</td></tr>
                <tr><td>dept_id</td><td>int</td><td>否</td><td>部门ID（会包含子部门）</td></tr>
                <tr><td>status</td><td>int</td><td>否</td><td>状态：0=禁用，1=启用</td></tr>
                <tr><td>page</td><td>int</td><td>否</td><td>页码，默认1</td></tr>
                <tr><td>limit</td><td>int</td><td>否</td><td>每页条数，默认20</td></tr>
              </tbody>
            </table>
          </div>
          <pre><code>// 响应示例
{
  "code": 0,
  "msg": "success",
  "total": 58,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "nickname": "超级管理员",
      "avatar": "/uploads/avatar/1.jpg",
      "mobile": "13800138000",
      "email": "admin@example.com",
      "dept_id": 1,
      "dept": { "id": 1, "name": "技术部" },
      "post": { "id": 1, "name": "技术总监" },
      "roles": ["super_admin"],
      "status": 1,
      "create_time": "2024-01-01 08:00:00"
    }
  ]
}</code></pre>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/user/save — 新增用户</div>
          </div>
          <div class="table-wrapper">
            <table class="param-table">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>username</td><td>string</td><td>✅</td><td>用户名，4-20位</td></tr>
                <tr><td>password</td><td>string</td><td>✅</td><td>密码，6-20位</td></tr>
                <tr><td>nickname</td><td>string</td><td>✅</td><td>昵称</td></tr>
                <tr><td>role_ids</td><td>array</td><td>否</td><td>角色ID数组，如 [1, 2]</td></tr>
                <tr><td>mobile</td><td>string</td><td>否</td><td>手机号</td></tr>
                <tr><td>email</td><td>string</td><td>否</td><td>邮箱</td></tr>
                <tr><td>dept_id</td><td>int</td><td>否</td><td>部门ID</td></tr>
                <tr><td>post_id</td><td>int</td><td>否</td><td>岗位ID</td></tr>
                <tr><td>status</td><td>int</td><td>否</td><td>状态，默认1</td></tr>
              </tbody>
            </table>
          </div>
          <pre><code>// 请求示例
POST /api/user/save
{
  "username": "zhangsan",
  "password": "123456",
  "nickname": "张三",
  "role_ids": [2, 3],
  "mobile": "13900139000",
  "dept_id": 2
}

// 成功响应
{ "code": 0, "msg": "新增成功", "data": { "id": 10 } }</code></pre>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/user/update — 更新用户</div>
          </div>
          <pre><code>// 请求示例（只需传需要更新的字段，id必传）
POST /api/user/update
{
  "id": 10,
  "nickname": "张三（新）",
  "role_ids": [2],
  "status": 1
}</code></pre>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/user/delete — 删除用户</div>
          </div>
          <pre><code>// 请求示例
POST /api/user/delete
{ "id": 10 }

// 成功响应
{ "code": 0, "msg": "删除成功", "data": [] }

// 失败：删除当前登录用户
{ "code": 400, "msg": "不能删除当前登录用户", "data": [] }</code></pre>

          <h3>角色管理 /role</h3>

          <div class="api-group">
            <div class="api-method get">GET</div>
            <div class="api-path">/role/list — 角色列表</div>
          </div>
          <div class="table-wrapper">
            <table class="param-table">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>keyword</td><td>string</td><td>否</td><td>搜索：角色名/角色代码</td></tr>
                <tr><td>status</td><td>int</td><td>否</td><td>状态</td></tr>
              </tbody>
            </table>
          </div>
          <pre><code>{
  "code": 0,
  "msg": "success",
  "total": 5,
  "data": [
    {
      "id": 1,
      "name": "超级管理员",
      "code": "super_admin",
      "status": 1,
      "sort": 0,
      "remark": "拥有所有权限",
      "create_time": "2024-01-01 08:00:00"
    },
    {
      "id": 2,
      "name": "运营主管",
      "code": "operation_manager",
      "status": 1,
      "sort": 1,
      "remark": "运营模块管理权限",
      "create_time": "2024-01-01 08:00:00"
    }
  ]
}</code></pre>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/role/save — 新增角色</div>
          </div>
          <pre><code>// 请求示例
POST /api/role/save
{
  "name": "内容编辑",
  "code": "content_editor",
  "status": 1,
  "sort": 10,
  "data_scope": "all",
  "remark": "内容模块编辑权限"
}</code></pre>

          <h3>菜单管理 /menu</h3>

          <div class="api-group">
            <div class="api-method get">GET</div>
            <div class="api-path">/menu/nav — 获取登录用户菜单导航</div>
          </div>
          <pre><code>{
  "code": 0,
  "msg": "success",
  "data": [
    {
      "id": 1,
      "name": "工作台",
      "path": "/dashboard",
      "icon": "Odometer",
      "children": []
    },
    {
      "id": 2,
      "name": "系统管理",
      "path": "/system",
      "icon": "Setting",
      "children": [
        { "id": 3, "name": "用户管理", "path": "/system/user", "icon": "User" }
      ]
    }
  ]
}</code></pre>

          <h3>登录认证 /login</h3>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/login — 用户登录</div>
          </div>
          <pre><code>// 请求
POST /api/login
{
  "username": "admin",
  "password": "admin123"
}

// 成功响应
{
  "code": 0,
  "msg": "登录成功",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 86400,
    "user_info": {
      "id": 1,
      "username": "admin",
      "nickname": "超级管理员",
      "avatar": ""
    }
  }
}</code></pre>

          <div class="api-group">
            <div class="api-method post">POST</div>
            <div class="api-path">/logout — 退出登录</div>
          </div>
          <pre><code>// 成功响应
{ "code": 0, "msg": "退出成功", "data": [] }</code></pre>
        </section>

        <!-- ========== 错误码说明 ========== -->
        <section id="error-codes" class="doc-section">
          <h2 class="section-title">错误码说明</h2>

          <div class="table-wrapper">
            <table class="param-table">
              <thead>
                <tr><th>错误码</th><th>说明</th><th>处理建议</th></tr>
              </thead>
              <tbody>
                <tr><td class="code">0</td><td>成功</td><td>—</td></tr>
                <tr><td class="code">400</td><td>业务逻辑错误</td><td>如"用户名已存在"、"权限不足"，根据 msg 提示处理</td></tr>
                <tr><td class="code">401</td><td>未授权 / Token失效</td><td>跳转登录页，重新获取 Token</td></tr>
                <tr><td class="code">403</td><td>无权限访问</td><td>当前账号无此接口权限，联系管理员</td></tr>
                <tr><td class="code">404</td><td>接口不存在</td><td>检查请求路径是否正确</td></tr>
                <tr><td class="code">422</td><td>参数校验失败</td><td>检查必填参数是否完整，格式是否正确</td></tr>
                <tr><td class="code">500</td><td>服务器内部错误</td><td>查看后端日志，联系技术支持</td></tr>
                <tr><td class="code">502</td><td>网关错误</td><td>Nginx 配置问题或后端服务未启动</td></tr>
                <tr><td class="code">503</td><td>服务不可用</td><td>后端进程崩溃或内存不足</td></tr>
              </tbody>
            </table>
          </div>

          <div class="callout info">
            <strong>前端 HTTP 状态码处理：</strong>401 自动跳转登录，404 提示"接口不存在"，500+ 提示"服务器错误"。具体业务错误码在响应 body 的 <code>code</code> 字段中。
          </div>
        </section>

        <!-- ========== 生产环境部署 ========== -->
        <section id="deployment" class="doc-section">
          <h2 class="section-title">生产环境部署</h2>

          <h3>服务器推荐配置</h3>
          <div class="req-grid">
            <div class="req-item"><span class="req-icon">🖥️</span><div><strong>CPU</strong><p>2核以上，推荐4核</p></div></div>
            <div class="req-item"><span class="req-icon">💾</span><div><strong>内存</strong><p>≥ 4GB，推荐 8GB</p></div></div>
            <div class="req-item"><span class="req-icon">💿</span><div><strong>系统</strong><p>CentOS 7+ / Ubuntu 20+</p></div></div>
            <div class="req-item"><span class="req-icon">🌐</span><div><strong>域名</strong><p>已备案域名，解析到服务器IP</p></div></div>
          </div>

          <h3>基础环境安装</h3>
          <pre><code># CentOS 安装 Nginx + PHP 8.2 + MySQL 8.0

# 1. 安装 Nginx
yum install -y nginx
systemctl enable --now nginx

# 2. 安装 PHP 8.2（Remi源）
yum install -y epel-release
yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf module reset php
dnf module enable php:8.2
yum install -y php-fpm php-cli php-mysqlnd php-json php-mbstring php-tokenizer php-xml php-curl php-gd php-bcmath php-pdo

# 3. 安装 MySQL 8.0
yum install -y https://dev.mysql.com/get/mysql80-community-release-el8-4.noarch.rpm
yum install -y mysql-community-server
systemctl enable --now mysqld

# 4. 安装 Redis
yum install -y redis
systemctl enable --now redis

# 5. 安装 Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer config -g repo.packagist composer https://packagist.phpcomposer.com</code></pre>

          <h3>项目部署步骤</h3>
          <pre><code># 1. 将后端代码放到 /www/wwwroot/feiyuadmin/backend/
# 前端构建到 /www/wwwroot/feiyuadmin/backend/public/pc/

# 2. 安装后端依赖
cd /www/wwwroot/feiyuadmin/backend
composer install --no-dev --optimize-autoloader

# 3. 配置 .env
cp .env.example .env
# 编辑 .env 配置数据库、Redis、APP_KEY

# 4. 生成应用密钥
php think jwt:generate

# 5. 设置目录权限
chown -R nginx:nginx /www/wwwroot/feiyuadmin/backend
chmod -R 755 /www/wwwroot/feiyuadmin/backend
chmod -R 777 /www/wwwroot/feiyuadmin/backend/runtime
chmod -R 777 /www/wwwroot/feiyuadmin/backend/public/uploads</code></pre>

          <h3>PHP-FPM 配置</h3>
          <pre><code># /etc/php-fpm.d/www.conf 关键配置
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

# 内存泄漏防护
pm.max_requests = 500</code></pre>

          <h3>配置守护进程（Supervisor）</h3>
          <pre><code># 安装 Supervisor
pip install supervisor
echo_supervisord_conf > /etc/supervisord.conf

# 添加 feiyuadmin 配置
cat >> /etc/supervisord.d/feiyuadmin.ini << 'EOF'
[program:feiyuadmin-queue]
command=php /www/wwwroot/feiyuadmin/backend/think queue:work --queue default --sleep 3 --tries 3
directory=/www/wwwroot/feiyuadmin/backend
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=nginx
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log
EOF

supervisord -c /etc/supervisord.conf
supervisorctl reload</code></pre>
        </section>

        <!-- ========== Nginx配置 ========== -->
        <section id="nginx-config" class="doc-section">
          <h2 class="section-title">Nginx 配置</h2>

          <h3>PC 端（演示站）完整配置</h3>
          <pre><code># /etc/nginx/conf.d/feiyuadmin.conf

# PC端（前后端分离，/pc/ 路径走静态资源）
server {
    listen 80;
    server_name feiyuadmin.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name feiyuadmin.example.com;

    # SSL证书
    ssl_certificate     /etc/nginx/ssl/example.com.pem;
    ssl_certificate_key /etc/nginx/ssl/example.com.key;
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_session_cache   shared:SSL:10m;
    ssl_session_timeout 1d;

    root /www/wwwroot/feiyuadmin/backend/public;
    index index.html index.php;

    # 访问日志
    access_log /var/log/nginx/feiyuadmin_access.log;
    error_log  /var/log/nginx/feiyuadmin_error.log;

    # Gzip压缩
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

    # 安全头
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # ========== PC端演示站（静态） ==========
    location /pc/ {
        alias /www/wwwroot/feiyuadmin/backend/public/pc/;
        try_files $uri $uri/ /pc/index.html;
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # ========== 后端 API ==========
    location /api/ {
        # 代理到 PHP-FPM
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;

        # 上传文件大小限制（需与 think-file 配置一致）
        client_max_body_size 50m;
    }

    # ========== ThinkPHP 路由（PHP文件） ==========
    location ~ \.php(/|$) {
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        client_max_body_size 50m;
    }

    # ========== 静态资源缓存 ==========
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # ========== 禁止访问敏感文件 ==========
    location ~ /\.env {
        deny all;
    }
    location ~ /\.git {
        deny all;
    }
    location ~ /runtime {
        deny all;
    }
}</code></pre>

          <h3>移动端后台配置</h3>
          <pre><code>    # 移动端后台（/admin/ 路径）
    location /admin/ {
        alias /www/wwwroot/feiyuadmin/backend/public/admin/;
        try_files $uri $uri/ /admin/index.html;
        expires 30d;
    }

    # 移动端 API
    location /adminapi/ {
        rewrite ^/adminapi/(.*)$ /api/$1 break;
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }</code></pre>

          <h3>Nginx 常用命令</h3>
          <pre><code># 测试配置语法
nginx -t

# 重载配置（不中断服务）
nginx -s reload

# 重启
systemctl restart nginx

# 查看并发连接
netstat -an | grep :80 | wc -l

# 查看活跃连接
ss -s</code></pre>
        </section>

        <!-- ========== 常见问题排查 ========== -->
        <section id="troubleshooting" class="doc-section">
          <h2 class="section-title">常见问题排查</h2>

          <div class="qa-list">
            <div class="qa-item" v-for="item in qaList" :key="item.q">
              <h4>❓ {{ item.q }}</h4>
              <p>{{ item.a }}</p>
            </div>
          </div>

          <h3>日志查看</h3>
          <pre><code># Nginx 错误日志
tail -f /var/log/nginx/feiyuadmin_error.log

# PHP-FPM 错误日志
tail -f /var/log/php-fpm/error.log

# ThinkPHP 应用日志
tail -f /www/wwwroot/feiyuadmin/backend/runtime/log/$(date +Ymd).log

# MySQL 慢查询日志
tail -f /var/log/mysql/slow-query.log

# Supervisor 日志（队列）
tail -f /www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log</code></pre>

          <h3>性能调优</h3>
          <pre><code># PHP-FPM 进程数估算：CPU核心数 × 2 + 1
# 例如 4核 CPU：pm.max_children = 9

# MySQL 关键参数（my.cnf）
innodb_buffer_pool_size = 1G    # 建议为物理内存的 50-70%
max_connections = 500
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Redis 持久化（生产环境建议 RDB + AOF）
appendonly yes
appendfsync everysec
maxmemory 512mb
maxmemory-policy allkeys-lru</code></pre>
        </section>

        <!-- ========== 系统截图 ========== -->
        <section id="screenshots" class="doc-section">
          <h2 class="section-title">系统截图</h2>
          <p class="lead">飞鱼 Admin 界面展示（演示站 /pc/demo）</p>

          <div class="screenshots-grid">
            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="placeholder-label">🚀 工作台</div>
                <div class="placeholder-desc">数据概览 · 快捷入口 · 通知公告</div>
              </div>
              <h4>工作台</h4>
              <p>数据概览、快捷操作入口、站内通知一目了然</p>
            </div>

            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="placeholder-label">👥 用户管理</div>
                <div class="placeholder-desc">用户列表 · 角色分配 · 状态管理</div>
              </div>
              <h4>用户管理</h4>
              <p>支持新增/编辑/删除/禁用用户，灵活分配多个角色</p>
            </div>

            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="placeholder-label">🔐 角色权限</div>
                <div class="placeholder-desc">角色列表 · 菜单权限 · 数据范围</div>
              </div>
              <h4>角色权限管理</h4>
              <p>基于 RBAC 模型的菜单+按钮级别权限配置</p>
            </div>

            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="placeholder-label">📋 菜单管理</div>
                <div class="placeholder-desc">菜单配置 · 路由设置 · 图标选择</div>
              </div>
              <h4>菜单管理</h4>
              <p>可视化菜单配置，支持多级菜单、路由、图标、权限标识</p>
            </div>

            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="placeholder-label">📊 数据大屏</div>
                <div class="placeholder-desc">拖拽设计 · ECharts图表 · 实时数据</div>
              </div>
              <h4>数据大屏</h4>
              <p>拖拽式大屏设计器，内置丰富 ECharts 图表组件</p>
            </div>

            <div class="screenshot-item">
              <div class="screenshot-placeholder" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                <div class="placeholder-label">🔄 工作流</div>
                <div class="placeholder-label">流程设计 · 实例管理 · 待办审批</div>
              </div>
              <h4>工作流引擎</h4>
              <p>可视化流程设计器，支持审批流、条件分支、节点配置</p>
            </div>
          </div>
        </section>

      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const activeSection = ref('overview')

const frontendStack = [
  { name: 'Vue 3.5', desc: '渐进式前端框架，Composition API', emoji: '⚡', color: '#42b983' },
  { name: 'Vite 5', desc: '下一代前端构建工具，毫秒级 HMR', emoji: '🚀', color: '#646cff' },
  { name: 'Element Plus 2', desc: 'Vue3 UI 组件库，70+ 组件', emoji: '🎨', color: '#409eff' },
  { name: 'Pinia', desc: '新一代状态管理，TypeScript 支持', emoji: '📦', color: '#f7df1e' },
  { name: 'Vue Router 4', desc: 'Vue 官方路由，支持懒加载', emoji: '🛤️', color: '#4abf8a' },
  { name: 'Axios', desc: 'Promise HTTP 客户端，统一请求拦截', emoji: '🌐', color: '#5a29e4' },
  { name: 'ECharts 5', desc: '数据可视化图表库，20+ 图表类型', emoji: '📊', color: '#e43961' },
  { name: 'WangEditor', desc: '富文本编辑器，轻量可扩展', emoji: '📝', color: '#1890ff' },
]

const backendStack = [
  { name: 'PHP 8.2', desc: '后端核心语言，JIT 编译', emoji: '🐘', color: '#777bb4' },
  { name: 'ThinkPHP 8', desc: '高性能 PHP 框架，协程支持', emoji: '⚡', color: '#63b500' },
  { name: 'MySQL 8.0', desc: '关系型数据库，InnoDB 引擎', emoji: '🐬', color: '#00758f' },
  { name: 'Redis', desc: '内存数据库，缓存+Session+队列', emoji: '🔴', color: '#dc382d' },
  { name: 'JWT', desc: '无状态认证，令牌自动续期', emoji: '🔑', color: '#d63aff' },
  { name: 'RBAC', desc: '基于角色的访问控制模型', emoji: '🔐', color: '#f7c948' },
]

const features = [
  { icon: '🔐', title: '用户权限管理', desc: '基于 RBAC 模型，支持用户、角色、岗位、部门管理，菜单+按钮级别权限控制' },
  { icon: '⚡', title: '代码生成器', desc: '一键生成 CRUD 代码，可视化配置字段、验证规则、关联关系' },
  { icon: '🔄', title: '工作流引擎', desc: '可视化流程设计器，支持审批流、条件分支、数据处理节点' },
  { icon: '🏢', title: '多租户支持', desc: '数据完全隔离，租户套餐管理，一套系统服务多个客户' },
  { icon: '📊', title: '数据可视化', desc: '内置 ECharts，支持拖拽式大屏设计器，多图表联动' },
  { icon: '📱', title: '响应式设计', desc: '自适应 PC、平板、手机，移动端后台 + PC 演示站双端' },
  { icon: '🔔', title: '消息通知', desc: '多渠道通知（短信/邮件/微信），可视化模板配置' },
  { icon: '⏰', title: '定时任务', desc: '可视化 Crontab 管理，支持日志记录、任务依赖' },
  { icon: '💳', title: '支付渠道', desc: '支付宝/微信支付配置，订单、退款、分账一体化' },
  { icon: '📝', title: '表单设计器', desc: '拖拽式表单构建，支持数据联动、逻辑规则' },
  { icon: '🔍', title: '操作审计', desc: '完整操作日志、登录日志记录，支持追溯查询' },
  { icon: '🛠️', title: '系统工具', desc: '部门管理、岗位管理、字典管理、系统配置' },
]

const qaList = [
  { q: '访问页面空白，控制台报错 401 Unauthorized', a: 'Token 过期或未携带。检查请求 Header 是否正确添加了 Authorization: Bearer {token}，或清除 localStorage 重新登录。' },
  { q: '后端接口返回 502 Bad Gateway', a: 'Nginx 无法连接到 PHP-FPM。检查 PHP-FPM 是否运行（systemctl status php-fpm），端口是否一致（配置中为 9000），以及 SELinux/AppArmor 设置。' },
  { q: '文件上传失败，提示 "上传目录不可写"', a: '执行 chmod -R 777 /www/wwwroot/feiyuadmin/backend/public/uploads，并确保 owner 为 nginx 用户。' },
  { q: '页面加载慢，Network 显示大量资源 304', a: 'Nginx 静态资源未配置缓存。添加 expires 指令和 gzip 压缩配置，参考本文档 Nginx 配置章节。' },
  { q: '登录后刷新页面跳转回登录页', a: '通常是 Token 校验失败。检查后端 jwt.secret 配置、前后端密钥是否一致，或 Redis 连接是否正常（Session 存储在 Redis 时）。' },
  { q: '定时任务没有执行', a: '确认 Supervisor/队列进程是否在运行（supervisorctl status），以及 think queue:work 命令路径是否正确。' },
  { q: '数据库迁移报错 SQLSTATE[42000]', a: '检查 MySQL 版本是否 >= 5.7，确认数据库字符集为 utf8mb4，以及是否有对应权限。' },
]

const scrollTo = (id) => {
  const el = document.getElementById(id)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    activeSection.value = id
  }
}

let observer
onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        activeSection.value = entry.target.id
      }
    })
  }, { threshold: 0.2 })

  document.querySelectorAll('.doc-section[id]').forEach(el => {
    observer.observe(el)
  })
})

onUnmounted(() => {
  observer?.disconnect()
})
</script>

<style scoped>
.doc-container {
  min-height: 100vh;
  background: #f5f7fa;
  font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
}

/* ========== 顶部导航 ========== */
.doc-header {
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
  color: #fff;
  padding: 0 40px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}

.header-inner {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}

.header-brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-brand h1 {
  font-size: 18px;
  margin: 0;
}

.header-brand p {
  font-size: 12px;
  opacity: 0.7;
  margin: 0;
}

.header-badges {
  display: flex;
  gap: 8px;
}

.badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.badge.blue { background: rgba(37,99,235,0.3); color: #fff; }
.badge.green { background: rgba(82,196,26,0.3); color: #a8f0a8; }
.badge.purple { background: rgba(139,92,246,0.3); color: #ddd6fe; }

/* ========== 文档主体 ========== */
.doc-body {
  display: flex;
  max-width: 1400px;
  margin: 0 auto;
  gap: 0;
}

/* ========== 左侧目录 ========== */
.doc-toc {
  width: 220px;
  flex-shrink: 0;
  padding: 32px 20px;
  position: sticky;
  top: 64px;
  height: calc(100vh - 64px);
  overflow-y: auto;
  background: #fff;
  border-right: 1px solid #eee;
}

.toc-section {
  margin-bottom: 24px;
}

.toc-label {
  font-size: 11px;
  font-weight: 600;
  color: #909399;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
  padding-left: 8px;
}

.doc-toc ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.doc-toc li {
  margin-bottom: 2px;
}

.doc-toc a {
  display: block;
  padding: 7px 10px;
  color: #606266;
  text-decoration: none;
  font-size: 13px;
  border-radius: 6px;
  transition: all 0.15s;
}

.doc-toc a:hover {
  background: #e6f7ff;
  color: #2563EB;
}

.doc-toc a.active {
  background: #2563EB;
  color: #fff;
}

/* ========== 主内容 ========== */
.doc-content {
  flex: 1;
  min-width: 0;
  padding: 40px 48px;
  background: #fff;
}

.doc-section {
  margin-bottom: 80px;
  padding-bottom: 60px;
  border-bottom: 1px solid #eee;
}

.doc-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.section-title {
  font-size: 26px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0 0 24px 0;
  padding-bottom: 12px;
  border-bottom: 3px solid #2563EB;
  display: inline-block;
}

.doc-section h3 {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
  margin: 28px 0 12px;
  padding-top: 8px;
}

.doc-section h4 {
  font-size: 14px;
  font-weight: 600;
  color: #303133;
  margin: 16px 0 8px;
}

.lead {
  font-size: 15px;
  color: #606266;
  line-height: 1.8;
  margin-bottom: 20px;
}

p {
  color: #606266;
  line-height: 1.8;
  margin-bottom: 12px;
}

a {
  color: #2563EB;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

/* ========== 信息卡片 ========== */
.info-cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin: 24px 0;
}

.info-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid #eee;
}

.card-icon {
  font-size: 28px;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  flex-shrink: 0;
}

.card-icon.blue { background: rgba(37,99,235,0.1); }
.card-icon.green { background: rgba(82,196,26,0.1); }
.card-icon.purple { background: rgba(139,92,246,0.1); }
.card-icon.orange { background: rgba(249,115,22,0.1); }

.info-card h4 {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 4px;
}

.info-card p {
  font-size: 13px;
  color: #909399;
  margin: 0;
}

/* ========== 元数据表格 ========== */
.meta-table {
  margin-top: 24px;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

table td {
  padding: 10px 14px;
  border-bottom: 1px solid #f0f0f0;
  color: #606266;
}

table td.label {
  font-weight: 600;
  color: #303133;
  width: 120px;
  background: #fafafa;
}

/* ========== 技术栈 ========== */
.stack-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}

.stack-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.stack-icon {
  font-size: 20px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  flex-shrink: 0;
}

.stack-info strong {
  display: block;
  font-size: 12px;
  color: #303133;
}

.stack-info span {
  display: block;
  font-size: 11px;
  color: #909399;
  margin-top: 1px;
}

/* ========== 功能特性 ========== */
.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.feature-card {
  padding: 20px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid #f0f0f0;
  transition: all 0.2s;
}

.feature-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.06);
  border-color: #d0e8ff;
}

.feature-icon {
  font-size: 24px;
  margin-bottom: 10px;
}

.feature-card h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a2e;
  margin: 0 0 6px;
}

.feature-card p {
  font-size: 12px;
  color: #909399;
  margin: 0;
  line-height: 1.5;
}

/* ========== 环境要求 ========== */
.req-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}

.req-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.req-icon {
  font-size: 24px;
}

.req-item strong {
  display: block;
  font-size: 13px;
  color: #303133;
}

.req-item p {
  font-size: 12px;
  color: #909399;
  margin: 0;
}

/* ========== 代码块 ========== */
pre {
  background: #1a1a2e;
  color: #e6e6e6;
  padding: 16px 20px;
  border-radius: 8px;
  overflow-x: auto;
  font-size: 12.5px;
  line-height: 1.7;
  margin: 12px 0 20px;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

code {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

/* ========== 步骤导航 ========== */
.step-nav {
  display: flex;
  gap: 0;
  margin-bottom: 28px;
  flex-wrap: wrap;
  gap: 4px;
}

.step {
  padding: 6px 14px;
  background: #f0f0f0;
  border-radius: 20px;
  font-size: 12px;
  color: #909399;
  font-weight: 500;
}

.step.active {
  background: #2563EB;
  color: #fff;
}

/* ========== API 相关 ========== */
.api-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 24px 0 8px;
  padding: 10px 14px;
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid #eee;
}

.api-method {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 4px;
  letter-spacing: 0.5px;
}

.api-method.get { background: #52c41a; color: #fff; }
.api-method.post { background: #2563EB; color: #fff; }
.api-method.put { background: #fa8c16; color: #fff; }
.api-method.delete { background: #f5222d; color: #fff; }

.api-path {
  font-size: 13px;
  color: #303133;
  font-weight: 500;
  font-family: 'Monaco', monospace;
}

/* ========== 参数表格 ========== */
.table-wrapper {
  overflow-x: auto;
  margin: 12px 0;
}

.param-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
  border: 1px solid #f0f0f0;
  border-radius: 6px;
  overflow: hidden;
}

.param-table thead tr {
  background: #fafafa;
}

.param-table th {
  padding: 9px 12px;
  text-align: left;
  font-weight: 600;
  color: #303133;
  border-bottom: 1px solid #eee;
  white-space: nowrap;
}

.param-table td {
  padding: 8px 12px;
  border-bottom: 1px solid #f5f5f5;
  color: #606266;
}

.param-table tr:last-child td {
  border-bottom: none;
}

.param-table td.code {
  font-family: 'Monaco', monospace;
  color: #d43961;
  font-weight: 600;
}

/* ========== 提示框 ========== */
.callout {
  padding: 14px 16px;
  border-radius: 8px;
  margin: 16px 0;
  font-size: 13px;
  line-height: 1.7;
}

.callout.warning {
  background: #fffbe6;
  border: 1px solid #ffe58f;
  color: #7c5914;
}

.callout.info {
  background: #e6f7ff;
  border: 1px solid #91d5ff;
  color: #003a8c;
}

/* ========== 截图区域 ========== */
.screenshots-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.screenshot-item {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #eee;
  transition: all 0.2s;
}

.screenshot-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

.screenshot-placeholder {
  height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.placeholder-label {
  font-size: 18px;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.placeholder-desc {
  font-size: 12px;
  color: rgba(255,255,255,0.8);
}

.screenshot-item h4 {
  font-size: 14px;
  font-weight: 600;
  color: #303133;
  margin: 12px 16px 4px;
}

.screenshot-item p {
  font-size: 12px;
  color: #909399;
  margin: 0 16px 12px;
  line-height: 1.5;
}

/* ========== Q&A ========== */
.qa-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.qa-item {
  background: #f8fafc;
  padding: 16px 20px;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.qa-item h4 {
  font-size: 13px;
  color: #2563EB;
  margin: 0 0 8px;
}

.qa-item p {
  font-size: 13px;
  color: #606266;
  margin: 0;
  line-height: 1.7;
}

/* ========== 响应式 ========== */
@media (max-width: 1200px) {
  .doc-toc {
    width: 180px;
  }
  .stack-grid { grid-template-columns: repeat(2, 1fr); }
  .features-grid { grid-template-columns: repeat(2, 1fr); }
  .screenshots-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
  .doc-toc {
    display: none;
  }
  .doc-body {
    display: block;
  }
  .doc-content {
    padding: 24px 20px;
  }
  .stack-grid { grid-template-columns: 1fr; }
  .features-grid { grid-template-columns: 1fr; }
  .info-cards { grid-template-columns: 1fr; }
  .req-grid { grid-template-columns: repeat(2, 1fr); }
  .screenshots-grid { grid-template-columns: 1fr; }
}
</style>

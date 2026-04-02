<template>
  <div class="doc-container">
    <header class="doc-header">
      <div class="header-inner">
        <a href="/pc/" class="back-home">
          <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼 Admin</span>
        </a>
        <nav class="header-nav">
          <a href="/doc#overview">概览</a>
          <a href="/doc#quick-start">快速开始</a>
          <a href="/doc#api-reference">API</a>
          <a href="/doc#crud-example">开发指南</a>
          <a href="/doc#deployment">部署</a>
        </nav>
        <a href="http://demo.fydev.cn/admin" target="_blank" class="btn-primary">进入后台</a>
      </div>
    </header>

    <div class="doc-body">
      <nav class="doc-toc">
        <div class="toc-section">
          <div class="toc-title">📖 文档</div>
          <ul>
            <li><a href="#overview" :class="{ active: activeSection === 'overview' }" @click.prevent="scrollTo('overview')">项目概览</a></li>
            <li><a href="#tech-stack" :class="{ active: activeSection === 'tech-stack' }" @click.prevent="scrollTo('tech-stack')">技术栈</a></li>
            <li><a href="#features" :class="{ active: activeSection === 'features' }" @click.prevent="scrollTo('features')">功能特性</a></li>
            <li><a href="#quick-start" :class="{ active: activeSection === 'quick-start' }" @click.prevent="scrollTo('quick-start')">快速开始</a></li>
            <li><a href="#crud-example" :class="{ active: activeSection === 'crud-example' }" @click.prevent="scrollTo('crud-example')">CRUD开发示例</a></li>
            <li><a href="#api-reference" :class="{ active: activeSection === 'api-reference' }" @click.prevent="scrollTo('api-reference')">API接口文档</a></li>
            <li><a href="#error-codes" :class="{ active: activeSection === 'error-codes' }" @click.prevent="scrollTo('error-codes')">错误码说明</a></li>
            <li><a href="#deployment" :class="{ active: activeSection === 'deployment' }" @click.prevent="scrollTo('deployment')">生产部署</a></li>
            <li><a href="#nginx-config" :class="{ active: activeSection === 'nginx-config' }" @click.prevent="scrollTo('nginx-config')">Nginx配置</a></li>
            <li><a href="#troubleshooting" :class="{ active: activeSection === 'troubleshooting' }" @click.prevent="scrollTo('troubleshooting')">问题排查</a></li>
          </ul>
        </div>
      </nav>

      <main class="doc-main">

        <!-- ========== 项目概览 ========== -->
        <section id="overview" class="doc-section">
          <h2>项目概览</h2>
          <p class="lead">飞鱼 Admin 是一套基于 <strong>Vue3 + Vite + Element Plus</strong> 前端架构，配合 <strong>ThinkPHP 8</strong> 后端框架的企业级后台管理解决方案。完全免费开源，采用 MIT 许可证，可商用。</p>
          <div class="info-cards">
            <div class="info-card"><div class="ci blue">🌐</div><div><h4>前后端分离</h4><p>标准 RESTful API，支持多端复用</p></div></div>
            <div class="info-card"><div class="ci green">⚡</div><div><h4>代码生成器</h4><p>一键生成 CRUD，效率提升 80%</p></div></div>
            <div class="info-card"><div class="ci purple">🔐</div><div><h4>RBAC 权限</h4><p>菜单+按钮级细粒度权限控制</p></div></div>
            <div class="info-card"><div class="ci orange">🏢</div><div><h4>多租户</h4><p>数据完全隔离，一套系统服务多客户</p></div></div>
          </div>
          <table class="meta-table">
            <tbody>
              <tr><td class="lbl">前端框架</td><td>Vue 3.5 + Vite 5 + Element Plus 2</td></tr>
              <tr><td class="lbl">后端框架</td><td>ThinkPHP 8 + PHP 8.0+</td></tr>
              <tr><td class="lbl">数据库</td><td>MySQL 5.7+ / 8.0</td></tr>
              <tr><td class="lbl">认证方式</td><td>JWT Bearer Token</td></tr>
              <tr><td class="lbl">开源协议</td><td>MIT License</td></tr>
            </tbody>
          </table>
        </section>

        <!-- ========== 技术栈 ========== -->
        <section id="tech-stack" class="doc-section">
          <h2>技术栈</h2>
          <h3>前端技术</h3>
          <div class="stack-grid">
            <div class="stack-item" v-for="s in feStack" :key="s.name"><div class="si" :style="{background: s.color}">{{ s.emoji }}</div><div><strong>{{ s.name }}</strong><span>{{ s.desc }}</span></div></div>
          </div>
          <h3>后端技术</h3>
          <div class="stack-grid">
            <div class="stack-item" v-for="s in beStack" :key="s.name"><div class="si" :style="{background: s.color}">{{ s.emoji }}</div><div><strong>{{ s.name }}</strong><span>{{ s.desc }}</span></div></div>
          </div>
        </section>

        <!-- ========== 功能特性 ========== -->
        <section id="features" class="doc-section">
          <h2>功能特性</h2>
          <div class="features-grid">
            <div class="feat" v-for="f in features" :key="f.title"><div class="feat-icon">{{ f.icon }}</div><h3>{{ f.title }}</h3><p>{{ f.desc }}</p></div>
          </div>
        </section>

        <!-- ========== 快速开始 ========== -->
        <section id="quick-start" class="doc-section">
          <h2>快速开始</h2>
          <h3>环境要求</h3>
          <div class="req-grid">
            <div class="req"><span class="ri">🐘</span><div><strong>PHP</strong><p>≥ 8.0，推荐 8.2</p></div></div>
            <div class="req"><span class="ri">🐬</span><div><strong>MySQL</strong><p>≥ 5.7.36 / 8.0</p></div></div>
            <div class="req"><span class="ri">📦</span><div><strong>Node.js</strong><p>≥ 16，推荐 20 LTS</p></div></div>
            <div class="req"><span class="ri">⚡</span><div><strong>pnpm</strong><p>≥ 7.0</p></div></div>
          </div>
          <h3>前端部署</h3>
          <pre><code># 安装依赖
pnpm install

# 开发模式
pnpm run dev

# PC端构建
pnpm run build
# 输出: ../backend/public/pc/</code></pre>
          <h3>后端部署</h3>
          <pre><code># 配置数据库
# 编辑 backend/config/database.php

# 访问安装向导
http://your-domain.com/install</code></pre>
        </section>

        <!-- ========== CRUD开发示例 ========== -->
        <section id="crud-example" class="doc-section">
          <h2>CRUD 开发示例</h2>
          <p class="lead">以"新闻管理"模块为例，演示从后端到前端的完整开发流程。</p>
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
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='新闻表';</code></pre>

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

    /** 列表 GET /api/news/list */
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

    /** 详情 GET /api/news/read?id=1 */
    public function read(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) return $this->error('参数错误');
        $news = NewsModel::with(['category'])->find($id);
        if (!$news) return $this->error('记录不存在');
        return $this->success($news);
    }

    /** 新增 POST /api/news/save */
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

    /** 更新 POST /api/news/update */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) return $this->error('参数错误');
        $news = NewsModel::find($id);
        if (!$news) return $this->error('记录不存在');
        $title = $this->param('title', '');
        if ($title !== '') $news->title = $title;
        $news->category_id = (int) $this->param('category_id', $news->category_id);
        if ($this->param('author', '')   !== '') $news->author  = $this->param('author');
        if ($this->param('status', '')   !== '') $news->status  = (int) $this->param('status');
        if ($this->param('sort', '')     !== '') $news->sort    = (int) $this->param('sort');
        $news->save();
        return $this->success([], '更新成功');
    }

    /** 删除 POST /api/news/delete */
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
          <pre><code># backend/route/api.php
use think\facade\Route;

Route::group('news', function () {
    Route::get('list',   'admin.News/list');
    Route::get('read',   'admin.News/read');
    Route::post('save',  'admin.News/save');
    Route::post('update','admin.News/update');
    Route::post('delete','admin.News/delete');
})->middleware('adminAuth');</code></pre>

          <h3>⑤ 前端 API 文件</h3>
          <pre><code>// frontend/src/api/news.js
import request from '@/utils/request'

export const getNewsList = (params) =>
  request({ url: '/news/list', method: 'get', params })

export const getNews = (id) =>
  request({ url: '/news/read', method: 'get', params: { id } })

export const createNews = (data) =>
  request({ url: '/news/save', method: 'post', data })

export const updateNews = (data) =>
  request({ url: '/news/update', method: 'post', data })

export const deleteNews = (id) =>
  request({ url: '/news/delete', method: 'post', data: { id } })</code></pre>

          <h3>⑥ 前端页面（Vue3）</h3>
          <pre><code>&lt;template&gt;
  &lt;div class="news-page"&gt;
    &lt;div class="search-bar"&gt;
      &lt;el-input v-model="query.keyword" placeholder="搜索标题/作者" clearable @keyup.enter="fetchList" /&gt;
      &lt;el-select v-model="query.status" placeholder="状态" clearable&gt;
        &lt;el-option label="启用" :value="1" /&gt;
        &lt;el-option label="禁用" :value="0" /&gt;
      &lt;/el-select&gt;
      &lt;el-button type="primary" @click="fetchList"&gt;查询&lt;/el-button&gt;
      &lt;el-button type="primary" @click="openDialog('create')"&gt;新增新闻&lt;/el-button&gt;
    &lt;/div&gt;

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
      &lt;el-table-column label="操作" width="200" fixed="right"&gt;
        &lt;template #default="{ row }"&gt;
          &lt;el-button type="primary" link @click="openDialog('edit', row)"&gt;编辑&lt;/el-button&gt;
          &lt;el-button type="danger" link @click="handleDelete(row.id)"&gt;删除&lt;/el-button&gt;
        &lt;/template&gt;
      &lt;/el-table-column&gt;
    &lt;/el-table&gt;

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
          <h2>API 接口文档</h2>

          <h3>统一响应格式</h3>
          <pre><code>{
  "code": 0,           // 0=成功，其他=失败（见错误码表）
  "msg": "success",   // 提示信息
  "total": 100,       // 分页时返回总数
  "data": {}           // 返回数据
}</code></pre>

          <h3>认证 Header</h3>
          <pre><code>Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json</code></pre>

          <h3>分页参数</h3>
          <div class="table-wrap">
            <table class="param-tbl">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>page</td><td>int</td><td>否</td><td>页码，默认 1</td></tr>
                <tr><td>limit</td><td>int</td><td>否</td><td>每页条数，默认 20，最大 100</td></tr>
              </tbody>
            </table>
          </div>

          <h3>登录认证 /login</h3>
          <div class="api-grp">
            <span class="method post">POST</span>
            <span class="path">/api/login — 用户登录</span>
          </div>
          <pre><code>// 请求
POST /api/login
{ "username": "admin", "password": "admin123" }

// 成功响应
{
  "code": 0,
  "msg": "登录成功",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 86400,
    "user_info": { "id": 1, "username": "admin", "nickname": "超级管理员" }
  }
}</code></pre>

          <h3>用户管理 /user</h3>
          <div class="api-grp">
            <span class="method get">GET</span>
            <span class="path">/api/user/list — 用户列表</span>
          </div>
          <div class="table-wrap">
            <table class="param-tbl">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>keyword</td><td>string</td><td>否</td><td>搜索：用户名/昵称/手机号</td></tr>
                <tr><td>dept_id</td><td>int</td><td>否</td><td>部门ID（包含子部门）</td></tr>
                <tr><td>status</td><td>int</td><td>否</td><td>状态：0=禁用，1=启用</td></tr>
                <tr><td>page</td><td>int</td><td>否</td><td>页码，默认1</td></tr>
                <tr><td>limit</td><td>int</td><td>否</td><td>每页条数，默认20</td></tr>
              </tbody>
            </table>
          </div>
          <pre><code>{
  "code": 0,
  "msg": "success",
  "total": 58,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "nickname": "超级管理员",
      "mobile": "13800138000",
      "dept": { "id": 1, "name": "技术部" },
      "post": { "id": 1, "name": "技术总监" },
      "roles": ["super_admin"],
      "status": 1,
      "create_time": "2024-01-01 08:00:00"
    }
  ]
}</code></pre>

          <div class="api-grp">
            <span class="method post">POST</span>
            <span class="path">/api/user/save — 新增用户</span>
          </div>
          <div class="table-wrap">
            <table class="param-tbl">
              <thead><tr><th>参数名</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
              <tbody>
                <tr><td>username</td><td>string</td><td>✅</td><td>用户名，4-20位</td></tr>
                <tr><td>password</td><td>string</td><td>✅</td><td>密码，6-20位</td></tr>
                <tr><td>nickname</td><td>string</td><td>✅</td><td>昵称</td></tr>
                <tr><td>role_ids</td><td>array</td><td>否</td><td>角色ID数组，如 [1, 2]</td></tr>
                <tr><td>mobile</td><td>string</td><td>否</td><td>手机号</td></tr>
                <tr><td>dept_id</td><td>int</td><td>否</td><td>部门ID</td></tr>
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
  "mobile": "13900139000"
}

// 成功响应
{ "code": 0, "msg": "新增成功", "data": { "id": 10 } }</code></pre>

          <div class="api-grp">
            <span class="method post">POST</span>
            <span class="path">/api/user/update — 更新用户</span>
          </div>
          <pre><code>// 请求示例（只需传需要更新的字段，id必传）
POST /api/user/update
{ "id": 10, "nickname": "张三（新）", "role_ids": [2], "status": 1 }</code></pre>

          <div class="api-grp">
            <span class="method post">POST</span>
            <span class="path">/api/user/delete — 删除用户</span>
          </div>
          <pre><code>// 请求
POST /api/user/delete
{ "id": 10 }

// 成功: { "code": 0, "msg": "删除成功", "data": [] }
// 失败: { "code": 400, "msg": "不能删除当前登录用户", "data": [] }</code></pre>

          <h3>角色管理 /role</h3>
          <div class="api-grp">
            <span class="method get">GET</span>
            <span class="path">/api/role/list — 角色列表</span>
          </div>
          <pre><code>{
  "code": 0,
  "msg": "success",
  "total": 5,
  "data": [
    { "id": 1, "name": "超级管理员", "code": "super_admin", "status": 1, "sort": 0 },
    { "id": 2, "name": "运营主管",   "code": "operation_manager", "status": 1, "sort": 1 }
  ]
}</code></pre>

          <div class="api-grp">
            <span class="method post">POST</span>
            <span class="path">/api/role/save — 新增角色</span>
          </div>
          <pre><code>POST /api/role/save
{
  "name": "内容编辑",
  "code": "content_editor",
  "status": 1,
  "sort": 10,
  "remark": "内容模块编辑权限"
}</code></pre>
        </section>

        <!-- ========== 错误码 ========== -->
        <section id="error-codes" class="doc-section">
          <h2>错误码说明</h2>
          <div class="table-wrap">
            <table class="param-tbl">
              <thead><tr><th>错误码</th><th>说明</th><th>处理建议</th></tr></thead>
              <tbody>
                <tr><td class="code">0</td><td>成功</td><td>—</td></tr>
                <tr><td class="code">400</td><td>业务逻辑错误</td><td>如"用户名已存在"，根据 msg 提示处理</td></tr>
                <tr><td class="code">401</td><td>未授权 / Token失效</td><td>跳转登录页，重新获取 Token</td></tr>
                <tr><td class="code">403</td><td>无权限访问</td><td>当前账号无此接口权限，联系管理员</td></tr>
                <tr><td class="code">404</td><td>接口不存在</td><td>检查请求路径是否正确</td></tr>
                <tr><td class="code">422</td><td>参数校验失败</td><td>检查必填参数是否完整</td></tr>
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

        <!-- ========== 生产部署 ========== -->
        <section id="deployment" class="doc-section">
          <h2>生产环境部署</h2>
          <h3>基础环境安装（CentOS）</h3>
          <pre><code># 安装 Nginx
yum install -y nginx
systemctl enable --now nginx

# 安装 PHP 8.2
dnf module enable php:8.2 -y
yum install -y php-fpm php-cli php-mysqlnd php-json php-mbstring php-xml php-curl php-gd php-bcmath php-pdo

# 安装 MySQL 8.0
yum install -y mysql-community-server
systemctl enable --now mysqld

# 安装 Redis
yum install -y redis
systemctl enable --now redis</code></pre>

          <h3>项目部署步骤</h3>
          <pre><code># 1. 部署后端代码到 /www/wwwroot/feiyuadmin/backend/
# 2. 前端 PC 构建到 /www/wwwroot/feiyuadmin/backend/public/pc/

# 3. 安装后端依赖
cd /www/wwwroot/feiyuadmin/backend
composer install --no-dev --optimize-autoloader

# 4. 配置 .env
cp .env.example .env
# 编辑 .env 配置数据库连接

# 5. 生成密钥
php think jwt:generate

# 6. 设置权限
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
pm.max_requests = 500  # 防止内存泄漏</code></pre>

          <h3>Supervisor 守护进程</h3>
          <pre><code># 安装 Supervisor
pip install supervisor

# 添加队列 worker 配置
cat >> /etc/supervisord.d/feiyuadmin.ini << 'EOF'
[program:feiyuadmin-queue]
command=php /www/wwwroot/feiyuadmin/backend/think queue:work --queue default --sleep 3 --tries 3
directory=/www/wwwroot/feiyuadmin/backend
autostart=true
autorestart=true
user=nginx
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log
EOF

supervisorctl reread
supervisorctl update
supervisorctl start feiyuadmin-queue</code></pre>
        </section>

        <!-- ========== Nginx配置 ========== -->
        <section id="nginx-config" class="doc-section">
          <h2>Nginx 配置</h2>
          <pre><code># /etc/nginx/conf.d/feiyuadmin.conf

server {
    listen 80;
    server_name feiyuadmin.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name feiyuadmin.example.com;

    ssl_certificate     /etc/nginx/ssl/example.com.pem;
    ssl_certificate_key /etc/nginx/ssl/example.com.key;
    ssl_protocols       TLSv1.2 TLSv1.3;

    root /www/wwwroot/feiyuadmin/backend/public;
    index index.php index.html;

    # Gzip压缩
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

    # 安全头
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    # ========== PC端演示站（静态） ==========
    location /pc/ {
        alias /www/wwwroot/feiyuadmin/backend/public/pc/;
        try_files $uri $uri/ /pc/index.html;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # ========== 后端 API ==========
    location /api/ {
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size 50m;
    }

    # ========== PHP-FPM ==========
    location ~ \.php(/|$) {
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        client_max_body_size 50m;
    }

    # ========== 禁止访问敏感文件 ==========
    location ~ /\.env { deny all; }
    location ~ /\.git  { deny all; }
    location ~ /runtime { deny all; }

    # ========== 静态资源缓存 ==========
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}</code></pre>

          <h3>常用命令</h3>
          <pre><code>nginx -t        # 测试配置
nginx -s reload  # 重载配置（不中断）
systemctl restart nginx  # 重启</code></pre>
        </section>

        <!-- ========== 问题排查 ========== -->
        <section id="troubleshooting" class="doc-section">
          <h2>常见问题排查</h2>
          <div class="qa-list">
            <div class="qa" v-for="item in qaList" :key="item.q">
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
tail -f /www/wwwroot/feiyuadmin/backend/runtime/log/$(date +%Y%m%d).log

# Supervisor 日志（队列）
tail -f /www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log</code></pre>

          <h3>性能调优参考值</h3>
          <pre><code># MySQL (my.cnf)
innodb_buffer_pool_size = 1G    # 物理内存的50-70%
max_connections = 500
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Redis (redis.conf)
appendonly yes
appendfsync everysec
maxmemory 512mb
maxmemory-policy allkeys-lru</code></pre>
        </section>

      </main>
    </div>

    <!-- AI 助手浮窗 -->
    <AiChatWindow />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AiChatWindow from '@/components/AiChatWindow.vue'

const activeSection = ref('overview')

const feStack = [
  { name: 'Vue 3.5', desc: '渐进式前端框架，组合式 API', emoji: '⚡', color: '#42b983' },
  { name: 'Vite 5', desc: '下一代前端构建工具，HMR 热更新', emoji: '🚀', color: '#646cff' },
  { name: 'Element Plus 2', desc: 'Vue3 UI 组件库，70+ 组件', emoji: '🎨', color: '#409eff' },
  { name: 'Pinia', desc: '新一代状态管理', emoji: '📦', color: '#f7df1e' },
  { name: 'Vue Router 4', desc: '官方路由，支持懒加载', emoji: '🛤️', color: '#4abf8a' },
  { name: 'Axios', desc: 'Promise HTTP 客户端，统一拦截', emoji: '🌐', color: '#5a29e4' },
]

const beStack = [
  { name: 'PHP 8.2', desc: '后端核心语言，JIT 编译', emoji: '🐘', color: '#777bb4' },
  { name: 'ThinkPHP 8', desc: '高性能 PHP 框架', emoji: '⚡', color: '#63b500' },
  { name: 'MySQL 8.0', desc: '关系型数据库，InnoDB 引擎', emoji: '🐬', color: '#00758f' },
  { name: 'Redis', desc: '缓存 + Session + 队列', emoji: '🔴', color: '#dc382d' },
  { name: 'JWT', desc: '无状态认证，令牌自动续期', emoji: '🔑', color: '#d63aff' },
  { name: 'RBAC', desc: '基于角色的访问控制', emoji: '🔐', color: '#f7c948' },
]

const features = [
  // V1.0 核心模块
  { icon: '👤', title: '用户管理', desc: '用户CRUD、状态管理、个人信息' },
  { icon: '🔐', title: '角色权限', desc: 'RBAC模型，菜单+按钮级权限控制' },
  { icon: '📋', title: '菜单管理', desc: '动态菜单配置，权限分配' },
  { icon: '🏢', title: '部门管理', desc: '组织架构树形管理' },
  { icon: '💼', title: '岗位管理', desc: '岗位序列管理' },
  { icon: '📝', title: '操作日志', desc: '操作行为记录，可追溯' },
  { icon: '🔑', title: '登录日志', desc: '登录记录，异常登录告警' },
  { icon: '⚙️', title: '参数配置', desc: '系统参数动态配置' },
  { icon: '📖', title: '数据字典', desc: '枚举类型统一管理' },
  { icon: '📁', title: '文件上传', desc: '本地上传、CDN、OSS' },
  // V2.0 扩展模块
  { icon: '⚡', title: '代码生成器', desc: '一键生成CRUD，可视化配置' },
  { icon: '🏢', title: '多租户', desc: '数据完全隔离，SaaS支持' },
  { icon: '🔄', title: '工作流引擎', desc: '可视化流程设计器' },
  { icon: '📊', title: '数据大屏', desc: '拖拽式大屏设计器' },
  { icon: '🔔', title: '消息通知', desc: '多渠道通知推送' },
  { icon: '⏰', title: '定时任务', desc: 'Crontab任务调度' },
  { icon: '📝', title: '表单设计器', desc: '拖拽式表单构建' },
  { icon: '💳', title: '支付渠道', desc: '微信/支付宝支付' },
  { icon: '📰', title: '富文本编辑器', desc: 'Markdown/WYSIWYG' },
  { icon: '💻', title: '代码编辑器', desc: 'Monaco Editor' },
]

const qaList = [
  { q: '页面空白，控制台 401 Unauthorized', a: 'Token 过期或未携带。检查 Header 是否正确添加 Authorization: Bearer {token}，或清除 localStorage 重新登录。' },
  { q: '后端接口返回 502 Bad Gateway', a: 'Nginx 无法连接 PHP-FPM。检查 PHP-FPM 是否运行（systemctl status php-fpm），端口是否一致（默认9000）。' },
  { q: '文件上传失败，提示"上传目录不可写"', a: '执行 chmod -R 777 /www/wwwroot/feiyuadmin/backend/public/uploads，并确保 owner 为 nginx 用户。' },
  { q: '登录后刷新页面跳转回登录页', a: '通常是 Token 校验失败。检查后端 jwt.secret 配置、前后端密钥是否一致，或 Redis 连接是否正常。' },
  { q: '定时任务没有执行', a: '确认 Supervisor 进程是否在运行（supervisorctl status），以及 think queue:work 命令路径是否正确。' },
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
      if (entry.isIntersecting) activeSection.value = entry.target.id
    })
  }, { threshold: 0.2 })
  document.querySelectorAll('.doc-section[id]').forEach(el => observer.observe(el))
})

onUnmounted(() => observer?.disconnect())
</script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif; background: #f5f7fa; color: #303133; }
</style>

<style scoped>
.doc-container { min-height: 100vh; background: #f5f7fa; }

/* Header */
.doc-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}
.header-inner {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  padding: 0 40px;
}
.back-home {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  text-decoration: none;
  font-size: 17px;
  font-weight: 600;
}
.header-nav {
  display: flex;
  gap: 28px;
}
.header-nav a {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}
.header-nav a:hover { color: #fff; }
.btn-primary {
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.3);
  padding: 7px 18px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s;
}
.btn-primary:hover {
  background: rgba(255,255,255,0.25);
  text-decoration: none;
}

/* Body layout */
.doc-body {
  display: flex;
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px;
  gap: 40px;
}

/* TOC */
.doc-toc {
  width: 200px;
  flex-shrink: 0;
  position: sticky;
  top: 104px;
  height: fit-content;
}
.toc-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.toc-title {
  font-size: 11px;
  font-weight: 600;
  color: #909399;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  padding-left: 4px;
}
.doc-toc ul { list-style: none; padding: 0; margin: 0; }
.doc-toc li { margin-bottom: 2px; }
.doc-toc a {
  display: block;
  padding: 7px 10px;
  color: #606266;
  text-decoration: none;
  font-size: 13px;
  border-radius: 6px;
  transition: all 0.15s;
}
.doc-toc a:hover { background: #e6f7ff; color: #2563EB; }
.doc-toc a.active { background: #2563EB; color: #fff; }

/* Main content */
.doc-main { flex: 1; min-width: 0; }

.doc-section {
  background: #fff;
  border-radius: 12px;
  padding: 40px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.doc-section h2 {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 3px solid #2563EB;
  display: inline-block;
}
.doc-section h3 {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
  margin: 24px 0 12px;
}
.doc-section h4 { font-size: 14px; font-weight: 600; margin: 14px 0 8px; }
.lead { font-size: 15px; color: #606266; line-height: 1.8; margin-bottom: 16px; }
p { color: #606266; line-height: 1.8; margin-bottom: 10px; }

/* Info cards */
.info-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin: 20px 0; }
.info-card { display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #eee; }
.ci { font-size: 24px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0; }
.blue { background: rgba(37,99,235,0.1); }
.green { background: rgba(82,196,26,0.1); }
.purple { background: rgba(139,92,246,0.1); }
.orange { background: rgba(249,115,22,0.1); }
.info-card h4 { font-size: 13px; font-weight: 600; color: #1a1a2e; margin: 0 0 4px; }
.info-card p { font-size: 12px; color: #909399; margin: 0; }

/* Meta table */
.meta-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 16px; }
.meta-table td { padding: 9px 14px; border-bottom: 1px solid #f0f0f0; }
.lbl { font-weight: 600; color: #303133; background: #fafafa; width: 120px; }

/* Stack grid */
.stack-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
.stack-item { display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #f0f0f0; }
.si { font-size: 20px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0; }
.stack-item strong { display: block; font-size: 12px; color: #303133; }
.stack-item span { display: block; font-size: 11px; color: #909399; }

/* Features grid */
.features-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.feat { padding: 18px; background: #f8fafc; border-radius: 8px; border: 1px solid #f0f0f0; }
.feat-icon { font-size: 22px; margin-bottom: 8px; }
.feat h3 { font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 4px; }
.feat p { font-size: 12px; color: #909399; margin: 0; line-height: 1.5; }

/* Req grid */
.req-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
.req { display: flex; align-items: center; gap: 10px; padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #f0f0f0; }
.ri { font-size: 22px; }
.req strong { display: block; font-size: 12px; color: #303133; }
.req p { font-size: 11px; color: #909399; margin: 0; }

/* Code blocks */
pre { background: #1a1a2e; color: #e6e6e6; padding: 16px 20px; border-radius: 8px; overflow-x: auto; font-size: 12.5px; line-height: 1.7; margin: 12px 0 20px; font-family: 'Monaco', 'Menlo', monospace; }

/* Step nav */
.step-nav { display: flex; gap: 4px; margin-bottom: 24px; flex-wrap: wrap; }
.step { padding: 5px 14px; background: #f0f0f0; border-radius: 20px; font-size: 12px; color: #909399; font-weight: 500; }
.step.active { background: #2563EB; color: #fff; }

/* API groups */
.api-grp { display: flex; align-items: center; gap: 10px; margin: 20px 0 8px; padding: 10px 14px; background: #f8fafc; border-radius: 6px; border: 1px solid #eee; }
.method { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; }
.get { background: #52c41a; color: #fff; }
.post { background: #2563EB; color: #fff; }
.put { background: #fa8c16; color: #fff; }
.delete { background: #f5222d; color: #fff; }
.path { font-size: 13px; color: #303133; font-weight: 500; font-family: 'Monaco', monospace; }

/* Tables */
.table-wrap { overflow-x: auto; margin: 12px 0; }
.param-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; border: 1px solid #f0f0f0; border-radius: 6px; overflow: hidden; }
.param-tbl thead tr { background: #fafafa; }
.param-tbl th { padding: 8px 12px; text-align: left; font-weight: 600; color: #303133; border-bottom: 1px solid #eee; white-space: nowrap; }
.param-tbl td { padding: 8px 12px; border-bottom: 1px solid #f5f5f5; color: #606266; }
.param-tbl tr:last-child td { border-bottom: none; }
.param-tbl td.code { font-family: 'Monaco', monospace; color: #d43961; font-weight: 600; }

/* Callout */
.callout { padding: 14px 16px; border-radius: 8px; margin: 16px 0; font-size: 13px; line-height: 1.7; }
.info { background: #e6f7ff; border: 1px solid #91d5ff; color: #003a8c; }

/* QA */
.qa-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.qa { background: #f8fafc; padding: 14px 18px; border-radius: 8px; border: 1px solid #f0f0f0; }
.qa h4 { font-size: 13px; color: #2563EB; margin-bottom: 6px; }
.qa p { font-size: 13px; color: #606266; margin: 0; line-height: 1.7; }

@media (max-width: 1100px) {
  .doc-toc { display: none; }
  .stack-grid { grid-template-columns: repeat(2, 1fr); }
  .features-grid { grid-template-columns: repeat(2, 1fr); }
  .req-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .doc-body { padding: 20px; gap: 0; }
  .header-inner { padding: 0 20px; }
  .header-nav { display: none; }
  .doc-section { padding: 24px; }
  .stack-grid { grid-template-columns: 1fr; }
  .features-grid { grid-template-columns: 1fr; }
  .info-cards { grid-template-columns: 1fr; }
  .req-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

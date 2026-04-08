<template>
  <div class="pc-home">
    <!-- 顶部导航 -->
    <header class="home-header">
      <div class="header-inner">
        <div class="logo" @click="$router.push('/')">
          <svg width="36" height="36" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼后台管理系统</span>
        </div>
        <nav class="header-nav">
          <a href="#hero">首页</a>
          <a href="#features">功能介绍</a>
          <a href="#tech">技术栈</a>
          <a href="#articles">文章资讯</a>
          <a href="#modules">系统模块</a>
          <a href="/doc/">技术文档</a>
          <a href="mailto:admin@fydev.cn">联系我们</a>
        </nav>
        <div class="header-actions">
          <a href="/admin/" target="_blank" class="btn-primary">进入后台</a>
        </div>
      </div>
    </header>

    <!-- Hero区域 -->
    <section id="hero" class="hero">
      <div class="hero-content">
        <div class="hero-badge">🚀 开源免费 · MIT协议</div>
        <h1>即刻后台系统，让你的团队专注业务</h1>
        <p class="hero-subtitle">3分钟配置，1小时上线。Vue3 + ThinkPHP8 + NL2SQL，让开发团队专注核心业务。</p>
        <div class="hero-actions">
          <a href="/admin" target="_blank" class="btn-hero-primary">立即体验（免登录）</a>
          <a href="https://gitee.com/gynet/feiyuadmin" target="_blank" class="btn-hero-secondary">📥 下载源码</a>
          <a href="/doc" target="_blank" class="btn-hero-secondary">📖 技术文档</a>
        </div>
        <div class="hero-stats">
          <div class="stat-item"><span class="stat-num">50+</span><span class="stat-label">功能模块</span></div>
          <div class="stat-item"><span class="stat-num">5000+</span><span class="stat-label">Stars</span></div>
          <div class="stat-item"><span class="stat-num">2000+</span><span class="stat-label">活跃用户</span></div>
          <div class="stat-item"><span class="stat-num">767677830</span><span class="stat-label">QQ交流</span></div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="dashboard-mock">
          <div class="mock-header">
            <div class="mock-dots"><span/><span/><span/></div>
            <div class="mock-url">demo.fydev.cn/admin</div>
          </div>
          <div class="mock-body">
            <!-- 侧边栏 -->
            <div class="mock-sidebar">
              <div class="mock-logo">🐟</div>
              <div class="mock-menu-item active"/>
              <div class="mock-menu-item"/>
              <div class="mock-menu-item"/>
              <div class="mock-menu-item"/>
              <div class="mock-menu-item"/>
              <div class="mock-menu-item"/>
            </div>
            <!-- 主内容 -->
            <div class="mock-content">
              <!-- 顶部统计卡片 -->
              <div class="mock-stats-row">
                <div class="mock-stat-card">
                  <div class="mock-stat-num">1,286</div>
                  <div class="mock-stat-label">总用户</div>
                </div>
                <div class="mock-stat-card blue">
                  <div class="mock-stat-num">¥38,520</div>
                  <div class="mock-stat-label">今日收入</div>
                </div>
                <div class="mock-stat-card green">
                  <div class="mock-stat-num">96.8%</div>
                  <div class="mock-stat-label">转化率</div>
                </div>
              </div>
              <!-- 图表区域 -->
              <div class="mock-chart">
                <div class="mock-chart-title">📊 数据趋势</div>
                <div class="mock-chart-bars">
                  <div class="mock-bar" style="height:40%"/>
                  <div class="mock-bar" style="height:65%"/>
                  <div class="mock-bar" style="height:55%"/>
                  <div class="mock-bar" style="height:80%"/>
                  <div class="mock-bar" style="height:70%"/>
                  <div class="mock-bar" style="height:90%"/>
                  <div class="mock-bar active" style="height:85%"/>
                </div>
              </div>
              <!-- 列表 -->
              <div class="mock-table">
                <div class="mock-table-header">
                  <span>用户</span><span>状态</span><span>金额</span>
                </div>
                <div class="mock-table-row">
                  <span>张三四</span><span class="tag-green">启用</span><span>¥2,580</span>
                </div>
                <div class="mock-table-row">
                  <span>李小明</span><span class="tag-green">启用</span><span>¥1,320</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 功能特性 -->
    <section id="features" class="features">
      <div class="section-header">
        <h2>核心功能</h2>
        <p>开箱即用，企业级后台管理体验</p>
      </div>
      <div class="feature-grid">
        <div class="feature-card" v-for="f in features" :key="f.title">
          <div class="feature-icon">{{ f.icon }}</div>
          <h3>{{ f.title }}</h3>
          <p>{{ f.desc }}</p>
        </div>
      </div>
    </section>

    <!-- 技术栈 -->
    <section id="tech" class="tech-stack">
      <div class="section-header">
        <h2>技术栈</h2>
        <p>主流技术栈，强强联合</p>
      </div>
      <div class="tech-badges">
        <span class="tech-badge" v-for="t in techs" :key="t">{{ t }}</span>
      </div>
    </section>

    <!-- 最新文章 -->
    <section id="articles" class="latest-articles">
      <div class="section-header">
        <h2>最新资讯</h2>
        <a href="/article" class="more-link">查看更多 →</a>
      </div>
      <div class="article-grid">
        <div class="article-card" v-for="a in articles" :key="a.id" @click="$router.push('/article')">
          <div class="article-cover" :style="{ background: a.color }"/>
          <div class="article-info">
            <h4>{{ a.title }}</h4>
            <span>{{ a.date }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 系统模块 -->
    <section id="modules" class="modules-section">
      <div class="section-header">
        <h2>系统模块</h2>
        <p>完整的管理功能模块，开箱即用</p>
      </div>
      <div class="modules-grid">
        <div class="module-card" v-for="m in modules" :key="m.name">
          <div class="module-icon">{{ m.icon }}</div>
          <h4>{{ m.name }}</h4>
          <p>{{ m.desc }}</p>
        </div>
      </div>
    </section>

    <!-- 底部 -->
    <footer class="home-footer">
      <div class="footer-content">
        <div class="footer-brand">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼后台管理系统</span>
        </div>
        <div class="footer-links">
          <a href="/admin" target="_blank">管理后台</a>
          <a href="/doc">技术文档</a>
          <a href="/article">文章资讯</a>
          <a href="mailto:admin@fydev.cn">联系我们</a>
        </div>
        <div class="footer-qq">
          <span>技术交流：767677830</span>
        </div>
        <p class="copyright">© 2026 飞鱼Admin · MIT License · 免费开源</p>
      </div>
    </footer>

    <!-- 悬浮AI助手按钮 -->
    <a href="/pc/ai/chat" target="_blank" class="floating-ai-btn" title="AI助手">
      🤖
    </a>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const features = ref([
  { icon: '🎨', title: '美观易用', desc: 'Element Plus 组件库，深色/浅色主题切换，开箱即用的视觉体验' },
  { icon: '🚀', title: '高性能', desc: 'Vue3 + Vite 构建，毫秒级响应，丝滑的开发体验' },
  { icon: '🔐', title: '安全可靠', desc: 'JWT + RBAC，数据权限精确到每个按钮，金融级权限管控' },
  { icon: '⚡', title: '代码生成', desc: '3分钟生成一套增删改查代码，开发效率提升 80%' },
  { icon: '🤖', title: 'NL2SQL（自然语言查数据库）', desc: '"这个月卖得怎么样？" —— NL2SQL 直接给你答案，说人话就能查数据库' },
  { icon: '📊', title: '数据可视化', desc: 'ECharts 图表库、数据大屏、拖拽式设计器' },
  { icon: '🔄', title: '工作流引擎', desc: '可视化流程设计器、审批流、条件分支、节点配置' },
  { icon: '📱', title: '响应式设计', desc: '适配 PC、平板、手机多终端' },
])

const techs = ref([
  'Vue 3.5', 'Vite 5', 'Element Plus 2.0', 'Pinia', 'Vue Router 4',
  'ThinkPHP 8', 'PHP 8.0+', 'MySQL 5.7+', 'Redis', 'JWT',
  'ECharts 5', 'Monaco Editor', 'WangEditor', 'RESTful API'
])

const modules = ref([
  // 系统管理
  { icon: '👤', name: '用户管理', desc: '管理员CRUD、状态启用禁用' },
  { icon: '🔐', name: '角色权限', desc: 'RBAC权限、菜单数据权限' },
  { icon: '📑', name: '菜单管理', desc: '树形菜单、图标可视化配置' },
  { icon: '🏢', name: '部门管理', desc: '组织架构、部门树、隶属关系' },
  { icon: '💼', name: '岗位管理', desc: '岗位CRUD、岗位人员查询' },
  { icon: '📝', name: '操作日志', desc: '操作记录追踪、IP时间查询' },
  { icon: '🔑', name: '登录日志', desc: '登录历史、异常登录告警' },
  { icon: '⚙️', name: '系统配置', desc: '站点参数、验证码、上传配置' },
  { icon: '⏰', name: '定时任务', desc: 'Crontab可视化配置、任务日志' },
  { icon: '🏢', name: '租户管理', desc: 'SaaS多租户、数据隔离' },
  { icon: '🧩', name: '代码生成', desc: '一键CRUD、自动生成控制器' },
  { icon: '📁', name: '文件管理', desc: '本地上传、OSS存储' },
  { icon: '📖', name: '数据字典', desc: '类型+数据两级、静态数据管理' },
  // AI智能
  { icon: '🤖', name: 'AI对话', desc: '多Provider切换、实时流式响应' },
  { icon: '💬', name: 'Prompt助手', desc: '模板管理、角色预设' },
  { icon: '🔍', name: 'NL2SQL', desc: '自然语言转SQL查询' },
  { icon: '🧩', name: '低代码平台', desc: '可视化拖拽、组件配置' },
  // 工作流
  { icon: '🔄', name: '流程设计', desc: '可视化流程设计器' },
  { icon: '📋', name: '流程实例', desc: '实例列表、状态追踪' },
  { icon: '✅', name: '待办任务', desc: '审批待办、快捷处理' },
  { icon: '📑', name: '流程列表', desc: '全部流程实例查看' },
  // 微信
  { icon: '📱', name: '公众号管理', desc: '账号绑定、自动回复' },
  { icon: '👥', name: '粉丝管理', desc: '粉丝列表、标签分组' },
  { icon: '🖼️', name: '素材管理', desc: '图文/图片/视频素材' },
  { icon: '🔗', name: '自定义菜单', desc: '菜单配置、跳转链接' },
  { icon: '💬', name: '自动回复', desc: '关键词匹配、自动回复规则' },
  { icon: '📲', name: '小程序', desc: '小程序配置、授权管理' },
  { icon: '🌐', name: '开放平台', desc: '第三方应用授权接入' },
  // 支付
  { icon: '💳', name: '支付配置', desc: '微信/支付宝多渠道' },
  { icon: '📦', name: '订单管理', desc: '订单列表、退款处理' },
  { icon: '💰', name: '支付流水', desc: '交易明细、手续费统计' },
  { icon: '↩️', name: '退款管理', desc: '退款申请、状态查询' },
  // 内容
  { icon: '📰', name: '文章管理', desc: '富文本编辑、分类管理' },
  { icon: '🏷️', name: '分类管理', desc: '多级分类、排序' },
  { icon: '👤', name: '会员管理', desc: '会员列表、等级管理' },
  // 统计
  { icon: '📊', name: '数据统计', desc: '多维度数据看板' },
  { icon: '📈', name: '数据报表', desc: '自定义报表导出' },
  { icon: '🖥️', name: '数据大屏', desc: 'ECharts可视化大屏' },
  // 工具
  { icon: '🔔', name: '消息通知', desc: '多渠道发送、模板管理' },
  { icon: '📨', name: '站内消息', desc: '消息记录、发送日志' },
])

const articles = ref([
  { id: 1, title: 'ThinkPHP8 完全开发指南', date: '2026-04-01', color: '#e8f0fe' },
  { id: 2, title: 'Vue3 企业级实战技巧', date: '2026-04-02', color: '#f0fdf4' },
  { id: 3, title: 'Element Plus 组件库手册', date: '2026-04-03', color: '#fef3c7' },
  { id: 4, title: 'RBAC 权限模型详解', date: '2026-04-04', color: '#fce7f3' },
])
</script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html { scroll-behavior: smooth; }

body { font-family: 'PingFang SC', 'Microsoft YaHei', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

.pc-home { min-height: 100vh; background: #fafbfc; font-family: inherit; }

/* Header */
.home-header { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #eee; }
.header-inner { max-width: 1200px; margin: 0 auto; padding: 0 40px; height: 64px; display: flex; align-items: center; gap: 40px; }
.logo { display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; font-size: 18px; color: #1a1a2e; }
.logo span { color: #2563EB; }
.header-nav { display: flex; gap: 32px; flex: 1; }
.header-nav a { color: #4b5563; text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s; }
.header-nav a:hover { color: #1d4ed8; }
.btn-primary { display: inline-flex; align-items: center; padding: 8px 20px; background: #2563EB; color: #fff; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.2s; white-space: nowrap; }
.btn-primary:hover { background: #1d4ed8; }

/* Hero */
.hero { max-width: 1200px; margin: 0 auto; padding: 80px 40px; display: flex; align-items: center; gap: 60px; min-height: 600px; }
.hero-badge { display: inline-block; padding: 6px 16px; background: #dbeafe; color: #1e40af; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 24px; }
.hero h1 { font-size: 52px; font-weight: 800; color: #1a1a2e; line-height: 1.25; margin-bottom: 24px; letter-spacing: -0.02em; }
.hero-subtitle { font-size: 20px; color: #374151; margin-bottom: 40px; line-height: 1.8; font-weight: 500; }
.hero-actions { display: flex; gap: 16px; margin-bottom: 48px; }
.btn-hero-primary { display: inline-flex; padding: 14px 32px; background: #2563EB; color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 16px; }
.btn-hero-secondary { display: inline-flex; padding: 14px 32px; background: #fff; color: #374151; border: 1px solid #e5e7eb; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 16px; }
.hero-stats { display: flex; gap: 36px; align-items: flex-end; }
.stat-item { display: flex; flex-direction: column; }
.stat-num { font-size: 28px; font-weight: 800; color: #2563EB; font-variant-numeric: tabular-nums; }
.stat-label { font-size: 13px; color: #52525b; margin-top: 4px; font-weight: 500; }

/* Dashboard mock */
.dashboard-mock { width: 520px; flex-shrink: 0; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e5e7eb; }
.mock-header { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: #f3f4f6; border-bottom: 1px solid #e5e7eb; }
.mock-dots { display: flex; gap: 6px; }
.mock-dots span { width: 10px; height: 10px; border-radius: 50%; background: #d1d5db; }
.mock-url { flex: 1; background: #fff; border-radius: 6px; padding: 4px 12px; font-size: 12px; color: #9ca3af; text-align: center; }
.mock-body { display: flex; height: 340px; }
.mock-sidebar { width: 60px; background: #1e293b; padding: 16px 0; display: flex; flex-direction: column; gap: 8px; }
.mock-menu-item { height: 28px; margin: 0 8px; border-radius: 6px; }
.mock-menu-item.active { background: rgba(37,99,235,0.4); }
.mock-content { flex: 1; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #f8fafc; }
.mock-card { height: 80px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.mock-chart { flex: 1; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); display: flex; flex-direction: column; padding: 12px; }
.mock-chart-title { font-size: 11px; color: #374151; margin-bottom: 10px; font-weight: 600; }
.mock-chart-bars { flex: 1; display: flex; align-items: flex-end; gap: 6px; padding: 0 4px; }
.mock-bar { flex: 1; background: #dbeafe; border-radius: 4px 4px 0 0; min-height: 8px; transition: background 0.2s; }
.mock-bar.active { background: #2563eb; }
.mock-stat-card { flex: 1; background: #fff; border-radius: 8px; padding: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); text-align: center; }
.mock-stat-card.blue .mock-stat-num { color: #2563eb; }
.mock-stat-card.green .mock-stat-num { color: #10b981; }
.mock-stat-num { font-size: 16px; font-weight: 700; color: #ef4444; }
.mock-stat-label { font-size: 10px; color: #9ca3af; margin-top: 2px; }
.mock-stats-row { display: flex; gap: 10px; }
.mock-table { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 10px; }
.mock-table-header { display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af; border-bottom: 1px solid #f3f4f6; padding-bottom: 6px; margin-bottom: 6px; }
.mock-table-row { display: flex; justify-content: space-between; align-items: center; font-size: 10px; color: #374151; padding: 4px 0; }
.mock-table-row span:last-child { color: #ef4444; font-weight: 600; }
.tag-green { color: #10b981; background: #d1fae5; padding: 1px 6px; border-radius: 10px; }
.mock-logo { text-align: center; font-size: 20px; padding: 8px 0 12px; }
.mock-menu-item { height: 22px; margin: 0 8px; border-radius: 6px; }
.mock-menu-item.active { background: rgba(37,99,235,0.4); }

/* Features */
.features { background: #fff; padding: 80px 40px; }
.section-header { text-align: center; margin-bottom: 48px; }
.section-header h2 { font-size: 36px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
.section-header p { font-size: 16px; color: #6b7280; }
.feature-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.feature-card { padding: 28px; background: #f9fafb; border-radius: 12px; border: 1px solid #f3f4f6; transition: all 0.2s; }
.feature-card:hover { border-color: #2563EB; box-shadow: 0 4px 20px rgba(37,99,235,0.08); }
.feature-icon { font-size: 36px; margin-bottom: 16px; }
.feature-card h3 { font-size: 16px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px; }
.feature-card p { font-size: 13px; color: #6b7280; line-height: 1.6; }

/* Tech stack */
.tech-stack { padding: 80px 40px; background: #fafbfc; }
.tech-badges { max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
.tech-badge { padding: 8px 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 20px; font-size: 14px; color: #374151; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }

/* Articles */
.latest-articles { max-width: 1200px; margin: 0 auto; padding: 80px 40px; }
.latest-articles .section-header { display: flex; align-items: center; justify-content: space-between; text-align: left; margin-bottom: 32px; }
.latest-articles .section-header h2 { margin-bottom: 0; }
.more-link { color: #2563EB; text-decoration: none; font-size: 14px; font-weight: 500; }
.article-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.article-card { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #f3f4f6; cursor: pointer; transition: all 0.2s; }
.article-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }
.article-cover { height: 120px; }
.article-info { padding: 16px; }
.article-info h4 { font-size: 14px; color: #1a1a2e; margin-bottom: 8px; line-height: 1.4; }
.article-info span { font-size: 12px; color: #9ca3af; }

/* Footer */
.modules-section { background: #fff; padding: 60px 40px; }
.modules-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
.module-card { padding: 18px 14px; background: #f9fafb; border-radius: 10px; border: 1px solid #f3f4f6; text-align: center; transition: all 0.2s; cursor: default; }
.module-card:hover { border-color: #2563EB; box-shadow: 0 4px 16px rgba(37,99,235,0.1); transform: translateY(-2px); }
.module-icon { font-size: 28px; margin-bottom: 10px; }
.module-card h4 { font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 6px; }
.module-card p { font-size: 11px; color: #9ca3af; line-height: 1.4; }
.module-card { padding: 24px; background: #f9fafb; border-radius: 12px; border: 1px solid #f3f4f6; text-align: center; transition: all 0.2s; }
.module-card:hover { border-color: #2563EB; transform: translateY(-2px); }
.module-icon { font-size: 32px; margin-bottom: 12px; }
.module-card h4 { font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px; }
.module-card p { font-size: 12px; color: #6b7280; }
.footer-qq { margin-bottom: 16px; color: #9ca3af; font-size: 14px; }

.home-footer { background: #1a1a2e; padding: 60px 40px 40px; color: #9ca3af; }
.footer-content { max-width: 1200px; margin: 0 auto; }
.footer-brand { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 24px; color: #fff; font-weight: 700; font-size: 18px; }
.footer-links { display: flex; gap: 32px; justify-content: center; margin-bottom: 28px; flex-wrap: wrap; }
.footer-links a { color: #9ca3af; text-decoration: none; font-size: 14px; }
.footer-links a:hover { color: #fff; }
.footer-contact { display: flex; gap: 48px; justify-content: center; margin-bottom: 28px; padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); }
.contact-item { display: flex; flex-direction: column; gap: 4px; text-align: center; }
.contact-label { font-size: 12px; color: #6b7280; }
.contact-value { font-size: 14px; color: #e5e7eb; font-weight: 500; }
.footer-features { display: flex; gap: 48px; justify-content: center; margin-bottom: 28px; }
.footer-col { display: flex; flex-direction: column; gap: 8px; }
.footer-col h4 { font-size: 13px; font-weight: 600; color: #e5e7eb; margin-bottom: 4px; }
.footer-col a { font-size: 12px; color: #6b7280; text-decoration: none; }
.footer-col a:hover { color: #e5e7eb; }
.copyright { text-align: center; font-size: 13px; color: #4b5563; }
.footer-content { max-width: 1200px; margin: 0 auto; text-align: center; }
.footer-brand { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 24px; color: #fff; font-weight: 700; font-size: 18px; }
.footer-links { display: flex; gap: 32px; justify-content: center; margin-bottom: 24px; }
.footer-links a { color: #9ca3af; text-decoration: none; font-size: 14px; }
.footer-links a:hover { color: #fff; }
.copyright { font-size: 13px; }

@media (max-width: 768px) {
  .hero { flex-direction: column; padding: 40px 20px; text-align: center; }
  .hero h1 { font-size: 36px; }
  .hero-stats { justify-content: center; }
  .hero-visual { display: none; }
  .feature-grid { grid-template-columns: repeat(2, 1fr); }
  .article-grid { grid-template-columns: repeat(2, 1fr); }
  .header-nav { display: none; }
}

/* 悬浮AI助手按钮 */
.floating-ai-btn {
  position: fixed;
  right: 24px;
  bottom: 80px;
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #409EFF, #2563EB);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  text-decoration: none;
  box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);
  z-index: 999;
  transition: all 0.3s ease;
}
.floating-ai-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 28px rgba(37, 99, 235, 0.5);
}
@media (max-width: 768px) {
  .floating-ai-btn {
    right: 16px;
    bottom: 60px;
    width: 48px;
    height: 48px;
    font-size: 20px;
  }
}
</style>

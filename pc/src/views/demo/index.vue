<template>
  <div class="demo-container">
    <!-- 导航栏 -->
    <header class="demo-header">
      <div class="header-content">
        <div class="logo">
          <img :src="fishLogo" alt="飞鱼" class="logo-img" />
          <span class="logo-text">飞鱼 Admin</span>
          <span class="opensource-badge">
            <el-icon><Link /></el-icon>
            免费开源
          </span>
        </div>
        <nav class="nav-links">
          <a href="#features">功能特性</a>
          <a href="#tech">技术栈</a>
          <a href="#screenshots">系统模块</a>
          <a href="/#/doc" target="_blank">技术文档</a>
          <a href="#contact">联系我们</a>
        </nav>
        <div class="header-actions">
          <el-button type="primary" @click="goLogin">演示地址</el-button>
        </div>
      </div>
    </header>

    <!-- Hero 区域 -->
    <section class="hero-section">
      <div class="hero-bg">
        <div class="bg-grid"></div>
        <div class="bg-glow"></div>
      </div>
      <div class="hero-content">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          v2.0 全新发布
        </div>
        <h1 class="hero-title">新一代企业级<br/>Vue3 后台管理系统</h1>
        <p class="hero-desc">
          基于 Vue3 + Element Plus + Vite 构建的高效、稳定、安全的后台管理解决方案。
          支持多租户、代码生成器、工作流等企业级功能。
        </p>
        <div class="hero-actions">
          <el-button type="primary" size="large" style="min-width: 160px;" @click="goLogin">
            <el-icon><ArrowRight /></el-icon>
            演示地址
          </el-button>
          <el-button type="success" size="large" style="min-width: 160px;" @click="goGitee">
            <el-icon><Link /></el-icon>
            下载源码
          </el-button>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number">1000+</span>
            <span class="stat-label">企业用户</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-number">50+</span>
            <span class="stat-label">功能模块</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-number">99.9%</span>
            <span class="stat-label">运行稳定性</span>
          </div>
        </div>
      </div>
      <div class="hero-preview">
        <div class="preview-window">
          <div class="window-header">
            <div class="window-dots">
              <span class="dot red"></span>
              <span class="dot yellow"></span>
              <span class="dot green"></span>
            </div>
            <div class="window-title">飞鱼后台管理系统</div>
          </div>
          <div class="window-content">
            <div class="preview-sidebar">
              <div class="sidebar-logo">
                <img :src="fishLogo" alt="飞鱼" class="sidebar-mini-logo" />
                <span>飞鱼后台</span>
              </div>
              <div class="sidebar-menu">
                <div class="menu-item active">
                  <div class="menu-icon"></div>
                  <span>工作台</span>
                </div>
                <div class="menu-item">
                  <div class="menu-icon"></div>
                  <span>系统管理</span>
                </div>
                <div class="menu-item">
                  <div class="menu-icon"></div>
                  <span>用户管理</span>
                </div>
                <div class="menu-item">
                  <div class="menu-icon"></div>
                  <span>角色管理</span>
                </div>
              </div>
            </div>
            <div class="preview-main">
              <div class="preview-header">
                <div class="preview-tab active">工作台</div>
                <div class="preview-tab">菜单管理</div>
              </div>
              <div class="preview-content">
                <div class="preview-card">
                  <div class="card-header-bar"></div>
                  <div class="card-lines">
                    <div class="line short"></div>
                    <div class="line long"></div>
                    <div class="line medium"></div>
                  </div>
                </div>
                <div class="preview-table">
                  <div class="table-header">
                    <div class="th">菜单名称</div>
                    <div class="th">路由</div>
                    <div class="th">操作</div>
                  </div>
                  <div class="table-row">
                    <div class="td">工作台</div>
                    <div class="td">/dashboard</div>
                    <div class="td">
                      <span class="action-btn">新增</span>
                      <span class="action-btn">编辑</span>
                      <span class="action-btn danger">删除</span>
                    </div>
                  </div>
                  <div class="table-row">
                    <div class="td">用户管理</div>
                    <div class="td">/system/user</div>
                    <div class="td">
                      <span class="action-btn">新增</span>
                      <span class="action-btn">编辑</span>
                      <span class="action-btn danger">删除</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 功能特性 -->
    <section id="features" class="features-section">
      <div class="section-header">
        <h2>为什么选择飞鱼 Admin</h2>
        <p>专为企业级应用打造的全方位解决方案</p>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon blue">
            <el-icon><Lightning /></el-icon>
          </div>
          <h3>性能卓越</h3>
          <p>基于 Vue3 + Vite 构建，毫秒级响应，支持百万数据秒级渲染</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon green">
            <el-icon><Lock /></el-icon>
          </div>
          <h3>安全可靠</h3>
          <p>完整权限体系，JWT 令牌认证，细粒度 RBAC 权限控制</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon purple">
            <el-icon><Grid /></el-icon>
          </div>
          <h3>模块化设计</h3>
          <p>前后端分离架构，标准化 API 接口，易于二次开发</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon orange">
            <el-icon><Cpu /></el-icon>
          </div>
          <h3>代码生成器</h3>
          <p>一键生成 CRUD 代码，支持自定义模板，开发效率翻倍</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon teal">
            <el-icon><Connection /></el-icon>
          </div>
          <h3>多终端支持</h3>
          <p>响应式设计，适配 PC、平板、手机等多种设备</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon red">
            <el-icon><DataLine /></el-icon>
          </div>
          <h3>数据可视化</h3>
          <p>内置 ECharts 图表库，支持拖拽式大屏设计</p>
        </div>
      </div>
    </section>

    <!-- 技术栈 -->
    <section id="tech" class="tech-section">
      <div class="section-header">
        <h2>技术栈</h2>
        <p>主流技术栈，强强联合</p>
      </div>
      <div class="tech-grid">
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><path fill="#35495E" d="M86.3 3.7l-64 112a7.97 7.97 0 0 0 4.5 11.3l56 16 20 56a7.98 7.98 0 0 0 14.3-4.5l14.6-51.2 14.6 51.2a7.98 7.98 0 1 0 14.3-4.5l20-56 56-16a7.97 7.97 0 0 0 4.5-11.3l-64-112a7.98 7.98 0 0 0-13.7 0z"/><path fill="#42b883" d="M64 0l-64 112 56 16 20 56 20-56 56-16z"/></svg>
          </div>
          <span class="tech-name">Vue 3</span>
          <span class="tech-desc">渐进式前端框架</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><path fill="#646CFF" d="M60 4L10 84h40l10-20 10 20h48L60 4zM10 94l50 30 50-30H80l-20 12-20-12H10z"/></svg>
          </div>
          <span class="tech-name">Vite</span>
          <span class="tech-desc">下一代前端构建工具</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><circle fill="#409EFF" cx="64" cy="64" r="64"/><path fill="#fff" d="M64 28a36 36 0 1 0 36 36 36 36 0 0 0-36-36zm0 8a28 28 0 1 1 0 56 28 28 0 0 1 0-56z"/><path fill="#fff" d="M64 48v32l20-16z"/></svg>
          </div>
          <span class="tech-name">Element Plus</span>
          <span class="tech-desc">Vue3 UI 组件库</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><path fill="#FFD859" d="M88.2 27.7L64 12 39.8 27.7l-16 40.3h80.4z"/><path fill="#FFB347" d="M24.2 72l-16-4v28l16 4V72z"/><path fill="#FF8C00" d="M8.2 100l16 4-16 4v-8z"/><path fill="#FFB347" d="M103.8 100l16-4-16-4v8z"/><path fill="#FFD859" d="M64 72l16 40.3L64 92.6l-16 19.7 16-40.3z"/><path fill="#FF8C00" d="M64 72l16 40.3 16-40.3H64z"/></svg>
          </div>
          <span class="tech-name">Pinia</span>
          <span class="tech-desc">新一代状态管理</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><ellipse fill="#777BB4" cx="64" cy="64" rx="56" ry="32"/><path fill="#fff" d="M88.5 58.2c-1.9-3.8-5.3-5.7-9.3-5.7-4.5 0-8.2 2.2-10.3 5.7-1.4 2.3-2 5-1.6 7.7.3 2.7 1.4 5.2 3.2 7.2 1.2 1.3 2.6 2.3 4.2 2.9 1.6.6 3.3 1 5.1 1 1.7 0 3.4-.4 4.9-1.2 1.5-.8 2.7-1.9 3.6-3.3l-3.2-1.8c-.6.9-1.5 1.7-2.5 2.2-1.1.5-2.3.8-3.5.8-1.6 0-3.1-.3-4.5-1-1.4-.7-2.5-1.7-3.4-3-1.5-2.4-2.2-5.2-1.9-8.1.3-2.9 1.4-5.6 3.1-7.8 1.7-2.2 4.1-3.9 6.9-4.8 2.8-.9 5.8-1 8.6-.3 2.8.7 5.3 2.2 7.2 4.4l3-2.5c-2.3-2.7-5.4-4.7-9-5.5-3.6-.8-7.3-.6-10.7.5z"/></svg>
          </div>
          <span class="tech-name">PHP 8.2</span>
          <span class="tech-desc">后端核心语言</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon">
            <svg viewBox="0 0 128 128"><rect fill="#64A838" width="128" height="128" rx="16"/><path fill="#fff" d="M32 40l16 8-16 8h16l16-8-16-8H32zm32 0l16 8-16 8h16l16-8-16-8H64zm32 0l16 8-16 8h16l16-8-16-8H96zM32 64l16 8-16 8h16l16-8-16-8H32zm32 0l16 8-16 8h16l16-8-16-8H64zm32 0l16 8-16 8h16l16-8-16-8H96zM32 88l16 8-16 8h16l16-8-16-8H32zm32 0l16 8-16 8h16l16-8-16-8H64zm32 0l16 8-16 8h16l16-8-16-8H96z"/></svg>
          </div>
          <span class="tech-name">ThinkPHP 8</span>
          <span class="tech-desc">高性能 PHP 框架</span>
        </div>
      </div>
    </section>

    <!-- 后台模块展示 -->
    <section id="screenshots" class="screenshots-section">
      <div class="section-header">
        <h2>后台功能模块</h2>
        <p>完整的后台管理系统模块展示</p>
      </div>
      <div class="screenshots-grid">
        <div class="screenshot-card" v-for="m in backendModules" :key="m.name">
          <div class="screenshot-img" :style="{ background: m.gradient }">
            <div class="module-preview">
              <div class="module-icon">{{ m.icon }}</div>
              <div class="module-name">{{ m.name }}</div>
            </div>
          </div>
          <div class="screenshot-info">
            <h4>{{ m.name }}</h4>
            <p>{{ m.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 技术文档 -->
    <section id="docs" class="docs-section">
      <div class="section-header">
        <h2>技术文档中心</h2>
        <p>🎉 <strong>完全免费开源</strong> · MIT 协议可商用 · 配套完整开发文档</p>
      </div>
      <div class="docs-grid">
        <div class="doc-card">
          <div class="doc-icon">
            <el-icon><Document /></el-icon>
          </div>
          <div class="doc-content">
            <h4>🚀 快速开始</h4>
            <p>5分钟快速部署，从零开始搭建环境到成功运行</p>
            <div class="doc-tags">
              <span class="doc-tag green">免费</span>
              <span class="doc-tag">环境要求</span>
              <span class="doc-tag">安装步骤</span>
              <span class="doc-tag">配置文件</span>
            </div>
            <a href="/#/doc#quickstart" target="_blank" class="doc-link">查看文档 →</a>
          </div>
        </div>
        <div class="doc-card">
          <div class="doc-icon">
            <el-icon><Key /></el-icon>
          </div>
          <div class="doc-content">
            <h4>📡 接口文档</h4>
            <p>完整的 RESTful API 接口说明，支持在线调试</p>
            <div class="doc-tags">
              <span class="doc-tag green">免费</span>
              <span class="doc-tag">登录接口</span>
              <span class="doc-tag">权限接口</span>
              <span class="doc-tag">业务接口</span>
            </div>
            <a href="/#/doc#api" target="_blank" class="doc-link">查看文档 →</a>
          </div>
        </div>
        <div class="doc-card">
          <div class="doc-icon">
            <el-icon><Setting /></el-icon>
          </div>
          <div class="doc-content">
            <h4>🔧 开发指南</h4>
            <p>前后端分离架构，标准化开发流程，二开无忧</p>
            <div class="doc-tags">
              <span class="doc-tag green">免费</span>
              <span class="doc-tag">项目结构</span>
              <span class="doc-tag">路由配置</span>
              <span class="doc-tag">组件开发</span>
            </div>
            <a href="/#/doc#guide" target="_blank" class="doc-link">查看文档 →</a>
          </div>
        </div>
        <div class="doc-card">
          <div class="doc-icon">
            <el-icon><Cpu /></el-icon>
          </div>
          <div class="doc-content">
            <h4>⚡ 代码生成器</h4>
            <p>一键生成 CRUD 代码，开发效率提升 80%</p>
            <div class="doc-tags">
              <span class="doc-tag green">免费</span>
              <span class="doc-tag">模板配置</span>
              <span class="doc-tag">字段映射</span>
              <span class="doc-tag">批量生成</span>
            </div>
            <a href="/#/doc#generator" target="_blank" class="doc-link">查看文档 →</a>
          </div>
        </div>
      </div>
      <div class="docs-extra">
        <div class="extra-item">
          <span class="extra-icon">📦</span>
          <span>Docker 一键部署</span>
        </div>
        <div class="extra-item">
          <span class="extra-icon">🔄</span>
          <span>持续更新中</span>
        </div>
        <div class="extra-item">
          <span class="extra-icon">💻</span>
          <span>Vue3 + TP8</span>
        </div>
        <div class="extra-item">
          <span class="extra-icon">📱</span>
          <span>多终端适配</span>
        </div>
      </div>
    </section>

    <!-- 开源协议 -->
    <section class="opensource-section">
      <div class="opensource-content">
        <div class="opensource-badge-large">
          <el-icon><Link /></el-icon>
          <span>MIT License</span>
        </div>
        <h2>完全免费，开源可商用的后台管理系统</h2>
        <p>基于 MIT 协议开源，您可以免费用于个人和商业项目。无授权费用，无使用限制。</p>
        <div class="opensource-features">
          <div class="os-feature">
            <el-icon><Check /></el-icon>
            <span>完全免费</span>
          </div>
          <div class="os-feature">
            <el-icon><Check /></el-icon>
            <span>开源可商用</span>
          </div>
          <div class="os-feature">
            <el-icon><Check /></el-icon>
            <span>无授权费用</span>
          </div>
          <div class="os-feature">
            <el-icon><Check /></el-icon>
            <span>永久更新</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 底部 CTA -->
    <section id="contact" class="cta-section">
      <div class="cta-content">
        <h2>准备好开始使用了吗？</h2>
        <p>演示地址体验飞鱼 Admin，<strong style="color: #52c41a;">完全免费，开源可商用</strong></p>
        <div class="cta-buttons">
          <el-button type="primary" size="large" style="min-width: 180px;" @click="goGitee">
            <el-icon><Link /></el-icon>
            获取源码
          </el-button>
          <el-button size="large" plain @click="goContact">QQ: 767677830</el-button>
        </div>
      </div>
    </section>

    <!-- 底部 -->
    <footer class="demo-footer">
      <div class="footer-content">
        <div class="footer-left">
          <div class="footer-logo">
            <img :src="fishLogo" alt="飞鱼" class="footer-logo-img" />
            <span>飞鱼 Admin</span>
          </div>
          <p class="footer-desc">新一代企业级 Vue3 后台管理系统</p>
        </div>
        <div class="footer-right">
          <div class="footer-links">
            <div class="link-group">
              <h4>产品</h4>
              <a href="#">功能介绍</a>
              <a href="#">更新日志</a>
              <a href="#">帮助文档</a>
            </div>
            <div class="link-group">
              <h4>支持</h4>
              <a href="#">技术论坛</a>
              <a href="#">在线客服</a>
              <a href="#">联系销售</a>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© 2024 飞鱼 Admin. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ArrowRight, Link, Lightning, Lock, Grid, Cpu, Connection, DataLine, Document, Setting, Check } from '@element-plus/icons-vue'
import fishLogo from '@/assets/logo-fish.png'

const router = useRouter()

const goLogin = () => {
  window.open('http://demo.fydev.cn/admin/', '_blank')
}

const goGitee = () => {
  window.open('https://gitee.com/gynet/feiyuadmin', '_blank')
}

const goContact = () => {
  prompt('复制QQ号联系', '767677830')
}

const goGithub = () => {
  window.open('/doc', '_blank')
}

const getBarHeight = (i) => {
  const heights = ['45%', '70%', '55%', '85%', '40%', '65%', '80%']
  return heights[(i - 1) % heights.length]
}
const backendModules = [
  { icon: '👤', name: '用户管理', desc: '用户CRUD、状态管理', gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
  { icon: '🔐', name: '角色权限', desc: 'RBAC权限控制', gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
  { icon: '📋', name: '菜单管理', desc: '动态菜单配置', gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
  { icon: '🏢', name: '部门管理', desc: '组织架构管理', gradient: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)' },
  { icon: '💼', name: '岗位管理', desc: '岗位序列管理', gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' },
  { icon: '📝', name: '操作日志', desc: '操作行为记录', gradient: 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)' },
  { icon: '🔑', name: '登录日志', desc: '登录记录追踪', gradient: 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)' },
  { icon: '⚙️', name: '参数配置', desc: '系统参数管理', gradient: 'linear-gradient(135deg, #f093fb 0%, #4facfe 100%)' },
  { icon: '📖', name: '数据字典', desc: '枚举类型管理', gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
  { icon: '📁', name: '文件上传', desc: '本地上传/OSS', gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' },
  { icon: '⚡', name: '代码生成器', desc: '一键CRUD生成', gradient: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)' },
  { icon: '🏢', name: '多租户', desc: 'SaaS多客户支持', gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
  { icon: '🔄', name: '工作流引擎', desc: '可视化流程设计', gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
  { icon: '📊', name: '数据大屏', desc: '拖拽式大屏设计', gradient: 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)' },
  { icon: '🔔', name: '消息通知', desc: '多渠道推送', gradient: 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)' },
  { icon: '⏰', name: '定时任务', desc: 'Crontab调度', gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' },
  { icon: '📝', name: '表单设计器', desc: '拖拽表单构建', gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
  { icon: '💳', name: '支付渠道', desc: '微信/支付宝', gradient: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)' },
  { icon: '📰', name: '富文本编辑器', desc: 'Markdown/WYSIWYG', gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
  { icon: '💻', name: '代码编辑器', desc: 'Monaco Editor', gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
]
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.demo-container {
  min-height: 100vh;
  background: #f8fafc;
}

/* 导航栏 */
.demo-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid #e5e7eb;
  z-index: 1000;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-img {
  width: 36px;
  height: 36px;
  object-fit: contain;
}

.logo-text {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a2e;
}

/* 开源标签 */
.opensource-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%);
  border-radius: 20px;
  font-size: 12px;
  color: #fff;
  font-weight: 500;
  margin-left: 12px;
}

.header-actions .el-button {
  height: 36px;
  padding: 0 20px;
  font-size: 14px;
  border-radius: 6px;
  background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
  border: none;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}

.header-actions .el-button:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
  transform: translateY(-1px);
}

.nav-links {
  display: flex;
  gap: 40px;
}

.nav-links a {
  color: #606266;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}

.nav-links a:hover {
  color: #2563EB;
}

/* Hero 区域 */
.hero-section {
  position: relative;
  padding: 160px 40px 100px;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.bg-grid {
  position: absolute;
  inset: 0;
  background-image: 
    linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px);
  background-size: 60px 60px;
}

.bg-glow {
  position: absolute;
  top: -200px;
  left: 50%;
  transform: translateX(-50%);
  width: 800px;
  height: 800px;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 600px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px;
  background: rgba(37, 99, 235, 0.1);
  border: 1px solid rgba(37, 99, 235, 0.2);
  border-radius: 20px;
  font-size: 13px;
  color: #2563EB;
  margin-bottom: 24px;
}

.badge-dot {
  width: 6px;
  height: 6px;
  background: #2563EB;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.hero-title {
  font-size: 52px;
  font-weight: 700;
  line-height: 1.2;
  color: #1a1a2e;
  margin-bottom: 20px;
}

.hero-desc {
  font-size: 18px;
  color: #606266;
  line-height: 1.8;
  margin-bottom: 32px;
}

.hero-actions {
  display: flex;
  gap: 16px;
  margin-bottom: 48px;
}

.hero-actions .el-button {
  height: 52px;
  padding: 0 40px;
  font-size: 16px;
  border-radius: 8px;
  font-weight: 500;
  min-width: 160px;
}

.hero-actions .el-button--primary {
  background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
  border: none;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
}

.hero-actions .el-button--primary:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
  transform: translateY(-2px);
}

.hero-actions .el-button.is-plain {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: #fff;
}

.hero-actions .el-button.is-plain:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 32px;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 28px;
  font-weight: 700;
  color: #2563EB;
}

.stat-label {
  font-size: 13px;
  color: #909399;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #e5e7eb;
}

/* 预览窗口 */
.hero-preview {
  position: absolute;
  top: 100px;
  right: 40px;
  width: 720px;
  z-index: 1;
}

.preview-window {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.window-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: #f5f7fa;
  border-bottom: 1px solid #e5e7eb;
}

.window-dots {
  display: flex;
  gap: 6px;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.dot.red { background: #ff5f56; }
.dot.yellow { background: #ffbd2e; }
.dot.green { background: #27c93f; }

.window-title {
  font-size: 13px;
  color: #606266;
}

.window-content {
  display: flex;
  height: 420px;
}

.preview-sidebar {
  width: 200px;
  background: #fff;
  border-right: 1px solid #e5e7eb;
  padding: 16px;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  margin-bottom: 16px;
}

.mini-logo {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #2563EB, #1d4ed8);
  border-radius: 6px;
}

.sidebar-mini-logo {
  width: 28px;
  height: 28px;
  object-fit: contain;
}

.sidebar-logo span {
  font-size: 13px;
  font-weight: 600;
  color: #303133;
}

.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 6px;
  font-size: 13px;
  color: #606266;
  cursor: pointer;
}

.menu-item.active {
  background: #e6f7ff;
  color: #2563EB;
}

.menu-icon {
  width: 16px;
  height: 16px;
  background: #dcdfe6;
  border-radius: 4px;
}

.menu-item.active .menu-icon {
  background: #2563EB;
}

.preview-main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.preview-header {
  display: flex;
  gap: 8px;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.preview-tab {
  padding: 6px 16px;
  font-size: 12px;
  color: #909399;
  border-radius: 4px;
  cursor: pointer;
}

.preview-tab.active {
  background: #e6f7ff;
  color: #2563EB;
}

.preview-content {
  flex: 1;
  padding: 16px;
  background: #f8fafc;
}

.preview-card {
  background: #fff;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-header-bar {
  width: 80px;
  height: 16px;
  background: #e6f7ff;
  border-radius: 4px;
  margin-bottom: 12px;
}

.card-lines {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.line {
  height: 10px;
  background: #f0f0f0;
  border-radius: 4px;
}

.line.short { width: 40%; }
.line.medium { width: 60%; }
.line.long { width: 80%; }

.preview-table {
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-header {
  display: flex;
  background: #fafafa;
  border-bottom: 1px solid #f0f0f0;
}

.th {
  flex: 1;
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #606266;
}

.table-row {
  display: flex;
  border-bottom: 1px solid #f0f0f0;
}

.table-row:last-child {
  border-bottom: none;
}

.td {
  flex: 1;
  padding: 12px 16px;
  font-size: 12px;
  color: #303133;
  display: flex;
  align-items: center;
}

.action-btn {
  color: #409eff;
  font-size: 11px;
  margin-right: 8px;
  cursor: pointer;
}

.action-btn.danger {
  color: #f56c6c;
}

/* 功能特性 */
.features-section {
  padding: 100px 40px;
  background: #fff;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-header h2 {
  font-size: 36px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.section-header p {
  font-size: 16px;
  color: #909399;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.feature-card {
  padding: 32px;
  background: #f8fafc;
  border-radius: 12px;
  transition: all 0.3s;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.feature-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin-bottom: 20px;
}

.feature-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563EB; }
.feature-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.feature-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.feature-icon.orange { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.feature-icon.teal { background: rgba(20, 184, 166, 0.1); color: #14b8a6; }
.feature-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.feature-card h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.feature-card p {
  font-size: 14px;
  color: #606266;
  line-height: 1.7;
}

/* 技术栈 */
.tech-section {
  padding: 100px 40px;
  background: #f8fafc;
}

.tech-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.tech-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px;
  background: #fff;
  border-radius: 12px;
  transition: all 0.3s;
}

.tech-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.tech-icon {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tech-icon svg {
  width: 48px;
  height: 48px;
}

.tech-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 4px;
}

.tech-desc {
  font-size: 12px;
  color: #909399;
}

/* 截图预览 */
.screenshots-section {
  padding: 100px 40px;
  background: #f8fafc;
}

.screenshots-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.screenshot-card {
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
}

.screenshot-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
}

.screenshot-img {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.module-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.module-icon {
  font-size: 48px;
  filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
}

.module-name {
  font-size: 20px;
  font-weight: 600;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.window-dots {
  display: flex;
  gap: 6px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.dot.red { background: #ff5f56; }
.dot.yellow { background: #ffbd2e; }
.dot.green { background: #27c93f; }

.window-title {
  font-size: 12px;
  color: #909399;
}

.window-body {
  display: flex;
  height: 280px;
}

.mock-sidebar {
  width: 140px;
  background: #fff;
  border-right: 1px solid #e5e7eb;
  padding: 12px;
}

.mock-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  margin-bottom: 16px;
}

.mock-logo img {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

.mock-logo span {
  font-size: 12px;
  font-weight: 600;
  color: #303133;
}

.mock-menu {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.mock-menu-item {
  padding: 8px 10px;
  font-size: 11px;
  color: #606266;
  border-radius: 4px;
  cursor: pointer;
}

.mock-menu-item.active {
  background: #e6f7ff;
  color: #2563EB;
}

.mock-content {
  flex: 1;
  padding: 12px;
  background: #f8fafc;
  overflow: hidden;
}

.mock-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.mock-tab {
  padding: 6px 14px;
  font-size: 11px;
  color: #909399;
  background: #fff;
  border-radius: 4px;
  cursor: pointer;
}

.mock-tab.active {
  background: #e6f7ff;
  color: #2563EB;
}

.mock-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 12px;
}

.mock-stat-card {
  background: #fff;
  border-radius: 6px;
  padding: 12px;
  display: flex;
  gap: 10px;
}

.stat-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 6px;
}

.stat-text {
  flex: 1;
}

.stat-num {
  width: 40px;
  height: 12px;
  background: #e5e7eb;
  border-radius: 4px;
  margin-bottom: 6px;
}

.stat-label {
  width: 60px;
  height: 8px;
  background: #f0f0f0;
  border-radius: 4px;
}

.mock-chart {
  background: #fff;
  border-radius: 6px;
  padding: 12px;
  height: 100px;
}

.chart-title {
  width: 60px;
  height: 10px;
  background: #e5e7eb;
  border-radius: 4px;
  margin-bottom: 12px;
}

.chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  height: 60px;
}

.bar {
  flex: 1;
  background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
  border-radius: 4px 4px 0 0;
  opacity: 0.8;
}

.mock-table {
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
}

.table-header {
  display: flex;
  background: #fafafa;
  border-bottom: 1px solid #f0f0f0;
}

.th {
  flex: 1;
  padding: 10px 8px;
  font-size: 10px;
  font-weight: 600;
  color: #606266;
  text-align: left;
}

.table-row {
  display: flex;
  border-bottom: 1px solid #f0f0f0;
}

.table-row:last-child {
  border-bottom: none;
}

.td {
  flex: 1;
  padding: 10px 8px;
  font-size: 10px;
  color: #303133;
  display: flex;
  align-items: center;
}

.status-on {
  width: 40px;
  height: 16px;
  background: #d4edda;
  color: #155724;
  border-radius: 10px;
  font-size: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action {
  font-size: 10px;
  cursor: pointer;
}

.action.edit {
  color: #409eff;
  margin-right: 6px;
}

.action.del {
  color: #f56c6c;
}

.mock-tree {
  background: #fff;
  border-radius: 6px;
  padding: 12px;
}

.tree-node {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  font-size: 11px;
  color: #303133;
}

.tree-node.root {
  font-weight: 600;
}

.tree-children {
  margin-left: 20px;
  border-left: 1px dashed #e5e7eb;
}

.tree-icon {
  font-size: 12px;
}

.tree-name {
  flex: 1;
}

.tree-path {
  color: #909399;
  font-size: 10px;
}

.screenshot-info {
  padding: 16px 20px;
  background: #fff;
}

.screenshot-info h4 {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 4px;
}

.screenshot-info p {
  font-size: 13px;
  color: #909399;
}

/* CTA */
.cta-section {
  padding: 100px 40px;
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
}

.cta-content {
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
}

.cta-content h2 {
  font-size: 36px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 12px;
}

.cta-content p {
  font-size: 18px;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 32px;
}

.cta-buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.cta-buttons .el-button {
  min-width: 180px;
  height: 52px;
  padding: 0 32px !important;
  font-size: 16px;
  border-radius: 8px;
}

.cta-buttons .el-button--primary {
  background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%);
  border: none;
  box-shadow: 0 4px 14px rgba(82, 196, 26, 0.4);
}

.cta-buttons .el-button--primary:hover {
  background: linear-gradient(135deg, #73d13d 0%, #52c41a 100%);
  box-shadow: 0 6px 20px rgba(82, 196, 26, 0.5);
  transform: translateY(-2px);
}

.cta-buttons .el-button.is-plain {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: #fff;
}

.cta-buttons .el-button.is-plain:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
}

/* 技术文档 */
.docs-section {
  padding: 100px 40px;
  background: #f8fafc;
}

.docs-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.doc-card {
  background: #fff;
  border-radius: 12px;
  padding: 28px;
  transition: all 0.3s;
  border: 1px solid #e5e7eb;
}

.doc-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
  border-color: #2563EB;
}

.doc-icon {
  width: 48px;
  height: 48px;
  background: rgba(37, 99, 235, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: #2563EB;
  margin-bottom: 16px;
}

.doc-content h4 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 8px;
}

.doc-content p {
  font-size: 13px;
  color: #909399;
  line-height: 1.6;
  margin-bottom: 12px;
}

.doc-tags {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.doc-tag {
  padding: 2px 8px;
  background: #f0f9ff;
  color: #2563EB;
  font-size: 11px;
  border-radius: 4px;
}

.doc-tag.green {
  background: #f0f9eb;
  color: #52c41a;
}

.doc-link {
  display: inline-block;
  margin-top: 12px;
  color: #2563EB;
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}

.doc-link:hover {
  color: #1d4ed8;
}

.docs-extra {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin-top: 40px;
  padding-top: 30px;
  border-top: 1px dashed #e5e7eb;
}

.extra-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #606266;
  font-size: 14px;
}

.extra-icon {
  font-size: 18px;
}

/* 开源协议 */
.opensource-section {
  padding: 80px 40px;
  background: linear-gradient(135deg, #1e3a5f 0%, #0d1117 100%);
  text-align: center;
}

.opensource-content {
  max-width: 700px;
  margin: 0 auto;
}

.opensource-badge-large {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 20px;
  background: rgba(82, 196, 26, 0.2);
  border: 1px solid rgba(82, 196, 26, 0.4);
  border-radius: 24px;
  font-size: 14px;
  color: #52c41a;
  margin-bottom: 24px;
}

.opensource-content h2 {
  font-size: 32px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 16px;
}

.opensource-content p {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.8;
  margin-bottom: 32px;
}

.opensource-features {
  display: flex;
  justify-content: center;
  gap: 40px;
}

.os-feature {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  font-size: 14px;
}

.os-feature .el-icon {
  color: #52c41a;
  font-size: 18px;
}

/* 底部 */
.demo-footer {
  background: #0d1117;
  color: #fff;
  padding: 60px 40px 20px;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  margin-bottom: 40px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.footer-logo-img {
  width: 28px;
  height: 28px;
  object-fit: contain;
}

.footer-logo span {
  font-size: 18px;
  font-weight: 600;
}

.footer-desc {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.5);
}

.footer-links {
  display: flex;
  gap: 80px;
}

.link-group h4 {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 16px;
  color: #fff;
}

.link-group a {
  display: block;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  margin-bottom: 10px;
  transition: color 0.2s;
}

.link-group a:hover {
  color: #fff;
}

.footer-bottom {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.4);
}

/* Button improvements */
.el-button--primary {
  background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
  border: none !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
}
.el-button--primary:hover {
  background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4) !important;
}
.el-button[plain] {
  border-radius: 8px !important;
  font-weight: 500 !important;
  border-width: 1px !important;
}
</style>

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
          <a href="#screenshots">截图预览</a>
          <a href="/doc#overview" target="_blank">技术文档</a>
          <a href="#contact">联系我们</a>
        </nav>
        <div class="header-actions">
          <el-button type="primary" @click="goLogin">获取源码</el-button>
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
            获取源码
          </el-button>
          <el-button size="large" plain style="min-width: 160px;" @click="goGithub">
            <el-icon><Link /></el-icon>
            文档中心
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
          <div class="tech-icon vue"></div>
          <span class="tech-name">Vue 3</span>
          <span class="tech-desc">渐进式前端框架</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon vite"></div>
          <span class="tech-name">Vite</span>
          <span class="tech-desc">下一代前端构建工具</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon element"></div>
          <span class="tech-name">Element Plus</span>
          <span class="tech-desc">Vue3 UI 组件库</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon pinia"></div>
          <span class="tech-name">Pinia</span>
          <span class="tech-desc">新一代状态管理</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon php"></div>
          <span class="tech-name">PHP 8.2</span>
          <span class="tech-desc">后端核心语言</span>
        </div>
        <div class="tech-item">
          <div class="tech-icon thinkphp"></div>
          <span class="tech-name">ThinkPHP 8</span>
          <span class="tech-desc">高性能 PHP 框架</span>
        </div>
      </div>
    </section>

    <!-- 截图预览 -->
    <section id="screenshots" class="screenshots-section">
      <div class="section-header">
        <h2>截图预览</h2>
        <p>简洁高效的界面设计</p>
      </div>
      <div class="screenshots-grid">
        <div class="screenshot-card">
          <div class="screenshot-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <span class="img-label">工作台</span>
          </div>
          <div class="screenshot-info">
            <h4>数据概览</h4>
            <p>直观展示核心业务数据</p>
          </div>
        </div>
        <div class="screenshot-card">
          <div class="screenshot-img" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <span class="img-label">用户管理</span>
          </div>
          <div class="screenshot-info">
            <h4>用户管理</h4>
            <p>完善的用户权限体系</p>
          </div>
        </div>
        <div class="screenshot-card">
          <div class="screenshot-img" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <span class="img-label">菜单管理</span>
          </div>
          <div class="screenshot-info">
            <h4>菜单管理</h4>
            <p>灵活的菜单权限配置</p>
          </div>
        </div>
        <div class="screenshot-card">
          <div class="screenshot-img" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <span class="img-label">数据大屏</span>
          </div>
          <div class="screenshot-info">
            <h4>数据可视化</h4>
            <p>拖拽式大屏设计器</p>
          </div>
        </div>
        <div class="screenshot-card">
          <div class="screenshot-img" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
            <span class="img-label">代码生成</span>
          </div>
          <div class="screenshot-info">
            <h4>代码生成器</h4>
            <p>一键生成 CRUD 代码</p>
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
            <a href="/doc#quick-start" target="_blank" class="doc-link">查看文档 →</a>
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
            <a href="/doc#api-reference" target="_blank" class="doc-link">查看文档 →</a>
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
            <a href="/doc#crud-example" target="_blank" class="doc-link">查看文档 →</a>
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
            <a href="/doc#crud-example" target="_blank" class="doc-link">查看文档 →</a>
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
        <p>获取源码体验飞鱼 Admin，<strong style="color: #52c41a;">完全免费，开源可商用</strong></p>
        <div class="cta-buttons">
          <el-button type="primary" size="large" style="min-width: 180px;" @click="goLogin">
            <el-icon><Link /></el-icon>
            免费获取源码
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
  window.open('/admin/', '_blank')
}

const goContact = () => {
  prompt('复制QQ号联系', '767677830')
}

const goGithub = () => {
  window.open('/doc', '_blank')
}
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.tech-icon.vue { background: linear-gradient(135deg, #42b883 0%, #35495e 100%); }
.tech-icon.vite { background: linear-gradient(135deg, #646cff 0%, #a855f7 100%); }
.tech-icon.element { background: linear-gradient(135deg, #409eff 0%, #337ecc 100%); }
.tech-icon.pinia { background: linear-gradient(135deg, #ffd859 0%, #ffb347 100%); }
.tech-icon.php { background: linear-gradient(135deg, #777bb4 0%, #554594 100%); }
.tech-icon.thinkphp { background: linear-gradient(135deg, #64a838 0%, #478032 100%); }

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
  background: #fff;
}

.screenshots-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.screenshot-card {
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  transition: all 0.3s;
}

.screenshot-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
}

.screenshot-img {
  height: 220px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.img-label {
  font-size: 24px;
  font-weight: 600;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.screenshot-info {
  padding: 20px;
}

.screenshot-info h4 {
  font-size: 16px;
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

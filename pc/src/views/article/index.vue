<template>
  <div class="article-page">
    <!-- 顶部导航 -->
    <header class="art-header">
      <div class="header-inner">
        <div class="logo" @click="$router.push('/')">
          <svg width="32" height="32" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼</span>
        </div>
        <div class="header-right">
          <a href="/" class="header-link">首页</a>
          <a href="/article" class="header-link active">文章</a>
        </div>
      </div>
    </header>

    <!-- 文章列表视图 -->
    <div v-if="!articleId" class="art-main">
      <div class="art-container">
        <!-- 页面标题 -->
        <div class="page-title">
          <h1>技术文章</h1>
          <p>分享前沿技术，解读行业动态</p>
        </div>

        <!-- 分类筛选 -->
        <div class="category-filter">
          <span class="filter-label">分类：</span>
          <div class="filter-tags">
            <span :class="['filter-tag', { active: !currentCategory }]" @click="selectCategory(null)">全部</span>
            <span v-for="cat in categories" :key="cat.id"
              :class="['filter-tag', { active: currentCategory === cat.id }]"
              @click="selectCategory(cat.id)">{{ cat.name }}</span>
          </div>
        </div>

        <!-- 文章列表 -->
        <div v-if="articles.length > 0" class="article-grid">
          <div v-for="item in articles" :key="item.id" class="article-card" @click="viewDetail(item.id)">
            <div class="card-cover">
              <el-image :src="item.cover" fit="cover" class="cover-img">
                <template #error>
                  <div class="cover-placeholder">
                    <el-icon :size="40"><Document /></el-icon>
                  </div>
                </template>
              </el-image>
              <span v-if="item.category_name" class="card-category">{{ item.category_name }}</span>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-summary">{{ item.summary || item.description || '暂无摘要' }}</p>
              <div class="card-meta">
                <span class="meta-item">
                  <el-icon><Calendar /></el-icon>
                  {{ item.create_time || item.published_at }}
                </span>
                <span class="meta-item">
                  <el-icon><View /></el-icon>
                  {{ item.view_count || 0 }} 阅读
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 空状态 -->
        <div v-else class="empty-state">
          <el-empty description="暂无文章" />
        </div>

        <!-- 分页 -->
        <div v-if="total > pageSize" class="pagination-wrap">
          <el-pagination
            background
            layout="prev, pager, next"
            :total="total"
            :page-size="pageSize"
            v-model:current-page="page"
            @current-change="loadArticles"
          />
        </div>
      </div>
    </div>

    <!-- 文章详情视图 -->
    <div v-else class="detail-page">
      <div class="detail-container">
        <div class="detail-back" @click="articleId = null">
          <el-icon><ArrowLeft /></el-icon>
          返回列表
        </div>

        <article v-if="currentArticle" class="article-detail">
          <header class="detail-header">
            <div class="detail-tags">
              <el-tag v-if="currentArticle.category_name" size="small">{{ currentArticle.category_name }}</el-tag>
            </div>
            <h1 class="detail-title">{{ currentArticle.title }}</h1>
            <div class="detail-meta">
              <span>发布时间：{{ currentArticle.create_time || currentArticle.published_at }}</span>
              <span>阅读：{{ currentArticle.view_count || 0 }}</span>
              <span>作者：{{ currentArticle.author || '官方' }}</span>
            </div>
          </header>

          <div class="detail-cover" v-if="currentArticle.cover">
            <el-image :src="currentArticle.cover" fit="cover" />
          </div>

          <div class="detail-content" v-html="currentArticle.content"></div>

          <!-- 点赞/收藏 -->
          <div class="detail-actions">
            <el-button :type="isLiked ? 'primary' : 'default'" @click="toggleLike">
              <el-icon><Star /></el-icon>
              {{ isLiked ? '已点赞' : '点赞' }} ({{ currentArticle.like_count || 0 }})
            </el-button>
            <el-button @click="$router.push('/article')">
              <el-icon><ArrowLeft /></el-icon>
              返回列表
            </el-button>
          </div>
        </article>

        <!-- 加载状态 -->
        <div v-else-if="loading" class="detail-loading">
          <el-skeleton :rows="10" animated />
        </div>
      </div>
    </div>

    <!-- 底部导航栏 -->
    <nav class="bottom-nav">
      <div class="nav-item" @click="$router.push('/')">
        <el-icon :size="22"><House /></el-icon>
        <span>首页</span>
      </div>
      <div class="nav-item" @click="$router.push('/category')">
        <el-icon :size="22"><Grid /></el-icon>
        <span>分类</span>
      </div>
      <div class="nav-item" @click="$router.push('/cart')">
        <el-icon :size="22"><ShoppingCart /></el-icon>
        <span>购物车</span>
      </div>
      <div class="nav-item" @click="$router.push('/user')">
        <el-icon :size="22"><User /></el-icon>
        <span>我的</span>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Document, Calendar, View, Star, House, Grid, ShoppingCart, User, ArrowLeft } from '@element-plus/icons-vue'
import request from '@/utils/request'

const articles = ref([])
const categories = ref([])
const currentCategory = ref(null)
const articleId = ref(null)
const currentArticle = ref(null)
const isLiked = ref(false)
const loading = ref(false)
const page = ref(1)
const pageSize = ref(12)
const total = ref(0)

const loadCategories = async () => {
  try {
    const res = await request.get('/article/categories')
    categories.value = res.data || []
  } catch {}
}

const loadArticles = async () => {
  loading.value = true
  try {
    const res = await request.get('/article/lists', {
      params: { category_id: currentCategory.value, page: page.value, page_size: pageSize.value }
    })
    articles.value = res.data?.list || res.data || []
    total.value = res.data?.total || 0
  } catch {
    articles.value = []
  } finally {
    loading.value = false
  }
}

const selectCategory = (id) => {
  currentCategory.value = id
  page.value = 1
  loadArticles()
}

const viewDetail = async (id) => {
  articleId.value = id
  loading.value = true
  try {
    const res = await request.get(`/article/detail/${id}`)
    currentArticle.value = res.data
    isLiked.value = res.data?.is_liked || false
  } catch {
    ElMessage.error('加载失败')
  } finally {
    loading.value = false
  }
}

const toggleLike = async () => {
  if (!articleId.value) return
  try {
    await request.post('/article/like', { id: articleId.value })
    isLiked.value = !isLiked.value
    const count = currentArticle.value.like_count || 0
    currentArticle.value.like_count = isLiked.value ? count + 1 : count - 1
  } catch {}
}

onMounted(() => {
  loadCategories()
  loadArticles()
})
</script>

<style scoped>
.article-page {
  min-height: 100vh;
  background: #f5f6f8;
  padding-bottom: 70px;
}

/* 顶部导航 */
.art-header {
  background: #fff;
  border-bottom: 1px solid #ebeef5;
  position: sticky;
  top: 0;
  z-index: 100;
}
.header-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 18px;
  color: #2563EB;
  cursor: pointer;
}
.header-right { display: flex; gap: 20px; }
.header-link { color: #606266; text-decoration: none; font-size: 14px; }
.header-link.active { color: #2563EB; font-weight: 600; }

/* 主容器 */
.art-main { padding: 20px; }
.art-container { max-width: 1200px; margin: 0 auto; }

/* 页面标题 */
.page-title {
  text-align: center;
  padding: 40px 0 30px;
}
.page-title h1 { font-size: 28px; color: #303133; margin-bottom: 8px; }
.page-title p { color: #909399; font-size: 15px; }

/* 分类筛选 */
.category-filter {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  background: #fff;
  padding: 16px 20px;
  border-radius: 10px;
}
.filter-label { color: #606266; font-size: 14px; font-weight: 500; }
.filter-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.filter-tag {
  padding: 4px 14px;
  border-radius: 20px;
  font-size: 13px;
  cursor: pointer;
  color: #606266;
  background: #f0f2f5;
  transition: all 0.2s;
}
.filter-tag:hover { background: #e0e7ff; color: #2563EB; }
.filter-tag.active { background: #2563EB; color: #fff; }

/* 文章卡片 */
.article-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.article-card {
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.article-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(37,99,235,0.15);
}
.card-cover { position: relative; height: 180px; }
.cover-img { width: 100%; height: 100%; display: block; }
.cover-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f6f8;
  color: #c0c4cc;
}
.card-category {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #2563EB;
  color: #fff;
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 4px;
}
.card-body { padding: 16px; }
.card-title {
  font-size: 16px;
  color: #303133;
  margin: 0 0 8px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.card-summary {
  font-size: 13px;
  color: #909399;
  margin: 0 0 12px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-height: 1.6;
}
.card-meta { display: flex; gap: 16px; }
.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #c0c4cc;
}

/* 空状态 */
.empty-state { padding: 60px 0; }

/* 分页 */
.pagination-wrap { display: flex; justify-content: center; margin-top: 30px; }

/* 文章详情 */
.detail-page { padding: 20px; }
.detail-container { max-width: 800px; margin: 0 auto; }
.detail-back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #2563EB;
  cursor: pointer;
  margin-bottom: 24px;
  font-size: 14px;
}
.article-detail {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}
.detail-header { padding: 32px 40px 24px; border-bottom: 1px solid #ebeef5; }
.detail-tags { margin-bottom: 12px; }
.detail-title {
  font-size: 24px;
  color: #303133;
  margin: 0 0 16px;
  line-height: 1.4;
}
.detail-meta {
  display: flex;
  gap: 20px;
  font-size: 13px;
  color: #909399;
}
.detail-cover {
  padding: 20px 40px;
}
.detail-cover .el-image { width: 100%; border-radius: 8px; }
.detail-content {
  padding: 0 40px 32px;
  font-size: 15px;
  line-height: 1.8;
  color: #404040;
}
.detail-content :deep(h1),
.detail-content :deep(h2),
.detail-content :deep(h3) { color: #303133; margin: 20px 0 10px; }
.detail-content :deep(p) { margin: 0 0 16px; }
.detail-content :deep(img) { max-width: 100%; border-radius: 6px; }
.detail-content :deep(code) {
  background: #f0f2f5;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: monospace;
}
.detail-content :deep(pre) {
  background: #1a1a2e;
  color: #e6e6e6;
  padding: 16px;
  border-radius: 8px;
  overflow-x: auto;
  margin: 16px 0;
}
.detail-actions {
  padding: 20px 40px;
  border-top: 1px solid #ebeef5;
  display: flex;
  gap: 12px;
}

/* 加载状态 */
.detail-loading { padding: 40px; }

/* 底部导航 */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  border-top: 1px solid #ebeef5;
  display: flex;
  z-index: 200;
  padding: 6px 0;
  padding-bottom: env(safe-area-inset-bottom, 6px);
}
.nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  color: #909399;
  font-size: 11px;
  cursor: pointer;
  padding: 4px 0;
}
.nav-item.active { color: #2563EB; }

@media (max-width: 768px) {
  .article-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .detail-header { padding: 20px; }
  .detail-cover { padding: 12px 20px; }
  .detail-content { padding: 0 20px 24px; }
  .detail-actions { padding: 16px 20px; }
  .detail-title { font-size: 20px; }
  .page-title h1 { font-size: 22px; }
}
</style>

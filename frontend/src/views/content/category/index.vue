<template>
  <div class="category-layout">
    <!-- 左侧分类树 -->
    <el-card class="left-panel" shadow="never">
      <template #header>
        <div class="panel-header">
          <span>分类管理</span>
          <el-button type="primary" size="small" :icon="Plus" @click="openForm(null)">新增</el-button>
        </div>
      </template>
      <el-tree
        ref="treeRef"
        :data="treeData"
        :props="{ label: 'name', children: 'children' }"
        node-key="id"
        highlight-current
        default-expand-all
        :expand-on-click-node="false"
        @node-click="handleNodeClick"
      >
        <template #default="{ node, data }">
          <span class="tree-node">
            <span class="node-name">{{ data.name }}</span>
            <span class="node-actions">
              <el-button link type="primary" size="small" :icon="Plus" @click.stop="openForm(null, data.id)">子级</el-button>
              <el-button link type="primary" size="small" @click.stop="openForm(data)">编辑</el-button>
              <el-button link type="danger" size="small" @click.stop="handleDelete(data)">删除</el-button>
            </span>
          </span>
        </template>
      </el-tree>
    </el-card>

    <!-- 右侧文章列表 -->
    <div class="right-panel">
      <div class="right-header">
        <span class="panel-title">分类文章</span>
        <el-tag v-if="currentCategory" type="primary">{{ currentCategory.name }}</el-tag>
        <span v-else class="no-category-tip">请选择左侧分类</span>
      </div>
      <el-card shadow="never">
        <!-- 搜索栏 -->
        <div class="search-bar">
          <el-input v-model="searchForm.keyword" placeholder="文章标题搜索" style="width: 200px" clearable @keyup.enter="loadArticles" />
          <el-select v-model="searchForm.status" placeholder="状态" style="width: 120px" clearable @change="loadArticles">
            <el-option label="已发布" :value="1" />
            <el-option label="草稿" :value="0" />
          </el-select>
          <el-button type="primary" :icon="Search" @click="loadArticles">搜索</el-button>
          <el-button :icon="Refresh" @click="resetArticleSearch">重置</el-button>
        </div>

        <el-table :data="articleList" v-loading="articleLoading" stripe style="margin-top:12px">
          <el-table-column prop="id" label="ID" width="80" />
          <el-table-column prop="title" label="标题" min-width="200" show-overflow-tooltip />
          <el-table-column prop="author" label="作者" width="100" />
          <el-table-column prop="status" label="状态" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
                {{ row.status === 1 ? '已发布' : '草稿' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="views" label="浏览量" width="100" align="center" />
          <el-table-column prop="create_time" label="发布时间" width="180" />
        </el-table>
        <div class="pagination-wrap">
          <el-pagination
            v-model:current-page="articlePage"
            v-model:page-size="articlePageSize"
            :total="articleTotal"
            :page-sizes="[10, 20, 50]"
            layout="total, sizes, prev, pager, next"
            @change="loadArticles"
          />
        </div>
      </el-card>
    </div>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑分类' : '新增分类'" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
        <el-form-item label="分类名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="分类编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入分类编码（唯一）" />
        </el-form-item>
        <el-form-item label="父级分类" prop="pid">
          <el-tree-select
            v-model="form.pid"
            :data="treeDataSelect"
            :props="{ label: 'name', value: 'id' }"
            check-strictly
            clearable
            placeholder="顶级分类（不选）"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :value="1">启用</el-radio>
            <el-radio :value="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import request from '@/utils/request'

const treeRef = ref(null)
const treeData = ref([])
const formVisible = ref(false)
const submitLoading = ref(false)
const formRef = ref(null)

const currentCategory = ref(null)
const articleList = ref([])
const articleLoading = ref(false)
const articlePage = ref(1)
const articlePageSize = ref(10)
const articleTotal = ref(0)
const searchForm = reactive({ keyword: '', status: '' })

const form = reactive({
  id: null, name: '', code: '', pid: 0, sort: 100, status: 1
})

const rules = {
  name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入分类编码', trigger: 'blur' }],
}

// 给 tree-select 用，禁用自己作为父级
const treeDataSelect = computed(() => {
  const removeNode = (nodes, excludeId) => {
    return nodes
      .filter(n => n.id !== excludeId)
      .map(n => ({ ...n, children: n.children ? removeNode(n.children, excludeId) : undefined }))
  }
  return removeNode(treeData.value, form.id || -1)
})

// 转为 list（含所有层级扁平）
const flattenTree = (nodes) => {
  const result = []
  const flat = (arr) => arr.forEach(n => { result.push(n); if (n.children) flat(n.children) })
  flat(nodes)
  return result
}

const loadCategoryTree = async () => {
  try {
    const res = await request.get('/adminapi/mobile/category/lists')
    treeData.value = res.data || []
  } catch { treeData.value = [] }
}

const handleNodeClick = (data) => {
  currentCategory.value = data
  articlePage.value = 1
  loadArticles()
}

const loadArticles = async () => {
  if (!currentCategory.value) {
    articleList.value = []
    return
  }
  articleLoading.value = true
  try {
    const params = {
      page: articlePage.value,
      page_size: articlePageSize.value,
      category_id: currentCategory.value.id,
      keyword: searchForm.keyword,
      status: searchForm.status,
    }
    const res = await request.get('/adminapi/mobile/article/lists', { params })
    articleList.value = res.data?.list || res.data || []
    articleTotal.value = res.data?.total || 0
  } catch {
    articleList.value = []
  } finally {
    articleLoading.value = false
  }
}

const resetArticleSearch = () => {
  searchForm.keyword = ''; searchForm.status = ''
  articlePage.value = 1
  loadArticles()
}

const openForm = (row, defaultPid = null) => {
  if (row) {
    Object.assign(form, { id: row.id, name: row.name, code: row.code, pid: row.pid, sort: row.sort, status: row.status })
  } else {
    Object.assign(form, { id: null, name: '', code: '', pid: defaultPid || 0, sort: 100, status: 1 })
  }
  formVisible.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value?.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    if (form.id) {
      await request.put(`/adminapi/mobile/category/save?id=${form.id}`, { ...form })
      ElMessage.success('编辑成功')
    } else {
      await request.post('/adminapi/mobile/category/save', { ...form })
      ElMessage.success('新增成功')
    }
    formVisible.value = false
    await loadCategoryTree()
  } catch (e) {
    ElMessage.error(e?.message || '操作失败')
  } finally {
    submitLoading.value = false
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除分类"${row.name}"吗？${row.children?.length ? '（该分类下有子分类，将一并删除）' : ''}`, '提示', { type: 'warning' })
    await request.delete(`/adminapi/mobile/category/delete?id=${row.id}`)
    ElMessage.success('删除成功')
    if (currentCategory.value?.id === row.id) currentCategory.value = null
    await loadCategoryTree()
  } catch {}
}

onMounted(() => loadCategoryTree())
</script>

<style scoped>
.category-layout {
  display: flex;
  gap: 16px;
  height: calc(100vh - 140px);
}
.left-panel {
  width: 360px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.left-panel :deep(.el-card__body) {
  flex: 1;
  overflow-y: auto;
}
.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.tree-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding-right: 8px;
}
.node-name {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}
.node-actions {
  display: flex;
  gap: 4px;
  margin-left: 8px;
}
.right-panel {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.right-header {
  display: flex;
  align-items: center;
  gap: 12px;
}
.panel-title {
  font-size: 15px;
  font-weight: 600;
}
.no-category-tip {
  color: #909399;
  font-size: 13px;
}
.search-bar {
  display: flex;
  gap: 8px;
  align-items: center;
}
.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>

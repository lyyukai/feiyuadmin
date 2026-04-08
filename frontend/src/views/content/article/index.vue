<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增文章</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="标题 / 作者" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.category_id" placeholder="全部分类" style="width: 140px" clearable @change="loadData">
          <el-option v-for="c in categoryList" :key="c.id" :label="c.name" :value="c.id" />
        </el-select>
        <el-select v-model="searchForm.status" placeholder="状态" style="width: 120px" clearable @change="loadData">
          <el-option label="已发布" :value="1" />
          <el-option label="草稿" :value="0" />
        </el-select>
        <el-date-picker v-model="searchForm.dateRange" type="daterange" range-separator="至" start-placeholder="开始日期"
          end-placeholder="结束日期" value-format="YYYY-MM-DD" style="width: 240px" clearable @change="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="title" label="标题" min-width="200" show-overflow-tooltip>
          <template #default="{ row }">
            <div class="article-title-cell">
              <el-image v-if="row.cover_image" :src="row.cover_image" fit="cover" class="article-cover" />
              <span>{{ row.title }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="category_name" label="分类" width="120">
          <template #default="{ row }">
            <el-tag size="small">{{ row.category_name || '-' }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="author" label="作者" width="100" />
        <el-table-column prop="tags" label="标签" width="160">
          <template #default="{ row }">
            <span v-if="!row.tags">-</span>
            <el-tag v-for="tag in (row.tags || '').split(',')" :key="tag" size="small" style="margin-right:4px" v-else>{{ tag }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
              {{ row.status === 1 ? '已发布' : '草稿' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="views" label="浏览量" width="100" align="center" />
        <el-table-column prop="create_time" label="发布时间" width="180" />
        <el-table-column label="操作" width="220" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link :type="row.status === 1 ? 'warning' : 'success'" size="small" @click="toggleStatus(row)">
              {{ row.status === 1 ? '下架' : '发布' }}
            </el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.page_size"
          :total="pagination.total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next, jumper"
          @change="loadData"
        />
      </div>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑文章' : '新增文章'" width="860px" destroy-on-close :close-on-click-modal="false">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
        <el-row :gutter="16">
          <el-col :span="16">
            <el-form-item label="标题" prop="title">
              <el-input v-model="form.title" placeholder="请输入文章标题" maxlength="200" show-word-limit />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="分类" prop="category_id">
              <el-select v-model="form.category_id" placeholder="请选择分类" style="width: 100%">
                <el-option v-for="c in categoryList" :key="c.id" :label="c.name" :value="c.id" />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="作者" prop="author">
              <el-input v-model="form.author" placeholder="请输入作者" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="标签" prop="tags">
              <el-input v-model="form.tags" placeholder="多个标签用英文逗号分隔" />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="封面图" prop="cover_image">
          <div class="cover-uploader" @click="triggerCoverUpload">
            <el-image v-if="form.cover_image" :src="form.cover_image" fit="cover" class="cover-preview" />
            <div v-else class="cover-placeholder">
              <el-icon :size="32"><Plus /></el-icon>
              <span>点击上传封面图</span>
            </div>
          </div>
          <input ref="coverInputRef" type="file" accept="image/*" style="display:none" @change="handleCoverChange" />
          <el-button v-if="form.cover_image" type="danger" size="small" style="margin-top:8px" @click="form.cover_image = ''">移除</el-button>
        </el-form-item>

        <el-form-item label="摘要" prop="summary">
          <el-input v-model="form.summary" type="textarea" :rows="2" placeholder="请输入文章摘要" maxlength="300" show-word-limit />
        </el-form-item>

        <el-form-item label="正文内容" prop="content">
          <RichEditor v-model="form.content" height="360px" placeholder="请输入正文内容..." />
        </el-form-item>

        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :value="0">存草稿</el-radio>
            <el-radio :value="1">立即发布</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button @click="handleSubmitDraft">保存草稿</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">发布</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import RichEditor from '@/components/RichEditor/index.vue'
import request from '@/utils/request'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const categoryList = ref([])
const formVisible = ref(false)
const formRef = ref(null)
const coverInputRef = ref(null)

const pagination = reactive({ page: 1, page_size: 10, total: 0 })

const searchForm = reactive({ keyword: '', category_id: '', status: '', dateRange: [] })

const statusType = { 1: 'success', 0: 'info' }

const form = reactive({
  id: null, title: '', category_id: '', author: '', tags: '',
  cover_image: '', summary: '', content: '', status: 0
})

const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
}

const loadCategoryList = async () => {
  try {
    const res = await request.get('/adminapi/mobile/category/lists')
    categoryList.value = res.data || []
  } catch { categoryList.value = [] }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      page_size: pagination.page_size,
      keyword: searchForm.keyword,
      category_id: searchForm.category_id,
      status: searchForm.status,
    }
    if (searchForm.dateRange && searchForm.dateRange.length === 2) {
      params.start_date = searchForm.dateRange[0]
      params.end_date = searchForm.dateRange[1]
    }
    const res = await request.get('/adminapi/mobile/article/lists', { params })
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || 0
  } catch {
    ElMessage.error('加载数据失败')
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchForm.keyword = ''; searchForm.category_id = ''; searchForm.status = ''; searchForm.dateRange = []
  pagination.page = 1
  loadData()
}

const openForm = (row) => {
  if (row) {
    Object.assign(form, {
      id: row.id, title: row.title, category_id: row.category_id || '',
      author: row.author || '', tags: row.tags || '',
      cover_image: row.cover_image || '', summary: row.summary || '',
      content: row.content || '', status: row.status ?? 0
    })
  } else {
    Object.assign(form, { id: null, title: '', category_id: '', author: '', tags: '', cover_image: '', summary: '', content: '', status: 0 })
  }
  formVisible.value = true
}

const triggerCoverUpload = () => coverInputRef.value?.click()

const handleCoverChange = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  const fd = new FormData()
  fd.append('file', file)
  try {
    const res = await request.post('/adminapi/upload/image', fd)
    form.cover_image = res.data?.url || res.url || ''
    ElMessage.success('封面上传成功')
  } catch { ElMessage.error('封面上传失败') }
  e.target.value = ''
}

const handleSubmit = async () => {
  const valid = await formRef.value?.validate().catch(() => false)
  if (!valid) return
  submitLoading.value = true
  try {
    const payload = { ...form }
    if (form.id) {
      await request.put(`/adminapi/mobile/article/save?id=${form.id}`, payload)
      ElMessage.success('编辑成功')
    } else {
      await request.post('/adminapi/mobile/article/save', payload)
      ElMessage.success('新增成功')
    }
    formVisible.value = false
    loadData()
  } catch (e) {
    ElMessage.error(e?.message || '操作失败')
  } finally {
    submitLoading.value = false
  }
}

const handleSubmitDraft = async () => {
  form.status = 0
  await handleSubmit()
}

const toggleStatus = async (row) => {
  const newStatus = row.status === 1 ? 0 : 1
  try {
    await request.put(`/adminapi/mobile/article/save?id=${row.id}`, { ...row, status: newStatus })
    ElMessage.success(newStatus === 1 ? '发布成功' : '下架成功')
    loadData()
  } catch { ElMessage.error('操作失败') }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除文章"${row.title}"吗？`, '提示', { type: 'warning' })
    await request.delete(`/adminapi/mobile/article/delete?id=${row.id}`)
    ElMessage.success('删除成功')
    loadData()
  } catch {}
}

onMounted(() => {
  loadCategoryList()
  loadData()
})
</script>

<style scoped>
.search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 16px;
}
.search-bar-left,
.search-bar-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.table-card {
  border-radius: 8px;
}
.article-title-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}
.article-cover {
  width: 40px;
  height: 40px;
  border-radius: 4px;
  flex-shrink: 0;
}
.cover-uploader {
  width: 200px;
  height: 120px;
  border: 1px dashed #dcdfe6;
  border-radius: 6px;
  cursor: pointer;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.2s;
}
.cover-uploader:hover {
  border-color: #409eff;
}
.cover-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cover-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #909399;
  gap: 6px;
  font-size: 12px;
}
.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>

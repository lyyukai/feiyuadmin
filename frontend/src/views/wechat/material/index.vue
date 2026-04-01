<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-select v-model="searchForm.account_id" placeholder="选择公众号" clearable style="width: 180px">
          <el-option v-for="item in accountList" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <el-button type="primary" :icon="Plus" @click="handleUpload">上传素材</el-button>
      </div>
      <div class="search-bar-right">
        <el-select v-model="searchForm.type" placeholder="素材类型" style="width: 140px" clearable>
          <el-option label="图片" value="image" />
          <el-option label="视频" value="video" />
          <el-option label="语音" value="voice" />
          <el-option label="图文" value="news" />
        </el-select>
        <el-select v-model="searchForm.is_permanent" placeholder="素材类型" style="width: 140px" clearable>
          <el-option label="临时素材" :value="0" />
          <el-option label="永久素材" :value="1" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 统计卡片 -->
    <el-row :gutter="20" class="statistics-row">
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value">{{ statistics.total || 0 }}</div>
            <div class="stat-label">素材总数</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #67c23a">{{ statistics.image_count || 0 }}</div>
            <div class="stat-label">图片</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #e6a23c">{{ statistics.video_count || 0 }}</div>
            <div class="stat-label">视频</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #909399">{{ statistics.total_size_format || '0 B' }}</div>
            <div class="stat-label">总占用空间</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="title" label="标题" min-width="150" />
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.type === 'image'" type="success" size="small">图片</el-tag>
            <el-tag v-else-if="row.type === 'video'" type="warning" size="small">视频</el-tag>
            <el-tag v-else-if="row.type === 'voice'" type="info" size="small">语音</el-tag>
            <el-tag v-else type="primary" size="small">图文</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="format" label="格式" width="80" align="center">
          <template #default="{ row }">
            {{ row.format?.toUpperCase() || '-' }}
          </template>
        </el-table-column>
        <el-table-column prop="size" label="大小" width="100" align="center">
          <template #default="{ row }">
            {{ formatSize(row.size) }}
          </template>
        </el-table-column>
        <el-table-column prop="is_permanent" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.is_permanent === 1 ? 'success' : 'info'" size="small">
              {{ row.is_permanent === 1 ? '永久' : '临时' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="url" label="素材" min-width="200">
          <template #default="{ row }">
            <div v-if="row.type === 'image'" style="display: flex; align-items: center; gap: 10px;">
              <el-image v-if="row.url" :src="row.url" style="width: 50px; height: 50px; border-radius: 4px;" fit="cover" :preview-src-list="[row.url]" />
              <el-button link type="primary" size="small" @click="handleCopy(row.url)">复制链接</el-button>
            </div>
            <span v-else-if="row.url">{{ row.url }}</span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="media_id" label="媒体ID" min-width="150" show-overflow-tooltip />
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="120" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="handlePreview(row)">预览</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </el-card>

    <!-- 上传弹窗 -->
    <el-dialog v-model="uploadVisible" title="上传素材" width="500px" destroy-on-close>
      <el-form :model="uploadForm" label-width="100px">
        <el-form-item label="选择公众号" required>
          <el-select v-model="uploadForm.account_id" placeholder="请选择公众号" style="width: 100%;">
            <el-option v-for="item in accountList" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="素材类型" required>
          <el-select v-model="uploadForm.type" placeholder="请选择素材类型" style="width: 100%;">
            <el-option label="图片" value="image" />
            <el-option label="语音" value="voice" />
            <el-option label="视频" value="video" />
            <el-option label="图文" value="news" />
          </el-select>
        </el-form-item>
        <el-form-item label="选择文件" required>
          <el-upload
            ref="uploadRef"
            :auto-upload="false"
            :limit="1"
            :on-change="handleFileChange"
            :on-remove="handleFileRemove"
            drag
          >
            <el-icon class="el-icon--upload"><upload-filled /></el-icon>
            <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
            <template #tip>
              <div class="el-upload__tip">图片不超过2MB，视频不超过10MB，语音不超过2MB</div>
            </template>
          </el-upload>
        </el-form-item>
        <el-form-item label="标题" v-if="uploadForm.type === 'video'">
          <el-input v-model="uploadForm.title" placeholder="请输入标题" />
        </el-form-item>
        <el-form-item label="描述" v-if="uploadForm.type === 'video'">
          <el-input v-model="uploadForm.introduction" type="textarea" rows="3" placeholder="请输入描述" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="uploadVisible = false">取消</el-button>
        <el-button type="primary" :loading="uploadLoading" @click="submitUpload">上传</el-button>
      </template>
    </el-dialog>

    <!-- 预览弹窗 -->
    <el-dialog v-model="previewVisible" title="素材预览" width="600px" destroy-on-close>
      <div style="text-align: center;">
        <el-image v-if="previewUrl && previewType === 'image'" :src="previewUrl" style="max-width: 100%;" fit="contain" />
        <video v-else-if="previewUrl && previewType === 'video'" :src="previewUrl" controls style="max-width: 100%;" />
        <audio v-else-if="previewUrl && previewType === 'voice'" :src="previewUrl" controls style="width: 100%;" />
        <div v-else>暂无预览</div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Plus } from '@element-plus/icons-vue'
import { UploadFilled } from '@element-plus/icons-vue'
import { getWechatAccountList, getWechatMaterialList, uploadWechatMaterial, deleteWechatMaterial, getWechatMaterialStatistics } from '@/api/wechat'

const loading = ref(false)
const uploadLoading = ref(false)
const tableData = ref([])
const accountList = ref([])
const statistics = ref({})

const searchForm = reactive({
  account_id: '',
  type: '',
  is_permanent: ''
})

const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

const uploadVisible = ref(false)
const uploadForm = reactive({
  account_id: '',
  type: 'image',
  title: '',
  introduction: '',
  file: null
})

const previewVisible = ref(false)
const previewUrl = ref('')
const previewType = ref('')

// 加载公众号列表
const loadAccounts = async () => {
  try {
    const res = await getWechatAccountList({ page: 1, limit: 100 })
    accountList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    }
    const res = await getWechatMaterialList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 加载统计数据
const loadStatistics = async () => {
  try {
    const params = {}
    if (searchForm.account_id) params.account_id = searchForm.account_id
    const res = await getWechatMaterialStatistics(params)
    statistics.value = res.data || {}
  } catch (e) {
    console.error(e)
  }
}

// 重置搜索
const resetSearch = () => {
  searchForm.account_id = ''
  searchForm.type = ''
  searchForm.is_permanent = ''
  pagination.page = 1
  loadData()
  loadStatistics()
}

// 上传素材
const handleUpload = () => {
  uploadForm.account_id = searchForm.account_id || (accountList.value.length > 0 ? accountList.value[0].id : '')
  uploadForm.type = 'image'
  uploadForm.title = ''
  uploadForm.introduction = ''
  uploadForm.file = null
  uploadVisible.value = true
}

const handleFileChange = (file) => {
  uploadForm.file = file.raw
}

const handleFileRemove = () => {
  uploadForm.file = null
}

const submitUpload = async () => {
  if (!uploadForm.account_id) {
    ElMessage.warning('请选择公众号')
    return
  }
  if (!uploadForm.file) {
    ElMessage.warning('请选择文件')
    return
  }

  uploadLoading.value = true
  try {
    const formData = new FormData()
    formData.append('account_id', uploadForm.account_id)
    formData.append('type', uploadForm.type)
    formData.append('title', uploadForm.title)
    formData.append('introduction', uploadForm.introduction)
    formData.append('file', uploadForm.file)

    await uploadWechatMaterial(formData)
    ElMessage.success('上传成功')
    uploadVisible.value = false
    loadData()
    loadStatistics()
  } catch (e) {
    console.error(e)
  } finally {
    uploadLoading.value = false
  }
}

// 预览
const handlePreview = (row) => {
  previewUrl.value = row.url
  previewType.value = row.type
  previewVisible.value = true
}

// 复制链接
const handleCopy = (url) => {
  navigator.clipboard.writeText(url)
  ElMessage.success('链接已复制')
}

// 删除
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定删除该素材吗？', '提示', { type: 'warning' })
    await deleteWechatMaterial(row.id)
    ElMessage.success('删除成功')
    loadData()
    loadStatistics()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

// 格式化文件大小
const formatSize = (size) => {
  if (!size || size < 1024) return size + ' B'
  if (size < 1024 * 1024) return (size / 1024).toFixed(2) + ' KB'
  if (size < 1024 * 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB'
  return (size / (1024 * 1024 * 1024)).toFixed(2) + ' GB'
}

onMounted(() => {
  loadAccounts()
  loadData()
  loadStatistics()
})
</script>

<style scoped>
.statistics-row {
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #303133;
}

.stat-label {
  font-size: 14px;
  color: #909399;
  margin-top: 8px;
}

.pagination-container {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}
</style>

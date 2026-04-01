<template>
  <div class="page-container">
    <!-- 统计卡片 -->
    <div class="statistics-cards">
      <el-row :gutter="16">
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-icon" style="background: #409eff;">
              <el-icon size="24"><Document /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ statistics.total_count || 0 }}</div>
              <div class="stat-label">文件总数</div>
            </div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-icon" style="background: #67c23a;">
              <el-icon size="24"><Folder /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ statistics.total_size_format || '0 B' }}</div>
              <div class="stat-label">存储用量</div>
            </div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-icon" style="background: #e6a23c;">
              <el-icon size="24"><Picture /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ statistics.image_count || 0 }}</div>
              <div class="stat-label">图片数量</div>
            </div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-icon" style="background: #f56c6c;">
              <el-icon size="24"><VideoCamera /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-value">{{ statistics.video_count || 0 }}</div>
              <div class="stat-label">视频数量</div>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>

    <!-- 上传区域 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">文件上传</span>
        <div class="storage-info">
          <span>存储方式: </span>
          <el-tag :type="storageTagType" size="small">{{ storageText }}</el-tag>
        </div>
      </div>
      
      <div class="upload-section">
        <el-tabs v-model="uploadType" class="upload-tabs">
          <el-tab-pane label="图片上传" name="image">
            <el-upload
              ref="imageUploadRef"
              class="upload-area"
              drag
              :action="uploadUrl"
              :headers="uploadHeaders"
              :before-upload="(file) => handleBeforeUpload(file, 'image')"
              :on-success="handleUploadSuccess"
              :on-error="handleUploadError"
              :on-remove="handleRemove"
              :file-list="fileList"
              :accept="imageAccept"
              :show-file-list="false"
            >
              <div class="upload-content">
                <el-icon class="upload-icon"><UploadFilled /></el-icon>
                <div class="upload-text">将图片拖到此处，或<em>点击上传</em></div>
                <div class="upload-tip">
                  支持 {{ allowedImageExt.join('、') }} 格式，单个文件不超过 {{ imageMaxSizeFormat }}
                </div>
              </div>
            </el-upload>
          </el-tab-pane>
          
          <el-tab-pane label="文件上传" name="file">
            <el-upload
              ref="fileUploadRef"
              class="upload-area"
              drag
              :action="uploadUrl"
              :headers="uploadHeaders"
              :before-upload="(file) => handleBeforeUpload(file, 'file')"
              :on-success="handleUploadSuccess"
              :on-error="handleUploadError"
              :on-remove="handleRemove"
              :file-list="fileList"
              :show-file-list="false"
            >
              <div class="upload-content">
                <el-icon class="upload-icon"><UploadFilled /></el-icon>
                <div class="upload-text">将文件拖到此处，或<em>点击上传</em></div>
                <div class="upload-tip">
                  支持多种格式，单个文件不超过 {{ fileMaxSizeFormat }}
                </div>
              </div>
            </el-upload>
          </el-tab-pane>
          
          <el-tab-pane label="视频上传" name="video">
            <el-upload
              ref="videoUploadRef"
              class="upload-area"
              drag
              :action="uploadUrl"
              :headers="uploadHeaders"
              :before-upload="(file) => handleBeforeUpload(file, 'video')"
              :on-success="handleUploadSuccess"
              :on-error="handleUploadError"
              :on-remove="handleRemove"
              :file-list="fileList"
              :accept="videoAccept"
              :show-file-list="false"
            >
              <div class="upload-content">
                <el-icon class="upload-icon"><UploadFilled /></el-icon>
                <div class="upload-text">将视频拖到此处，或<em>点击上传</em></div>
                <div class="upload-tip">
                  支持 {{ allowedVideoExt.join('、') }} 格式，单个文件不超过 {{ fileMaxSizeFormat }}
                </div>
              </div>
            </el-upload>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>

    <!-- 文件列表 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">文件列表</span>
        <div class="header-actions">
          <el-select v-model="listParams.type" placeholder="文件类型" clearable style="width: 120px" @change="loadData">
            <el-option label="图片" value="image" />
            <el-option label="视频" value="video" />
            <el-option label="音频" value="audio" />
            <el-option label="文件" value="file" />
          </el-select>
          <el-select v-model="listParams.storage" placeholder="存储方式" clearable style="width: 120px" @change="loadData">
            <el-option label="本地" value="local" />
            <el-option label="阿里云OSS" value="oss" />
            <el-option label="腾讯云COS" value="cos" />
            <el-option label="七牛云" value="qiniu" />
          </el-select>
          <el-input
            v-model="listParams.keyword"
            placeholder="搜索文件名"
            clearable
            style="width: 200px"
            @keyup.enter="loadData"
          >
            <template #prefix><el-icon><Search /></el-icon></template>
          </el-input>
          <el-button type="primary" @click="loadData"><el-icon><Search /></el-icon>搜索</el-button>
          <el-button @click="resetSearch">重置</el-button>
          <el-button type="danger" @click="handleBatchDelete" :disabled="selectedIds.length === 0">
            批量删除 ({{ selectedIds.length }})
          </el-button>
        </div>
      </div>

      <el-table 
        :data="tableData" 
        v-loading="loading" 
        @selection-change="handleSelectionChange"
        :default-sort="{ prop: 'create_time', order: 'descending' }"
      >
        <el-table-column type="selection" width="50" />
        <el-table-column label="预览" width="80" align="center">
          <template #default="{ row }">
            <div v-if="row.type === 'image'" class="file-preview-cell" @click="handlePreview(row)">
              <el-image
                :src="row.url"
                fit="cover"
                style="width: 50px; height: 50px; border-radius: 4px; cursor: pointer;"
              />
            </div>
            <div v-else-if="row.type === 'video'" class="file-type-icon video" @click="handlePreview(row)">
              <el-icon size="32"><VideoCamera /></el-icon>
            </div>
            <div v-else class="file-type-icon" @click="handlePreview(row)">
              <el-icon size="32"><Document /></el-icon>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="original" label="文件名" min-width="200" show-overflow-tooltip>
          <template #default="{ row }">
            <span class="file-name" @click="handlePreview(row)">{{ row.original }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="type_text" label="类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="getTypeTagType(row.type)" size="small">{{ row.type_text }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="size_format" label="大小" width="100" align="center" />
        <el-table-column prop="storage_text" label="存储" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="getStorageTagType(row.storage)" size="small">{{ row.storage_text }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="上传时间" width="180" sortable />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleCopyLink(row)">复制链接</el-button>
            <el-button link type="primary" @click="handlePreview(row)">预览</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          background
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </div>

    <!-- 文件预览组件 -->
    <FilePreview 
      v-model="previewVisible" 
      :file="currentFile"
      @download="handleFileDownload"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  UploadFilled, Search, Document, Picture, VideoCamera, Folder 
} from '@element-plus/icons-vue'
import { 
  getUploadLists, deleteUpload, batchDeleteUpload, 
  getUploadConfig, getUploadStatistics, formatFileSize 
} from '@/api/upload'
import FilePreview from '@/components/FilePreview/index.vue'

const loading = ref(false)
const tableData = ref([])
const fileList = ref([])
const selectedIds = ref([])
const previewVisible = ref(false)
const currentFile = ref({})
const statistics = ref({})
const uploadConfig = ref({})

// 上传类型
const uploadType = ref('image')

// 上传 refs
const imageUploadRef = ref(null)
const fileUploadRef = ref(null)
const videoUploadRef = ref(null)

// 列表查询参数
const listParams = reactive({
  keyword: '',
  type: '',
  storage: ''
})

// 分页
const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

// 计算属性
const uploadUrl = computed(() => import.meta.env.VITE_API_URL + '/upload/' + uploadType.value)
const uploadHeaders = computed(() => ({
  Authorization: 'Bearer ' + localStorage.getItem('token')
}))

// 文件格式限制
const allowedImageExt = computed(() => {
  return (uploadConfig.value.file_image_ext || 'jpg,jpeg,png,gif,bmp,webp,svg,ico').split(',')
})
const allowedVideoExt = computed(() => {
  return (uploadConfig.value.file_video_ext || 'mp4,avi,mov,wmv,flv,mkv,webm').split(',')
})
const imageAccept = computed(() => '.' + allowedImageExt.value.join(',.'))
const videoAccept = computed(() => '.' + allowedVideoExt.value.join(',.'))
const imageMaxSizeFormat = computed(() => formatFileSize(uploadConfig.value.file_image_max_size || 5242880))
const fileMaxSizeFormat = computed(() => formatFileSize(uploadConfig.value.file_max_size || 52428800))

// 存储方式
const storageText = computed(() => {
  const texts = {
    local: '本地存储',
    oss: '阿里云OSS',
    cos: '腾讯云COS',
    qiniu: '七牛云'
  }
  return texts[uploadConfig.value.storage_type] || '本地存储'
})

const storageTagType = computed(() => {
  const types = {
    local: 'info',
    oss: 'success',
    cos: 'warning',
    qiniu: ''
  }
  return types[uploadConfig.value.storage_type] || 'info'
})

// 文件类型标签样式
const getTypeTagType = (type) => {
  const types = {
    image: 'success',
    video: 'danger',
    audio: 'warning',
    file: 'info'
  }
  return types[type] || 'info'
}

// 存储方式标签样式
const getStorageTagType = (storage) => {
  const types = {
    local: 'info',
    oss: 'success',
    cos: 'warning',
    qiniu: ''
  }
  return types[storage] || 'info'
}

// 上传前验证
const handleBeforeUpload = (file, type) => {
  const extension = file.name.split('.').pop()?.toLowerCase()
  const maxSize = type === 'image' 
    ? (uploadConfig.value.file_image_max_size || 5242880)
    : (uploadConfig.value.file_max_size || 52428800)
  
  // 验证扩展名
  let allowedExt = type === 'image' ? allowedImageExt.value : allowedVideoExt.value
  if (type === 'file') {
    allowedExt = (uploadConfig.value.file_ext || '').split(',')
  }
  
  if (!allowedExt.includes(extension)) {
    ElMessage.error(`不允许的文件格式: .${extension}`)
    return false
  }
  
  // 验证大小
  if (file.size > maxSize) {
    ElMessage.error(`文件大小超过限制，最大允许: ${formatFileSize(maxSize)}`)
    return false
  }
  
  return true
}

// 上传成功
const handleUploadSuccess = (res) => {
  if (res.code === 200) {
    ElMessage.success('上传成功')
    loadData()
    loadStatistics()
  } else {
    ElMessage.error(res.msg || '上传失败')
  }
}

// 上传失败
const handleUploadError = () => {
  ElMessage.error('上传失败，请重试')
}

// 移除文件
const handleRemove = (file, fileList) => {
  // 文件从上传组件移除
}

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const res = await getUploadLists({
      page: pagination.page,
      page_size: pagination.pageSize,
      keyword: listParams.keyword,
      type: listParams.type,
      storage: listParams.storage
    })
    tableData.value = res.list || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 加载配置
const loadConfig = async () => {
  try {
    const res = await getUploadConfig()
    uploadConfig.value = res
  } catch (e) {
    console.error(e)
  }
}

// 加载统计
const loadStatistics = async () => {
  try {
    const res = await getUploadStatistics()
    statistics.value = res
  } catch (e) {
    console.error(e)
  }
}

// 重置搜索
const resetSearch = () => {
  listParams.keyword = ''
  listParams.type = ''
  listParams.storage = ''
  pagination.page = 1
  loadData()
}

// 表格选择
const handleSelectionChange = (selection) => {
  selectedIds.value = selection.map(item => item.id)
}

// 预览文件
const handlePreview = (row) => {
  currentFile.value = row
  previewVisible.value = true
}

// 复制链接
const handleCopyLink = (row) => {
  navigator.clipboard.writeText(row.url).then(() => {
    ElMessage.success('链接已复制')
  }).catch(() => {
    ElMessage.error('复制失败')
  })
}

// 删除单个文件
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除文件"${row.original}"吗？`, '提示', { type: 'warning' })
    await deleteUpload(row.id)
    ElMessage.success('删除成功')
    loadData()
    loadStatistics()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

// 批量删除
const handleBatchDelete = async () => {
  try {
    await ElMessageBox.confirm(`确定删除选中的 ${selectedIds.value.length} 个文件吗？`, '提示', { type: 'warning' })
    await batchDeleteUpload(selectedIds.value)
    ElMessage.success('批量删除成功')
    selectedIds.value = []
    loadData()
    loadStatistics()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('批量删除失败')
  }
}

// 文件下载
const handleFileDownload = ({ url, name }) => {
  const link = document.createElement('a')
  link.href = url
  link.download = name
  link.click()
}

// 页面加载
onMounted(() => {
  loadData()
  loadConfig()
  loadStatistics()
})
</script>

<style scoped>
.page-container { padding: 0; }

.statistics-cards {
  margin-bottom: 16px;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 16px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 8px;
  color: #fff;
  margin-right: 16px;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 20px;
  font-weight: 600;
  color: #303133;
}

.stat-label {
  font-size: 13px;
  color: #909399;
  margin-top: 4px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.card-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}

.storage-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #606266;
}

.header-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.upload-section {
  padding: 24px;
}

.upload-tabs {
  width: 100%;
}

.upload-area {
  width: 100%;
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

:deep(.el-upload-dragger) {
  width: 100%;
  padding: 40px 20px;
  border: 2px dashed #dcdfe6;
  border-radius: 8px;
  background: #fafafa;
  transition: all 0.3s;
}

:deep(.el-upload-dragger:hover) {
  border-color: #409eff;
  background: #f0f7ff;
}

.upload-icon {
  font-size: 48px;
  color: #409eff;
  margin-bottom: 12px;
}

.upload-text {
  font-size: 14px;
  color: #606266;
}

.upload-text em {
  color: #409eff;
  font-style: normal;
}

.upload-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 8px;
}

.file-preview-cell {
  cursor: pointer;
}

.file-type-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  background: #f5f7fa;
  border-radius: 4px;
  color: #909399;
  cursor: pointer;
}

.file-type-icon.video {
  background: #fdf6ec;
  color: #e6a23c;
}

.file-name {
  color: #409eff;
  cursor: pointer;
}

.file-name:hover {
  text-decoration: underline;
}

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 12px 16px;
  border-top: 1px solid #f0f0f0;
}
</style>

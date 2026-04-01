<template>
  <el-dialog
    v-model="visible"
    :title="previewTitle"
    width="900px"
    :top="'5vh'"
    :destroy-on-close="true"
    class="file-preview-dialog"
  >
    <!-- 图片预览 -->
    <div v-if="previewType === 'image'" class="preview-content image-preview">
      <img 
        ref="imageRef"
        :src="fileUrl" 
        :style="imageStyle"
        @wheel="handleImageWheel"
        class="preview-image"
      />
      <div class="image-toolbar">
        <el-button @click="zoomIn" :icon="ZoomIn" size="small">放大</el-button>
        <el-button @click="zoomOut" :icon="ZoomOut" size="small">缩小</el-button>
        <el-button @click="resetImage" :icon="Refresh" size="small">重置</el-button>
        <span class="zoom-info">{{ Math.round(zoom * 100) }}%</span>
      </div>
    </div>

    <!-- 视频预览 -->
    <div v-else-if="previewType === 'video'" class="preview-content video-preview">
      <video 
        ref="videoRef"
        :src="fileUrl" 
        controls 
        autoplay
        class="preview-video"
      >
        您的浏览器不支持视频播放
      </video>
    </div>

    <!-- PDF预览 -->
    <div v-else-if="previewType === 'pdf'" class="preview-content pdf-preview">
      <iframe 
        :src="pdfUrl" 
        class="preview-pdf"
        frameborder="0"
      ></iframe>
    </div>

    <!-- Office文档预览 -->
    <div v-else-if="previewType === 'office'" class="preview-content office-preview">
      <iframe 
        :src="officeUrl" 
        class="preview-office"
        frameborder="0"
      ></iframe>
    </div>

    <!-- 音频预览 -->
    <div v-else-if="previewType === 'audio'" class="preview-content audio-preview">
      <audio 
        ref="audioRef"
        :src="fileUrl" 
        controls 
        autoplay
        class="preview-audio"
      >
        您的浏览器不支持音频播放
      </audio>
    </div>

    <!-- 其他文件 -->
    <div v-else class="preview-content file-preview">
      <el-result
        icon="info"
        title="该文件类型暂不支持预览"
        :sub-title="`文件名: ${fileName}`"
      >
        <template #extra>
          <el-button type="primary" @click="downloadFile">下载文件</el-button>
        </template>
      </el-result>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="visible = false">关闭</el-button>
        <el-button type="primary" @click="downloadFile">下载</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { ZoomIn, ZoomOut, Refresh } from '@element-plus/icons-vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  file: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue', 'download'])

const visible = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

// 文件信息
const fileId = ref(0)
const fileName = ref('')
const fileUrl = ref('')
const fileType = ref('')
const previewType = ref('')
const extension = ref('')

// 图片缩放
const zoom = ref(1)
const imageRef = ref(null)
const imageStyle = computed(() => ({
  transform: `scale(${zoom.value})`,
  transition: 'transform 0.2s ease'
}))

// 视频/音频ref
const videoRef = ref(null)
const audioRef = ref(null)

// 计算预览标题
const previewTitle = computed(() => {
  const titles = {
    image: '图片预览',
    video: '视频预览',
    audio: '音频预览',
    pdf: 'PDF预览',
    office: '文档预览',
    file: '文件信息'
  }
  return titles[previewType.value] || '文件预览'
})

// PDF预览URL (使用PDF.js)
const pdfUrl = computed(() => {
  if (previewType.value === 'pdf') {
    return `/static/pdfjs/web/viewer.html?file=${encodeURIComponent(fileUrl.value)}`
  }
  return ''
})

// Office文档预览URL (使用微软Office Online)
const officeUrl = computed(() => {
  if (previewType.value === 'office') {
    const officeTypes = {
      doc: 'Word',
      docx: 'Word',
      xls: 'Excel',
      xlsx: 'Excel',
      ppt: 'PowerPoint',
      pptx: 'PowerPoint'
    }
    const app = officeTypes[extension.value] || 'Word'
    return `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl.value)}`
  }
  return ''
})

// 监听file prop变化
watch(() => props.file, (newFile) => {
  if (newFile && newFile.id) {
    fileId.value = newFile.id
    fileName.value = newFile.original || newFile.name
    fileUrl.value = newFile.url
    fileType.value = newFile.type
    extension.value = newFile.extension || ''
    previewType.value = getPreviewType(newFile)
    zoom.value = 1
  }
}, { immediate: true })

// 获取预览类型
const getPreviewType = (file) => {
  if (file.type === 'image') return 'image'
  if (file.type === 'video') return 'video'
  if (file.type === 'audio') return 'audio'
  if (file.extension === 'pdf') return 'pdf'
  if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(file.extension)) return 'office'
  return 'file'
}

// 图片缩放
const zoomIn = () => {
  zoom.value = Math.min(zoom.value + 0.25, 5)
}

const zoomOut = () => {
  zoom.value = Math.max(zoom.value - 0.25, 0.25)
}

const resetImage = () => {
  zoom.value = 1
}

const handleImageWheel = (e) => {
  if (e.deltaY < 0) {
    zoomIn()
  } else {
    zoomOut()
  }
  e.preventDefault()
}

// 下载文件
const downloadFile = () => {
  if (fileUrl.value) {
    const link = document.createElement('a')
    link.href = fileUrl.value
    link.download = fileName.value
    link.click()
  }
  emit('download', { id: fileId.value, url: fileUrl.value, name: fileName.value })
}
</script>

<style scoped>
.preview-content {
  min-height: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.image-preview {
  position: relative;
}

.preview-image {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
  cursor: grab;
}

.preview-image:active {
  cursor: grabbing;
}

.image-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding: 8px 16px;
  background: #f5f7fa;
  border-radius: 4px;
}

.zoom-info {
  margin-left: 16px;
  font-size: 14px;
  color: #606266;
}

.video-preview {
  width: 100%;
}

.preview-video {
  width: 100%;
  max-height: 70vh;
  background: #000;
}

.audio-preview {
  padding: 40px;
}

.preview-audio {
  width: 100%;
  max-width: 600px;
}

.pdf-preview {
  width: 100%;
  height: 70vh;
}

.preview-pdf {
  width: 100%;
  height: 100%;
}

.office-preview {
  width: 100%;
  height: 70vh;
}

.preview-office {
  width: 100%;
  height: 100%;
}

.file-preview {
  padding: 40px;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

:deep(.el-dialog__body) {
  padding: 20px;
  overflow: auto;
}
</style>

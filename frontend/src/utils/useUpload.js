/**
 * 统一文件上传 - 组合式函数 (Composable)
 * 
 * 使用方式：
 * import { useUpload } from '@/utils/useUpload'
 * 
 * const { uploadImage, uploadFile, uploadVideo, uploadUrl } = useUpload()
 */
import { ref, computed } from 'vue'
import { ElMessage } from 'element-plus'
import request from '@/utils/request'

// 文件类型映射
const FILE_TYPES = {
  image: '图片',
  file: '文件',
  video: '视频'
}

// 文件大小限制（字节）
const DEFAULT_MAX_SIZE = {
  image: 5 * 1024 * 1024,  // 5MB
  file: 50 * 1024 * 1024,  // 50MB
  video: 100 * 1024 * 1024 // 100MB
}

// 允许的扩展名
const DEFAULT_EXTENSIONS = {
  image: ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'],
  file: [],
  video: ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm']
}

/**
 * 统一上传 Hook
 * @param {Object} options 配置选项
 * @param {string} options.baseUrl API基础路径，默认从环境变量获取
 * @param {Function} options.onSuccess 上传成功回调
 * @param {Function} options.onError 上传失败回调
 */
export function useUpload(options = {}) {
  const { baseUrl = import.meta.env.VITE_API_BASE_URL || '/adminapi' } = options

  const uploading = ref(false)
  const uploadProgress = ref(0)

  // 动态上传URL
  const uploadUrl = computed(() => baseUrl)

  /**
   * 上传文件
   * @param {File} file 文件对象
   * @param {string} type 文件类型：image/file/video
   * @returns {Promise<Object>} 上传结果
   */
  const upload = async (file, type = 'image') => {
    if (!file) {
      throw new Error('请选择要上传的文件')
    }

    // 验证文件类型
    const ext = file.name.split('.').pop()?.toLowerCase()
    const allowedExts = DEFAULT_EXTENSIONS[type] || []
    if (allowedExts.length > 0 && !allowedExts.includes(ext)) {
      throw new Error(`不支持 ${ext} 格式，请上传 ${allowedExts.join('、')} 格式的文件`)
    }

    // 验证文件大小
    const maxSize = DEFAULT_MAX_SIZE[type] || DEFAULT_MAX_SIZE.file
    if (file.size > maxSize) {
      throw new Error(`文件大小不能超过 ${formatFileSize(maxSize)}`)
    }

    uploading.value = true
    uploadProgress.value = 0

    try {
      const formData = new FormData()
      formData.append('file', file)

      const response = await request.post(`/upload/${type}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          if (progressEvent.total) {
            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          }
        }
      })

      if (response.code === 0 || response.code === 200) {
        const url = response.data?.url || response.url || response.data
        options.onSuccess?.(url, response.data, response)
        return { success: true, url, data: response.data }
      } else {
        throw new Error(response.msg || '上传失败')
      }
    } catch (error) {
      const errorMsg = error.message || '上传失败'
      options.onError?.(errorMsg, error)
      throw error
    } finally {
      uploading.value = false
    }
  }

  /**
   * 上传图片
   * @param {File} file 文件对象
   * @returns {Promise<Object>} 上传结果
   */
  const uploadImage = (file) => upload(file, 'image')

  /**
   * 上传普通文件
   * @param {File} file 文件对象
   * @returns {Promise<Object>} 上传结果
   */
  const uploadFile = (file) => upload(file, 'file')

  /**
   * 上传视频
   * @param {File} file 文件对象
   * @returns {Promise<Object>} 上传结果
   */
  const uploadVideo = (file) => upload(file, 'video')

  /**
   * 验证文件（不上传）
   * @param {File} file 文件对象
   * @param {string} type 文件类型
   * @returns {Object} 验证结果
   */
  const validateFile = (file, type = 'image') => {
    const errors = []

    // 检查扩展名
    const ext = file.name.split('.').pop()?.toLowerCase()
    const allowedExts = DEFAULT_EXTENSIONS[type] || []
    if (allowedExts.length > 0 && !allowedExts.includes(ext)) {
      errors.push(`不支持 ${ext} 格式`)
    }

    // 检查大小
    const maxSize = DEFAULT_MAX_SIZE[type] || DEFAULT_MAX_SIZE.file
    if (file.size > maxSize) {
      errors.push(`文件大小不能超过 ${formatFileSize(maxSize)}`)
    }

    return {
      valid: errors.length === 0,
      errors
    }
  }

  return {
    uploading,
    uploadProgress,
    uploadUrl,
    upload,
    uploadImage,
    uploadFile,
    uploadVideo,
    validateFile
  }
}

/**
 * 格式化文件大小
 */
export function formatFileSize(size) {
  if (!size) return '0 B'
  if (size < 1024) return size + ' B'
  if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB'
  if (size < 1024 * 1024 * 1024) return (size / (1024 * 1024)).toFixed(1) + ' MB'
  return (size / (1024 * 1024 * 1024)).toFixed(1) + ' GB'
}

/**
 * 获取文件扩展名
 */
export function getFileExtension(filename) {
  return filename.split('.').pop()?.toLowerCase() || ''
}

/**
 * 获取文件类型（根据扩展名）
 */
export function getFileType(extension) {
  const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico']
  const videoExts = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm']
  const audioExts = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma']
  const docExts = ['doc', 'docx']
  const excelExts = ['xls', 'xlsx']
  const pptExts = ['ppt', 'pptx']
  const pdfExts = ['pdf']
  const zipExts = ['zip', 'rar', '7z', 'tar', 'gz']

  const ext = (extension || '').toLowerCase()

  if (imageExts.includes(ext)) return 'image'
  if (videoExts.includes(ext)) return 'video'
  if (audioExts.includes(ext)) return 'audio'
  if (docExts.includes(ext)) return 'document'
  if (excelExts.includes(ext)) return 'spreadsheet'
  if (pptExts.includes(ext)) return 'presentation'
  if (pdfExts.includes(ext)) return 'pdf'
  if (zipExts.includes(ext)) return 'archive'

  return 'file'
}

/**
 * 判断是否为图片
 */
export function isImage(extension) {
  return getFileType(extension) === 'image'
}

/**
 * 判断是否为视频
 */
export function isVideo(extension) {
  return getFileType(extension) === 'video'
}

// 导出默认的 upload 函数（方便直接调用）
export default useUpload

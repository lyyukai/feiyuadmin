import request from '@/utils/request'

/**
 * 文件上传 V2 API
 */

// 上传图片
export const uploadImage = (formData) => request.post('/upload/image', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
})

// 上传文件
export const uploadFile = (formData) => request.post('/upload/file', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
})

// 上传视频
export const uploadVideo = (formData) => request.post('/upload/video', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
})

// 获取文件列表
export const getUploadLists = (params) => request.get('/upload/lists', { params })

// 删除文件
export const deleteUpload = (id) => request.post('/upload/delete', { id })

// 批量删除
export const batchDeleteUpload = (ids) => request.post('/upload/batchDelete', { ids })

// 获取文件详情
export const getUploadDetail = (id) => request.get('/upload/detail', { params: { id } })

// 获取存储配置
export const getUploadConfig = () => request.get('/upload/config')

// 获取统计信息
export const getUploadStatistics = () => request.get('/upload/statistics')

// 清除配置缓存
export const clearUploadCache = () => request.post('/upload/clearCache')

// 格式化文件大小
export const formatFileSize = (size) => {
  if (!size) return '-'
  if (size < 1024) return size + ' B'
  if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB'
  if (size < 1024 * 1024 * 1024) return (size / (1024 * 1024)).toFixed(1) + ' MB'
  return (size / (1024 * 1024 * 1024)).toFixed(1) + ' GB'
}

// 获取文件类型图标
export const getFileTypeIcon = (extension) => {
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
  if (docExts.includes(ext)) return 'word'
  if (excelExts.includes(ext)) return 'excel'
  if (pptExts.includes(ext)) return 'ppt'
  if (pdfExts.includes(ext)) return 'pdf'
  if (zipExts.includes(ext)) return 'zip'
  
  return 'file'
}

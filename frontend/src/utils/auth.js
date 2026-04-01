/**
 * 存储工具
 */
const storagePrefix = import.meta.env.VITE_APP_PREFIX || 'feiyu_'

export const storage = {
  // 设置
  set(key, value) {
    const data = JSON.stringify(value)
    localStorage.setItem(storagePrefix + key, data)
  },
  
  // 获取
  get(key, defaultValue = null) {
    const data = localStorage.getItem(storagePrefix + key)
    if (data === null) return defaultValue
    try {
      return JSON.parse(data)
    } catch {
      return data
    }
  },
  
  // 删除
  remove(key) {
    localStorage.removeItem(storagePrefix + key)
  },
  
  // 清空
  clear() {
    Object.keys(localStorage)
      .filter(key => key.startsWith(storagePrefix))
      .forEach(key => localStorage.removeItem(key))
  }
}

/**
 * Token 管理
 */
export const token = {
  // 设置 Token
  set(token) {
    storage.set('token', token)
  },
  
  // 获取 Token
  get() {
    return storage.get('token', '')
  },
  
  // 移除 Token
  remove() {
    storage.remove('token')
  }
}

/**
 * 格式化工具函数
 */

// 格式化日期
export const formatDate = (date, format = 'YYYY-MM-DD HH:mm:ss') => {
  if (!date) return ''
  const d = new Date(date)
  
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  const seconds = String(d.getSeconds()).padStart(2, '0')
  
  return format
    .replace('YYYY', year)
    .replace('MM', month)
    .replace('DD', day)
    .replace('HH', hours)
    .replace('mm', minutes)
    .replace('ss', seconds)
}

// 格式化文件大小
export const formatFileSize = (size) => {
  if (!size) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB', 'TB']
  let index = 0
  while (size >= 1024 && index < units.length - 1) {
    size /= 1024
    index++
  }
  return size.toFixed(2) + ' ' + units[index]
}

// 格式化数字
export const formatNumber = (num, decimals = 0) => {
  if (!num) return '0'
  return Number(num).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

// 防抖
export const debounce = (fn, delay = 300) => {
  let timer = null
  return function (...args) {
    if (timer) clearTimeout(timer)
    timer = setTimeout(() => {
      fn.apply(this, args)
    }, delay)
  }
}

// 节流
export const throttle = (fn, delay = 300) => {
  let lastTime = 0
  return function (...args) {
    const now = Date.now()
    if (now - lastTime >= delay) {
      lastTime = now
      fn.apply(this, args)
    }
  }
}

// 深拷贝
export const deepClone = (obj) => {
  if (obj === null || typeof obj !== 'object') return obj
  const clone = Array.isArray(obj) ? [] : {}
  for (const key in obj) {
    if (Object.prototype.hasOwnProperty.call(obj, key)) {
      clone[key] = deepClone(obj[key])
    }
  }
  return clone
}

// 生成随机字符串
export const randomString = (length = 32) => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  let result = ''
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  return result
}

// 验证工具
export const validate = {
  // 手机号
  mobile(value) {
    return /^1[3-9]\d{9}$/.test(value)
  },
  // 邮箱
  email(value) {
    return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value)
  },
  // 身份证
  idCard(value) {
    return /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(value)
  },
  // URL
  url(value) {
    return /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_+.~#?&//=]*)$/.test(value)
  }
}

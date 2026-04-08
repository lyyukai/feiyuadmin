/**
 * Responsive Utility Functions
 * H5 Mobile-First Design for FeiyuAdmin v3
 */

/**
 * Breakpoint definitions (must match CSS variables.css)
 * breakpoints: ['mobile', 'tablet', 'desktop', 'wide']
 */
export const BREAKPOINTS = {
  mobile: 768,   // < 768px
  tablet: 1024,  // < 1024px
  desktop: 1440, // < 1440px
  // wide: ≥ 1440px
}

/**
 * Current breakpoint key
 * @returns {'mobile' | 'tablet' | 'desktop' | 'wide'}
 */
export function getCurrentBreakpoint() {
  const width = window.innerWidth
  if (width < BREAKPOINTS.mobile) return 'mobile'
  if (width < BREAKPOINTS.tablet) return 'tablet'
  if (width < BREAKPOINTS.desktop) return 'desktop'
  return 'wide'
}

/**
 * Check if current viewport is mobile
 * @returns {boolean}
 */
export function isMobile() {
  return window.innerWidth < BREAKPOINTS.mobile
}

/**
 * Check if current viewport is tablet
 * @returns {boolean}
 */
export function isTablet() {
  const w = window.innerWidth
  return w >= BREAKPOINTS.mobile && w < BREAKPOINTS.tablet
}

/**
 * Check if current viewport is desktop
 * @returns {boolean}
 */
export function isDesktop() {
  const w = window.innerWidth
  return w >= BREAKPOINTS.tablet && w < BREAKPOINTS.desktop
}

/**
 * Check if current viewport is wide
 * @returns {boolean}
 */
export function isWide() {
  return window.innerWidth >= BREAKPOINTS.desktop
}

/**
 * Check if current viewport is mobile or tablet
 * @returns {boolean}
 */
export function isMobileOrTablet() {
  return window.innerWidth < BREAKPOINTS.tablet
}

/**
 * Get viewport width
 * @returns {number}
 */
export function getViewportWidth() {
  return window.innerWidth
}

/**
 * Get viewport height
 * @returns {number}
 */
export function getViewportHeight() {
  return window.innerHeight
}

/**
 * Debounce utility for resize events
 * @param {Function} fn
 * @param {number} delay ms
 * @returns {Function}
 */
export function debounce(fn, delay = 250) {
  let timer = null
  return function (...args) {
    if (timer) clearTimeout(timer)
    timer = setTimeout(() => fn.apply(this, args), delay)
  }
}

/**
 * Throttle utility for scroll/resize events
 * @param {Function} fn
 * @param {number} limit ms
 * @returns {Function}
 */
export function throttle(fn, limit = 100) {
  let inThrottle = false
  return function (...args) {
    if (!inThrottle) {
      fn.apply(this, args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}

/**
 * Reactive breakpoint — call callback on breakpoint change
 * Returns a cleanup function
 * @param {Function} callback (breakpoint) => void
 * @returns {Function} unsubscribe
 */
export function onBreakpointChange(callback) {
  let current = getCurrentBreakpoint()

  const handler = () => {
    const next = getCurrentBreakpoint()
    if (next !== current) {
      current = next
      callback(current)
    }
  }

  window.addEventListener('resize', handler, { passive: true })
  return () => window.removeEventListener('resize', handler)
}

/**
 * Clamp a value between min and max
 * @param {number} value
 * @param {number} min
 * @param {number} max
 * @returns {number}
 */
export function clamp(value, min, max) {
  return Math.min(Math.max(value, min), max)
}

/**
 * Format file size for display
 * @param {number} bytes
 * @returns {string}
 */
export function formatFileSize(bytes) {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return `${(bytes / Math.pow(k, i)).toFixed(1)} ${sizes[i]}`
}

/**
 * Safe JSON parse
 * @param {string} str
 * @param {*} fallback
 * @returns {*}
 */
export function safeJsonParse(str, fallback = null) {
  try {
    return JSON.parse(str)
  } catch {
    return fallback
  }
}

/**
 * Get CSS custom property value
 * @param {string} name
 * @param {string} fallback
 * @returns {string}
 */
export function getCssVar(name, fallback = '') {
  return getComputedStyle(document.documentElement)
    .getPropertyValue(name)
    .trim() || fallback
}

/**
 * Set CSS custom property value
 * @param {string} name
 * @param {string} value
 */
export function setCssVar(name, value) {
  document.documentElement.style.setProperty(name, value)
}

/**
 * Check if device supports touch
 * @returns {boolean}
 */
export function isTouchDevice() {
  return (
    'ontouchstart' in window ||
    navigator.maxTouchPoints > 0
  )
}

/**
 * Scroll to element smoothly
 * @param {string|Element} target
 * @param {number} offsetTop
 */
export function scrollToElement(target, offsetTop = 0) {
  const el = typeof target === 'string' ? document.querySelector(target) : target
  if (!el) return
  const top = el.getBoundingClientRect().top + window.pageYOffset - offsetTop
  window.scrollTo({ top, behavior: 'smooth' })
}

export default {
  BREAKPOINTS,
  getCurrentBreakpoint,
  isMobile,
  isTablet,
  isDesktop,
  isWide,
  isMobileOrTablet,
  getViewportWidth,
  getViewportHeight,
  debounce,
  throttle,
  onBreakpointChange,
  clamp,
  formatFileSize,
  safeJsonParse,
  getCssVar,
  setCssVar,
  isTouchDevice,
  scrollToElement,
}

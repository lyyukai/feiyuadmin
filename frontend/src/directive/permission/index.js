/**
 * v-permission 指令
 * 用法：v-permission="['system:user:add']"
 * 带有权限数组时，任一权限满足即可
 */
import { useUserStore } from '@/stores/user'

function checkPermission(el, binding) {
  const userStore = useUserStore()
  const value = binding.value

  if (!value) return

  const permissions = Array.isArray(value) ? value : [value]

  const hasPermission = permissions.some(permission => {
    return userStore.hasPermission(permission)
  })

  if (!hasPermission) {
    el.parentNode?.removeChild(el)
  }
}

export default {
  mounted(el, binding) {
    checkPermission(el, binding)
  },
  updated(el, binding) {
    checkPermission(el, binding)
  }
}

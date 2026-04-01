import { defineStore } from 'pinia'
import { getUserInfo, getMenuNav, logout as logoutApi, login as loginApi } from '@/api'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: localStorage.getItem('token') || '',
    userInfo: {},
    menus: [],
    isLoggedIn: false
  }),

  getters: {
    avatar: (state) => state.userInfo?.avatar || '',
    nickname: (state) => state.userInfo?.nickname || state.userInfo?.username || '',
  },

  actions: {
    // 登录
    async login(loginForm) {
      try {
        const res = await loginApi(loginForm)
        const token = res.data?.token
        if (token) {
          this.token = token
          this.isLoggedIn = true
          localStorage.setItem('token', token)
          return true
        }
        return false
      } catch (error) {
        throw error
      }
    },

    // 退出
    async logout() {
      try {
        await logoutApi()
      } catch {}
      this.token = ''
      this.userInfo = {}
      this.menus = []
      this.isLoggedIn = false
      localStorage.removeItem('token')
    },

    // 获取用户信息
    async getUserInfo() {
      if (!this.token) return
      try {
        const res = await getUserInfo()
        this.userInfo = res.data || {}
        this.isLoggedIn = true
      } catch {
        this.logout()
      }
    },

    // 获取菜单
    async getMenus() {
      if (!this.token) return
      try {
        const res = await getMenuNav()
        this.menus = res.data || []
      } catch {
        this.menus = []
      }
    }
  }
})

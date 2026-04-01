import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  base: '/admin/',
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  server: {
    port: 5173,
    host: '0.0.0.0',
    proxy: {
      '/api': {
        target: 'http://localhost:8088',
        changeOrigin: true
      },
      '/adminapi': {
        target: 'http://localhost:8088',
        changeOrigin: true
      },
      '/pcapi': {
        target: 'http://localhost:8088',
        changeOrigin: true
      },
      '/mobileapi': {
        target: 'http://localhost:8088',
        changeOrigin: true
      }
    }
  },
  build: {
    outDir: '/www/wwwroot/feiyuadmin/backend/public/admin',
    // 生产构建 chunk 策略
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('element-plus')) return 'element-plus'
            if (id.includes('echarts')) return 'echarts'
            if (id.includes('monaco-editor')) return 'monaco-editor'
            if (id.includes('@wangeditor')) return 'wang-editor'
          }
        }
      }
    },
    chunkSizeWarningLimit: 500,
    // 关闭 sourcemap 减小体积
    sourcemap: false,
    // 清理旧构建
    emptyOutDir: true,
  }
})

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
      '/admin/api': {
        target: 'http://localhost:8088',
        changeOrigin: true
      }
    }
  },
  build: {
    outDir: '/www/wwwroot/feiyuadmin/backend/public/admin',
    // 生产构建 chunk 策略：限制单个文件大小，避免碎片化
    rollupOptions: {
      output: {
        manualChunks: {
          'element-plus': ['element-plus'],
          'echarts': ['echarts'],
          'monaco-editor': ['monaco-editor'],
          'wang-editor': ['@wangeditor/editor', '@wangeditor/editor-for-vue'],
        },
        // 禁止 Vite 生成超过 500KB 的 chunk
        chunkSizeWarningLimit: 500,
      }
    },
    // 关闭 sourcemap 减小体积
    sourcemap: false,
    // 清理旧构建
    emptyOutDir: true,
  }
})

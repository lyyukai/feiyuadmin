import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  base: '/pc/',
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  build: {
    outDir: '/www/wwwroot/feiyuadmin/backend/public/pc',
    rollupOptions: {
      output: {
        assetsDir: 'assets',
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('element-plus')) return 'element-plus'
            if (id.includes('@element-plus')) return 'element-plus-icons'
            if (id.includes('vue')) return 'vue'
          }
        }
      }
    }
  }
})

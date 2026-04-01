<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <slot name="buttons"></slot>
      </div>
      <div class="search-bar-right">
        <slot name="search"></slot>
        <el-button type="primary" :icon="Search" @click="$emit('search')">搜索</el-button>
        <el-button :icon="Refresh" @click="$emit('reset')">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table v-bind="$attrs">
        <slot></slot>
      </el-table>
    </el-card>
  </div>
</template>

<script setup>
import { Search, Refresh } from '@element-plus/icons-vue'

defineOptions({ inheritAttrs: false })
defineEmits(['search', 'reset'])
</script>

<style scoped>
.search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #fff;
  border-radius: 8px;
  margin-bottom: 12px;
}

.search-bar-left,
.search-bar-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.table-card {
  border-radius: 8px;
}

:deep(.el-table) {
  border-radius: 8px;
}
</style>

<template>
  <div class="table-wrapper" :class="{ 'dark-theme': theme === 'dark' }">
    <div class="table-title" v-if="config.title">{{ config.title }}</div>
    <el-table :data="tableData" size="small" :max-height="config.maxHeight || 200" :header-cell-style="{ background: theme === 'dark' ? '#1a1a2e' : '#f5f7fa' }">
      <el-table-column v-for="col in columns" :key="col.prop" :prop="col.prop" :label="col.label" :width="col.width" />
    </el-table>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const defaultData = [
  { name: '张三', score: 98, rank: 1 },
  { name: '李四', score: 95, rank: 2 },
  { name: '王五', score: 92, rank: 3 },
  { name: '赵六', score: 88, rank: 4 },
  { name: '孙七', score: 85, rank: 5 }
]

const tableData = computed(() => {
  return props.data.length > 0 ? props.data : defaultData
})

const columns = computed(() => {
  if (props.config.columns && props.config.columns.length > 0) {
    return props.config.columns
  }
  if (tableData.value.length > 0) {
    return Object.keys(tableData.value[0]).map(key => ({
      prop: key,
      label: key.toUpperCase(),
      width: 'auto'
    }))
  }
  return []
})
</script>

<style scoped>
.table-wrapper {
  padding: 10px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.table-title {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 10px;
  text-align: center;
  color: v-bind('theme === "dark" ? "#fff" : "#333"');
}

.table-wrapper :deep(.el-table) {
  background: transparent;
}

.table-wrapper :deep(.el-table__row) {
  background: transparent !important;
}

.table-wrapper.dark-theme :deep(.el-table) {
  --el-table-bg-color: transparent;
  --el-table-tr-bg-color: transparent;
  --el-table-header-bg-color: #1a1a2e;
  color: #fff;
}

.table-wrapper.dark-theme :deep(.el-table th) {
  background: #1a1a2e !important;
  color: #fff;
  border-color: #333 !important;
}

.table-wrapper.dark-theme :deep(.el-table td) {
  border-color: #333 !important;
  color: #fff;
}
</style>

<template>
  <div class="page-container">
    <el-card>
      <template #header>
        <span>数据概览</span>
      </template>
      <el-row :gutter="20">
        <el-col :span="6" v-for="(item, index) in stats" :key="index">
          <div class="stat-item">
            <p class="stat-label">{{ item.label }}</p>
            <p class="stat-value">{{ item.value }}</p>
            <p class="stat-change" :class="item.change > 0 ? 'up' : 'down'">
              {{ item.change > 0 ? '+' : '' }}{{ item.change }}%
            </p>
          </div>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const stats = ref([
  { label: '今日访问', value: '1,234', change: 12.5 },
  { label: '今日订单', value: '89', change: -3.2 },
  { label: '今日收入', value: '¥12,580', change: 8.7 },
  { label: '新增会员', value: '45', change: 15.3 }
])
</script>

<style scoped>
.stat-item {
  padding: 20px;
  background: #f5f7fa;
  border-radius: 8px;
}

.stat-label {
  color: #909399;
  font-size: 14px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #303133;
}

.stat-change {
  font-size: 12px;
  margin-top: 4px;
}

.stat-change.up {
  color: #67C23A;
}

.stat-change.down {
  color: #F56C6C;
}
</style>

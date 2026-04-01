<template>
  <div class="ranking-wrapper" :class="{ 'dark-theme': theme === 'dark' }">
    <div class="ranking-title" v-if="config.title">{{ config.title }}</div>
    <div class="ranking-list">
      <div v-for="(item, index) in rankingData" :key="index" class="ranking-item">
        <div class="ranking-index" :class="{ 'top-3': index < 3 }">
          {{ index + 1 }}
        </div>
        <div class="ranking-info">
          <div class="ranking-name">{{ item.name }}</div>
          <div class="ranking-bar">
            <div class="ranking-bar-inner" :style="{ width: getBarWidth(item.value) + '%' }"></div>
          </div>
        </div>
        <div class="ranking-value">{{ item.value }}{{ config.unit || '' }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const defaultData = [
  { name: '北京市', value: 15000 },
  { name: '上海市', value: 12000 },
  { name: '广州市', value: 10000 },
  { name: '深圳市', value: 9000 },
  { name: '杭州市', value: 8000 },
  { name: '成都市', value: 7000 },
  { name: '武汉市', value: 6000 }
]

const rankingData = computed(() => {
  return props.data.length > 0 ? props.data : defaultData
})

const maxValue = computed(() => {
  return Math.max(...rankingData.value.map(d => d.value), 1)
})

function getBarWidth(value) {
  return (value / maxValue.value) * 100
}
</script>

<style scoped>
.ranking-wrapper {
  padding: 10px;
  height: 100%;
  color: #333;
}

.dark-theme {
  color: #fff;
}

.ranking-title {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 15px;
  text-align: center;
}

.ranking-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ranking-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ranking-index {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #666;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
}

.ranking-index.top-3 {
  background: linear-gradient(135deg, #409eff, #79bbff);
}

.ranking-info {
  flex: 1;
  min-width: 0;
}

.ranking-name {
  font-size: 12px;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ranking-bar {
  height: 6px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
  overflow: hidden;
}

.dark-theme .ranking-bar {
  background: rgba(255, 255, 255, 0.1);
}

.ranking-bar-inner {
  height: 100%;
  background: linear-gradient(90deg, #409eff, #67c23a);
  border-radius: 3px;
  transition: width 1s ease;
}

.ranking-value {
  font-size: 12px;
  font-weight: 600;
  min-width: 60px;
  text-align: right;
}
</style>

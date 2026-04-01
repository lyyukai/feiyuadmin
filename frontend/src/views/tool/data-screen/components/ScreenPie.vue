<template>
  <div class="chart-wrapper" ref="chartRef"></div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import * as echarts from 'echarts'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const chartRef = ref(null)
let chart = null

function getChartOption() {
  const defaultData = [
    { name: '直接访问', value: 335 },
    { name: '邮件营销', value: 310 },
    { name: '联盟广告', value: 234 },
    { name: '视频广告', value: 135 },
    { name: '搜索引擎', value: 1548 }
  ]
  const chartData = props.data.length > 0 ? props.data : defaultData

  return {
    backgroundColor: 'transparent',
    title: {
      text: props.config.title || '',
      left: 'center',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333', fontSize: 14 }
    },
    tooltip: {
      trigger: 'item',
      backgroundColor: props.theme === 'dark' ? '#1a1a2e' : '#fff',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333' }
    },
    legend: {
      orient: 'vertical',
      right: '5%',
      top: 'center',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333' }
    },
    series: [{
      type: 'pie',
      radius: ['40%', '70%'],
      center: ['35%', '50%'],
      avoidLabelOverlap: false,
      itemStyle: {
        borderRadius: 10,
        borderColor: props.theme === 'dark' ? '#1a1a2e' : '#fff',
        borderWidth: 2
      },
      label: { show: false },
      emphasis: {
        label: { show: true, fontSize: 14, fontWeight: 'bold' }
      },
      data: chartData.map((d, i) => ({
        ...d,
        itemStyle: { color: ['#409eff', '#67c23a', '#e6a23c', '#f56c6c', '#909399'][i % 5] }
      }))
    }]
  }
}

function initChart() {
  if (!chartRef.value) return
  chart = echarts.init(chartRef.value)
  chart.setOption(getChartOption())
}

function updateChart() {
  if (chart) {
    chart.setOption(getChartOption())
  }
}

onMounted(() => {
  initChart()
  window.addEventListener('resize', updateChart)
})

onUnmounted(() => {
  if (chart) {
    chart.dispose()
    chart = null
  }
  window.removeEventListener('resize', updateChart)
})

watch(() => [props.data, props.theme], updateChart, { deep: true })
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  height: 100%;
}
</style>

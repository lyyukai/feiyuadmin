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
  const value = props.data.length > 0 ? (props.data[0]?.value || 75) : 75
  const name = props.data.length > 0 ? (props.data[0]?.name || '指标') : '指标'

  return {
    backgroundColor: 'transparent',
    title: {
      text: props.config.title || '',
      left: 'center',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333', fontSize: 14 }
    },
    series: [{
      type: 'gauge',
      center: ['50%', '60%'],
      radius: '90%',
      startAngle: 200,
      endAngle: -20,
      min: 0,
      max: 100,
      splitNumber: 10,
      itemStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
          { offset: 0, color: '#409eff' },
          { offset: 1, color: '#67c23a' }
        ])
      },
      progress: { show: true, width: 20, roundCap: true },
      pointer: { show: true, length: '60%', width: 6 },
      axisLine: { lineStyle: { width: 20, color: [[1, props.theme === 'dark' ? '#333' : '#e0e0e0']] } },
      axisTick: { show: false },
      splitLine: { show: false },
      axisLabel: { show: false },
      anchor: { show: false },
      title: { show: false },
      detail: {
        valueAnimation: true,
        fontSize: 24,
        fontWeight: 'bold',
        offsetCenter: [0, '40%'],
        formatter: '{value}%',
        color: props.theme === 'dark' ? '#fff' : '#333'
      },
      data: [{ value, name }]
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

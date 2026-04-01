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
    { name: '一月', value: 120 },
    { name: '二月', value: 200 },
    { name: '三月', value: 150 },
    { name: '四月', value: 300 },
    { name: '五月', value: 250 },
    { name: '六月', value: 400 }
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
      trigger: 'axis',
      backgroundColor: props.theme === 'dark' ? '#1a1a2e' : '#fff',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333' }
    },
    grid: { left: '10%', right: '10%', top: '20%', bottom: '15%', containLabel: true },
    xAxis: {
      type: 'category',
      data: chartData.map(d => d.name),
      axisLine: { lineStyle: { color: props.theme === 'dark' ? '#444' : '#ccc' } },
      axisLabel: { color: props.theme === 'dark' ? '#fff' : '#333' }
    },
    yAxis: {
      type: 'value',
      axisLine: { lineStyle: { color: props.theme === 'dark' ? '#444' : '#ccc' } },
      axisLabel: { color: props.theme === 'dark' ? '#fff' : '#333' },
      splitLine: { lineStyle: { color: props.theme === 'dark' ? '#333' : '#eee' } }
    },
    series: [{
      type: 'bar',
      data: chartData.map(d => d.value),
      itemStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: '#409eff' },
          { offset: 1, color: '#79bbff' }
        ])
      },
      barWidth: '50%'
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

<template>
  <div class="chart-wrapper" ref="chartRef"></div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import * as echarts from 'echarts'
import chinaJson from '@/assets/echarts-map/china.json'

const props = defineProps({
  config: { type: Object, default: () => ({}) },
  data: { type: Array, default: () => [] },
  theme: { type: String, default: 'dark' }
})

const chartRef = ref(null)
let chart = null

function getChartOption() {
  const defaultData = [
    { name: '北京', value: 150 },
    { name: '上海', value: 120 },
    { name: '广东', value: 200 },
    { name: '浙江', value: 100 },
    { name: '江苏', value: 90 },
    { name: '四川', value: 80 },
    { name: '湖北', value: 70 }
  ]
  const chartData = props.data.length > 0 ? props.data : defaultData

  return {
    backgroundColor: 'transparent',
    title: {
      text: props.config.title || '中国地图',
      left: 'center',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333', fontSize: 14 }
    },
    tooltip: {
      trigger: 'item',
      backgroundColor: props.theme === 'dark' ? '#1a1a2e' : '#fff',
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333' }
    },
    visualMap: {
      min: 0,
      max: 200,
      left: '5%',
      bottom: '5%',
      text: ['高', '低'],
      textStyle: { color: props.theme === 'dark' ? '#fff' : '#333' },
      inRange: { color: ['#409eff', '#79bbff', '#a0dffe', '#c3e6ff'] },
      calculable: true
    },
    series: [{
      type: 'map',
      map: 'china',
      roam: true,
      label: { show: false },
      emphasis: {
        label: { show: true, color: '#fff' },
        itemStyle: { areaColor: '#409eff' }
      },
      data: chartData
    }]
  }
}

function initChart() {
  if (!chartRef.value) return
  echarts.registerMap('china', chinaJson)
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

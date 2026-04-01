<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Download" @click="handleExport">导出数据</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.orderNo" placeholder="订单号" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.status" placeholder="订单状态" style="width: 140px" clearable>
          <el-option label="待支付" value="待支付" />
          <el-option label="已完成" value="已完成" />
          <el-option label="处理中" value="处理中" />
          <el-option label="已取消" value="已取消" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="orderNo" label="订单号" min-width="180" show-overflow-tooltip />
        <el-table-column prop="memberName" label="会员" min-width="100" />
        <el-table-column prop="amount" label="金额" width="120" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.amount }}</span></template>
        </el-table-column>
        <el-table-column prop="paymentMethod" label="支付方式" width="120" align="center">
          <template #default="{ row }">
            <el-tag :type="row.paymentMethod === '微信支付' ? 'success' : 'primary'" size="small">{{ row.paymentMethod }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="statusType[row.status] || 'info'" size="small">{{ row.status }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createTime" label="下单时间" width="180" />
        <el-table-column label="操作" width="120" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="showDetail(row)">详情</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 订单详情弹窗 -->
    <el-dialog v-model="detailVisible" title="订单详情" width="600px">
      <el-descriptions :column="2" border v-if="detailData.orderNo">
        <el-descriptions-item label="订单号">{{ detailData.orderNo }}</el-descriptions-item>
        <el-descriptions-item label="会员">{{ detailData.memberName }}</el-descriptions-item>
        <el-descriptions-item label="支付方式">{{ detailData.paymentMethod }}</el-descriptions-item>
        <el-descriptions-item label="金额"><span class="price">¥{{ detailData.amount }}</span></el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag :type="statusType[detailData.status] || 'info'" size="small">{{ detailData.status }}</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="下单时间">{{ detailData.createTime }}</el-descriptions-item>
      </el-descriptions>
      <template #footer>
        <el-button @click="detailVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Refresh, Download } from '@element-plus/icons-vue'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const detailData = ref({})

const searchForm = reactive({ orderNo: '', status: '' })

const statusType = { '已完成': 'success', '处理中': 'warning', '已取消': 'info', '待支付': 'warning' }

const mockData = [
  { orderNo: 'FY202603300001', memberName: '张三', amount: 299.00, paymentMethod: '微信支付', status: '已完成', createTime: '2026-03-30 10:00:00' },
  { orderNo: 'FY202603300002', memberName: '李四', amount: 599.00, paymentMethod: '支付宝', status: '处理中', createTime: '2026-03-30 11:30:00' },
  { orderNo: 'FY202603300003', memberName: '王五', amount: 1299.00, paymentMethod: '微信支付', status: '已完成', createTime: '2026-03-30 14:15:00' }
]

const loadData = () => {
  loading.value = true
  let list = [...mockData]
  if (searchForm.orderNo) {
    list = list.filter(item => item.orderNo.toLowerCase().includes(searchForm.orderNo.toLowerCase()))
  }
  if (searchForm.status) {
    list = list.filter(item => item.status === searchForm.status)
  }
  tableData.value = list
  loading.value = false
}

const resetSearch = () => { searchForm.orderNo = ''; searchForm.status = ''; loadData() }

const showDetail = (row) => { detailData.value = row; detailVisible.value = true }

const handleExport = () => {
  if (!tableData.value.length) { ElMessage.warning('暂无数据可导出'); return }
  const rows = tableData.value.map(d => [d.orderNo, d.memberName, d.amount, d.paymentMethod, d.status, d.createTime])
  const csv = ['订单号,会员,金额,支付方式,状态,下单时间', ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `订单数据_${new Date().toISOString().slice(0, 10)}.csv`
  a.click()
  URL.revokeObjectURL(url)
  ElMessage.success('导出成功')
}

loadData()
</script>

<style scoped>
.price { color: #F56C6C; font-weight: 500; }
</style>

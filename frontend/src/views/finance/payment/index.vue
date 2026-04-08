<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <el-card class="search-card" shadow="never">
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item label="交易单号">
          <el-input v-model="searchForm.trade_no" placeholder="请输入交易单号" clearable style="width: 200px" @keyup.enter="loadData" />
        </el-form-item>
        <el-form-item label="订单号">
          <el-input v-model="searchForm.order_no" placeholder="请输入订单号" clearable style="width: 180px" @keyup.enter="loadData" />
        </el-form-item>
        <el-form-item label="支付渠道">
          <el-select v-model="searchForm.channel" placeholder="全部渠道" clearable style="width: 130px">
            <el-option label="微信" :value="1" />
            <el-option label="支付宝" :value="2" />
          </el-select>
        </el-form-item>
        <el-form-item label="交易状态">
          <el-select v-model="searchForm.status" placeholder="全部状态" clearable style="width: 130px">
            <el-option label="待支付" :value="0" />
            <el-option label="支付中" :value="1" />
            <el-option label="成功" :value="2" />
            <el-option label="失败" :value="3" />
            <el-option label="已退款" :value="4" />
          </el-select>
        </el-form-item>
        <el-form-item label="日期范围">
          <el-date-picker
            v-model="dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            value-format="YYYY-MM-DD"
            style="width: 240px"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
          <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 统计卡片 -->
    <div class="stat-cards">
      <el-card class="stat-card" shadow="never">
        <div class="stat-item">
          <div class="stat-label">今日收入</div>
          <div class="stat-value income">¥{{ stat.today_income?.toFixed(2) || '0.00' }}</div>
        </div>
      </el-card>
      <el-card class="stat-card" shadow="never">
        <div class="stat-item">
          <div class="stat-label">本月收入</div>
          <div class="stat-value income">¥{{ stat.month_income?.toFixed(2) || '0.00' }}</div>
        </div>
      </el-card>
      <el-card class="stat-card" shadow="never">
        <div class="stat-item">
          <div class="stat-label">待结算</div>
          <div class="stat-value pending">¥{{ stat.pending_settle?.toFixed(2) || '0.00' }}</div>
        </div>
      </el-card>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>支付流水</span>
          <el-button type="success" :icon="Download" @click="handleExport" plain>导出</el-button>
        </div>
      </template>
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="trade_no" label="交易单号" min-width="180" show-overflow-tooltip />
        <el-table-column prop="order_no" label="订单号" min-width="160" show-overflow-tooltip />
        <el-table-column prop="merchant_no" label="商户单号" min-width="160" show-overflow-tooltip>
          <template #default="{ row }">{{ row.merchant_no || '-' }}</template>
        </el-table-column>
        <el-table-column prop="channel" label="支付渠道" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.channel === '1'" type="success" size="small">微信</el-tag>
            <el-tag v-else-if="row.channel === '2'" type="primary" size="small">支付宝</el-tag>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="way" label="支付方式" width="110" align="center">
          <template #default="{ row }">{{ row.way || '-' }}</template>
        </el-table-column>
        <el-table-column prop="amount" label="交易金额" width="110" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.amount }}</span></template>
        </el-table-column>
        <el-table-column prop="fee" label="手续费" width="100" align="right">
          <template #default="{ row }"><span class="fee">¥{{ row.fee }}</span></template>
        </el-table-column>
        <el-table-column prop="net_amount" label="实收金额" width="110" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.net_amount }}</span></template>
        </el-table-column>
        <el-table-column prop="status" label="交易状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="statusTagType[row.status]" size="small">{{ statusLabel[row.status] }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="trade_time" label="交易时间" width="170">
          <template #default="{ row }">{{ row.trade_time || '-' }}</template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="170" />
      </el-table>

      <!-- 分页 -->
      <div class="pagination-wrap">
        <el-pagination
          background
          layout="total, prev, pager, next"
          :total="pagination.total"
          :current-page="pagination.page"
          :page-size="pagination.limit"
          @current-change="handlePageChange"
        />
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Refresh, Download } from '@element-plus/icons-vue'
import { getPayStatementList } from '@/api/pay'

const loading = ref(false)
const tableData = ref([])
const dateRange = ref([])
const stat = reactive({ today_income: 0, month_income: 0, pending_settle: 0 })

const searchForm = reactive({
  trade_no: '',
  order_no: '',
  channel: '',
  status: ''
})

const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

const statusLabel = { 0: '待支付', 1: '支付中', 2: '成功', 3: '失败', 4: '已退款' }
const statusTagType = { 0: 'info', 1: 'warning', 2: 'success', 3: 'danger', 4: 'warning' }

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      trade_no: searchForm.trade_no,
      order_no: searchForm.order_no,
      channel: searchForm.channel,
      status: searchForm.status,
      start_time: dateRange.value?.[0] || '',
      end_time: dateRange.value?.[1] || ''
    }
    Object.keys(params).forEach(k => { if (params[k] === '') delete params[k] })

    const res = await getPayStatementList(params)
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || res.total || 0

    // Update stats from response extend
    if (res.data?.extend) {
      Object.assign(stat, res.data.extend)
    }
  } catch (e) {
    ElMessage.error(e.message || '加载失败')
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  pagination.page = page
  loadData()
}

const resetSearch = () => {
  searchForm.trade_no = ''
  searchForm.order_no = ''
  searchForm.channel = ''
  searchForm.status = ''
  dateRange.value = []
  pagination.page = 1
  loadData()
}

const handleExport = () => {
  if (!tableData.value.length) { ElMessage.warning('暂无数据可导出'); return }
  const header = ['交易单号', '订单号', '商户单号', '支付渠道', '支付方式', '交易金额', '手续费', '实收金额', '交易状态', '交易时间', '创建时间']
  const rows = tableData.value.map(d => [
    d.trade_no, d.order_no, d.merchant_no || '-',
    d.channel === '1' ? '微信' : d.channel === '2' ? '支付宝' : '-',
    d.way || '-', d.amount, d.fee, d.net_amount,
    statusLabel[d.status] || d.status,
    d.trade_time || '-', d.create_time
  ])
  const csv = [header.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `支付流水_${new Date().toISOString().slice(0, 10)}.csv`
  a.click()
  URL.revokeObjectURL(url)
  ElMessage.success('导出成功')
}

onMounted(() => loadData())
</script>

<style scoped>
.price { color: #F56C6C; font-weight: 500; }
.fee { color: #909399; }
.search-card { margin-bottom: 12px; }
.stat-cards { display: flex; gap: 12px; margin-bottom: 12px; }
.stat-card { flex: 1; }
.stat-item { text-align: center; }
.stat-label { font-size: 13px; color: #909399; margin-bottom: 6px; }
.stat-value { font-size: 22px; font-weight: 600; }
.stat-value.income { color: #67C23A; }
.stat-value.pending { color: #E6A23C; }
.table-card { margin-bottom: 12px; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
</style>

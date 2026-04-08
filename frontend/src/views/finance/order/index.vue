<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <el-card class="search-card" shadow="never">
      <el-form :inline="true" :model="searchForm" class="search-form">
        <el-form-item label="订单号">
          <el-input v-model="searchForm.order_no" placeholder="请输入订单号" clearable style="width: 200px" @keyup.enter="loadData" />
        </el-form-item>
        <el-form-item label="会员名">
          <el-input v-model="searchForm.username" placeholder="请输入会员名" clearable style="width: 150px" @keyup.enter="loadData" />
        </el-form-item>
        <el-form-item label="支付状态">
          <el-select v-model="searchForm.status" placeholder="全部状态" clearable style="width: 130px">
            <el-option label="待支付" :value="0" />
            <el-option label="已支付" :value="1" />
            <el-option label="已退款" :value="2" />
            <el-option label="已关闭" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="支付渠道">
          <el-select v-model="searchForm.channel" placeholder="全部渠道" clearable style="width: 130px">
            <el-option label="微信" :value="1" />
            <el-option label="支付宝" :value="2" />
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

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>订单列表</span>
          <el-button type="success" :icon="Download" @click="handleExport" plain>导出</el-button>
        </div>
      </template>
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip />
        <el-table-column prop="username" label="用户" min-width="100">
          <template #default="{ row }">{{ row.username || '-' }}</template>
        </el-table-column>
        <el-table-column prop="subject" label="商品" min-width="120" show-overflow-tooltip>
          <template #default="{ row }">{{ row.subject || '-' }}</template>
        </el-table-column>
        <el-table-column prop="total_fee" label="订单金额" width="110" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.total_fee }}</span></template>
        </el-table-column>
        <el-table-column prop="pay_amount" label="实付金额" width="110" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.pay_amount }}</span></template>
        </el-table-column>
        <el-table-column prop="pay_channel" label="支付方式" width="110" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.pay_channel === 1" type="success" size="small">微信支付</el-tag>
            <el-tag v-else-if="row.pay_channel === 2" type="primary" size="small">支付宝</el-tag>
            <el-tag v-else type="info" size="small">-</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="statusTagType[row.status]" size="small">{{ statusLabel[row.status] }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="下单时间" width="170" />
        <el-table-column label="操作" width="150" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="showDetail(row)">详情</el-button>
            <el-button v-if="row.status === 1" link type="danger" size="small" @click="handleRefund(row)">退款</el-button>
          </template>
        </el-table-column>
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

    <!-- 订单详情弹窗 -->
    <el-dialog v-model="detailVisible" title="订单详情" width="650px" destroy-on-close>
      <el-descriptions :column="2" border v-if="detailData.id">
        <el-descriptions-item label="订单ID">{{ detailData.id }}</el-descriptions-item>
        <el-descriptions-item label="订单号">{{ detailData.order_no }}</el-descriptions-item>
        <el-descriptions-item label="用户">
          {{ detailData.username || '-' }} (ID: {{ detailData.user_id || '-' }})
        </el-descriptions-item>
        <el-descriptions-item label="商品">{{ detailData.subject || '-' }}</el-descriptions-item>
        <el-descriptions-item label="订单金额">
          <span class="price">¥{{ detailData.total_fee }}</span>
        </el-descriptions-item>
        <el-descriptions-item label="实付金额">
          <span class="price">¥{{ detailData.pay_amount }}</span>
        </el-descriptions-item>
        <el-descriptions-item label="支付渠道">
          <el-tag v-if="detailData.pay_channel === 1" type="success" size="small">微信支付</el-tag>
          <el-tag v-else-if="detailData.pay_channel === 2" type="primary" size="small">支付宝</el-tag>
          <span v-else>-</span>
        </el-descriptions-item>
        <el-descriptions-item label="支付方式">{{ detailData.pay_way || '-' }}</el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag :type="statusTagType[detailData.status]" size="small">{{ statusLabel[detailData.status] }}</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="支付时间">{{ detailData.pay_time || '-' }}</el-descriptions-item>
        <el-descriptions-item label="下单时间">{{ detailData.create_time }}</el-descriptions-item>
        <el-descriptions-item label="商户单号" :span="2">{{ detailData.out_trade_no || '-' }}</el-descriptions-item>
      </el-descriptions>
      <template #footer>
        <el-button @click="detailVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Download } from '@element-plus/icons-vue'
import { getPayOrderList, getPayOrderDetail, refundPayOrder } from '@/api/pay'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const detailData = ref({})
const dateRange = ref([])

const searchForm = reactive({
  order_no: '',
  username: '',
  status: '',
  channel: '',
  start_time: '',
  end_time: ''
})

const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

const statusLabel = { 0: '待支付', 1: '已支付', 2: '已退款', 3: '已关闭' }
const statusTagType = { 0: 'warning', 1: 'success', 2: 'info', 3: 'danger' }

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      order_no: searchForm.order_no,
      username: searchForm.username,
      status: searchForm.status,
      channel: searchForm.channel,
      start_time: dateRange.value?.[0] || '',
      end_time: dateRange.value?.[1] || ''
    }
    // Remove empty params
    Object.keys(params).forEach(k => { if (params[k] === '') delete params[k] })

    const res = await getPayOrderList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
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
  searchForm.order_no = ''
  searchForm.username = ''
  searchForm.status = ''
  searchForm.channel = ''
  dateRange.value = []
  pagination.page = 1
  loadData()
}

const showDetail = async (row) => {
  try {
    const res = await getPayOrderDetail({ id: row.id })
    detailData.value = res.data || res || {}
    detailVisible.value = true
  } catch (e) {
    ElMessage.error('加载详情失败')
  }
}

const handleRefund = async (row) => {
  try {
    await ElMessageBox.confirm(
      `确定要对订单「${row.order_no}」进行退款吗？退款金额：¥${row.pay_amount}`,
      '确认退款',
      { type: 'warning' }
    )
    await refundPayOrder({ id: row.id })
    ElMessage.success('退款成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e.message || '退款失败')
  }
}

const handleExport = () => {
  if (!tableData.value.length) { ElMessage.warning('暂无数据可导出'); return }
  const header = ['订单ID', '订单号', '用户', '商品', '订单金额', '实付金额', '支付方式', '状态', '下单时间']
  const rows = tableData.value.map(d => [
    d.id, d.order_no, d.username || '-', d.subject || '-',
    d.total_fee, d.pay_amount,
    d.pay_channel === 1 ? '微信支付' : d.pay_channel === 2 ? '支付宝' : '-',
    statusLabel[d.status] || d.status, d.create_time
  ])
  const csv = [header.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `订单数据_${new Date().toISOString().slice(0, 10)}.csv`
  a.click()
  URL.revokeObjectURL(url)
  ElMessage.success('导出成功')
}

onMounted(() => loadData())
</script>

<style scoped>
.price { color: #F56C6C; font-weight: 500; }
.search-card { margin-bottom: 12px; }
.table-card { margin-bottom: 12px; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
</style>

<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Download" @click="handleExport">导出数据</el-button>
      </div>
      <div class="search-bar-right">
        <el-date-picker
          v-model="dateRange"
          type="daterange"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          value-format="YYYY-MM-DD"
          style="width: 240px"
          @change="loadData"
        />
        <el-input v-model="searchForm.order_no" placeholder="订单号" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.status" placeholder="订单状态" style="width: 140px" clearable>
          <el-option label="待支付" value="pending" />
          <el-option label="已支付" value="paid" />
          <el-option label="已退款" value="refunded" />
          <el-option label="已关闭" value="closed" />
          <el-option label="退款中" value="refunding" />
        </el-select>
        <el-select v-model="searchForm.channel" placeholder="支付渠道" style="width: 120px" clearable>
          <el-option label="微信支付" value="wechat" />
          <el-option label="支付宝" value="alipay" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="order_no" label="订单号" min-width="180" />
        <el-table-column prop="channel" label="支付渠道" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.channel === 'wechat' ? 'success' : 'primary'" size="small">
              {{ row.channel === 'wechat' ? '微信' : '支付宝' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="subject" label="订单标题" min-width="150" show-overflow-tooltip />
        <el-table-column prop="total_fee" label="订单金额（元）" width="120" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.total_fee }}</span></template>
        </el-table-column>
        <el-table-column prop="paid_fee" label="实际支付（元）" width="120" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.paid_fee || '0.00' }}</span></template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.status === 'pending'" type="warning" size="small">待支付</el-tag>
            <el-tag v-else-if="row.status === 'paid'" type="success" size="small">已支付</el-tag>
            <el-tag v-else-if="row.status === 'refunded'" type="info" size="small">已退款</el-tag>
            <el-tag v-else-if="row.status === 'refunding'" type="warning" size="small">退款中</el-tag>
            <el-tag v-else type="danger" size="small">已关闭</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="pay_time" label="支付时间" width="160" />
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="showDetail(row)">详情</el-button>
            <el-button v-if="row.status === 'pending'" link type="warning" size="small" @click="handleManualPaid(row)">补单</el-button>
            <el-button v-if="row.status === 'pending'" link type="danger" size="small" @click="handleClose(row)">关闭</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-wrapper">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @change="loadData"
        />
      </div>
    </el-card>

    <!-- 订单详情弹窗 -->
    <el-dialog v-model="detailVisible" title="订单详情" width="650px">
      <el-descriptions :column="2" border v-if="detailData.id">
        <el-descriptions-item label="订单号">{{ detailData.order_no }}</el-descriptions-item>
        <el-descriptions-item label="第三方订单号">{{ detailData.out_trade_no || '-' }}</el-descriptions-item>
        <el-descriptions-item label="支付渠道">
          <el-tag :type="detailData.channel === 'wechat' ? 'success' : 'primary'" size="small">
            {{ detailData.channel === 'wechat' ? '微信支付' : '支付宝' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="订单标题">{{ detailData.subject || '-' }}</el-descriptions-item>
        <el-descriptions-item label="订单金额">¥{{ detailData.total_fee }}</el-descriptions-item>
        <el-descriptions-item label="实际支付">¥{{ detailData.paid_fee || '0.00' }}</el-descriptions-item>
        <el-descriptions-item label="订单状态">
          <el-tag v-if="detailData.status === 'pending'" type="warning" size="small">待支付</el-tag>
          <el-tag v-else-if="detailData.status === 'paid'" type="success" size="small">已支付</el-tag>
          <el-tag v-else-if="detailData.status === 'refunded'" type="info" size="small">已退款</el-tag>
          <el-tag v-else-if="detailData.status === 'refunding'" type="warning" size="small">退款中</el-tag>
          <el-tag v-else type="danger" size="small">已关闭</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="客户端IP">{{ detailData.client_ip || '-' }}</el-descriptions-item>
        <el-descriptions-item label="支付时间">{{ detailData.pay_time || '-' }}</el-descriptions-item>
        <el-descriptions-item label="过期时间">{{ detailData.expire_time || '-' }}</el-descriptions-item>
        <el-descriptions-item label="创建时间">{{ detailData.create_time }}</el-descriptions-item>
      </el-descriptions>
      <template #footer><el-button @click="detailVisible = false">关闭</el-button></template>
    </el-dialog>

    <!-- 手动补单弹窗 -->
    <el-dialog v-model="paidVisible" title="手动补单" width="450px">
      <el-form :model="paidForm" label-width="100px">
        <el-form-item label="订单号">
          <el-input v-model="paidForm.order_no" disabled />
        </el-form-item>
        <el-form-item label="第三方单号">
          <el-input v-model="paidForm.out_trade_no" placeholder="请输入第三方支付单号" />
        </el-form-item>
        <el-form-item label="支付时间">
          <el-date-picker
            v-model="paidForm.pay_time"
            type="datetime"
            placeholder="选择支付时间"
            value-format="YYYY-MM-DD HH:mm:ss"
            style="width: 100%"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="paidVisible = false">取消</el-button>
        <el-button type="primary" :loading="paidLoading" @click="submitManualPaid">确认补单</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Download } from '@element-plus/icons-vue'
import { getPayOrderList, getPayOrderDetail, closePayOrder, manualPaidOrder } from '@/api/pay'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const detailData = ref({})
const paidVisible = ref(false)
const paidLoading = ref(false)
const dateRange = ref([])
const searchForm = reactive({ order_no: '', status: '', channel: '' })
const pagination = reactive({ page: 1, limit: 10, total: 0 })
const paidForm = reactive({ id: 0, order_no: '', out_trade_no: '', pay_time: '' })

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    }
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_time = dateRange.value[0]
      params.end_time = dateRange.value[1]
    }
    const res = await getPayOrderList(params)
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || 0
  } catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}

const resetSearch = () => {
  searchForm.order_no = ''
  searchForm.status = ''
  searchForm.channel = ''
  dateRange.value = []
  pagination.page = 1
  loadData()
}

const showDetail = async (row) => {
  try {
    const res = await getPayOrderDetail({ id: row.id })
    detailData.value = res.data || row
    detailVisible.value = true
  } catch { ElMessage.error('加载详情失败') }
}

const handleClose = async (row) => {
  try {
    await ElMessageBox.confirm('确定关闭该订单吗？', '提示', { type: 'warning' })
    await closePayOrder({ id: row.id })
    ElMessage.success('订单已关闭')
    loadData()
  } catch {}
}

const handleManualPaid = (row) => {
  paidForm.id = row.id
  paidForm.order_no = row.order_no
  paidForm.out_trade_no = ''
  paidForm.pay_time = ''
  paidVisible.value = true
}

const submitManualPaid = async () => {
  if (!paidForm.out_trade_no && !paidForm.pay_time) {
    ElMessage.warning('请填写第三方单号或支付时间')
    return
  }
  paidLoading.value = true
  try {
    await manualPaidOrder(paidForm)
    ElMessage.success('补单成功')
    paidVisible.value = false
    loadData()
  } catch (e) { ElMessage.error(e.message || '补单失败') } finally { paidLoading.value = false }
}

const handleExport = () => {
  const data = tableData.value
  if (!data.length) { ElMessage.warning('暂无数据可导出'); return }
  const statusMap = { pending: '待支付', paid: '已支付', refunded: '已退款', closed: '已关闭', refunding: '退款中' }
  const channelMap = { wechat: '微信支付', alipay: '支付宝' }
  const rows = data.map(d => [
    d.order_no, channelMap[d.channel] || d.channel, d.subject, d.total_fee, d.paid_fee || '0.00',
    statusMap[d.status] || d.status, d.pay_time || '-', d.create_time
  ])
  const csv = ['订单号,支付渠道,订单标题,订单金额,实际支付,状态,支付时间,创建时间', ...rows.map(r => r.join(','))].join('\n')
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
.pagination-wrapper { margin-top: 16px; display: flex; justify-content: flex-end; }
</style>

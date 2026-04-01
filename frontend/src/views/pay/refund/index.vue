<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="showApplyDialog = true">申请退款</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.refund_no" placeholder="退款单号" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.status" placeholder="退款状态" style="width: 140px" clearable>
          <el-option label="处理中" value="pending" />
          <el-option label="退款成功" value="success" />
          <el-option label="退款失败" value="fail" />
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
        <el-table-column prop="refund_no" label="退款单号" min-width="180" />
        <el-table-column prop="channel" label="支付渠道" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.channel === 'wechat' ? 'success' : 'primary'" size="small">
              {{ row.channel === 'wechat' ? '微信' : '支付宝' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="refund_fee" label="退款金额（元）" width="140" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.refund_fee }}</span></template>
        </el-table-column>
        <el-table-column prop="total_fee" label="订单金额（元）" width="140" align="right">
          <template #default="{ row }"><span>¥{{ row.total_fee }}</span></template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.status === 'pending'" type="warning" size="small">处理中</el-tag>
            <el-tag v-else-if="row.status === 'success'" type="success" size="small">成功</el-tag>
            <el-tag v-else type="danger" size="small">失败</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="reason" label="退款原因" min-width="150" show-overflow-tooltip />
        <el-table-column prop="refund_time" label="退款时间" width="160" />
        <el-table-column prop="create_time" label="申请时间" width="160" />
        <el-table-column label="操作" width="120" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="showDetail(row)">详情</el-button>
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

    <!-- 详情弹窗 -->
    <el-dialog v-model="detailVisible" title="退款详情" width="600px">
      <el-descriptions :column="2" border v-if="detailData.id">
        <el-descriptions-item label="退款单号">{{ detailData.refund_no }}</el-descriptions-item>
        <el-descriptions-item label="第三方退款单号">{{ detailData.out_refund_no || '-' }}</el-descriptions-item>
        <el-descriptions-item label="支付渠道">
          <el-tag :type="detailData.channel === 'wechat' ? 'success' : 'primary'" size="small">
            {{ detailData.channel === 'wechat' ? '微信支付' : '支付宝' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="退款金额">¥{{ detailData.refund_fee }}</el-descriptions-item>
        <el-descriptions-item label="订单金额">¥{{ detailData.total_fee }}</el-descriptions-item>
        <el-descriptions-item label="退款状态">
          <el-tag v-if="detailData.status === 'pending'" type="warning" size="small">处理中</el-tag>
          <el-tag v-else-if="detailData.status === 'success'" type="success" size="small">成功</el-tag>
          <el-tag v-else type="danger" size="small">失败</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="退款原因" :span="2">{{ detailData.reason || '-' }}</el-descriptions-item>
        <el-descriptions-item label="退款时间">{{ detailData.refund_time || '-' }}</el-descriptions-item>
        <el-descriptions-item label="申请时间">{{ detailData.create_time }}</el-descriptions-item>
      </el-descriptions>
      <template #footer><el-button @click="detailVisible = false">关闭</el-button></template>
    </el-dialog>

    <!-- 申请退款弹窗 -->
    <el-dialog v-model="showApplyDialog" title="申请退款" width="500px">
      <el-form :model="applyForm" :rules="applyRules" ref="applyFormRef" label-width="100px">
        <el-form-item label="订单号" prop="order_id">
          <el-select
            v-model="applyForm.order_id"
            filterable
            remote
            reserve-keyword
            placeholder="请输入订单号搜索"
            :remote-method="searchOrders"
            :loading="orderLoading"
            style="width: 100%"
            @change="onOrderChange"
          >
            <el-option
              v-for="item in orderOptions"
              :key="item.id"
              :label="item.order_no + ' - ¥' + item.paid_fee"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="订单金额">
          <span>¥{{ applyForm.total_fee || '0.00' }}</span>
        </el-form-item>
        <el-form-item label="退款金额" prop="refund_fee">
          <el-input-number v-model="applyForm.refund_fee" :min="0.01" :max="applyForm.total_fee" :precision="2" style="width: 100%" />
        </el-form-item>
        <el-form-item label="退款原因" prop="reason">
          <el-input v-model="applyForm.reason" type="textarea" :rows="3" placeholder="请输入退款原因" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showApplyDialog = false">取消</el-button>
        <el-button type="primary" :loading="applyLoading" @click="submitApply">确认申请</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Plus } from '@element-plus/icons-vue'
import { getPayRefundList, getPayRefundDetail, applyPayRefund, getPayOrderList } from '@/api/pay'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const detailData = ref({})
const showApplyDialog = ref(false)
const applyLoading = ref(false)
const orderLoading = ref(false)
const applyFormRef = ref(null)
const orderOptions = ref([])
const searchForm = reactive({ refund_no: '', status: '', channel: '' })
const pagination = reactive({ page: 1, limit: 10, total: 0 })
const applyForm = reactive({ order_id: null, order_no: '', total_fee: 0, refund_fee: 0, reason: '' })

const applyRules = {
  order_id: [{ required: true, message: '请选择订单', trigger: 'change' }],
  refund_fee: [{ required: true, message: '请输入退款金额', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getPayRefundList({ page: pagination.page, limit: pagination.limit, ...searchForm })
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || 0
  } catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}

const resetSearch = () => { searchForm.refund_no = ''; searchForm.status = ''; searchForm.channel = ''; pagination.page = 1; loadData() }

const showDetail = async (row) => {
  try {
    const res = await getPayRefundDetail({ id: row.id })
    detailData.value = res.data || row
    detailVisible.value = true
  } catch { ElMessage.error('加载详情失败') }
}

const searchOrders = async (query) => {
  if (!query) { orderOptions.value = []; return }
  orderLoading.value = true
  try {
    const res = await getPayOrderList({ order_no: query, status: 'paid', limit: 20 })
    orderOptions.value = res.data?.list || res.data || []
  } catch {} finally { orderLoading.value = false }
}

const onOrderChange = (orderId) => {
  const order = orderOptions.value.find(o => o.id === orderId)
  if (order) {
    applyForm.order_no = order.order_no
    applyForm.total_fee = order.paid_fee
    applyForm.refund_fee = order.paid_fee
  }
}

const submitApply = async () => {
  await applyFormRef.value?.validate(async (valid) => {
    if (!valid) return
    applyLoading.value = true
    try {
      await applyPayRefund(applyForm)
      ElMessage.success('申请成功')
      showApplyDialog.value = false
      loadData()
    } catch (e) { ElMessage.error(e.message || '申请失败') } finally { applyLoading.value = false }
  })
}

onMounted(() => loadData())
</script>

<style scoped>
.price { color: #F56C6C; font-weight: 500; }
.pagination-wrapper { margin-top: 16px; display: flex; justify-content: flex-end; }
</style>

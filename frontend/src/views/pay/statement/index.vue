<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="showCreateDialog = true">创建分账</el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.order_no" placeholder="订单号" style="width: 180px" clearable @keyup.enter="loadData" />
        <el-select v-model="searchForm.type" placeholder="分账类型" style="width: 140px" clearable>
          <el-option label="平台抽佣" value="platform" />
          <el-option label="商户分账" value="merchant" />
          <el-option label="供应商分账" value="supplier" />
          <el-option label="员工提成" value="staff" />
        </el-select>
        <el-select v-model="searchForm.status" placeholder="分账状态" style="width: 120px" clearable>
          <el-option label="处理中" value="pending" />
          <el-option label="成功" value="success" />
          <el-option label="失败" value="fail" />
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
        <el-table-column prop="type" label="分账类型" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.type === 'platform'" size="small">平台抽佣</el-tag>
            <el-tag v-else-if="row.type === 'merchant'" type="success" size="small">商户分账</el-tag>
            <el-tag v-else-if="row.type === 'supplier'" type="warning" size="small">供应商</el-tag>
            <el-tag v-else-if="row.type === 'staff'" type="info" size="small">员工提成</el-tag>
            <el-tag v-else size="small">{{ row.type }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="receiver_name" label="接收方" min-width="120" />
        <el-table-column prop="amount" label="分账金额（元）" width="130" align="right">
          <template #default="{ row }"><span class="price">¥{{ row.amount }}</span></template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.status === 'pending'" type="warning" size="small">处理中</el-tag>
            <el-tag v-else-if="row.status === 'success'" type="success" size="small">成功</el-tag>
            <el-tag v-else type="danger" size="small">失败</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="fail_reason" label="失败原因" min-width="150" show-overflow-tooltip />
        <el-table-column prop="create_time" label="创建时间" width="160" />
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
    <el-dialog v-model="detailVisible" title="分账详情" width="600px">
      <el-descriptions :column="2" border v-if="detailData.id">
        <el-descriptions-item label="分账单号">{{ detailData.refund_no || '-' }}</el-descriptions-item>
        <el-descriptions-item label="订单号">{{ detailData.order_no }}</el-descriptions-item>
        <el-descriptions-item label="支付渠道">
          <el-tag :type="detailData.channel === 'wechat' ? 'success' : 'primary'" size="small">
            {{ detailData.channel === 'wechat' ? '微信支付' : '支付宝' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="分账类型">
          <span v-if="detailData.type === 'platform'">平台抽佣</span>
          <span v-else-if="detailData.type === 'merchant'">商户分账</span>
          <span v-else-if="detailData.type === 'supplier'">供应商分账</span>
          <span v-else-if="detailData.type === 'staff'">员工提成</span>
          <span v-else>{{ detailData.type }}</span>
        </el-descriptions-item>
        <el-descriptions-item label="接收方类型">{{ detailData.receiver_type === 'openid' ? 'OpenID' : '账户' }}</el-descriptions-item>
        <el-descriptions-item label="接收方ID">{{ detailData.receiver_id || '-' }}</el-descriptions-item>
        <el-descriptions-item label="接收方名称">{{ detailData.receiver_name || '-' }}</el-descriptions-item>
        <el-descriptions-item label="分账金额">¥{{ detailData.amount }}</el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag v-if="detailData.status === 'pending'" type="warning" size="small">处理中</el-tag>
          <el-tag v-else-if="detailData.status === 'success'" type="success" size="small">成功</el-tag>
          <el-tag v-else type="danger" size="small">失败</el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="失败原因" :span="2">{{ detailData.fail_reason || '-' }}</el-descriptions-item>
        <el-descriptions-item label="结果详情" :span="2">{{ detailData.result_msg || '-' }}</el-descriptions-item>
        <el-descriptions-item label="创建时间">{{ detailData.create_time }}</el-descriptions-item>
      </el-descriptions>
      <template #footer><el-button @click="detailVisible = false">关闭</el-button></template>
    </el-dialog>

    <!-- 创建分账弹窗 -->
    <el-dialog v-model="showCreateDialog" title="创建分账" width="500px">
      <el-form :model="createForm" :rules="createRules" ref="createFormRef" label-width="100px">
        <el-form-item label="订单号" prop="order_id">
          <el-select
            v-model="createForm.order_id"
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
        <el-form-item label="可分账金额">
          <span class="price">¥{{ availableAmount }}</span>
        </el-form-item>
        <el-form-item label="分账类型" prop="type">
          <el-select v-model="createForm.type" placeholder="请选择分账类型" style="width: 100%">
            <el-option label="平台抽佣" value="platform" />
            <el-option label="商户分账" value="merchant" />
            <el-option label="供应商分账" value="supplier" />
            <el-option label="员工提成" value="staff" />
          </el-select>
        </el-form-item>
        <el-form-item label="接收方类型">
          <el-radio-group v-model="createForm.receiver_type">
            <el-radio label="openid">OpenID</el-radio>
            <el-radio label="account">账户</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="接收方ID" prop="receiver_id">
          <el-input v-model="createForm.receiver_id" placeholder="请输入接收方ID" />
        </el-form-item>
        <el-form-item label="接收方名称">
          <el-input v-model="createForm.receiver_name" placeholder="请输入接收方名称" />
        </el-form-item>
        <el-form-item label="分账金额" prop="amount">
          <el-input-number v-model="createForm.amount" :min="0.01" :max="availableAmount" :precision="2" style="width: 100%" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showCreateDialog = false">取消</el-button>
        <el-button type="primary" :loading="createLoading" @click="submitCreate">确认创建</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Refresh, Plus } from '@element-plus/icons-vue'
import { getPayStatementList, getPayStatementDetail, createPayStatement, getAvailableAmount, getPayOrderList } from '@/api/pay'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const detailData = ref({})
const showCreateDialog = ref(false)
const createLoading = ref(false)
const orderLoading = ref(false)
const createFormRef = ref(null)
const orderOptions = ref([])
const availableAmount = ref(0)
const searchForm = reactive({ order_no: '', type: '', status: '' })
const pagination = reactive({ page: 1, limit: 10, total: 0 })
const createForm = reactive({
  order_id: null,
  type: 'platform',
  receiver_type: 'openid',
  receiver_id: '',
  receiver_name: '',
  amount: 0
})

const createRules = {
  order_id: [{ required: true, message: '请选择订单', trigger: 'change' }],
  type: [{ required: true, message: '请选择分账类型', trigger: 'change' }],
  receiver_id: [{ required: true, message: '请输入接收方ID', trigger: 'blur' }],
  amount: [{ required: true, message: '请输入分账金额', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getPayStatementList({ page: pagination.page, limit: pagination.limit, ...searchForm })
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || 0
  } catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}

const resetSearch = () => { searchForm.order_no = ''; searchForm.type = ''; searchForm.status = ''; pagination.page = 1; loadData() }

const showDetail = async (row) => {
  try {
    const res = await getPayStatementDetail({ id: row.id })
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

const onOrderChange = async (orderId) => {
  const order = orderOptions.value.find(o => o.id === orderId)
  if (order) {
    createForm.order_no = order.order_no
    try {
      const res = await getAvailableAmount({ order_id: orderId })
      availableAmount.value = res.data?.available || 0
      createForm.amount = availableAmount.value
    } catch { availableAmount.value = 0 }
  }
}

const submitCreate = async () => {
  await createFormRef.value?.validate(async (valid) => {
    if (!valid) return
    createLoading.value = true
    try {
      await createPayStatement(createForm)
      ElMessage.success('创建成功')
      showCreateDialog.value = false
      loadData()
    } catch (e) { ElMessage.error(e.message || '创建失败') } finally { createLoading.value = false }
  })
}

onMounted(() => loadData())
</script>

<style scoped>
.price { color: #F56C6C; font-weight: 500; }
.pagination-wrapper { margin-top: 16px; display: flex; justify-content: flex-end; }
</style>

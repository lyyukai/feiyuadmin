<template>
  <div class="page-container">
    <!-- 统计卡片 -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value">{{ stats.total }}</div>
        <div class="stat-label">总发送量</div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-value">{{ stats.success }}</div>
        <div class="stat-label">成功</div>
      </div>
      <div class="stat-card stat-failed">
        <div class="stat-value">{{ stats.failed }}</div>
        <div class="stat-label">失败</div>
      </div>
      <div class="stat-card stat-pending">
        <div class="stat-value">{{ stats.pending }}</div>
        <div class="stat-label">待发送</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ stats.success_rate }}%</div>
        <div class="stat-label">成功率</div>
      </div>
    </div>

    <div class="page-card">
      <div class="card-header">
        <span class="card-title">发送记录</span>
        <div class="header-actions">
          <el-date-picker v-model="dateRange" type="daterange" range-separator="至" start-placeholder="开始日期"
            end-placeholder="结束日期" value-format="YYYY-MM-DD" style="width: 240px" @change="loadData" />
          <el-select v-model="searchForm.channel_code" placeholder="筛选渠道" style="width: 140px" clearable @change="loadData">
            <el-option label="邮件" value="email" />
            <el-option label="短信" value="sms" />
            <el-option label="企微机器人" value="wechat" />
            <el-option label="站内信" value="message" />
          </el-select>
          <el-select v-model="searchForm.status" placeholder="筛选状态" style="width: 120px" clearable @change="loadData">
            <el-option label="待发送" value="0" />
            <el-option label="成功" value="1" />
            <el-option label="失败" value="2" />
          </el-select>
          <el-input v-model="searchForm.keyword" placeholder="搜索标题/内容" style="width: 180px" clearable @clear="loadData" @keyup.enter="loadData">
            <template #prefix><el-icon><Search /></el-icon></template>
          </el-input>
          <el-button @click="loadData"><el-icon><Refresh /></el-icon></el-button>
        </div>
      </div>

      <el-table :data="tableData" v-loading="loading" row-key="id">
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="channel_code" label="渠道" width="100" align="center">
          <template #default="{ row }">
            <el-tag size="small" :type="channelTagType[row.channel_code]">{{ channelMap[row.channel_code] }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="receiver" label="接收者" width="160" show-overflow-tooltip />
        <el-table-column prop="title" label="标题" min-width="180" show-overflow-tooltip />
        <el-table-column prop="content" label="内容" min-width="200" show-overflow-tooltip />
        <el-table-column prop="template_code" label="模板" width="140">
          <template #default="{ row }">
            <span v-if="row.template_code">{{ row.template_code }}</span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag size="small" :type="statusTagType[row.status]">{{ statusMap[row.status] }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="error_msg" label="错误信息" min-width="150" show-overflow-tooltip>
          <template #default="{ row }">
            <span v-if="row.status === 2" class="error-text">{{ row.error_msg }}</span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="send_time" label="发送时间" width="160" align="center" />
        <el-table-column prop="create_time" label="创建时间" width="160" align="center" />
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next"
          background
          @current-change="loadData"
          @size-change="loadData"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Search, Refresh } from '@element-plus/icons-vue'
import { getNoticeRecordLists, getNoticeRecordStatistics } from '@/api'

const loading = ref(false)
const tableData = ref([])
const dateRange = ref([])

const pagination = reactive({ page: 1, pageSize: 20, total: 0 })
const searchForm = reactive({ keyword: '', channel_code: '', status: '' })

const stats = reactive({ total: 0, success: 0, failed: 0, pending: 0, success_rate: 0 })

const channelMap = { email: '邮件', sms: '短信', wechat: '企微', message: '站内信' }
const channelTagType = { email: 'primary', sms: 'success', wechat: 'warning', message: 'info' }
const statusMap = { 0: '待发送', 1: '成功', 2: '失败' }
const statusTagType = { 0: 'info', 1: 'success', 2: 'danger' }

const loadStats = async () => {
  try {
    const params = {}
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_time = dateRange.value[0]
      params.end_time = dateRange.value[1]
    }
    const res = await getNoticeRecordStatistics(params)
    Object.assign(stats, res.data || stats)
  } catch (e) {
    console.error(e)
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      page_size: pagination.pageSize,
      keyword: searchForm.keyword,
      channel_code: searchForm.channel_code,
      status: searchForm.status
    }
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_time = dateRange.value[0]
      params.end_time = dateRange.value[1]
    }
    const res = await getNoticeRecordLists(params)
    tableData.value = res.data || []
    pagination.total = parseInt(res.headers?.['x-total'] || 0)
    loadStats()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => { loadData() })
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  margin-bottom: 16px;
}

.stat-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  border: 1px solid #ebeef5;
}

.stat-success { border-left: 3px solid #67c23a; }
.stat-failed { border-left: 3px solid #f56c6c; }
.stat-pending { border-left: 3px solid #e6a23c; }

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #303133;
  line-height: 1;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 13px;
  color: #909399;
}

.header-actions { display: flex; gap: 10px; align-items: center; }

.error-text {
  color: #f56c6c;
  font-size: 12px;
}
</style>

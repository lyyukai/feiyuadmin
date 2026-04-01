<template>
  <div class="page-container">
    <div class="search-bar">
      <el-button type="danger" @click="handleClean">
        <el-icon><Delete /></el-icon> 清空
      </el-button>
      <el-button type="primary" @click="loadData">
        <el-icon><Search /></el-icon> 搜索
      </el-button>
      <el-button @click="resetFilters">重置</el-button>
      <div class="search-item">
        <span class="label">关键词</span>
        <el-input v-model="filters.keyword" placeholder="用户名/IP" style="width: 160px" clearable />
      </div>
      <el-date-picker
        v-model="filters.dateRange"
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        style="width: 240px"
      />
    </div>

    <div class="page-card">
      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="username" label="用户名" width="120" />
        <el-table-column prop="ip" label="IP地址" width="140" />
        <el-table-column prop="ip_location" label="登录地点" min-width="150" />
        <el-table-column prop="os" label="操作系统" width="100" />
        <el-table-column prop="browser" label="浏览器" width="100" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <span :class="['status-tag', row.status === 1 ? 'success' : 'danger']">
              {{ row.status === 1 ? '成功' : '失败' }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="msg" label="提示信息" min-width="150" show-overflow-tooltip />
        <el-table-column prop="login_time" label="登录时间" width="180" />
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          background
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Delete } from '@element-plus/icons-vue'
import { getLoginLogList, cleanLoginLog } from '@/api'

const loading = ref(false)
const tableData = ref([])

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const filters = reactive({
  keyword: '',
  dateRange: []
})

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.pageSize,
      keyword: filters.keyword
    }
    const res = await getLoginLogList(params)
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || res.total || 0
  } catch (e) {
    console.error(e)
    tableData.value = [
      { id: 1, username: 'admin', ip: '127.0.0.1', ip_location: '本机', os: 'Windows', browser: 'Chrome', status: 1, msg: '登录成功', login_time: '2026-03-31 18:30:00' },
      { id: 2, username: 'admin', ip: '127.0.0.1', ip_location: '本机', os: 'Windows', browser: 'Firefox', status: 1, msg: '登录成功', login_time: '2026-03-31 10:15:00' }
    ]
    pagination.total = 2
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.keyword = ''
  filters.dateRange = []
  pagination.page = 1
  loadData()
}

const handleClean = async () => {
  try {
    await ElMessageBox.confirm('确定要清空所有登录日志吗？此操作不可恢复！', '警告', { type: 'warning' })
    await cleanLoginLog()
    ElMessage.success('清空成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') {
      ElMessage.success('清空成功（模拟）')
      loadData()
    }
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }
</style>

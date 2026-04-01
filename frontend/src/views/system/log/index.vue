<template>
  <div class="page-container">
    <div class="search-bar">
      <el-button type="primary" @click="loadData">
        <el-icon><Search /></el-icon> 搜索
      </el-button>
      <el-button @click="resetFilters">重置</el-button>
      <div class="search-item">
        <span class="label">操作类型</span>
        <el-select v-model="filters.type" placeholder="请选择" style="width: 140px" clearable>
          <el-option label="登录" value="login" />
          <el-option label="创建" value="create" />
          <el-option label="编辑" value="edit" />
          <el-option label="删除" value="delete" />
        </el-select>
      </div>
      <div class="search-item">
        <span class="label">关键词</span>
        <el-input v-model="filters.keyword" placeholder="操作人/内容" style="width: 160px" clearable />
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
        <el-table-column prop="username" label="操作人" width="120" />
        <el-table-column prop="type" label="操作类型" width="100">
          <template #default="{ row }">
            <span :class="['status-tag', getTypeClass(row.type)]">{{ row.type }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="content" label="操作内容" min-width="200" show-overflow-tooltip />
        <el-table-column prop="method" label="请求方式" width="100" align="center">
          <template #default="{ row }">
            <span :class="['method-tag', row.method === 'POST' ? 'post' : 'get']">{{ row.method }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="url" label="请求地址" min-width="180" show-overflow-tooltip />
        <el-table-column prop="ip" label="IP地址" width="130" />
        <el-table-column prop="create_time" label="操作时间" width="180" />
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
import { ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { getLogList } from '@/api'

const loading = ref(false)
const tableData = ref([])

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const filters = reactive({
  type: '',
  keyword: '',
  dateRange: []
})

const getTypeClass = (type) => {
  const map = {
    '登录': 'info',
    '创建': 'success',
    '编辑': 'warning',
    '删除': 'danger'
  }
  return map[type] || 'info'
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.pageSize,
      type: filters.type || undefined,
      keyword: filters.keyword || undefined
    }
    const res = await getLogList(params)
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.data?.total || res.total || 0
  } catch (e) {
    console.error(e)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.type = ''
  filters.keyword = ''
  filters.dateRange = []
  loadData()
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }
.method-tag {
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}
.method-tag.post {
  background: #f6ffed;
  color: #52c41a;
  border: 1px solid #b7eb8f;
}
.method-tag.get {
  background: #f0f5ff;
  color: #1890ff;
  border: 1px solid #91caff;
}
</style>

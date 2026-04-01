<template>
  <div class="message-list">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="filter-tabs">
        <span
          v-for="item in filterOptions"
          :key="item.value"
          :class="['filter-tab', { active: searchForm.is_read === item.value }]"
          @click="handleFilter(item.value)"
        >
          {{ item.label }}
          <span v-if="item.value === 0 && unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </span>
      </div>
      <div class="search-actions">
        <el-button @click="loadData" :loading="loading">
          <el-icon><Refresh /></el-icon>
        </el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card" style="border-radius: 0; box-shadow: none;">
      <el-table :data="tableData" v-loading="loading" @row-click="handleRowClick">
        <el-table-column prop="is_read" label="状态" width="80" align="center">
          <template #default="{ row }">
            <span :class="['status-dot', row.is_read ? 'read' : 'unread']"></span>
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <span :class="['type-tag', 'type-' + row.type]">
              {{ typeMap[row.type] || row.type }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="标题" min-width="200">
          <template #default="{ row }">
            <span :class="['title-text', !row.is_read && 'unread-title']">{{ row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="content" label="内容" min-width="300" show-overflow-tooltip />
        <el-table-column prop="create_time" label="时间" width="160" align="center" />
        <el-table-column label="操作" width="120" fixed="right" align="center">
          <template #default="{ row }">
            <el-button v-if="!row.is_read" link type="primary" @click.stop="handleRead(row)">标为已读</el-button>
            <el-button link type="danger" @click.stop="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          background
          @current-change="loadData"
          @size-change="loadData"
        />
      </div>
    </div>

    <!-- 详情弹窗 -->
    <el-dialog v-model="detailVisible" title="消息详情" width="560px" destroy-on-close>
      <div class="notice-detail" v-if="currentRow">
        <div class="detail-header">
          <span class="detail-type" :class="'type-' + currentRow.type">{{ typeMap[currentRow.type] || currentRow.type }}</span>
          <h3 class="detail-title">{{ currentRow.title }}</h3>
          <span class="detail-time">{{ currentRow.create_time }}</span>
        </div>
        <div class="detail-body">
          <p>{{ currentRow.content }}</p>
        </div>
      </div>
      <template #footer>
        <el-button @click="detailVisible = false">关闭</el-button>
        <el-button v-if="currentRow && !currentRow.is_read" type="primary" @click="handleRead(currentRow)">标为已读</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Refresh } from '@element-plus/icons-vue'
import { getNoticeLists, readNotice, deleteNotice } from '@/api'

const loading = ref(false)
const tableData = ref([])
const detailVisible = ref(false)
const currentRow = ref(null)

const pagination = reactive({ page: 1, pageSize: 20, total: 0 })
const searchForm = reactive({ is_read: '' })

const filterOptions = [
  { label: '全部', value: '' },
  { label: '未读', value: 0 },
  { label: '已读', value: 1 }
]

const typeMap = { 1: '系统通知', 2: '业务通知', 3: '预警通知' }
const unreadCount = computed(() => tableData.value.filter(row => !row.is_read).length)

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.pageSize,
      is_read: searchForm.is_read
    }
    const res = await getNoticeLists(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleFilter = (value) => {
  searchForm.is_read = value
  pagination.page = 1
  loadData()
}

const handleRowClick = async (row) => {
  currentRow.value = row
  detailVisible.value = true
  if (!row.is_read) {
    await readNotice(row.id)
    row.is_read = 1
  }
}

const handleRead = async (row) => {
  try {
    await readNotice(row.id)
    row.is_read = 1
    ElMessage.success('已标记为已读')
    if (detailVisible.value) {
      detailVisible.value = false
    }
  } catch (e) {
    ElMessage.error('操作失败')
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除该消息吗？`, '提示', { type: 'warning' })
    await deleteNotice(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

onMounted(() => { loadData() })
</script>

<style scoped>
.filter-tabs { display: flex; gap: 4px; }
.filter-tab {
  padding: 6px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  color: #606266;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 4px;
}
.filter-tab:hover { color: #409eff; }
.filter-tab.active { background: #ecf5ff; color: #409eff; font-weight: 500; }
.badge { background: #f56c6c; color: #fff; border-radius: 10px; padding: 0 6px; font-size: 11px; line-height: 16px; }
.pagination-wrap { display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding: 12px 16px; border-top: 1px solid #f0f0f0; }
.status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.status-dot.unread { background: #f56c6c; }
.status-dot.read { background: #dcdfe6; }
.type-tag { padding: 2px 8px; border-radius: 4px; font-size: 12px; }
.type-1 { background: #ecf5ff; color: #409eff; }
.type-2 { background: #f0f9eb; color: #67c23a; }
.type-3 { background: #fef0f0; color: #f56c6c; }
.title-text { font-size: 14px; color: #606266; }
.title-text.unread-title { color: #303133; font-weight: 600; }
.notice-detail .detail-header { border-bottom: 1px solid #f0f0f0; padding-bottom: 12px; margin-bottom: 12px; }
.detail-type { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-bottom: 8px; }
.detail-title { margin: 0 0 8px; font-size: 16px; font-weight: 600; color: #303133; }
.detail-time { font-size: 12px; color: #909399; }
.detail-body { font-size: 14px; color: #606266; line-height: 1.8; }
</style>

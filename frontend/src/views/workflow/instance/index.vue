<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-actions">
        <el-select v-model="searchWorkflow" placeholder="选择流程" style="width: 180px; margin-right: 10px" clearable>
          <el-option v-for="wf in workflowList" :key="wf.id" :label="wf.name" :value="wf.id" />
        </el-select>
        <el-input
          v-model="searchKeyword"
          placeholder="搜索实例编号/标题"
          style="width: 200px; margin-right: 10px"
          clearable
          @keyup.enter="loadData"
        />
        <el-select v-model="searchStatus" placeholder="状态" style="width: 120px; margin-right: 10px" clearable>
          <el-option label="进行中" :value="0" />
          <el-option label="已完成" :value="1" />
          <el-option label="已驳回" :value="2" />
          <el-option label="已撤回" :value="3" />
        </el-select>
        <el-button type="primary" @click="loadData">
          <el-icon><Search /></el-icon> 搜索
        </el-button>
        <el-button @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">流程实例列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="instance_no" label="实例编号" min-width="160">
          <template #default="{ row }">
            <code class="code-text">{{ row.instance_no }}</code>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="标题" min-width="150" show-overflow-tooltip />
        <el-table-column prop="workflow_name" label="流程名称" min-width="120" />
        <el-table-column prop="apply_user_name" label="申请人" width="100" align="center" />
        <el-table-column prop="current_node_name" label="当前节点" min-width="120">
          <template #default="{ row }">
            <el-tag size="small" type="info">{{ row.current_node_name || '-' }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-tag :type="getStatusType(row.status)" size="small">
              {{ getStatusText(row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="发起时间" min-width="160" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleViewDetail(row)">详情</el-button>
            <el-button link type="primary" @click="handleViewHistory(row)">历史</el-button>
            <el-button link type="danger" @click="handleWithdraw(row)" v-if="row.status === 0 && row.apply_user === currentUserId">撤回</el-button>
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
    <el-dialog v-model="detailDialogVisible" title="实例详情" width="700px" destroy-on-close>
      <div class="instance-detail" v-if="currentInstance">
        <div class="detail-header">
          <h3>{{ currentInstance.title }}</h3>
          <el-tag :type="getStatusType(currentInstance.status)">
            {{ getStatusText(currentInstance.status) }}
          </el-tag>
        </div>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="实例编号">{{ currentInstance.instance_no }}</el-descriptions-item>
          <el-descriptions-item label="流程名称">{{ currentInstance.workflow_name }}</el-descriptions-item>
          <el-descriptions-item label="申请人">{{ currentInstance.apply_user_name }}</el-descriptions-item>
          <el-descriptions-item label="当前节点">{{ currentInstance.current_node_name || '-' }}</el-descriptions-item>
          <el-descriptions-item label="发起时间">{{ currentInstance.create_time }}</el-descriptions-item>
          <el-descriptions-item label="结束时间">{{ currentInstance.end_time || '-' }}</el-descriptions-item>
        </el-descriptions>

        <div class="form-data-section" v-if="currentInstance.form_data">
          <h4>表单数据</h4>
          <el-descriptions :column="1" border size="small">
            <el-descriptions-item v-for="(value, key) in currentInstance.form_data" :key="key" :label="key">
              {{ value }}
            </el-descriptions-item>
          </el-descriptions>
        </div>

        <div class="tasks-section">
          <h4>审批记录</h4>
          <el-table :data="instanceTasks" size="small" border>
            <el-table-column prop="node_name" label="节点" width="120" />
            <el-table-column prop="assignee_name" label="审批人" width="100" />
            <el-table-column prop="task_type_text" label="类型" width="80" />
            <el-table-column prop="action_status_text" label="状态" width="90">
              <template #default="{ row }">
                <el-tag size="small" :type="getTaskStatusType(row.action_status)">
                  {{ row.action_status_text }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="action_remark" label="备注" min-width="120" show-overflow-tooltip />
            <el-table-column prop="action_time" label="审批时间" width="160" />
          </el-table>
        </div>
      </div>
    </el-dialog>

    <!-- 历史弹窗 -->
    <el-dialog v-model="historyDialogVisible" title="流程历史" width="700px" destroy-on-close>
      <el-timeline v-if="historyData.tasks?.length">
        <el-timeline-item
          v-for="task in historyData.tasks"
          :key="task.id"
          :color="getTimelineColor(task.action_status)"
        >
          <div class="timeline-item">
            <div class="timeline-header">
              <strong>{{ task.node_name }}</strong>
              <el-tag size="small" :type="getTaskStatusType(task.action_status)" style="margin-left: 10px">
                {{ task.action_status_text }}
              </el-tag>
            </div>
            <div class="timeline-content">
              <p>审批人: {{ task.assignee_name }}</p>
              <p v-if="task.action_remark">备注: {{ task.action_remark }}</p>
              <p>时间: {{ task.action_time || task.create_time }}</p>
            </div>
          </div>
        </el-timeline-item>
      </el-timeline>
      <el-empty v-else description="暂无历史记录" />
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import workflowApi from '@/api/workflow'

const loading = ref(false)
const tableData = ref([])
const workflowList = ref([])
const detailDialogVisible = ref(false)
const historyDialogVisible = ref(false)
const currentInstance = ref(null)
const instanceTasks = ref([])
const historyData = ref({})

const searchKeyword = ref('')
const searchStatus = ref('')
const searchWorkflow = ref('')
const currentUserId = computed(() => {
  // 从localStorage获取当前用户ID
  const userInfo = JSON.parse(localStorage.getItem('userInfo') || '{}')
  return userInfo.id || 0
})

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const getStatusText = (status) => {
  const texts = { 0: '进行中', 1: '已完成', 2: '已驳回', 3: '已撤回' }
  return texts[status] || '未知'
}

const getStatusType = (status) => {
  const types = { 0: 'primary', 1: 'success', 2: 'danger', 3: 'info' }
  return types[status] || 'info'
}

const getTaskStatusType = (status) => {
  const types = { 0: 'warning', 1: 'success', 2: 'danger', 3: 'info', 4: 'primary' }
  return types[status] || 'info'
}

const getTimelineColor = (status) => {
  const colors = { 0: '#409eff', 1: '#67c23a', 2: '#f56c6c', 3: '#909399' }
  return colors[status] || '#409eff'
}

const loadWorkflowList = async () => {
  try {
    const res = await workflowApi.lists({ page: 1, page_size: 100, status: 1 })
    workflowList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      page_size: pagination.pageSize
    }
    if (searchKeyword.value) params.keyword = searchKeyword.value
    if (searchStatus.value !== '') params.status = searchStatus.value
    if (searchWorkflow.value) params.workflow_id = searchWorkflow.value

    const res = await workflowApi.instanceLists(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchKeyword.value = ''
  searchStatus.value = ''
  searchWorkflow.value = ''
  pagination.page = 1
  loadData()
}

const handleViewDetail = async (row) => {
  try {
    const res = await workflowApi.instanceDetail(row.id)
    currentInstance.value = res
    instanceTasks.value = res.tasks || []
    detailDialogVisible.value = true
  } catch (e) {
    console.error(e)
  }
}

const handleViewHistory = async (row) => {
  try {
    const res = await workflowApi.instanceHistory(row.id)
    historyData.value = res
    historyDialogVisible.value = true
  } catch (e) {
    console.error(e)
  }
}

const handleWithdraw = async (row) => {
  try {
    await ElMessageBox.confirm(`确定撤回申请"${row.title}"吗？`, '提示', { type: 'warning' })
    await workflowApi.withdraw(row.id)
    ElMessage.success('撤回成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('撤回失败')
  }
}

onMounted(() => {
  loadWorkflowList()
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }

.search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.search-actions {
  display: flex;
  align-items: center;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.card-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-top: 1px solid #f0f0f0;
}

.code-text {
  font-family: Consolas, Monaco, monospace;
  font-size: 12px;
  background: #f5f7fa;
  padding: 2px 6px;
  border-radius: 4px;
  color: #409eff;
}

.instance-detail {
  padding: 10px 0;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.detail-header h3 {
  margin: 0;
  font-size: 16px;
}

.form-data-section,
.tasks-section {
  margin-top: 20px;
}

.form-data-section h4,
.tasks-section h4 {
  margin: 0 0 10px 0;
  font-size: 14px;
  color: #303133;
}

.timeline-item {
  padding-bottom: 10px;
}

.timeline-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.timeline-content {
  font-size: 13px;
  color: #606266;
  line-height: 1.6;
}

.timeline-content p {
  margin: 4px 0;
}
</style>

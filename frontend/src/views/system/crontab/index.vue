<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增任务
      </el-button>
      <div class="search-actions">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索任务名称/命令"
          style="width: 200px; margin-right: 10px"
          clearable
          @keyup.enter="loadData"
        />
        <el-select v-model="searchStatus" placeholder="状态" style="width: 120px; margin-right: 10px" clearable>
          <el-option label="运行中" :value="1" />
          <el-option label="已暂停" :value="0" />
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
        <span class="card-title">定时任务列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="name" label="任务名称" min-width="150" />
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="{ row }">
            <span class="type-tag">{{ getTypeText(row.type) }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="rule" label="执行规则" min-width="130">
          <template #default="{ row }">
            <code class="rule-code">{{ row.rule }}</code>
          </template>
        </el-table-column>
        <el-table-column prop="command" label="执行命令" min-width="180" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'" size="small">
              {{ row.status === 1 ? '运行中' : '已暂停' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="retry_times" label="重试" width="60" align="center">
          <template #default="{ row }">
            {{ row.retry_times }}次
          </template>
        </el-table-column>
        <el-table-column prop="last_run_time" label="上次执行" min-width="150">
          <template #default="{ row }">
            {{ row.last_run_time || '-' }}
          </template>
        </el-table-column>
        <el-table-column prop="next_run_time" label="下次执行" min-width="150">
          <template #default="{ row }">
            <span v-if="row.status === 1">{{ row.next_time || '-' }}</span>
            <span v-else class="text-muted">-</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="280" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleExecute(row)" :loading="row.executing">执行</el-button>
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="warning" @click="handlePauseResume(row)">
              {{ row.status === 1 ? '暂停' : '恢复' }}
            </el-button>
            <el-button link type="info" @click="handleViewLog(row)">日志</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
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

    <!-- 任务弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="任务名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入任务名称" />
        </el-form-item>
        <el-form-item label="任务类型" prop="type">
          <el-radio-group v-model="form.type">
            <el-radio :label="1">Shell脚本</el-radio>
            <el-radio :label="2">PHP类</el-radio>
            <el-radio :label="3">URL回调</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="执行规则" prop="rule">
          <el-input v-model="form.rule" placeholder="如: */5 * * * * (每5分钟)" />
          <div class="form-tip">
            格式: 分 时 日 月 周 (Linux crontab格式)
            <br />常用示例: <code>*/5 * * * *</code>每5分钟 | <code>0 * * * *</code>每小时 | <code>0 0 * * *</code>每天
          </div>
        </el-form-item>
        <el-form-item label="执行命令" prop="command">
          <el-input
            v-model="form.command"
            type="textarea"
            :rows="3"
            :placeholder="commandPlaceholder"
          />
          <div class="form-tip" v-if="form.type === 2">
            PHP类格式: app\cron\MyTask 或 MyTask (需包含 run() 方法)
          </div>
        </el-form-item>
        <el-form-item label="失败重试" prop="retry_times">
          <el-select v-model="form.retry_times" style="width: 200px">
            <el-option v-for="n in 6" :key="n-1" :label="`${n-1}次`" :value="n-1" />
          </el-select>
          <span class="form-tip" style="margin-left: 10px">执行失败后自动重试次数(0-5)</span>
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">运行中</el-radio>
            <el-radio :label="0">暂停</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" type="textarea" :rows="2" placeholder="请输入备注信息" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitLoading">确定</el-button>
      </template>
    </el-dialog>

    <!-- 日志弹窗 -->
    <el-dialog v-model="logDialogVisible" title="执行日志" width="900px" destroy-on-close>
      <div class="log-toolbar">
        <el-button type="danger" size="small" @click="handleClearLogs">清空日志</el-button>
        <el-button size="small" @click="loadLogs">刷新</el-button>
      </div>
      <el-table :data="logData" v-loading="logLoading" max-height="400">
        <el-table-column prop="id" label="ID" width="80" align="center" />
        <el-table-column prop="execute_time" label="执行时间" width="160" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'" size="small">
              {{ row.status === 1 ? '成功' : '失败' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="duration" label="耗时" width="100" align="center">
          <template #default="{ row }">
            {{ formatDuration(row.duration) }}
          </template>
        </el-table-column>
        <el-table-column prop="output" label="执行输出" min-width="200">
          <template #default="{ row }">
            <el-tooltip :content="row.output || '(无输出)'" placement="top" :max-length="500">
              <span class="log-output">{{ row.output || '(无输出)' }}</span>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-wrap">
        <el-pagination
          v-model:current-page="logPagination.page"
          v-model:page-size="logPagination.pageSize"
          :page-sizes="[10, 20, 50]"
          :total="logPagination.total"
          layout="total, sizes, prev, pager, next"
          background
          small
          @current-change="loadLogs"
          @size-change="loadLogs"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search } from '@element-plus/icons-vue'
import { getCrontabLists, addCrontab, editCrontab, deleteCrontab, executeCrontab, pauseCrontab, resumeCrontab, getCrontabLogLists, clearCrontabLogs } from '@/api'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)
const searchKeyword = ref('')
const searchStatus = ref('')

// 日志相关
const logDialogVisible = ref(false)
const logLoading = ref(false)
const logData = ref([])
const currentLogTask = ref(null)
const logPagination = reactive({
  page: 1,
  pageSize: 10,
  total: 0
})

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const form = reactive({
  id: '',
  name: '',
  type: 1,
  rule: '',
  command: '',
  status: 1,
  retry_times: 0,
  remark: ''
})

const rules = {
  name: [{ required: true, message: '请输入任务名称', trigger: 'blur' }],
  rule: [{ required: true, message: '请输入执行规则', trigger: 'blur' }],
  command: [{ required: true, message: '请输入执行命令', trigger: 'blur' }]
}

const commandPlaceholder = computed(() => {
  const placeholders = {
    1: '请输入Shell命令，如: php /www/test.php 或 echo "hello"',
    2: '请输入PHP类名，如: app\\cron\\MyTask 或 MyTask',
    3: '请输入URL地址，如: https://example.com/api/cron'
  }
  return placeholders[form.type] || ''
})

const getTypeText = (type) => {
  const types = { 1: 'Shell', 2: 'PHP类', 3: 'URL' }
  return types[type] || '未知'
}

const formatDuration = (duration) => {
  if (!duration && duration !== 0) return '-'
  if (duration < 1) return (duration * 1000).toFixed(0) + 'ms'
  if (duration < 60) return duration.toFixed(2) + 's'
  const min = Math.floor(duration / 60)
  const sec = (duration % 60).toFixed(0)
  return `${min}分${sec}秒`
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

    const res = await getCrontabLists(params)
    tableData.value = (res.data || []).map(item => ({ ...item, executing: false }))
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
  pagination.page = 1
  loadData()
}

const handleAdd = () => {
  dialogTitle.value = '新增任务'
  Object.assign(form, {
    id: '',
    name: '',
    type: 1,
    rule: '',
    command: '',
    status: 1,
    retry_times: 0,
    remark: ''
  })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑任务'
  Object.assign(form, { ...row })
  dialogVisible.value = true
}

const handleExecute = async (row) => {
  try {
    await ElMessageBox.confirm(`确定立即执行任务"${row.name}"吗？`, '提示', { type: 'warning' })

    const index = tableData.value.findIndex(item => item.id === row.id)
    if (index !== -1) tableData.value[index].executing = true

    const res = await executeCrontab(row.id)

    if (res.code === 0) {
      ElMessage.success(`执行成功${res.data?.duration ? `，耗时: ${formatDuration(res.data.duration)}` : ''}`)
    } else {
      ElMessage.error(res.msg || '执行失败')
    }
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('执行失败')
  } finally {
    if (index !== -1) tableData.value[index].executing = false
  }
}

const handlePauseResume = async (row) => {
  const action = row.status === 1 ? '暂停' : '恢复'
  try {
    await ElMessageBox.confirm(`确定${action}任务"${row.name}"吗？`, '提示', { type: 'warning' })

    if (row.status === 1) {
      await pauseCrontab(row.id)
    } else {
      await resumeCrontab(row.id)
    }

    ElMessage.success(`${action}成功`)
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(`${action}失败`)
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除任务"${row.name}"吗？此操作将同时删除该任务的所有日志！`, '提示', { type: 'warning' })
    await deleteCrontab(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    if (form.id) {
      await editCrontab(form)
      ElMessage.success('编辑成功')
    } else {
      await addCrontab(form)
      ElMessage.success('新增成功')
    }
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证失败
  } finally {
    submitLoading.value = false
  }
}

const handleViewLog = (row) => {
  currentLogTask.value = row
  logPagination.page = 1
  logDialogVisible.value = true
  loadLogs()
}

const loadLogs = async () => {
  if (!currentLogTask.value) return
  logLoading.value = true
  try {
    const params = {
      page: logPagination.page,
      page_size: logPagination.pageSize,
      task_id: currentLogTask.value.id
    }
    const res = await getCrontabLogLists(params)
    logData.value = res.data || []
    logPagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    logLoading.value = false
  }
}

const handleClearLogs = async () => {
  if (!currentLogTask.value) return
  try {
    await ElMessageBox.confirm('确定清空该任务的所有日志吗？', '提示', { type: 'warning' })
    await clearCrontabLogs(currentLogTask.value.id)
    ElMessage.success('日志已清空')
    loadLogs()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('清空失败')
  }
}

onMounted(() => {
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

.rule-code {
  font-family: Consolas, Monaco, monospace;
  font-size: 12px;
  background: #f5f7fa;
  padding: 2px 6px;
  border-radius: 4px;
  color: #409eff;
}

.type-tag {
  font-size: 12px;
  color: #909399;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
  line-height: 1.5;
}

.form-tip code {
  background: #f5f7fa;
  padding: 1px 4px;
  border-radius: 3px;
  font-family: Consolas, Monaco, monospace;
  color: #409eff;
}

.text-muted {
  color: #c0c4cc;
}

.log-toolbar {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-bottom: 12px;
}

.log-output {
  display: block;
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: Consolas, Monaco, monospace;
  font-size: 12px;
}
</style>

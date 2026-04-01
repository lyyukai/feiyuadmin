<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <span class="page-title">我的待办</span>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="instance.instance_no" label="实例编号" min-width="160">
          <template #default="{ row }">
            <code class="code-text">{{ row.instance?.instance_no || '-' }}</code>
          </template>
        </el-table-column>
        <el-table-column prop="instance.title" label="标题" min-width="150" show-overflow-tooltip />
        <el-table-column prop="instance.workflow_name" label="流程名称" min-width="120" />
        <el-table-column prop="node_name" label="当前节点" min-width="120">
          <template #default="{ row }">
            <el-tag size="small" type="primary">{{ row.node_name }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="instance.apply_user_name" label="申请人" width="100" align="center" />
        <el-table-column prop="task_type_text" label="任务类型" width="90" align="center">
          <template #default="{ row }">
            <el-tag size="small" :type="row.task_type === 'counter_sign' ? 'warning' : 'default'">
              {{ row.task_type_text }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="instance.create_time" label="发起时间" min-width="160" />
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleApprove(row)">审批</el-button>
            <el-button link type="success" @click="handleTransfer(row)">转交</el-button>
            <el-button link type="warning" @click="handleRemind(row)">催办</el-button>
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

    <!-- 审批弹窗 -->
    <el-dialog v-model="approveDialogVisible" title="审批" width="600px" destroy-on-close>
      <el-form ref="approveFormRef" :model="approveForm" label-width="100px">
        <el-form-item label="实例编号">
          {{ currentTask?.instance?.instance_no }}
        </el-form-item>
        <el-form-item label="标题">
          {{ currentTask?.instance?.title }}
        </el-form-item>
        <el-form-item label="流程名称">
          {{ currentTask?.instance?.workflow_name }}
        </el-form-item>
        <el-form-item label="申请人">
          {{ currentTask?.instance?.apply_user_name }}
        </el-form-item>

        <template v-if="currentTask?.instance?.form_data">
          <el-divider>表单数据</el-divider>
          <el-form-item v-for="(value, key) in currentTask.instance.form_data" :key="key" :label="key">
            {{ value }}
          </el-form-item>
        </template>

        <el-divider />
        <el-form-item label="审批意见" prop="remark">
          <el-input
            v-model="approveForm.remark"
            type="textarea"
            :rows="3"
            placeholder="请输入审批意见"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button type="danger" @click="submitApprove('reject')">驳回</el-button>
        <el-button type="success" @click="submitApprove('approve')">通过</el-button>
      </template>
    </el-dialog>

    <!-- 转交弹窗 -->
    <el-dialog v-model="transferDialogVisible" title="转交任务" width="500px" destroy-on-close>
      <el-form ref="transferFormRef" :model="transferForm" label-width="100px">
        <el-form-item label="转交给">
          <el-select v-model="transferForm.transfer_to" placeholder="请选择用户" filterable style="width: 100%">
            <el-option v-for="user in userList" :key="user.id" :label="user.name" :value="user.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="转交原因">
          <el-input
            v-model="transferForm.remark"
            type="textarea"
            :rows="3"
            placeholder="请输入转交原因"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="transferDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitTransfer" :loading="submitLoading">确定转交</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import workflowApi from '@/api/workflow'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const approveDialogVisible = ref(false)
const transferDialogVisible = ref(false)
const currentTask = ref(null)
const approveFormRef = ref(null)
const transferFormRef = ref(null)

const userList = ref([
  { id: 1, name: '张三' },
  { id: 2, name: '李四' },
  { id: 3, name: '王五' },
  { id: 4, name: '赵六' }
])

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const approveForm = reactive({
  remark: ''
})

const transferForm = reactive({
  transfer_to: '',
  transfer_to_name: '',
  remark: ''
})

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      page_size: pagination.pageSize
    }

    const res = await workflowApi.todoList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleApprove = (row) => {
  currentTask.value = row
  approveForm.remark = ''
  approveDialogVisible.value = true
}

const submitApprove = async (action) => {
  try {
    if (action === 'reject' && !approveForm.remark) {
      return ElMessage.warning('驳回时必须填写审批意见')
    }

    await workflowApi.approve({
      task_id: currentTask.value.id,
      action: action,
      remark: approveForm.remark
    })

    ElMessage.success(action === 'approve' ? '审批通过' : '已驳回')
    approveDialogVisible.value = false
    loadData()
  } catch (e) {
    ElMessage.error('操作失败')
  }
}

const handleTransfer = (row) => {
  currentTask.value = row
  transferForm.transfer_to = ''
  transferForm.transfer_to_name = ''
  transferForm.remark = ''
  transferDialogVisible.value = true
}

const submitTransfer = async () => {
  if (!transferForm.transfer_to) {
    return ElMessage.warning('请选择转交目标')
  }

  const user = userList.value.find(u => u.id === transferForm.transfer_to)
  transferForm.transfer_to_name = user?.name || ''

  submitLoading.value = true
  try {
    await workflowApi.approve({
      task_id: currentTask.value.id,
      action: 'transfer',
      remark: transferForm.remark,
      transfer_to: transferForm.transfer_to,
      transfer_to_name: transferForm.transfer_to_name
    })

    ElMessage.success('已转交')
    transferDialogVisible.value = false
    loadData()
  } catch (e) {
    ElMessage.error('转交失败')
  } finally {
    submitLoading.value = false
  }
}

const handleRemind = async (row) => {
  try {
    await workflowApi.approve({
      task_id: row.id,
      action: 'remind'
    })
    ElMessage.success('已催办')
    loadData()
  } catch (e) {
    ElMessage.error('催办失败')
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

.page-title {
  font-size: 16px;
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
</style>

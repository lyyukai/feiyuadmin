<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-select v-model="searchForm.account_id" placeholder="选择公众号" clearable style="width: 180px">
          <el-option v-for="item in accountList" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <el-select v-model="searchForm.status" placeholder="关注状态" clearable style="width: 120px">
          <el-option label="已关注" :value="1" />
          <el-option label="未关注" :value="0" />
        </el-select>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchForm.keyword" placeholder="昵称/OPENID/备注" style="width: 200px" clearable @keyup.enter="loadData" />
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 统计卡片 -->
    <el-row :gutter="20" class="statistics-row">
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value">{{ statistics.total || 0 }}</div>
            <div class="stat-label">总粉丝数</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #67c23a">{{ statistics.active || 0 }}</div>
            <div class="stat-label">已关注</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #909399">{{ statistics.inactive || 0 }}</div>
            <div class="stat-label">未关注</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never">
          <div class="stat-item">
            <div class="stat-value" style="color: #f56c6c">{{ statistics.blacklist || 0 }}</div>
            <div class="stat-label">黑名单</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="openid" label="OPENID" min-width="180" show-overflow-tooltip />
        <el-table-column prop="nickname" label="昵称" min-width="120">
          <template #default="{ row }">
            <div style="display: flex; align-items: center; gap: 8px;">
              <el-avatar v-if="row.avatar" :src="row.avatar" size="small" />
              <span>{{ row.nickname || '-' }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="gender" label="性别" width="80" align="center">
          <template #default="{ row }">
            {{ row.gender === 1 ? '男' : row.gender === 2 ? '女' : '未知' }}
          </template>
        </el-table-column>
        <el-table-column prop="province" label="地区" min-width="120">
          <template #default="{ row }">
            {{ row.province || '' }}{{ row.city ? ' ' + row.city : '' }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
              {{ row.status === 1 ? '已关注' : '未关注' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="remark" label="备注" min-width="120" show-overflow-tooltip />
        <el-table-column prop="subscribe_time" label="关注时间" width="180" />
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openRemark(row)">备注</el-button>
            <el-button link :type="row.blacklist === 1 ? 'success' : 'danger'" size="small" @click="toggleBlacklist(row)">
              {{ row.blacklist === 1 ? '移除黑名单' : '拉黑' }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.limit"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="loadData"
          @current-change="loadData"
        />
      </div>
    </el-card>

    <!-- 备注弹窗 -->
    <el-dialog v-model="remarkVisible" title="设置备注" width="400px" destroy-on-close>
      <el-form :model="remarkForm" label-width="80px">
        <el-form-item label="备注">
          <el-input v-model="remarkForm.remark" placeholder="请输入备注" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="remarkVisible = false">取消</el-button>
        <el-button type="primary" @click="saveRemark">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh } from '@element-plus/icons-vue'
import { getWechatAccountList, getWechatFansList, getWechatFansStatistics, updateWechatFansRemark, setWechatFansBlacklist } from '@/api/wechat'

const loading = ref(false)
const tableData = ref([])
const accountList = ref([])
const statistics = ref({})

const searchForm = reactive({
  account_id: '',
  status: '',
  keyword: ''
})

const pagination = reactive({
  page: 1,
  limit: 10,
  total: 0
})

const remarkVisible = ref(false)
const remarkForm = reactive({
  id: 0,
  remark: ''
})

// 加载公众号列表
const loadAccounts = async () => {
  try {
    const res = await getWechatAccountList({ page: 1, limit: 100 })
    accountList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

// 加载粉丝列表
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      limit: pagination.limit,
      ...searchForm
    }
    const res = await getWechatFansList(params)
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 加载统计数据
const loadStatistics = async () => {
  try {
    const params = {}
    if (searchForm.account_id) params.account_id = searchForm.account_id
    const res = await getWechatFansStatistics(params)
    statistics.value = res.data || {}
  } catch (e) {
    console.error(e)
  }
}

// 重置搜索
const resetSearch = () => {
  searchForm.account_id = ''
  searchForm.status = ''
  searchForm.keyword = ''
  pagination.page = 1
  loadData()
  loadStatistics()
}

// 设置备注
const openRemark = (row) => {
  remarkForm.id = row.id
  remarkForm.remark = row.remark || ''
  remarkVisible.value = true
}

const saveRemark = async () => {
  try {
    await updateWechatFansRemark(remarkForm)
    ElMessage.success('设置成功')
    remarkVisible.value = false
    loadData()
  } catch (e) {
    console.error(e)
  }
}

// 切换黑名单状态
const toggleBlacklist = async (row) => {
  const action = row.blacklist === 1 ? '移除' : '加入'
  try {
    await ElMessageBox.confirm(`确定${action}该粉丝到黑名单？`, '提示')
    await setWechatFansBlacklist({
      id: row.id,
      blacklist: row.blacklist === 1 ? 0 : 1
    })
    ElMessage.success('设置成功')
    loadData()
    loadStatistics()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

onMounted(() => {
  loadAccounts()
  loadData()
  loadStatistics()
})
</script>

<style scoped>
.statistics-row {
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #303133;
}

.stat-label {
  font-size: 14px;
  color: #909399;
  margin-top: 8px;
}

.pagination-container {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}
</style>

<template>
  <div class="dashboard">
    <!-- 统计卡片 -->
    <el-row :gutter="16" class="stat-row">
      <el-col :xs="12" :sm="6">
        <div class="stat-card stat-users">
          <div class="stat-icon">
            <el-icon><User /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.userCount }}</div>
            <div class="stat-label">用户总数</div>
          </div>
        </div>
      </el-col>
      <el-col :xs="12" :sm="6">
        <div class="stat-card stat-roles">
          <div class="stat-icon">
            <el-icon><Stamp /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.roleCount }}</div>
            <div class="stat-label">角色数量</div>
          </div>
        </div>
      </el-col>
      <el-col :xs="12" :sm="6">
        <div class="stat-card stat-visits">
          <div class="stat-icon">
            <el-icon><TrendCharts /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.todayVisits }}</div>
            <div class="stat-label">今日访问</div>
          </div>
        </div>
      </el-col>
      <el-col :xs="12" :sm="6">
        <div class="stat-card stat-operations">
          <div class="stat-icon">
            <el-icon><Document /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.operationCount }}</div>
            <div class="stat-label">操作日志</div>
          </div>
        </div>
      </el-col>
    </el-row>

    <!-- 快捷入口 & 最新用户 -->
    <el-row :gutter="16" class="content-row">
      <el-col :xs="24" :sm="12">
        <el-card class="quick-entry-card" shadow="never">
          <template #header>
            <span>快捷入口</span>
          </template>
          <div class="quick-entry-grid">
            <div class="quick-entry-item" @click="$router.push('/system/user')">
              <el-icon><User /></el-icon>
              <span>用户管理</span>
            </div>
            <div class="quick-entry-item" @click="$router.push('/system/role')">
              <el-icon><Stamp /></el-icon>
              <span>角色管理</span>
            </div>
            <div class="quick-entry-item" @click="$router.push('/system/menu')">
              <el-icon><Menu /></el-icon>
              <span>菜单管理</span>
            </div>
            <div class="quick-entry-item" @click="$router.push('/system/dept')">
              <el-icon><OfficeBuilding /></el-icon>
              <span>部门管理</span>
            </div>
            <div class="quick-entry-item" @click="$router.push('/system/post')">
              <el-icon><Briefcase /></el-icon>
              <span>岗位管理</span>
            </div>
            <div class="quick-entry-item" @click="$router.push('/system/config')">
              <el-icon><Tools /></el-icon>
              <span>系统配置</span>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :xs="24" :sm="12">
        <el-card class="latest-users-card" shadow="never">
          <template #header>
            <span>最新用户</span>
          </template>
          <el-table :data="latestUsers" style="width: 100%" size="small">
            <el-table-column prop="username" label="用户名" />
            <el-table-column prop="nickname" label="昵称" />
            <el-table-column prop="login_time" label="最后登录" width="160">
              <template #default="{ row }">
                {{ row.login_time || '从未登录' }}
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>

    <!-- 最新操作日志 -->
    <el-row :gutter="16" class="content-row">
      <el-col :span="24">
        <el-card class="logs-card" shadow="never">
          <template #header>
            <span>最新操作日志</span>
            <el-button link type="primary" @click="$router.push('/system/log')">查看更多</el-button>
          </template>
          <el-table :data="latestLogs" style="width: 100%" size="small">
            <el-table-column prop="username" label="操作人" width="100" />
            <el-table-column prop="type" label="操作类型" width="100" />
            <el-table-column prop="content" label="操作内容" show-overflow-tooltip />
            <el-table-column prop="ip" label="IP地址" width="130" />
            <el-table-column prop="create_time" label="操作时间" width="180" />
          </el-table>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { User, Stamp, TrendCharts, Document, Menu, OfficeBuilding, Briefcase, Tools } from '@element-plus/icons-vue'

const stats = ref({
  userCount: 0,
  roleCount: 0,
  todayVisits: 0,
  operationCount: 0
})

const latestUsers = ref([])
const latestLogs = ref([])

const loadStats = async () => {
  // 模拟数据，实际应该从API获取
  stats.value = {
    userCount: 4,
    roleCount: 3,
    todayVisits: 128,
    operationCount: 56
  }
}

const loadLatestUsers = async () => {
  latestUsers.value = [
    { username: 'admin', nickname: '管理员', login_time: '2026-03-31 18:00:00' },
    { username: 'zhangsan', nickname: '张三', login_time: '2026-03-31 17:30:00' },
    { username: 'lisi', nickname: '李四', login_time: '2026-03-31 16:45:00' }
  ]
}

const loadLatestLogs = async () => {
  latestLogs.value = [
    { username: 'admin', type: '登录', content: '用户登录系统', ip: '127.0.0.1', create_time: '2026-03-31 18:00:00' },
    { username: 'admin', type: '编辑', content: '修改用户信息', ip: '127.0.0.1', create_time: '2026-03-31 17:55:00' },
    { username: 'admin', type: '创建', content: '新增角色：普通员工', ip: '127.0.0.1', create_time: '2026-03-31 17:30:00' }
  ]
}

onMounted(() => {
  loadStats()
  loadLatestUsers()
  loadLatestLogs()
})
</script>

<style scoped>
.dashboard { padding: 0; }

.stat-row { margin-bottom: 16px; }

.stat-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
}

.stat-users .stat-icon { background: #e6f7ff; color: #1890ff; }
.stat-roles .stat-icon { background: #fff7e6; color: #fa8c16; }
.stat-visits .stat-icon { background: #e6fffb; color: #52c41a; }
.stat-operations .stat-icon { background: #fff1f0; color: #ff4d4f; }

.stat-info { flex: 1; }

.stat-value {
  font-size: 28px;
  font-weight: 600;
  color: #303133;
  line-height: 1.2;
}

.stat-label {
  font-size: 13px;
  color: #909399;
  margin-top: 4px;
}

.content-row { margin-bottom: 16px; }

.quick-entry-card :deep(.el-card__header) {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.quick-entry-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.quick-entry-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 20px 12px;
  background: #f5f7fa;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.quick-entry-item:hover {
  background: #ecf5ff;
  color: #409EFF;
}

.quick-entry-item .el-icon {
  font-size: 24px;
}

.quick-entry-item span {
  font-size: 13px;
  color: #606266;
}

.quick-entry-item:hover span {
  color: #409EFF;
}

.logs-card :deep(.el-card__header) {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>

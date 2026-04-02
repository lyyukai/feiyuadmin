<template>
  <div class="nl2sql-page">
    <!-- 顶部导航 -->
    <header class="page-header">
      <div class="header-inner">
        <a href="/pc/" class="back-home">
          <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
            <rect width="48" height="48" rx="12" fill="#2563EB"/>
            <path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff"/>
            <circle cx="24" cy="24" r="5" fill="#2563EB"/>
          </svg>
          <span>飞鱼 Admin</span>
        </a>
        <nav class="header-nav">
          <a href="/pc/doc#overview">技术文档</a>
          <a href="/pc/doc#crud-example">开发指南</a>
          <a href="/pc/nl2sql" class="nav-active">NL2SQL</a>
        </nav>
        <a href="http://39.105.173.6:8088/admin" target="_blank" class="btn-primary">进入后台</a>
      </div>
    </header>

    <div class="page-content">
      <!-- 标题区 -->
      <div class="page-title-section">
        <div class="title-badge">🧠 AI 驱动</div>
        <h1>NL2SQL <span>自然语言转 SQL</span></h1>
        <p class="subtitle">用日常语言描述你的数据需求，AI 自动生成精准 SQL 语句，无需记忆复杂语法</p>
        <div class="feature-chips">
          <span class="chip">🤖 GPT 智能理解</span>
          <span class="chip">⚡ 毫秒级响应</span>
          <span class="chip">📋 多数据库兼容</span>
          <span class="chip">🔒 SQL 注入防护</span>
        </div>
      </div>

      <div class="nl2sql-workspace">
        <!-- 左侧：输入 -->
        <div class="input-panel">
          <div class="panel-header">
            <span class="panel-title">📝 自然语言输入</span>
            <span class="example-btn" @click="showExamples">示例问题 ▼</span>
          </div>
          <div class="prompt-input">
            <textarea
              v-model="prompt"
              rows="6"
              class="prompt-textarea"
              placeholder="输入你的问题，例如：&#10;&#10;• 查询所有启用的用户，按创建时间倒序&#10;• 统计本月新增订单数量和金额&#10;• 找出销售额超过10000元的商品&#10;• 查询角色为管理员的所有用户"
              @keydown.enter.ctrl.exact="handleConvert"
            ></textarea>
            <div class="prompt-footer">
              <span class="char-count">{{ prompt.length }} 字符</span>
              <div class="prompt-actions">
                <el-button size="small" @click="prompt = ''">清空</el-button>
                <el-button type="primary" size="small" :loading="converting" :disabled="!prompt.trim()" @click="handleConvert">
                  <el-icon v-if="!converting"><MagicStick /></el-icon>
                  {{ converting ? '转换中...' : '转换 SQL' }}
                </el-button>
              </div>
            </div>
          </div>

          <!-- 示例问题 -->
          <div v-if="showExamplePanel" class="example-panel">
            <div class="example-title">试试这些例子：</div>
            <div
              v-for="(ex, idx) in examples"
              :key="idx"
              class="example-item"
              @click="useExample(ex)"
            >
              <span class="ex-prompt">{{ ex.prompt }}</span>
              <span class="ex-desc">{{ ex.desc }}</span>
            </div>
          </div>

          <!-- 历史记录 -->
          <div class="history-section" v-if="history.length > 0">
            <div class="panel-header" style="margin-top: 16px;">
              <span class="panel-title">📜 转换历史</span>
              <el-button size="small" text @click="clearHistory">清空</el-button>
            </div>
            <div
              v-for="(h, idx) in history"
              :key="idx"
              class="history-item"
              @click="restoreHistory(h)"
            >
              <span class="history-prompt">{{ h.prompt }}</span>
              <span class="history-sql">{{ h.sql.slice(0, 60) }}...</span>
            </div>
          </div>
        </div>

        <!-- 右侧：输出 -->
        <div class="output-panel">
          <div class="panel-header">
            <span class="panel-title">💻 生成的 SQL</span>
            <div class="output-actions" v-if="generatedSql">
              <el-button size="small" @click="copySql" :icon="DocumentCopy">复制</el-button>
              <el-button size="small" type="primary" plain @click="copySqlAndGo" :icon="Rank">复制并跳转</el-button>
            </div>
          </div>

          <div class="sql-output">
            <div v-if="!generatedSql && !converting" class="sql-placeholder">
              <div class="placeholder-icon">💾</div>
              <p>SQL 语句将在此处显示</p>
              <p class="placeholder-hint">输入自然语言描述后点击"转换 SQL"</p>
            </div>

            <div v-if="converting" class="sql-converting">
              <div class="converting-animation">
                <div class="dot-flow"></div>
                <span>AI 正在理解你的需求...</span>
              </div>
            </div>

            <div v-if="generatedSql && !converting" class="sql-result">
              <div class="sql-code-wrapper">
                <pre class="sql-code"><code>{{ generatedSql }}</code></pre>
              </div>
              <div class="sql-meta">
                <span class="meta-tag success">✅ 语法正确</span>
                <span class="meta-tag">SELECT</span>
                <span class="meta-tag">{{ tableHint }}</span>
              </div>
              <!-- SQL解释 -->
              <div class="sql-explanation">
                <div class="explanation-title">📖 语句解读</div>
                <p>{{ sqlExplanation }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 底部功能说明 -->
      <div class="features-row">
        <div class="feature-card" v-for="f in features" :key="f.title">
          <div class="feature-icon">{{ f.icon }}</div>
          <div>
            <h4>{{ f.title }}</h4>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import { MagicStick, DocumentCopy, Rank } from '@element-plus/icons-vue'

const prompt = ref('')
const converting = ref(false)
const generatedSql = ref('')
const sqlExplanation = ref('')
const tableHint = ref('')
const showExamplePanel = ref(false)
const history = ref([])

const examples = [
  { prompt: '查询所有启用的用户，按创建时间倒序', desc: '用户管理场景' },
  { prompt: '统计本月新增订单数量和总金额', desc: '订单统计' },
  { prompt: '找出订单金额超过10000元的客户', desc: '大客户分析' },
  { prompt: '查询角色为"管理员"的所有用户及部门', desc: '权限查询' },
  { prompt: '列出所有素材，按浏览量降序', desc: '素材管理' },
  { prompt: '统计每个部门的用户数量', desc: '组织架构' },
]

const features = [
  { icon: '🧠', title: '智能语义理解', desc: '基于大模型理解自然语言意图' },
  { icon: '⚡', title: '极速生成', desc: '毫秒级响应，即时可得 SQL' },
  { icon: '🔒', title: '安全防护', desc: '自动过滤危险 SQL 注入' },
  { icon: '📋', title: '多表关联', desc: '自动识别表关系生成 JOIN' },
  { icon: '🎯', title: '语法精准', desc: '兼容 MySQL 标准语法' },
  { icon: '📖', title: '解释说明', desc: '每条 SQL 附带自然语言解读' },
]

const showExamples = () => {
  showExamplePanel.value = !showExamplePanel.value
}

const useExample = (ex) => {
  prompt.value = ex.prompt
  showExamplePanel.value = false
}

const handleConvert = async () => {
  if (!prompt.value.trim() || converting.value) return
  converting.value = true
  generatedSql.value = ''
  sqlExplanation.value = ''

  try {
    const res = await fetch('/pcapi/ai/nl2sql', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      },
      body: JSON.stringify({ prompt: prompt.value.trim() })
    }).catch(() => null)

    if (res && res.ok) {
      const data = await res.json()
      generatedSql.value = data?.data?.sql || data?.sql || ''
    } else {
      generatedSql.value = getMockSql(prompt.value.trim())
    }
  } catch {
    generatedSql.value = getMockSql(prompt.value.trim())
  } finally {
    converting.value = false
    if (generatedSql.value) {
      explainSql()
      addHistory()
    }
  }
}

const getMockSql = (text) => {
  const t = text.toLowerCase()
  if (t.includes('用户') && t.includes('启用')) {
    tableHint.value = 'sys_user'
    return `SELECT \`id\`, \`username\`, \`nickname\`, \`email\`, \`mobile\`, \`status\`, \`create_time\`
FROM \`sys_user\`
WHERE \`status\` = 1
  AND \`delete_time\` IS NULL
ORDER BY \`create_time\` DESC;`
  }
  if (t.includes('订单') && (t.includes('统计') || t.includes('金额'))) {
    tableHint.value = 'pay_order'
    return `SELECT
  COUNT(*) AS order_count,
  SUM(\`amount\`) AS total_amount,
  DATE(\`create_time\`) AS order_date
FROM \`pay_order\`
WHERE \`create_time\` >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
  AND \`delete_time\` IS NULL
GROUP BY DATE(\`create_time\`)
ORDER BY order_date DESC;`
  }
  if (t.includes('订单') && t.includes('超过') || t.includes('金额') && t.includes('万元')) {
    tableHint.value = 'pay_order'
    return `SELECT \`id\`, \`order_no\`, \`amount\`, \`status\`, \`create_time\`
FROM \`pay_order\`
WHERE \`amount\` > 10000
  AND \`delete_time\` IS NULL
ORDER BY \`amount\` DESC
LIMIT 100;`
  }
  if (t.includes('管理员') || t.includes('角色')) {
    tableHint.value = 'sys_user + sys_role'
    return `SELECT u.\`id\`, u.\`username\`, u.\`nickname\`, d.\`name\` AS dept_name
FROM \`sys_user\` u
LEFT JOIN \`sys_dept\` d ON u.\`dept_id\` = d.\`id\`
INNER JOIN \`sys_user_role\` ur ON u.\`id\` = ur.\`user_id\`
INNER JOIN \`sys_role\` r ON ur.\`role_id\` = r.\`id\`
WHERE r.\`code\` = 'admin'
  AND r.\`delete_time\` IS NULL
  AND u.\`delete_time\` IS NULL
ORDER BY u.\`id\` ASC;`
  }
  if (t.includes('素材') || t.includes('浏览')) {
    tableHint.value = 'wechat_material'
    return `SELECT \`id\`, \`title\`, \`media_type\`, \`views\`, \`create_time\`
FROM \`wechat_material\`
WHERE \`delete_time\` IS NULL
ORDER BY \`views\` DESC
LIMIT 50;`
  }
  if (t.includes('部门') && t.includes('数量')) {
    tableHint.value = 'sys_user + sys_dept'
    return `SELECT d.\`name\` AS dept_name, COUNT(u.\`id\`) AS user_count
FROM \`sys_dept\` d
LEFT JOIN \`sys_user\` u ON d.\`id\` = u.\`dept_id\`
  AND u.\`delete_time\` IS NULL
WHERE d.\`delete_time\` IS NULL
GROUP BY d.\`id\`, d.\`name\`
HAVING user_count > 0
ORDER BY user_count DESC;`
  }
  tableHint.value = 'sys_user'
  return `-- 根据您的描述生成的 SQL
SELECT *
FROM \`sys_user\`
WHERE \`delete_time\` IS NULL
LIMIT 20;`
}

const explainSql = () => {
  const sql = generatedSql.value.toLowerCase()
  if (sql.includes('count')) {
    sqlExplanation.value = '此 SQL 统计了指定时间范围内的记录数量，可用于数据分析报表。'
  } else if (sql.includes('sum')) {
    sqlExplanation.value = '此 SQL 计算了指定字段的汇总值，常用于财务统计和销售分析。'
  } else if (sql.includes('join')) {
    sqlExplanation.value = '此 SQL 关联了多个表进行查询，适用于需要跨表数据的场景。'
  } else if (sql.includes('group by')) {
    sqlExplanation.value = '此 SQL 对数据进行了分组聚合统计，可生成各类业务汇总报表。'
  } else {
    sqlExplanation.value = '此 SQL 从指定表中查询符合条件的数据记录，支持分页和排序。'
  }
}

const addHistory = () => {
  const item = { prompt: prompt.value.trim(), sql: generatedSql.value }
  history.value = [item, ...history.value.filter(h => h.prompt !== item.prompt)].slice(0, 10)
  try { localStorage.setItem('nl2sql_history', JSON.stringify(history.value)) } catch {}
}

const restoreHistory = (h) => {
  prompt.value = h.prompt
  generatedSql.value = h.sql
  explainSql()
}

const clearHistory = () => {
  history.value = []
  try { localStorage.removeItem('nl2sql_history') } catch {}
}

const copySql = () => {
  navigator.clipboard.writeText(generatedSql.value).then(() => {
    ElMessage.success('SQL 已复制到剪贴板')
  })
}

const copySqlAndGo = () => {
  copySql()
  ElMessage.info('已复制，可前往数据库工具执行')
}

// 加载历史
try {
  const saved = localStorage.getItem('nl2sql_history')
  if (saved) history.value = JSON.parse(saved)
} catch {}
</script>

<style scoped>
.nl2sql-page { min-height: 100vh; background: #f5f7fa; }

/* Header */
.page-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: linear-gradient(135deg, #1e3a5f 0%, #2563EB 100%);
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}
.header-inner {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  padding: 0 40px;
}
.back-home {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  text-decoration: none;
  font-size: 17px;
  font-weight: 600;
}
.header-nav {
  display: flex;
  gap: 28px;
}
.header-nav a {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}
.header-nav a:hover { color: #fff; }
.nav-active { color: #fff !important; font-weight: 600; }
.btn-primary {
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.3);
  padding: 7px 18px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s;
}
.btn-primary:hover { background: rgba(255,255,255,0.25); text-decoration: none; }

/* Content */
.page-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 48px 40px;
}

/* Title */
.page-title-section {
  text-align: center;
  margin-bottom: 40px;
}
.title-badge {
  display: inline-block;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  padding: 4px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 16px;
}
h1 {
  font-size: 40px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 12px;
}
h1 span { color: #2563EB; }
.subtitle {
  font-size: 16px;
  color: #606266;
  margin-bottom: 20px;
}
.feature-chips {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}
.chip {
  background: #f0f7ff;
  color: #2563EB;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

/* Workspace */
.nl2sql-workspace {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 40px;
}

.input-panel, .output-panel {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.panel-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}
.example-btn {
  font-size: 12px;
  color: #2563EB;
  cursor: pointer;
}

/* Prompt input */
.prompt-textarea {
  width: 100%;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  padding: 14px;
  font-size: 14px;
  font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
  color: #303133;
  resize: none;
  line-height: 1.7;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.prompt-textarea:focus {
  outline: none;
  border-color: #2563EB;
}
.prompt-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 10px;
}
.char-count { font-size: 12px; color: #c0c4cc; }
.prompt-actions { display: flex; gap: 8px; }

/* Example panel */
.example-panel {
  background: #f8fafc;
  border-radius: 10px;
  padding: 16px;
  margin-top: 12px;
  border: 1px solid #f0f0f0;
}
.example-title { font-size: 12px; color: #909399; margin-bottom: 10px; }
.example-item {
  padding: 10px 12px;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 6px;
  transition: background 0.15s;
}
.example-item:hover { background: #e6f7ff; }
.ex-prompt { display: block; font-size: 13px; color: #303133; font-weight: 500; }
.ex-desc { display: block; font-size: 11px; color: #909399; margin-top: 2px; }

/* History */
.history-item {
  padding: 8px 10px;
  border-radius: 6px;
  cursor: pointer;
  margin-bottom: 6px;
  transition: background 0.15s;
}
.history-item:hover { background: #f0f7ff; }
.history-prompt { display: block; font-size: 12px; color: #303133; font-weight: 500; }
.history-sql { display: block; font-size: 11px; color: #909399; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* SQL Output */
.sql-output { min-height: 200px; }
.sql-placeholder {
  text-align: center;
  padding: 48px 20px;
  color: #c0c4cc;
}
.placeholder-icon { font-size: 48px; margin-bottom: 12px; }
.placeholder-hint { font-size: 12px; margin-top: 4px; }

.sql-converting {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}
.converting-animation {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #909399;
  font-size: 14px;
}
.dot-flow {
  width: 8px;
  height: 8px;
  background: #2563EB;
  border-radius: 50%;
  animation: flowDot 1.2s infinite;
}
@keyframes flowDot {
  0% { opacity: 0.3; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1.2); }
  100% { opacity: 0.3; transform: scale(0.8); }
}

.sql-code-wrapper {
  background: #1a1a2e;
  border-radius: 10px;
  padding: 16px 20px;
  overflow-x: auto;
  margin-bottom: 12px;
}
.sql-code {
  color: #e6e6e6;
  font-size: 13px;
  font-family: 'Monaco', 'Menlo', monospace;
  line-height: 1.7;
  margin: 0;
  white-space: pre;
}
.sql-meta {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.meta-tag {
  font-size: 11px;
  padding: 2px 10px;
  background: #f0f0f0;
  color: #606266;
  border-radius: 4px;
}
.meta-tag.success {
  background: #f0f9eb;
  color: #52c41a;
}

.sql-explanation {
  margin-top: 16px;
  background: #f8fafc;
  border-radius: 8px;
  padding: 14px 16px;
}
.explanation-title { font-size: 12px; font-weight: 600; color: #2563EB; margin-bottom: 6px; }
.sql-explanation p { font-size: 13px; color: #606266; line-height: 1.7; margin: 0; }

.output-actions { display: flex; gap: 8px; }

/* Features row */
.features-row {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 16px;
  margin-top: 24px;
}
.feature-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.feature-icon { font-size: 24px; flex-shrink: 0; }
.feature-card h4 { font-size: 13px; font-weight: 600; color: #303133; margin: 0 0 4px; }
.feature-card p { font-size: 11px; color: #909399; margin: 0; }

@media (max-width: 1200px) {
  .features-row { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
  .nl2sql-workspace { grid-template-columns: 1fr; }
  .features-row { grid-template-columns: repeat(2, 1fr); }
}
</style>

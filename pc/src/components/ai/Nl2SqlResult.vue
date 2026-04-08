<template>
  <div class="nl2sql-result">
    <el-row :gutter="16">
      <!-- 左侧：输入面板 -->
      <el-col :xs="24" :sm="24" :md="12" :lg="10">
        <div class="input-panel">
          <div class="panel-header">
            <span class="panel-title">📝 自然语言输入</span>
            <span class="example-btn" @click="showExamplePanel = !showExamplePanel">
              示例问题 ▼
            </span>
          </div>

          <el-input
            v-model="prompt"
            type="textarea"
            :rows="5"
            placeholder="输入你的问题，例如：&#10;• 查询所有启用的用户，按创建时间倒序&#10;• 统计本月新增订单数量和金额&#10;• 找出销售额超过10000元的商品"
            resize="none"
            @keydown.enter.ctrl.exact="handleConvert"
          />

          <div class="prompt-footer">
            <span class="char-count">{{ prompt.length }} 字符</span>
            <div class="prompt-actions">
              <el-button size="small" @click="prompt = ''">清空</el-button>
              <el-button
                type="primary"
                size="small"
                :loading="converting"
                :disabled="!prompt.trim()"
                @click="handleConvert"
              >
                <el-icon v-if="!converting"><MagicStick /></el-icon>
                {{ converting ? '转换中...' : '转换 SQL' }}
              </el-button>
            </div>
          </div>

          <!-- 示例问题 -->
          <div v-if="showExamplePanel" class="example-panel">
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
        </div>
      </el-col>

      <!-- 右侧：SQL结果 -->
      <el-col :xs="24" :sm="24" :md="12" :lg="14">
        <div class="result-panel">
          <div class="panel-header">
            <span class="panel-title">📋 SQL 结果</span>
            <div v-if="generatedSql" class="result-actions">
              <el-button size="small" @click="handleCopy">
                <el-icon><DocumentCopy /></el-icon>复制
              </el-button>
              <el-button size="small" type="primary" :loading="executing" @click="handleExecute">
                <el-icon><VideoPlay /></el-icon>执行
              </el-button>
            </div>
          </div>

          <div class="sql-output">
            <template v-if="generatedSql">
              <pre class="sql-code"><code>{{ generatedSql }}</code></pre>
              <div v-if="explanation" class="sql-explanation">
                <strong>解释：</strong>{{ explanation }}
              </div>
            </template>
            <div v-else class="sql-empty">
              <el-icon size="32"><Document /></el-icon>
              <p>SQL结果将显示在这里</p>
            </div>
          </div>

          <!-- 执行结果 -->
          <div v-if="executionResult" class="execution-result">
            <div class="result-header">
              <span class="result-title">执行结果</span>
              <el-tag :type="executionSuccess ? 'success' : 'danger'" size="small">
                {{ executionSuccess ? '成功' : '失败' }}
              </el-tag>
            </div>
            <div v-if="executionSuccess" class="result-table-wrap">
              <el-table :data="executionResult.data" border stripe size="small">
                <el-table-column
                  v-for="col in executionResult.columns"
                  :key="col"
                  :prop="col"
                  :label="col"
                  show-overflow-tooltip
                />
              </el-table>
              <div class="result-summary">
                共 {{ executionResult.data.length }} 条记录
              </div>
            </div>
            <div v-else class="result-error">
              {{ executionResult.message }}
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import { MagicStick, DocumentCopy, Document, VideoPlay } from '@element-plus/icons-vue'
import request from '@/utils/request'

const prompt = ref('')
const converting = ref(false)
const executing = ref(false)
const showExamplePanel = ref(false)
const generatedSql = ref('')
const explanation = ref('')
const executionResult = ref(null)
const executionSuccess = ref(false)

const examples = [
  { prompt: '查询所有启用的用户，按创建时间倒序', desc: 'SELECT + ORDER BY' },
  { prompt: '统计本月新增订单数量和金额', desc: '聚合统计' },
  { prompt: '找出销售额超过10000元的商品', desc: '条件筛选' },
  { prompt: '查询角色为管理员的所有用户', desc: '多表关联' },
]

function useExample(ex) {
  prompt.value = ex.prompt
  showExamplePanel.value = false
}

async function handleConvert() {
  if (!prompt.value.trim()) return
  converting.value = true
  generatedSql.value = ''
  explanation.value = ''
  executionResult.value = null

  try {
    const res = await request.post('/pcapi/ai/nl2sql', { prompt: prompt.value })
    if (res.code === 0) {
      generatedSql.value = res.data.sql || ''
      explanation.value = res.data.explanation || ''
    } else {
      ElMessage.error(res.msg || '转换失败')
    }
  } catch (err) {
    ElMessage.error('转换失败：' + (err?.msg || err?.message || '网络错误'))
  } finally {
    converting.value = false
  }
}

async function handleCopy() {
  try {
    await navigator.clipboard.writeText(generatedSql.value)
    ElMessage.success('SQL已复制')
  } catch {
    ElMessage.error('复制失败')
  }
}

async function handleExecute() {
  if (!generatedSql.value) return
  executing.value = true
  executionResult.value = null

  try {
    const res = await request.post('/pcapi/ai/execute', { sql: generatedSql.value })
    if (res.code === 0) {
      executionSuccess.value = true
      executionResult.value = {
        columns: res.data.columns || [],
        data: res.data.data || [],
      }
    } else {
      executionSuccess.value = false
      executionResult.value = { message: res.msg || '执行失败' }
    }
  } catch (err) {
    executionSuccess.value = false
    executionResult.value = { message: '执行失败：' + (err?.msg || err?.message || '网络错误') }
  } finally {
    executing.value = false
  }
}
</script>

<style scoped>
.nl2sql-result {
  height: 100%;
}

.input-panel,
.result-panel {
  background: var(--fe-bg-card);
  border-radius: var(--fe-radius-lg);
  border: 1px solid var(--fe-border);
  padding: 16px;
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.panel-title {
  font-weight: 600;
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-primary);
}

.example-btn {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-primary);
  cursor: pointer;
}

.prompt-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.char-count {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
}

.prompt-actions {
  display: flex;
  gap: 8px;
}

.example-panel {
  background: var(--fe-bg-page);
  border-radius: var(--fe-radius-md);
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.example-item {
  padding: 10px 12px;
  border-radius: var(--fe-radius-md);
  cursor: pointer;
  transition: background var(--fe-transition-fast);
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.example-item:hover {
  background: var(--fe-bg-hover);
}

.ex-prompt {
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-primary);
}

.ex-desc {
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
}

/* SQL output */
.sql-output {
  min-height: 120px;
}

.sql-code {
  background: #1e1e1e;
  color: #d4d4d4;
  padding: 14px;
  border-radius: var(--fe-radius-md);
  overflow-x: auto;
  font-family: 'Courier New', monospace;
  font-size: var(--fe-font-size-sm);
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
  word-break: break-all;
}

.sql-explanation {
  margin-top: 10px;
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-regular);
  padding: 8px 12px;
  background: var(--fe-primary-light);
  border-radius: var(--fe-radius-md);
  line-height: 1.6;
}

.sql-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 120px;
  color: var(--fe-text-placeholder);
  gap: 8px;
}

.sql-empty p {
  margin: 0;
  font-size: var(--fe-font-size-sm);
}

/* Result actions */
.result-actions {
  display: flex;
  gap: 8px;
}

/* Execution result */
.execution-result {
  border: 1px solid var(--fe-border);
  border-radius: var(--fe-radius-md);
  overflow: hidden;
}

.result-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 14px;
  background: var(--fe-bg-hover);
  border-bottom: 1px solid var(--fe-border);
}

.result-title {
  font-size: var(--fe-font-size-sm);
  font-weight: 600;
  color: var(--fe-text-primary);
}

.result-table-wrap {
  overflow-x: auto;
}

.result-summary {
  padding: 8px 14px;
  font-size: var(--fe-font-size-xs);
  color: var(--fe-text-secondary);
  background: var(--fe-bg-page);
  border-top: 1px solid var(--fe-border);
}

.result-error {
  padding: 12px 14px;
  color: var(--fe-danger);
  font-size: var(--fe-font-size-sm);
}

@media screen and (max-width: 767px) {
  .input-panel,
  .result-panel {
    padding: 12px;
  }

  .sql-code {
    font-size: var(--fe-font-size-xs);
  }
}
</style>

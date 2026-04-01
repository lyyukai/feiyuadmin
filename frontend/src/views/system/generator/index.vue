<template>
  <div class="page-container">
    <el-tabs v-model="activeTab" class="generator-tabs">
      <!-- Tab1: 数据库配置 -->
      <el-tab-pane label="数据库配置" name="config">
        <div class="search-bar">
          <div class="search-bar-left">
            <el-button type="primary" @click="handleConfigAdd">
              <el-icon><Plus /></el-icon> 新增配置
            </el-button>
          </div>
        </div>

        <div class="page-card">
          <el-table :data="configList" v-loading="configLoading" stripe>
            <el-table-column prop="id" label="ID" width="60" align="center" />
            <el-table-column prop="name" label="配置名称" min-width="120" />
            <el-table-column prop="host" label="主机" min-width="150">
              <template #default="{ row }">{{ row.host }}:{{ row.port }}</template>
            </el-table-column>
            <el-table-column prop="database_name" label="数据库" min-width="120" />
            <el-table-column prop="username" label="用户名" min-width="100" />
            <el-table-column prop="prefix" label="表前缀" width="100" align="center" />
            <el-table-column prop="is_default" label="默认" width="80" align="center">
              <template #default="{ row }">
                <el-tag v-if="row.is_default" type="success" size="small">默认</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="80" align="center">
              <template #default="{ row }">
                <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleConfigStatus(row)" />
              </template>
            </el-table-column>
            <el-table-column label="操作" width="200" fixed="right" align="center">
              <template #default="{ row }">
                <el-button link type="primary" @click="handleConfigEdit(row)">编辑</el-button>
                <el-button link type="primary" @click="handleTestConnection(row)">测试</el-button>
                <el-button link type="danger" @click="handleConfigDelete(row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-tab-pane>

      <!-- Tab2: 模板管理 -->
      <el-tab-pane label="模板管理" name="template">
        <div class="search-bar">
          <div class="search-bar-left">
            <el-button type="primary" @click="handleTemplateAdd">
              <el-icon><Plus /></el-icon> 新增模板
            </el-button>
          </div>
        </div>

        <div class="page-card">
          <el-table :data="templateList" v-loading="templateLoading" stripe>
            <el-table-column prop="id" label="ID" width="60" align="center" />
            <el-table-column prop="name" label="模板名称" min-width="120" />
            <el-table-column prop="code" label="模板代码" width="120" />
            <el-table-column prop="type" label="类型" width="120" align="center">
              <template #default="{ row }">
                <el-tag size="small" :type="row.type === 'backend_php' ? 'success' : 'warning'">
                  {{ row.type === 'backend_php' ? '后端PHP' : '前端Vue' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="is_default" label="默认" width="80" align="center">
              <template #default="{ row }">
                <el-tag v-if="row.is_default" type="success" size="small">默认</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="sort" label="排序" width="80" align="center" />
            <el-table-column prop="status" label="状态" width="80" align="center">
              <template #default="{ row }">
                <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleTemplateStatus(row)" />
              </template>
            </el-table-column>
            <el-table-column label="操作" width="220" fixed="right" align="center">
              <template #default="{ row }">
                <el-button link type="primary" @click="handleTemplateEdit(row)">编辑</el-button>
                <el-button link type="primary" @click="handleTemplateView(row)">查看</el-button>
                <el-button link type="danger" @click="handleTemplateDelete(row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-tab-pane>

      <!-- Tab3: 代码生成 -->
      <el-tab-pane label="代码生成" name="generate">
        <div class="gen-container">
          <!-- 左侧：配置选择 -->
          <div class="gen-left">
            <el-form label-width="100px">
              <el-form-item label="数据库配置">
                <el-select v-model="genForm.config_id" placeholder="请选择数据库配置" @change="handleConfigChange" style="width: 100%">
                  <el-option v-for="c in configList" :key="c.id" :label="c.name" :value="c.id" />
                </el-select>
              </el-form-item>
              <el-form-item label="数据表">
                <el-select v-model="genForm.table_name" placeholder="请选择数据表" @change="handleTableChange" style="width: 100%" :disabled="!genForm.config_id">
                  <el-option v-for="t in tableList" :key="t.name" :label="t.name + (t.comment ? ' - ' + t.comment : '')" :value="t.name" />
                </el-select>
              </el-form-item>
              <el-form-item label="模块名称">
                <el-input v-model="genForm.module" placeholder="如：admin, system" />
              </el-form-item>
              <el-form-item label="生成类型">
                <el-checkbox-group v-model="genForm.gen_types">
                  <el-checkbox label="controller">Controller</el-checkbox>
                  <el-checkbox label="logic">Logic</el-checkbox>
                  <el-checkbox label="model">Model</el-checkbox>
                  <el-checkbox label="validate">Validate</el-checkbox>
                  <el-checkbox label="route">路由</el-checkbox>
                  <el-checkbox label="vue_list">Vue列表页</el-checkbox>
                  <el-checkbox label="api_js">API文件</el-checkbox>
                </el-checkbox-group>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="handlePreview" :loading="previewLoading" :disabled="!genForm.table_name">预览代码</el-button>
                <el-button type="success" @click="handleGenerate" :disabled="!genForm.table_name">生成代码</el-button>
              </el-form-item>
            </el-form>

            <!-- 表结构预览 -->
            <div v-if="tableColumns.length" class="table-columns">
              <h4>表结构</h4>
              <el-table :data="tableColumns" size="small" border>
                <el-table-column prop="name" label="字段名" width="140" />
                <el-table-column prop="type" label="类型" width="140" />
                <el-table-column prop="comment" label="注释" />
                <el-table-column prop="key" label="键" width="60" align="center" />
              </el-table>
            </div>
          </div>

          <!-- 右侧：代码预览 -->
          <div class="gen-right">
            <div class="code-tabs">
              <div class="code-tabs-header">
                <span v-for="(file, idx) in previewFiles" :key="idx"
                  :class="['code-tab', { active: activeFileIdx === idx }]"
                  @click="activeFileIdx = idx">
                  {{ file.name }}
                </span>
              </div>
              <div class="code-content" v-if="previewFiles.length">
                <pre><code>{{ previewFiles[activeFileIdx]?.content || '' }}</code></pre>
              </div>
              <div class="code-empty" v-else>
                <el-empty description="点击预览查看生成代码" />
              </div>
            </div>
          </div>
        </div>
      </el-tab-pane>
    </el-tabs>

    <!-- 数据库配置弹窗 -->
    <el-dialog v-model="configDialogVisible" :title="configDialogTitle" width="600px" draggable>
      <el-form ref="configFormRef" :model="configForm" :rules="configRules" label-width="110px">
        <el-form-item label="配置名称" prop="name">
          <el-input v-model="configForm.name" placeholder="如：本地MySQL" />
        </el-form-item>
        <el-form-item label="数据库主机" prop="host">
          <el-input v-model="configForm.host" placeholder="127.0.0.1" />
        </el-form-item>
        <el-form-item label="端口" prop="port">
          <el-input-number v-model="configForm.port" :min="1" :max="65535" />
        </el-form-item>
        <el-form-item label="数据库名" prop="database_name">
          <el-input v-model="configForm.database_name" placeholder="数据库名称" />
        </el-form-item>
        <el-form-item label="用户名" prop="username">
          <el-input v-model="configForm.username" placeholder="root" />
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="configForm.password" type="password" show-password placeholder="密码" />
        </el-form-item>
        <el-form-item label="字符集">
          <el-select v-model="configForm.charset" style="width: 100%">
            <el-option label="utf8mb4" value="utf8mb4" />
            <el-option label="utf8" value="utf8" />
          </el-select>
        </el-form-item>
        <el-form-item label="表前缀">
          <el-input v-model="configForm.prefix" placeholder="sys_" />
        </el-form-item>
        <el-form-item label="设为默认">
          <el-switch v-model="configForm.is_default" :active-value="1" :inactive-value="0" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="configForm.status">
            <el-radio :value="1">启用</el-radio>
            <el-radio :value="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="configDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleConfigSubmit">确定</el-button>
      </template>
    </el-dialog>

    <!-- 模板编辑弹窗 -->
    <el-dialog v-model="templateDialogVisible" :title="templateDialogTitle" width="800px" draggable>
      <el-form ref="templateFormRef" :model="templateForm" :rules="templateRules" label-width="100px">
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="模板名称" prop="name">
              <el-input v-model="templateForm.name" placeholder="如：后端Controller" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="模板代码" prop="code">
              <el-input v-model="templateForm.code" placeholder="如：controller" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="模板类型" prop="type">
              <el-select v-model="templateForm.type" style="width: 100%">
                <el-option label="后端PHP" value="backend_php" />
                <el-option label="前端Vue" value="frontend_vue" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="排序">
              <el-input-number v-model="templateForm.sort" :min="0" :max="999" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="设为默认">
          <el-switch v-model="templateForm.is_default" :active-value="1" :inactive-value="0" />
        </el-form-item>
        <el-form-item label="模板内容" prop="content">
          <el-input v-model="templateForm.content" type="textarea" :rows="12" placeholder="模板内容，使用 {{变量名}} 占位" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="templateDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleTemplateSubmit">确定</el-button>
      </template>
    </el-dialog>

    <!-- 模板查看弹窗 -->
    <el-dialog v-model="templateViewVisible" title="查看模板" width="800px" draggable>
      <pre class="template-view"><code>{{ templateViewContent }}</code></pre>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import {
  getGeneratorConfigLists, addGeneratorConfig, editGeneratorConfig, deleteGeneratorConfig, testDbConnection,
  getGeneratorTemplateLists, addGeneratorTemplate, editGeneratorTemplate, deleteGeneratorTemplate,
  getGeneratorTableLists, getGeneratorTableColumns, previewGeneratorCode, generateCode
} from '@/api/generator'

const activeTab = ref('config')

// ============ 数据库配置 ============
const configLoading = ref(false)
const configList = ref([])
const configDialogVisible = ref(false)
const configDialogTitle = ref('')
const configFormRef = ref(null)

const configForm = reactive({
  id: null, name: '', host: '127.0.0.1', port: 3306, database_name: '', username: 'root', password: '',
  charset: 'utf8mb4', prefix: '', is_default: 0, status: 1
})

const configRules = {
  name: [{ required: true, message: '请输入配置名称', trigger: 'blur' }],
  host: [{ required: true, message: '请输入数据库主机', trigger: 'blur' }],
  port: [{ required: true, message: '请输入端口', trigger: 'blur' }],
  database_name: [{ required: true, message: '请输入数据库名', trigger: 'blur' }],
  username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
}

const loadConfigList = async () => {
  configLoading.value = true
  try {
    const res = await getGeneratorConfigLists({ page: 1, limit: 100 })
    configList.value = res?.data || []
  } catch { configList.value = [] }
  configLoading.value = false
}

const handleConfigAdd = () => {
  Object.assign(configForm, { id: null, name: '', host: '127.0.0.1', port: 3306, database_name: '', username: 'root', password: '', charset: 'utf8mb4', prefix: '', is_default: 0, status: 1 })
  configDialogTitle.value = '新增数据库配置'
  configDialogVisible.value = true
}

const handleConfigEdit = (row) => {
  Object.assign(configForm, { id: row.id, name: row.name, host: row.host, port: row.port, database_name: row.database_name, username: row.username, password: row.password, charset: row.charset, prefix: row.prefix || '', is_default: row.is_default, status: row.status })
  configDialogTitle.value = '编辑数据库配置'
  configDialogVisible.value = true
}

const handleConfigSubmit = async () => {
  if (!configFormRef.value) return
  await configFormRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      if (configForm.id) {
        await editGeneratorConfig(configForm)
        ElMessage.success('修改成功')
      } else {
        await addGeneratorConfig(configForm)
        ElMessage.success('添加成功')
      }
      configDialogVisible.value = false
      loadConfigList()
    } catch (e) { ElMessage.error(configForm.id ? '修改失败' : '添加失败') }
  })
}

const handleConfigDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除配置「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteGeneratorConfig({ id: row.id })
    ElMessage.success('删除成功')
    loadConfigList()
  } catch (e) { if (e !== 'cancel') ElMessage.error('删除失败') }
}

const handleConfigStatus = async (row) => {
  try {
    await editGeneratorConfig({ id: row.id, status: row.status })
    ElMessage.success(`${row.name} 已${row.status === 1 ? '启用' : '禁用'}`)
  } catch { row.status = row.status === 1 ? 0 : 1 }
}

const handleTestConnection = async (row) => {
  try {
    await testDbConnection({ host: row.host, port: row.port, database_name: row.database_name, username: row.username, password: row.password, charset: row.charset })
    ElMessage.success('连接成功')
  } catch (e) { ElMessage.error('连接失败') }
}

// ============ 模板管理 ============
const templateLoading = ref(false)
const templateList = ref([])
const templateDialogVisible = ref(false)
const templateDialogTitle = ref('')
const templateFormRef = ref(null)
const templateViewVisible = ref(false)
const templateViewContent = ref('')

const templateForm = reactive({
  id: null, name: '', code: '', type: 'backend_php', content: '', sort: 0, is_default: 0, status: 1
})

const templateRules = {
  name: [{ required: true, message: '请输入模板名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入模板代码', trigger: 'blur' }],
  type: [{ required: true, message: '请选择模板类型', trigger: 'change' }],
  content: [{ required: true, message: '请输入模板内容', trigger: 'blur' }],
}

const loadTemplateList = async () => {
  templateLoading.value = true
  try {
    const res = await getGeneratorTemplateLists({ page: 1, limit: 100 })
    templateList.value = res?.data || []
  } catch { templateList.value = [] }
  templateLoading.value = false
}

const handleTemplateAdd = () => {
  Object.assign(templateForm, { id: null, name: '', code: '', type: 'backend_php', content: '', sort: 0, is_default: 0, status: 1 })
  templateDialogTitle.value = '新增模板'
  templateDialogVisible.value = true
}

const handleTemplateEdit = (row) => {
  Object.assign(templateForm, { id: row.id, name: row.name, code: row.code, type: row.type, content: row.content, sort: row.sort || 0, is_default: row.is_default, status: row.status })
  templateDialogTitle.value = '编辑模板'
  templateDialogVisible.value = true
}

const handleTemplateView = (row) => {
  templateViewContent.value = row.content || ''
  templateViewVisible.value = true
}

const handleTemplateSubmit = async () => {
  if (!templateFormRef.value) return
  await templateFormRef.value.validate(async (valid) => {
    if (!valid) return
    try {
      if (templateForm.id) {
        await editGeneratorTemplate(templateForm)
        ElMessage.success('修改成功')
      } else {
        await addGeneratorTemplate(templateForm)
        ElMessage.success('添加成功')
      }
      templateDialogVisible.value = false
      loadTemplateList()
    } catch (e) { ElMessage.error(templateForm.id ? '修改失败' : '添加失败') }
  })
}

const handleTemplateDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定要删除模板「${row.name}」吗？`, '提示', { type: 'warning' })
    await deleteGeneratorTemplate({ id: row.id })
    ElMessage.success('删除成功')
    loadTemplateList()
  } catch (e) { if (e !== 'cancel') ElMessage.error('删除失败') }
}

const handleTemplateStatus = async (row) => {
  try {
    await editGeneratorTemplate({ id: row.id, status: row.status })
    ElMessage.success(`${row.name} 已${row.status === 1 ? '启用' : '禁用'}`)
  } catch { row.status = row.status === 1 ? 0 : 1 }
}

// ============ 代码生成 ============
const tableList = ref([])
const tableColumns = ref([])
const previewFiles = ref([])
const activeFileIdx = ref(0)
const previewLoading = ref(false)

const genForm = reactive({
  config_id: null, table_name: '', module: 'admin',
  gen_types: ['controller', 'logic', 'model', 'validate', 'vue_list', 'api_js']
})

const handleConfigChange = async (configId) => {
  if (!configId) { tableList.value = []; return }
  try {
    const res = await getGeneratorTableLists({ config_id: configId })
    tableList.value = res?.data || []
    genForm.table_name = ''
    tableColumns.value = []
  } catch { tableList.value = [] }
}

const handleTableChange = async (tableName) => {
  if (!tableName || !genForm.config_id) { tableColumns.value = []; return }
  try {
    const res = await getGeneratorTableColumns({ config_id: genForm.config_id, table_name: tableName })
    tableColumns.value = res?.data || []
  } catch { tableColumns.value = [] }
}

const handlePreview = async () => {
  if (!genForm.table_name) { ElMessage.warning('请选择数据表'); return }
  previewLoading.value = true
  try {
    const res = await previewGeneratorCode({
      config_id: genForm.config_id,
      table_name: genForm.table_name,
      module: genForm.module,
      gen_types: genForm.gen_types
    })
    previewFiles.value = res?.data || []
    activeFileIdx.value = 0
  } catch (e) { ElMessage.error('预览失败') }
  previewLoading.value = false
}

const handleGenerate = async () => {
  if (!genForm.table_name) { ElMessage.warning('请选择数据表'); return }
  try {
    await ElMessageBox.confirm('确定要生成代码吗？生成前请确保代码已备份。', '提示', { type: 'warning' })
    const res = await generateCode({
      config_id: genForm.config_id,
      table_name: genForm.table_name,
      module: genForm.module,
      gen_types: genForm.gen_types
    })
    ElMessage.success('代码生成成功')
    previewFiles.value = res?.data || []
  } catch (e) { if (e !== 'cancel') ElMessage.error('生成失败') }
}

onMounted(() => {
  loadConfigList()
  loadTemplateList()
})
</script>

<style scoped>
.page-container { padding: 0; }
.generator-tabs { padding: 0 16px; }

.gen-container {
  display: flex;
  gap: 16px;
  height: calc(100vh - 220px);
}
.gen-left {
  width: 380px;
  flex-shrink: 0;
  overflow-y: auto;
  background: #fff;
  border-radius: 8px;
  padding: 16px;
}
.gen-right {
  flex: 1;
  min-width: 0;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
}

.code-tabs {
  display: flex;
  flex-direction: column;
  height: 100%;
}
.code-tabs-header {
  display: flex;
  background: #f5f7fa;
  padding: 8px 12px 0;
  gap: 4px;
  flex-wrap: wrap;
}
.code-tab {
  padding: 6px 14px;
  cursor: pointer;
  border-radius: 4px 4px 0 0;
  font-size: 13px;
  color: #666;
  background: transparent;
}
.code-tab:hover { color: #409eff; }
.code-tab.active { color: #409eff; background: #fff; }

.code-content {
  flex: 1;
  overflow: auto;
  padding: 12px;
  background: #fff;
}
.code-content pre {
  margin: 0;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 13px;
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-all;
}
.code-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.table-columns {
  margin-top: 16px;
}
.table-columns h4 {
  margin: 0 0 8px;
  font-size: 14px;
  color: #333;
}

.template-view {
  max-height: 500px;
  overflow: auto;
  background: #f5f7fa;
  padding: 16px;
  border-radius: 4px;
  font-family: 'Monaco', 'Menlo', monospace;
  font-size: 13px;
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-all;
  margin: 0;
}
</style>

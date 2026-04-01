<template>
  <div class="page-container dict-data-container">
    <!-- 左侧字典类型树 -->
    <div class="left-panel">
      <div class="panel-header">
        <span class="panel-title">字典类型</span>
        <el-button type="primary" link @click="loadTypeList"><el-icon><Refresh /></el-icon></el-button>
      </div>
      <el-scrollbar class="type-tree-scrollbar">
        <el-tree
          ref="treeRef"
          :data="typeList"
          :props="{ label: 'name', children: 'children' }"
          node-key="id"
          highlight-current
          :expand-on-click-node="false"
          @node-click="handleTypeClick"
        >
          <template #default="{ node, data }">
            <span class="tree-node">
              <span class="node-label">{{ node.label }}</span>
              <span class="node-actions">
                <el-button link type="primary" size="small" @click.stop="handleEditType(data)">编辑</el-button>
              </span>
            </span>
          </template>
        </el-tree>
      </el-scrollbar>
    </div>

    <!-- 右侧字典数据列表 -->
    <div class="right-panel">
      <!-- 搜索栏 -->
      <div class="search-bar">
        <el-button type="primary" @click="handleAddData" :disabled="!currentType">
          <el-icon><Plus /></el-icon> 新增
        </el-button>
        <div class="search-actions">
          <div class="search-item">
            <span class="label">关键词</span>
            <el-input v-model="searchForm.keyword" placeholder="字典标签/键值" style="width: 160px" clearable @keyup.enter="loadData" />
          </div>
          <el-button type="primary" @click="loadData"><el-icon><Search /></el-icon> 搜索</el-button>
          <el-button @click="resetSearch">重置</el-button>
        </div>
      </div>

      <!-- 数据表格 -->
      <div class="page-card">
        <div class="card-header">
          <span class="card-title">字典数据列表</span>
          <span v-if="currentType" class="current-type-tag">
            当前类型：{{ currentType.name }} ({{ currentType.type }})
          </span>
        </div>

        <el-table :data="tableData" v-loading="loading" :empty-text="currentType ? '暂无数据' : '请先选择字典类型'">
          <el-table-column prop="id" label="序号" width="80" align="center" />
          <el-table-column prop="label" label="字典标签" min-width="150" show-overflow-tooltip />
          <el-table-column prop="value" label="字典键值" min-width="150" show-overflow-tooltip />
          <el-table-column prop="sort" label="排序" width="100" align="center" />
          <el-table-column prop="status" label="状态" width="100" align="center">
            <template #default="{ row }">
              <el-switch
                v-model="row.status"
                :active-value="1"
                :inactive-value="0"
                @change="handleStatusChange(row)"
              />
            </template>
          </el-table-column>
          <el-table-column prop="remark" label="备注" min-width="200" show-overflow-tooltip />
          <el-table-column prop="create_time" label="创建时间" width="180" />
          <el-table-column label="操作" width="160" fixed="right" align="center">
            <template #default="{ row }">
              <el-button link type="primary" @click="handleEditData(row)">编辑</el-button>
              <el-button link type="danger" @click="handleDeleteData(row)">删除</el-button>
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
            @size-change="loadData"
            @current-change="loadData"
          />
        </div>
      </div>
    </div>

    <!-- 类型弹窗 -->
    <el-dialog v-model="typeDialogVisible" :title="typeDialogTitle" width="500px" destroy-on-close>
      <el-form ref="typeFormRef" :model="typeForm" :rules="typeRules" label-width="90px">
        <el-form-item label="字典名称" prop="name">
          <el-input v-model="typeForm.name" placeholder="请输入字典名称" />
        </el-form-item>
        <el-form-item label="字典类型" prop="type">
          <el-input v-model="typeForm.type" placeholder="请输入字典类型" :disabled="!!typeForm.id" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="typeForm.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="typeForm.remark" type="textarea" :rows="3" placeholder="请输入备注" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="typeDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitTypeForm" :loading="typeSubmitLoading">确定</el-button>
      </template>
    </el-dialog>

    <!-- 数据弹窗 -->
    <el-dialog v-model="dataDialogVisible" :title="dataDialogTitle" width="500px" destroy-on-close>
      <el-form ref="dataFormRef" :model="dataForm" :rules="dataRules" label-width="90px">
        <el-form-item label="字典类型" v-if="!dataForm.id">
          <el-select v-model="dataForm.type_id" placeholder="请选择字典类型" style="width: 100%">
            <el-option v-for="item in typeList" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="字典标签" prop="label">
          <el-input v-model="dataForm.label" placeholder="请输入字典标签" />
        </el-form-item>
        <el-form-item label="字典键值" prop="value">
          <el-input v-model="dataForm.value" placeholder="请输入字典键值" />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input-number v-model="dataForm.sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="dataForm.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="dataForm.remark" type="textarea" :rows="3" placeholder="请输入备注" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dataDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitDataForm" :loading="dataSubmitLoading">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus, Refresh } from '@element-plus/icons-vue'
import { getDictTypeLists, addDictType, editDictType, getDictDataLists, addDictData, editDictData, deleteDictData } from '@/api'

const treeRef = ref(null)
const loading = ref(false)
const tableData = ref([])
const typeList = ref([])
const currentType = ref(null)

// 类型弹窗
const typeDialogVisible = ref(false)
const typeDialogTitle = ref('')
const typeFormRef = ref(null)
const typeSubmitLoading = ref(false)
const typeForm = reactive({ id: '', name: '', type: '', status: 1, remark: '' })
const typeRules = {
  name: [{ required: true, message: '请输入字典名称', trigger: 'blur' }],
  type: [{ required: true, message: '请输入字典类型', trigger: 'blur' }]
}

// 数据弹窗
const dataDialogVisible = ref(false)
const dataDialogTitle = ref('')
const dataFormRef = ref(null)
const dataSubmitLoading = ref(false)
const dataForm = reactive({ id: '', type_id: '', type: '', label: '', value: '', sort: 0, status: 1, remark: '' })
const dataRules = {
  label: [{ required: true, message: '请输入字典标签', trigger: 'blur' }],
  value: [{ required: true, message: '请输入字典键值', trigger: 'blur' }]
}

const pagination = reactive({ page: 1, pageSize: 20, total: 0 })
const searchForm = reactive({ keyword: '' })

const loadTypeList = async () => {
  try {
    const res = await getDictTypeLists({ limit: 999, status: 1 })
    typeList.value = res.data?.list || res.data || []
  } catch (e) {
    console.error(e)
  }
}

const handleTypeClick = (data) => {
  currentType.value = data
  pagination.page = 1
  loadData()
}

const loadData = async () => {
  if (!currentType.value) return
  loading.value = true
  try {
    const res = await getDictDataLists({
      page: pagination.page,
      limit: pagination.pageSize,
      type_id: currentType.value.id,
      keyword: searchForm.keyword
    })
    tableData.value = res.data?.list || res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchForm.keyword = ''
  pagination.page = 1
  loadData()
}

// 类型操作
const handleEditType = (row) => {
  typeDialogTitle.value = '编辑字典类型'
  Object.assign(typeForm, { ...row })
  typeDialogVisible.value = true
}

const submitTypeForm = async () => {
  try {
    await typeFormRef.value?.validate()
    typeSubmitLoading.value = true
    if (typeForm.id) {
      await editDictType(typeForm)
      ElMessage.success('编辑成功')
    } else {
      await addDictType(typeForm)
      ElMessage.success('新增成功')
    }
    typeDialogVisible.value = false
    loadTypeList()
  } catch (e) {
    // 验证或请求失败
  } finally {
    typeSubmitLoading.value = false
  }
}

// 数据操作
const handleAddData = () => {
  dataDialogTitle.value = '新增字典数据'
  Object.assign(dataForm, { id: '', type_id: currentType.value?.id, label: '', value: '', sort: 0, status: 1, remark: '' })
  dataDialogVisible.value = true
}

const handleEditData = (row) => {
  dataDialogTitle.value = '编辑字典数据'
  Object.assign(dataForm, { ...row, type_id: row.type_id || currentType.value?.id })
  dataDialogVisible.value = true
}

const handleDeleteData = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除字典数据"${row.label}"吗？`, '提示', { type: 'warning' })
    await deleteDictData(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const handleStatusChange = async (row) => {
  try {
    await editDictData({ id: row.id, status: row.status })
    ElMessage.success('状态更新成功')
  } catch (e) {
    row.status = row.status === 1 ? 0 : 1
    ElMessage.error('状态更新失败')
  }
}

const submitDataForm = async () => {
  try {
    await dataFormRef.value?.validate()
    dataSubmitLoading.value = true
    if (dataForm.id) {
      await editDictData(dataForm)
      ElMessage.success('编辑成功')
    } else {
      await addDictData(dataForm)
      ElMessage.success('新增成功')
    }
    dataDialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证或请求失败
  } finally {
    dataSubmitLoading.value = false
  }
}

onMounted(() => {
  loadTypeList()
})
</script>

<style scoped>
.page-container { padding: 0; height: calc(100vh - 180px); display: flex; gap: 16px; }

.dict-data-container {
  padding: 0;
  display: flex;
  gap: 16px;
  height: calc(100vh - 180px);
}

.left-panel {
  width: 260px;
  background: #fff;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.panel-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}

.type-tree-scrollbar {
  flex: 1;
  overflow-y: auto;
}

:deep(.el-tree-node__content) {
  height: 36px;
}

.tree-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding-right: 8px;
}

.node-label {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.node-actions {
  opacity: 0;
  transition: opacity 0.2s;
}

:deep(.el-tree-node__content:hover .node-actions) {
  opacity: 1;
}

.right-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 0;
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

.current-type-tag {
  font-size: 12px;
  color: #409eff;
  background: #ecf5ff;
  padding: 4px 10px;
  border-radius: 4px;
}

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 12px 16px;
  border-top: 1px solid #f0f0f0;
}
</style>

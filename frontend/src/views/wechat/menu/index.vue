<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增菜单</el-button>
        <el-button type="success" :icon="Upload" @click="handlePush">推送到微信</el-button>
      </div>
      <div class="search-bar-right">
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe row-key="id" default-expand-all>
        <el-table-column prop="name" label="菜单名称" min-width="150" />
        <el-table-column prop="type" label="菜单类型" width="120" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.type === 'click'" size="small">点击事件</el-tag>
            <el-tag v-else-if="row.type === 'view'" type="success" size="small">跳转链接</el-tag>
            <el-tag v-else type="info" size="small">小程序</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="key" label="菜单Key" min-width="120" />
        <el-table-column prop="url" label="跳转URL" min-width="200" show-overflow-tooltip />
        <el-table-column prop="sort" label="排序" width="80" align="center" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑菜单' : '新增菜单'" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级菜单">
          <el-select v-model="form.pid" placeholder="请选择上级菜单" clearable style="width: 100%">
            <el-option v-for="item in tableData" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="菜单名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入菜单名称" />
        </el-form-item>
        <el-form-item label="菜单类型" prop="type">
          <el-radio-group v-model="form.type">
            <el-radio value="click">点击事件</el-radio>
            <el-radio value="view">跳转链接</el-radio>
            <el-radio value="miniprogram">小程序</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="菜单Key" prop="key" v-if="form.type === 'click'">
          <el-input v-model="form.key" placeholder="请输入菜单Key" />
        </el-form-item>
        <el-form-item label="跳转URL" prop="url" v-if="form.type === 'view' || form.type === 'miniprogram'">
          <el-input v-model="form.url" placeholder="请输入URL" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="formVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Upload } from '@element-plus/icons-vue'
import { getWechatMenuList, saveWechatMenu, deleteWechatMenu, pushWechatMenu } from '@/api/wechat'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)
const form = reactive({ id: null, pid: 0, name: '', type: 'click', key: '', url: '', sort: 0, status: 1 })
const rules = {
  name: [{ required: true, message: '请输入菜单名称', trigger: 'blur' }],
  type: [{ required: true, message: '请选择菜单类型', trigger: 'change' }]
}

const loadData = async () => {
  loading.value = true
  try { const res = await getWechatMenuList(); tableData.value = res.data || [] }
  catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}

const openForm = (row) => {
  if (row) Object.assign(form, row)
  else form.id = null, form.pid = 0, form.name = '', form.type = 'click', form.key = '', form.url = '', form.sort = 0, form.status = 1
  formVisible.value = true
}

const handleSubmit = async () => {
  if (!form.name) { ElMessage.warning('请输入菜单名称'); return }
  submitLoading.value = true
  try { await saveWechatMenu(form); ElMessage.success(form.id ? '编辑成功' : '新增成功'); formVisible.value = false; loadData() }
  catch (e) { ElMessage.error(e.message || '操作失败') } finally { submitLoading.value = false }
}

const handleDelete = async (row) => {
  try { await ElMessageBox.confirm(`确定删除菜单"${row.name}"吗？`, '提示', { type: 'warning' }); await deleteWechatMenu(row.id); ElMessage.success('删除成功'); loadData() } catch {}
}

const handleStatusChange = async (row) => {
  try { await saveWechatMenu({ id: row.id, status: row.status }); ElMessage.success('状态更新成功') }
  catch { row.status = row.status === 1 ? 0 : 1; ElMessage.error('更新失败') }
}

const handlePush = async () => {
  try { await ElMessageBox.confirm('确定推送菜单到微信服务器吗？', '提示', { type: 'warning' }); await pushWechatMenu(); ElMessage.success('推送成功') } catch {}
}

onMounted(() => loadData())
</script>

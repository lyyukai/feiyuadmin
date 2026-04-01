<template>
  <div class="page-container">
    <!-- 搜索栏：左操作右检索 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" :icon="Plus" @click="openForm()">新增规则</el-button>
      </div>
      <div class="search-bar-right">
        <el-select v-model="searchForm.type" placeholder="回复类型" style="width: 140px" clearable>
          <el-option label="关键词回复" value="keyword" />
          <el-option label="关注回复" value="follow" />
          <el-option label="默认回复" value="default" />
        </el-select>
        <el-button type="primary" :icon="Search" @click="loadData">搜索</el-button>
        <el-button :icon="Refresh" @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <el-card class="table-card" shadow="never">
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="keyword" label="关键词" min-width="120" />
        <el-table-column prop="type" label="类型" width="120" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.type === 'keyword'" type="primary" size="small">关键词回复</el-tag>
            <el-tag v-else-if="row.type === 'follow'" type="success" size="small">关注回复</el-tag>
            <el-tag v-else type="warning" size="small">默认回复</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="reply_type" label="回复类型" width="100" align="center">
          <template #default="{ row }">
            <span>{{ replyTypeMap[row.reply_type] || row.reply_type }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="content" label="回复内容" min-width="200" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" @change="handleStatusChange(row)" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" />
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openForm(row)">编辑</el-button>
            <el-button link type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="formVisible" :title="form.id ? '编辑规则' : '新增规则'" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="规则类型" prop="type">
          <el-radio-group v-model="form.type">
            <el-radio value="keyword">关键词回复</el-radio>
            <el-radio value="follow">关注回复</el-radio>
            <el-radio value="default">默认回复</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="关键词" prop="keyword" v-if="form.type === 'keyword'">
          <el-input v-model="form.keyword" placeholder="请输入关键词" />
        </el-form-item>
        <el-form-item label="回复类型" prop="reply_type">
          <el-radio-group v-model="form.reply_type">
            <el-radio value="text">文本</el-radio>
            <el-radio value="image">图片</el-radio>
            <el-radio value="news">图文</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="回复内容" prop="content">
          <el-input v-model="form.content" type="textarea" :rows="4" placeholder="请输入回复内容" />
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
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getWechatReplyList, addWechatReply, editWechatReply, deleteWechatReply } from '@/api/wechat'

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const formVisible = ref(false)
const formRef = ref(null)
const replyTypeMap = { text: '文本', image: '图片', news: '图文' }
const searchForm = reactive({ type: '' })
const form = reactive({ id: null, type: 'keyword', keyword: '', reply_type: 'text', content: '', status: 1 })
const rules = {
  type: [{ required: true, message: '请选择规则类型', trigger: 'change' }],
  reply_type: [{ required: true, message: '请选择回复类型', trigger: 'change' }],
  content: [{ required: true, message: '请输入回复内容', trigger: 'blur' }]
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getWechatReplyList()
    let list = res.data || []
    if (searchForm.type) list = list.filter(item => item.type === searchForm.type)
    tableData.value = list
  } catch { ElMessage.error('加载数据失败') } finally { loading.value = false }
}
const resetSearch = () => { searchForm.type = ''; loadData() }
const openForm = (row) => {
  if (row) Object.assign(form, row)
  else { form.id = null; form.type = 'keyword'; form.keyword = ''; form.reply_type = 'text'; form.content = ''; form.status = 1 }
  formVisible.value = true
}
const handleSubmit = async () => {
  if (form.type === 'keyword' && !form.keyword) { ElMessage.warning('请输入关键词'); return }
  submitLoading.value = true
  try {
    if (form.id) { await editWechatReply(form); ElMessage.success('编辑成功') }
    else { await addWechatReply(form); ElMessage.success('新增成功') }
    formVisible.value = false; loadData()
  } catch (e) { ElMessage.error(e.message || '操作失败') } finally { submitLoading.value = false }
}
const handleDelete = async (row) => {
  try { await ElMessageBox.confirm('确定删除该规则吗？', '提示', { type: 'warning' }); await deleteWechatReply(row.id); ElMessage.success('删除成功'); loadData() } catch {}
}
const handleStatusChange = async (row) => {
  try { await editWechatReply({ id: row.id, status: row.status }); ElMessage.success('状态更新成功') }
  catch { row.status = row.status === 1 ? 0 : 1; ElMessage.error('更新失败') }
}
onMounted(() => loadData())
</script>

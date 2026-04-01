<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <el-button type="primary" @click="handleAdd">
        <el-icon><Plus /></el-icon> 新增
      </el-button>
      <div class="search-actions">
        <div class="search-item">
          <span class="label">角色名称</span>
          <el-input v-model="searchForm.keyword" placeholder="请输入" style="width: 180px" clearable />
        </div>
        <el-button type="primary" @click="loadData">
          <el-icon><Search /></el-icon> 搜索
        </el-button>
        <el-button @click="resetSearch">重置</el-button>
      </div>
    </div>

    <!-- 表格 -->
    <div class="page-card">
      <div class="card-header">
        <span class="card-title">角色列表</span>
      </div>

      <el-table :data="tableData" v-loading="loading">
        <el-table-column prop="id" label="序号" width="80" align="center" />
        <el-table-column prop="name" label="角色名称" min-width="120" />
        <el-table-column prop="code" label="角色编码" min-width="120" />
        <el-table-column prop="remark" label="备注" min-width="200" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="100" align="center">
          <template #default="{ row }">
            <span :class="['status-tag', row.status === 1 ? 'success' : 'danger']">
              {{ row.status === 1 ? '正常' : '禁用' }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template #default="{ row }">
            <el-button link type="primary" class="action-btn" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="primary" class="action-btn" @click="handlePermission(row)">权限</el-button>
            <el-button link type="danger" class="action-btn" @click="handleDelete(row)">删除</el-button>
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
        />
      </div>
    </div>

    <!-- 角色弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="角色名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入角色名称" />
        </el-form-item>
        <el-form-item label="角色编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入角色编码" />
        </el-form-item>
        <el-form-item label="备注">
          <el-input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入备注" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitLoading">确定</el-button>
      </template>
    </el-dialog>

    <!-- 权限弹窗 -->
    <el-dialog v-model="permissionVisible" title="配置权限" width="500px" destroy-on-close>
      <el-tree
        ref="treeRef"
        :data="menuTree"
        :props="{ label: 'label', children: 'children', disabled: () => false }"
        node-key="id"
        show-checkbox
        default-expand-all
        @check="handleTreeCheck"
      />
      <template #footer>
        <el-button @click="permissionVisible = false">取消</el-button>
        <el-button type="primary" @click="submitPermission" :loading="permissionLoading">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus } from '@element-plus/icons-vue'
import { getRoleList, addRole, editRole, deleteRole, getMenuTree, getRoleMenus, saveRoleMenus } from '@/api'

const loading = ref(false)
const submitLoading = ref(false)
const permissionLoading = ref(false)
const tableData = ref([])
const dialogVisible = ref(false)
const permissionVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref(null)
const treeRef = ref(null)
const currentRoleId = ref(0)

const pagination = reactive({
  page: 1,
  pageSize: 20,
  total: 0
})

const searchForm = reactive({
  keyword: ''
})

const form = reactive({
  id: '',
  name: '',
  code: '',
  remark: '',
  status: 1
})

const rules = {
  name: [{ required: true, message: '请输入角色名称', trigger: 'blur' }],
  code: [{ required: true, message: '请输入角色编码', trigger: 'blur' }]
}

const menuTree = ref([])

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const res = await getRoleList({ page: pagination.page, limit: pagination.pageSize, keyword: searchForm.keyword })
    tableData.value = res.data || []
    pagination.total = res.total || 0
  } catch (e) {
    console.error('加载角色列表失败:', e)
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const resetSearch = () => {
  searchForm.keyword = ''
  pagination.page = 1
  loadData()
}

// 新增
const handleAdd = () => {
  dialogTitle.value = '新增角色'
  Object.assign(form, { id: '', name: '', code: '', remark: '', status: 1 })
  dialogVisible.value = true
}

// 编辑
const handleEdit = (row) => {
  dialogTitle.value = '编辑角色'
  Object.assign(form, { ...row, status: row.status || 1 })
  dialogVisible.value = true
}

// 删除
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除角色"${row.name}"吗？`, '提示', { type: 'warning' })
    await deleteRole({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

// 提交角色表单
const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    
    if (form.id) {
      await editRole(form)
      ElMessage.success('编辑成功')
    } else {
      await addRole(form)
      ElMessage.success('新增成功')
    }
    
    dialogVisible.value = false
    loadData()
  } catch (e) {
    // 验证失败或请求失败
  } finally {
    submitLoading.value = false
  }
}

// 递归设置所有子节点为选中状态
const cascadeCheckChildren = (nodes) => {
  nodes.forEach(node => {
    if (node.children && node.children.length > 0) {
      treeRef.value?.setChecked(node.id, true, false)
      cascadeCheckChildren(node.children)
    }
  })
}

// 配置权限
const handlePermission = async (row) => {
  currentRoleId.value = row.id
  permissionVisible.value = true
  
  try {
    // 加载菜单树
    const treeRes = await getMenuTree()
    menuTree.value = formatTree(treeRes.data || [])
    
    // 加载角色已有菜单
    const roleRes = await getRoleMenus({ id: row.id })
    const checkedIds = roleRes.data || []
    
    // 等待树渲染完成后设置选中状态
    await nextTick()
    if (treeRef.value) {
      treeRef.value.setCheckedKeys(checkedIds)
      // 手动级联：将选中节点的子节点也设为选中
      await nextTick()
      if (checkedIds.length > 0) {
        const allNodes = getAllTreeNodes(menuTree.value)
        checkedIds.forEach(id => {
          const node = allNodes.find(n => n.id === id)
          if (node && node.children && node.children.length > 0) {
            cascadeCheckChildren([node])
          }
        })
      }
    }
  } catch (e) {
    console.error('加载权限数据失败:', e)
    menuTree.value = []
  }
}

// 获取所有树节点（扁平化）
const getAllTreeNodes = (nodes, result = []) => {
  nodes.forEach(node => {
    result.push(node)
    if (node.children && node.children.length > 0) {
      getAllTreeNodes(node.children, result)
    }
  })
  return result
}

// 格式化菜单树
const formatTree = (list) => {
  if (!list || !Array.isArray(list)) return []
  return list.map(item => ({
    id: item.id,
    label: item.name,
    children: item.children && item.children.length > 0 ? formatTree(item.children) : undefined
  }))
}

// 提交权限
const submitPermission = async () => {
  try {
    permissionLoading.value = true
    
    const checkedKeys = treeRef.value?.getCheckedKeys() || []
    const halfKeys = treeRef.value?.getHalfCheckedKeys() || []
    const allKeys = [...checkedKeys, ...halfKeys]
    
    await saveRoleMenus({ id: currentRoleId.value, menu_ids: allKeys })
    ElMessage.success('权限配置成功')
    permissionVisible.value = false
  } catch (e) {
    ElMessage.error('权限配置失败')
  } finally {
    permissionLoading.value = false
  }
}

// 处理树形节点勾选 - 父子联动
const handleTreeCheck = (data, checkInfo) => {
  // 如果当前节点被选中，自动选中所有子节点
  if (checkInfo.checkedKeys.includes(data.id)) {
    if (data.children && data.children.length > 0) {
      data.children.forEach(child => {
        treeRef.value?.setChecked(child.id, true, false)
        // 递归选中孙节点
        const setDeepChildren = (node) => {
          if (node.children) {
            node.children.forEach(c => {
              treeRef.value?.setChecked(c.id, true, false)
              setDeepChildren(c)
            })
          }
        }
        setDeepChildren(child)
      })
    }
  } else {
    // 如果当前节点取消选中，自动取消所有子节点
    if (data.children && data.children.length > 0) {
      const clearDeepChildren = (nodes) => {
        nodes.forEach(node => {
          treeRef.value?.setChecked(node.id, false, false)
          if (node.children && node.children.length > 0) {
            clearDeepChildren(node.children)
          }
        })
      }
      clearDeepChildren(data.children)
    }
  }
}

watch(() => [pagination.page, pagination.pageSize], () => {
  loadData()
})

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }

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
  padding: 12px 16px;
  border-top: 1px solid #f0f0f0;
}
</style>

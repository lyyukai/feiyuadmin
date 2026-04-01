<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <div class="search-bar-left">
        <el-button type="primary" @click="handleAdd(null)">
          <el-icon><Plus /></el-icon> 新增菜单
        </el-button>
      </div>
      <div class="search-bar-right">
        <el-input v-model="searchKeyword" placeholder="菜单名称/路由" style="width: 200px" clearable @keyup.enter="filterTree" />
        <el-button type="primary" @click="filterTree"><el-icon><Search /></el-icon> 搜索</el-button>
        <el-button @click="searchKeyword = ''; filterTree()"><el-icon><Refresh /></el-icon> 重置</el-button>
      </div>
    </div>

    <div class="page-card">
      <div class="card-header">
        <span class="card-title">菜单列表</span>
      </div>

      <!-- 树形菜单列表 -->
      <el-tree
        ref="treeRef"
        :data="treeData"
        node-key="id"
        :props="{ label: 'name', children: 'children' }"
        default-expand-all
        :expand-on-click-node="false"
        highlight-current
      >
        <template #default="{ node, data }">
          <span class="tree-node">
            <span class="node-info">
              <el-icon v-if="data.icon" class="node-icon"><component :is="data.icon" /></el-icon>
              <span class="node-name">{{ data.name }}</span>
              <el-tag v-if="data.menu_type === 'button'" size="small" type="info">按钮</el-tag>
              <el-tag v-else-if="data.menu_type === 'link'" size="small" type="warning">外链</el-tag>
              <el-tag v-else-if="data.menu_type === 'iframe'" size="small" type="success">Iframe</el-tag>
            </span>
            <span class="node-path">{{ data.path || '-' }}</span>
            <span class="node-actions">
              <el-button link type="primary" @click.stop="handleAdd(data)">新增</el-button>
              <el-button link type="primary" @click.stop="handleEdit(data)">编辑</el-button>
              <el-button link type="danger" @click.stop="handleDelete(data)">删除</el-button>
            </span>
          </span>
        </template>
      </el-tree>
    </div>

    <!-- 新增/编辑弹窗 -->
    <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级菜单">
          <el-tree-select
            v-model="form.pid"
            :data="treeSelectData"
            :props="{ label: 'name', value: 'id', children: 'children' }"
            check-strictly
            clearable
            placeholder="请选择上级菜单"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="菜单类型">
          <el-radio-group v-model="form.menu_type">
            <el-radio label="menu">菜单</el-radio>
            <el-radio label="iframe">Iframe</el-radio>
            <el-radio label="link">外链</el-radio>
            <el-radio label="button">按钮</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="菜单名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入菜单名称" />
        </el-form-item>
        <el-form-item label="路由路径">
          <el-input v-model="form.path" placeholder="请输入路由路径" />
        </el-form-item>
        
        <!-- 图标选择器 -->
        <el-form-item label="图标" v-if="form.menu_type === 'menu'">
          <div class="icon-selector">
            <div class="icon-input">
              <el-input v-model="form.icon" placeholder="点击选择或输入图标名称" />
              <el-icon v-if="form.icon" class="preview-icon"><component :is="form.icon" /></el-icon>
            </div>
            <el-popover placement="bottom" :width="400" trigger="click">
              <template #reference>
                <el-button type="primary" plain>选择图标</el-button>
              </template>
              <div class="icon-grid">
                <div class="icon-item" v-for="icon in iconList" :key="icon" @click="selectIcon(icon)">
                  <el-icon><component :is="icon" /></el-icon>
                  <span>{{ icon }}</span>
                </div>
              </div>
            </el-popover>
          </div>
        </el-form-item>
        
        <el-form-item label="组件路径" v-if="form.menu_type === 'menu'">
          <el-input v-model="form.component" placeholder="如: system/user/index" />
        </el-form-item>
        <el-form-item label="权限标识" v-if="form.menu_type === 'button'">
          <el-input v-model="form.permission" placeholder="如: system:user:list" />
        </el-form-item>
        <el-form-item label="排序">
          <el-input-number v-model="form.sort" :min="0" :max="999" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">显示</el-radio>
            <el-radio :label="0">隐藏</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitLoading">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getMenuList, addMenu, editMenu, deleteMenu } from '@/api'

// Element Plus常用图标列表
const iconList = [
  'User', 'UserFilled', 'UserPlus', 'Avatar', 'List', 'Setting', 'Tools', 'Monitor',
  'Folder', 'FolderOpened', 'Document', 'DocumentChecked', 'Tickets', 'Collection',
  'DataAnalysis', 'DataBoard', 'PieChart', 'LineChart', 'Histogram', 'BarChart',
  'Grid', 'Menu', 'Operation', 'Share', 'Connection', 'Link', 'LinkFailed',
  'Position', 'Location', 'LocationInformation', 'Place', 'School', 'Graduate',
  'OfficeBuilding', 'HomeFilled', 'House', 'Shop', 'ShopWindow', 'Goods', 'GoodsFilled',
  'PriceTag', 'Discount', 'Sell', 'ShoppingCart', 'ShoppingCartFull', 'ShoppingTrolley',
  'Bell', 'BellFilled', 'Message', 'MessageSolid', 'ChatDotRound', 'ChatDotSquare',
  'ChatLineRound', 'ChatLineSquare', 'ChatRound', 'ChatSquare', 'Phone', 'PhoneFilled',
  'VideoCamera', 'VideoCameraFilled', 'VideoPause', 'VideoPlay', 'VideoCamera', 'Camera',
  'CameraFilled', 'Microphone', 'Microphone', 'Headset', 'Crown', 'CrownFilled', 'Medal',
  'Trophy', 'TrophyBase', 'Medal', 'Pointer', 'Aim', 'Guide', 'Compass', 'MapLocation',
  'Coordinate', 'Clock', 'Timer', 'Stopwatch', 'Calendar', 'Filter', 'Sort', 'SortUp', 'SortDown',
  'Rank', 'Rising', 'TrendCharts', 'DataLine', 'Promotion', 'HotWater', 'ColdDrink',
  'CoffeeCup', 'IceCream', 'IceCreamRound', 'IceCreamSquare', 'Dessert', 'SetMeal', 'Dish',
  'Bowl', 'KnifeFork', 'TableLamp', 'Lamp', 'LampFilled', 'View', 'ViewInAr', 'Aim',
  'Odometer', 'Speedometer', 'Key', 'Lock', 'Unlock', 'Edit', 'EditPen', 'Edit', 'Brush',
  'Pen', 'Pencil', 'Delete', 'DeleteFilled', 'Delete', 'Plus', 'Plus', 'Minus', 'Plus', 'Close',
  'Check', 'CircleCheck', 'CircleCheckFilled', 'CircleClose', 'CircleCloseFilled', 'SuccessFilled',
  'InfoFilled', 'WarningFilled', 'ErrorFilled', 'QuestionFilled', 'Completed', 'TurnOff', 'Switch',
  'ToggleLeft', 'ToggleRight', 'ArrowLeft', 'ArrowLeft', 'ArrowRight', 'ArrowRight', 'ArrowUp',
  'ArrowUp', 'ArrowDown', 'ArrowDown', 'DArrowLeft', 'DArrowRight', 'ArrowLeftBold', 'ArrowRightBold',
  'ArrowUpBold', 'ArrowDownBold', 'Back', 'Right', 'Top', 'Bottom', 'TopRight', 'TopLeft',
  'BottomRight', 'BottomLeft', 'Upload', 'UploadFilled', 'Download', 'Download', 'Upload', 'Download',
  'Paperclip', 'Attachment', 'Link', 'Connection', 'SetUp', 'Setting', 'Configuration', 'Menu',
  'Operation', 'Refresh', 'RefreshLeft', 'RefreshRight', 'CopyDocument', 'DocumentCopy', 'Document',
  'FolderAdd', 'FolderDelete', 'FolderChecked', 'Tickets', 'Ticket', 'Stamp', 'Files', 'Film',
  'Film', 'Camera', 'Collection', 'Postcard', 'Postcard', 'Mail', 'MailOpen', 'Envelope', 'MessageBox',
  'ChatLineSquare', 'ChatDotSquare', 'ChatSquare', 'ChatLineRound', 'ChatRound', 'ChatDotRound',
  'Phone', 'PhoneOutline', 'PhoneFilled', 'Service', 'Support', 'Ticket', 'Stamp', 'Wallet', 'Coin',
  'Money', 'CreditCard', 'Bankcard', 'Marketing', 'TrendCharts', 'DataAnalysis', 'DataBoard',
  'PieChart', 'LineChart', 'Histogram', 'BarChart', 'Leaf', 'Orange', 'Grape', 'Watermelon',
  'Cherry', 'Apple', 'Pear', 'Watermelon', 'Coffee', 'Beverage', 'IceDrink', 'MilkTea', 'Fruit',
  'Fries', 'Bread', 'Sugar', 'Lollipop', 'Cookie', 'Chocolate', 'Egg', 'Egg', 'Chicken', 'Crop',
  'Cooking', 'Food', 'Dish', 'Bowl', 'ForkSpoon', 'KnifeFork', 'Tableware', 'Sugar', 'Salt', 'Pepper'
]

const loading = ref(false)
const submitLoading = ref(false)
const tableData = ref([])
const treeData = ref([])
const treeRef = ref(null)
const dialogVisible = ref(false)
const searchKeyword = ref('')
const dialogTitle = ref('')
const formRef = ref(null)

const form = reactive({
  id: '',
  pid: 0,
  name: '',
  path: '',
  icon: '',
  component: '',
  menu_type: 'menu',
  permission: '',
  sort: 0,
  status: 1
})

const rules = {
  name: [{ required: true, message: '请输入菜单名称', trigger: 'blur' }]
}

// 构建树形数据
const buildTree = (list, pid = 0) => {
  return list.filter(item => item.pid === pid)
    .map(item => ({
      ...item,
      children: buildTree(list, item.id)
    }))
}

// 用于选择上级菜单的树数据（排除自己和下级）
const treeSelectData = computed(() => {
  const addRoot = [{ id: 0, name: '顶级菜单', children: [] }]
  return [...addRoot, ...buildTree(tableData.value)]
})

// 过滤树
const filterTree = () => {
  if (!searchKeyword.value) {
    treeData.value = buildTree(tableData.value)
    return
  }
  const kw = searchKeyword.value.toLowerCase()
  const filter = (list) => {
    return list.filter(item => {
      const match = (item.name && item.name.toLowerCase().includes(kw)) ||
                    (item.path && item.path.toLowerCase().includes(kw))
      if (item.children) {
        item.children = filter(item.children)
      }
      return match || (item.children && item.children.length > 0)
    })
  }
  treeData.value = filter(JSON.parse(JSON.stringify(buildTree(tableData.value))))
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await getMenuList()
    tableData.value = res.data || []
    filterTree()
  } catch (e) {
    console.error('加载菜单列表失败:', e)
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const selectIcon = (icon) => {
  form.icon = icon
}

const handleAdd = (parent) => {
  dialogTitle.value = parent ? '新增子菜单' : '新增菜单'
  form.id = ''
  form.pid = parent?.id || 0
  form.name = ''
  form.path = ''
  form.icon = ''
  form.component = ''
  form.menu_type = 'menu'
  form.permission = ''
  form.sort = 0
  form.status = 1
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑菜单'
  Object.assign(form, {
    id: row.id,
    pid: row.pid || 0,
    name: row.name,
    path: row.path || '',
    icon: row.icon || '',
    component: row.component || '',
    menu_type: row.menu_type || 'menu',
    permission: row.permission || '',
    sort: row.sort || 0,
    status: row.status !== undefined ? row.status : 1
  })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(`确定删除菜单"${row.name}"吗？`, '提示', { type: 'warning' })
    await deleteMenu({ id: row.id })
    ElMessage.success('删除成功')
    loadData()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error('删除失败')
  }
}

const submitForm = async () => {
  try {
    await formRef.value?.validate()
    submitLoading.value = true
    if (form.id) {
      await editMenu(form)
      ElMessage.success('编辑成功')
    } else {
      await addMenu(form)
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

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.page-container { padding: 0; }
.tree-node {
  display: flex;
  align-items: center;
  width: 100%;
  padding-right: 20px;
}
.node-info {
  display: flex;
  align-items: center;
  flex: 0 0 200px;
}
.node-icon {
  margin-right: 8px;
  font-size: 16px;
}
.node-name {
  margin-right: 8px;
}
.node-path {
  flex: 1;
  color: #999;
  font-size: 12px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.node-actions {
  margin-left: auto;
}
.icon-selector {
  display: flex;
  gap: 10px;
  align-items: center;
}
.icon-input {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
}
.preview-icon {
  font-size: 20px;
}
.icon-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 8px;
  max-height: 300px;
  overflow-y: auto;
}
.icon-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px;
  cursor: pointer;
  border-radius: 4px;
}
.icon-item:hover {
  background: #f5f7fa;
}
.icon-item .el-icon {
  font-size: 20px;
  margin-bottom: 4px;
}
.icon-item span {
  font-size: 10px;
  color: #666;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 100%;
  text-align: center;
}
</style>

<style scoped>
/* 菜单管理树形列表优化 */
.page-card :deep(.el-tree) {
  background: var(--fe-bg-page);
  color: var(--fe-text-primary);
  padding: 8px;
  border-radius: 8px;
}

.page-card :deep(.el-tree-node__content) {
  height: 44px;
  line-height: 44px;
  margin: 2px 0;
  padding: 0 12px;
  border-radius: 6px;
  transition: all 0.2s;
  border-left: 3px solid transparent;
}

.page-card :deep(.el-tree-node__content:hover) {
  background: var(--fe-bg-hover);
}

.page-card :deep(.el-tree-node.is-expanded > .el-tree-node__children) {
  background: var(--fe-bg-page);
  border-left: 2px solid var(--fe-border-color);
  border-radius: 8px;
  margin-left: 32px;
  padding: 4px 0;
}

.page-card :deep(.el-tree-node.is-current > .el-tree-node__content) {
  background: var(--fe-bg-active) !important;
  border-left-color: var(--fe-primary);
}

.page-card :deep(.el-tree-node__expand-icon) {
  color: var(--fe-text-secondary);
}

.page-card :deep(.el-tree-node__label) {
  font-size: 14px;
}
</style>

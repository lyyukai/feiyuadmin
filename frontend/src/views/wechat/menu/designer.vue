<template>
  <div class="page-container">
    <el-row :gutter="20">
      <!-- 左侧：公众号选择和菜单配置 -->
      <el-col :span="12">
        <el-card shadow="never">
          <template #header>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span>菜单设计</span>
              <div>
                <el-select v-model="currentAccountId" placeholder="选择公众号" style="width: 150px; margin-right: 10px;" @change="loadMenuList">
                  <el-option v-for="item in accountList" :key="item.id" :label="item.name" :value="item.id" />
                </el-select>
                <el-button type="primary" size="small" @click="saveMenu">保存菜单</el-button>
                <el-button type="success" size="small" @click="pushMenu">推送到微信</el-button>
              </div>
            </div>
          </template>

          <div class="menu-designer">
            <!-- 菜单预览 -->
            <div class="menu-preview">
              <div class="preview-header">菜单预览</div>
              <div class="preview-content">
                <div class="preview-menu-bar">
                  <div v-for="(btn, index) in menuData.button" :key="index" class="preview-menu-item" :class="{ active: selectedMenu && selectedMenu.index === index }" @click="selectMenu(index)">
                    {{ btn.name }}
                    <div v-if="btn.sub_button && btn.sub_button.length" class="preview-submenu">
                      <div v-for="(sub, subIndex) in btn.sub_button" :key="subIndex" class="preview-submenu-item" :class="{ active: selectedMenu && selectedMenu.index === index && selectedMenu.subIndex === subIndex }" @click.stop="selectSubMenu(index, subIndex)">
                        {{ sub.name }}
                      </div>
                    </div>
                  </div>
                  <div v-if="menuData.button.length < 3" class="preview-menu-item add-btn" @click="addMenu">
                    <el-icon><Plus /></el-icon>
                  </div>
                </div>
              </div>
            </div>

            <!-- 菜单编辑 -->
            <div class="menu-editor" v-if="selectedMenu">
              <div class="editor-title">
                {{ selectedMenu.isSub ? '子菜单编辑' : '菜单编辑' }}
              </div>

              <el-form label-width="80px" size="small">
                <el-form-item label="菜单名称">
                  <el-input v-model="selectedMenu.data.name" placeholder="请输入菜单名称" maxlength="5" show-word-limit />
                </el-form-item>

                <template v-if="selectedMenu.isSub">
                  <el-form-item label="菜单类型">
                    <el-radio-group v-model="selectedMenu.data.type">
                      <el-radio value="click">点击事件</el-radio>
                      <el-radio value="view">跳转链接</el-radio>
                      <el-radio value="miniprogram">小程序</el-radio>
                    </el-radio-group>
                  </el-form-item>

                  <el-form-item label="菜单Key" v-if="selectedMenu.data.type === 'click'">
                    <el-input v-model="selectedMenu.data.key" placeholder="请输入菜单Key" />
                  </el-form-item>

                  <el-form-item label="跳转URL" v-if="selectedMenu.data.type === 'view'">
                    <el-input v-model="selectedMenu.data.url" placeholder="请输入跳转URL" />
                  </el-form-item>

                  <template v-if="selectedMenu.data.type === 'miniprogram'">
                    <el-form-item label="小程序AppID">
                      <el-input v-model="selectedMenu.data.appid" placeholder="请输入小程序AppID" />
                    </el-form-item>
                    <el-form-item label="小程序页面">
                      <el-input v-model="selectedMenu.data.pagepath" placeholder="请输入小程序页面路径" />
                    </el-form-item>
                    <el-form-item label="备用URL">
                      <el-input v-model="selectedMenu.data.url" placeholder="旧版微信客户端无法跳转小程序时，此链接可打开" />
                    </el-form-item>
                  </template>
                </template>

                <template v-else>
                  <el-form-item label="">
                    <el-checkbox v-model="hasSubMenu" @change="toggleSubMenu">添加子菜单</el-checkbox>
                  </el-form-item>

                  <template v-if="!hasSubMenu">
                    <el-form-item label="菜单类型">
                      <el-radio-group v-model="selectedMenu.data.type">
                        <el-radio value="click">点击事件</el-radio>
                        <el-radio value="view">跳转链接</el-radio>
                        <el-radio value="miniprogram">小程序</el-radio>
                      </el-radio-group>
                    </el-form-item>

                    <el-form-item label="菜单Key" v-if="selectedMenu.data.type === 'click'">
                      <el-input v-model="selectedMenu.data.key" placeholder="请输入菜单Key" />
                    </el-form-item>

                    <el-form-item label="跳转URL" v-if="selectedMenu.data.type === 'view'">
                      <el-input v-model="selectedMenu.data.url" placeholder="请输入跳转URL" />
                    </el-form-item>

                    <template v-if="selectedMenu.data.type === 'miniprogram'">
                      <el-form-item label="小程序AppID">
                        <el-input v-model="selectedMenu.data.appid" placeholder="请输入小程序AppID" />
                      </el-form-item>
                      <el-form-item label="小程序页面">
                        <el-input v-model="selectedMenu.data.pagepath" placeholder="请输入小程序页面路径" />
                      </el-form-item>
                      <el-form-item label="备用URL">
                        <el-input v-model="selectedMenu.data.url" placeholder="旧版微信客户端无法跳转小程序时，此链接可打开" />
                      </el-form-item>
                    </template>
                  </template>
                </template>

                <el-form-item>
                  <el-button type="danger" size="small" @click="deleteMenu">删除菜单</el-button>
                </el-form-item>
              </el-form>
            </div>

            <div class="menu-editor" v-else style="text-align: center; color: #909399; padding: 40px;">
              <p>请点击左侧菜单进行编辑</p>
              <p style="font-size: 12px;">最多添加3个一级菜单，每个一级菜单下最多添加5个子菜单</p>
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- 右侧：历史菜单 -->
      <el-col :span="12">
        <el-card shadow="never">
          <template #header>
            <span>历史菜单</span>
          </template>
          <el-table :data="menuHistoryList" stripe size="small">
            <el-table-column prop="name" label="菜单名称" min-width="100" />
            <el-table-column prop="status" label="状态" width="80" align="center">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'info'" size="small">
                  {{ row.status === 1 ? '已发布' : '未发布' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="update_time" label="更新时间" width="160" />
            <el-table-column label="操作" width="120" align="center">
              <template #default="{ row }">
                <el-button link type="primary" size="small" @click="loadMenu(row)">编辑</el-button>
                <el-button link type="danger" size="small" @click="deleteMenuHistory(row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { getWechatAccountList, getWechatMenuList, getWechatMenuDetail, saveWechatMenu, deleteWechatMenu, pushWechatMenu } from '@/api/wechat'

const currentAccountId = ref(null)
const accountList = ref([])
const menuData = reactive({ button: [] })
const selectedMenu = ref(null)
const hasSubMenu = ref(false)
const menuHistoryList = ref([])

// 加载公众号列表
const loadAccounts = async () => {
  try {
    const res = await getWechatAccountList({ page: 1, limit: 100 })
    accountList.value = res.data || []
    if (accountList.value.length > 0) {
      currentAccountId.value = accountList.value[0].id
      loadMenuList()
    }
  } catch (e) {
    console.error(e)
  }
}

// 加载菜单列表
const loadMenuList = async () => {
  if (!currentAccountId.value) return
  try {
    const res = await getWechatMenuList({ account_id: currentAccountId.value })
    menuHistoryList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

// 选择一级菜单
const selectMenu = (index) => {
  const btn = menuData.button[index]
  selectedMenu.value = {
    index,
    subIndex: null,
    isSub: false,
    data: btn
  }
  hasSubMenu.value = btn.sub_button && btn.sub_button.length > 0
}

// 选择子菜单
const selectSubMenu = (index, subIndex) => {
  const btn = menuData.button[index]
  if (btn.sub_button && btn.sub_button[subIndex]) {
    selectedMenu.value = {
      index,
      subIndex,
      isSub: true,
      data: btn.sub_button[subIndex]
    }
    hasSubMenu.value = false
  }
}

// 添加一级菜单
const addMenu = () => {
  if (menuData.button.length >= 3) {
    ElMessage.warning('最多只能添加3个一级菜单')
    return
  }
  menuData.button.push({
    type: 'click',
    name: '菜单名称',
    key: '',
    url: '',
    sub_button: []
  })
  selectMenu(menuData.button.length - 1)
}

// 添加子菜单
const addSubMenu = () => {
  if (!selectedMenu.value || selectedMenu.value.isSub) return
  const btn = menuData.button[selectedMenu.value.index]
  if (!btn.sub_button) btn.sub_button = []
  if (btn.sub_button.length >= 5) {
    ElMessage.warning('最多只能添加5个子菜单')
    return
  }
  btn.sub_button.push({
    type: 'click',
    name: '子菜单',
    key: '',
    url: ''
  })
  selectSubMenu(selectedMenu.value.index, btn.sub_button.length - 1)
}

// 切换子菜单
const toggleSubMenu = (val) => {
  if (!selectedMenu.value || selectedMenu.value.isSub) return
  const btn = menuData.button[selectedMenu.value.index]
  if (val) {
    btn.sub_button = []
    btn.type = ''
    btn.key = ''
    btn.url = ''
  } else {
    delete btn.sub_button
  }
}

// 删除菜单
const deleteMenu = () => {
  if (!selectedMenu.value) return
  if (selectedMenu.value.isSub) {
    const btn = menuData.button[selectedMenu.value.index]
    btn.sub_button.splice(selectedMenu.value.subIndex, 1)
  } else {
    menuData.button.splice(selectedMenu.value.index, 1)
  }
  selectedMenu.value = null
  hasSubMenu.value = false
}

// 保存菜单
const saveMenu = async () => {
  if (!currentAccountId.value) {
    ElMessage.warning('请选择公众号')
    return
  }

  // 验证菜单
  for (const btn of menuData.button) {
    if (!btn.sub_button || btn.sub_button.length === 0) {
      if (!btn.name) {
        ElMessage.warning('菜单名称不能为空')
        return
      }
    } else {
      for (const sub of btn.sub_button) {
        if (!sub.name) {
          ElMessage.warning('子菜单名称不能为空')
          return
        }
      }
    }
  }

  try {
    const existingMenu = menuHistoryList.value.find(m => m.account_id === currentAccountId.value)
    await saveWechatMenu({
      id: existingMenu?.id || 0,
      account_id: currentAccountId.value,
      name: '自定义菜单',
      menu_data: menuData.button
    })
    ElMessage.success('保存成功')
    loadMenuList()
  } catch (e) {
    console.error(e)
  }
}

// 推送菜单
const pushMenu = async () => {
  if (!currentAccountId.value) {
    ElMessage.warning('请选择公众号')
    return
  }
  try {
    await ElMessageBox.confirm('确定推送菜单到微信服务器吗？', '提示', { type: 'warning' })
    await pushWechatMenu()
    ElMessage.success('推送成功')
    loadMenuList()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

// 加载历史菜单
const loadMenu = async (row) => {
  try {
    const res = await getWechatMenuDetail({ id: row.id })
    menuData.button = res.data?.menu_data || []
    selectedMenu.value = null
    hasSubMenu.value = false
  } catch (e) {
    console.error(e)
    menuData.button = []
  }
}

// 删除历史菜单
const deleteMenuHistory = async (row) => {
  try {
    await ElMessageBox.confirm('确定删除该菜单？', '提示', { type: 'warning' })
    await deleteWechatMenu(row.id)
    ElMessage.success('删除成功')
    loadMenuList()
  } catch (e) {
    if (e !== 'cancel') console.error(e)
  }
}

onMounted(() => {
  loadAccounts()
})
</script>

<style scoped>
.menu-designer {
  min-height: 500px;
}

.menu-preview {
  margin-bottom: 20px;
}

.preview-header {
  background: #f5f5f5;
  padding: 10px;
  text-align: center;
  font-size: 14px;
  color: #666;
}

.preview-content {
  border: 1px solid #e5e5e5;
  border-top: none;
  padding: 20px;
}

.preview-menu-bar {
  display: flex;
  justify-content: center;
  gap: 2px;
}

.preview-menu-item {
  position: relative;
  min-width: 80px;
  padding: 10px 15px;
  background: #f5f5f5;
  border: 1px solid #e5e5e5;
  text-align: center;
  cursor: pointer;
  font-size: 14px;
}

.preview-menu-item:hover,
.preview-menu-item.active {
  background: #e5e5e5;
}

.preview-menu-item.add-btn {
  color: #409eff;
}

.preview-submenu {
  display: none;
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  background: #fff;
  border: 1px solid #e5e5e5;
  min-width: 100px;
}

.preview-menu-item:hover .preview-submenu {
  display: block;
}

.preview-submenu-item {
  padding: 10px;
  border-bottom: 1px solid #e5e5e5;
  white-space: nowrap;
}

.preview-submenu-item:last-child {
  border-bottom: none;
}

.preview-submenu-item:hover,
.preview-submenu-item.active {
  background: #f5f5f5;
}

.menu-editor {
  padding: 20px;
  background: #fafafa;
  border-radius: 4px;
}

.editor-title {
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e5e5e5;
}
</style>

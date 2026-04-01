<template>
  <div class="workflow-designer">
    <!-- 左侧工具栏 -->
    <div class="left-panel">
      <div class="panel-title">流程节点</div>
      <div class="node-list">
        <div class="node-item" draggable @dragstart="onDragStart($event, 'start')">
          <div class="node-icon start">
            <el-icon><VideoPlay /></el-icon>
          </div>
          <span>开始节点</span>
        </div>
        <div class="node-item" draggable @dragstart="onDragStart($event, 'approver')">
          <div class="node-icon approver">
            <el-icon><User /></el-icon>
          </div>
          <span>审批人节点</span>
        </div>
        <div class="node-item" draggable @dragstart="onDragStart($event, 'condition')">
          <div class="node-icon condition">
            <el-icon><Connection /></el-icon>
          </div>
          <span>条件分支</span>
        </div>
        <div class="node-item" draggable @dragstart="onDragStart($event, 'end')">
          <div class="node-icon end">
            <el-icon><CircleCheck /></el-icon>
          </div>
          <span>结束节点</span>
        </div>
      </div>

      <div class="panel-title" style="margin-top: 20px">属性配置</div>
      <div class="property-panel" v-if="selectedNode">
        <el-form label-width="80px" size="small">
          <el-form-item label="节点名称">
            <el-input v-model="selectedNode.node_name" placeholder="请输入节点名称" />
          </el-form-item>
          <el-form-item label="节点标识">
            <el-input v-model="selectedNode.node_key" placeholder="请输入节点标识" />
          </el-form-item>

          <!-- 审批人节点配置 -->
          <template v-if="selectedNode.node_type === 'approver'">
            <el-form-item label="审批类型">
              <el-radio-group v-model="selectedNode.config.approval_type" @change="handleApprovalTypeChange">
                <el-radio label="approve">单人审批</el-radio>
                <el-radio label="counter_sign">会签</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="审批人">
              <el-button type="primary" size="small" @click="showApproverSelect">选择审批人</el-button>
              <div class="approver-tags" v-if="selectedNode.config.approvers?.length">
                <el-tag
                  v-for="(item, index) in selectedNode.config.approvers"
                  :key="index"
                  closable
                  @close="removeApprover(index)"
                  style="margin-right: 5px; margin-top: 5px"
                >
                  {{ item.name }}
                </el-tag>
              </div>
            </el-form-item>
          </template>

          <!-- 条件分支配置 -->
          <template v-if="selectedNode.node_type === 'condition'">
            <el-form-item label="条件配置">
              <div class="condition-list">
                <div v-for="(condition, index) in selectedNode.config.conditions" :key="index" class="condition-item">
                  <el-select v-model="condition.field" placeholder="字段" style="width: 80px">
                    <el-option label="金额" value="amount" />
                    <el-option label="类型" value="type" />
                  </el-select>
                  <el-select v-model="condition.operator" placeholder="操作符" style="width: 80px; margin-left: 5px">
                    <el-option label="等于" value="==" />
                    <el-option label="大于" value=">" />
                    <el-option label="小于" value="<" />
                    <el-option label="包含" value="contains" />
                  </el-select>
                  <el-input v-model="condition.value" placeholder="值" style="width: 60px; margin-left: 5px" />
                  <el-button type="danger" size="small" text @click="removeCondition(index)" style="margin-left: 5px">删</el-button>
                </div>
              </div>
              <el-button type="primary" size="small" @click="addCondition" style="margin-top: 5px">添加条件</el-button>
            </el-form-item>
          </template>
        </el-form>
      </div>
      <div class="property-panel empty" v-else>
        <p>请选择节点进行配置</p>
      </div>
    </div>

    <!-- 中间画布 -->
    <div class="canvas-area">
      <div class="canvas-header">
        <el-button @click="goBack">返回列表</el-button>
        <span class="flow-name">{{ workflowName }}</span>
        <el-button type="primary" @click="saveDesign" :loading="saving">保存</el-button>
      </div>
      <div
        class="canvas"
        ref="canvasRef"
        @drop="onDrop"
        @dragover.prevent
        @click="onCanvasClick"
      >
        <!-- 节点 -->
        <div
          v-for="node in nodes"
          :key="node.node_key"
          class="flow-node"
          :class="[node.node_type, { selected: selectedNode?.node_key === node.node_key }]"
          :style="{ left: node.position_x + 'px', top: node.position_y + 'px' }"
          @click.stop="selectNode(node)"
          @mousedown="onNodeMouseDown($event, node)"
        >
          <div class="node-icon-wrapper">
            <el-icon v-if="node.node_type === 'start'"><VideoPlay /></el-icon>
            <el-icon v-else-if="node.node_type === 'approver'"><User /></el-icon>
            <el-icon v-else-if="node.node_type === 'condition'"><Connection /></el-icon>
            <el-icon v-else-if="node.node_type === 'end'"><CircleCheck /></el-icon>
          </div>
          <div class="node-label">{{ node.node_name || '未命名' }}</div>
        </div>

        <!-- 连线 SVG 层 -->
        <svg class="edges-svg" ref="edgesSvgRef">
          <defs>
            <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
              <polygon points="0 0, 10 3.5, 0 7" fill="#409eff" />
            </marker>
          </defs>
          <path
            v-for="edge in edges"
            :key="edge.id"
            :d="getEdgePath(edge)"
            stroke="#409eff"
            stroke-width="2"
            fill="none"
            marker-end="url(#arrowhead)"
            @click.stop="selectEdge(edge)"
            class="edge-path"
            :class="{ selected: selectedEdge?.id === edge.id }"
          />
        </svg>
      </div>
    </div>

    <!-- 右侧连线配置 -->
    <div class="right-panel">
      <div class="panel-title">连线配置</div>
      <div class="property-panel" v-if="selectedEdge">
        <el-form label-width="80px" size="small">
          <el-form-item label="连线类型">
            <el-radio-group v-model="selectedEdge.edge_type" @change="handleEdgeTypeChange">
              <el-radio label="default">默认</el-radio>
              <el-radio label="condition">条件</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="连线标签">
            <el-input v-model="selectedEdge.label" placeholder="请输入连线标签" />
          </el-form-item>
          <template v-if="selectedEdge.edge_type === 'condition'">
            <el-form-item label="条件配置">
              <el-select v-model="selectedEdge.condition_config.field" placeholder="字段" style="width: 100%">
                <el-option label="金额" value="amount" />
                <el-option label="类型" value="type" />
              </el-select>
              <el-select v-model="selectedEdge.condition_config.operator" placeholder="操作符" style="width: 100%; margin-top: 5px">
                <el-option label="等于" value="==" />
                <el-option label="不等于" value="!=" />
                <el-option label="大于" value=">" />
                <el-option label="小于" value="<" />
                <el-option label="大于等于" value=">=" />
                <el-option label="小于等于" value="<=" />
                <el-option label="包含" value="contains" />
              </el-select>
              <el-input v-model="selectedEdge.condition_config.value" placeholder="值" style="width: 100%; margin-top: 5px" />
            </el-form-item>
          </template>
          <el-form-item>
            <el-button type="danger" size="small" @click="deleteEdge">删除连线</el-button>
          </el-form-item>
        </el-form>
      </div>
      <div class="property-panel empty" v-else>
        <p>选择连线进行配置</p>
        <p style="margin-top: 10px; font-size: 12px; color: #909399">提示：在画布上拖拽两个节点可以创建连线</p>
      </div>

      <div class="panel-title" style="margin-top: 20px">操作提示</div>
      <div class="tips">
        <p>1. 从左侧拖拽节点到画布</p>
        <p>2. 点击节点选中并配置属性</p>
        <p>3. 拖拽节点边缘创建连线</p>
        <p>4. 画布右键点击创建连线</p>
        <p>5. 确保有且只有一个开始和结束节点</p>
      </div>
    </div>

    <!-- 审批人选择弹窗 -->
    <el-dialog v-model="approverDialogVisible" title="选择审批人" width="500px" destroy-on-close>
      <el-transfer
        v-model="selectedApprovers"
        :data="userList"
        :titles="['可选用户', '已选审批人']"
        filterable
        filter-placeholder="搜索用户"
      />
      <template #footer>
        <el-button @click="approverDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="confirmApprovers">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { VideoPlay, User, Connection, CircleCheck } from '@element-plus/icons-vue'
import { useRouter, useRoute } from 'vue-router'
import workflowApi from '@/api/workflow'

const router = useRouter()
const route = useRoute()

const workflowId = ref(route.query.id || '')
const workflowName = ref('新流程')
const saving = ref(false)
const loading = ref(false)

// 画布相关
const canvasRef = ref(null)
const edgesSvgRef = ref(null)
const nodes = ref([])
const edges = ref([])
const selectedNode = ref(null)
const selectedEdge = ref(null)
const dragNodeType = ref('')
const isDragging = ref(false)
const isConnecting = ref(false)
const connectStartNode = ref(null)

// 审批人相关
const approverDialogVisible = ref(false)
const selectedApprovers = ref([])
const userList = ref([
  { key: 1, label: '张三' },
  { key: 2, label: '李四' },
  { key: 3, label: '王五' },
  { key: 4, label: '赵六' }
])

// 生成唯一ID
const generateId = () => 'n_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
const generateNodeKey = (type) => type + '_' + Date.now()

// 节点拖拽开始
const onDragStart = (event, nodeType) => {
  dragNodeType.value = nodeType
  event.dataTransfer.effectAllowed = 'copy'
}

// 节点放置到画布
const onDrop = (event) => {
  event.preventDefault()
  const rect = canvasRef.value.getBoundingClientRect()
  const x = event.clientX - rect.left
  const y = event.clientY - rect.top

  if (dragNodeType.value) {
    const newNode = {
      id: generateId(),
      node_type: dragNodeType.value,
      node_name: getDefaultNodeName(dragNodeType.value),
      node_key: generateNodeKey(dragNodeType.value),
      position_x: x - 50,
      position_y: y - 20,
      config: getDefaultNodeConfig(dragNodeType.value)
    }
    nodes.value.push(newNode)
    selectNode(newNode)
    dragNodeType.value = ''
  }
}

// 获取默认节点名称
const getDefaultNodeName = (type) => {
  const names = {
    start: '开始',
    approver: '审批人',
    condition: '条件分支',
    end: '结束'
  }
  return names[type] || '新节点'
}

// 获取默认节点配置
const getDefaultNodeConfig = (type) => {
  if (type === 'approver') {
    return {
      approval_type: 'approve',
      approvers: []
    }
  }
  if (type === 'condition') {
    return {
      conditions: []
    }
  }
  return {}
}

// 选择节点
const selectNode = (node) => {
  selectedNode.value = node
  selectedEdge.value = null
}

// 选择连线
const selectEdge = (edge) => {
  selectedEdge.value = edge
  selectedNode.value = null
}

// 点击画布空白处
const onCanvasClick = () => {
  selectedNode.value = null
  selectedEdge.value = null
}

// 节点拖拽移动
const onNodeMouseDown = (event, node) => {
  if (event.button !== 0) return

  const startX = event.clientX
  const startY = event.clientY
  const originalX = node.position_x
  const originalY = node.position_y

  const onMouseMove = (e) => {
    const dx = e.clientX - startX
    const dy = e.clientY - startY
    node.position_x = originalX + dx
    node.position_y = originalY + dy
  }

  const onMouseUp = () => {
    document.removeEventListener('mousemove', onMouseMove)
    document.removeEventListener('mouseup', onMouseUp)
  }

  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

// 计算连线路径
const getEdgePath = (edge) => {
  const sourceNode = nodes.value.find(n => n.node_key === edge.source_key)
  const targetNode = nodes.value.find(n => n.node_key === edge.target_key)

  if (!sourceNode || !targetNode) return ''

  const sx = sourceNode.position_x + 50
  const sy = sourceNode.position_y + 20
  const tx = targetNode.position_x + 50
  const ty = targetNode.position_y + 20

  // 简单直线
  return `M ${sx} ${sy} L ${tx} ${ty}`
}

// 审批人选择相关
const showApproverSelect = () => {
  selectedApprovers.value = selectedNode.value?.config?.approvers?.map(a => a.id) || []
  approverDialogVisible.value = true
}

const confirmApprovers = () => {
  const approvers = selectedApprovers.value.map(id => {
    const user = userList.value.find(u => u.key === id)
    return { id, name: user?.label || '' }
  })
  selectedNode.value.config.approvers = approvers
  approverDialogVisible.value = false
}

const removeApprover = (index) => {
  selectedNode.value.config.approvers.splice(index, 1)
}

// 条件配置
const addCondition = () => {
  if (!selectedNode.value.config.conditions) {
    selectedNode.value.config.conditions = []
  }
  selectedNode.value.config.conditions.push({
    field: 'amount',
    operator: '>',
    value: ''
  })
}

const removeCondition = (index) => {
  selectedNode.value.config.conditions.splice(index, 1)
}

// 连线类型变更
const handleEdgeTypeChange = () => {
  if (selectedEdge.value.edge_type === 'condition') {
    selectedEdge.value.condition_config = { field: '', operator: '==', value: '' }
  }
}

// 删除连线
const deleteEdge = () => {
  if (!selectedEdge.value) return
  const index = edges.value.findIndex(e => e.id === selectedEdge.value.id)
  if (index !== -1) {
    edges.value.splice(index, 1)
    selectedEdge.value = null
  }
}

// 保存设计
const saveDesign = async () => {
  if (!workflowId.value) {
    ElMessage.error('缺少流程ID，请先保存基本信息')
    return
  }

  // 验证
  const startNodes = nodes.value.filter(n => n.node_type === 'start')
  const endNodes = nodes.value.filter(n => n.node_type === 'end')

  if (startNodes.length !== 1) {
    return ElMessage.error('必须有且只有一个开始节点')
  }
  if (endNodes.length !== 1) {
    return ElMessage.error('必须有且只有一个结束节点')
  }
  if (nodes.value.filter(n => n.node_type === 'approver').length === 0) {
    return ElMessage.error('至少需要一个审批人节点')
  }

  saving.value = true
  try {
    const flowData = {
      nodes: nodes.value,
      edges: edges.value
    }
    await workflowApi.edit({
      id: workflowId.value,
      flow_data: flowData
    })
    ElMessage.success('保存成功')
  } catch (e) {
    console.error(e)
  } finally {
    saving.value = false
  }
}

// 返回列表
const goBack = () => {
  router.push('/workflow/list')
}

// 加载数据
const loadData = async () => {
  if (!workflowId.value) return

  loading.value = true
  try {
    const res = await workflowApi.detail(workflowId.value)
    workflowName.value = res.name || '流程设计'
    const flowData = res.flow_data || {}
    nodes.value = flowData.nodes || []
    edges.value = flowData.edges || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (workflowId.value) {
    loadData()
  }
})
</script>

<style scoped>
.workflow-designer {
  display: flex;
  height: calc(100vh - 140px);
  background: #f5f7fa;
}

.left-panel,
.right-panel {
  width: 280px;
  background: #fff;
  border-right: 1px solid #e4e7ed;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.right-panel {
  border-right: none;
  border-left: 1px solid #e4e7ed;
}

.panel-title {
  padding: 12px 16px;
  font-weight: 600;
  font-size: 14px;
  color: #303133;
  border-bottom: 1px solid #f0f0f0;
}

.node-list {
  padding: 12px;
}

.node-item {
  display: flex;
  align-items: center;
  padding: 10px 12px;
  margin-bottom: 8px;
  background: #f5f7fa;
  border-radius: 6px;
  cursor: move;
  transition: all 0.2s;
}

.node-item:hover {
  background: #ecf5ff;
  box-shadow: 0 2px 8px rgba(64, 158, 255, 0.2);
}

.node-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
  color: #fff;
}

.node-icon.start { background: #67c23a; }
.node-icon.approver { background: #409eff; }
.node-icon.condition { background: #e6a23c; }
.node-icon.end { background: #f56c6c; }

.node-item span {
  font-size: 13px;
  color: #606266;
}

.property-panel {
  padding: 12px;
  flex: 1;
}

.property-panel.empty {
  color: #909399;
  text-align: center;
  padding-top: 60px;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.approver-tags {
  margin-top: 8px;
}

.condition-list {
  margin-top: 8px;
}

.condition-item {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.tips {
  padding: 12px;
  font-size: 12px;
  color: #909399;
  line-height: 1.8;
}

.tips p {
  margin: 0;
}

.canvas-area {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.canvas-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #fff;
  border-bottom: 1px solid #e4e7ed;
}

.flow-name {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.canvas {
  flex: 1;
  position: relative;
  overflow: auto;
  background-image: radial-gradient(circle, #dcdfe6 1px, transparent 1px);
  background-size: 20px 20px;
}

.flow-node {
  position: absolute;
  width: 100px;
  height: 40px;
  background: #fff;
  border: 2px solid #dcdfe6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: move;
  user-select: none;
  transition: box-shadow 0.2s, border-color 0.2s;
}

.flow-node:hover {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

.flow-node.selected {
  border-color: #409eff;
  box-shadow: 0 0 0 3px rgba(64, 158, 255, 0.3);
}

.flow-node.start {
  border-color: #67c23a;
  background: linear-gradient(135deg, #f0f9eb 0%, #fff 100%);
}

.flow-node.approver {
  border-color: #409eff;
  background: linear-gradient(135deg, #ecf5ff 0%, #fff 100%);
}

.flow-node.condition {
  border-color: #e6a23c;
  background: linear-gradient(135deg, #fdf6ec 0%, #fff 100%);
}

.flow-node.end {
  border-color: #f56c6c;
  background: linear-gradient(135deg, #fef0f0 0%, #fff 100%);
}

.node-icon-wrapper {
  margin-right: 6px;
}

.node-icon-wrapper .el-icon {
  font-size: 16px;
}

.flow-node.start .node-icon-wrapper .el-icon { color: #67c23a; }
.flow-node.approver .node-icon-wrapper .el-icon { color: #409eff; }
.flow-node.condition .node-icon-wrapper .el-icon { color: #e6a23c; }
.flow-node.end .node-icon-wrapper .el-icon { color: #f56c6c; }

.node-label {
  font-size: 12px;
  color: #606266;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 60px;
}

.edges-svg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.edge-path {
  pointer-events: stroke;
  cursor: pointer;
}

.edge-path:hover,
.edge-path.selected {
  stroke-width: 3;
  stroke: #67c23a;
}
</style>

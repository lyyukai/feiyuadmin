<template>
  <div class="prompt-list">
    <el-table :data="prompts" border stripe>
      <el-table-column prop="name" label="名称" min-width="150">
        <template #default="{ row }">
          <div class="prompt-name">
            <span>{{ row.name }}</span>
            <el-tag v-if="row.isDefault" size="small" type="success">默认</el-tag>
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="description" label="描述" min-width="200">
        <template #default="{ row }">
          <span class="description-text">{{ row.description || '-' }}</span>
        </template>
      </el-table-column>

      <el-table-column prop="category" label="分类" width="120" class="fe-hide-mobile">
        <template #default="{ row }">
          <el-tag v-if="row.category" size="small">{{ row.category }}</el-tag>
          <span v-else>-</span>
        </template>
      </el-table-column>

      <el-table-column prop="status" label="状态" width="80">
        <template #default="{ row }">
          <el-switch
            :model-value="row.status === 1"
            @change="() => $emit('toggle', row)"
            active-text=""
            inactive-text=""
          />
        </template>
      </el-table-column>

      <el-table-column prop="usageCount" label="使用次数" width="100" align="center" class="fe-hide-mobile">
        <template #default="{ row }">
          <span>{{ row.usageCount || 0 }}</span>
        </template>
      </el-table-column>

      <el-table-column prop="updatedAt" label="更新时间" width="180" class="fe-hide-mobile">
        <template #default="{ row }">
          <span>{{ formatTime(row.updatedAt) }}</span>
        </template>
      </el-table-column>

      <el-table-column label="操作" width="180" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link size="small" @click="$emit('edit', row)">
            编辑
          </el-button>
          <el-button type="success" link size="small" @click="$emit('duplicate', row)">
            复制
          </el-button>
          <el-button type="danger" link size="small" @click="$emit('delete', row)">
            删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script setup>
defineProps({
  prompts: {
    type: Array,
    default: () => [],
  },
})

defineEmits(['edit', 'delete', 'toggle', 'duplicate'])

function formatTime(isoString) {
  if (!isoString) return '-'
  return new Date(isoString).toLocaleString('zh-CN')
}
</script>

<style scoped>
.prompt-list {
  width: 100%;
}

.prompt-name {
  display: flex;
  align-items: center;
  gap: 6px;
}

.description-text {
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-regular);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: block;
  max-width: 200px;
}
</style>

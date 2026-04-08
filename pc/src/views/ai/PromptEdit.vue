<template>
  <el-dialog
    :model-value="modelValue"
    :title="mode === 'create' ? '新建Prompt' : '编辑Prompt'"
    width="600px"
    @update:model-value="$emit('update:modelValue', $event)"
    @close="handleClose"
  >
    <el-form :model="form" :rules="rules" ref="formRef" label-width="90px">
      <el-form-item label="名称" prop="name">
        <el-input v-model="form.name" placeholder="请输入Prompt名称" />
      </el-form-item>

      <el-form-item label="描述" prop="description">
        <el-input v-model="form.description" type="textarea" :rows="2" placeholder="简要描述该Prompt的用途" />
      </el-form-item>

      <el-form-item label="分类" prop="category">
        <el-select v-model="form.category" placeholder="选择分类" clearable style="width: 100%">
          <el-option label="通用" value="通用" />
          <el-option label="代码" value="代码" />
          <el-option label="SQL" value="SQL" />
          <el-option label="文案" value="文案" />
        </el-select>
      </el-form-item>

      <el-form-item label="System Prompt" prop="systemPrompt">
        <el-input
          v-model="form.systemPrompt"
          type="textarea"
          :rows="4"
          placeholder="定义AI助手的角色和行为..."
        />
      </el-form-item>

      <el-form-item label="用户模板" prop="userPromptTemplate">
        <el-input
          v-model="form.userPromptTemplate"
          type="textarea"
          :rows="3"
          placeholder="用户的输入模板，可使用变量占位符 {input}"
        />
      </el-form-item>

      <el-form-item label="状态">
        <el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
        <span style="margin-left: 8px; font-size: 12px; color: var(--fe-text-secondary)">
          {{ form.status === 1 ? '启用' : '禁用' }}
        </span>
      </el-form-item>
    </el-form>

    <template #footer>
      <el-button @click="handleClose">取消</el-button>
      <el-button type="primary" :loading="saving" @click="handleSave">保存</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'

const props = defineProps({
  modelValue: Boolean,
  prompt: Object,
  mode: {
    type: String,
    default: 'create',
  },
})

const emit = defineEmits(['update:modelValue', 'success'])

const formRef = ref(null)
const saving = ref(false)

const form = reactive({
  name: '',
  description: '',
  category: '',
  systemPrompt: '',
  userPromptTemplate: '',
  status: 1,
})

const rules = {
  name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
}

watch(
  () => props.prompt,
  (val) => {
    if (val) {
      Object.assign(form, val)
    } else {
      Object.assign(form, {
        name: '',
        description: '',
        category: '',
        systemPrompt: '',
        userPromptTemplate: '',
        status: 1,
      })
    }
  },
  { immediate: true }
)

function handleClose() {
  emit('update:modelValue', false)
}

async function handleSave() {
  try {
    await formRef.value.validate()
    saving.value = true
    await new Promise(r => setTimeout(r, 500))
    emit('success', { ...form })
    handleClose()
  } catch {
    // validation failed
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
/* scoped styles for dialog form */
</style>

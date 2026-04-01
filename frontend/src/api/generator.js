import request from '@/utils/request'

// ==================== 代码生成器 API ====================

// 数据库配置
export const getGeneratorConfigLists = (params) => request.get('/generator/config_lists', { params })
export const getGeneratorConfigInfo = (params) => request.get('/generator/config_info', { params })
export const addGeneratorConfig = (data) => request.post('/generator/config_add', data)
export const editGeneratorConfig = (data) => request.post('/generator/config_edit', data)
export const deleteGeneratorConfig = (data) => request.post('/generator/config_delete', data)
export const testDbConnection = (data) => request.post('/generator/test_connection', data)

// 模板管理
export const getGeneratorTemplateLists = (params) => request.get('/generator/template_lists', { params })
export const getGeneratorTemplateInfo = (params) => request.get('/generator/template_info', { params })
export const addGeneratorTemplate = (data) => request.post('/generator/template_add', data)
export const editGeneratorTemplate = (data) => request.post('/generator/template_edit', data)
export const deleteGeneratorTemplate = (data) => request.post('/generator/template_delete', data)

// 表结构和代码生成
export const getGeneratorTableLists = (params) => request.get('/generator/table_lists', { params })
export const getGeneratorTableColumns = (params) => request.get('/generator/table_columns', { params })
export const getGeneratorGenTypes = () => request.get('/generator/gen_types')
export const previewGeneratorCode = (params) => request.get('/generator/preview', { params })
export const generateCode = (data) => request.post('/generator/generate', data)

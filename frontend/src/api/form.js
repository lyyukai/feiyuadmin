import request from '@/utils/request'

// 表单设计 - 列表
export const getFormLists = (params) => request.get('/form/lists', { params })

// 表单设计 - 详情
export const getFormInfo = (params) => request.get('/form/info', { params })

// 表单设计 - 添加
export const addForm = (data) => request.post('/form/add', data)

// 表单设计 - 编辑
export const editForm = (data) => request.post('/form/edit', data)

// 表单设计 - 删除
export const deleteForm = (data) => request.post('/form/delete', data)

// 表单设计 - 切换状态
export const toggleFormStatus = (data) => request.post('/form/toggle_status', data)

// 表单数据 - 列表
export const getFormDataList = (params) => request.get('/form/data_list', { params })

// 表单数据 - 提交
export const submitFormData = (data) => request.post('/form/submit_data', data)

// 表单数据 - 删除
export const deleteFormData = (data) => request.post('/form/delete_data', data)

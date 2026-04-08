import request from '@/utils/request'

// AI对话
export const chat = (data) => request.post('/ai/chat/chat', data)
export const getAiProviders = () => request.get('/ai/chat/providers')

// Prompt管理
export const getPromptList = (params) => request.get('/ai/prompt/list', { params })
export const getPromptDetail = (id) => request.get('/ai/prompt/detail', { params: { id } })
export const addPrompt = (data) => request.post('/ai/prompt/save', data)
export const editPrompt = (data) => request.post('/ai/prompt/update', data)
export const deletePrompt = (id) => request.post('/ai/prompt/delete', { id })

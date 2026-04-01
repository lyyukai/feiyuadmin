import request from '@/utils/request'

// ==================== 公众号账号 ====================
export const getWechatAccountList = (params) => request.get('/wechat/account/lists', { params })
export const getWechatAccountConfig = (params) => request.get('/wechat/account/config', { params })
export const addWechatAccount = (data) => request.post('/wechat/account/add', data)
export const editWechatAccount = (data) => request.post('/wechat/account/edit', data)
export const deleteWechatAccount = (id) => request.post('/wechat/account/delete', { id })

// ==================== 微信菜单 ====================
export const getWechatMenuList = (params) => request.get('/wechat/menu/lists', { params })
export const getWechatMenuDetail = (params) => request.get('/wechat/menu/detail', { params })
export const saveWechatMenu = (data) => request.post('/wechat/menu/save', data)
export const deleteWechatMenu = (id) => request.post('/wechat/menu/delete', { id })
export const pushWechatMenu = (id) => request.post('/wechat/menu/push', { id })

// ==================== 自动回复 ====================
export const getWechatReplyList = (params) => request.get('/wechat/reply/lists', { params })
export const addWechatReply = (data) => request.post('/wechat/reply/add', data)
export const editWechatReply = (data) => request.post('/wechat/reply/edit', data)
export const deleteWechatReply = (id) => request.post('/wechat/reply/delete', { id })

// ==================== 素材管理 ====================
export const getWechatMaterialList = (params) => request.get('/wechat/material/lists', { params })
export const uploadWechatMaterial = (data) => request.post('/wechat/material/upload', data)
export const deleteWechatMaterial = (id) => request.post('/wechat/material/delete', { id })
export const getWechatMaterialStatistics = (params) => request.get('/wechat/material/statistics', { params })

// ==================== 粉丝管理 ====================
export const getWechatFansList = (params) => request.get('/wechat/fans/lists', { params })
export const getWechatFansDetail = (id) => request.get('/wechat/fans/detail', { params: { id } })
export const updateWechatFansRemark = (data) => request.post('/wechat/fans/updateRemark', data)
export const setWechatFansBlacklist = (data) => request.post('/wechat/fans/setBlacklist', data)
export const syncWechatFans = (accountId) => request.post('/wechat/fans/sync', { account_id: accountId })
export const getWechatFansStatistics = (params) => request.get('/wechat/fans/statistics', { params })
export const getWechatFansTagList = (params) => request.get('/wechat/fans/tagLists', { params })
export const createWechatFansTag = (data) => request.post('/wechat/fans/createTag', data)
export const deleteWechatFansTag = (id) => request.post('/wechat/fans/deleteTag', { id })

// ==================== 小程序管理 ====================
export const getMiniProgramList = (params) => request.get('/wechat/mini_program/lists', { params })
export const getMiniProgramDetail = (id) => request.get('/wechat/mini_program/detail', { params: { id } })
export const addMiniProgram = (data) => request.post('/wechat/mini_program/add', data)
export const editMiniProgram = (data) => request.post('/wechat/mini_program/edit', data)
export const deleteMiniProgram = (id) => request.post('/wechat/mini_program/delete', { id })
export const getMiniProgramVersionList = (params) => request.get('/wechat/mini_program/versionLists', { params })
export const addMiniProgramVersion = (data) => request.post('/wechat/mini_program/addVersion', data)
export const deleteMiniProgramVersion = (id) => request.post('/wechat/mini_program/deleteVersion', { id })
export const getMiniProgramMemberList = (params) => request.get('/wechat/mini_program/memberLists', { params })
export const addMiniProgramMember = (data) => request.post('/wechat/mini_program/addMember', data)
export const deleteMiniProgramMember = (id) => request.post('/wechat/mini_program/deleteMember', { id })

// ==================== 开放平台 ====================
export const getOpenPlatformList = (params) => request.get('/wechat/open_platform/lists', { params })
export const getOpenPlatformDetail = (id) => request.get('/wechat/open_platform/detail', { params: { id } })
export const addOpenPlatform = (data) => request.post('/wechat/open_platform/add', data)
export const editOpenPlatform = (data) => request.post('/wechat/open_platform/edit', data)
export const deleteOpenPlatform = (id) => request.post('/wechat/open_platform/delete', { id })
export const getOpenPlatformAuthList = (params) => request.get('/wechat/open_platform/authLists', { params })
export const getOpenPlatformAuthDetail = (id) => request.get('/wechat/open_platform/authDetail', { params: { id } })
export const getOpenPlatformPreAuthUrl = (data) => request.post('/wechat/open_platform/getPreAuthUrl', data)

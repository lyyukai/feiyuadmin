import request from '@/utils/request'

// 租户列表
export const getTenantLists = (params) => request.get('/tenant/lists', { params })

// 租户信息
export const getTenantInfo = (id) => request.get('/tenant/info', { params: { id } })

// 添加租户
export const addTenant = (data) => request.post('/tenant/add', data)

// 编辑租户
export const editTenant = (data) => request.post('/tenant/edit', data)

// 删除租户
export const deleteTenant = (data) => request.post('/tenant/delete', data)

// 修改租户状态
export const setTenantStatus = (data) => request.post('/tenant/status', data)

// 租户套餐列表
export const getPackageLists = (params) => request.get('/tenant/package_lists', { params })

// 租户套餐信息
export const getPackageInfo = (id) => request.get('/tenant/package_info', { params: { id } })

// 添加租户套餐
export const addPackage = (data) => request.post('/tenant/package_add', data)

// 编辑租户套餐
export const editPackage = (data) => request.post('/tenant/package_edit', data)

// 删除租户套餐
export const deletePackage = (data) => request.post('/tenant/package_delete', data)

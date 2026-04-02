import request from '@/utils/request'

// 登录
export const login = (data) => request.post('/login', data)

// 退出
export const logout = () => request.post('/logout')

// 获取验证码图片
export const getCaptcha = (key = 'login') => request.get('/captcha/generate', { params: { key } })

// 获取用户信息
export const getUserInfo = () => request.get('/user/info')

// 用户列表
export const getUserList = (params) => request.get('/user/lists', { params })

// 添加用户
export const addUser = (data) => request.post('/user/add', data)

// 编辑用户
export const editUser = (data) => request.post('/user/edit', data)

// 删除用户
export const deleteUser = (data) => request.post('/user/delete', data)

// 获取角色列表
export const getRoleList = (params) => request.get('/role/lists', { params })

// 获取所有角色
export const getAllRoles = () => request.get('/role/all')

// 添加角色
export const addRole = (data) => request.post('/role/add', data)

// 编辑角色
export const editRole = (data) => request.post('/role/edit', data)

// 删除角色
export const deleteRole = (data) => request.post('/role/delete', data)

// 获取角色菜单
export const getRoleMenus = (params) => request.get('/role/menus', { params })

// 保存角色菜单
export const saveRoleMenus = (data) => request.post('/role/menus', data)

// 获取菜单列表
export const getMenuList = () => request.get('/menu/lists')

// 获取菜单树
export const getMenuTree = () => request.get('/menu/tree')

// 获取菜单导航
export const getMenuNav = () => request.get('/menu/nav')

// 添加菜单
export const addMenu = (data) => request.post('/menu/add', data)

// 编辑菜单
export const editMenu = (data) => request.post('/menu/edit', data)

// 删除菜单
export const deleteMenu = (data) => request.post('/menu/delete', data)

// 获取部门列表
export const getDeptList = (params) => request.get('/dept/lists', { params })

// 获取部门树
export const getDeptTree = () => request.get('/dept/tree')

// 添加部门
export const addDept = (data) => request.post('/dept/add', data)

// 编辑部门
export const editDept = (data) => request.post('/dept/edit', data)

// 删除部门
export const deleteDept = (data) => request.post('/dept/delete', data)

// 获取岗位列表
export const getPostList = (params) => request.get('/post/lists', { params })

// 获取所有岗位
export const getAllPosts = () => request.get('/post/all')

// 添加岗位
export const addPost = (data) => request.post('/post/add', data)

// 编辑岗位
export const editPost = (data) => request.post('/post/edit', data)

// 删除岗位
export const deletePost = (data) => request.post('/post/delete', data)

// 获取登录日志
export const getLoginLogList = (params) => request.get('/login_log/lists', { params })

// 获取操作日志
export const getLogList = (params) => request.get('/log/lists', { params })

// 清空登录日志
export const cleanLoginLog = () => request.delete('/login_log/clean')

// 重置密码
export const resetUserPassword = (id) => request.post('/user/resetPwd', { id })

// 批量操作
export const batchUser = (ids, action) => request.post('/user/batch', { ids, action })

// 文件上传
export const uploadImage = (formData) => request.post('/upload/image', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
export const getUploadLists = (params) => request.get('/upload/lists', { params })
export const deleteUpload = (id) => request.post('/upload/delete', { id })

// 数据字典
export const getDictTypeLists = (params) => request.get('/dict/type/lists', { params })
export const addDictType = (data) => request.post('/dict/type/add', data)
export const editDictType = (data) => request.post('/dict/type/edit', data)
export const deleteDictType = (id) => request.post('/dict/type/delete', { id })
export const getDictDataLists = (params) => request.get('/dict/data/lists', { params })
export const addDictData = (data) => request.post('/dict/data/add', data)
export const editDictData = (data) => request.post('/dict/data/edit', data)
export const deleteDictData = (id) => request.post('/dict/data/delete', { id })

// 消息通知
export const getNoticeLists = (params) => request.get('/notice/lists', { params })
export const getNoticeDetail = (id) => request.get('/notice/detail', { params: { id } })
export const sendNotice = (data) => request.post('/notice/send', data)
export const readNotice = (id) => request.post('/notice/read', { id })
export const deleteNotice = (id) => request.post('/notice/delete', { id })
export const sendNoticeMessage = (data) => request.post('/notice/send_message', data)

// 通知渠道
export const getNoticeChannelLists = (params) => request.get('/notice_channel/lists', { params })
export const getNoticeChannelDetail = (id) => request.get('/notice_channel/detail', { params: { id } })
export const addNoticeChannel = (data) => request.post('/notice_channel/add', data)
export const editNoticeChannel = (data) => request.post('/notice_channel/edit', data)
export const deleteNoticeChannel = (id) => request.post('/notice_channel/delete', { id })
export const getNoticeChannelTypes = () => request.get('/notice_channel/types')

// 消息模板
export const getNoticeTemplateLists = (params) => request.get('/notice_template/lists', { params })
export const getNoticeTemplateDetail = (id) => request.get('/notice_template/detail', { params: { id } })
export const addNoticeTemplate = (data) => request.post('/notice_template/add', data)
export const editNoticeTemplate = (data) => request.post('/notice_template/edit', data)
export const deleteNoticeTemplate = (id) => request.post('/notice_template/delete', { id })

// 发送记录
export const getNoticeRecordLists = (params) => request.get('/notice_record/lists', { params })
export const getNoticeRecordDetail = (id) => request.get('/notice_record/detail', { params: { id } })
export const getNoticeRecordStatistics = (params) => request.get('/notice_record/statistics', { params })

// 定时任务
export const getCrontabLists = (params) => request.get('/crontab/lists', { params })
export const addCrontab = (data) => request.post('/crontab/add', data)
export const editCrontab = (data) => request.post('/crontab/edit', data)
export const deleteCrontab = (id) => request.post('/crontab/delete', { id })
export const executeCrontab = (id) => request.post('/crontab/execute', { id })
export const pauseCrontab = (id) => request.post('/crontab/pause', { id })
export const resumeCrontab = (id) => request.post('/crontab/resume', { id })
export const getCrontabLogLists = (params) => request.get('/crontab/log_lists', { params })
export const clearCrontabLogs = (task_id) => request.post('/crontab/clear_logs', { task_id })

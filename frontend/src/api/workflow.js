import request from '@/utils/request'

// 工作流相关 API

export default {
  // ==================== 流程管理 ====================

  /**
   * 获取流程列表
   */
  lists(params) {
    return request.get('workflow/lists', { params })
  },

  /**
   * 获取流程详情
   */
  detail(id) {
    return request.get('workflow/detail', { params: { id } })
  },

  /**
   * 添加流程
   */
  add(data) {
    return request.post('workflow/add', data)
  },

  /**
   * 编辑流程
   */
  edit(data) {
    return request.post('workflow/edit', data)
  },

  /**
   * 删除流程
   */
  delete(id) {
    return request.post('workflow/delete', { id })
  },

  /**
   * 发布流程
   */
  publish(id) {
    return request.post('workflow/publish', { id })
  },

  /**
   * 取消发布
   */
  unpublish(id) {
    return request.post('workflow/unpublish', { id })
  },

  /**
   * 切换状态
   */
  toggleStatus(id) {
    return request.post('workflow/toggle_status', { id })
  },

  // ==================== 实例管理 ====================

  /**
   * 获取实例列表
   */
  instanceLists(params) {
    return request.get('workflow/instance_lists', { params })
  },

  /**
   * 获取实例详情
   */
  instanceDetail(id) {
    return request.get('workflow/instance_detail', { params: { id } })
  },

  /**
   * 获取我的待办
   */
  todoList(params) {
    return request.get('workflow/todo_list', { params })
  },

  /**
   * 发起流程
   */
  start(data) {
    return request.post('workflow/start', data)
  },

  /**
   * 审批操作
   */
  approve(data) {
    return request.post('workflow/approve', data)
  },

  /**
   * 撤回申请
   */
  withdraw(id) {
    return request.post('workflow/withdraw', { id })
  },

  /**
   * 获取实例历史
   */
  instanceHistory(id) {
    return request.get('workflow/instance_history', { params: { id } })
  }
}

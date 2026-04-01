import request from '@/utils/request'

// 大屏列表
export function screenLists(params) {
  return request({
    url: '/adminapi/screen/lists',
    method: 'get',
    params
  })
}

// 大屏详情
export function screenDetail(id) {
  return request({
    url: '/adminapi/screen/detail',
    method: 'get',
    params: { id }
  })
}

// 创建大屏
export function screenCreate(data) {
  return request({
    url: '/adminapi/screen/create',
    method: 'post',
    data
  })
}

// 更新大屏
export function screenUpdate(data) {
  return request({
    url: '/adminapi/screen/update',
    method: 'post',
    data
  })
}

// 删除大屏
export function screenDelete(id) {
  return request({
    url: '/adminapi/screen/delete',
    method: 'post',
    data: { id }
  })
}

// 保存大屏配置
export function screenSaveConfig(data) {
  return request({
    url: '/adminapi/screen/save_config',
    method: 'post',
    data
  })
}

// 添加组件
export function screenAddComponent(data) {
  return request({
    url: '/adminapi/screen/add_component',
    method: 'post',
    data
  })
}

// 更新组件
export function screenUpdateComponent(data) {
  return request({
    url: '/adminapi/screen/update_component',
    method: 'post',
    data
  })
}

// 删除组件
export function screenDeleteComponent(id) {
  return request({
    url: '/adminapi/screen/delete_component',
    method: 'post',
    data: { id }
  })
}

// 设置状态
export function screenSetStatus(id, status) {
  return request({
    url: '/adminapi/screen/set_status',
    method: 'post',
    data: { id, status }
  })
}

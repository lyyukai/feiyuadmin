import request from '@/utils/request'

// ==================== 支付配置 ====================
export const getPayConfigList = (params) => request.get('/pay/config/lists', { params })
export const getPayConfigInfo = (params) => request.get('/pay/config/info', { params })
export const savePayConfig = (data) => request.post('/pay/config/save', data)

// ==================== 支付订单 ====================
export const getPayOrderList = (params) => request.get('/pay/order/lists', { params })
export const getPayOrderDetail = (params) => request.get('/pay/order/detail', { params })
export const closePayOrder = (data) => request.post('/pay/order/close', data)
export const manualPaidOrder = (data) => request.post('/pay/order/manualPaid', data)
export const refundPayOrder = (data) => request.post('/pay/order/refund', data)

// ==================== 退款 ====================
export const getPayRefundList = (params) => request.get('/pay/refund/lists', { params })
export const getPayRefundDetail = (params) => request.get('/pay/refund/detail', { params })
export const applyPayRefund = (data) => request.post('/pay/refund/apply', data)

// ==================== 支付流水 ====================
export const getPayStatementList = (params) => request.get('/pay/statement/lists', { params })
export const getPayStatementDetail = (params) => request.get('/pay/statement/detail', { params })
export const getAvailableAmount = (params) => request.get('/pay/statement/availableAmount', { params })
export const createPayStatement = (data) => request.post('/pay/statement/create', data)

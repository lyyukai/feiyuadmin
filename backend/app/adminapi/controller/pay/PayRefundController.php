<?php
/**
 * 退款管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\pay;

use app\adminapi\controller\BaseAdminController;
use app\service\pay\PayRefundService;
use think\Response;

/**
 * 退款管理
 * Class PayRefundController
 * @package app\adminapi\controller\pay
 */
class PayRefundController extends BaseAdminController
{
    protected PayRefundService $service;

    protected function initialize(): void
    {
        parent::initialize();
        $this->service = new PayRefundService();
    }

    /**
     * 退款列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $result = $this->service->lists($params);
        return $this->success('获取成功', $result['list'], $result['total']);
    }

    /**
     * 退款详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $data = $this->service->detail($id);
        if (!$data) {
            return $this->fail('退款记录不存在');
        }

        return $this->success('获取成功', $data);
    }

    /**
     * 申请退款
     */
    public function apply(): Response
    {
        $data = $this->request->post();

        $order_id = (int) ($data['order_id'] ?? 0);
        $refund_fee = floatval($data['refund_fee'] ?? 0);
        $reason = $data['reason'] ?? '';

        if ($order_id <= 0) {
            return $this->fail('订单ID不能为空');
        }

        if ($refund_fee <= 0) {
            return $this->fail('退款金额必须大于0');
        }

        try {
            $this->service->apply($order_id, $refund_fee, $reason);
            return $this->success('申请成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}

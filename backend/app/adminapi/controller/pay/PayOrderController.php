<?php
/**
 * 支付订单控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\pay;

use app\adminapi\controller\BaseAdminController;
use app\service\pay\PayOrderService;
use think\Response;

/**
 * 支付订单管理
 * Class PayOrderController
 * @package app\adminapi\controller\pay
 */
class PayOrderController extends BaseAdminController
{
    protected PayOrderService $service;

    protected function initialize(): void
    {
        parent::initialize();
        $this->service = new PayOrderService();
    }

    /**
     * 订单列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $result = $this->service->lists($params);
        return $this->success('获取成功', $result['list'], $result['total']);
    }

    /**
     * 订单详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $data = $this->service->detail($id);
        if (!$data) {
            return $this->fail('订单不存在');
        }

        return $this->success('获取成功', $data);
    }

    /**
     * 关闭订单
     */
    public function close(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        try {
            $this->service->close($id);
            return $this->success('关闭成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 手动补单
     */
    public function manualPaid(): Response
    {
        $id = (int) $this->request->post('id', 0);
        $out_trade_no = $this->request->post('out_trade_no', '');
        $pay_time = $this->request->post('pay_time', '');

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        try {
            $this->service->manualPaid($id, $out_trade_no, $pay_time);
            return $this->success('补单成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 退款订单
     */
    public function refund(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        try {
            $this->service->refund($id);
            return $this->success('退款成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}

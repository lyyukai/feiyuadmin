<?php
/**
 * 分账记录控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\pay;

use app\adminapi\controller\BaseAdminController;
use app\common\model\pay\PayStatement as PayStatementModel;
use app\common\model\pay\PayOrder as PayOrderModel;
use think\facade\Db;

/**
 * 分账记录管理
 * Class PayStatementController
 * @package app\adminapi\controller\pay
 */
class PayStatementController extends BaseAdminController
{
    /**
     * 分账列表
     */
    public function lists(): \think\Response
    {
        $page = (int) $this->request->get('page', 1);
        $limit = (int) $this->request->get('limit', 10);
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $order_no = $this->request->get('order_no', '');

        $where = [];
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }
        if ($order_no !== '') {
            $where[] = ['order_no', 'like', "%{$order_no}%"];
        }

        $list = PayStatementModel::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $this->success('获取成功', $list['data'], $list['total']);
    }

    /**
     * 分账详情
     */
    public function detail(): \think\Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = PayStatementModel::find($id);
        if (!$model) {
            return $this->fail('记录不存在');
        }

        return $this->success('获取成功', $model->toArray());
    }

    /**
     * 执行分账
     */
    public function create(): \think\Response
    {
        $data = $this->request->post();

        $order_id = (int) ($data['order_id'] ?? 0);
        $type = $data['type'] ?? 'platform';
        $receiver_type = $data['receiver_type'] ?? 'openid';
        $receiver_id = $data['receiver_id'] ?? '';
        $receiver_name = $data['receiver_name'] ?? '';
        $amount = floatval($data['amount'] ?? 0);

        if ($order_id <= 0) {
            return $this->fail('订单ID不能为空');
        }

        if ($amount <= 0) {
            return $this->fail('分账金额必须大于0');
        }

        // 获取订单
        $order = PayOrderModel::find($order_id);
        if (!$order) {
            return $this->fail('订单不存在');
        }

        if ($order->status !== 'paid') {
            return $this->fail('只有已支付的订单才能分账');
        }

        // 生成单号
        $statement_no = 'S' . date('YmdHis') . rand(1000, 9999);

        $model = PayStatementModel::create([
            'order_id' => $order_id,
            'order_no' => $order->order_no,
            'channel' => $order->channel,
            'type' => $type,
            'receiver_type' => $receiver_type,
            'receiver_id' => $receiver_id,
            'receiver_name' => $receiver_name,
            'amount' => $amount,
            'status' => 'pending',
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        return $this->success('分账记录创建成功', ['id' => $model->id]);
    }

    /**
     * 获取订单可分账金额
     */
    public function getAvailableAmount(): \think\Response
    {
        $order_id = (int) $this->request->get('order_id', 0);

        if ($order_id <= 0) {
            return $this->fail('参数错误');
        }

        $order = PayOrderModel::find($order_id);
        if (!$order) {
            return $this->fail('订单不存在');
        }

        // 已分账金额
        $totalStatement = PayStatementModel::where('order_id', $order_id)
            ->where('status', 'success')
            ->sum('amount');

        $available = $order->paid_fee - $totalStatement;

        return $this->success('获取成功', [
            'paid_fee' => $order->paid_fee,
            'total_statement' => $totalStatement,
            'available' => max(0, $available),
        ]);
    }
}

<?php
/**
 * 退款服务层
 */

declare(strict_types=1);

namespace app\service\pay;

use app\common\model\pay\PayRefund as PayRefundModel;
use app\common\model\pay\PayOrder as PayOrderModel;
use think\facade\Db;

/**
 * 退款服务
 * Class PayRefundService
 * @package app\service\pay
 */
class PayRefundService
{
    /**
     * 退款列表
     */
    public function lists(array $params = []): array
    {
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        $status = $params['status'] ?? '';
        $refund_no = $params['refund_no'] ?? '';
        $channel = $params['channel'] ?? '';

        $where = [];
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }
        if ($refund_no !== '') {
            $where[] = ['refund_no', 'like', "%{$refund_no}%"];
        }
        if ($channel !== '') {
            $where[] = ['channel', '=', $channel];
        }

        $list = PayRefundModel::with(['order'])
            ->where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return ['list' => $list['data'], 'total' => $list['total']];
    }

    /**
     * 退款详情
     */
    public function detail(int $id): array
    {
        $model = PayRefundModel::with(['order'])->find($id);
        if (!$model) {
            return [];
        }
        return $model->toArray();
    }

    /**
     * 申请退款
     */
    public function apply(int $order_id, float $refund_fee, string $reason = ''): PayRefundModel
    {
        $order = PayOrderModel::find($order_id);
        if (!$order) {
            throw new \Exception('订单不存在');
        }

        if ($order->status !== 'paid') {
            throw new \Exception('只有已支付的订单才能退款');
        }

        if ($refund_fee <= 0) {
            throw new \Exception('退款金额必须大于0');
        }

        if ($refund_fee > $order->paid_fee) {
            throw new \Exception('退款金额不能超过实际支付金额');
        }

        $refund_no = 'R' . date('YmdHis') . rand(1000, 9999);

        Db::startTrans();
        try {
            // 创建退款记录
            $refund = PayRefundModel::create([
                'refund_no' => $refund_no,
                'out_refund_no' => '',
                'order_id' => $order_id,
                'channel' => $order->channel,
                'refund_fee' => $refund_fee,
                'total_fee' => $order->paid_fee,
                'reason' => $reason,
                'status' => 'pending',
                'create_time' => date('Y-m-d H:i:s'),
            ]);

            // 更新订单状态
            $order->status = 'refunding';
            $order->update_time = date('Y-m-d H:i:s');
            $order->save();

            Db::commit();
            return $refund;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 退款成功回调
     */
    public function success(int $id, string $out_refund_no = ''): bool
    {
        $refund = PayRefundModel::find($id);
        if (!$refund) {
            return false;
        }

        Db::startTrans();
        try {
            $refund->status = 'success';
            $refund->out_refund_no = $out_refund_no;
            $refund->refund_time = date('Y-m-d H:i:s');
            $refund->update_time = date('Y-m-d H:i:s');
            $refund->save();

            // 更新订单状态
            $order = PayOrderModel::find($refund->order_id);
            if ($order) {
                $order->status = 'refunded';
                $order->update_time = date('Y-m-d H:i:s');
                $order->save();
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 退款失败
     */
    public function fail(int $id, string $reason = ''): bool
    {
        $refund = PayRefundModel::find($id);
        if (!$refund) {
            return false;
        }

        Db::startTrans();
        try {
            $refund->status = 'fail';
            $refund->update_time = date('Y-m-d H:i:s');
            $refund->save();

            // 恢复订单状态
            $order = PayOrderModel::find($refund->order_id);
            if ($order) {
                $order->status = 'paid';
                $order->update_time = date('Y-m-d H:i:s');
                $order->save();
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }
}

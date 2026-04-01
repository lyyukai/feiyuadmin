<?php
/**
 * 支付订单服务层
 */

declare(strict_types=1);

namespace app\service\pay;

use app\common\model\pay\PayOrder as PayOrderModel;
use app\common\model\pay\PayConfig as PayConfigModel;
use think\facade\Db;

/**
 * 支付订单服务
 * Class PayOrderService
 * @package app\service\pay
 */
class PayOrderService
{
    /**
     * 订单列表
     */
    public function lists(array $params = []): array
    {
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        $channel = $params['channel'] ?? '';
        $status = $params['status'] ?? '';
        $order_no = $params['order_no'] ?? '';
        $start_time = $params['start_time'] ?? '';
        $end_time = $params['end_time'] ?? '';

        $where = [];
        if ($channel !== '') {
            $where[] = ['channel', '=', $channel];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }
        if ($order_no !== '') {
            $where[] = ['order_no', 'like', "%{$order_no}%"];
        }
        if ($start_time !== '') {
            $where[] = ['create_time', '>=', $start_time];
        }
        if ($end_time !== '') {
            $where[] = ['create_time', '<=', $end_time . ' 23:59:59'];
        }

        $list = PayOrderModel::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return ['list' => $list['data'], 'total' => $list['total']];
    }

    /**
     * 订单详情
     */
    public function detail(int $id): array
    {
        $model = PayOrderModel::find($id);
        if (!$model) {
            return [];
        }
        return $model->toArray();
    }

    /**
     * 创建订单
     */
    public function create(array $data): PayOrderModel
    {
        $order_no = 'P' . date('YmdHis') . rand(1000, 9999);
        $expire_time = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $order = PayOrderModel::create([
            'order_no' => $order_no,
            'out_trade_no' => $data['out_trade_no'] ?? '',
            'channel' => $data['channel'] ?? 'wechat',
            'subject' => $data['subject'] ?? '',
            'total_fee' => $data['total_fee'] ?? 0,
            'paid_fee' => 0,
            'status' => 'pending',
            'attach' => $data['attach'] ?? '',
            'client_ip' => $data['client_ip'] ?? request()->ip(),
            'expire_time' => $expire_time,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        return $order;
    }

    /**
     * 关闭订单
     */
    public function close(int $id): bool
    {
        $model = PayOrderModel::find($id);
        if (!$model) {
            throw new \Exception('订单不存在');
        }

        if ($model->status !== 'pending') {
            throw new \Exception('只有待支付订单才能关闭');
        }

        $model->status = 'closed';
        $model->update_time = date('Y-m-d H:i:s');

        return $model->save();
    }

    /**
     * 手动补单（标记为已支付）
     */
    public function manualPaid(int $id, string $out_trade_no = '', string $pay_time = ''): bool
    {
        $model = PayOrderModel::find($id);
        if (!$model) {
            throw new \Exception('订单不存在');
        }

        if ($model->status !== 'pending') {
            throw new \Exception('只能对待支付订单进行补单');
        }

        Db::startTrans();
        try {
            $model->status = 'paid';
            $model->out_trade_no = $out_trade_no ?: $model->out_trade_no;
            $model->paid_fee = $model->total_fee;
            $model->pay_time = $pay_time ?: date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');
            $model->save();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 支付回调处理
     */
    public function notify(string $channel, string $out_trade_no, array $data): bool
    {
        $order = PayOrderModel::where('out_trade_no', $out_trade_no)->find();
        if (!$order) {
            return false;
        }

        if ($order->status !== 'pending') {
            return true; // 已处理过
        }

        $order->status = 'paid';
        $order->paid_fee = $data['total_fee'] ?? $order->total_fee;
        $order->pay_time = $data['pay_time'] ?? date('Y-m-d H:i:s');
        $order->update_time = date('Y-m-d H:i:s');

        return $order->save();
    }

    /**
     * 根据订单号查询
     */
    public function getByOrderNo(string $order_no): ?PayOrderModel
    {
        return PayOrderModel::where('order_no', $order_no)->find();
    }
}

<?php
/**
 * 支付订单服务层
 */

declare(strict_types=1);

namespace app\service\pay;

use app\common\model\pay\PayOrder as PayOrderModel;
use think\facade\Db;

/**
 * 支付订单服务
 * Class PayOrderService
 * @package app\service\pay
 */
class PayOrderService
{
    // 前端数字状态 → DB字符串状态 映射
    protected static array $statusMap = [
        0 => 'pending',    // 待支付
        1 => 'paid',       // 已支付
        2 => 'refunded',   // 已退款
        3 => 'closed',     // 已关闭
    ];

    // DB字符串状态 → 前端数字状态 映射
    protected static array $statusReverseMap = [
        'pending'  => 0,
        'paid'     => 1,
        'refunded' => 2,
        'refunding'=> 2,
        'closed'   => 3,
    ];

    // 支付渠道映射
    protected static array $channelMap = [
        1 => 'wechat',
        2 => 'alipay',
    ];

    /**
     * 订单列表
     */
    public function lists(array $params = []): array
    {
        $page   = (int)($params['page'] ?? 1);
        $limit  = (int)($params['limit'] ?? 10);
        $order_no  = $params['order_no'] ?? '';
        $username  = $params['username'] ?? '';
        $status    = $params['status'] ?? '';
        $channel   = $params['channel'] ?? '';
        $start_time = $params['start_time'] ?? '';
        $end_time   = $params['end_time'] ?? '';

        $where = [];
        // 订单号搜索
        if ($order_no !== '') {
            $where[] = ['order_no', 'like', "%{$order_no}%"];
        }
        // 用户名搜索
        if ($username !== '') {
            $where[] = ['username', 'like', "%{$username}%"];
        }
        // 状态：前端传数字，转换为DB字符串
        if ($status !== '') {
            $statusInt = (int)$status;
            if (isset(self::$statusMap[$statusInt])) {
                $where[] = ['status', '=', self::$statusMap[$statusInt]];
            }
        }
        // 支付渠道
        if ($channel !== '') {
            $channelInt = (int)$channel;
            if (isset(self::$channelMap[$channelInt])) {
                $where[] = ['pay_channel', '=', $channelInt];
            }
        }
        // 时间范围
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

        // 转换状态为数字
        foreach ($list['data'] as &$item) {
            $item['status'] = $this->formatStatus($item['status']);
            $item['pay_channel'] = $item['pay_channel'] ?? 0;
            $item['pay_way'] = $item['pay_way'] ?? '';
            $item['username'] = $item['username'] ?? '';
            $item['user_id'] = $item['user_id'] ?? 0;
            $item['pay_amount'] = $item['pay_amount'] ?? '0.00';
            $item['subject'] = $item['subject'] ?? '';
        }

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
        $data = $model->toArray();
        $data['status'] = $this->formatStatus($data['status']);
        return $data;
    }

    /**
     * 退款订单
     */
    public function refund(int $id): bool
    {
        $model = PayOrderModel::find($id);
        if (!$model) {
            throw new \Exception('订单不存在');
        }
        if ($model->status !== 'paid') {
            throw new \Exception('只有已支付的订单才能退款');
        }
        $model->status = 'refunded';
        $model->update_time = date('Y-m-d H:i:s');
        return $model->save();
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
     * 格式化状态为数字
     */
    protected function formatStatus(string $status): int
    {
        return self::$statusReverseMap[$status] ?? 0;
    }
}

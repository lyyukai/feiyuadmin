<?php
/**
 * 支付流水控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\pay;

use app\adminapi\controller\BaseAdminController;
use app\common\model\pay\PayStatement as PayStatementModel;
use app\common\service\JsonService;
use think\facade\Db;

/**
 * 支付流水管理
 * Class PayStatementController
 * @package app\adminapi\controller\pay
 */
class PayStatementController extends BaseAdminController
{
    // DB字符串状态 → 前端数字状态
    protected static array $statusReverseMap = [
        '0'        => 0,
        '1'        => 1,
        '2'        => 2,
        '3'        => 3,
        '4'        => 4,
        'pending'  => 0,
        'processing'=> 1,
        'success'  => 2,
        'fail'     => 3,
        'refunded' => 4,
    ];

    /**
     * 流水列表
     */
    public function lists(): \think\Response
    {
        $page       = (int) $this->request->get('page', 1);
        $limit      = (int) $this->request->get('limit', 10);
        $trade_no   = $this->request->get('trade_no', '');
        $order_no   = $this->request->get('order_no', '');
        $channel    = $this->request->get('channel', '');
        $status     = $this->request->get('status', '');
        $start_time = $this->request->get('start_time', '');
        $end_time   = $this->request->get('end_time', '');

        $where = [];
        if ($trade_no !== '') {
            $where[] = ['trade_no', 'like', "%{$trade_no}%"];
        }
        if ($order_no !== '') {
            $where[] = ['order_no', 'like', "%{$order_no}%"];
        }
        if ($channel !== '') {
            $where[] = ['channel', '=', $channel];
        }
        if ($status !== '') {
            $statusInt = (int)$status;
            $statusMap = [0=>'0', 1=>'1', 2=>'2', 3=>'3', 4=>'4'];
            if (isset($statusMap[$statusInt])) {
                $where[] = ['status', '=', $statusMap[$statusInt]];
            }
        }
        if ($start_time !== '') {
            $where[] = ['create_time', '>=', $start_time];
        }
        if ($end_time !== '') {
            $where[] = ['create_time', '<=', $end_time . ' 23:59:59'];
        }

        $paginate = PayStatementModel::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        // 格式化状态为数字
        foreach ($paginate['data'] as &$item) {
            $item['status'] = self::$statusReverseMap[$item['status']] ?? 0;
            $item['channel'] = $item['channel'] ?? '0';
            $item['way'] = $item['way'] ?? '';
            $item['fee'] = $item['fee'] ?? '0.00';
            $item['net_amount'] = $item['net_amount'] ?? '0.00';
            $item['trade_no'] = $item['trade_no'] ?? '';
            $item['merchant_no'] = $item['merchant_no'] ?? '';
        }

        // 统计数据
        $todayStart = date('Y-m-d 00:00:00');
        $monthStart = date('Y-m-01 00:00:00');

        $todayIncome = (float)PayStatementModel::where('status', '2')
            ->where('trade_time', '>=', $todayStart)
            ->sum('net_amount');

        $monthIncome = (float)PayStatementModel::where('status', '2')
            ->where('trade_time', '>=', $monthStart)
            ->sum('net_amount');

        $stat = [
            'today_income'   => round($todayIncome, 2),
            'month_income'   => round($monthIncome, 2),
            'pending_settle'=> round($monthIncome, 2),
        ];

        return JsonService::list($paginate['data'], $paginate['total'], $stat);
    }

    /**
     * 流水详情
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

        $data = $model->toArray();
        $data['status'] = self::$statusReverseMap[$data['status']] ?? 0;

        return $this->success('获取成功', $data);
    }
}

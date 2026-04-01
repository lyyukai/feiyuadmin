<?php
/**
 * 退款记录模型
 */

declare(strict_types=1);

namespace app\common\model\pay;

use think\Model;

/**
 * 退款记录模型
 * Class PayRefund
 * @package app\common\model\pay
 */
class PayRefund extends Model
{
    protected $name = 'pay_refund';
    protected $table = 'fy_pay_refund';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'refund_fee' => 'float',
        'total_fee' => 'float',
    ];

    // 状态常量
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_REFUNDING = 'refunding';

    // 关联订单
    public function order()
    {
        return $this->belongsTo(PayOrder::class, 'order_id');
    }
}

<?php
/**
 * 支付订单模型
 */

declare(strict_types=1);

namespace app\common\model\pay;

use think\Model;

/**
 * 支付订单模型
 * Class PayOrder
 * @package app\common\model\pay
 */
class PayOrder extends Model
{
    protected $name = 'pay_order';
    protected $table = 'sys_pay_order';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'total_fee' => 'float',
        'paid_fee' => 'float',
    ];

    // 状态常量
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CLOSED = 'closed';
    const STATUS_REFUNDING = 'refunding';
}

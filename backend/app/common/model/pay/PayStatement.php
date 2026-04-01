<?php
/**
 * 分账记录模型
 */

declare(strict_types=1);

namespace app\common\model\pay;

use think\Model;

/**
 * 分账记录模型
 * Class PayStatement
 * @package app\common\model\pay
 */
class PayStatement extends Model
{
    protected $name = 'pay_statement';
    protected $table = 'fy_pay_statement';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'amount' => 'float',
    ];

    // 状态常量
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
}

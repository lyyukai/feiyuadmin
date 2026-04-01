<?php
/**
 * 支付配置模型
 */

declare(strict_types=1);

namespace app\common\model\pay;

use think\Model;

/**
 * 支付配置模型
 * Class PayConfig
 * @package app\common\model\pay
 */
class PayConfig extends Model
{
    protected $name = 'pay_config';
    protected $table = 'sys_pay_config';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'status' => 'integer',
    ];
}

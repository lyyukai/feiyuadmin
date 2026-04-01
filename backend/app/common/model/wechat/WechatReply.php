<?php
/**
 * 微信自动回复模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 自动回复模型
 * Class WechatReply
 * @package app\common\model\wechat
 */
class WechatReply extends Model
{
    protected $name = 'wechat_reply';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'status' => 'integer',
    ];
}

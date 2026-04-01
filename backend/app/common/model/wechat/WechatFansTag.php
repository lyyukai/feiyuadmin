<?php
/**
 * 微信粉丝标签模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 粉丝标签模型
 * Class WechatFansTag
 * @package app\common\model\wechat
 */
class WechatFansTag extends Model
{
    protected $name = 'wechat_fans_tag';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'tagid' => 'integer',
        'fans_count' => 'integer',
    ];
}

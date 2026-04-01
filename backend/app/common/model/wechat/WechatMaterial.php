<?php
/**
 * 微信素材模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 素材模型
 * Class WechatMaterial
 * @package app\common\model\wechat
 */
class WechatMaterial extends Model
{
    protected $name = 'wechat_material';

    protected $autoWriteTimestamp = false;
}

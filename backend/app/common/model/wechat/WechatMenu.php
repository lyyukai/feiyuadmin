<?php
/**
 * 微信自定义菜单模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 菜单模型
 * Class WechatMenu
 * @package app\common\model\wechat
 */
class WechatMenu extends Model
{
    protected $name = 'wechat_menu';

    protected $autoWriteTimestamp = false;

    protected $type = [
        'status' => 'integer',
        'account_id' => 'integer',
    ];

    // 获取菜单数据（JSON解析）
    public function getMenuDataAttr($value): array
    {
        return json_decode($value, true) ?? [];
    }
}

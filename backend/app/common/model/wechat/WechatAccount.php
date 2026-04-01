<?php
/**
 * 微信公众号账号模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 公众号账号模型
 * Class WechatAccount
 * @package app\common\model\wechat
 */
class WechatAccount extends Model
{
    protected $name = 'wechat_account';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'type' => 'integer',
        'status' => 'integer',
    ];

    // 获取器：隐藏敏感字段
    public function hidden(array $fields, bool $merge = false): think\Model
    {
        if (in_array('appsecret', $fields)) {
            $this->append(['appsecret']);
        }
        return parent::hidden($fields);
    }

    // 获取appsecret（脱敏）
    public function getAppsecretAttr($value): string
    {
        if (empty($value)) {
            return '';
        }
        return substr($value, 0, 4) . '****' . substr($value, -4);
    }
}

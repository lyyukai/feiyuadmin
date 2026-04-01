<?php
/**
 * 开放平台模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 开放平台模型
 * Class OpenPlatform
 * @package app\common\model\wechat
 */
class OpenPlatform extends Model
{
    protected $name = 'open_platform';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'status' => 'integer',
    ];

    // 获取器：脱敏appsecret
    public function getAppsecretAttr($value): string
    {
        if (empty($value)) {
            return '';
        }
        return substr($value, 0, 4) . '****' . substr($value, -4);
    }

    // 状态文本
    public function getStatusTextAttr(): string
    {
        return $this->status == 1 ? '启用' : '禁用';
    }

    // 判断token是否过期
    public function isTokenExpired(): bool
    {
        if (empty($this->token_expire_time)) {
            return true;
        }
        return strtotime($this->token_expire_time) < time();
    }
}

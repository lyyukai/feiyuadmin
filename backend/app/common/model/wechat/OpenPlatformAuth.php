<?php
/**
 * 开放平台授权模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 开放平台授权模型
 * Class OpenPlatformAuth
 * @package app\common\model\wechat
 */
class OpenPlatformAuth extends Model
{
    protected $name = 'open_platform_auth';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'platform_id' => 'integer',
        'authorizer_type' => 'integer',
        'status' => 'integer',
    ];

    // 授权类型文本
    public function getAuthorizerTypeTextAttr(): string
    {
        $typeMap = [1 => '公众号', 2 => '小程序'];
        return $typeMap[$this->authorizer_type] ?? '未知';
    }

    // 状态文本
    public function getStatusTextAttr(): string
    {
        return $this->status == 1 ? '已授权' : '已取消';
    }

    // 判断token是否过期
    public function isTokenExpired(): bool
    {
        if (empty($this->authorizer_expire_time)) {
            return true;
        }
        return strtotime($this->authorizer_expire_time) < time();
    }
}

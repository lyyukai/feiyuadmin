<?php
/**
 * 小程序模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 小程序模型
 * Class MiniProgram
 * @package app\common\model\wechat
 */
class MiniProgram extends Model
{
    protected $name = 'mini_program';

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
}

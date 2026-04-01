<?php
/**
 * 小程序版本模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 小程序版本模型
 * Class MiniProgramVersion
 * @package app\common\model\wechat
 */
class MiniProgramVersion extends Model
{
    protected $name = 'mini_program_version';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'mini_program_id' => 'integer',
        'audit_status' => 'integer',
        'status' => 'integer',
    ];

    // 审核状态文本
    public function getAuditStatusTextAttr(): string
    {
        $statusMap = [0 => '未提交', 1 => '审核中', 2 => '通过', 3 => '拒绝'];
        return $statusMap[$this->audit_status] ?? '未知';
    }

    // 发布状态文本
    public function getStatusTextAttr(): string
    {
        return $this->status == 1 ? '已发布' : '未发布';
    }
}

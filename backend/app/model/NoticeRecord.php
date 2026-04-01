<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 发送记录模型
 */
class NoticeRecord extends Model
{
    protected $name = 'notice_record';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 状态
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    /**
     * 状态文本
     */
    public function getStatusTextAttr($value, $data): string
    {
        $status = [
            self::STATUS_PENDING => '待发送',
            self::STATUS_SUCCESS => '成功',
            self::STATUS_FAILED => '失败',
        ];
        return $status[$data['status']] ?? '未知';
    }

    /**
     * 渠道类型文本
     */
    public function getChannelTypeTextAttr($value, $data): string
    {
        $types = [
            1 => '邮件',
            2 => '短信',
            3 => '企微机器人',
            4 => '站内信',
        ];
        return $types[$data['channel_type']] ?? '未知';
    }

    /**
     * 获取变量值数组
     */
    public function getVarsAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 设置变量值数组
     */
    public function setVarsAttr($value): string
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : ($value ?? '{}');
    }
}

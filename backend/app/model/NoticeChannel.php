<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 通知渠道模型
 */
class NoticeChannel extends Model
{
    protected $name = 'notice_channel';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 渠道类型
    const TYPE_EMAIL = 1;
    const TYPE_SMS = 2;
    const TYPE_WECHAT = 3;
    const TYPE_MESSAGE = 4;

    // 渠道编码
    const CODE_EMAIL = 'email';
    const CODE_SMS = 'sms';
    const CODE_WECHAT = 'wechat';
    const CODE_MESSAGE = 'message';

    // 状态
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * 类型文本
     */
    public function getTypeTextAttr($value, $data): string
    {
        $types = [
            self::TYPE_EMAIL => '邮件',
            self::TYPE_SMS => '短信',
            self::TYPE_WECHAT => '企微机器人',
            self::TYPE_MESSAGE => '站内信',
        ];
        return $types[$data['type']] ?? '未知';
    }

    /**
     * 状态文本
     */
    public function getStatusTextAttr($value, $data): string
    {
        return ($data['status'] ?? 1) == self::STATUS_ENABLED ? '启用' : '禁用';
    }

    /**
     * 获取配置数组
     */
    public function getConfigAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 设置配置数组
     */
    public function setConfigAttr($value): string
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : ($value ?? '{}');
    }

    /**
     * 按编码获取渠道
     */
    public static function getByCode(string $code): ?self
    {
        return self::where('code', $code)->where('status', self::STATUS_ENABLED)->find();
    }

    /**
     * 按类型获取已启用的渠道
     */
    public static function getByType(int $type): ?self
    {
        return self::where('type', $type)->where('status', self::STATUS_ENABLED)->find();
    }
}

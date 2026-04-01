<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 消息通知模型
 */
class Notice extends Model
{
    protected $name = 'notice';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 类型常量
     */
    const TYPE_SYSTEM = 1;   // 系统通知
    const TYPE_USER = 2;      // 用户消息
    const TYPE_TASK = 3;      // 任务通知

    /**
     * 状态常量
     */
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * 是否已读常量
     */
    const UNREAD = 0;
    const READ = 1;

    /**
     * 类型文本获取器
     */
    public function getTypeTextAttr($value, $data): string
    {
        $types = [
            self::TYPE_SYSTEM => '系统通知',
            self::TYPE_USER => '用户消息',
            self::TYPE_TASK => '任务通知',
        ];
        return $types[$data['type']] ?? '未知';
    }

    /**
     * 状态文本获取器
     */
    public function getStatusTextAttr($value, $data): string
    {
        return ($data['status'] ?? 1) == self::STATUS_ENABLED ? '启用' : '禁用';
    }

    /**
     * 是否已读文本获取器
     */
    public function getIsReadTextAttr($value, $data): string
    {
        return ($data['is_read'] ?? 0) == self::READ ? '已读' : '未读';
    }

    /**
     * 类型搜索器
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('type', $value);
        }
    }

    /**
     * 接收者ID搜索器
     */
    public function searchReceiverIdAttr($query, $value)
    {
        if ($value !== '') {
            $query->where(function ($q) use ($value) {
                $q->where('receiver_id', $value)->whereOr('receiver_id', 0);
            });
        }
    }

    /**
     * 是否已读搜索器
     */
    public function searchIsReadAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('is_read', $value);
        }
    }

    /**
     * 关键词搜索
     */
    public function searchKeywordAttr($query, $value)
    {
        if (!empty($value)) {
            $query->whereLike('title|content', "%{$value}%");
        }
    }
}

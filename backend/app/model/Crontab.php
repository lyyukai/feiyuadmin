<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 定时任务模型
 */
class Crontab extends Model
{
    protected $name = 'crontab';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 状态常量
    const STATUS_DISABLED = 0;  // 暂停
    const STATUS_ENABLED = 1;   // 运行中

    // 类型常量
    const TYPE_SHELL = 'shell';   // Shell脚本
    const TYPE_PHP = 'php';      // PHP类
    const TYPE_URL = 'url';      // URL回调

    /**
     * 类型文本获取器
     */
    public function getTypeTextAttr($value, $data): string
    {
        $types = [
            self::TYPE_SHELL => 'Shell脚本',
            self::TYPE_PHP => 'PHP类',
            self::TYPE_URL => 'URL回调',
        ];
        return $types[$data['task_type']] ?? '未知';
    }

    /**
     * 状态获取器
     */
    public function getStatusAttr($value): int
    {
        return (int)($value ?? 1);
    }

    /**
     * 类型获取器
     */
    public function getTypeAttr($value): string
    {
        return $value ?? self::TYPE_SHELL;
    }
}

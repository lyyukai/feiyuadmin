<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 定时任务日志模型
 */
class CronLog extends Model
{
    protected $name = 'cron_log';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 执行状态常量
    const EXEC_SUCCESS = 1;
    const EXEC_FAIL = 0;
}

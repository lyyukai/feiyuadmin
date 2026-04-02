<?php
/**
 * 飞鱼后台管理系统 - 定时任务逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\model\Crontab;
use app\model\CronLog;
use Cron\CronExpression;

/**
 * 定时任务逻辑
 * Class CrontabLogic
 * @package app\adminapi\logic\admin
 */
class CrontabLogic
{
    /**
     * 获取任务列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $pageSize = min((int) ($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;

        $where = [];
        if (isset($params['type']) && $params['type'] !== '') {
            $where[] = ['task_type', '=', $params['type']];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['exec_status', '=', (int) $params['status']];
        }
        if (!empty($params['keyword'])) {
            $where[] = ['task_name|task_command', 'like', "%{$params['keyword']}%"];
        }

        $query = Crontab::where($where)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $pageSize)->select()->toArray();

        // 计算下次执行时间
        foreach ($list as &$item) {
            if ($item['exec_status'] == Crontab::STATUS_ENABLED) {
                $item['next_time'] = self::calculateNextRunTime($item['cron_expression']);
            } else {
                $item['next_time'] = '-';
            }
        }

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * 获取任务详情
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $crontab = Crontab::find($id);
        if (empty($crontab)) {
            return [];
        }
        return $crontab->toArray();
    }

    /**
     * 添加任务
     * @param array $params
     * @return int
     */
    public static function add(array $params): int
    {
        $rule = $params['rule'] ?? $params['cron_expression'] ?? '';
        $nextRunTime = self::calculateNextRunTime($rule);

        $crontab = new Crontab();
        $crontab->task_name = $params['task_name'] ?? $params['name'] ?? '';
        $crontab->task_type = $params['task_type'] ?? $params['type'] ?? Crontab::TYPE_SHELL;
        $crontab->task_command = $params['task_command'] ?? $params['command'] ?? '';
        $crontab->cron_expression = $rule;
        $crontab->exec_status = (int) ($params['status'] ?? Crontab::STATUS_ENABLED);
        $crontab->retry_times = (int) ($params['retry_times'] ?? 0);
        $crontab->retry_interval = (int) ($params['retry_interval'] ?? 60);
        $crontab->remark = $params['remark'] ?? '';
        $crontab->next_run_time = $nextRunTime;
        $crontab->last_exec_time = null;
        $crontab->save();

        return $crontab->id;
    }

    /**
     * 编辑任务
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }

        $crontab = Crontab::find($id);
        if (empty($crontab)) {
            return false;
        }

        if (isset($params['task_name'])) {
            $crontab->task_name = $params['task_name'];
        }
        if (isset($params['name'])) {
            $crontab->task_name = $params['name'];
        }
        if (isset($params['task_type'])) {
            $crontab->task_type = $params['task_type'];
        }
        if (isset($params['type'])) {
            $crontab->task_type = $params['type'];
        }
        if (isset($params['task_command'])) {
            $crontab->task_command = $params['task_command'];
        }
        if (isset($params['command'])) {
            $crontab->task_command = $params['command'];
        }
        if (isset($params['rule']) || isset($params['cron_expression'])) {
            $rule = $params['rule'] ?? $params['cron_expression'];
            $crontab->cron_expression = $rule;
            $crontab->next_run_time = self::calculateNextRunTime($rule);
        }
        if (isset($params['retry_times'])) {
            $crontab->retry_times = (int) $params['retry_times'];
        }
        if (isset($params['retry_interval'])) {
            $crontab->retry_interval = (int) $params['retry_interval'];
        }
        if (isset($params['remark'])) {
            $crontab->remark = $params['remark'];
        }

        return $crontab->save();
    }

    /**
     * 删除任务
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $crontab = Crontab::find($id);
        if (empty($crontab)) {
            return false;
        }
        return $crontab->delete();
    }

    /**
     * 切换状态
     * @param int $id
     * @param int $status
     * @return bool
     */
    public static function toggleStatus(int $id, int $status): bool
    {
        $crontab = Crontab::find($id);
        if (empty($crontab)) {
            return false;
        }
        $crontab->exec_status = $status;
        if ($status == Crontab::STATUS_ENABLED) {
            $crontab->next_run_time = self::calculateNextRunTime($crontab->cron_expression);
        }
        return $crontab->save();
    }

    /**
     * 立即执行任务
     * @param int $id
     * @return array
     */
    public static function execute(int $id): array
    {
        $crontab = Crontab::find($id);
        if (empty($crontab)) {
            return ['success' => false, 'message' => '任务不存在'];
        }

        $startTime = microtime(true);
        $output = '';
        $success = true;

        try {
            $command = $crontab->task_command;
            if ($crontab->task_type === Crontab::TYPE_SHELL) {
                exec($command, $outputArr, $returnVar);
                $output = implode("\n", $outputArr);
                $success = ($returnVar === 0);
            } elseif ($crontab->task_type === Crontab::TYPE_PHP) {
                ob_start();
                include $command;
                $output = ob_get_clean();
                $success = true;
            } else {
                $output = '不支持的任务类型';
                $success = false;
            }
        } catch (\Exception $e) {
            $output = $e->getMessage();
            $success = false;
        }

        $duration = (microtime(true) - $startTime) * 1000;

        // 更新执行时间和状态
        $crontab->last_exec_time = date('Y-m-d H:i:s');
        $crontab->last_exec_result = $output;
        if (!$success && $crontab->retry_times > 0) {
            // 重试逻辑
            for ($i = 0; $i < $crontab->retry_times; $i++) {
                sleep($crontab->retry_interval);
                exec($crontab->task_command, $outputArr, $returnVar);
                if ($returnVar === 0) {
                    $success = true;
                    break;
                }
            }
        }
        $crontab->save();

        // 记录日志
        self::addLog($id, $crontab->task_name, $success ? 1 : 0, $output, $duration);

        return [
            'success' => $success,
            'output' => $output,
            'duration' => round($duration, 2),
        ];
    }

    /**
     * 获取执行日志
     * @param array $params
     * @return array
     */
    public static function getLogs(array $params): array
    {
        $taskId = (int) ($params['task_id'] ?? 0);
        $page = (int) ($params['page'] ?? 1);
        $pageSize = min((int) ($params['page_size'] ?? 20), 100);

        $where = [];
        if ($taskId > 0) {
            $where[] = ['task_id', '=', $taskId];
        }

        $query = CronLog::where($where)->order('id', 'desc');
        $total = $query->count();
        $list = $query->limit(($page - 1) * $pageSize, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * 计算下次执行时间
     * @param string $expression
     * @return string
     */
    public static function calculateNextRunTime(string $expression): string
    {
        try {
            $cron = new CronExpression($expression);
            return $cron->getNextRunDate()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * 添加执行日志
     * @param int $taskId
     * @param string $taskName
     * @param int $status
     * @param string $output
     * @param float $duration
     * @return int
     */
    protected static function addLog(int $taskId, string $taskName, int $status, string $output, float $duration): int
    {
        $log = new CronLog();
        $log->task_id = $taskId;
        $log->status = $status;
        $log->output = mb_substr($output, 0, 1000);
        $log->duration = $duration;
        $log->execute_time = date('Y-m-d H:i:s');
        $log->save();

        return (int) $log->id;
    }
}

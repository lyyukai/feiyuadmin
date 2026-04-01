<?php
/**
 * 定时任务调度服务
 * 用于管理定时任务的执行
 */

namespace app\service;

use app\model\Crontab;
use app\model\CronLog;
use app\adminapi\logic\admin\CrontabLogic;
use Cron\CronExpression;

class CronService
{
    /**
     * 单例实例
     */
    protected static $instance = null;

    /**
     * 获取实例
     */
    public static function getInstance(): CronService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 检查所有任务，找到需要执行的任务并执行
     */
    public function checkAndExecute(): void
    {
        $tasks = Crontab::where('status', Crontab::STATUS_ENABLED)
            ->whereNotNull('next_run_time')
            ->select();

        $now = date('Y-m-d H:i:00');

        foreach ($tasks as $task) {
            try {
                // 检查是否到达执行时间
                if ($task->next_run_time && $task->next_run_time <= $now) {
                    $this->executeTask($task);
                }
            } catch (\Throwable $e) {
                // 记录错误但不中断其他任务
                $this->logError($task->id, $e->getMessage());
            }
        }
    }

    /**
     * 执行单个任务
     */
    public function executeTask(Crontab $task): array
    {
        $startTime = microtime(true);
        $output = '';
        $success = false;
        $retryCount = 0;
        $maxRetries = $task->retry_times ?? 0;

        do {
            try {
                $retryCount++;
                $output = $this->runTask($task);
                $success = true;
                break;
            } catch (\Throwable $e) {
                $output = '执行失败: ' . $e->getMessage();
                if ($retryCount <= $maxRetries) {
                    $output .= " (重试 {$retryCount}/{$maxRetries})";
                    usleep(100000); // 100ms
                }
            }
        } while ($retryCount <= $maxRetries);

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 4);

        // 记录执行日志
        $this->addLog($task->id, $success ? CronLog::EXEC_SUCCESS : CronLog::EXEC_FAIL, $output, $duration);

        // 更新任务执行时间
        $task->last_run_time = date('Y-m-d H:i:s');
        $task->next_run_time = $this->calculateNextRunTime($task->rule);
        $task->save();

        return [
            'success' => $success,
            'output' => $output,
            'duration' => $duration,
            'retry_count' => $retryCount,
        ];
    }

    /**
     * 运行任务
     */
    protected function runTask(Crontab $task): string
    {
        switch ($task->type) {
            case Crontab::TYPE_SHELL:
                return $this->execShell($task->command);

            case Crontab::TYPE_PHP:
                return $this->execPhpClass($task->command);

            case Crontab::TYPE_URL:
                return $this->fetchUrl($task->command);

            default:
                throw new \Exception('不支持的任务类型');
        }
    }

    /**
     * 执行Shell命令
     */
    protected function execShell(string $command): string
    {
        $output = [];
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);
        $result = implode("\n", $output);
        if ($returnVar !== 0) {
            throw new \Exception("命令执行失败 (返回码: {$returnVar})\n{$result}");
        }
        return $result;
    }

    /**
     * 执行PHP类
     */
    protected function execPhpClass(string $classPath): string
    {
        // 自动添加命名空间前缀
        if (strpos($classPath, '\\') === false) {
            $classPath = 'app\\cron\\' . $classPath;
        }

        if (!class_exists($classPath)) {
            throw new \Exception("类不存在: {$classPath}");
        }

        $instance = new $classPath();
        if (!method_exists($instance, 'run')) {
            throw new \Exception("类必须包含 run() 方法: {$classPath}");
        }

        ob_start();
        try {
            $result = $instance->run();
            $output = ob_get_clean();
            return $output ?: (is_string($result) ? $result : json_encode($result));
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * 请求URL
     */
    protected function fetchUrl(string $url): string
    {
        if (empty($url)) {
            throw new \Exception('URL不能为空');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('请求失败: ' . $error);
        }
        if ($httpCode != 200) {
            throw new \Exception("HTTP错误: {$httpCode}");
        }

        return 'HTTP ' . $httpCode . ': ' . substr($response, 0, 500);
    }

    /**
     * 添加执行日志
     */
    protected function addLog(int $taskId, int $status, string $output, float $duration): int
    {
        $log = new CronLog();
        $log->task_id = $taskId;
        $log->status = $status;
        $log->output = mb_substr($output, 0, 65535);
        $log->duration = $duration;
        $log->execute_time = date('Y-m-d H:i:s');
        $log->save();

        return $log->id;
    }

    /**
     * 计算下次执行时间
     */
    public function calculateNextRunTime(string $rule): ?string
    {
        if (empty($rule)) {
            return null;
        }

        try {
            $cron = new CronExpression($rule);
            return $cron->getNextRunDate()->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * 记录错误
     */
    protected function logError(int $taskId, string $message): void
    {
        error_log("[CronService] Task {$taskId} Error: {$message}");
    }
}

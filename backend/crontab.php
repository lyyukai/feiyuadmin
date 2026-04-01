#!/usr/bin/env php
<?php
/**
 * 定时任务调度命令
 * 
 * 使用方式:
 *   php crontab.php                    # 检查并执行到期的任务
 *   php crontab.php execute 1          # 执行指定ID的任务
 *   php crontab.php check              # 仅检查任务状态
 * 
 * 建议在系统crontab中添加:
 *   * * * * * cd /www/wwwroot/feiyuadmin/backend && php crontab.php >> runtime/logs/crontab.log 2>&1
 */

namespace think;

require __DIR__ . '/vendor/autoload.php';

// 应用初始化
$app = new App();
$app->initialize();

$command = $argv[1] ?? 'run';
$taskId = isset($argv[2]) ? (int) $argv[2] : 0;

switch ($command) {
    case 'execute':
        if ($taskId <= 0) {
            echo "Error: Task ID is required\n";
            exit(1);
        }
        executeTask($taskId);
        break;
        
    case 'check':
        checkTasks();
        break;
        
    case 'run':
    default:
        runScheduler();
        break;
}

/**
 * 运行调度器 - 检查并执行到期的任务
 */
function runScheduler(): void
{
    $startTime = microtime(true);
    
    echo "[" . date('Y-m-d H:i:s') . "] Cron scheduler started\n";
    
    try {
        $service = \app\service\CronService::getInstance();
        $service->checkAndExecute();
        echo "[" . date('Y-m-d H:i:s') . "] Cron scheduler finished\n";
    } catch (\Throwable $e) {
        echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    $duration = round(microtime(true) - $startTime, 4);
    echo "Duration: {$duration}s\n";
}

/**
 * 执行指定任务
 */
function executeTask(int $taskId): void
{
    echo "[" . date('Y-m-d H:i:s') . "] Executing task #{$taskId}\n";
    
    try {
        $task = \app\model\Crontab::find($taskId);
        if (!$task) {
            echo "Error: Task not found\n";
            exit(1);
        }
        
        $service = \app\service\CronService::getInstance();
        $result = $service->executeTask($task);
        
        echo "Status: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "Output: " . ($result['output'] ?: '(no output)') . "\n";
        echo "Duration: " . $result['duration'] . "s\n";
        
        if (!$result['success']) {
            exit(1);
        }
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

/**
 * 检查任务状态
 */
function checkTasks(): void
{
    echo "[" . date('Y-m-d H:i:s') . "] Checking tasks...\n";
    
    try {
        $tasks = \app\model\Crontab::where('status', 1)->select();
        
        foreach ($tasks as $task) {
            $nextTime = $task->next_run_time ?: 'N/A';
            echo sprintf(
                "[%d] %s - %s - Next: %s\n",
                $task->id,
                str_pad($task->name, 20),
                $task->rule,
                $nextTime
            );
        }
        
        echo "Total: " . count($tasks) . " active tasks\n";
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

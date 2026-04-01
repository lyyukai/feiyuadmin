<?php
/**
 * 定时任务示例 - 系统信息采集
 */

namespace app\cron;

class ExampleTask
{
    /**
     * 执行入口
     */
    public function run(): string
    {
        $time = date('Y-m-d H:i:s');
        $info = [
            'time' => $time,
            'memory' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'peak_memory' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB',
        ];
        
        echo "[{$time}] 系统信息采集任务执行\n";
        echo "当前内存使用: {$info['memory']}\n";
        echo "内存峰值: {$info['peak_memory']}\n";
        
        return json_encode($info);
    }
}

-- ============================================
-- 定时任务管理表 SQL
-- 表前缀: sys_
-- 创建时间: 2026-04-01
-- ============================================

-- 任务表
DROP TABLE IF EXISTS `sys_cron_task`;
CREATE TABLE `sys_cron_task` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '任务名称',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '任务类型: 1=Shell脚本, 2=PHP类, 3=URL回调',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT 'Crontab表达式',
  `command` varchar(500) NOT NULL DEFAULT '' COMMENT '执行命令/脚本路径/类路径/URL',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态: 0=暂停, 1=运行中',
  `retry_times` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败重试次数(0-5)',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `last_run_time` datetime DEFAULT NULL COMMENT '上次执行时间',
  `next_run_time` datetime DEFAULT NULL COMMENT '下次执行时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_rule` (`rule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='定时任务表';

-- 执行日志表
DROP TABLE IF EXISTS `sys_cron_log`;
CREATE TABLE `sys_cron_log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `task_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '任务ID',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行状态: 0=失败, 1=成功',
  `output` text COMMENT '执行输出',
  `duration` decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '执行耗时(秒)',
  `execute_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '执行时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_status` (`status`),
  KEY `idx_execute_time` (`execute_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='定时任务执行日志表';

-- 示例数据
INSERT INTO `sys_cron_task` (`name`, `type`, `rule`, `command`, `status`, `retry_times`, `remark`) VALUES
('系统信息采集', 1, '*/5 * * * *', 'echo "System check at $(date)" >> /tmp/cron_test.log', 1, 2, '每5分钟执行一次系统检查'),
('数据清理任务', 1, '0 2 * * *', 'rm -f /tmp/*.tmp', 0, 0, '每天凌晨2点清理临时文件');

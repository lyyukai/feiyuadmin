-- ============================================
-- 飞羽后台管理系统 V2 增量迁移脚本
-- 执行时间: 2026-04-01
-- 说明: 创建V2新增的数据表
-- ============================================

-- 1. 定时任务表 (sys_cron_task)
-- 注意: sys_crontab 已存在但结构不完整，使用 cron_task 作为新表名
DROP TABLE IF EXISTS `sys_cron_task`;
CREATE TABLE `sys_cron_task` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '任务名称',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '任务类型: 1=Shell脚本, 2=PHP类, 3=URL回调',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT 'Crontab表达式',
  `command` varchar(500) NOT NULL DEFAULT '' COMMENT '执行命令/脚本路径/类路径/URL',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态: 0=暂停, 1=运行中',
  `retry_times` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败重试次数(0-5)',
  `exec_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '上次执行状态: 0=失败, 1=成功',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `last_run_time` datetime DEFAULT NULL COMMENT '上次执行时间',
  `next_run_time` datetime DEFAULT NULL COMMENT '下次执行时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_rule` (`rule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='定时任务表';

-- 2. 定时任务执行日志表 (sys_cron_log)
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

-- 3. 表单设计表 (sys_form_design)
DROP TABLE IF EXISTS `sys_form_design`;
CREATE TABLE `sys_form_design` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表单ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '表单名称',
  `table_name` varchar(100) NOT NULL DEFAULT '' COMMENT '关联数据表名',
  `config` json DEFAULT NULL COMMENT '表单配置JSON(字段、布局、校验规则)',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=启用',
  `create_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人ID',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_uid` (`create_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='表单设计表';

-- 4. 表单数据表 (sys_form_data)
DROP TABLE IF EXISTS `sys_form_data`;
CREATE TABLE `sys_form_data` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '数据ID',
  `form_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '表单ID',
  `data` json DEFAULT NULL COMMENT '表单数据JSON',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提交人ID',
  `user_name` varchar(100) NOT NULL DEFAULT '' COMMENT '提交人名称',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态: 0=禁用, 1=启用',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_form_id` (`form_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='表单数据表';

-- ============================================
-- 执行完成
-- ============================================

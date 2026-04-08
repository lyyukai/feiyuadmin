-- =============================================
-- 飞鱼后台 工作流相关表建表SQL
-- 执行时间: 2026-04-04
-- 负责人: 李彦宏
-- 前缀: fy_（如需修改请替换所有 fy_）
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 1. workflow 工作流定义表
-- ----------------------------
DROP TABLE IF EXISTS `fy_workflow`;
CREATE TABLE `fy_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '工作流名称',
  `code` varchar(64) NOT NULL COMMENT '工作流编码',
  `description` text COMMENT '描述',
  `flow_data` text COMMENT '流程设计数据JSON',
  `form_fields` text COMMENT '表单字段配置JSON',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT '发布状态：0未发布 1已发布',
  `version` int(11) NOT NULL DEFAULT 1 COMMENT '版本号',
  `create_user` int(11) NOT NULL DEFAULT 0 COMMENT '创建人ID',
  `update_user` int(11) NOT NULL DEFAULT 0 COMMENT '更新人ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='工作流定义表';

-- ----------------------------
-- 2. workflow_node 流程节点表
-- ----------------------------
DROP TABLE IF EXISTS `fy_workflow_node`;
CREATE TABLE `fy_workflow_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL COMMENT '所属工作流ID',
  `node_type` varchar(20) NOT NULL COMMENT '节点类型：start/approver/condition/end',
  `node_name` varchar(100) NOT NULL COMMENT '节点名称',
  `node_key` varchar(64) NOT NULL COMMENT '节点唯一标识',
  `position_x` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'X坐标',
  `position_y` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Y坐标',
  `config` text COMMENT '节点配置JSON',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `idx_node_key` (`node_key`),
  CONSTRAINT `fk_node_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `fy_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='工作流节点表';

-- ----------------------------
-- 3. workflow_edge 流程连线表
-- ----------------------------
DROP TABLE IF EXISTS `fy_workflow_edge`;
CREATE TABLE `fy_workflow_edge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL COMMENT '所属工作流ID',
  `edge_type` varchar(20) NOT NULL DEFAULT 'default' COMMENT '连线类型：default/condition',
  `source_key` varchar(64) NOT NULL COMMENT '源节点key',
  `target_key` varchar(64) NOT NULL COMMENT '目标节点key',
  `label` varchar(200) DEFAULT NULL COMMENT '连线标签',
  `condition_config` text COMMENT '条件配置JSON',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `idx_source_key` (`source_key`),
  CONSTRAINT `fk_edge_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `fy_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='工作流连线表';

-- ----------------------------
-- 4. workflow_instance 流程实例表
-- ----------------------------
DROP TABLE IF EXISTS `fy_workflow_instance`;
CREATE TABLE `fy_workflow_instance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL COMMENT '工作流ID',
  `workflow_name` varchar(100) NOT NULL COMMENT '工作流名称',
  `instance_no` varchar(32) NOT NULL COMMENT '实例编号',
  `title` varchar(200) NOT NULL COMMENT '实例标题',
  `apply_user` int(11) NOT NULL COMMENT '申请人ID',
  `apply_user_name` varchar(50) DEFAULT NULL COMMENT '申请人姓名',
  `form_data` text COMMENT '表单数据JSON',
  `current_node_key` varchar(64) DEFAULT NULL COMMENT '当前节点key',
  `current_node_name` varchar(100) DEFAULT NULL COMMENT '当前节点名称',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0进行中 1已完成 2已驳回 3已撤回',
  `is_ended` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否结束：0否 1是',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_instance_no` (`instance_no`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `idx_apply_user` (`apply_user`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_instance_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `fy_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='工作流实例表';

-- ----------------------------
-- 5. workflow_task 流程任务表
-- ----------------------------
DROP TABLE IF EXISTS `fy_workflow_task`;
CREATE TABLE `fy_workflow_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL COMMENT '实例ID',
  `workflow_id` int(11) NOT NULL COMMENT '工作流ID',
  `node_key` varchar(64) NOT NULL COMMENT '节点key',
  `node_name` varchar(100) NOT NULL COMMENT '节点名称',
  `task_type` varchar(20) NOT NULL COMMENT '任务类型：start/approve/counter_sign',
  `assignee` int(11) NOT NULL COMMENT '审批人ID',
  `assignee_name` varchar(50) DEFAULT NULL COMMENT '审批人姓名',
  `action_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审批状态：0待处理 1已通过 2已驳回 3已转交 4已催办',
  `action_remark` varchar(500) DEFAULT NULL COMMENT '审批备注',
  `action_time` datetime DEFAULT NULL COMMENT '审批时间',
  `is_current` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否为当前任务：0否 1是',
  `transfer_from` int(11) DEFAULT NULL COMMENT '转交人ID',
  `transfer_from_name` varchar(50) DEFAULT NULL COMMENT '转交人姓名',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_instance_id` (`instance_id`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `idx_assignee` (`assignee`),
  KEY `idx_action_status` (`action_status`),
  KEY `idx_is_current` (`is_current`),
  CONSTRAINT `fk_task_instance` FOREIGN KEY (`instance_id`) REFERENCES `fy_workflow_instance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_task_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `fy_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='工作流任务表';

SET FOREIGN_KEY_CHECKS = 1;

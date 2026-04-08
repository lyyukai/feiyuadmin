-- =============================================
-- 工作流表 Schema 修正 SQL（ALTER）
-- 用于修正现有表的字段，使其与 WorkflowLogic.php 代码一致
-- 执行前请备份！
-- 执行时间: 2026-04-04
-- 负责人: 李彦宏
-- =============================================

SET NAMES utf8mb4;

-- ----------------------------
-- fy_workflow 表修正
-- ----------------------------
-- 新增代码期望的字段（原 fy_workflow 只有 workflow_name, workflow_code）
ALTER TABLE `fy_workflow`
  ADD COLUMN `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '工作流名称（代码期望字段）' AFTER `workflow_name`,
  ADD COLUMN `code` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '工作流编码（代码期望字段）' AFTER `workflow_code`,
  ADD COLUMN `flow_data` TEXT COMMENT '流程设计数据JSON' AFTER `description`,
  ADD COLUMN `form_fields` TEXT COMMENT '表单字段配置JSON' AFTER `flow_data`,
  ADD COLUMN `is_published` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '发布状态：0未发布 1已发布' AFTER `status`,
  ADD COLUMN `version` INT(11) NOT NULL DEFAULT 1 COMMENT '版本号' AFTER `is_published`,
  ADD COLUMN `create_user` INT(11) NOT NULL DEFAULT 0 COMMENT '创建人ID' AFTER `version`,
  ADD COLUMN `update_user` INT(11) NOT NULL DEFAULT 0 COMMENT '更新人ID' AFTER `create_user`;

-- 将 workflow_name/code 的数据同步到 name/code（处理已有测试数据）
UPDATE `fy_workflow` SET `name` = `workflow_name`, `code` = `workflow_code` WHERE `name` = '' OR `name` IS NULL;

-- ----------------------------
-- fy_workflow_node 表修正
-- ----------------------------
ALTER TABLE `fy_workflow_node`
  ADD COLUMN `node_key` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '节点唯一标识' AFTER `node_name`,
  ADD COLUMN `config` TEXT COMMENT '节点配置JSON' AFTER `node_key`;

-- ----------------------------
-- fy_workflow_instance 表修正
-- ----------------------------
ALTER TABLE `fy_workflow_instance`
  ADD COLUMN `workflow_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '工作流名称（代码期望）' AFTER `workflow_id`,
  ADD COLUMN `instance_no` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '实例编号（代码期望）' AFTER `workflow_name`,
  ADD COLUMN `title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '实例标题（代码期望）' AFTER `instance_no`,
  ADD COLUMN `apply_user` INT(11) NOT NULL DEFAULT 0 COMMENT '申请人ID（代码期望）' AFTER `title`,
  ADD COLUMN `apply_user_name` VARCHAR(50) DEFAULT NULL COMMENT '申请人姓名（代码期望）' AFTER `apply_user`,
  ADD COLUMN `form_data` TEXT COMMENT '表单数据JSON（代码期望）' AFTER `current_node_id`,
  ADD COLUMN `current_node_key` VARCHAR(64) DEFAULT NULL COMMENT '当前节点key（代码期望）' AFTER `form_data`,
  ADD COLUMN `current_node_name` VARCHAR(100) DEFAULT NULL COMMENT '当前节点名称（代码期望）' AFTER `current_node_key`,
  ADD COLUMN `is_ended` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否结束：0否 1是（代码期望）' AFTER `status`,
  ADD COLUMN `end_time` DATETIME DEFAULT NULL COMMENT '结束时间（代码期望）' AFTER `is_ended`;

-- ----------------------------
-- fy_workflow_task 表修正
-- ----------------------------
ALTER TABLE `fy_workflow_task`
  ADD COLUMN `workflow_id` INT(11) NOT NULL DEFAULT 0 COMMENT '工作流ID（代码期望）' AFTER `instance_id`,
  ADD COLUMN `node_key` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '节点key（代码期望）' AFTER `workflow_id`,
  ADD COLUMN `node_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '节点名称（代码期望）' AFTER `node_key`,
  ADD COLUMN `assignee` INT(11) NOT NULL DEFAULT 0 COMMENT '审批人ID（代码期望）' AFTER `task_type`,
  ADD COLUMN `assignee_name` VARCHAR(50) DEFAULT NULL COMMENT '审批人姓名（代码期望）' AFTER `assignee`,
  ADD COLUMN `action_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '审批状态（代码期望）' AFTER `assignee_name`,
  ADD COLUMN `action_remark` VARCHAR(500) DEFAULT NULL COMMENT '审批备注（代码期望）' AFTER `action_status`,
  ADD COLUMN `action_time` DATETIME DEFAULT NULL COMMENT '审批时间（代码期望）' AFTER `action_remark`,
  ADD COLUMN `is_current` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否为当前任务（代码期望）' AFTER `action_time`,
  ADD COLUMN `transfer_from` INT(11) DEFAULT NULL COMMENT '转交人ID（代码期望）' AFTER `is_current`,
  ADD COLUMN `transfer_from_name` VARCHAR(50) DEFAULT NULL COMMENT '转交人姓名（代码期望）' AFTER `transfer_from`;

-- ----------------------------
-- fy_workflow_edge 表修正
-- ----------------------------
ALTER TABLE `fy_workflow_edge`
  ADD COLUMN `edge_type` VARCHAR(20) NOT NULL DEFAULT 'default' COMMENT '连线类型（代码期望）' AFTER `workflow_id`,
  ADD COLUMN `source_key` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '源节点key（代码期望）' AFTER `edge_type`,
  ADD COLUMN `target_key` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '目标节点key（代码期望）' AFTER `source_key`,
  ADD COLUMN `label` VARCHAR(200) DEFAULT NULL COMMENT '连线标签（代码期望）' AFTER `target_key`,
  ADD COLUMN `condition_config` TEXT COMMENT '条件配置JSON（代码期望）' AFTER `label`;

-- =============================================
-- 完整建表 SQL（新建库用，包含完整正确 schema）
-- =============================================
-- 见同目录下 workflow_tables.sql

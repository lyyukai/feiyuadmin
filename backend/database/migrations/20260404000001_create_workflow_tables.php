<?php
/**
 * 工作流相关表迁移脚本
 * 迁移时间: 2026-04-04
 * 负责人: 李彦宏
 */

use think\db\exception\InvalidArgumentException;
use think\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateWorkflowTables extends Migrator
{
    /**
     * 建表 SQL（用于直接执行）
     * 前缀占位符: {PREFIX} 实际执行时替换为配置的前缀
     */

    public function up(): void
    {
        $prefix = config('database.prefix', 'fy_');

        // =============================================
        // 1. workflow 工作流定义表
        // =============================================
        if (!table_exists($prefix . 'workflow')) {
            $this->table($prefix . 'workflow', [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '工作流定义表',
            ])
            ->addColumn('name', 'string', ['limit' => 100, 'null' => false, 'comment' => '工作流名称'])
            ->addColumn('code', 'string', ['limit' => 64, 'null' => false, 'comment' => '工作流编码'])
            ->addColumn('description', 'text', ['null' => true, 'comment' => '描述'])
            ->addColumn('flow_data', 'text', ['null' => true, 'comment' => '流程设计数据JSON'])
            ->addColumn('form_fields', 'text', ['null' => true, 'comment' => '表单字段配置JSON'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('is_published', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '发布状态：0未发布 1已发布'])
            ->addColumn('version', 'integer', ['limit' => 11, 'default' => 1, 'comment' => '版本号'])
            ->addColumn('create_user', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '创建人ID'])
            ->addColumn('update_user', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '更新人ID'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('code', ['name' => 'idx_code'])
            ->addIndex('status', ['name' => 'idx_status'])
            ->create();
        }

        // =============================================
        // 2. workflow_node 流程节点表
        // =============================================
        if (!table_exists($prefix . 'workflow_node')) {
            $this->table($prefix . 'workflow_node', [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '工作流节点表',
            ])
            ->addColumn('workflow_id', 'integer', ['limit' => 11, 'null' => false, 'comment' => '所属工作流ID'])
            ->addColumn('node_type', 'string', ['limit' => 20, 'null' => false, 'comment' => '节点类型：start/approver/condition/end'])
            ->addColumn('node_name', 'string', ['limit' => 100, 'null' => false, 'comment' => '节点名称'])
            ->addColumn('node_key', 'string', ['limit' => 64, 'null' => false, 'comment' => '节点唯一标识'])
            ->addColumn('position_x', 'decimal', ['limit' => '10,2', 'default' => 0, 'comment' => 'X坐标'])
            ->addColumn('position_y', 'decimal', ['limit' => '10,2', 'default' => 0, 'comment' => 'Y坐标'])
            ->addColumn('config', 'text', ['null' => true, 'comment' => '节点配置JSON'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('workflow_id', ['name' => 'idx_workflow_id'])
            ->addIndex('node_key', ['name' => 'idx_node_key'])
            ->addForeignKey('workflow_id', $prefix . 'workflow', 'id', [
                'constraint' => 'fk_node_workflow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // =============================================
        // 3. workflow_edge 流程连线表
        // =============================================
        if (!table_exists($prefix . 'workflow_edge')) {
            $this->table($prefix . 'workflow_edge', [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '工作流连线表',
            ])
            ->addColumn('workflow_id', 'integer', ['limit' => 11, 'null' => false, 'comment' => '所属工作流ID'])
            ->addColumn('edge_type', 'string', ['limit' => 20, 'default' => 'default', 'comment' => '连线类型：default/condition'])
            ->addColumn('source_key', 'string', ['limit' => 64, 'null' => false, 'comment' => '源节点key'])
            ->addColumn('target_key', 'string', ['limit' => 64, 'null' => false, 'comment' => '目标节点key'])
            ->addColumn('label', 'string', ['limit' => 200, 'null' => true, 'comment' => '连线标签'])
            ->addColumn('condition_config', 'text', ['null' => true, 'comment' => '条件配置JSON'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('workflow_id', ['name' => 'idx_workflow_id'])
            ->addIndex('source_key', ['name' => 'idx_source_key'])
            ->addForeignKey('workflow_id', $prefix . 'workflow', 'id', [
                'constraint' => 'fk_edge_workflow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // =============================================
        // 4. workflow_instance 流程实例表
        // =============================================
        if (!table_exists($prefix . 'workflow_instance')) {
            $this->table($prefix . 'workflow_instance', [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '工作流实例表',
            ])
            ->addColumn('workflow_id', 'integer', ['limit' => 11, 'null' => false, 'comment' => '工作流ID'])
            ->addColumn('workflow_name', 'string', ['limit' => 100, 'null' => false, 'comment' => '工作流名称'])
            ->addColumn('instance_no', 'string', ['limit' => 32, 'null' => false, 'comment' => '实例编号'])
            ->addColumn('title', 'string', ['limit' => 200, 'null' => false, 'comment' => '实例标题'])
            ->addColumn('apply_user', 'integer', ['limit' => 11, 'null' => false, 'comment' => '申请人ID'])
            ->addColumn('apply_user_name', 'string', ['limit' => 50, 'null' => true, 'comment' => '申请人姓名'])
            ->addColumn('form_data', 'text', ['null' => true, 'comment' => '表单数据JSON'])
            ->addColumn('current_node_key', 'string', ['limit' => 64, 'null' => true, 'comment' => '当前节点key'])
            ->addColumn('current_node_name', 'string', ['limit' => 100, 'null' => true, 'comment' => '当前节点名称'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '状态：0进行中 1已完成 2已驳回 3已撤回'])
            ->addColumn('is_ended', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否结束：0否 1是'])
            ->addColumn('end_time', 'datetime', ['null' => true, 'comment' => '结束时间'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('workflow_id', ['name' => 'idx_workflow_id'])
            ->addIndex('apply_user', ['name' => 'idx_apply_user'])
            ->addIndex('instance_no', ['name' => 'idx_instance_no', 'unique' => true])
            ->addIndex('status', ['name' => 'idx_status'])
            ->addForeignKey('workflow_id', $prefix . 'workflow', 'id', [
                'constraint' => 'fk_instance_workflow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // =============================================
        // 5. workflow_task 流程任务表
        // =============================================
        if (!table_exists($prefix . 'workflow_task')) {
            $this->table($prefix . 'workflow_task', [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '工作流任务表',
            ])
            ->addColumn('instance_id', 'integer', ['limit' => 11, 'null' => false, 'comment' => '实例ID'])
            ->addColumn('workflow_id', 'integer', ['limit' => 11, 'null' => false, 'comment' => '工作流ID'])
            ->addColumn('node_key', 'string', ['limit' => 64, 'null' => false, 'comment' => '节点key'])
            ->addColumn('node_name', 'string', ['limit' => 100, 'null' => false, 'comment' => '节点名称'])
            ->addColumn('task_type', 'string', ['limit' => 20, 'null' => false, 'comment' => '任务类型：start/approve/counter_sign'])
            ->addColumn('assignee', 'integer', ['limit' => 11, 'null' => false, 'comment' => '审批人ID'])
            ->addColumn('assignee_name', 'string', ['limit' => 50, 'null' => true, 'comment' => '审批人姓名'])
            ->addColumn('action_status', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '审批状态：0待处理 1已通过 2已驳回 3已转交 4已催办'])
            ->addColumn('action_remark', 'string', ['limit' => 500, 'null' => true, 'comment' => '审批备注'])
            ->addColumn('action_time', 'datetime', ['null' => true, 'comment' => '审批时间'])
            ->addColumn('is_current', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '是否为当前任务：0否 1是'])
            ->addColumn('transfer_from', 'integer', ['limit' => 11, 'null' => true, 'comment' => '转交人ID'])
            ->addColumn('transfer_from_name', 'string', ['limit' => 50, 'null' => true, 'comment' => '转交人姓名'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('instance_id', ['name' => 'idx_instance_id'])
            ->addIndex('workflow_id', ['name' => 'idx_workflow_id'])
            ->addIndex('assignee', ['name' => 'idx_assignee'])
            ->addIndex('action_status', ['name' => 'idx_action_status'])
            ->addIndex('is_current', ['name' => 'idx_is_current'])
            ->addForeignKey('instance_id', $prefix . 'workflow_instance', 'id', [
                'constraint' => 'fk_task_instance',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('workflow_id', $prefix . 'workflow', 'id', [
                'constraint' => 'fk_task_workflow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }
    }

    public function down(): void
    {
        $prefix = config('database.prefix', 'fy_');

        // 删除顺序：先删子表，再删主表（外键约束）
        if (table_exists($prefix . 'workflow_task')) {
            $this->dropTable($prefix . 'workflow_task');
        }
        if (table_exists($prefix . 'workflow_instance')) {
            $this->dropTable($prefix . 'workflow_instance');
        }
        if (table_exists($prefix . 'workflow_edge')) {
            $this->dropTable($prefix . 'workflow_edge');
        }
        if (table_exists($prefix . 'workflow_node')) {
            $this->dropTable($prefix . 'workflow_node');
        }
        if (table_exists($prefix . 'workflow')) {
            $this->dropTable($prefix . 'workflow');
        }
    }
}

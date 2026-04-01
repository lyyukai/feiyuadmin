<?php
/**
 * 工作流执行器 - 负责节点流转逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\workflow;

use app\model\Workflow;
use app\model\WorkflowInstance;
use app\model\WorkflowTask;
use app\model\WorkflowNode;
use app\model\WorkflowEdge;

/**
 * 工作流执行器
 * Class WorkflowExecutor
 * @package app\adminapi\logic\workflow
 */
class WorkflowExecutor
{
    /**
     * 执行工作流
     * @param WorkflowInstance $instance
     * @param Workflow $workflow
     * @param string $action 触发动作: start/approve/reject
     * @param string $fromNodeKey 从哪个节点来
     */
    public static function execute(WorkflowInstance $instance, Workflow $workflow, string $action, string $fromNodeKey = '')
    {
        $flowData = $workflow->flow_data;
        $nodes = $flowData['nodes'] ?? [];
        $edges = $flowData['edges'] ?? [];

        if ($action === 'start') {
            // 从开始节点出发，找下一个节点
            $startNode = array_filter($nodes, fn($n) => ($n['node_type'] ?? '') === 'start');
            $startNode = array_values($startNode)[0] ?? null;
            if (!$startNode) {
                return;
            }

            // 找到开始节点的下一个节点
            $nextNodeKey = self::getNextNodeKey($startNode['node_key'], $edges, $nodes, []);
            if ($nextNodeKey) {
                self::createTasks($instance, $workflow, $nextNodeKey, $nodes, 'start');
            }
        } elseif ($action === 'approve') {
            // 审批通过后，找下一个节点
            $nextNodeKey = self::getNextNodeKey($fromNodeKey, $edges, $nodes, []);

            if ($nextNodeKey) {
                $nextNode = array_filter($nodes, fn($n) => ($n['node_key'] ?? '') === $nextNodeKey);
                $nextNode = array_values($nextNode)[0] ?? null;

                if ($nextNode) {
                    if ($nextNode['node_type'] === 'end') {
                        // 流程结束
                        $instance->status = WorkflowInstance::STATUS_FINISHED;
                        $instance->is_ended = 1;
                        $instance->current_node_key = $nextNodeKey;
                        $instance->current_node_name = $nextNode['node_name'] ?? '';
                        $instance->end_time = date('Y-m-d H:i:s');
                        $instance->save();
                    } else {
                        // 创建下一节点任务
                        self::createTasks($instance, $workflow, $nextNodeKey, $nodes, 'approve');
                    }
                }
            } else {
                // 没有下一节点，流程结束
                $instance->status = WorkflowInstance::STATUS_FINISHED;
                $instance->is_ended = 1;
                $instance->end_time = date('Y-m-d H:i:s');
                $instance->save();
            }
        }
    }

    /**
     * 获取下一个节点key
     * @param string $fromKey
     * @param array $edges
     * @param array $nodes
     * @param array $formData
     * @return string|null
     */
    protected static function getNextNodeKey(string $fromKey, array $edges, array $nodes, array $formData): ?string
    {
        // 找到从当前节点出发的所有连线
        $outgoingEdges = array_filter($edges, fn($e) => ($e['source_key'] ?? '') === $fromKey);

        if (empty($outgoingEdges)) {
            return null;
        }

        // 如果有多条连线，检查条件
        $outgoingEdges = array_values($outgoingEdges);

        if (count($outgoingEdges) === 1) {
            // 只有一条连线，直接返回
            return $outgoingEdges[0]['target_key'] ?? null;
        }

        // 多条连线，检查条件
        foreach ($outgoingEdges as $edge) {
            $conditionConfig = $edge['condition_config'] ?? [];
            if (self::evaluateCondition($conditionConfig, $formData)) {
                return $edge['target_key'] ?? null;
            }
        }

        // 没有匹配条件，返回默认连线
        foreach ($outgoingEdges as $edge) {
            if (($edge['edge_type'] ?? 'default') === 'default') {
                return $edge['target_key'] ?? null;
            }
        }

        return null;
    }

    /**
     * 评估条件
     * @param array $condition
     * @param array $formData
     * @return bool
     */
    protected static function evaluateCondition(array $condition, array $formData): bool
    {
        if (empty($condition)) {
            return true;
        }

        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '==';
        $value = $condition['value'] ?? '';

        $formValue = $formData[$field] ?? '';

        switch ($operator) {
            case '==':
                return (string) $formValue === (string) $value;
            case '!=':
                return (string) $formValue !== (string) $value;
            case '>':
                return (float) $formValue > (float) $value;
            case '>=':
                return (float) $formValue >= (float) $value;
            case '<':
                return (float) $formValue < (float) $value;
            case '<=':
                return (float) $formValue <= (float) $value;
            case 'contains':
                return strpos((string) $formValue, (string) $value) !== false;
            case 'not_contains':
                return strpos((string) $formValue, (string) $value) === false;
            default:
                return true;
        }
    }

    /**
     * 创建任务
     * @param WorkflowInstance $instance
     * @param Workflow $workflow
     * @param string $nodeKey
     * @param array $nodes
     * @param string $actionType
     */
    protected static function createTasks(WorkflowInstance $instance, Workflow $workflow, string $nodeKey, array $nodes, string $actionType)
    {
        // 将之前当前任务标记为非当前
        WorkflowTask::where('instance_id', $instance->id)
            ->where('is_current', 1)
            ->update(['is_current' => 0]);

        $node = array_filter($nodes, fn($n) => ($n['node_key'] ?? '') === $nodeKey);
        $node = array_values($node)[0] ?? null;

        if (!$node) {
            return;
        }

        $nodeType = $node['node_type'] ?? '';
        $config = $node['config'] ?? [];

        // 更新实例当前节点
        $instance->current_node_key = $nodeKey;
        $instance->current_node_name = $node['node_name'] ?? '';
        $instance->save();

        if ($nodeType === 'approver') {
            // 审批人节点
            $approvers = $config['approvers'] ?? [];
            $taskType = $config['approval_type'] ?? 'approve'; // approve=单人, counter_sign=会签

            if (empty($approvers)) {
                return;
            }

            if ($taskType === 'approve') {
                // 单人审批：只创建第一个审批人任务
                $approver = $approvers[0] ?? [];
                self::createTask($instance, $workflow, $node, $approver, WorkflowTask::TYPE_APPROVE);
            } else {
                // 会签：创建所有审批人任务
                foreach ($approvers as $approver) {
                    self::createTask($instance, $workflow, $node, $approver, WorkflowTask::TYPE_COUNTER_SIGN);
                }
            }
        }
    }

    /**
     * 创建单个任务
     * @param WorkflowInstance $instance
     * @param Workflow $workflow
     * @param array $node
     * @param array $approver
     * @param string $taskType
     */
    protected static function createTask(WorkflowInstance $instance, Workflow $workflow, array $node, array $approver, string $taskType)
    {
        $task = new WorkflowTask();
        $task->instance_id = $instance->id;
        $task->workflow_id = $workflow->id;
        $task->node_key = $node['node_key'] ?? '';
        $task->node_name = $node['node_name'] ?? '';
        $task->task_type = $taskType;
        $task->assignee = $approver['id'] ?? 0;
        $task->assignee_name = $approver['name'] ?? '';
        $task->action_status = WorkflowTask::STATUS_PENDING;
        $task->is_current = 1;
        $task->save();
    }
}

<?php
/**
 * 飞羽后台管理系统 - 工作流逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\workflow;

use app\common\service\JsonService;
use app\model\Workflow;
use app\model\WorkflowNode;
use app\model\WorkflowEdge;
use app\model\WorkflowInstance;
use app\model\WorkflowTask;
use think\facade\Db;

/**
 * 工作流逻辑
 * Class WorkflowLogic
 * @package app\adminapi\logic\workflow
 */
class WorkflowLogic
{
    /**
     * 获取流程列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;
        $keyword = $params['keyword'] ?? '';
        $status = isset($params['status']) ? (int) $params['status'] : null;

        $where = function ($query) use ($keyword, $status) {
            if (!empty($keyword)) {
                $query->whereLike('name|code|description', "%{$keyword}%");
            }
            if ($status !== null) {
                $query->where('status', $status);
            }
        };

        $query = Workflow::where($where)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 获取流程详情
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $workflow = Workflow::with(['nodes', 'edges'])->find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        $data = $workflow->toArray();
        // 合并节点和连线到flow_data中
        $data['flow_data'] = [
            'nodes' => $data['nodes'] ?? [],
            'edges' => $data['edges'] ?? [],
        ];
        unset($data['nodes'], $data['edges']);

        return $data;
    }

    /**
     * 添加流程
     * @param array $params
     * @param int $adminId
     * @return int
     */
    public static function add(array $params, int $adminId): int
    {
        self::validate($params);

        // 检查编码唯一性
        if (Workflow::where('code', $params['code'])->find()) {
            JsonService::throwFail('流程编码已存在');
        }

        $workflow = new Workflow();
        $workflow->name = $params['name'];
        $workflow->code = $params['code'];
        $workflow->description = $params['description'] ?? '';
        $workflow->flow_data = $params['flow_data'] ?? [];
        $workflow->status = (int) ($params['status'] ?? 1);
        $workflow->is_published = 0;
        $workflow->create_user = $adminId;
        $workflow->save();

        // 保存节点
        if (!empty($params['flow_data']['nodes'])) {
            self::saveNodes($workflow->id, $params['flow_data']['nodes']);
        }

        // 保存连线
        if (!empty($params['flow_data']['edges'])) {
            self::saveEdges($workflow->id, $params['flow_data']['edges']);
        }

        return $workflow->id;
    }

    /**
     * 编辑流程
     * @param array $params
     * @param int $adminId
     * @return bool
     */
    public static function edit(array $params, int $adminId): bool
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        // 如果已发布，不允许编辑
        if ($workflow->is_published == 1) {
            JsonService::throwFail('已发布的流程不允许直接编辑，请先取消发布');
        }

        // 检查编码唯一性
        if (Workflow::where('code', $params['code'])->where('id', '<>', $id)->find()) {
            JsonService::throwFail('流程编码已存在');
        }

        $workflow->name = $params['name'];
        $workflow->code = $params['code'];
        $workflow->description = $params['description'] ?? '';
        $workflow->flow_data = $params['flow_data'] ?? [];
        $workflow->status = (int) ($params['status'] ?? 1);
        $workflow->update_user = $adminId;
        $workflow->save();

        // 删除旧节点和连线
        WorkflowNode::where('workflow_id', $id)->delete();
        WorkflowEdge::where('workflow_id', $id)->delete();

        // 保存新节点
        if (!empty($params['flow_data']['nodes'])) {
            self::saveNodes($workflow->id, $params['flow_data']['nodes']);
        }

        // 保存新连线
        if (!empty($params['flow_data']['edges'])) {
            self::saveEdges($workflow->id, $params['flow_data']['edges']);
        }

        return true;
    }

    /**
     * 删除流程
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        // 检查是否有实例
        if (WorkflowInstance::where('workflow_id', $id)->find()) {
            JsonService::throwFail('该流程已有实例，无法删除');
        }

        // 删除节点和连线
        WorkflowNode::where('workflow_id', $id)->delete();
        WorkflowEdge::where('workflow_id', $id)->delete();

        $workflow->delete();

        return true;
    }

    /**
     * 发布流程
     * @param int $id
     * @return bool
     */
    public static function publish(int $id): bool
    {
        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        // 验证流程完整性
        self::validateFlowData($workflow->flow_data);

        $workflow->is_published = 1;
        $workflow->version = $workflow->version + 1;
        $workflow->save();

        return true;
    }

    /**
     * 取消发布
     * @param int $id
     * @return bool
     */
    public static function unpublish(int $id): bool
    {
        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        // 检查是否有进行中的实例
        if (WorkflowInstance::where('workflow_id', $id)->where('is_ended', 0)->find()) {
            JsonService::throwFail('该流程有待处理的实例，无法取消发布');
        }

        $workflow->is_published = 0;
        $workflow->save();

        return true;
    }

    /**
     * 切换状态
     * @param int $id
     * @return bool
     */
    public static function toggleStatus(int $id): bool
    {
        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }

        $workflow->status = $workflow->status === 1 ? 0 : 1;
        $workflow->save();

        return true;
    }

    /**
     * 保存节点
     * @param int $workflowId
     * @param array $nodes
     */
    protected static function saveNodes(int $workflowId, array $nodes): void
    {
        foreach ($nodes as $node) {
            $nodeModel = new WorkflowNode();
            $nodeModel->workflow_id = $workflowId;
            $nodeModel->node_type = $node['node_type'] ?? '';
            $nodeModel->node_name = $node['node_name'] ?? '';
            $nodeModel->node_key = $node['node_key'] ?? '';
            $nodeModel->position_x = $node['position_x'] ?? 0;
            $nodeModel->position_y = $node['position_y'] ?? 0;
            $nodeModel->config = $node['config'] ?? [];
            $nodeModel->save();
        }
    }

    /**
     * 保存连线
     * @param int $workflowId
     * @param array $edges
     */
    protected static function saveEdges(int $workflowId, array $edges): void
    {
        foreach ($edges as $edge) {
            $edgeModel = new WorkflowEdge();
            $edgeModel->workflow_id = $workflowId;
            $edgeModel->edge_type = $edge['edge_type'] ?? 'default';
            $edgeModel->source_key = $edge['source_key'] ?? '';
            $edgeModel->target_key = $edge['target_key'] ?? '';
            $edgeModel->label = $edge['label'] ?? '';
            $edgeModel->condition_config = $edge['condition_config'] ?? [];
            $edgeModel->save();
        }
    }

    /**
     * 验证参数
     * @param array $params
     */
    protected static function validate(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('流程名称不能为空');
        }
        if (empty($params['code'])) {
            JsonService::throwFail('流程编码不能为空');
        }
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $params['code'])) {
            JsonService::throwFail('流程编码格式错误，只能包含小写字母、数字和下划线，且以字母开头');
        }
    }

    /**
     * 验证流程数据完整性
     * @param array $flowData
     */
    protected static function validateFlowData(array $flowData): void
    {
        $nodes = $flowData['nodes'] ?? [];
        $edges = $flowData['edges'] ?? [];

        if (empty($nodes)) {
            JsonService::throwFail('流程节点不能为空');
        }

        // 检查开始节点
        $startNodes = array_filter($nodes, fn($n) => ($n['node_type'] ?? '') === 'start');
        if (count($startNodes) !== 1) {
            JsonService::throwFail('流程必须有且只有一个开始节点');
        }

        // 检查结束节点
        $endNodes = array_filter($nodes, fn($n) => ($n['node_type'] ?? '') === 'end');
        if (count($endNodes) !== 1) {
            JsonService::throwFail('流程必须要有结束节点');
        }

        // 检查审批人节点
        $approverNodes = array_filter($nodes, fn($n) => ($n['node_type'] ?? '') === 'approver');
        if (empty($approverNodes)) {
            JsonService::throwFail('流程至少需要一个审批人节点');
        }
    }

    // ==================== 实例管理 ====================

    /**
     * 获取实例列表
     * @param array $params
     * @return array
     */
    public static function getInstanceList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;
        $keyword = $params['keyword'] ?? '';
        $status = isset($params['status']) ? (int) $params['status'] : null;
        $workflowId = (int) ($params['workflow_id'] ?? 0);

        $where = function ($query) use ($keyword, $status, $workflowId) {
            if (!empty($keyword)) {
                $query->whereLike('title|instance_no', "%{$keyword}%");
            }
            if ($status !== null) {
                $query->where('status', $status);
            }
            if ($workflowId > 0) {
                $query->where('workflow_id', $workflowId);
            }
        };

        $query = WorkflowInstance::where($where)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 获取实例详情
     * @param int $id
     * @return array
     */
    public static function getInstanceInfo(int $id): array
    {
        $instance = WorkflowInstance::with(['tasks'])->find($id);
        if (empty($instance)) {
            JsonService::throwFail('实例不存在');
        }

        return $instance->toArray();
    }

    /**
     * 获取我的待办任务
     * @param array $params
     * @param int $userId
     * @return array
     */
    public static function getTodoList(array $params, int $userId): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;

        $query = WorkflowTask::with(['instance', 'instance.workflow'])
            ->where('assignee', $userId)
            ->where('action_status', WorkflowTask::STATUS_PENDING)
            ->where('is_current', 1)
            ->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 发起流程
     * @param array $params
     * @param int $userId
     * @param string $userName
     * @return int
     */
    public static function start(array $params, int $userId, string $userName): int
    {
        $workflowId = (int) ($params['workflow_id'] ?? 0);
        if (empty($workflowId)) {
            JsonService::throwFail('流程ID不能为空');
        }

        $workflow = Workflow::with(['nodes', 'edges'])->find($workflowId);
        if (empty($workflow)) {
            JsonService::throwFail('流程不存在');
        }
        if ($workflow->status !== 1) {
            JsonService::throwFail('流程已禁用');
        }
        if ($workflow->is_published !== 1) {
            JsonService::throwFail('流程未发布');
        }

        // 生成实例编号
        $instanceNo = 'WF' . date('YmdHis') . rand(1000, 9999);

        // 创建实例
        $instance = new WorkflowInstance();
        $instance->workflow_id = $workflowId;
        $instance->workflow_name = $workflow->name;
        $instance->instance_no = $instanceNo;
        $instance->title = $params['title'] ?? $workflow->name . '申请';
        $instance->apply_user = $userId;
        $instance->apply_user_name = $userName;
        $instance->form_data = $params['form_data'] ?? [];
        $instance->status = WorkflowInstance::STATUS_RUNNING;
        $instance->is_ended = 0;
        $instance->save();

        // 触发工作流执行器
        WorkflowExecutor::execute($instance, $workflow, 'start');

        return $instance->id;
    }

    /**
     * 审批操作
     * @param array $params
     * @param int $userId
     * @param string $userName
     * @return bool
     */
    public static function approve(array $params, int $userId, string $userName): bool
    {
        $taskId = (int) ($params['task_id'] ?? 0);
        $action = $params['action'] ?? 'approve'; // approve/reject/transfer/remind
        $remark = $params['remark'] ?? '';

        if (empty($taskId)) {
            JsonService::throwFail('任务ID不能为空');
        }

        $task = WorkflowTask::with(['instance', 'instance.workflow'])->find($taskId);
        if (empty($task)) {
            JsonService::throwFail('任务不存在');
        }
        if ($task->assignee != $userId) {
            JsonService::throwFail('您不是该任务的审批人');
        }
        if ($task->action_status !== WorkflowTask::STATUS_PENDING) {
            JsonService::throwFail('该任务已处理');
        }

        $instance = $task->instance;
        $workflow = $instance->workflow;

        switch ($action) {
            case 'approve':
                // 审批通过
                $task->action_status = WorkflowTask::STATUS_APPROVED;
                $task->action_remark = $remark;
                $task->action_time = date('Y-m-d H:i:s');
                $task->save();

                // 触发工作流执行器，推进到下一节点
                WorkflowExecutor::execute($instance, $workflow, 'approve', $task->node_key);
                break;

            case 'reject':
                // 驳回
                $task->action_status = WorkflowTask::STATUS_REJECTED;
                $task->action_remark = $remark;
                $task->action_time = date('Y-m-d H:i:s');
                $task->save();

                // 更新实例状态
                $instance->status = WorkflowInstance::STATUS_REJECTED;
                $instance->is_ended = 1;
                $instance->end_time = date('Y-m-d H:i:s');
                $instance->save();
                break;

            case 'transfer':
                // 转交
                $transferTo = (int) ($params['transfer_to'] ?? 0);
                if (empty($transferTo)) {
                    JsonService::throwFail('转交目标人不能为空');
                }

                $task->action_status = WorkflowTask::STATUS_TRANSFERRED;
                $task->action_remark = $remark;
                $task->action_time = date('Y-m-d H:i:s');
                $task->transfer_from = $userId;
                $task->transfer_from_name = $userName;
                $task->save();

                // 创建新任务
                $newTask = new WorkflowTask();
                $newTask->instance_id = $instance->id;
                $newTask->workflow_id = $workflow->id;
                $newTask->node_key = $task->node_key;
                $newTask->node_name = $task->node_name;
                $newTask->task_type = $task->task_type;
                $newTask->assignee = $transferTo;
                $newTask->assignee_name = $params['transfer_to_name'] ?? '';
                $newTask->action_status = WorkflowTask::STATUS_PENDING;
                $newTask->is_current = 1;
                $newTask->save();
                break;

            case 'remind':
                // 催办
                $task->action_status = WorkflowTask::STATUS_REMINDED;
                $task->action_time = date('Y-m-d H:i:s');
                $task->save();
                // 注意：催办只是标记，实际催办通知由其他服务处理
                break;
        }

        return true;
    }

    /**
     * 撤回申请
     * @param int $instanceId
     * @param int $userId
     * @return bool
     */
    public static function withdraw(int $instanceId, int $userId): bool
    {
        $instance = WorkflowInstance::find($instanceId);
        if (empty($instance)) {
            JsonService::throwFail('实例不存在');
        }
        if ($instance->apply_user != $userId) {
            JsonService::throwFail('您不是申请人');
        }
        if ($instance->status !== WorkflowInstance::STATUS_RUNNING) {
            JsonService::throwFail('当前状态不允许撤回');
        }

        // 检查是否已有审批人处理
        $hasApproved = WorkflowTask::where('instance_id', $instanceId)
            ->where('action_status', '<>', WorkflowTask::STATUS_PENDING)
            ->find();
        if ($hasApproved) {
            JsonService::throwFail('已有审批人处理，无法撤回');
        }

        // 删除所有待处理任务
        WorkflowTask::where('instance_id', $instanceId)
            ->where('action_status', WorkflowTask::STATUS_PENDING)
            ->delete();

        // 更新实例状态
        $instance->status = WorkflowInstance::STATUS_WITHDRAWN;
        $instance->is_ended = 1;
        $instance->end_time = date('Y-m-d H:i:s');
        $instance->save();

        return true;
    }

    /**
     * 获取实例历史
     * @param int $instanceId
     * @return array
     */
    public static function getInstanceHistory(int $instanceId): array
    {
        $instance = WorkflowInstance::with(['workflow'])->find($instanceId);
        if (empty($instance)) {
            JsonService::throwFail('实例不存在');
        }

        $tasks = WorkflowTask::where('instance_id', $instanceId)
            ->order('id', 'asc')
            ->select()
            ->toArray();

        return [
            'instance' => $instance->toArray(),
            'tasks' => $tasks,
        ];
    }
}

<?php
/**
 * 工作流逻辑
 */

namespace app\adminapi\logic\workflow;

use app\model\Workflow;
use app\model\WorkflowNode;
use app\model\WorkflowInstance;
use app\model\WorkflowTask;

class WorkflowLogic
{
    /**
     * 获取工作流列表
     */
    public static function getList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $pageSize = min((int)($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;
        
        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['workflow_name|workflow_code', 'like', "%{$params['keyword']}%"];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', (int)$params['status']];
        }
        
        $list = Workflow::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();
        
        $total = Workflow::where($where)->count();
        
        foreach ($list as &$item) {
            $item['status_name'] = $item['status'] == 0 ? '草稿' : ($item['status'] == 1 ? '已发布' : '已禁用');
        }
        
        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }

    /**
     * 获取工作流详情
     */
    public static function getInfo(int $id): array
    {
        $workflow = Workflow::find($id);
        if (empty($workflow)) {
            return [];
        }
        return $workflow->toArray();
    }

    /**
     * 添加工作流
     */
    public static function add(array $params): int
    {
        if (empty($params['workflow_name'])) {
            throw new \Exception('工作流名称不能为空');
        }
        if (empty($params['workflow_code'])) {
            throw new \Exception('工作流编码不能为空');
        }
        
        $exists = Workflow::where('workflow_code', $params['workflow_code'])->find();
        if ($exists) {
            throw new \Exception('工作流编码已存在');
        }

        $workflow = new Workflow();
        $workflow->workflow_name = $params['workflow_name'];
        $workflow->workflow_code = $params['workflow_code'];
        $workflow->description = $params['description'] ?? '';
        $workflow->status = (int)($params['status'] ?? 0);
        $workflow->save();

        return $workflow->id;
    }

    /**
     * 编辑工作流
     */
    public static function edit(array $params): bool
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }
        
        $workflow = Workflow::find($id);
        if (!$workflow) {
            return false;
        }
        
        if (isset($params['workflow_name'])) {
            $workflow->workflow_name = $params['workflow_name'];
        }
        if (isset($params['workflow_code'])) {
            $workflow->workflow_code = $params['workflow_code'];
        }
        if (isset($params['description'])) {
            $workflow->description = $params['description'];
        }
        if (isset($params['status'])) {
            $workflow->status = (int)$params['status'];
        }
        
        return $workflow->save();
    }

    /**
     * 删除工作流
     */
    public static function delete(int $id): bool
    {
        $workflow = Workflow::find($id);
        if (!$workflow) {
            return false;
        }
        return $workflow->delete();
    }

    /**
     * 获取流程实例列表
     */
    public static function getInstanceList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $pageSize = min((int)($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;
        
        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['instance_name|applicant_name', 'like', "%{$params['keyword']}%"];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', (int)$params['status']];
        }
        
        $list = WorkflowInstance::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();
        
        $total = WorkflowInstance::where($where)->count();
        
        foreach ($list as &$item) {
            $item['status_name'] = $item['status'] == 0 ? '审批中' : ($item['status'] == 1 ? '已完成' : '已取消');
        }
        
        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }

    /**
     * 获取待办任务列表
     */
    public static function getTodoList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $pageSize = min((int)($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;
        $adminId = (int)($params['admin_id'] ?? 0);
        
        $where = [
            ['task_status', '=', 0],
            ['assignee_id', '=', $adminId]
        ];
        
        $list = WorkflowTask::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();
        
        $total = WorkflowTask::where($where)->count();
        
        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }
}

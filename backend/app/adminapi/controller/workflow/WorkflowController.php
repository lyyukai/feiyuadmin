<?php
/**
 * 飞羽后台管理系统 - 工作流控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\workflow;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\workflow\WorkflowLogic;
use think\Request;
use think\Response;

/**
 * 工作流控制器
 * Class WorkflowController
 * @package app\adminapi\controller\workflow
 */
class WorkflowController extends BaseAdminController
{
    /**
     * 获取当前管理员ID
     */
    protected function getAdminId(): int
    {
        return (int) ($this->request->userId ?? 0);
    }

    /**
     * 获取当前管理员名称
     */
    protected function getAdminName(): string
    {
        return $this->request->userName ?? '';
    }

    /**
     * 流程列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        $result = WorkflowLogic::getList($params);

        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
            'page' => $result['page'] ?? 1,
            'page_size' => $result['page_size'] ?? 20,
        ]);
    }

    /**
     * 流程详情
     * @return Response
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('流程ID不能为空');
        }

        $info = WorkflowLogic::getInfo($id);
        return $this->data($info);
    }

    /**
     * 添加流程
     * @return Response
     */
    public function add(): Response
    {
        $params = $this->param();
        $id = WorkflowLogic::add($params, $this->getAdminId());

        return $this->success('添加成功', ['id' => $id]);
    }

    /**
     * 编辑流程
     * @return Response
     */
    public function edit(): Response
    {
        $params = $this->param();
        WorkflowLogic::edit($params, $this->getAdminId());

        return $this->success('编辑成功');
    }

    /**
     * 删除流程
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('流程ID不能为空');
        }

        WorkflowLogic::delete($id);
        return $this->success('删除成功');
    }

    /**
     * 发布流程
     * @return Response
     */
    public function publish(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('流程ID不能为空');
        }

        WorkflowLogic::publish($id);
        return $this->success('发布成功');
    }

    /**
     * 取消发布
     * @return Response
     */
    public function unpublish(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('流程ID不能为空');
        }

        WorkflowLogic::unpublish($id);
        return $this->success('取消发布成功');
    }

    /**
     * 切换状态
     * @return Response
     */
    public function toggleStatus(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('流程ID不能为空');
        }

        WorkflowLogic::toggleStatus($id);
        return $this->success('操作成功');
    }

    // ==================== 实例管理 ====================

    /**
     * 实例列表
     * @return Response
     */
    public function instanceLists(): Response
    {
        $params = $this->param();
        $result = WorkflowLogic::getInstanceList($params);

        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
            'page' => $result['page'] ?? 1,
            'page_size' => $result['page_size'] ?? 20,
        ]);
    }

    /**
     * 实例详情
     * @return Response
     */
    public function instanceDetail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('实例ID不能为空');
        }

        $info = WorkflowLogic::getInstanceInfo($id);
        return $this->data($info);
    }

    /**
     * 我的待办
     * @return Response
     */
    public function todoList(): Response
    {
        $params = $this->param();
        $result = WorkflowLogic::getTodoList($params, $this->getAdminId());

        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
            'page' => $result['page'] ?? 1,
            'page_size' => $result['page_size'] ?? 20,
        ]);
    }

    /**
     * 发起流程
     * @return Response
     */
    public function start(): Response
    {
        $params = $this->param();
        $id = WorkflowLogic::start($params, $this->getAdminId(), $this->getAdminName());

        return $this->success('发起成功', ['id' => $id]);
    }

    /**
     * 审批操作
     * @return Response
     */
    public function approve(): Response
    {
        $params = $this->param();
        WorkflowLogic::approve($params, $this->getAdminId(), $this->getAdminName());

        return $this->success('操作成功');
    }

    /**
     * 撤回申请
     * @return Response
     */
    public function withdraw(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('实例ID不能为空');
        }

        WorkflowLogic::withdraw($id, $this->getAdminId());
        return $this->success('撤回成功');
    }

    /**
     * 实例历史
     * @return Response
     */
    public function instanceHistory(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('实例ID不能为空');
        }

        $data = WorkflowLogic::getInstanceHistory($id);
        return $this->data($data);
    }
}

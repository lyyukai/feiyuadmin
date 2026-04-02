<?php
/**
 * 飞鱼后台管理系统 - 定时任务控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\admin\CrontabLogic;
use think\Response;

/**
 * 定时任务控制器
 * Class CrontabController
 * @package app\adminapi\controller
 */
class CrontabController extends BaseAdminController
{
    /**
     * 任务列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        $result = CrontabLogic::getList($params);

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
     * 任务详情
     * @return Response
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $info = CrontabLogic::getInfo($id);
        if (empty($info)) {
            return $this->fail('任务不存在');
        }

        return $this->data($info);
    }

    /**
     * 添加任务
     * @return Response
     */
    public function add(): Response
    {
        $params = $this->param();

        if (empty($params['name'])) {
            return $this->fail('任务名称不能为空');
        }
        if (empty($params['rule'])) {
            return $this->fail('Cron规则不能为空');
        }
        if (empty($params['command'])) {
            return $this->fail('执行命令不能为空');
        }

        // 验证重试次数
        $retryTimes = (int) ($params['retry_times'] ?? 0);
        if ($retryTimes < 0 || $retryTimes > 5) {
            return $this->fail('重试次数只能为0-5');
        }

        $id = CrontabLogic::add($params);
        if ($id <= 0) {
            return $this->fail('添加失败');
        }

        return $this->success('添加成功', ['id' => $id]);
    }

    /**
     * 编辑任务
     * @return Response
     */
    public function edit(): Response
    {
        $params = $this->param();

        if (empty($params['id'])) {
            return $this->fail('任务ID不能为空');
        }

        // 验证重试次数
        $retryTimes = (int) ($params['retry_times'] ?? 0);
        if ($retryTimes < 0 || $retryTimes > 5) {
            return $this->fail('重试次数只能为0-5');
        }

        $result = CrontabLogic::edit($params);
        if (!$result) {
            return $this->fail('编辑失败');
        }

        return $this->success('编辑成功');
    }

    /**
     * 删除任务
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $result = CrontabLogic::delete($id);
        if (!$result) {
            return $this->fail('删除失败');
        }

        return $this->success('删除成功');
    }

    /**
     * 执行任务
     * @return Response
     */
    public function execute(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $result = CrontabLogic::execute($id);

        return json([
            'code' => $result['success'] ? 0 : 400,
            'msg' => $result['success'] ? '执行成功' : '执行失败',
            'data' => [
                'output' => $result['output'] ?? '',
                'duration' => $result['duration'] ?? 0,
            ],
        ]);
    }

    /**
     * 暂停任务
     * @return Response
     */
    public function pause(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $result = CrontabLogic::pause($id);
        if (!$result) {
            return $this->fail('暂停失败');
        }

        return $this->success('已暂停');
    }

    /**
     * 恢复任务
     * @return Response
     */
    public function resume(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $result = CrontabLogic::resume($id);
        if (!$result) {
            return $this->fail('恢复失败');
        }

        return $this->success('已恢复');
    }

    /**
     * 切换任务状态
     * @return Response
     */
    public function toggleStatus(): Response
    {
        $id = (int) ($this->param('id', 0));
        $status = (int) ($this->param('status', 1));

        if ($id <= 0) {
            return $this->fail('任务ID不能为空');
        }

        $result = CrontabLogic::toggleStatus($id, $status);
        if (!$result) {
            return $this->fail('操作失败');
        }

        return $this->success('操作成功');
    }

    /**
     * 获取任务日志列表
     * @return Response
     */
    public function logLists(): Response
    {
        $params = $this->param();
        $result = CrontabLogic::getLogList($params);

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
     * 清空任务日志
     * @return Response
     */
    public function clearLogs(): Response
    {
        $taskId = (int) ($this->param('task_id', 0));
        if ($taskId <= 0) {
            return $this->fail('任务ID不能为空');
        }

        CrontabLogic::clearLogs($taskId);
        return $this->success('日志已清空');
    }
}

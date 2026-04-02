<?php
/**
 * 飞鱼后台管理系统 - 表单设计控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\FormLogic;
use think\Response;

/**
 * 表单设计控制器
 * Class FormController
 * @package app\adminapi\controller
 */
class FormController extends BaseAdminController
{
    /**
     * 表单列表
     * @return Response
     */
    public function lists(): Response
    {
        $result = FormLogic::getList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
        ]);
    }

    /**
     * 表单详情
     * @return Response
     */
    public function info(): Response
    {
        $id = (int) $this->param('id', 0);
        $result = FormLogic::getInfo($id);
        return $this->data($result);
    }

    /**
     * 添加表单
     * @return Response
     */
    public function add(): Response
    {
        FormLogic::add($this->param(), $this->adminId);
        return $this->success('添加成功');
    }

    /**
     * 编辑表单
     * @return Response
     */
    public function edit(): Response
    {
        FormLogic::edit($this->param(), $this->adminId);
        return $this->success('编辑成功');
    }

    /**
     * 删除表单
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        FormLogic::delete($id);
        return $this->success('删除成功');
    }

    /**
     * 切换状态
     * @return Response
     */
    public function toggleStatus(): Response
    {
        $id = (int) $this->param('id', 0);
        FormLogic::toggleStatus($id);
        return $this->success('操作成功');
    }

    /**
     * 表单数据列表
     * @return Response
     */
    public function dataList(): Response
    {
        $result = FormLogic::getDataList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
        ]);
    }

    /**
     * 提交表单数据
     * @return Response
     */
    public function submitData(): Response
    {
        FormLogic::submitData($this->param());
        return $this->success('提交成功');
    }

    /**
     * 删除表单数据
     * @return Response
     */
    public function deleteData(): Response
    {
        $id = (int) $this->param('id', 0);
        FormLogic::deleteData($id);
        return $this->success('删除成功');
    }
}

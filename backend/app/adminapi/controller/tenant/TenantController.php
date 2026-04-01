<?php
/**
 * 飞羽后台管理系统 - 租户管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\tenant;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\tenant\TenantLogic;
use app\adminapi\logic\tenant\TenantPackageLogic;
use think\Response;

/**
 * 租户管理控制器
 * Class TenantController
 * @package app\adminapi\controller\tenant
 */
class TenantController extends BaseAdminController
{
    /**
     * 租户列表
     * @return Response
     */
    public function lists(): Response
    {
        $result = TenantLogic::getList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
        ]);
    }

    /**
     * 租户信息
     * @return Response
     */
    public function info(): Response
    {
        $id = (int) $this->param('id', 0);
        $result = TenantLogic::getInfo($id);
        return $this->data($result);
    }

    /**
     * 添加租户
     * @return Response
     */
    public function add(): Response
    {
        TenantLogic::add($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑租户
     * @return Response
     */
    public function edit(): Response
    {
        TenantLogic::edit($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除租户
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        TenantLogic::delete($id);
        return $this->success('删除成功');
    }

    /**
     * 修改状态
     * @return Response
     */
    public function status(): Response
    {
        $id = (int) $this->param('id', 0);
        $status = (int) $this->param('status', 0);
        TenantLogic::setStatus($id, $status);
        return $this->success('状态修改成功');
    }

    /**
     * 租户套餐列表
     * @return Response
     */
    public function packageLists(): Response
    {
        $result = TenantPackageLogic::getList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
        ]);
    }

    /**
     * 套餐信息
     * @return Response
     */
    public function packageInfo(): Response
    {
        $id = (int) $this->param('id', 0);
        $result = TenantPackageLogic::getInfo($id);
        return $this->data($result);
    }

    /**
     * 添加套餐
     * @return Response
     */
    public function packageAdd(): Response
    {
        TenantPackageLogic::add($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑套餐
     * @return Response
     */
    public function packageEdit(): Response
    {
        TenantPackageLogic::edit($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除套餐
     * @return Response
     */
    public function packageDelete(): Response
    {
        $id = (int) $this->param('id', 0);
        TenantPackageLogic::delete($id);
        return $this->success('删除成功');
    }
}

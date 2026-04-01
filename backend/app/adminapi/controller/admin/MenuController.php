<?php
/**
 * 飞羽后台管理系统 - 菜单管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\admin\MenuLogic;

/**
 * 菜单管理控制器
 * Class MenuController
 * @package app\adminapi\controller\admin
 */
class MenuController extends BaseAdminController
{
    /**
     * 菜单列表
     * @return \think\response\Json
     */
    public function lists()
    {
        $result = MenuLogic::getList();
        return $this->data($result);
    }

    /**
     * 菜单树
     * @return \think\response\Json
     */
    public function tree()
    {
        $result = MenuLogic::getTree();
        return $this->data($result);
    }

    /**
     * 菜单导航（用于前端）
     * @return \think\response\Json
     */
    public function nav()
    {
        $result = MenuLogic::getNav();
        return $this->data($result);
    }

    /**
     * 添加菜单
     * @return \think\response\Json
     */
    public function add()
    {
        MenuLogic::add($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑菜单
     * @return \think\response\Json
     */
    public function edit()
    {
        MenuLogic::edit($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除菜单
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = (int) $this->param('id', 0);
        MenuLogic::delete($id);
        return $this->success('删除成功');
    }
}

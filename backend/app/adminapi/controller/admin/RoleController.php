<?php
/**
 * 飞羽后台管理系统 - 角色管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;

/**
 * 角色管理控制器
 * Class RoleController
 * @package app\adminapi\controller\admin
 */
class RoleController extends BaseAdminController
{
    /**
     * 角色列表
     */
    public function lists()
    {
        $list = \app\model\Role::order('id', 'asc')->select()->toArray();
        return json(['code' => 0, 'msg' => '', 'data' => $list, 'total' => count($list)]);
    }

    /**
     * 所有角色
     */
    public function allList()
    {
        $list = \app\model\Role::where('status', 1)->column('id,name');
        return $this->data($list);
    }

    /**
     * 添加角色
     */
    public function add()
    {
        $params = $this->param();
        $role = new \app\model\Role();
        $role->name = $params['name'] ?? '';
        $role->code = $params['code'] ?? '';
        $role->remark = $params['remark'] ?? '';
        $role->sort = (int) ($params['sort'] ?? 0);
        $role->status = (int) ($params['status'] ?? 1);
        $role->save();
        return $this->success('添加成功');
    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        $role = \app\model\Role::find($id);
        if (!$role) {
            return $this->fail('角色不存在');
        }
        if (!empty($params['name'])) $role->name = $params['name'];
        if (!empty($params['code'])) $role->code = $params['code'];
        if (isset($params['remark'])) $role->remark = $params['remark'];
        if (isset($params['sort'])) $role->sort = (int) $params['sort'];
        if (isset($params['status'])) $role->status = (int) $params['status'];
        $role->save();
        return $this->success('编辑成功');
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        $id = (int) ($this->param('id', 0));
        if ($id === 1) {
            return $this->fail('超级管理员角色不能删除');
        }
        $role = \app\model\Role::find($id);
        if (!$role) {
            return $this->fail('角色不存在');
        }
        $role->delete();
        return $this->success('删除成功');
    }

    /**
     * 角色菜单
     */
    public function menus()
    {
        $id = (int) ($this->param('id', 0));
        $role = \app\model\Role::find($id);
        if (!$role) {
            return $this->fail('角色不存在');
        }
        $menuIds = \think\facade\Db::name('role_menu')->where('role_id', $id)->column('menu_id');
        return $this->data($menuIds);
    }

    /**
     * 保存角色菜单
     */
    public function saveMenus()
    {
        $id = (int) ($this->param('id', 0));
        $menuIds = $this->param('menu_ids', []);
        
        $role = \app\model\Role::find($id);
        if (!$role) {
            return $this->fail('角色不存在');
        }
        
        // 删除旧菜单
        \think\facade\Db::name('role_menu')->where('role_id', $id)->delete();
        
        // 添加新菜单
        foreach ($menuIds as $menuId) {
            \think\facade\Db::name('role_menu')->insert([
                'role_id' => $id,
                'menu_id' => (int) $menuId,
            ]);
        }
        
        return $this->success('保存成功');
    }
}

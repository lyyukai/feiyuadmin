<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Role as RoleModel;
use think\Request;
use think\Response;

/**
 * 角色控制器 - 角色CRUD + 菜单权限
 */
class Role
{
    protected Request $request;
    protected int $userId = 0;

    public function __construct()
    {
        $this->request = request();
        $this->userId = (int) ($this->request->userId ?? 0);
    }

    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function error(string $msg = '操作失败', int $code = 400): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => []]);
    }

    protected function page(int $total, array $list): Response
    {
        return json(['code' => 0, 'msg' => 'success', 'total' => $total, 'data' => $list]);
    }

    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    /**
     * 角色列表
     * GET /api/role/list
     */
    public function list(): Response
    {
        [$page, $limit] = $this->getPageParam();
        $keyword = $this->param('keyword', '');
        $status  = $this->param('status', '');

        $query = RoleModel::whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('name', "%{$keyword}%")
                  ->whereOr('code', 'like', "%{$keyword}%");
            });
        }

        if ($status !== '') {
            $query->where('status', (int) $status);
        }

        $total = $query->count();
        $list  = $query->page($page, $limit)
            ->order('sort', 'asc')
            ->select()
            ->toArray();

        return $this->page($total, $list);
    }

    /**
     * 新增角色
     * POST /api/role
     */
    public function save(): Response
    {
        $name = $this->param('name', '');
        $code = $this->param('code', '');

        if (empty($name) || empty($code)) {
            return $this->error('角色名称和角色代码不能为空');
        }

        // 检查角色代码唯一性
        if (RoleModel::where('code', $code)->whereNull('delete_time')->find()) {
            return $this->error('角色代码已存在');
        }

        $role = new RoleModel();
        $role->name       = $name;
        $role->code       = $code;
        $role->status     = (int) $this->param('status', 1);
        $role->sort       = (int) $this->param('sort', 0);
        $role->data_scope = $this->param('data_scope', 'all');
        $role->remark     = $this->param('remark', '');
        $role->save();

        // 保存菜单权限
        $menuIds = $this->param('menu_ids', []);
        if (!empty($menuIds)) {
            $role->saveMenus($menuIds);
        }

        return $this->success(['id' => $role->id], '新增成功');
    }

    /**
     * 编辑角色
     * PUT /api/role/:id
     */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        $name       = $this->param('name', '');
        $code       = $this->param('code', '');
        $status     = $this->param('status', '');
        $sort       = $this->param('sort', '');
        $dataScope  = $this->param('data_scope', '');
        $remark     = $this->param('remark', '');
        $menuIds    = $this->param('menu_ids', []);

        if ($name !== '')     $role->name       = $name;
        if ($code !== '')     $role->code       = $code;
        if ($status !== '')   $role->status     = (int) $status;
        if ($sort !== '')     $role->sort       = (int) $sort;
        if ($dataScope !== '')$role->data_scope = $dataScope;
        if ($remark !== '')   $role->remark     = $remark;

        $role->save();

        // 保存菜单权限
        if ($menuIds !== []) {
            $role->saveMenus($menuIds);
        }

        return $this->success([], '更新成功');
    }

    /**
     * 删除角色
     * DELETE /api/role/:id
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        // 超级管理员角色不可删除
        if ($id === 1) {
            return $this->error('超级管理员角色不可删除', 400);
        }

        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        // 检查是否有用户关联
        $userCount = db('sys_user_role')->where('role_id', $id)->count();
        if ($userCount > 0) {
            return $this->error('该角色下存在用户，请先解绑', 400);
        }

        // 删除菜单关联
        db('sys_role_menu')->where('role_id', $id)->delete();
        $role->delete();

        return $this->success([], '删除成功');
    }

    /**
     * 获取角色菜单权限
     * GET /api/role/:id/menus
     */
    public function menus(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        $menuIds = $role->getMenuIds();

        return $this->success($menuIds);
    }

    /**
     * 保存角色菜单权限
     * PUT /api/role/:id/menus
     */
    public function saveMenus(): Response
    {
        $id      = (int) $this->param('id', 0);
        $menuIds = $this->param('menu_ids', []);

        if (!$id) {
            return $this->error('参数错误');
        }

        // 超级管理员角色权限不可修改
        if ($id === 1) {
            return $this->error('超级管理员角色权限不可修改', 400);
        }

        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        $role->saveMenus($menuIds);

        return $this->success([], '权限保存成功');
    }

    /**
     * 获取所有角色（下拉框）
     * GET /api/role/all
     */
    public function all(): Response
    {
        $list = RoleModel::whereNull('delete_time')
            ->where('status', 1)
            ->order('sort', 'asc')
            ->column('name', 'id');

        $result = [];
        foreach ($list as $id => $name) {
            $result[] = ['id' => $id, 'name' => $name];
        }

        return $this->success($result);
    }

    protected function getPageParam(): array
    {
        $page  = (int) $this->param('page', 1);
        $limit = (int) $this->param('limit', 20);
        $limit = $limit > 100 ? 100 : $limit;
        return [$page, $limit];
    }
}

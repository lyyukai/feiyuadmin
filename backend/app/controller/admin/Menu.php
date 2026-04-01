<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Menu as MenuModel;
use app\model\UserModel;
use think\Request;
use think\Response;

/**
 * 菜单控制器 - 菜单CRUD
 */
class Menu
{
    protected Request $request;

    public function __construct()
    {
        $this->request = request();
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
     * 菜单树形列表
     * GET /api/menu/tree
     */
    public function tree(): Response
    {
        $tree = MenuModel::getTree();
        return $this->success($tree);
    }

    /**
     * 获取当前用户菜单导航
     * GET /api/menu/nav
     */
    public function nav(): Response
    {
        $userId = (int) $this->request->userId;
        $user = UserModel::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $menus = $user->getMenus();
        $tree = $this->buildMenuTree($menus);

        return $this->success($tree);
    }

    /**
     * 构建菜单树（过滤目录和菜单，type=0目录 type=1菜单）
     */
    private function buildMenuTree(array $menus): array
    {
        // 过滤出目录(type=0)和菜单(type=1)，排除按钮(type=2)
        $filtered = array_filter($menus, fn($m) => in_array($m['menu_type'], ['menu', 'iframe', 'link']));
        $indexed = array_values($filtered);

        return $this->buildTreeRecursive($indexed, 0);
    }

    /**
     * 递归构建菜单树
     */
    private function buildTreeRecursive(array $list, int $pid): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ((int) $item['pid'] === $pid) {
                $children = $this->buildTreeRecursive($list, (int) $item['id']);
                $node = [
                    'path'      => $item['path'] ?? '',
                    'name'      => $item['name'] ?? '',
                    'component' => $item['component'] ?? '',
                    'redirect'  => $item['redirect'] ?? '',
                    'meta'      => [
                        'title'    => $item['name'] ?? '',
                        'icon'     => $item['icon'] ?? '',
                        'hidden'   => (bool) ($item['is_hidden'] ?? false),
                        'full'     => (bool) ($item['is_full'] ?? false),
                        'cache'    => (bool) ($item['is_cache'] ?? false),
                        'permission' => $item['permission'] ?? '',
                    ],
                ];
                if (!empty($children)) {
                    $node['children'] = $children;
                }
                $tree[] = $node;
            }
        }
        return $tree;
    }

    /**
     * 菜单平铺列表
     * GET /api/menu/list
     */
    public function list(): Response
    {
        $keyword = $this->param('keyword', '');
        $status  = $this->param('status', '');

        $query = MenuModel::whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('name', "%{$keyword}%")
                  ->whereOr('permission', 'like', "%{$keyword}%");
            });
        }

        if ($status !== '') {
            $query->where('status', (int) $status);
        }

        $list = $query->order('sort', 'asc')->select()->toArray();

        return $this->success($list);
    }

    /**
     * 新增菜单
     * POST /api/menu
     */
    public function save(): Response
    {
        $name = $this->param('name', '');
        if (empty($name)) {
            return $this->error('菜单名称不能为空');
        }

        $menu = new MenuModel();
        $menu->name       = $name;
        $menu->pid        = (int) $this->param('pid', 0);
        $menu->path       = $this->param('path', '');
        $menu->component   = $this->param('component', '');
        $menu->redirect    = $this->param('redirect', '');
        $menu->icon        = $this->param('icon', '');
        $menu->menu_type   = $this->param('menu_type', 'menu');
        $menu->is_hidden   = (int) $this->param('is_hidden', 0);
        $menu->is_full     = (int) $this->param('is_full', 0);
        $menu->is_cache    = (int) $this->param('is_cache', 0);
        $menu->permission  = $this->param('permission', '');
        $menu->sort        = (int) $this->param('sort', 0);
        $menu->status      = (int) $this->param('status', 1);
        $menu->remark      = $this->param('remark', '');
        $menu->save();

        return $this->success(['id' => $menu->id], '新增成功');
    }

    /**
     * 编辑菜单
     * PUT /api/menu/:id
     */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $menu = MenuModel::find($id);
        if (!$menu) {
            return $this->error('菜单不存在');
        }

        $fields = ['name', 'pid', 'path', 'component', 'redirect', 'icon',
            'menu_type', 'is_hidden', 'is_full', 'is_cache', 'permission',
            'sort', 'status', 'remark'];

        foreach ($fields as $field) {
            $val = $this->param($field, '');
            if ($val !== '') {
                if (in_array($field, ['is_hidden', 'is_full', 'is_cache', 'sort', 'pid'])) {
                    $menu->$field = (int) $val;
                } else {
                    $menu->$field = $val;
                }
            }
        }

        $menu->save();

        return $this->success([], '更新成功');
    }

    /**
     * 删除菜单
     * DELETE /api/menu/:id
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        // 检查是否有子菜单
        if (MenuModel::hasChildren($id)) {
            return $this->error('该菜单下存在子菜单，请先删除', 400);
        }

        $menu = MenuModel::find($id);
        if (!$menu) {
            return $this->error('菜单不存在');
        }

        // 删除角色菜单关联
        db('sys_role_menu')->where('menu_id', $id)->delete();
        $menu->delete();

        return $this->success([], '删除成功');
    }
}

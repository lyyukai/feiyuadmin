<?php
/**
 * 飞羽后台管理系统 - 菜单管理逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\common\service\JsonService;
use app\model\Menu;

/**
 * 菜单管理逻辑
 * Class MenuLogic
 * @package app\adminapi\logic\admin
 */
class MenuLogic
{
    /**
     * 获取菜单列表
     * @return array
     */
    public static function getList(): array
    {
        $list = Menu::order('sort', 'asc')->select()->toArray();
        // 添加 path 字段（兼容前端）
        foreach ($list as &$item) {
            if (empty($item['path']) && !empty($item['url'])) {
                $item['path'] = $item['url'];
            }
        }
        return $list;
    }

    /**
     * 获取菜单树
     * @return array
     */
    public static function getTree(): array
    {
        $list = Menu::order('sort', 'asc')->select()->toArray();
        // 添加 path 字段（兼容前端）
        foreach ($list as &$item) {
            if (empty($item['path']) && !empty($item['url'])) {
                $item['path'] = $item['url'];
            }
        }
        return self::buildTree($list);
    }

    /**
     * 获取菜单导航
     * @return array
     */
    public static function getNav(): array
    {
        $list = Menu::where('status', 1)
            ->order('sort', 'asc')
            ->select()
            ->toArray();
        
        // 添加 path 字段（兼容前端）
        foreach ($list as &$item) {
            if (empty($item['path']) && !empty($item['url'])) {
                $item['path'] = $item['url'];
            }
        }
        
        return self::buildTree($list);
    }

    /**
     * 添加菜单
     * @param array $params
     */
    public static function add(array $params): void
    {
        self::validate($params);

        $menu = new Menu();
        $menu->pid = (int) ($params['pid'] ?? 0);
        $menu->name = $params['name'];
        $menu->icon = $params['icon'] ?? '';
        $menu->menu_type = $params['menu_type'] ?? 'menu';
        $menu->permission = $params['permission'] ?? '';
        $menu->url = $params['url'] ?? '';
        $menu->path = $params['path'] ?? ($params['url'] ?? '');
        $menu->url_type = (int) ($params['url_type'] ?? 1);
        $menu->is_hidden = (int) ($params['is_hidden'] ?? 0);
        $menu->sort = (int) ($params['sort'] ?? 0);
        $menu->status = (int) ($params['status'] ?? 1);
        $menu->save();
    }

    /**
     * 编辑菜单
     * @param array $params
     */
    public static function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $menu = Menu::find($id);
        if (empty($menu)) {
            JsonService::throwFail('菜单不存在');
        }

        if (!empty($params['name'])) {
            $menu->name = $params['name'];
        }
        if (isset($params['icon'])) {
            $menu->icon = $params['icon'];
        }
        if (isset($params['menu_type'])) {
            $menu->menu_type = $params['menu_type'];
        }
        if (isset($params['permission'])) {
            $menu->permission = $params['permission'];
        }
        if (isset($params['url'])) {
            $menu->url = $params['url'];
        }
        if (isset($params['url_type'])) {
            $menu->url_type = (int) $params['url_type'];
        }
        if (isset($params['is_hidden'])) {
            $menu->is_hidden = (int) $params['is_hidden'];
        }
        if (isset($params['sort'])) {
            $menu->sort = (int) $params['sort'];
        }
        if (isset($params['status'])) {
            $menu->status = (int) $params['status'];
        }

        $menu->save();
    }

    /**
     * 删除菜单
     * @param int $id
     */
    public static function delete(int $id): void
    {
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        // 检查是否有子菜单
        $childCount = Menu::where('pid', $id)->count();
        if ($childCount > 0) {
            JsonService::throwFail('请先删除子菜单');
        }

        $menu = Menu::find($id);
        if (empty($menu)) {
            JsonService::throwFail('菜单不存在');
        }

        $menu->delete();
    }

    /**
     * 验证参数
     * @param array $params
     */
    protected static function validate(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('菜单名称不能为空');
        }
    }

    /**
     * 构建树形结构
     * @param array $list
     * @param int $pid
     * @return array
     */
    protected static function buildTree(array $list, int $pid = 0): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ($item['pid'] === $pid) {
                $children = self::buildTree($list, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
}

<?php
/**
 * 微信菜单逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\WechatMenu;
use app\common\model\wechat\WechatAccount;

/**
 * 自定义菜单逻辑
 * Class WechatMenuLogic
 * @package app\adminapi\logic\wechat
 */
class WechatMenuLogic
{
    /**
     * 获取菜单列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $account_id = (int) ($params['account_id'] ?? 0);

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }

        $list = WechatMenu::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 保存菜单
     */
    public static function save(array $data): bool
    {
        try {
            $id = (int) ($data['id'] ?? 0);
            $account_id = (int) ($data['account_id'] ?? 0);

            if ($account_id <= 0) {
                throw new \Exception('请选择公众号账号');
            }

            // 验证账号存在
            $account = WechatAccount::find($account_id);
            if (!$account) {
                throw new \Exception('公众号账号不存在');
            }

            $menuData = [
                'account_id' => $account_id,
                'name' => $data['name'] ?? '',
                'menu_data' => json_encode($data['menu_data'] ?? [], JSON_UNESCAPED_UNICODE),
                'status' => 0, // 保存后默认未发布
                'update_time' => date('Y-m-d H:i:s'),
            ];

            if ($id > 0) {
                $model = WechatMenu::find($id);
                if (!$model) {
                    throw new \Exception('菜单不存在');
                }
                $model->name = $menuData['name'];
                $model->menu_data = $menuData['menu_data'];
                $model->status = $menuData['status'];
                $model->update_time = $menuData['update_time'];
                return $model->save() !== false;
            } else {
                $menuData['create_time'] = date('Y-m-d H:i:s');
                $model = new WechatMenu();
                return $model->save($menuData) !== false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 删除菜单
     */
    public static function delete(int $id): bool
    {
        try {
            $model = WechatMenu::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取菜单详情
     */
    public static function getDetail(int $id): ?WechatMenu
    {
        $model = WechatMenu::find($id);
        if ($model && $model->menu_data) {
            $model->menu_data = json_decode($model->menu_data, true);
        }
        return $model;
    }
}

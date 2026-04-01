<?php
/**
 * 微信账号逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\WechatAccount;
use think\facade\Db;

/**
 * 公众号账号逻辑
 * Class WechatAccountLogic
 * @package app\adminapi\logic\wechat
 */
class WechatAccountLogic
{
    /**
     * 获取账号列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $name = $params['name'] ?? '';
        $status = $params['status'] ?? '';
        $type = $params['type'] ?? '';

        $where = [];
        if ($name !== '') {
            $where[] = ['name', 'like', "%{$name}%"];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }
        if ($type !== '') {
            $where[] = ['type', '=', (int) $type];
        }

        $list = WechatAccount::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加账号
     */
    public static function add(array $data): bool
    {
        try {
            $model = new WechatAccount();
            $model->name = $data['name'] ?? '';
            $model->appid = $data['appid'] ?? '';
            $model->appsecret = $data['appsecret'] ?? '';
            $model->token = $data['token'] ?? '';
            $model->encoding_aeskey = $data['encoding_aeskey'] ?? '';
            $model->type = (int) ($data['type'] ?? 1);
            $model->status = (int) ($data['status'] ?? 1);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 编辑账号
     */
    public static function edit(int $id, array $data): bool
    {
        try {
            $model = WechatAccount::find($id);
            if (!$model) {
                return false;
            }

            $model->name = $data['name'] ?? '';
            $model->appid = $data['appid'] ?? '';
            if (!empty($data['appsecret'])) {
                $model->appsecret = $data['appsecret'];
            }
            $model->token = $data['token'] ?? '';
            $model->encoding_aeskey = $data['encoding_aeskey'] ?? '';
            $model->type = (int) ($data['type'] ?? 1);
            $model->status = (int) ($data['status'] ?? 1);
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除账号
     */
    public static function delete(int $id): bool
    {
        try {
            $model = WechatAccount::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取账号详情
     */
    public static function getDetail(int $id): ?WechatAccount
    {
        return WechatAccount::find($id);
    }
}

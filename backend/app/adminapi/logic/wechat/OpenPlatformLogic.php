<?php
/**
 * 开放平台逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\OpenPlatform;
use app\common\model\wechat\OpenPlatformAuth;

/**
 * 开放平台逻辑
 * Class OpenPlatformLogic
 * @package app\adminapi\logic\wechat
 */
class OpenPlatformLogic
{
    /**
     * 获取平台列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $name = $params['name'] ?? '';
        $status = $params['status'] ?? '';

        $where = [];
        if ($name !== '') {
            $where[] = ['name', 'like', "%{$name}%"];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }

        $list = OpenPlatform::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加平台
     */
    public static function add(array $data): bool
    {
        try {
            $model = new OpenPlatform();
            $model->name = $data['name'] ?? '';
            $model->appid = $data['appid'] ?? '';
            $model->appsecret = $data['appsecret'] ?? '';
            $model->token = $data['token'] ?? '';
            $model->encoding_aeskey = $data['encoding_aeskey'] ?? '';
            $model->status = (int) ($data['status'] ?? 1);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 编辑平台
     */
    public static function edit(int $id, array $data): bool
    {
        try {
            $model = OpenPlatform::find($id);
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
            $model->status = (int) ($data['status'] ?? 1);
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除平台
     */
    public static function delete(int $id): bool
    {
        try {
            $model = OpenPlatform::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取平台详情
     */
    public static function getDetail(int $id): ?OpenPlatform
    {
        return OpenPlatform::find($id);
    }

    /**
     * 获取授权列表
     */
    public static function getAuthList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $platform_id = (int) ($params['platform_id'] ?? 0);
        $authorizer_type = $params['authorizer_type'] ?? '';
        $status = $params['status'] ?? '';

        $where = [];
        if ($platform_id > 0) {
            $where[] = ['platform_id', '=', $platform_id];
        }
        if ($authorizer_type !== '') {
            $where[] = ['authorizer_type', '=', (int) $authorizer_type];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }

        $list = OpenPlatformAuth::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 获取授权详情
     */
    public static function getAuthDetail(int $id): ?OpenPlatformAuth
    {
        return OpenPlatformAuth::find($id);
    }

    /**
     * 更新授权信息
     */
    public static function updateAuth(int $id, array $data): bool
    {
        try {
            $model = OpenPlatformAuth::find($id);
            if (!$model) {
                return false;
            }

            $model->nick_name = $data['nick_name'] ?? '';
            $model->head_img = $data['head_img'] ?? '';
            $model->service_type_info = $data['service_type_info'] ?? '';
            $model->verify_type_info = $data['verify_type_info'] ?? '';
            $model->user_name = $data['user_name'] ?? '';
            $model->principal_name = $data['principal_name'] ?? '';
            $model->business_info = $data['business_info'] ?? '';
            $model->alias = $data['alias'] ?? '';
            $model->qrcode_url = $data['qrcode_url'] ?? '';
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取授权URL
     */
    public static function getPreAuthUrl(int $platformId, string $redirectUri): string
    {
        $platform = OpenPlatform::find($platformId);
        if (!$platform) {
            return '';
        }

        // TODO: 生成微信预授权URL
        // $url = sprintf(
        //     'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
        //     $platform->appid,
        //     $preAuthCode,
        //     urlencode($redirectUri)
        // );

        return '';
    }
}

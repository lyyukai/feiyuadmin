<?php
/**
 * 微信粉丝逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\WechatFans;
use app\common\model\wechat\WechatFansTag;

/**
 * 粉丝管理逻辑
 * Class WechatFansLogic
 * @package app\adminapi\logic\wechat
 */
class WechatFansLogic
{
    /**
     * 获取粉丝列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $account_id = (int) ($params['account_id'] ?? 0);
        $status = $params['status'] ?? '';
        $tagid = $params['tagid'] ?? '';
        $keyword = $params['keyword'] ?? '';

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }
        if ($tagid !== '') {
            $where[] = ['tagid_list', 'like', "%{$tagid}%"];
        }
        if ($keyword !== '') {
            $where[] = ['nickname|openid|remark', 'like', "%{$keyword}%"];
        }

        $list = WechatFans::where($where)
            ->order('subscribe_time', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 获取粉丝详情
     */
    public static function getDetail(int $id): ?WechatFans
    {
        return WechatFans::find($id);
    }

    /**
     * 更新粉丝备注
     */
    public static function updateRemark(int $id, string $remark): bool
    {
        try {
            $model = WechatFans::find($id);
            if (!$model) {
                return false;
            }
            $model->remark = $remark;
            $model->update_time = date('Y-m-d H:i:s');
            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 设置黑名单
     */
    public static function setBlacklist(int $id, int $blacklist = 1): bool
    {
        try {
            $model = WechatFans::find($id);
            if (!$model) {
                return false;
            }
            $model->blacklist = $blacklist;
            $model->update_time = date('Y-m-d H:i:s');
            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 同步粉丝（从微信服务器）
     */
    public static function syncFans(int $accountId): bool
    {
        // TODO: 调用微信API同步粉丝
        // $wechat = new \EasyWeChat\OfficialAccount\Application($config);
        // $userList = $wechat->user->list($nextOpenId);
        return true;
    }

    /**
     * 获取标签列表
     */
    public static function getTagList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $account_id = (int) ($params['account_id'] ?? 0);

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }

        $list = WechatFansTag::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 创建标签
     */
    public static function createTag(array $data): bool
    {
        try {
            $model = new WechatFansTag();
            $model->account_id = (int) ($data['account_id'] ?? 0);
            $model->name = $data['name'] ?? '';
            $model->tagid = 0; // 微信返回的tagid
            $model->fans_count = 0;
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除标签
     */
    public static function deleteTag(int $id): bool
    {
        try {
            $model = WechatFansTag::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取粉丝统计
     */
    public static function getStatistics(int $accountId = 0): array
    {
        $where = [];
        if ($accountId > 0) {
            $where[] = ['account_id', '=', $accountId];
        }

        $total = WechatFans::where($where)->count();
        $active = WechatFans::where(array_merge($where, [['status', '=', 1]]))->count();
        $inactive = WechatFans::where(array_merge($where, [['status', '=', 0]]))->count();
        $blacklist = WechatFans::where(array_merge($where, [['blacklist', '=', 1]]))->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'blacklist' => $blacklist,
        ];
    }
}

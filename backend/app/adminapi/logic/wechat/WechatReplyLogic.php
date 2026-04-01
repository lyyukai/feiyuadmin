<?php
/**
 * 微信自动回复逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\WechatReply;

/**
 * 自动回复逻辑
 * Class WechatReplyLogic
 * @package app\adminapi\logic\wechat
 */
class WechatReplyLogic
{
    /**
     * 获取回复规则列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $account_id = (int) ($params['account_id'] ?? 0);
        $type = $params['type'] ?? '';

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }

        $list = WechatReply::where($where)
            ->order('priority', 'desc')
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加规则
     */
    public static function add(array $data): bool
    {
        try {
            $model = new WechatReply();
            $model->account_id = (int) ($data['account_id'] ?? 0);
            $model->type = $data['type'] ?? 'keyword';
            $model->keyword = $data['keyword'] ?? '';
            $model->reply_type = $data['reply_type'] ?? 'text';
            $model->content = $data['content'] ?? '';
            $model->media_id = $data['media_id'] ?? '';
            $model->match_mode = $data['match_mode'] ?? 'full';
            $model->priority = (int) ($data['priority'] ?? 0);
            $model->status = (int) ($data['status'] ?? 1);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 编辑规则
     */
    public static function edit(int $id, array $data): bool
    {
        try {
            $model = WechatReply::find($id);
            if (!$model) {
                return false;
            }

            $model->account_id = (int) ($data['account_id'] ?? 0);
            $model->type = $data['type'] ?? 'keyword';
            $model->keyword = $data['keyword'] ?? '';
            $model->reply_type = $data['reply_type'] ?? 'text';
            $model->content = $data['content'] ?? '';
            $model->media_id = $data['media_id'] ?? '';
            $model->match_mode = $data['match_mode'] ?? 'full';
            $model->priority = (int) ($data['priority'] ?? 0);
            $model->status = (int) ($data['status'] ?? 1);
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除规则
     */
    public static function delete(int $id): bool
    {
        try {
            $model = WechatReply::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取规则详情
     */
    public static function getDetail(int $id): ?WechatReply
    {
        return WechatReply::find($id);
    }
}

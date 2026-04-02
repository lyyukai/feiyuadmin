<?php
/**
 * 飞鱼后台管理系统 - 消息通知逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\model\Notice;

/**
 * 消息通知逻辑
 * Class NoticeLogic
 * @package app\adminapi\logic\admin
 */
class NoticeLogic
{
    /**
     * 获取消息列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $pageSize = min((int) ($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;

        $where = [];
        if (isset($params['type']) && $params['type'] !== '') {
            $where[] = ['type', '=', (int) $params['type']];
        }
        if (isset($params['is_read']) && $params['is_read'] !== '') {
            $where[] = ['is_read', '=', (int) $params['is_read']];
        }
        if (!empty($params['keyword'])) {
            $where[] = ['title|content', 'like', "%{$params['keyword']}%"];
        }

        // 支持接收者ID筛选
        if (!empty($params['receiver_id'])) {
            $where[] = function ($query) use ($params) {
                $query->where('receiver_id', $params['receiver_id'])->whereOr('receiver_id', 0);
            };
        }

        $query = Notice::where($where)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $pageSize)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * 获取消息详情
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $notice = Notice::find($id);
        if (empty($notice)) {
            return [];
        }
        return $notice->toArray();
    }

    /**
     * 发送消息
     * @param array $params
     * @return int
     */
    public static function send(array $params): int
    {
        $notice = new Notice();
        $notice->title = $params['title'] ?? '';
        $notice->content = $params['content'] ?? '';
        $notice->type = (int) ($params['type'] ?? Notice::TYPE_SYSTEM);
        $notice->sender_id = (int) ($params['sender_id'] ?? 0);
        $notice->sender_name = $params['sender_name'] ?? '系统';
        $notice->receiver_id = (int) ($params['receiver_id'] ?? 0);
        $notice->status = (int) ($params['status'] ?? Notice::STATUS_ENABLED);
        $notice->is_read = Notice::UNREAD;
        $notice->save();

        return $notice->id;
    }

    /**
     * 编辑消息
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }

        $notice = Notice::find($id);
        if (empty($notice)) {
            return false;
        }

        if (isset($params['title'])) {
            $notice->title = $params['title'];
        }
        if (isset($params['content'])) {
            $notice->content = $params['content'];
        }
        if (isset($params['type'])) {
            $notice->type = (int) $params['type'];
        }
        if (isset($params['receiver_id'])) {
            $notice->receiver_id = (int) $params['receiver_id'];
        }
        if (isset($params['status'])) {
            $notice->status = (int) $params['status'];
        }

        return $notice->save() !== false;
    }

    /**
     * 删除消息
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $notice = Notice::find($id);
        if (empty($notice)) {
            return false;
        }
        return $notice->delete() !== false;
    }

    /**
     * 标记已读
     * @param array $params
     * @return int 影响的行数
     */
    public static function markRead(array $params): int
    {
        $id = (int) ($params['id'] ?? 0);

        if ($id > 0) {
            // 标记单条
            return Notice::where('id', $id)->update(['is_read' => Notice::READ]);
        } else {
            // 标记全部已读（按接收者）
            $receiverId = (int) ($params['receiver_id'] ?? 0);
            return Notice::where('receiver_id', $receiverId)
                ->whereOr('receiver_id', 0)
                ->where('is_read', Notice::UNREAD)
                ->update(['is_read' => Notice::READ]);
        }
    }

    /**
     * 获取未读消息数量
     * @param int $receiverId
     * @return int
     */
    public static function getUnreadCount(int $receiverId = 0): int
    {
        return Notice::where('is_read', Notice::UNREAD)
            ->where(function ($query) use ($receiverId) {
                $query->where('receiver_id', $receiverId)->whereOr('receiver_id', 0);
            })
            ->count();
    }
}

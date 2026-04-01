<?php
declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\model\NoticeChannel;

/**
 * 通知渠道逻辑
 */
class NoticeChannelLogic
{
    /**
     * 获取渠道列表
     */
    public static function getList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $pageSize = min((int)($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;

        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['channel_name|code', 'like', "%{$params['keyword']}%"];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', (int)$params['status']];
        }

        $list = NoticeChannel::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();

        $total = NoticeChannel::where($where)->count();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }

    /**
     * 获取渠道详情
     */
    public static function getInfo(int $id): array
    {
        $channel = NoticeChannel::find($id);
        return $channel ? $channel->toArray() : [];
    }

    /**
     * 添加渠道
     */
    public static function add(array $params): int
    {
        // 字段名映射
        $name = $params['channel_name'] ?? $params['name'] ?? '';
        $type = $params['channel_type'] ?? $params['type'] ?? 'email';
        
        if (empty($name)) {
            throw new \Exception('渠道名称不能为空');
        }
        
        // 检查编码唯一性
        $code = $params['code'] ?? '';
        if (!empty($code)) {
            $exists = NoticeChannel::where('code', $code)->find();
            if ($exists) {
                throw new \Exception('渠道编码已存在');
            }
        }

        $channel = new NoticeChannel();
        $channel->channel_name = $name;
        $channel->code = $code;
        $channel->channel_type = is_string($type) ? $type : (string)$type;
        $channel->status = (int)($params['status'] ?? 1);
        $config = $params['config'] ?? [];
        $channel->config = is_array($config) ? json_encode($config, JSON_UNESCAPED_UNICODE) : $config;
        $channel->remark = $params['remark'] ?? '';
        $channel->save();

        return $channel->id;
    }

    /**
     * 编辑渠道
     */
    public static function edit(array $params): bool
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }

        $channel = NoticeChannel::find($id);
        if (!$channel) {
            return false;
        }

        if (isset($params['channel_name'])) {
            $channel->channel_name = $params['channel_name'];
        }
        if (isset($params['name'])) {
            $channel->channel_name = $params['name'];
        }
        if (isset($params['channel_type'])) {
            $channel->channel_type = $params['channel_type'];
        }
        if (isset($params['type'])) {
            $channel->channel_type = is_string($params['type']) ? $params['type'] : (string)$params['type'];
        }
        if (isset($params['code'])) {
            if ($params['code'] !== $channel->code) {
                $exists = NoticeChannel::where('code', $params['code'])->where('id', '<>', $id)->find();
                if ($exists) {
                    throw new \Exception('渠道编码已存在');
                }
            }
            $channel->code = $params['code'];
        }
        if (isset($params['status'])) {
            $channel->status = (int)$params['status'];
        }
        if (isset($params['config'])) {
            $config = $params['config'];
            $channel->config = is_array($config) ? json_encode($config, JSON_UNESCAPED_UNICODE) : $config;
        }
        if (isset($params['remark'])) {
            $channel->remark = $params['remark'];
        }

        return $channel->save();
    }

    /**
     * 删除渠道
     */
    public static function delete(int $id): bool
    {
        $channel = NoticeChannel::find($id);
        if (!$channel) {
            return false;
        }
        return $channel->delete();
    }
}

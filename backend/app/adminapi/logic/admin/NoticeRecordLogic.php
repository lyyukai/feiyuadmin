<?php
declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\model\NoticeRecord;

/**
 * 发送记录逻辑
 */
class NoticeRecordLogic
{
    /**
     * 获取发送记录列表
     */
    public static function getList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = [];
        if (!empty($params['channel_type'])) {
            $where[] = ['channel_type', '=', $params['channel_type']];
        }
        if (isset($params['send_status']) && $params['send_status'] !== '') {
            $where[] = ['send_status', '=', (int)$params['send_status']];
        }
        if (!empty($params['keyword'])) {
            $where[] = ['receiver|title', 'like', "%{$params['keyword']}%"];
        }

        $list = NoticeRecord::where($where)
            ->order('id', 'desc')
            ->limit($offset, $limit)
            ->select()
            ->toArray();

        $total = NoticeRecord::where($where)->count();

        return ['list' => $list, 'total' => $total];
    }

    /**
     * 获取发送记录详情
     */
    public static function getInfo(int $id): array
    {
        $record = NoticeRecord::find($id);
        return $record ? $record->toArray() : [];
    }

    /**
     * 获取发送统计
     */
    public static function getStatistics(array $params): array
    {
        $today = date('Y-m-d');
        $total = NoticeRecord::count();
        $todayCount = NoticeRecord::whereTime('created_at', 'today')->count();
        $successCount = NoticeRecord::where('send_status', 1)->count();
        $failCount = NoticeRecord::where('send_status', 2)->count();

        return [
            'total' => $total,
            'today' => $todayCount,
            'success' => $successCount,
            'fail' => $failCount,
        ];
    }
}

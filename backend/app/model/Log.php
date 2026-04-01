<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 操作日志模型
 */
class Log extends Model
{
    protected $name = 'log';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_time';

    protected $updateTime = false;

    /**
     * 按条件搜索日志
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db();

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->whereOr('username', 'like', "%{$keyword}%")
                  ->whereOr('url', 'like', "%{$keyword}%");
            });
        }

        if (!empty($params['user_id'])) {
            $query->where('user_id', (int) $params['user_id']);
        }

        if (!empty($params['method'])) {
            $query->where('method', strtoupper($params['method']));
        }

        if (!empty($params['start_time'])) {
            $query->where('create_time', '>=', $params['start_time'] . ' 00:00:00');
        }

        if (!empty($params['end_time'])) {
            $query->where('create_time', '<=', $params['end_time'] . ' 23:59:59');
        }

        return $query->order('id', 'desc');
    }
}

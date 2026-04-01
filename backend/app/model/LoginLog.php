<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 登录日志模型
 */
class LoginLog extends Model
{
    protected $name = 'login_log';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'login_time';

    protected $updateTime = false;

    /**
     * 按条件搜索登录日志
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db();

        if (!empty($params['keyword'])) {
            $query->where('username', 'like', '%' . $params['keyword'] . '%');
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (!empty($params['ip'])) {
            $query->where('ip', $params['ip']);
        }

        if (!empty($params['start_time'])) {
            $query->where('login_time', '>=', $params['start_time'] . ' 00:00:00');
        }

        if (!empty($params['end_time'])) {
            $query->where('login_time', '<=', $params['end_time'] . ' 23:59:59');
        }

        return $query->order('id', 'desc');
    }
}

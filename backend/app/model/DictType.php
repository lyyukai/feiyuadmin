<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 数据字典类型模型
 */
class DictType extends Model
{
    protected $name = 'dict_type';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    /**
     * 状态获取器：1=正常, 0=禁用
     */
    public function getStatusTextAttr($value, $data): string
    {
        return isset($data['status']) && $data['status'] == 1 ? '正常' : '禁用';
    }

    /**
     * 按条件搜索字典类型
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db();

        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->whereOr('name', 'like', '%' . $params['keyword'] . '%')
                  ->whereOr('type', 'like', '%' . $params['keyword'] . '%');
            });
        }

        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int) $params['status']);
        }

        return $query->order('id', 'asc');
    }
}

<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 数据字典数据模型
 */
class DictData extends Model
{
    protected $name = 'dict_data';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    /**
     * 状态获取器
     */
    public function getStatusTextAttr($value, $data): string
    {
        return isset($data['status']) && $data['status'] == 1 ? '正常' : '禁用';
    }

    /**
     * 按字典类型获取数据列表
     */
    public static function getByType(string $type): array
    {
        return self::where('dict_type', $type)
            ->where('status', 1)
            ->order('sort', 'asc')
            ->select()->toArray();
    }

    /**
     * 按条件搜索字典数据
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db();

        if (!empty($params['type'])) {
            $query->where('dict_type', $params['type']);
        }

        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->whereOr('label', 'like', '%' . $params['keyword'] . '%')
                  ->whereOr('value', 'like', '%' . $params['keyword'] . '%');
            });
        }

        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int) $params['status']);
        }

        return $query->order('sort', 'asc')->order('id', 'asc');
    }
}

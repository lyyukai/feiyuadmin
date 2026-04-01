<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 参数配置模型
 */
class Config extends Model
{
    protected $name = 'config';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    /**
     * 自动获取器：解密value（支持加密存储的配置）
     */
    public function getValueAttr($value, $data): mixed
    {
        // 如果配置标记为加密，则解密
        if (!empty($data['is_encrypt']) && $value) {
            return decrypt($value);
        }
        return $value;
    }

    /**
     * 修改器：加密value
     */
    public function setValueAttr($value, $data): mixed
    {
        if (!empty($data['is_encrypt']) && $value) {
            return encrypt($value);
        }
        return $value;
    }

    /**
     * 按分组获取配置
     */
    public static function getByGroup(string $group): array
    {
        $configs = self::where('group', $group)->order('sort', 'asc')->select();
        $result = [];
        foreach ($configs as $config) {
            $result[$config['key']] = $config['value'];
        }
        return $result;
    }

    /**
     * 按条件搜索配置
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db();

        if (!empty($params['group'])) {
            $query->where('group', $params['group']);
        }

        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->whereOr('name', 'like', '%' . $params['keyword'] . '%')
                  ->whereOr('key', 'like', '%' . $params['keyword'] . '%');
            });
        }

        return $query->order('sort', 'asc')->order('id', 'asc');
    }
}

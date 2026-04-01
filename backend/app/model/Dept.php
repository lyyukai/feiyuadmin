<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 部门模型
 */
class Dept extends Model
{
    use SoftDelete;

    protected $name = 'dept';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['delete_time'];

    protected $type = [
        'pid'   => 'integer',
        'sort'  => 'integer',
        'status'=> 'integer',
    ];

    /**
     * 获取树形部门
     */
    public static function getTree(array $where = []): array
    {
        $query = self::whereNull('delete_time');
        if (!empty($where)) {
            $query->where($where);
        }
        $list = $query->order('sort', 'asc')->select()->toArray();

        return self::buildTree($list, 0);
    }

    /**
     * 递归构建树形结构
     */
    public static function buildTree(array $list, int $pid): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ((int) $item['pid'] === $pid) {
                $children = self::buildTree($list, (int) $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                } else {
                    $item['children'] = [];
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * 检查是否有子部门
     */
    public static function hasChildren(int $id): bool
    {
        return self::where('pid', $id)->whereNull('delete_time')->find() !== null;
    }

    /**
     * 检查部门下是否有用户
     */
    public static function hasUsers(int $id): bool
    {
        return User::where('dept_id', $id)->whereNull('delete_time')->find() !== null;
    }

    /**
     * 父级部门关联
     */
    public function parent(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(self::class, 'pid', 'id');
    }

    /**
     * 子部门关联
     */
    public function children(): \think\model\relation\HasMany
    {
        return $this->hasMany(self::class, 'pid', 'id');
    }

    /**
     * 部门下用户
     */
    public function users(): \think\model\relation\HasMany
    {
        return $this->hasMany(User::class, 'dept_id', 'id');
    }
}

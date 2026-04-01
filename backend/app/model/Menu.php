<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 菜单模型
 */
class Menu extends Model
{
    use SoftDelete;

    protected $name = 'menu';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['delete_time'];

    protected $type = [
        'pid'       => 'integer',
        'is_hidden' => 'integer',
        'is_full'   => 'integer',
        'is_cache'  => 'integer',
        'sort'      => 'integer',
        'status'    => 'integer',
    ];

    // 菜单类型常量
    const TYPE_MENU    = 'menu';
    const TYPE_IFRAME  = 'iframe';
    const TYPE_LINK    = 'link';
    const TYPE_BUTTON  = 'button';

    /**
     * 获取树形菜单
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
     * 检查是否有子菜单
     */
    public static function hasChildren(int $id): bool
    {
        return self::where('pid', $id)->whereNull('delete_time')->find() !== null;
    }

    /**
     * 获取所有子菜单ID（包括自己）
     */
    public static function getChildIds(int $id): array
    {
        $ids = [$id];
        $children = self::where('pid', $id)->whereNull('delete_time')->column('id');
        foreach ($children as $childId) {
            $ids = array_merge($ids, self::getChildIds($childId));
        }
        return array_unique($ids);
    }

    /**
     * 父级菜单关联
     */
    public function parent(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(self::class, 'pid', 'id');
    }

    /**
     * 子菜单关联
     */
    public function children(): \think\model\relation\HasMany
    {
        return $this->hasMany(self::class, 'pid', 'id');
    }
}

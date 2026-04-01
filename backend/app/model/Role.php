<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 角色模型
 */
class Role extends Model
{
    use SoftDelete;

    protected $name = 'role';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['delete_time'];

    protected $type = [
        'status' => 'integer',
        'sort'   => 'integer',
    ];

    /**
     * 菜单关联 (多对多)
     */
    public function menus(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu', 'menu_id', 'role_id');
    }

    /**
     * 获取菜单ID数组
     */
    public function getMenuIds(): array
    {
        return $this->menus()->column('menu.id');
    }

    /**
     * 保存菜单权限
     */
    public function saveMenus(array $menuIds): bool
    {
        // 删除旧关联
        db('role_menu')->where('role_id', $this->id)->delete();

        // 批量插入新关联
        if (!empty($menuIds)) {
            $data = array_map(fn($menuId) => [
                'role_id' => $this->id,
                'menu_id' => (int) $menuId,
            ], $menuIds);
            db('role_menu')->insertAll($data);
        }

        return true;
    }

    /**
     * 用户关联 (多对多)
     */
    public function users(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role', 'user_id', 'role_id');
    }
}

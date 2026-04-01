<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 管理员模型
 */
class User extends Model
{
    use SoftDelete;

    protected $name = 'user';
    protected $deleteTime = 'delete_time';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 隐藏字段
    protected $hidden = ['password', 'delete_time'];

    // 类型转换
    protected $type = [
        'dept_id' => 'integer',
        'post_id' => 'integer',
        'status'  => 'integer',
    ];

    /**
     * 密码自动加密
     */
    public function setPasswordAttr(mixed $value): string
    {
        return password_hash((string) $value, PASSWORD_DEFAULT);
    }

    /**
     * 部门关联
     */
    public function dept(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Dept::class, 'dept_id', 'id');
    }

    /**
     * 岗位关联
     */
    public function post(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    /**
     * 角色关联 (多对多)
     */
    public function roles(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'role_id', 'user_id');
    }

    /**
     * 获取角色ID数组
     */
    public function getRoleIds(): array
    {
        return $this->roles()->column('sys_role.id');
    }

    /**
     * 判断是否为超级管理员
     */
    public function isSuperAdmin(): bool
    {
        // 通过角色code判断，而非硬编码ID
        return $this->roles()->where('code', 'super_admin')->count() > 0;
    }

    /**
     * 获取权限标识列表
     */
    public function getPermissions(): array
    {
        // 超级管理员拥有所有权限
        if ($this->isSuperAdmin()) {
            return ['*'];
        }

        $roles = $this->roles;
        if ($roles->isEmpty()) {
            return [];
        }

        $menuIds = [];
        foreach ($roles as $role) {
            $menuIds = array_merge($menuIds, $role->menus()->column('sys_menu.id'));
        }
        $menuIds = array_unique($menuIds);

        if (empty($menuIds)) {
            return [];
        }

        // 获取按钮权限
        $permissions = Menu::whereIn('id', $menuIds)
            ->where('menu_type', 'button')
            ->whereNotNull('permission')
            ->where('permission', '<>', '')
            ->column('permission');

        return array_unique($permissions);
    }

    /**
     * 获取角色标识列表
     */
    public function getRoleCodes(): array
    {
        return $this->roles()->column('code');
    }

    /**
     * 获取用户菜单列表
     */
    public function getMenus(): array
    {
        // 超级管理员拥有所有菜单
        if ($this->id == 1) {
            return Menu::whereNull('delete_time')
                ->where('status', 1)
                ->order('sort', 'asc')
                ->select()
                ->toArray();
        }

        $roles = $this->roles;
        if ($roles->isEmpty()) {
            return [];
        }

        $menuIds = [];
        foreach ($roles as $role) {
            $menuIds = array_merge($menuIds, $role->menus()->column('sys_menu.id'));
        }
        $menuIds = array_unique(array_filter($menuIds));

        if (empty($menuIds)) {
            return [];
        }

        return Menu::whereIn('id', $menuIds)
            ->whereNull('delete_time')
            ->where('status', 1)
            ->order('sort', 'asc')
            ->select()
            ->toArray();
    }

    /**
     * 获取用户信息（用于登录后展示）
     */
    public function getInfo(): array
    {
        $data = $this->toArray();

        // 附加部门名称
        if ($this->dept_id) {
            $dept = Dept::find($this->dept_id);
            $data['dept'] = $dept ? $dept->name : '';
        } else {
            $data['dept'] = '';
        }

        // 附加岗位名称
        if ($this->post_id) {
            $post = Post::find($this->post_id);
            $data['post_name'] = $post ? $post->name : '';
        } else {
            $data['post_name'] = '';
        }

        // 附加角色标识
        $data['roles'] = $this->getRoleCodes();

        // 附加权限标识
        $data['permissions'] = $this->getPermissions();

        // 字段名统一：mobile -> phone
        $data['phone'] = $data['mobile'] ?? '';
        unset($data['mobile']);

        return $data;
    }

    /**
     * 验证密码
     */
    public static function verifyPassword(string $username, string $password): ?self
    {
        $user = self::where('username', $username)->find();
        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user->password)) {
            return null;
        }
        if ($user->status != 1) {
            return null;
        }
        return $user;
    }
}

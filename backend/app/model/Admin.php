<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 管理员模型
 */
class Admin extends Model
{
    use SoftDelete;

    protected $name = 'admin';
    protected $deleteTime = 'delete_time';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 隐藏字段
    protected $hidden = ['password', 'delete_time'];

    // 类型转换
    protected $type = [
        'tenant_id' => 'integer',
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
     * 租户关联
     */
    public function tenant(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * 验证密码
     */
    public static function verifyPassword(string $username, string $password): ?self
    {
        $admin = self::where('username', $username)->find();
        if (!$admin) {
            return null;
        }
        if (!password_verify($password, $admin->password)) {
            return null;
        }
        if ($admin->status != 1) {
            return null;
        }
        // 检查租户状态
        if ($admin->tenant_id) {
            $tenant = Tenant::find($admin->tenant_id);
            if (!$tenant || $tenant->status != 1) {
                return null;
            }
            // 检查租户是否过期
            if ($tenant->isExpired()) {
                return null;
            }
        }
        return $admin;
    }

    /**
     * 获取管理员信息（用于登录后展示）
     */
    public function getInfo(): array
    {
        $data = $this->toArray();

        // 附加部门名称
        if ($this->dept_id) {
            $dept = Dept::find($this->dept_id);
            $data['dept_name'] = $dept ? $dept->name : '';
        } else {
            $data['dept_name'] = '';
        }

        // 附加岗位名称
        if ($this->post_id) {
            $post = Post::find($this->post_id);
            $data['post_name'] = $post ? $post->name : '';
        } else {
            $data['post_name'] = '';
        }

        // 附加租户名称
        if ($this->tenant_id) {
            $tenant = Tenant::find($this->tenant_id);
            $data['tenant_name'] = $tenant ? $tenant->name : '';
        } else {
            $data['tenant_name'] = '';
        }

        // 字段名统一
        $data['phone'] = $data['mobile'] ?? '';
        unset($data['mobile']);

        return $data;
    }
}

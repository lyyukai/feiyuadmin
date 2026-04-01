<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 租户模型
 */
class Tenant extends Model
{
    use SoftDelete;

    protected $name = 'tenant';
    protected $deleteTime = 'delete_time';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 隐藏字段
    protected $hidden = ['delete_time', 'db_config'];

    // 类型转换
    protected $type = [
        'tenant_type' => 'integer',
        'package_id' => 'integer',
        'status' => 'integer',
        'db_config' => 'json',
    ];

    // 状态常量
    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    // 隔离模式常量
    const TYPE_SHARED = 1;      // 共享表（推荐MySQL使用）
    const TYPE_DATABASE = 2;    // 独立数据库
    const TYPE_SCHEMA = 3;      // 独立Schema（PostgreSQL）

    /**
     * 获取隔离模式名称
     */
    public function getTypeNameAttr(): string
    {
        $names = [
            self::TYPE_SHARED => '共享表',
            self::TYPE_DATABASE => '独立数据库',
            self::TYPE_SCHEMA => '独立Schema',
        ];
        return $names[$this->tenant_type] ?? '未知';
    }

    /**
     * 获取状态名称
     */
    public function getStatusNameAttr(): string
    {
        return $this->status === self::STATUS_ENABLE ? '正常' : '禁用';
    }

    /**
     * 套餐关联
     */
    public function package(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(TenantPackage::class, 'package_id', 'id');
    }

    /**
     * 检查是否过期
     */
    public function isExpired(): bool
    {
        if (!$this->expire_time) {
            return false;
        }
        return strtotime($this->expire_time) < time();
    }

    /**
     * 获取用户ID列表（如果是共享表模式）
     */
    public static function getTenantUserIds(int $tenantId): array
    {
        return User::where('tenant_id', $tenantId)->column('id');
    }
}

<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 租户套餐模型
 */
class TenantPackage extends Model
{
    use SoftDelete;

    protected $name = 'tenant_package';
    protected $deleteTime = 'delete_time';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 类型转换
    protected $type = [
        'price' => 'float',
        'duration' => 'integer',
        'user_limit' => 'integer',
        'storage_limit' => 'integer',
        'api_limit' => 'integer',
        'status' => 'integer',
        'sort' => 'integer',
    ];

    /**
     * 获取状态名称
     */
    public function getStatusNameAttr(): string
    {
        return $this->status === 1 ? '正常' : '禁用';
    }

    /**
     * 格式化价格
     */
    public function getPriceTextAttr(): string
    {
        if ($this->price <= 0) {
            return '免费';
        }
        return '¥' . number_format($this->price, 2);
    }

    /**
     * 格式化存储限制
     */
    public function getStorageTextAttr(): string
    {
        if ($this->storage_limit <= 0) {
            return '无限制';
        }
        if ($this->storage_limit >= 1024) {
            return round($this->storage_limit / 1024, 1) . ' GB';
        }
        return $this->storage_limit . ' MB';
    }
}

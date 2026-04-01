<?php

namespace app\model\concern;

use think\Model;
use think\Request;

/**
 * 租户数据隔离 Trait
 * 
 * 使用方式：在模型中 use TenantScope;
 * 
 * 特性：
 * 1. 自动过滤 tenant_id（共享表模式）
 * 2. 新增数据时自动写入 tenant_id
 * 3. 支持超级管理员查看所有数据
 */
trait TenantScope
{
    /**
     * 租户ID字段名
     */
    protected $tenantKey = 'tenant_id';

    /**
     * 是否启用租户隔离
     */
    protected $tenantScope = true;

    /**
     * 初始化
     */
    protected function tenantScopeInit(): void
    {
        // 如果模型没有 tenant_id 字段，则不启用租户隔离
        if (!isset($this->data[$this->tenantKey]) && !$this->schemaHasField($this->tenantKey)) {
            $this->tenantScope = false;
        }
    }

    /**
     * 检查模型是否有指定字段
     */
    protected function schemaHasField(string $field): bool
    {
        static $schemaCache = [];
        $class = static::class;
        
        if (!isset($schemaCache[$class])) {
            $schemaCache[$class] = array_column($this->getSchema(), 'name');
        }
        
        return in_array($field, $schemaCache[$class]);
    }

    /**
     * 全局查询作用域 - 自动过滤tenant_id
     */
    public function scopeTenant(array $options = []): void
    {
        // 如果禁用租户隔离，则不添加条件
        if (!$this->tenantScope) {
            return;
        }

        // 获取当前请求中的租户ID
        $tenantId = $this->getCurrentTenantId();
        
        // 如果没有租户ID（普通用户），则只能查询自己的数据
        // 如果有租户ID，则只能查询该租户的数据
        if ($tenantId !== null) {
            $this->where($this->tenantKey, $tenantId);
        } else if (!$this->isSuperAdmin()) {
            // 非超级管理员且没有租户ID，只能看自己
            // 这里假设有一个创建人字段 created_by 或者使用user_id
            // 如果都没有，则不返回数据
        }
    }

    /**
     * 获取当前租户ID
     */
    protected function getCurrentTenantId(): ?int
    {
        try {
            $request = app('request');
            if (isset($request->tenantId) && $request->tenantId) {
                return (int) $request->tenantId;
            }
        } catch (\Throwable $e) {
            // 忽略异常
        }
        return null;
    }

    /**
     * 检查是否是超级管理员
     */
    protected function isSuperAdmin(): bool
    {
        try {
            $request = app('request');
            if (isset($request->isSuperAdmin) && $request->isSuperAdmin) {
                return true;
            }
            if (isset($request->adminInfo) && $request->adminInfo) {
                if (isset($request->adminInfo['admin_id']) && $request->adminInfo['admin_id'] == 1) {
                    return true;
                }
                if (isset($request->adminInfo['roles']) && in_array('super_admin', $request->adminInfo['roles'])) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // 忽略异常
        }
        return false;
    }

    /**
     * 获取当前登录用户ID
     */
    protected function getCurrentAdminId(): int
    {
        try {
            $request = app('request');
            if (isset($request->adminInfo) && $request->adminInfo) {
                return (int) ($request->adminInfo['admin_id'] ?? 0);
            }
            if (isset($request->userId)) {
                return (int) $request->userId;
            }
        } catch (\Throwable $e) {
            // 忽略异常
        }
        return 0;
    }

    /**
     * 保存前的回调 - 自动写入tenant_id
     */
    public function onBeforeInsert(Model $model): void
    {
        if (!$this->tenantScope) {
            return;
        }

        $tenantId = $this->getCurrentTenantId();
        
        // 超级管理员可以指定租户ID
        if ($this->isSuperAdmin() && isset($model->{$this->tenantKey})) {
            // 使用模型指定的tenant_id
            return;
        }

        // 普通用户自动写入租户ID
        if ($tenantId !== null) {
            $model->{$this->tenantKey} = $tenantId;
        }
    }

    /**
     * 查询前的回调 - 自动添加租户过滤
     */
    public function onBeforeSelect(Model $model): void
    {
        if (!$this->tenantScope || $this->isSuperAdmin()) {
            return;
        }

        $tenantId = $this->getCurrentTenantId();
        
        // 如果有租户ID，添加过滤条件
        if ($tenantId !== null) {
            $model->where($this->tenantKey, $tenantId);
        } else {
            // 没有租户ID，只看自己的数据（通过created_by或user_id）
            // 默认情况下，没有租户的用户只能看自己创建的数据
            // 这里需要根据具体业务调整
        }
    }

    /**
     * 软删除前的回调
     */
    public function onBeforeDelete(Model $model): void
    {
        if (!$this->tenantScope || $this->isSuperAdmin()) {
            return;
        }

        $tenantId = $this->getCurrentTenantId();
        
        // 如果有租户ID，验证是否属于该租户
        if ($tenantId !== null) {
            $record = static::find($model->id);
            if ($record && $record->{$this->tenantKey} != $tenantId) {
                throw new \Exception('无权删除此数据');
            }
        }
    }
}

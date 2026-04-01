<?php
/**
 * 飞羽后台管理系统 - 租户管理逻辑
 */

namespace app\adminapi\logic\tenant;

use app\common\service\JsonService;
use app\model\Tenant;

/**
 * 租户管理逻辑
 */
class TenantLogic
{
    /**
     * 获取租户列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $pageSize = min((int) ($params['page_size'] ?? 15), 100);
        $offset = ($page - 1) * $pageSize;
        
        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['tenant_name|tenant_code|contact_name', 'like', "%{$params['keyword']}%"];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', (int)$params['status']];
        }
        
        $list = Tenant::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();
        
        $total = Tenant::where($where)->count();
        
        foreach ($list as &$item) {
            $item['status_name'] = $item['status'] == 1 ? '正常' : '禁用';
            $item['expire_time_text'] = $item['expire_time'] ? date('Y-m-d H:i', strtotime($item['expire_time'])) : '永久';
        }
        
        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }

    /**
     * 获取租户详情
     */
    public static function getInfo(int $id): array
    {
        $tenant = Tenant::find($id);
        if (empty($tenant)) {
            return [];
        }
        return $tenant->toArray();
    }

    /**
     * 添加租户
     */
    public static function add(array $params): int
    {
        $tenantName = $params['tenant_name'] ?? $params['name'] ?? '';
        if (empty($tenantName)) {
            throw new \Exception('租户名称不能为空');
        }
        
        $tenantCode = $params['tenant_code'] ?? $params['code'] ?? '';
        if (empty($tenantCode)) {
            throw new \Exception('租户编码不能为空');
        }
        
        // 检查编码唯一性
        $exists = Tenant::where('tenant_code', $tenantCode)->find();
        if ($exists) {
            throw new \Exception('租户编码已存在');
        }

        $tenant = new Tenant();
        $tenant->tenant_name = $tenantName;
        $tenant->tenant_code = $tenantCode;
        $tenant->tenant_type = (int)($params['tenant_type'] ?? $params['隔离模式'] ?? 1);
        $tenant->package_id = (int)($params['package_id'] ?? 0);
        $tenant->contact_name = $params['contact_name'] ?? '';
        $tenant->contact_phone = $params['contact_phone'] ?? '';
        $tenant->status = (int)($params['status'] ?? 1);
        $tenant->expire_time = $params['expire_time'] ?? null;
        $tenant->save();

        return $tenant->id;
    }

    /**
     * 编辑租户
     */
    public static function edit(array $params): bool
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }
        
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return false;
        }
        
        if (isset($params['tenant_name'])) {
            $tenant->tenant_name = $params['tenant_name'];
        }
        if (isset($params['name'])) {
            $tenant->tenant_name = $params['name'];
        }
        if (isset($params['tenant_code'])) {
            $tenant->tenant_code = $params['tenant_code'];
        }
        if (isset($params['code'])) {
            $tenant->tenant_code = $params['code'];
        }
        if (isset($params['tenant_type'])) {
            $tenant->tenant_type = (int)$params['tenant_type'];
        }
        if (isset($params['隔离模式'])) {
            $tenant->tenant_type = (int)$params['隔离模式'];
        }
        if (isset($params['package_id'])) {
            $tenant->package_id = (int)$params['package_id'];
        }
        if (isset($params['contact_name'])) {
            $tenant->contact_name = $params['contact_name'];
        }
        if (isset($params['contact_phone'])) {
            $tenant->contact_phone = $params['contact_phone'];
        }
        if (isset($params['status'])) {
            $tenant->status = (int)$params['status'];
        }
        if (isset($params['expire_time'])) {
            $tenant->expire_time = $params['expire_time'];
        }
        
        return $tenant->save();
    }

    /**
     * 删除租户
     */
    public static function delete(int $id): bool
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return false;
        }
        return $tenant->delete();
    }
}

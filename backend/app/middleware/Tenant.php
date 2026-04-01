<?php

namespace app\middleware;

use think\Request;
use think\Response;
use app\service\TokenService;
use app\model\Tenant as TenantModel;

/**
 * 租户中间件
 * 功能：
 * 1. 自动从Token/请求中获取当前租户ID
 * 2. 自动注入tenant_id到请求
 * 3. 支持租户数据隔离
 */
class Tenant
{
    /**
     * 免租户验证的路径
     */
    protected array $except = [
        'api/login',
        'api/logout',
        'api/captcha',
    ];

    public function handle(Request $request, \Closure $next): Response
    {
        // OPTIONS请求直接放行
        if ($request->method(true) === 'OPTIONS') {
            return response('', 200);
        }

        // 检查是否在白名单
        $path = $request->pathinfo();
        if ($this->isExcept($path)) {
            return $next($request);
        }

        // 从请求中获取租户ID
        $tenantId = $this->getTenantId($request);

        // 如果是超级管理员，可以指定租户ID（用于后台管理）
        $isSuperAdmin = $this->isSuperAdmin($request);

        // 注入租户信息到请求
        $request->tenantId = $tenantId;
        $request->isSuperAdmin = $isSuperAdmin;

        // 租户模式下，验证租户状态
        if ($tenantId && !$isSuperAdmin) {
            $tenant = TenantModel::find($tenantId);
            if (!$tenant) {
                return json(['code' => 403, 'msg' => '租户不存在', 'data' => []]);
            }
            if ($tenant->status != 1) {
                return json(['code' => 403, 'msg' => '租户已被禁用', 'data' => []]);
            }
            if ($tenant->isExpired()) {
                return json(['code' => 403, 'msg' => '租户已过期', 'data' => []]);
            }
            $request->tenantInfo = $tenant->toArray();
        }

        return $next($request);
    }

    /**
     * 获取租户ID
     */
    protected function getTenantId(Request $request): ?int
    {
        // 1. 优先从请求header中获取（前端指定租户）
        $tenantId = $request->header('X-Tenant-Id', '');
        if ($tenantId) {
            return (int) $tenantId;
        }

        // 2. 从请求参数中获取
        $tenantId = $request->param('tenant_id', '');
        if ($tenantId) {
            return (int) $tenantId;
        }

        // 3. 从Token中解析（如果实现了多租户Token）
        // 这里可以扩展TokenService来支持租户信息
        $token = $this->getToken($request);
        if ($token) {
            // 解析Token获取租户ID（如果Token中包含）
            // 目前暂时不支持，留作扩展
        }

        return null;
    }

    /**
     * 检查是否是超级管理员
     */
    protected function isSuperAdmin(Request $request): bool
    {
        // 从request中获取adminInfo（由Auth中间件注入）
        if (isset($request->adminInfo) && $request->adminInfo) {
            // 超级管理员ID为1，或者有super_admin角色
            if (isset($request->adminInfo['admin_id']) && $request->adminInfo['admin_id'] == 1) {
                return true;
            }
            // 检查角色
            if (isset($request->adminInfo['roles']) && in_array('super_admin', $request->adminInfo['roles'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查是否在白名单
     */
    protected function isExcept(string $path): bool
    {
        foreach ($this->except as $pattern) {
            if (strpos($path, $pattern) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 从请求中获取Token
     */
    protected function getToken(Request $request): ?string
    {
        $auth = $request->header('Authorization', '');
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
        return null;
    }
}

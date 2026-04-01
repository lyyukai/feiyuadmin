<?php
/**
 * 飞羽后台管理系统 - 认证中间件
 */

declare(strict_types=1);

namespace app\adminapi\http\middleware;

use app\common\service\JsonService;
use app\service\TokenService;

/**
 * 认证中间件
 * Class AuthMiddleware
 * @package app\adminapi\http\middleware
 */
class AuthMiddleware
{
    /**
     * 认证
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 检查是否免登录（通过控制器上的注解或配置）
        // 这里简化处理，允许登录接口通过
        $path = $request->pathinfo();
        if (in_array($path, ['api/login', 'api/logout'])) {
            return $next($request);
        }

        // 获取 Token
        $token = $this->getToken($request);
        if (empty($token)) {
            return JsonService::fail('请先登录', [], 401);
        }

        // 验证 Token
        $adminId = TokenService::verify($token);
        if (empty($adminId)) {
            return JsonService::fail('登录已过期，请重新登录', [], 401);
        }

        // 获取管理员信息
        $adminInfo = $this->getAdminInfo($adminId);
        if (empty($adminInfo)) {
            return JsonService::fail('用户不存在', [], 401);
        }

        // 注入到请求
        $request->adminInfo = $adminInfo;
        $request->adminId = $adminId;

        return $next($request);
    }

    /**
     * 获取 Token
     * @param $request
     * @return string|null
     */
    protected function getToken($request): ?string
    {
        $auth = $request->header('Authorization', '');
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
        return null;
    }

    /**
     * 获取管理员信息
     * @param int $adminId
     * @return array|null
     */
    protected function getAdminInfo(int $adminId): ?array
    {
        $admin = \app\model\User::find($adminId);
        if (empty($admin)) {
            return null;
        }
        return [
            'admin_id' => $admin->id,
            'username' => $admin->username,
            'nickname' => $admin->nickname,
        ];
    }
}

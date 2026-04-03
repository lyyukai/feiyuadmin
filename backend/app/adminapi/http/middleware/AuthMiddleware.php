<?php
/**
 * 飞鱼后台管理系统 - 认证中间件
 * 
 * 支持控制器声明 notNeedLogin 属性来跳过认证
 * 示例:
 *   class LoginController extends BaseAdminController {
 *       protected array $notNeedLogin = ['account', 'logout'];
 *   }
 */

declare(strict_types=1);

namespace app\adminapi\http\middleware;

use app\common\service\JsonService;
use app\service\TokenService;
use think\exception\HttpException;

/**
 * 认证中间件
 * Class AuthMiddleware
 * @package app\adminapi\http\middleware
 */
class AuthMiddleware
{
    public function handle($request, \Closure $next)
    {
        // 从 InitMiddleware 绑定的控制器实例获取当前执行的 action
        $controller = $request->controllerObject ?? null;
        $action = $this->getCurrentAction($request);

        // 检查是否免登录
        if ($controller && property_exists($controller, 'notNeedLogin')) {
            $notNeedLogin = $controller->notNeedLogin;
            if (is_array($notNeedLogin) && in_array($action, $notNeedLogin)) {
                return $next($request);
            }
        }

        // 同样检查控制器方法上的 @NotLogin 注解（可选扩展）

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

    protected function getCurrentAction($request): string
    {
        $path = trim($request->pathinfo(), '/');
        $segments = explode('/', $path);
        return $segments[2] ?? '';
    }

    protected function getToken($request): ?string
    {
        $auth = $request->header('Authorization', '');
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
        return null;
    }

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

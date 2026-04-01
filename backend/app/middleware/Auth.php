<?php

namespace app\middleware;

use think\Request;
use think\Response;
use app\service\TokenService;

/**
 * 认证中间件
 */
class Auth
{
    protected array $except = [
        'api/login',
        'api/logout',
    ];

    public function handle(Request $request, \Closure $next): Response
    {
        // OPTIONS请求直接放行
        if ($request->method(true) === 'OPTIONS') {
            return response('', 200);
        }

        // 检查是否在白名单
        $path = $request->pathinfo();
        if (in_array($path, $this->except)) {
            return $next($request);
        }

        // 获取Token
        $token = $this->getToken($request);
        if (!$token) {
            return json(['code' => 401, 'msg' => '未登录，请先登录', 'data' => []]);
        }

        // 验证Token
        $userId = TokenService::verify($token);
        if (!$userId) {
            return json(['code' => 401, 'msg' => 'Token已过期，请重新登录', 'data' => []]);
        }

        // 将userId注入到request
        $request->userId = $userId;
        $request->userInfo = ['id' => $userId];

        return $next($request);
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

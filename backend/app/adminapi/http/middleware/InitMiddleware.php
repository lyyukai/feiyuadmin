<?php
declare(strict_types=1);

namespace app\adminapi\http\middleware;

use think\exception\ClassNotFoundException;
use think\exception\HttpException;

/**
 * 初始化中间件 - 自动路由解析
 * 
 * URL 规则: /{group}/{controller}/{action}
 * 示例:
 *   /adminapi/user/lists    → app\adminapi\controller\user\UserController::lists()
 *   /adminapi/auth/login     → app\adminapi\controller\auth\LoginController::login()
 *   /adminapi/captcha/generate → app\adminapi\controller\captcha\CaptchaController::generate()
 */
class InitMiddleware
{
    public function handle($request, \Closure $next)
    {
        $path = trim($request->pathinfo(), '/');
        $segments = explode('/', $path);

        // 必须至少是 group/controller/action 三段
        if (count($segments) < 3) {
            throw new HttpException(404, 'Route not found: ' . $path);
        }

        $group = $segments[0];       // e.g. adminapi
        $controller = $segments[1];   // e.g. user
        $action = $segments[2];       // e.g. lists
        $pathExtra = array_slice($segments, 3); // 额外路径参数

        // 构建控制器类名
        // user → UserController, user_group → UserGroupController
        $controllerClass = $this->buildControllerClass($group, $controller);

        try {
            $controllerInstance = invoke($controllerClass);
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'Controller not found: ' . $controllerClass);
        }

        // 检查方法是否存在
        if (!method_exists($controllerInstance, $action)) {
            throw new HttpException(404, "Method not found: {$controllerClass}::{$action}()");
        }

        // 绑定控制器实例到请求，后续可获取
        $request->controllerObject = $controllerInstance;

        return $next($request);
    }

    /**
     * 构建控制器类名
     * user → app\adminapi\controller\user\UserController
     */
    protected function buildControllerClass(string $group, string $controller): string
    {
        // 控制器名转 PascalCase: user_group → UserGroup
        $controllerName = '';
        $parts = explode('_', $controller);
        foreach ($parts as $part) {
            $controllerName .= ucfirst(strtolower($part));
        }

        return '\\app\\' . $group . '\\controller\\' . $controllerName . 'Controller';
    }
}

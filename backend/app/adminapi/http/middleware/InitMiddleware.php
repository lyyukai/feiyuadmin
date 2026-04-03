<?php
declare(strict_types=1);

namespace app\adminapi\http\middleware;

use think\Request;
use think\Response;
use think\exception\HttpException;

/**
 * 初始化中间件 - 控制器自动路由解析
 * 
 * URL 规则: /{group}/{controller}/{action}
 * 示例:
 *   /adminapi/user/lists    → app\adminapi\controller\user\UserController → lists()
 *   /adminapi/auth/login     → app\adminapi\controller\auth\LoginController → login()
 */
class InitMiddleware
{
    public function handle($request, \Closure $next): Response
    {
        $path = trim($request->pathinfo(), '/');
        $segments = explode('/', $path);

        if (count($segments) < 3) {
            throw new HttpException(404, 'Route not found: ' . $path);
        }

        $group = $segments[0];       // e.g. adminapi
        $controller = $segments[1];  // e.g. user
        $action = $segments[2];      // e.g. lists

        // 构建控制器类名: user → app\adminapi\controller\user\UserController
        $controllerClass = $this->buildControllerClass($group, $controller);

        try {
            $controllerInstance = invoke($controllerClass);
        } catch (\Throwable $e) {
            throw new HttpException(404, 'Controller not found: ' . $controllerClass);
        }

        if (!method_exists($controllerInstance, $action)) {
            throw new HttpException(404, "Method not found: {$controllerClass}::{$action}()");
        }

        // 注入控制器实例到请求
        $request->controllerObject = $controllerInstance;
        $request->controllerClass = $controllerClass;
        $request->controllerAction = $action;

        // 继续执行下一个中间件，最终由 ThinkPHP 的调度器调用控制器方法
        return $next($request);
    }

    protected function buildControllerClass(string $group, string $controller): string
    {
        $controllerName = '';
        $parts = explode('_', $controller);
        foreach ($parts as $part) {
            $controllerName .= ucfirst(strtolower($part));
        }
        return '\\app\\' . $group . '\\controller\\' . $controllerName . 'Controller';
    }
}

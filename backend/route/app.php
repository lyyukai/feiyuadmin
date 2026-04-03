<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 策略：
 * 1. 公开接口（captcha, auth）显式定义
 * 2. adminapi 下所有接口自动路由（使用 admin/ 子目录）
 */

use think\facade\Route;

// ============================================================
// SPA 前端入口
// ============================================================
Route::rule('admin/:any', function () {
    return view(app()->getRootPath() . 'public/admin/index.html');
})->pattern(['any' => '\w+']);

Route::rule('mobile/:any', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
})->pattern(['any' => '\w+']);

Route::rule('pc/:any', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
})->pattern(['any' => '\w+']);

// ============================================================
// 自动路由解析（处理 adminapi/*）
// URL: /adminapi/{controller}/{action} → app\adminapi\controller\admin\{Controller}
// ============================================================
$adminApiRoute = function ($controller, $action) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    // adminapi 下的控制器主要在 admin/ 子目录
    $controllerClass = '\\app\\adminapi\\controller\\admin\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
        // 尝试根目录
        $controllerClass = '\\app\\adminapi\\controller\\' . $controllerName . 'Controller';
        try {
            $ctrl = invoke($controllerClass);
        } catch (\Throwable $e2) {
            throw new \think\exception\HttpException(404, 'Controller not found: ' . $controllerClass);
        }
    }
    
    if (!method_exists($ctrl, $action)) {
        throw new \think\exception\HttpException(404, "Method not found: {$controllerClass}::{$action}()");
    }
    
    request()->controllerObject = $ctrl;
    request()->controllerClass = $controllerClass;
    request()->controllerAction = $action;
    
    return invoke([$ctrl, $action]);
};

// ============================================================
// 公开接口（免登录）
// ============================================================
Route::get('adminapi/captcha/generate', [\app\adminapi\controller\captcha\CaptchaController::class, 'generate']);
Route::post('adminapi/captcha/verify', [\app\adminapi\controller\captcha\CaptchaController::class, 'verify']);
Route::post('adminapi/login/account', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/logout', [\app\adminapi\controller\auth\LoginController::class, 'logout']);

// ============================================================
// adminapi 其他接口自动路由
// ============================================================
Route::any('adminapi/:controller/:action', $adminApiRoute)
    ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
    ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

// ============================================================
// pcapi 自动路由
// ============================================================
Route::any('pcapi/:controller/:action', function ($controller, $action) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerClass = '\\app\\adminapi\\controller\\pc\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
        throw new \think\exception\HttpException(404, 'Controller not found: ' . $controllerClass);
    }
    
    if (!method_exists($ctrl, $action)) {
        throw new \think\exception\HttpException(404, "Method not found: {$controllerClass}::{$action}()");
    }
    
    request()->controllerObject = $ctrl;
    request()->controllerClass = $controllerClass;
    request()->controllerAction = $action;
    
    return invoke([$ctrl, $action]);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
  ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

// ============================================================
// mobileapi 自动路由
// ============================================================
Route::any('mobileapi/:controller/:action', function ($controller, $action) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerClass = '\\app\\adminapi\\controller\\mobile\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
        throw new \think\exception\HttpException(404, 'Controller not found: ' . $controllerClass);
    }
    
    if (!method_exists($ctrl, $action)) {
        throw new \think\exception\HttpException(404, "Method not found: {$controllerClass}::{$action}()");
    }
    
    request()->controllerObject = $ctrl;
    request()->controllerClass = $controllerClass;
    request()->controllerAction = $action;
    
    return invoke([$ctrl, $action]);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
  ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

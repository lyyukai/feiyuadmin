<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 策略：显式定义公开接口 + 自动路由处理其他接口
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
// 公开接口（免登录）
// ============================================================
Route::get('adminapi/captcha/generate', [\app\adminapi\controller\captcha\CaptchaController::class, 'generate']);
Route::post('adminapi/captcha/verify', [\app\adminapi\controller\captcha\CaptchaController::class, 'verify']);
Route::post('adminapi/login/account', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/logout', [\app\adminapi\controller\auth\LoginController::class, 'logout']);

// ============================================================
// adminapi 自动路由
// ============================================================
// 3段: /adminapi/controller/action
Route::any('adminapi/:controller/:action', function ($controller, $action) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerClass = '\\app\\adminapi\\controller\\admin\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
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
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// 2段: /adminapi/controller (action默认index)
Route::any('adminapi/:controller', function ($controller) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerClass = '\\app\\adminapi\\controller\\admin\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
        $controllerClass = '\\app\\adminapi\\controller\\' . $controllerName . 'Controller';
        try {
            $ctrl = invoke($controllerClass);
        } catch (\Throwable $e2) {
            throw new \think\exception\HttpException(404, 'Controller not found: ' . $controllerClass);
        }
    }
    
    request()->controllerObject = $ctrl;
    request()->controllerClass = $controllerClass;
    request()->controllerAction = 'index';
    
    return invoke([$ctrl, 'index']);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// pcapi 自动路由
// ============================================================
Route::any('pcapi/:controller[/:action]', function ($controller, $action = 'index') {
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
  ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]*']);

// ============================================================
// mobileapi 自动路由
// ============================================================
Route::any('mobileapi/:controller[/:action]', function ($controller, $action = 'index') {
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
  ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]*']);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 策略：显式定义公开接口 + 自动路由处理其他接口
 * 支持1-3段路径自动解析，snake_case转PascalCase
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

Route::rule('/', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
});

// ============================================================
// 公开接口（免登录）
// ============================================================
Route::get('adminapi/captcha/generate', [\app\adminapi\controller\captcha\CaptchaController::class, 'generate']);
Route::post('adminapi/captcha/verify', [\app\adminapi\controller\captcha\CaptchaController::class, 'verify']);
Route::post('adminapi/login', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/account', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/logout', [\app\adminapi\controller\auth\LoginController::class, 'logout']);

// ============================================================
// PC前台公开接口（免登录）
// 这些路由直接调用控制器方法，不经过 AuthMiddleware
// ============================================================
Route::post('pcapi/ai/nl2sql', function () {
    $ctrl = invoke(\app\adminapi\controller\pc\AiController::class);
    return invoke([$ctrl, 'nl2sql']);
});

Route::post('pcapi/ai/lowcode', function () {
    $ctrl = invoke(\app\adminapi\controller\pc\AiController::class);
    return invoke([$ctrl, 'lowcode']);
});

// ============================================================
// 路由辅助函数
// ============================================================

function snakeToPascal($str) {
    $result = '';
    $parts = explode('_', $str);
    foreach ($parts as $p) {
        $result .= ucfirst(strtolower($p));
    }
    return $result;
}

function invokeController($controllerClass, $action) {
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
}

function resolveAdminController($module, $controller, $action) {
    $moduleDir = strtolower($module);
    $controllerName = snakeToPascal($controller);
    $actionName = snakeToPascal($action);
    
    $fullControllerName = snakeToPascal($module) . $controllerName;
    $controllerClass = '\\app\\adminapi\\controller\\' . $moduleDir . '\\' . $fullControllerName . 'Controller';

    try {
        return invokeController($controllerClass, $actionName);
    } catch (\Throwable $e) {
        $controllerClass = '\\app\\adminapi\\controller\\' . $moduleDir . '\\' . $controllerName . 'Controller';
        return invokeController($controllerClass, $actionName);
    }
}

function resolveSimpleController($controller, $action) {
    $controllerName = snakeToPascal($controller);
    $actionName = snakeToPascal($action);
    $controllerLower = strtolower($controller);
    
    // 1. admin/{Controller}Controller
    $controllerClass = '\\app\\adminapi\\controller\\admin\\' . $controllerName . 'Controller';
    try {
        return invokeController($controllerClass, $actionName);
    } catch (\Throwable $e) {}
    
    // 2. {controller}Controller (根目录)
    $controllerClass = '\\app\\adminapi\\controller\\' . $controllerName . 'Controller';
    try {
        return invokeController($controllerClass, $actionName);
    } catch (\Throwable $e) {}
    
    // 3. {controller}/{Controller}Controller (子目录)
    $controllerClass = '\\app\\adminapi\\controller\\' . $controllerLower . '\\' . $controllerName . 'Controller';
    return invokeController($controllerClass, $actionName);
}

// ============================================================
// adminapi 自动路由
// ============================================================
Route::any('adminapi/:module/:controller/:action', function ($module, $controller, $action) {
    return resolveAdminController($module, $controller, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

Route::any('adminapi/:controller/:action', function ($controller, $action) {
    return resolveSimpleController($controller, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// pcapi 自动路由（需要认证）
// ============================================================
Route::any('pcapi/:controller/:action', function ($controller, $action) {
    return resolveSimpleController($controller, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// mobileapi 自动路由
// ============================================================
Route::any('mobileapi/:controller/:action', function ($controller, $action) {
    $controllerName = snakeToPascal($controller);
    $actionName = snakeToPascal($action);
    $controllerClass = '\\app\\adminapi\\controller\\mobile\\' . $controllerName . 'Controller';
    return invokeController($controllerClass, $actionName);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

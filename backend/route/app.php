<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 策略：显式定义公开接口 + 自动路由处理其他接口
 * 支持1-3段路径自动解析
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
Route::post('adminapi/login', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/account', [\app\adminapi\controller\auth\LoginController::class, 'account']);
Route::post('adminapi/login/logout', [\app\adminapi\controller\auth\LoginController::class, 'logout']);

// ============================================================
// 路由辅助函数
// ============================================================
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

// 解析adminapi的3段路径: /adminapi/module/controller/action
// 例如: wechat/account/lists → wechat/WechatAccountController::lists()
function resolveAdminController($module, $controller, $action) {
    $moduleDir = strtolower($module);
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    
    // 标准模块: wechat/account/lists → WechatAccount
    $fullControllerName = ucfirst(strtolower($module)) . $controllerName;
    $controllerClass = '\\app\\adminapi\\controller\\' . $moduleDir . '\\' . $fullControllerName . 'Controller';
    try {
        return invokeController($controllerClass, $action);
    } catch (\Throwable $e) {
        // 回退: generator/Generator
        $controllerClass = '\\app\\adminapi\\controller\\' . $moduleDir . '\\' . $controllerName . 'Controller';
        return invokeController($controllerClass, $action);
    }
}

// 解析2段路径: /adminapi/controller/action
// 例如: user/lists → admin/UserController::lists()
// 例如: upload/lists → UploadController (根目录)
// 例如: generator/lists → generator/GeneratorController
function resolveSimpleController($controller, $action) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerLower = strtolower($controller);
    
    // 尝试顺序:
    // 1. admin/{Controller}Controller (如 admin/UserController)
    $controllerClass = '\\app\\adminapi\\controller\\admin\\' . $controllerName . 'Controller';
    try {
        return invokeController($controllerClass, $action);
    } catch (\Throwable $e) {}
    
    // 2. {controller}Controller (根目录, 如 UploadController)
    $controllerClass = '\\app\\adminapi\\controller\\' . $controllerName . 'Controller';
    try {
        return invokeController($controllerClass, $action);
    } catch (\Throwable $e) {}
    
    // 3. {controller}/{Controller}Controller (子目录, 如 generator/GeneratorController)
    $controllerClass = '\\app\\adminapi\\controller\\' . $controllerLower . '\\' . $controllerName . 'Controller';
    return invokeController($controllerClass, $action);
}

// ============================================================
// adminapi 自动路由
// ============================================================
// 3段: /adminapi/module/controller/action
Route::any('adminapi/:module/:controller/:action', function ($module, $controller, $action) {
    return resolveAdminController($module, $controller, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// 2段: /adminapi/controller/action
Route::any('adminapi/:controller/:action', function ($controller, $action) {
    return resolveSimpleController($controller, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

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
    return invokeController($controllerClass, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

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
    return invokeController($controllerClass, $action);
})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

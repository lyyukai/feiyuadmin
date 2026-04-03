<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 策略：公开接口显式定义 + 其他接口自动路由
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
// 自动路由分发 Closure
// ============================================================
$autoRoute = function ($controller, $action, $request) {
    $controllerName = '';
    $parts = explode('_', $controller);
    foreach ($parts as $p) {
        $controllerName .= ucfirst(strtolower($p));
    }
    $controllerClass = '\\app\\adminapi\\controller\\' . $controllerName . 'Controller';
    
    try {
        $ctrl = invoke($controllerClass);
    } catch (\Throwable $e) {
        throw new \think\exception\HttpException(404, 'Controller not found: ' . $controllerClass);
    }
    
    if (!method_exists($ctrl, $action)) {
        throw new \think\exception\HttpException(404, "Method not found: {$controllerClass}::{$action}()");
    }
    
    $request->controllerObject = $ctrl;
    $request->controllerClass = $controllerClass;
    $request->controllerAction = $action;
    
    return invoke([$ctrl, $action], ['request' => $request]);
};

// ============================================================
// 后台管理端 adminapi/*
// ============================================================
Route::group('adminapi', function () {
    
    // ---- 公开接口（免登录）----
    Route::get('captcha/generate', 'app\adminapi\controller\captcha\CaptchaController@generate');
    Route::post('captcha/verify', 'app\adminapi\controller\captcha\CaptchaController@verify');
    Route::post('login/account', 'app\adminapi\controller\auth\LoginController@account');
    Route::post('login/logout', 'app\adminapi\controller\auth\LoginController@logout');
    
    // ---- 自动路由 ----
    Route::any(':controller/:action', $autoRoute)
        ->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
        ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
        ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// PC 前台端 pcapi/*
// ============================================================
Route::group('pcapi', function () {
    
    Route::any(':controller/:action', $autoRoute)
        ->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
        ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
        ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// 移动端 H5 mobileapi/*
// ============================================================
Route::group('mobileapi', function () {
    
    Route::any(':controller/:action', $autoRoute)
        ->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
        ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
        ->pattern(['controller' => '[a-zA-Z0-9_]+', 'action' => '[a-zA-Z0-9_]+']);

})->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

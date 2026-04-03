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
// 自动路由解析 Closure
// ============================================================
$autoRoute = function ($group, $controller, $action) {
    // controller 可能是 "admin/user" 或 "user" 格式
    // 需要映射到对应的子目录
    $controllerParts = explode('/', $controller);
    
    // 如果是 "admin/user" 格式，保持 admin 子目录
    // 如果只是 "user"，放在根目录（无子目录）
    $controllerName = '';
    $subDir = '';
    
    if (count($controllerParts) == 2) {
        $subDir = ucfirst(strtolower($controllerParts[0])) . '\\';
        $controllerName = ucfirst(strtolower($controllerParts[1]));
    } else {
        $controllerName = ucfirst(strtolower($controllerParts[0]));
    }
    
    $controllerClass = '\\app\\' . $group . '\\controller\\' . $subDir . $controllerName . 'Controller';
    
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
};

// ============================================================
// 后台管理端 adminapi/*
// ============================================================
Route::group('adminapi', function () use ($autoRoute) {
    
    // ---- 公开接口（免登录）----
    Route::get('captcha/generate', [\app\adminapi\controller\captcha\CaptchaController::class, 'generate']);
    Route::post('captcha/verify', [\app\adminapi\controller\captcha\CaptchaController::class, 'verify']);
    Route::post('login/account', [\app\adminapi\controller\auth\LoginController::class, 'account']);
    Route::post('login/logout', [\app\adminapi\controller\auth\LoginController::class, 'logout']);
    
    // ---- 自动路由 ----
    Route::any(':controller/:action', function ($controller, $action) use ($autoRoute) {
        return $autoRoute('adminapi', $controller, $action);
    })->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
      ->pattern(['controller' => '[a-zA-Z0-9_/]+', 'action' => '[a-zA-Z0-9_]+']);

});

// ============================================================
// PC 前台端 pcapi/*
// ============================================================
Route::group('pcapi', function () use ($autoRoute) {
    
    Route::any(':controller/:action', function ($controller, $action) use ($autoRoute) {
        return $autoRoute('pcapi', $controller, $action);
    })->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
      ->pattern(['controller' => '[a-zA-Z0-9_/]+', 'action' => '[a-zA-Z0-9_]+']);

});

// ============================================================
// 移动端 H5 mobileapi/*
// ============================================================
Route::group('mobileapi', function () use ($autoRoute) {
    
    Route::any(':controller/:action', function ($controller, $action) use ($autoRoute) {
        return $autoRoute('mobileapi', $controller, $action);
    })->middleware(\app\adminapi\http\middleware\AuthMiddleware::class)
      ->pattern(['controller' => '[a-zA-Z0-9_/]+', 'action' => '[a-zA-Z0-9_]+']);

});

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

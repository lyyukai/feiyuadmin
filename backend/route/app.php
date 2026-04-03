<?php
/**
 * 飞鱼后台管理系统 - 路由配置
 * 
 * 路由策略：自动路由 + 中间件绑定
 * API 路径规范:
 *   /adminapi/{controller}/{action}   ← 后台管理端
 *   /pcapi/{controller}/{action}     ← PC 前台端
 *   /mobileapi/{controller}/{action}  ← 移动端
 */

use think\facade\Route;

// ============================================================
// SPA 前端入口（所有未匹配的路径都 fallback 到 index.html）
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
// API 自动路由（由 InitMiddleware 解析 pathinfo，自动映射到控制器方法）
// 不需要在路由层手动定义每个接口
// ============================================================

// 后台管理端
Route::group('adminapi', function () {
    // 空闭包，所有路由由 InitMiddleware 自动解析
})->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
  ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// PC 前台端（InitMiddleware 自动路由，AuthMiddleware 按控制器 notNeedLogin 判断）
Route::group('pcapi', function () {
})->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
  ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// 移动端 H5
Route::group('mobileapi', function () {
})->middleware(\app\adminapi\http\middleware\InitMiddleware::class)
  ->middleware(\app\adminapi\http\middleware\AuthMiddleware::class);

// ============================================================
// 定时任务
// ============================================================
Route::rule('crontab', function () {
    \think\facade\Console::call('crontab');
});

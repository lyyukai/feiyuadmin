<?php
/**
 * 微信回调API路由
 */

use think\facade\Route;

// 微信回调接口（无需认证）
Route::group('api', function () {
    // 微信消息回调
    Route::get('wechat/callback/:account_id', 'api/wechat.Callback/index');
    Route::post('wechat/callback/:account_id', 'api/wechat.Callback/receive');
});

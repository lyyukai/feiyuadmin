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

    // 消息通知（调用 adminapi 模块）
    Route::get('notice/lists', 'adminapi/Notice/lists');
    Route::get('notice/detail', 'adminapi/Notice/detail');
    Route::post('notice/send', 'adminapi/Notice/send');
    Route::post('notice/edit', 'adminapi/Notice/edit');
    Route::post('notice/delete', 'adminapi/Notice/delete');
    Route::post('notice/read', 'adminapi/Notice/read');
    Route::get('notice/unread_count', 'adminapi/Notice/unreadCount');
});

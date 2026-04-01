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
    Route::get('notice/lists', 'app\adminapi\controller\NoticeController::lists');
    Route::get('notice/detail', 'app\adminapi\controller\NoticeController::detail');
    Route::post('notice/send', 'app\adminapi\controller\NoticeController::send');
    Route::post('notice/edit', 'app\adminapi\controller\NoticeController::edit');
    Route::post('notice/delete', 'app\adminapi\controller\NoticeController::delete');
    Route::post('notice/read', 'app\adminapi\controller\NoticeController::read');
    Route::get('notice/unread_count', 'app\adminapi\controller\NoticeController::unreadCount');
});

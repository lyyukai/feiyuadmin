<?php
/**
 * 微信回调API路由 / Mobile端 API路由
 */

use think\facade\Route;

// Mobile端 API - /api/* 前缀
Route::group('api', function () {
    // 消息通知
    Route::get('notice/lists', 'app\adminapi\controller\NoticeController@lists');
    Route::get('notice/detail', 'app\adminapi\controller\NoticeController@detail');
    Route::post('notice/send', 'app\adminapi\controller\NoticeController@send');
    Route::post('notice/edit', 'app\adminapi\controller\NoticeController@edit');
    Route::post('notice/delete', 'app\adminapi\controller\NoticeController@delete');
    Route::post('notice/read', 'app\adminapi\controller\NoticeController@read');
    Route::get('notice/unread_count', 'app\adminapi\controller\NoticeController@unreadCount');
});

// 微信回调接口（无需认证，保持原有路径）
Route::group('api', function () {
    // 微信消息回调
    Route::get('wechat/callback/:account_id', 'api/wechat.Callback@index');
    Route::post('wechat/callback/:account_id', 'api/wechat.Callback@receive');
});

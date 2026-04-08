<?php
use think\facade\Route;

/**
 * adminapi 路由
 * 验证码
 */
Route::get('captcha/generate', 'app\adminapi\controller\captcha\CaptchaController@generate');
Route::post('captcha/verify', 'app\adminapi\controller\captcha\CaptchaController@verify');

/**
 * AI助手路由
 */
Route::get('ai/chat/index', 'app\adminapi\controller\ai\ChatController@index');
Route::post('ai/chat/chat', 'app\adminapi\controller\ai\ChatController@chat');
Route::get('ai/chat/providers', 'app\adminapi\controller\ai\ChatController@providers');

/**
 * 系统配置路由
 */
Route::any('system_config/lists', 'app\adminapi\controller\admin\SystemConfigController@lists');
Route::any('system_config/save', 'app\adminapi\controller\admin\SystemConfigController@save');
Route::any('system_config/testStorage', 'app\adminapi\controller\admin\SystemConfigController@testStorage');

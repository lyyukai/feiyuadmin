<?php
use think\facade\Route;

/**
 * adminapi 路由
 * 验证码
 */
Route::get('captcha/generate', 'app\adminapi\controller\captcha\CaptchaController@generate');
Route::post('captcha/verify', 'app\adminapi\controller\captcha\CaptchaController@verify');

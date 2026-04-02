<?php
/**
 * 飞鱼后台管理系统 - 登录验证器
 */

declare(strict_types=1);

namespace app\adminapi\validate;

use app\common\validate\BaseValidate;

/**
 * 登录验证器
 * Class LoginValidate
 * @package app\adminapi\validate
 */
class LoginValidate extends BaseValidate
{
    protected $rule = [
        'username'   => 'require|max:20',
        'password'   => 'require|min:6|max:20',
        'captcha'    => 'length:4|alphaNum',
        'captcha_key'=> 'max:32',
    ];

    protected $message = [
        'username.require' => '请输入用户名',
        'username.max' => '用户名最多20个字符',
        'password.require' => '请输入密码',
        'password.min' => '密码至少6个字符',
        'password.max' => '密码最多20个字符',
    ];

    protected $scene = [
        'login' => ['username', 'password'],
    ];
}

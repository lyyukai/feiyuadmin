<?php
/**
 * 微信公众号账号验证器
 */

declare(strict_types=1);

namespace app\adminapi\validate\wechat;

use think\Validate;

/**
 * 账号验证器
 * Class WechatAccountValidate
 * @package app\adminapi\validate\wechat
 */
class WechatAccountValidate extends Validate
{
    protected $rule = [
        'name' => 'require|max:50',
        'appid' => 'require|max:100',
        'type' => 'require|in:1,2',
    ];

    protected $message = [
        'name.require' => '账号名称不能为空',
        'name.max' => '账号名称最多50个字符',
        'appid.require' => 'AppID不能为空',
        'appid.max' => 'AppID最多100个字符',
        'type.require' => '账号类型不能为空',
        'type.in' => '账号类型不正确',
    ];

    protected $scene = [
        'add' => ['name', 'appid', 'type'],
        'edit' => ['name', 'appid', 'type'],
    ];
}

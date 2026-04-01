<?php
/**
 * 微信自动回复验证器
 */

declare(strict_types=1);

namespace app\adminapi\validate\wechat;

use think\Validate;

/**
 * 自动回复验证器
 * Class WechatReplyValidate
 * @package app\adminapi\validate\wechat
 */
class WechatReplyValidate extends Validate
{
    protected $rule = [
        'account_id' => 'require|number',
        'type' => 'require|in:keyword,follow,default',
        'keyword' => 'max:100',
        'reply_type' => 'require|in:text,image,news,voice',
        'content' => 'require',
    ];

    protected $message = [
        'account_id.require' => '请选择公众号账号',
        'account_id.number' => '公众号账号ID不正确',
        'type.require' => '回复类型不能为空',
        'type.in' => '回复类型不正确',
        'keyword.max' => '关键词最多100个字符',
        'reply_type.require' => '回复内容类型不能为空',
        'reply_type.in' => '回复内容类型不正确',
        'content.require' => '回复内容不能为空',
    ];

    protected $scene = [
        'add' => ['account_id', 'type', 'reply_type', 'content'],
        'edit' => ['account_id', 'type', 'reply_type', 'content'],
    ];
}

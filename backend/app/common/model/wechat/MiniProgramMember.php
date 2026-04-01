<?php
/**
 * 小程序成员模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 小程序成员模型
 * Class MiniProgramMember
 * @package app\common\model\wechat
 */
class MiniProgramMember extends Model
{
    protected $name = 'mini_program_member';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'mini_program_id' => 'integer',
        'user_id' => 'integer',
    ];

    // 角色文本
    public function getRoleTextAttr(): string
    {
        $roleMap = ['developer' => '开发者', 'experience' => '体验者'];
        return $roleMap[$this->role] ?? '未知';
    }
}

<?php
/**
 * 微信粉丝模型
 */

declare(strict_types=1);

namespace app\common\model\wechat;

use think\Model;

/**
 * 粉丝模型
 * Class WechatFans
 * @package app\common\model\wechat
 */
class WechatFans extends Model
{
    protected $name = 'wechat_fans';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    // 类型转换
    protected $type = [
        'gender' => 'integer',
        'status' => 'integer',
        'blacklist' => 'integer',
    ];

    // 获取器：处理昵称表情
    public function getNicknameAttr($value): string
    {
        // 过滤emoji表情
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $value ?? '');
    }

    // 获取器：处理标签列表
    public function getTagidListAttr($value): array
    {
        if (empty($value)) {
            return [];
        }
        return explode(',', $value);
    }

    // 修改器：处理标签列表
    public function setTagidListAttr($value): string
    {
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value ?? '';
    }

    // 性别文本
    public function getGenderTextAttr(): string
    {
        $genderMap = [0 => '未知', 1 => '男', 2 => '女'];
        return $genderMap[$this->gender] ?? '未知';
    }

    // 状态文本
    public function getStatusTextAttr(): string
    {
        return $this->status == 1 ? '已关注' : '未关注';
    }
}

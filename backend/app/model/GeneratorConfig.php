<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 代码生成器-数据库配置模型
 */
class GeneratorConfig extends Model
{
    use SoftDelete;

    protected $name = 'generator_config';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['password', 'delete_time'];

    /**
     * 获取默认配置
     */
    public static function getDefault(): ?self
    {
        return self::where('is_default', 1)->where('status', 1)->find();
    }

    /**
     * 设置默认配置
     */
    public static function setDefault(int $id): void
    {
        self::where('is_default', 1)->update(['is_default' => 0]);
        self::where('id', $id)->update(['is_default' => 1]);
    }
}

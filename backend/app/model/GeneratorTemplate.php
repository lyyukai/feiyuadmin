<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 代码生成器-模板管理模型
 */
class GeneratorTemplate extends Model
{
    use SoftDelete;

    protected $name = 'generator_template';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 模板类型常量
     */
    const TYPE_BACKEND_PHP = 'backend_php';
    const TYPE_FRONTEND_VUE = 'frontend_vue';

    /**
     * 获取默认模板
     */
    public static function getDefaults(): array
    {
        return self::where('is_default', 1)->where('status', 1)->select()->toArray();
    }

    /**
     * 按类型获取模板
     */
    public static function getByType(string $type): array
    {
        return self::where('type', $type)->where('status', 1)->order('sort', 'asc')->select()->toArray();
    }
}

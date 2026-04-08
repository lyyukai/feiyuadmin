<?php
/**
 * 文章分类模型
 */
declare(strict_types=1);

namespace app\model;

use think\Model;

class ContentCategory extends Model
{
    protected $name = 'content_category';
    protected $pk   = 'id';

    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;

    // 类型转换
    protected $json   = [];
    protected $jsonAssoc = true;
}

<?php
/**
 * 文章模型
 */
declare(strict_types=1);

namespace app\model;

use think\Model;

class ContentArticle extends Model
{
    protected $name = 'content_article';
    protected $pk   = 'id';

    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = 'update_time';

    // 类型转换
    protected $json   = [];
    protected $jsonAssoc = true;
}

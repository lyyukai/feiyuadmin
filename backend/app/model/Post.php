<?php
declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 岗位模型
 */
class Post extends Model
{
    use SoftDelete;

    protected $name = 'post';
    protected $deleteTime = 'delete_time';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['delete_time'];

    protected $type = [
        'sort'   => 'integer',
        'status' => 'integer',
    ];

    /**
     * 岗位下用户
     */
    public function users(): \think\model\relation\HasMany
    {
        return $this->hasMany(User::class, 'post_id', 'id');
    }
}

<?php
declare(strict_types=1);

namespace app\adminapi\model;

use think\Model;

/**
 * 大屏组件模型
 */
class ScreenComponent extends Model
{
    protected $name = 'screen_component';
    protected $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 类型转换
    protected $type = [
        'config' => 'json',
        'data_source' => 'json',
    ];

    /**
     * 大屏关联
     */
    public function screen(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(DataScreen::class, 'screen_id', 'id');
    }
}

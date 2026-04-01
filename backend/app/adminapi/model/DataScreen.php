<?php
declare(strict_types=1);

namespace app\adminapi\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 数据大屏模型
 */
class DataScreen extends Model
{
    use SoftDelete;

    protected $name = 'data_screen';
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 类型转换
    protected $type = [
        'config' => 'json',
        'status' => 'integer',
    ];

    /**
     * 获取完整的大屏配置
     */
    public function getFullConfig(): array
    {
        $data = $this->toArray();
        $data['config'] = $data['config'] ?? [];
        return $data;
    }
}

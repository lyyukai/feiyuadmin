<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 工作流连线模型
 */
class WorkflowEdge extends Model
{
    protected $name = 'workflow_edge';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 连线类型常量
    const TYPE_DEFAULT = 'default';
    const TYPE_CONDITION = 'condition';

    /**
     * 配置获取器
     */
    public function getConditionConfigAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 配置修改器
     */
    public function setConditionConfigAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    /**
     * 获取关联的流程
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id', 'id');
    }
}

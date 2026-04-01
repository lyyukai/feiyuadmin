<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 工作流节点模型
 */
class WorkflowNode extends Model
{
    protected $name = 'workflow_node';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 节点类型常量
    const TYPE_START = 'start';
    const TYPE_APPROVER = 'approver';
    const TYPE_CONDITION = 'condition';
    const TYPE_END = 'end';

    /**
     * 节点类型文本获取器
     */
    public function getNodeTypeTextAttr($value, $data): string
    {
        $types = [
            self::TYPE_START => '开始节点',
            self::TYPE_APPROVER => '审批人节点',
            self::TYPE_CONDITION => '条件分支',
            self::TYPE_END => '结束节点',
        ];
        return $types[$data['node_type']] ?? '未知';
    }

    /**
     * 配置获取器
     */
    public function getConfigAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 配置修改器
     */
    public function setConfigAttr($value)
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

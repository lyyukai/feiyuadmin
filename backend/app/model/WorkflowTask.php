<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 工作流任务模型
 */
class WorkflowTask extends Model
{
    protected $name = 'workflow_task';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 任务类型常量
    const TYPE_START = 'start';
    const TYPE_APPROVE = 'approve';
    const TYPE_COUNTER_SIGN = 'counter_sign';

    // 审批状态常量
    const STATUS_PENDING = 0;      // 待处理
    const STATUS_APPROVED = 1;     // 已通过
    const STATUS_REJECTED = 2;     // 已驳回
    const STATUS_TRANSFERRED = 3;  // 已转交
    const STATUS_REMINDED = 4;     // 已催办

    /**
     * 任务类型文本获取器
     */
    public function getTaskTypeTextAttr($value, $data): string
    {
        $types = [
            self::TYPE_START => '发起',
            self::TYPE_APPROVE => '审批',
            self::TYPE_COUNTER_SIGN => '会签',
        ];
        return $types[$data['task_type']] ?? '未知';
    }

    /**
     * 审批状态文本获取器
     */
    public function getActionStatusTextAttr($value, $data): string
    {
        $status = [
            self::STATUS_PENDING => '待处理',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已驳回',
            self::STATUS_TRANSFERRED => '已转交',
            self::STATUS_REMINDED => '已催办',
        ];
        return $status[$data['action_status']] ?? '未知';
    }

    /**
     * 状态搜索器
     */
    public function searchActionStatusAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('action_status', $value);
        }
    }

    /**
     * 审批人搜索器
     */
    public function searchAssigneeAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('assignee', $value);
        }
    }

    /**
     * 获取关联的实例
     */
    public function instance()
    {
        return $this->belongsTo(WorkflowInstance::class, 'instance_id', 'id');
    }

    /**
     * 获取关联的流程
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id', 'id');
    }
}

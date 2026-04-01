<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 工作流实例模型
 */
class WorkflowInstance extends Model
{
    protected $name = 'workflow_instance';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 状态常量
    const STATUS_RUNNING = 0;    // 进行中
    const STATUS_FINISHED = 1;   // 已完成
    const STATUS_REJECTED = 2;   // 已驳回
    const STATUS_WITHDRAWN = 3; // 已撤回

    // 是否结束常量
    const NOT_ENDED = 0;
    const ENDED = 1;

    /**
     * 状态文本获取器
     */
    public function getStatusTextAttr($value, $data): string
    {
        $status = [
            self::STATUS_RUNNING => '进行中',
            self::STATUS_FINISHED => '已完成',
            self::STATUS_REJECTED => '已驳回',
            self::STATUS_WITHDRAWN => '已撤回',
        ];
        return $status[$data['status']] ?? '未知';
    }

    /**
     * 表单数据获取器
     */
    public function getFormDataAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 表单数据修改器
     */
    public function setFormDataAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    /**
     * 状态搜索器
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('status', $value);
        }
    }

    /**
     * 关键词搜索
     */
    public function searchKeywordAttr($query, $value)
    {
        if (!empty($value)) {
            $query->whereLike('title|instance_no', "%{$value}%");
        }
    }

    /**
     * 获取关联的流程
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id', 'id');
    }

    /**
     * 获取关联的任务
     */
    public function tasks()
    {
        return $this->hasMany(WorkflowTask::class, 'instance_id', 'id');
    }
}

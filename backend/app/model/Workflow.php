<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 工作流模型
 */
class Workflow extends Model
{
    protected $name = 'workflow';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 状态常量
    const STATUS_DISABLED = 0;  // 禁用
    const STATUS_ENABLED = 1;   // 启用

    // 发布状态常量
    const NOT_PUBLISHED = 0;
    const PUBLISHED = 1;

    /**
     * 类型文本获取器
     */
    public function getStatusTextAttr($value, $data): string
    {
        return ($data['status'] ?? 0) == self::STATUS_ENABLED ? '启用' : '禁用';
    }

    /**
     * 发布状态文本获取器
     */
    public function getIsPublishedTextAttr($value, $data): string
    {
        return ($data['is_published'] ?? 0) == self::PUBLISHED ? '已发布' : '未发布';
    }

    /**
     * 流程数据获取器
     */
    public function getFlowDataAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 流程数据修改器
     */
    public function setFlowDataAttr($value)
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
            $query->whereLike('name|code|description', "%{$value}%");
        }
    }

    /**
     * 获取关联的节点
     */
    public function nodes()
    {
        return $this->hasMany(WorkflowNode::class, 'workflow_id', 'id');
    }

    /**
     * 获取关联的连线
     */
    public function edges()
    {
        return $this->hasMany(WorkflowEdge::class, 'workflow_id', 'id');
    }
}

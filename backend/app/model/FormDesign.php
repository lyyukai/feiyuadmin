<?php
/**
 * 飞鱼后台管理系统 - 表单设计模型
 */

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 表单设计模型
 * Class FormDesign
 * @package app\model
 */
class FormDesign extends Model
{
    protected $name = 'form_design';
    
    protected $pk = 'id';
    
    protected $autoWriteTimestamp = 'datetime';
    
    protected $createTime = 'create_time';
    
    protected $updateTime = 'update_time';
    
    protected $json = ['config'];
    
    /**
     * 获取器：处理config JSON字段
     */
    public function getConfigAttr($value)
    {
        return json_decode($value ?: '{}', true);
    }
    
    /**
     * 修改器：处理config JSON字段
     */
    public function setConfigAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }
}

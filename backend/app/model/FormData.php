<?php
/**
 * 飞鱼后台管理系统 - 表单数据模型
 */

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 表单数据模型
 * Class FormData
 * @package app\model
 */
class FormData extends Model
{
    protected $name = 'form_data';
    
    protected $pk = 'id';
    
    protected $autoWriteTimestamp = 'datetime';
    
    protected $createTime = 'create_time';
    
    protected $updateTime = false;
    
    protected $json = ['data'];
    
    /**
     * 获取器：处理data JSON字段
     */
    public function getDataAttr($value)
    {
        return json_decode($value ?: '{}', true);
    }
    
    /**
     * 修改器：处理data JSON字段
     */
    public function setDataAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }
    
    /**
     * 获取器：处理IP字段
     */
    public function getIpAttr($value)
    {
        return $value ?: '';
    }
}

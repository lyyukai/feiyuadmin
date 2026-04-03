<?php
/**
 * 飞鱼后台管理系统 - 配置控制器别名
 * 
 * 为 /config/* 路径提供别名支持
 * 实际路由到 SystemConfigController
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

/**
 * 配置控制器（SystemConfigController 别名）
 * 用于兼容 /config/* 路径的 API 调用
 */
class ConfigController extends SystemConfigController
{
    // 继承 SystemConfigController 的所有方法
    // /config/lists → SystemConfigController::lists()
    // /config/save → SystemConfigController::save()
}

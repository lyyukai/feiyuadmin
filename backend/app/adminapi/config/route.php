<?php
/**
 * 飞羽后台管理系统 - 后台管理路由中间件配置
 */

return [
    'middleware' => [
        // 初始化
        \app\adminapi\http\middleware\InitMiddleware::class,
        // 认证
        \app\adminapi\http\middleware\AuthMiddleware::class,
    ],
];

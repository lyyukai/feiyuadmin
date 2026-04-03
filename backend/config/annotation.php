<?php
// +----------------------------------------------------------------------
// | think-annotation 配置
// +----------------------------------------------------------------------

return [
    // 路由注解配置
    'route' => [
        'enable' => true,
        'controllers' => [
            // 扫描 adminapi 控制器目录
            app_path('adminapi/controller') => [
                'name' => 'adminapi',
                'middleware' => [],
            ],
        ],
    ],

    // 模型注解配置
    'model' => [
        'enable' => true,
    ],
];

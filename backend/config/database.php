<?php
return [
    // 默认数据库连接配置
    'default' => 'mysql',
    
    // 数据库连接配置
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => env('database.hostname', '127.0.0.1'),
            // 数据库名
            'database' => env('database.database', 'feiyuadmin'),
            // 用户名
            'username' => env('database.username', 'root'),
            // 密码
            'password' => env('database.password', 'root123456'),
            // 端口
            'hostport' => env('database.hostport', '3307'),
            // 编码
            'charset' => env('database.charset', 'utf8mb4'),
            // 排序规则
            'collate' => 'utf8mb4_general_ci',
            // 表前缀
            'prefix' => 'fy_',
            // 断线重连
            'break_reconnect' => true,
            // 是否JWT
            'jwt' => false,
            // 缓存目录
            'cache_dir' => '',
        ],
    ],
];

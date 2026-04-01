<?php
// Cache configuration
return [
    'default' => 'file',
    'stores' => [
        'file' => [
            'type' => 'File',
            'path' => '../runtime/cache/',
            'prefix' => '',
            'expire' => 0,
        ],
        'redis' => [
            'type' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'select' => 0,
            'timeout' => 0,
            'expire' => 0,
            'persistent' => false,
            'prefix' => '',
        ],
    ],
];

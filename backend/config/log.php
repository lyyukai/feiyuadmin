<?php
// Log configuration
return [
    'default' => 'file',
    'channels' => [
        'file' => [
            'type' => 'File',
            'path' => '../runtime/log/',
            'level' => ['error', 'warning', 'info', 'debug'],
            'file_size' => 2097152,
            'time_format' => 'Y-m-d H:i:s',
        ],
    ],
];

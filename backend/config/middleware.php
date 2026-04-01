<?php
// Middleware configuration
return [
    'alias' => [
        'Cors' => app\middleware\Cors::class,
        'Auth' => app\middleware\Auth::class,
    ],
    'priority' => [],
];

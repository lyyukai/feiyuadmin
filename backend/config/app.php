<?php
// Application configuration
return [
    'app_debug' => true,
    'app_trace' => false,
    'app_namespace' => 'app',
    'default_return_type' => 'json',
    'default_ajax_return' => 'json',
    'default_jsonp_handler' => 'jsonpReturn',
    'var_jsonp_handler' => 'callback',
    'exception_handle' => '\\app\\exception\\ExceptionHandle',
    'show_error_msg' => true,
    'url_route_on' => true,
    'url_route_must' => false,
    'url_html_suffix' => 'html',
    'session' => [
        'type' => 'file',
        'expire' => 86400,
    ],
    'cookie' => [
        'expire' => 0,
        'prefix' => '',
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => false,
    ],
    'middleware' => [],
];

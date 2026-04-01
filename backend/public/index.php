<?php
// +----------------------------------------------------------------------
// | ThinkPHP HTTP入口
// +----------------------------------------------------------------------
namespace think {

    // 安装锁定检测
    $lockFile = dirname(__DIR__) . '/install.lock';
    if (!file_exists($lockFile)) {
        header('Location: /install.php');
        exit;
    }

    require __DIR__ . '/../vendor/autoload.php';

    // 手动设置PATH_INFO（修复Nginx无法正确传递的问题）
    if (!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] === '') {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        
        // 移除查询参数
        $requestUri = strtok($requestUri, '?');
        
        // 提取PATH_INFO（REQUEST_URI - SCRIPT_NAME）
        if (strpos($requestUri, $scriptName) === 0) {
            $pathInfo = substr($requestUri, strlen($scriptName));
        } elseif (strpos($requestUri, dirname($scriptName)) === 0) {
            $pathInfo = substr($requestUri, strlen(dirname($scriptName)));
            if (strpos($pathInfo, $scriptName) === 0) {
                $pathInfo = substr($pathInfo, strlen($scriptName));
            }
        } else {
            $pathInfo = $requestUri;
        }
        
        $_SERVER['PATH_INFO'] = $pathInfo ?: '/';
        $_SERVER['ORIG_PATH_INFO'] = $_SERVER['PATH_INFO'];
    }

    $http = (new App())->http;
    $response = $http->run();
    $response->send();
    $http->end($response);
}

<?php
// +----------------------------------------------------------------------
// | 飞羽后台管理系统 - 管理后台入口
// +----------------------------------------------------------------------

namespace think;

// 检测是否已安装
$lockFile = __DIR__ . '/install.lock';
if (!is_file($lockFile)) {
    header('Location: install.php');
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$http = (new App())->http;

$response = $http->run();
$response->send();
$http->end($response);

<?php
// +----------------------------------------------------------------------
// | 应用配置
// +----------------------------------------------------------------------
namespace app;

use think\annotation\Annotation;
use think\annotation\Service as AnnotationService;

return [
    'app' => AppService::class,
    // 注解路由服务
    AnnotationService::class => AnnotationService::class,
];

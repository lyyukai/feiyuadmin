<?php
/**
 * 飞鱼后台管理系统 - 注解路由测试控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\test;

use think\annotation\route\Get;
use think\annotation\route\Group;
use think\annotation\route\Post;
use think\Response;

/**
 * 注解路由测试
 * @Group("test")
 */
class AnnotationTestController
{
    /**
     * 测试接口 - GET
     * 访问路径: GET /adminapi/test/index
     */
    #[Get('index')]
    public function index(): Response
    {
        return json([
            'code' => 0,
            'msg' => '注解路由测试成功！',
            'data' => [
                'annotation' => 'working',
                'timestamp' => time(),
            ]
        ]);
    }

    /**
     * 测试接口 - POST
     * 访问路径: POST /adminapi/test/save
     */
    #[Post('save')]
    public function save(): Response
    {
        return json([
            'code' => 0,
            'msg' => 'POST 注解路由测试成功！',
            'data' => [
                'method' => 'post',
                'timestamp' => time(),
            ]
        ]);
    }

    /**
     * 测试带参数的路由
     * 访问路径: GET /adminapi/test/detail/:id
     */
    #[Get('detail')]
    public function detail(): Response
    {
        $id = request()->param('id', 0);
        return json([
            'code' => 0,
            'msg' => '详情页',
            'data' => [
                'id' => $id,
                'timestamp' => time(),
            ]
        ]);
    }
}

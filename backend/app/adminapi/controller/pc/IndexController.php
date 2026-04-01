<?php
/**
 * PC端 - 首页控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

class IndexController extends BasePcController
{
    public function banner()
    {
        return $this->success('获取成功', []);
    }

    public function notice()
    {
        return $this->success('获取成功', []);
    }
}

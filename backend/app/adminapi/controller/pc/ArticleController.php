<?php
/**
 * PC端 - 文章控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

class ArticleController extends BasePcController
{
    public function lists()
    {
        return $this->success('获取成功', []);
    }

    public function detail()
    {
        return $this->success('获取成功', []);
    }
}

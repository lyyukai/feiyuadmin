<?php
/**
 * 移动端H5 - 文章控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

class ArticleController extends BaseMobileController
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

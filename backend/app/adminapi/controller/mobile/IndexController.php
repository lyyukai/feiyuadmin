<?php
/**
 * 移动端H5 - 首页控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

class IndexController extends BaseMobileController
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

<?php
/**
 * 移动端H5 - 订单控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

class OrderController extends BaseMobileController
{
    public function lists()
    {
        return $this->success('获取成功', []);
    }

    public function detail()
    {
        return $this->success('获取成功', []);
    }

    public function create()
    {
        return $this->success('创建成功', []);
    }

    public function cancel()
    {
        return $this->success('取消成功', []);
    }
}

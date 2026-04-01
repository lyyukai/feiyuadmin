<?php
/**
 * 移动端H5 - 反馈控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

class FeedbackController extends BaseMobileController
{
    public function lists()
    {
        return $this->success('获取成功', []);
    }

    public function add()
    {
        return $this->success('提交成功', []);
    }
}

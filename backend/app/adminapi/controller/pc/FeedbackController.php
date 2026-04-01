<?php
/**
 * PC端 - 反馈控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

class FeedbackController extends BasePcController
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

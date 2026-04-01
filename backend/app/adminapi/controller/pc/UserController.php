<?php
/**
 * PC端 - 用户控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

class UserController extends BasePcController
{
    public function info()
    {
        return $this->success('获取成功', []);
    }

    public function edit()
    {
        return $this->success('修改成功', []);
    }

    public function editAvatar()
    {
        return $this->success('修改成功', []);
    }

    public function editPassword()
    {
        return $this->success('修改成功', []);
    }
}

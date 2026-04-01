<?php
/**
 * PC端 - 收藏控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

class FavoritesController extends BasePcController
{
    public function lists()
    {
        return $this->success('获取成功', []);
    }

    public function add()
    {
        return $this->success('添加成功', []);
    }

    public function delete()
    {
        return $this->success('删除成功', []);
    }
}

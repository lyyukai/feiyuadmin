<?php
/**
 * PC端 - 通用控制器基类
 */
declare(strict_types=1);

namespace app\adminapi\controller\pc;

use app\adminapi\controller\BaseAdminController;

class BasePcController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
}

<?php
/**
 * 移动端H5 - 通用控制器基类
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

use app\adminapi\controller\BaseAdminController;

class BaseMobileController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
}

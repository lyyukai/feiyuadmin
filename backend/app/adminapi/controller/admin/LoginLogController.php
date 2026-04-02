<?php
/**
 * 飞鱼后台管理系统 - 登录日志控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;

/**
 * 登录日志控制器
 * Class LoginLogController
 * @package app\adminapi\controller\admin
 */
class LoginLogController extends BaseAdminController
{
    /**
     * 登录日志列表
     */
    public function lists()
    {
        $page = (int) ($this->param('page', 1));
        $limit = min((int) ($this->param('limit', 15)), 100);
        $offset = ($page - 1) * $limit;
        
        $query = \think\facade\Db::name('login_log');
        $total = $query->count();
        $list = $query->order('id', 'desc')->limit($offset, $limit)->select()->toArray();
        
        return $this->data(['list' => $list, 'total' => $total, 'page' => $page, 'limit' => $limit]);
    }
}

<?php
/**
 * 飞羽后台管理系统 - 后台管理控制器基类
 */

declare(strict_types=1);

namespace app\adminapi\controller;

use app\common\controller\BaseController;
use app\common\service\JsonService;
use think\App;
use think\Response;

/**
 * 后台管理控制器基类
 * Class BaseAdminController
 * @package app\adminapi\controller
 */
class BaseAdminController extends BaseController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['account'];

    /**
     * 构造函数
     */
    public function __construct(App $app = null)
    {
        if ($app !== null) {
            parent::__construct($app);
        }
        $this->initialize();
    }

    /**
     * 返回成功响应
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Response
     */
    protected function success(string $msg = '操作成功', array $data = [], int $code = 0, int $show = 1): Response
    {
        return JsonService::success($msg, $data, $code, $show);
    }

    /**
     * 返回失败响应
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return Response
     */
    protected function fail(string $msg = '操作失败', int $code = 400, array $data = []): Response
    {
        return JsonService::fail($msg, $data, $code);
    }

    /**
     * 返回数据
     * @param mixed $data
     * @return Response
     */
    protected function data($data): Response
    {
        return JsonService::data($data);
    }

    /**
     * 返回列表
     * @param array $list
     * @param int $total
     * @param array $extend
     * @return Response
     */
    protected function responseList(array $list, int $total = 0, array $extend = []): Response
    {
        return JsonService::list($list, $total, $extend);
    }
}

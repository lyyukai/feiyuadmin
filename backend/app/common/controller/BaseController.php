<?php
/**
 * 飞鱼后台管理系统 - 基础控制器
 */

declare(strict_types=1);

namespace app\common\controller;

use think\App;
use think\Request;
use think\Response;

/**
 * 控制器基类
 * Class BaseController
 * @package app\common\controller
 */
abstract class BaseController
{
    /** @var App */
    protected App $app;

    /** @var Request */
    protected Request $request;

    /** @var int 登录用户ID */
    protected int $adminId = 0;

    /** @var array 登录用户信息 */
    protected array $adminInfo = [];

    /** @var array 免登录接口 */
    protected array $notNeedLogin = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request ?? request();
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize(): void
    {
        if (isset($this->request->adminInfo) && $this->request->adminInfo) {
            $this->adminInfo = $this->request->adminInfo;
            $this->adminId = $this->request->adminInfo['admin_id'] ?? 0;
        }
    }

    /**
     * 检查是否需要登录
     * @return bool
     */
    protected function isNotNeedLogin(): bool
    {
        $action = $this->request->action();
        return in_array($action, $this->notNeedLogin) || in_array('*', $this->notNeedLogin);
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
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
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
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 返回数据
     * @param mixed $data
     * @return Response
     */
    protected function data($data): Response
    {
        return $this->success('', $data, 0, 0);
    }

    /**
     * 获取请求参数
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    protected function param(string $name = null, mixed $default = null): mixed
    {
        if ($name === null) {
            return $this->request->param();
        }
        return $this->request->param($name, $default);
    }

    /**
     * 获取 GET 参数
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $name = null, mixed $default = null): mixed
    {
        if ($name === null) {
            return $this->request->get();
        }
        return $this->request->get($name, $default);
    }

    /**
     * 获取 POST 参数
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    protected function post(string $name = null, mixed $default = null): mixed
    {
        if ($name === null) {
            return $this->request->post();
        }
        return $this->request->post($name, $default);
    }
}

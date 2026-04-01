<?php
declare(strict_types=1);

namespace app\controller;

use think\Request;
use think\Response;

/**
 * 基础控制器类
 */
abstract class Base
{
    /**
     * Request实例
     */
    protected Request $request;

    /**
     * 当前登录用户ID
     */
    protected int $userId = 0;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->request = request();
        $this->userId = (int) ($this->request->userId ?? 0);
    }

    /**
     * 返回成功JSON
     */
    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 返回错误JSON
     */
    protected function error(string $msg = '操作失败', int $code = 400, mixed $data = []): Response
    {
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 返回分页JSON
     */
    protected function page(int $total, array $list, string $msg = 'success'): Response
    {
        return json([
            'code' => 0,
            'msg' => $msg,
            'total' => $total,
            'data' => $list,
        ]);
    }

    /**
     * 获取请求参数
     */
    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    /**
     * 获取所有请求参数
     */
    protected function params(): array
    {
        return $this->request->param();
    }

    /**
     * 获取分页参数
     */
    protected function pageParam(int $defaultPage = 1, int $defaultLimit = 20): array
    {
        $page = (int) $this->request->param('page', $defaultPage);
        $limit = (int) $this->request->param('limit', $defaultLimit);
        $limit = $limit > 100 ? 100 : $limit;
        return [$page, $limit];
    }
}

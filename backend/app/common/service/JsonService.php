<?php
/**
 * 飞鱼后台管理系统 - JSON 响应服务
 */

declare(strict_types=1);

namespace app\common\service;

use think\Response;
use think\exception\HttpResponseException;

/**
 * JSON 响应服务
 * Class JsonService
 * @package app\common\service
 */
class JsonService
{
    /**
     * 操作成功
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Response
     */
    public static function success(string $msg = '操作成功', array $data = [], int $code = 0, int $show = 1): Response
    {
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 操作失败
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Response
     */
    public static function fail(string $msg = '操作失败', array $data = [], int $code = 400, int $show = 1): Response
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
    public static function data($data): Response
    {
        return self::success('', $data, 0, 0);
    }

    /**
     * 返回列表数据
     * @param array $list
     * @param int $total
     * @param array $extend
     * @return Response
     */
    public static function list(array $list, int $total = 0, array $extend = []): Response
    {
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'list' => $list,
                'total' => $total,
                'extend' => $extend,
            ],
        ]);
    }

    /**
     * 抛出成功响应
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     */
    public static function throwSuccess(string $msg = '操作成功', array $data = [], int $code = 0, int $show = 1): void
    {
        throw new HttpResponseException(self::success($msg, $data, $code, $show));
    }

    /**
     * 抛出失败响应
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     */
    public static function throwFail(string $msg = '操作失败', array $data = [], int $code = 400, int $show = 1): void
    {
        throw new HttpResponseException(self::fail($msg, $data, $code, $show));
    }

    /**
     * 检查条件并抛出异常
     * @param bool $condition
     * @param string $msg
     * @throws HttpResponseException
     */
    public static function check(bool $condition, string $msg = '操作失败'): void
    {
        if (!$condition) {
            self::throwFail($msg);
        }
    }
}

<?php
// +----------------------------------------------------------------------
// | 异常处理
// +----------------------------------------------------------------------
namespace app\exception;

use think\exception\Handle;
use think\Response;
use Throwable;

class ExceptionHandle extends Handle
{
    public function render($request, Throwable $e): Response
    {
        // API请求统一返回JSON
        if (str_starts_with($request->path(), 'api')) {
            $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            return json([
                'code' => $code,
                'msg' => $e->getMessage() ?: 'Server Error',
            ], $code);
        }

        return parent::render($request, $e);
    }
}

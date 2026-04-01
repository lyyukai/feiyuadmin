<?php
declare(strict_types=1);

namespace app\middleware;

use think\Response;

/**
 * CORS跨域中间件
 */
class Cors
{
    public function handle($request, \Closure $next): Response
    {
        $response = $next($request);
        
        // 允许的源：优先使用环境变量配置，默认为空（由前端代理控制）
        // 生产环境建议设置为实际前端域名，如：http://your-frontend.com
        $allowOrigin = env('CORS_ALLOW_ORIGIN', '');
        if (empty($allowOrigin)) {
            $allowOrigin = '*';  // 开发环境允许所有源，生产环境建议配置具体域名
        }

        $headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, X-Requested-With, X-Token',
            'Access-Control-Expose-Headers' => 'X-Token',
            'Access-Control-Max-Age' => 86400,
        ];
        if (!empty($allowOrigin)) {
            $headers['Access-Control-Allow-Origin'] = $allowOrigin;
        }
        $response->header($headers);

        return $response;
    }
}

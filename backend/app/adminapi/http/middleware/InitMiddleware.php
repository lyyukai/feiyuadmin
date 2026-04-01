<?php
declare(strict_types=1);

namespace app\adminapi\http\middleware;

class InitMiddleware
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}

<?php
declare(strict_types=1);

namespace app\service;

use think\facade\Cache;

/**
 * Token服务 - 基于Cache的简单Token方案
 */
class TokenService
{
    /**
     * 生成Token
     */
    public static function generate(int $userId): string
    {
        $token = bin2hex(random_bytes(32));  // 256位强随机Token
        $expire = 7 * 86400; // 7天有效期
        Cache::set('token_' . $token, $userId, $expire);
        return $token;
    }

    /**
     * 验证Token，返回userId
     */
    public static function verify(string $token): int
    {
        $userId = Cache::get('token_' . $token);
        return $userId ? (int) $userId : 0;
    }

    /**
     * 删除Token
     */
    public static function delete(string $token): bool
    {
        return Cache::delete('token_' . $token);
    }

    /**
     * 刷新Token有效期
     */
    public static function refresh(string $token): bool
    {
        $userId = self::verify($token);
        if ($userId) {
            $expire = 7 * 86400;
            Cache::set('token_' . $token, $userId, $expire);
            return true;
        }
        return false;
    }
}

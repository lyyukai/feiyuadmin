<?php
/**
 * 飞鱼后台管理系统 - 验证码服务
 */

declare(strict_types=1);

namespace app\service;

use think\facade\Cache;

/**
 * 验证码服务
 * Class CaptchaService
 */
class CaptchaService
{
    /**
     * 生成验证码
     * @param string $key 验证码标识（如登录用 "login"）
     * @return array ['token' => string, 'expire' => int]
     */
    public static function generate(string $key = 'login'): array
    {
        $code = self::generateCode(4);
        $token = bin2hex(random_bytes(16));

        // 存储到 Cache，5分钟有效期
        $cacheKey = 'captcha_' . $key;
        Cache::set($cacheKey, [
            'code' => strtolower($code),
            'create_time' => time(),
            'try_count' => 0,
        ], 300);

        return [
            'token' => $token,
            'code' => $code,  // 供调试，生产不使用
            'expire' => 300, // 5分钟
        ];
    }

    /**
     * 生成数字+字母混合验证码
     */
    protected static function generateCode(int $length): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }

    /**
     * 验证验证码（基于Cache）
     * @param string $inputCode 用户输入
     * @param string $key 验证码标识
     * @return bool
     */
    public static function verify(string $inputCode, string $key = 'login'): bool
    {
        $cacheKey = 'captcha_' . $key;
        $cacheData = Cache::get($cacheKey);

        if (empty($cacheData)) {
            return false;
        }

        // 检查是否过期（5分钟）
        if (time() - $cacheData['create_time'] > 300) {
            self::clear($key);
            return false;
        }

        // 验证失败计数，超3次强制刷新
        $tryCount = ($cacheData['try_count'] ?? 0) + 1;
        if ($tryCount >= 3) {
            self::clear($key);
            return false;
        }

        // 验证
        $valid = strtolower($inputCode) === $cacheData['code'];

        // 验证失败才记录次数，正确则直接清除
        if (!$valid) {
            $cacheData['try_count'] = $tryCount;
            Cache::set($cacheKey, $cacheData, 300);
        } else {
            self::clear($key);
        }

        return $valid;
    }

    /**
     * 清除验证码
     */
    public static function clear(string $key = 'login'): void
    {
        Cache::delete('captcha_' . $key);
    }

    /**
     * 获取当前验证码（调试用，生产环境不应暴露）
     */
    public static function getCode(string $key = 'login'): ?string
    {
        $data = Cache::get('captcha_' . $key);
        return $data['code'] ?? null;
    }
}

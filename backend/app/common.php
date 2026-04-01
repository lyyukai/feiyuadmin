<?php
/**
 * 系统公共函数库
 */

use think\facade\Crypt;

/**
 * 格式化字节大小
 */
function format_bytes(int $bytes): string
{
    if ($bytes <= 0) {
        return '0 B';
    }
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $i = floor(log($bytes, 1024));
    $i = $i > count($units) - 1 ? count($units) - 1 : $i;
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

/**
 * 加密字符串
 */
function encrypt(string $value): string
{
    return Crypt::encrypt($value, 'aes', 'base64');
}

/**
 * 解密字符串
 */
function decrypt(string $value): string
{
    try {
        return Crypt::decrypt($value, 'aes', 'base64');
    } catch (\Exception $e) {
        return $value;
    }
}

/**
 * 获取客户端IP地址
 */
function get_client_ip(): string
{
    $ip = request()->ip();
    return $ip ?? '0.0.0.0';
}

/**
 * 获取客户端UserAgent
 */
function get_client_user_agent(): string
{
    return request()->header('user-agent', '');
}

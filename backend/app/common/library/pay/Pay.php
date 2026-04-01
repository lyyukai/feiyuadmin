<?php
/**
 * 支付工厂类
 */

declare(strict_types=1);

namespace app\common\library\pay;

use app\common\model\pay\PayConfig as PayConfigModel;

/**
 * 支付工厂
 * Class Pay
 * @package app\common\library\pay
 */
class Pay
{
    /**
     * 驱动映射
     */
    protected static array $drivers = [
        'wechat' => \app\common\library\pay\driver\WechatPayDriver::class,
        'alipay' => \app\common\library\pay\driver\AlipayDriver::class,
    ];

    /**
     * 获取支付驱动
     * @param string $channel 渠道 wechat/alipay
     * @return PayGateway
     * @throws \Exception
     */
    public static function driver(string $channel): PayGateway
    {
        $driverClass = self::$drivers[$channel] ?? null;
        if (!$driverClass) {
            throw new \Exception("不支持的支付渠道: {$channel}");
        }

        // 获取配置
        $config = PayConfigModel::where('channel', $channel)
            ->where('status', 1)
            ->find();

        if (!$config) {
            throw new \Exception("{$channel} 支付配置不存在或已禁用");
        }

        $driver = new $driverClass();
        $driver->setConfig($config->toArray());
        $driver->setChannel($channel);

        return $driver;
    }

    /**
     * 注册驱动
     */
    public static function register(string $channel, string $driverClass): void
    {
        self::$drivers[$channel] = $driverClass;
    }

    /**
     * 发起支付
     */
    public static function pay(string $channel, array $params): array
    {
        try {
            $driver = self::driver($channel);
            return $driver->pay($params);
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * 支付回调
     */
    public static function notify(string $channel, array $data): array
    {
        try {
            $driver = self::driver($channel);
            return $driver->notify($data);
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * 退款
     */
    public static function refund(string $channel, array $params): array
    {
        try {
            $driver = self::driver($channel);
            return $driver->refund($params);
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage(), 'data' => []];
        }
    }
}

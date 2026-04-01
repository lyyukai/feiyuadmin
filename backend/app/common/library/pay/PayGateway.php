<?php
/**
 * 支付网关抽象类
 */

declare(strict_types=1);

namespace app\common\library\pay;

/**
 * 支付网关
 * Class PayGateway
 * @package app\common\library\pay
 */
abstract class PayGateway
{
    /**
     * 配置
     */
    protected array $config = [];

    /**
     * 渠道
     */
    protected string $channel = '';

    /**
     * 设置配置
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 设置渠道
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * 获取配置
     */
    protected function getConfig(string $key, $default = '')
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * 支付
     * @param array $params 支付参数
     * @return array ['code'=>0, 'msg'=>'', 'data'=>[]]
     */
    abstract public function pay(array $params): array;

    /**
     * 回调通知
     * @param array $data 回调数据
     * @return array ['code'=>0, 'msg'=>'', 'data'=>[]]
     */
    abstract public function notify(array $data): array;

    /**
     * 退款
     * @param array $params 退款参数
     * @return array ['code'=>0, 'msg'=>'', 'data'=>[]]
     */
    abstract public function refund(array $params): array;

    /**
     * 退款查询
     * @param string $out_refund_no 退款单号
     * @return array
     */
    abstract public function refundQuery(string $out_refund_no): array;

    /**
     * 关闭订单
     * @param string $out_trade_no 订单号
     * @return array
     */
    abstract public function close(string $out_trade_no): array;

    /**
     * 订单查询
     * @param string $out_trade_no 订单号
     * @return array
     */
    abstract public function query(string $out_trade_no): array;

    /**
     * 分账
     * @param array $params 分账参数
     * @return array
     */
    public function profitSharing(array $params): array
    {
        return ['code' => 0, 'msg' => '暂不支持分账'];
    }

    /**
     * 构建响应
     */
    protected function buildResult(int $code, string $msg, array $data = []): array
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }
}

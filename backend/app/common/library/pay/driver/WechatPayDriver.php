<?php
/**
 * 微信支付驱动
 */

declare(strict_types=1);

namespace app\common\library\pay\driver;

use app\common\library\pay\PayGateway;

/**
 * 微信支付驱动
 * Class WechatPayDriver
 * @package app\common\library\pay\driver
 */
class WechatPayDriver extends PayGateway
{
    /**
     * 微信支付接口地址
     */
    protected string $gateway = 'https://api.mch.weixin.qq.com';

    /**
     * 支付
     */
    public function pay(array $params): array
    {
        $order_no = $params['order_no'] ?? '';
        $total_fee = (int)($params['total_fee'] * 100); // 转换为分
        $subject = $params['subject'] ?? '';
        $notify_url = $params['notify_url'] ?? $this->getConfig('notify_url');
        $client_ip = $params['client_ip'] ?? request()->ip();

        // Native支付参数
        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'body' => $subject,
            'out_trade_no' => $order_no,
            'total_fee' => $total_fee,
            'spbill_create_ip' => $client_ip,
            'notify_url' => $notify_url,
            'trade_type' => 'NATIVE',
        ];

        // 签名
        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        // 发送请求
        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/pay/unifiedorder', $xml);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '支付下单失败');
        }

        if ($result['result_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['err_code_des'] ?? '支付下单失败');
        }

        return $this->buildResult(0, '下单成功', [
            'out_trade_no' => $result['out_trade_no'],
            'code_url' => $result['code_url'],
            'prepay_id' => $result['prepay_id'],
        ]);
    }

    /**
     * 支付回调
     */
    public function notify(array $data): array
    {
        $xml = file_get_contents('php://input');
        $result = $this->fromXml($xml);

        if (!isset($result['sign'])) {
            return $this->buildResult(1, '签名不存在');
        }

        // 验证签名
        $sign = $result['sign'];
        unset($result['sign']);
        if ($sign !== $this->makeSign($result)) {
            return $this->buildResult(1, '签名验证失败');
        }

        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '回调失败');
        }

        return $this->buildResult(0, 'success', [
            'out_trade_no' => $result['out_trade_no'],
            'transaction_id' => $result['transaction_id'] ?? '',
            'total_fee' => ($result['total_fee'] ?? 0) / 100,
            'pay_time' => date('Y-m-d H:i:s', strtotime($result['time_end'])),
        ]);
    }

    /**
     * 退款
     */
    public function refund(array $params): array
    {
        $refund_no = $params['refund_no'] ?? '';
        $out_trade_no = $params['out_trade_no'] ?? '';
        $total_fee = (int)($params['total_fee'] * 100);
        $refund_fee = (int)($params['refund_fee'] * 100);

        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'transaction_id' => $out_trade_no,
            'out_refund_no' => $refund_no,
            'total_fee' => $total_fee,
            'refund_fee' => $refund_fee,
        ];

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/secapi/pay/refund', $xml, true);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '退款失败');
        }

        if ($result['result_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['err_code_des'] ?? '退款失败');
        }

        return $this->buildResult(0, '退款成功', [
            'out_refund_no' => $result['out_refund_no'],
            'refund_id' => $result['refund_id'],
        ]);
    }

    /**
     * 退款查询
     */
    public function refundQuery(string $out_refund_no): array
    {
        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'out_refund_no' => $out_refund_no,
        ];

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/pay/refundquery', $xml);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '查询失败');
        }

        $status = 'pending';
        if (isset($result['refund_status_0'])) {
            $status = match ($result['refund_status_0']) {
                'SUCCESS' => 'success',
                'FAIL' => 'fail',
                default => 'refunding',
            };
        }

        return $this->buildResult(0, '查询成功', [
            'status' => $status,
            'refund_fee' => ($result['refund_fee_0'] ?? 0) / 100,
        ]);
    }

    /**
     * 关闭订单
     */
    public function close(string $out_trade_no): array
    {
        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'out_trade_no' => $out_trade_no,
        ];

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/pay/closeorder', $xml);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '关闭失败');
        }

        return $this->buildResult(0, '关闭成功');
    }

    /**
     * 订单查询
     */
    public function query(string $out_trade_no): array
    {
        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'out_trade_no' => $out_trade_no,
        ];

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/pay/orderquery', $xml);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '查询失败');
        }

        $status = match ($result['trade_state'] ?? '') {
            'SUCCESS' => 'paid',
            'REFUND' => 'refunded',
            'CLOSED' => 'closed',
            'PAYERROR' => 'pay_error',
            default => 'pending',
        };

        return $this->buildResult(0, '查询成功', [
            'status' => $status,
            'transaction_id' => $result['transaction_id'] ?? '',
            'total_fee' => ($result['total_fee'] ?? 0) / 100,
            'pay_time' => isset($result['time_end']) ? date('Y-m-d H:i:s', strtotime($result['time_end'])) : '',
        ]);
    }

    /**
     * 分账
     */
    public function profitSharing(array $params): array
    {
        if ($this->getConfig('profit_sharing') !== 'on') {
            return $this->buildResult(1, '分账功能未开启');
        }

        $order_no = $params['order_no'] ?? '';
        $receivers = $params['receivers'] ?? [];

        $data = [
            'appid' => $this->getConfig('appid'),
            'mch_id' => $this->getConfig('mchid'),
            'nonce_str' => $this->generateNonceStr(),
            'transaction_id' => $order_no,
            'receivers' => json_encode($receivers),
        ];

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;

        $xml = $this->toXml($data);
        $response = $this->httpPost($this->gateway . '/secapi/pay/profitsharing', $xml, true);

        $result = $this->fromXml($response);
        if ($result['return_code'] !== 'SUCCESS') {
            return $this->buildResult(1, $result['return_msg'] ?? '分账失败');
        }

        return $this->buildResult(0, '分账成功');
    }

    /**
     * 生成签名
     */
    protected function makeSign(array $data): string
    {
        ksort($data);
        $string = '';
        foreach ($data as $key => $value) {
            if ($value !== '' && $value !== null) {
                $string .= "{$key}={$value}&";
            }
        }
        $string .= "key=" . $this->getConfig('api_key');
        return strtoupper(md5($string));
    }

    /**
     * 生成随机字符串
     */
    protected function generateNonceStr(int $length = 32): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * 数组转XML
     */
    protected function toXml(array $data): string
    {
        $xml = '<xml>';
        foreach ($data as $key => $value) {
            $xml .= "<{$key}><![CDATA[{$value}]]></{$key}>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * XML转数组
     */
    protected function fromXml(string $xml): array
    {
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data ?: [];
    }

    /**
     * HTTP POST请求
     */
    protected function httpPost(string $url, string $data, bool $useCert = false): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($useCert) {
            $certPath = $this->getConfig('cert_path');
            $keyPath = $this->getConfig('key_path');
            if ($certPath && $keyPath) {
                curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
                curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
            }
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response ?: '';
    }
}

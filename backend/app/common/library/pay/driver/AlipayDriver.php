<?php
/**
 * 支付宝驱动
 */

declare(strict_types=1);

namespace app\common\library\pay\driver;

use app\common\library\pay\PayGateway;

/**
 * 支付宝驱动
 * Class AlipayDriver
 * @package app\common\library\pay\driver
 */
class AlipayDriver extends PayGateway
{
    /**
     * 支付宝网关
     */
    protected string $gateway = 'https://openapi.alipay.com/gateway.do';

    /**
     * 支付
     */
    public function pay(array $params): array
    {
        $order_no = $params['order_no'] ?? '';
        $total_fee = $params['total_fee'] ?? 0;
        $subject = $params['subject'] ?? '';
        $notify_url = $params['notify_url'] ?? $this->getConfig('notify_url');

        $bizContent = [
            'out_trade_no' => $order_no,
            'total_amount' => $total_fee,
            'subject' => $subject,
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ];

        $data = [
            'app_id' => $this->getConfig('appid'),
            'method' => 'alipay.trade.app.pay',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent),
        ];

        $data['sign'] = $this->makeSign($data);

        // 返回支付参数，前端调起支付
        return $this->buildResult(0, '下单成功', [
            'out_trade_no' => $order_no,
            'pay_params' => http_build_query($data),
        ]);
    }

    /**
     * 支付回调
     */
    public function notify(array $data): array
    {
        $sign = $data['sign'] ?? '';
        $signType = $data['sign_type'] ?? 'RSA2';

        // 验证签名
        $signData = $data;
        unset($signData['sign'], $signData['sign_type']);
        if (!$this->verifySign($signData, $sign, $signType)) {
            return $this->buildResult(1, '签名验证失败');
        }

        if (($data['trade_status'] ?? '') !== 'TRADE_SUCCESS') {
            return $this->buildResult(1, '交易未成功');
        }

        return $this->buildResult(0, 'success', [
            'out_trade_no' => $data['out_trade_no'],
            'transaction_id' => $data['trade_no'] ?? '',
            'total_fee' => $data['total_amount'] ?? 0,
            'pay_time' => $data['gmt_payment'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 退款
     */
    public function refund(array $params): array
    {
        $refund_no = $params['refund_no'] ?? '';
        $out_trade_no = $params['out_trade_no'] ?? '';
        $refund_fee = $params['refund_fee'] ?? 0;
        $reason = $params['reason'] ?? '';

        $bizContent = [
            'trade_no' => $out_trade_no,
            'refund_amount' => $refund_fee,
            'refund_reason' => $reason,
            'out_request_no' => $refund_no,
        ];

        $data = [
            'app_id' => $this->getConfig('appid'),
            'method' => 'alipay.trade.refund',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent),
        ];

        $data['sign'] = $this->makeSign($data);

        $response = $this->httpPost($this->gateway, $data);
        $result = json_decode($response, true);

        $refundResponse = $result['alipay_trade_refund_response'] ?? [];
        if ($refundResponse['code'] !== '10000') {
            return $this->buildResult(1, $refundResponse['sub_msg'] ?? '退款失败');
        }

        return $this->buildResult(0, '退款成功', [
            'out_refund_no' => $refundResponse['out_trade_no'] ?? $refund_no,
            'refund_id' => $refundResponse['trade_no'] ?? '',
        ]);
    }

    /**
     * 退款查询
     */
    public function refundQuery(string $out_refund_no): array
    {
        $bizContent = [
            'out_request_no' => $out_refund_no,
        ];

        $data = [
            'app_id' => $this->getConfig('appid'),
            'method' => 'alipay.trade.fastpay.refund.query',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent),
        ];

        $data['sign'] = $this->makeSign($data);

        $response = $this->httpPost($this->gateway, $data);
        $result = json_decode($response, true);

        $queryResponse = $result['alipay_trade_fastpay_refund_query_response'] ?? [];
        if ($queryResponse['code'] !== '10000') {
            return $this->buildResult(1, $queryResponse['sub_msg'] ?? '查询失败');
        }

        $status = match ($queryResponse['refund_status'] ?? '') {
            'REFUND_SUCCESS' => 'success',
            'REFUND_FAIL' => 'fail',
            default => 'refunding',
        };

        return $this->buildResult(0, '查询成功', [
            'status' => $status,
            'refund_fee' => $queryResponse['refund_amount'] ?? 0,
        ]);
    }

    /**
     * 关闭订单
     */
    public function close(string $out_trade_no): array
    {
        $bizContent = [
            'out_trade_no' => $out_trade_no,
        ];

        $data = [
            'app_id' => $this->getConfig('appid'),
            'method' => 'alipay.trade.close',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent),
        ];

        $data['sign'] = $this->makeSign($data);

        $response = $this->httpPost($this->gateway, $data);
        $result = json_decode($response, true);

        $closeResponse = $result['alipay_trade_close_response'] ?? [];
        if ($closeResponse['code'] !== '10000') {
            return $this->buildResult(1, $closeResponse['sub_msg'] ?? '关闭失败');
        }

        return $this->buildResult(0, '关闭成功');
    }

    /**
     * 订单查询
     */
    public function query(string $out_trade_no): array
    {
        $bizContent = [
            'out_trade_no' => $out_trade_no,
        ];

        $data = [
            'app_id' => $this->getConfig('appid'),
            'method' => 'alipay.trade.query',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($bizContent),
        ];

        $data['sign'] = $this->makeSign($data);

        $response = $this->httpPost($this->gateway, $data);
        $result = json_decode($response, true);

        $queryResponse = $result['alipay_trade_query_response'] ?? [];
        if ($queryResponse['code'] !== '10000') {
            return $this->buildResult(1, $queryResponse['sub_msg'] ?? '查询失败');
        }

        $status = match ($queryResponse['trade_status'] ?? '') {
            'WAIT_BUYER_PAY' => 'pending',
            'TRADE_CLOSED' => 'closed',
            'TRADE_SUCCESS' => 'paid',
            'TRADE_FINISHED' => 'paid',
            default => 'pending',
        };

        return $this->buildResult(0, '查询成功', [
            'status' => $status,
            'transaction_id' => $queryResponse['trade_no'] ?? '',
            'total_fee' => $queryResponse['total_amount'] ?? 0,
            'pay_time' => $queryResponse['pay_time'] ?? '',
        ]);
    }

    /**
     * 生成签名
     */
    protected function makeSign(array $data): string
    {
        $privateKey = $this->getConfig('api_key');
        if (empty($privateKey)) {
            return '';
        }

        ksort($data);
        $stringToSign = '';
        foreach ($data as $key => $value) {
            $stringToSign .= "{$key}=" . ($value !== '' ? $value : '') . "&";
        }
        $stringToSign = rtrim($stringToSign, '&');

        $key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        openssl_sign($stringToSign, $sign, $key, OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }

    /**
     * 验证签名
     */
    protected function verifySign(array $data, string $sign, string $signType = 'RSA2'): bool
    {
        $publicKey = $this->getConfig('alipay_public_key');
        if (empty($publicKey)) {
            return false;
        }

        ksort($data);
        $stringToSign = '';
        foreach ($data as $key => $value) {
            $stringToSign .= "{$key}=" . ($value !== '' ? $value : '') . "&";
        }
        $stringToSign = rtrim($stringToSign, '&');

        $key = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($publicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        $signType = $signType === 'RSA2' ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1;
        return openssl_verify($stringToSign, base64_decode($sign), $key, $signType) === 1;
    }

    /**
     * HTTP POST请求
     */
    protected function httpPost(string $url, array $data): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response ?: '';
    }
}

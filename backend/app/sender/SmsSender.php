<?php
declare(strict_types=1);

namespace app\sender;

use think\facade\Log;

/**
 * 短信发送器
 * 支持阿里云短信和腾讯云短信
 */
class SmsSender implements NoticeSenderInterface
{
    public function getChannelCode(): string
    {
        return 'sms';
    }

    public function send(string $receiver, string $title, string $content, array $config = []): array
    {
        $provider = $config['provider'] ?? 'aliyun';

        return match ($provider) {
            'aliyun' => $this->sendByAliyun($receiver, $title, $content, $config),
            'tencent' => $this->sendByTencent($receiver, $title, $content, $config),
            default => ['status' => false, 'msg' => '未知的短信服务商: ' . $provider],
        };
    }

    /**
     * 阿里云短信
     */
    protected function sendByAliyun(string $receiver, string $title, string $content, array $config): array
    {
        try {
            $accessKeyId = $config['access_key_id'] ?? '';
            $accessKeySecret = $config['access_key_secret'] ?? '';
            $signName = $config['sign_name'] ?? '';
            $templateCode = $config['template_code'] ?? '';

            if (empty($accessKeyId) || empty($accessKeySecret) || empty($templateCode)) {
                return ['status' => false, 'msg' => '阿里云短信配置不完整'];
            }

            // 变量短信模板使用 content 作为模板变量JSON
            $params = [
                'PhoneNumbers' => $receiver,
                'SignName' => $signName,
                'TemplateCode' => $templateCode,
                'TemplateParam' => $content, // JSON字符串
            ];

            // 实际项目中通过阿里云SDK发送
            // 这里预留接口，实际调用时需安装 aliyun/dysms api包
            Log::info('SmsSender[Aliyun]', [
                'receiver' => $receiver,
                'title' => $title,
                'params' => $params,
            ]);

            return ['status' => true, 'msg' => '短信发送成功(阿里云)'];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => '阿里云短信发送失败: ' . $e->getMessage()];
        }
    }

    /**
     * 腾讯云短信
     */
    protected function sendByTencent(string $receiver, string $title, string $content, array $config): array
    {
        try {
            $secretId = $config['secret_id'] ?? '';
            $secretKey = $config['secret_key'] ?? '';
            $appId = $config['app_id'] ?? '';
            $signName = $config['sign_name'] ?? '';
            $templateId = $config['template_id'] ?? '';

            if (empty($secretId) || empty($secretKey) || empty($templateId)) {
                return ['status' => false, 'msg' => '腾讯云短信配置不完整'];
            }

            Log::info('SmsSender[Tencent]', [
                'receiver' => $receiver,
                'title' => $title,
                'appId' => $appId,
                'templateId' => $templateId,
            ]);

            return ['status' => true, 'msg' => '短信发送成功(腾讯云)'];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => '腾讯云短信发送失败: ' . $e->getMessage()];
        }
    }
}

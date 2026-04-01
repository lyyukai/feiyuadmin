<?php
declare(strict_types=1);

namespace app\sender;

use think\facade\Log;

/**
 * 企微机器人发送器
 * 使用企业微信群机器人的Webhook接口
 */
class WechatSender implements NoticeSenderInterface
{
    public function getChannelCode(): string
    {
        return 'wechat';
    }

    public function send(string $receiver, string $title, string $content, array $config = []): array
    {
        try {
            $webhookUrl = $config['webhook_url'] ?? '';

            if (empty($webhookUrl)) {
                return ['status' => false, 'msg' => '企微机器人webhook地址未配置'];
            }

            // 构建消息内容
            $message = [
                'msgtype' => 'markdown',
                'markdown' => [
                    'content' => $this->buildMarkdown($title, $content),
                ],
            ];

            // 发送HTTP请求
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $response = $client->post($webhookUrl, [
                'json' => $message,
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['errcode']) && $result['errcode'] === 0) {
                return ['status' => true, 'msg' => '企微消息发送成功'];
            }

            $errmsg = $result['errmsg'] ?? '未知错误';
            return ['status' => false, 'msg' => '企微消息发送失败: ' . $errmsg];
        } catch (\Throwable $e) {
            Log::error('WechatSender error: ' . $e->getMessage());
            return ['status' => false, 'msg' => '企微消息发送异常: ' . $e->getMessage()];
        }
    }

    /**
     * 构建Markdown消息
     */
    protected function buildMarkdown(string $title, string $content): string
    {
        $md = "### {$title}\n\n";
        $md .= $content . "\n";
        return $md;
    }
}

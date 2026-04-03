<?php
/**
 * 通义千问 AI 服务
 */

declare(strict_types=1);

namespace app\service\ai;

class QwenService extends AiService
{
    protected string $provider = 'qwen';
    protected string $model = 'qwen-turbo';

    /**
     * 发送聊天请求
     * @param array $messages 消息列表，格式: [['role' => 'user', 'content' => 'xxx'], ...]
     * @param array $options 可选参数
     * @return array ['content' => '回复内容', 'usage' => [...]]
     */
    public function chat(array $messages, array $options = []): array
    {
        $data = [
            'model' => $options['model'] ?? $this->model,
            'input' => ['messages' => $messages],
            'parameters' => [
                'temperature' => $options['temperature'] ?? $this->temperature,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            ],
        ];

        try {
            $response = $this->request('POST', '/services/aigc/text-generation/generation', $data);
            
            // 通义千问返回格式
            return [
                'content' => $response['output']['choices'][0]['message']['content'] ?? '',
                'usage' => $response['usage'] ?? [],
                'raw' => $response,
            ];
        } catch (\Exception $e) {
            throw new \Exception('通义千问调用失败: ' . $e->getMessage());
        }
    }
}

<?php
/**
 * OpenAI / MiniMax 兼容 API 服务
 * 支持任何兼容 OpenAI 格式的大模型接口
 */

declare(strict_types=1);

namespace app\service\ai;

class OpenaiService extends AiService
{
    protected string $provider = 'openai';
    protected string $model = 'gpt-4';

    public function __construct(array $configOverrides = [])
    {
        parent::__construct($configOverrides);
    }

    /**
     * 发送聊天请求（OpenAI 兼容格式）
     * @param array $messages 消息列表，格式: [['role' => 'user', 'content' => 'xxx'], ...]
     * @param array $options 可选参数
     * @return array ['content' => '回复内容', 'usage' => [...]]
     */
    public function chat(array $messages, array $options = []): array
    {
        $data = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
        ];

        try {
            $response = $this->request('POST', '/chat/completions', $data);

            // OpenAI 兼容格式返回
            return [
                'content' => $response['choices'][0]['message']['content'] ?? '',
                'usage' => $response['usage'] ?? [],
                'raw' => $response,
            ];
        } catch (\Exception $e) {
            throw new \Exception('OpenAI/MiniMax 接口调用失败: ' . $e->getMessage());
        }
    }
}

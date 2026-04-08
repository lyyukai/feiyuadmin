<?php
/**
 * 文心一言 AI 服务
 */

declare(strict_types=1);

namespace app\service\ai;

class WenxinService extends AiService
{
    protected string $provider = 'wenxin';
    protected string $model = 'ernie-4.0';
    protected string $wenxinAk = '';
    protected string $wenxinSk = '';

    public function __construct(array $configOverrides = [])
    {
        parent::__construct($configOverrides);
        // 从配置或覆盖中获取 AK/SK
        $aiConfig = \think\facade\Config::get('site.ai', []);
        $this->wenxinAk = $configOverrides['wenxin_ak'] ?? $aiConfig['wenxin_ak'] ?? '';
        $this->wenxinSk = $configOverrides['wenxin_sk'] ?? $aiConfig['wenxin_sk'] ?? '';
    }

    /**
     * 发送聊天请求
     * @param array $messages 消息列表，格式: [['role' => 'user', 'content' => 'xxx'], ...]
     * @param array $options 可选参数
     * @return array ['content' => '回复内容', 'usage' => [...]]
     */
    public function chat(array $messages, array $options = []): array
    {
        // 构建请求参数
        $data = [
            'messages' => $messages,
            'stream' => false,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_output_tokens' => $options['max_tokens'] ?? $this->maxTokens,
        ];

        try {
            $response = $this->request('POST', '/oauth/2.0/binary', $data);
            
            // 文心一言返回格式
            return [
                'content' => $response['result'] ?? $response['choices'][0]['message']['content'] ?? '',
                'usage' => $response['usage'] ?? [],
                'raw' => $response,
            ];
        } catch (\Exception $e) {
            throw new \Exception('文心一言调用失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取Access Token（需要先调用）
     */
    public function getAccessToken(): string
    {
        $cacheKey = 'ai_wenxin_access_token';
        $token = Cache::get($cacheKey);
        
        if ($token) {
            return $token;
        }

        // 调用百度Access Token API
        $ak = $this->wenxinAk;
        $sk = $this->wenxinSk;
        
        if (empty($ak) || empty($sk)) {
            throw new \Exception('文心一言 AK/SK 未配置');
        }

        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => $ak,
            'client_secret' => $sk,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (isset($result['access_token'])) {
            $token = $result['access_token'];
            $expiresIn = $result['expires_in'] ?? 2592000;
            Cache::set($cacheKey, $token, $expiresIn - 300);
            return $token;
        }

        throw new \Exception('获取文心一言Access Token失败');
    }
}

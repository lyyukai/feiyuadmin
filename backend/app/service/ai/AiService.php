<?php
/**
 * 飞鱼后台管理系统 - AI服务基类
 * 
 * 支持多种大模型：文心一言、通义千问、OpenAI
 */

declare(strict_types=1);

namespace app\service\ai;

use think\facade\Cache;
use think\facade\Config;

abstract class AiService
{
    // 配置
    protected string $provider = '';           // 模型提供商
    protected string $apiKey = '';            // API Key
    protected string $baseUrl = '';           // API 地址
    protected string $model = '';             // 模型名称
    protected float $temperature = 0.7;       // 温度参数
    protected int $maxTokens = 2000;          // 最大token数

    public function __construct()
    {
        $this->initConfig();
    }

    /**
     * 初始化配置
     */
    protected function initConfig(): void
    {
        $aiConfig = Config::get('site.ai', []);
        $this->apiKey = $aiConfig['api_key'] ?? '';
        $this->baseUrl = $aiConfig['base_url'] ?? '';
        $this->model = $aiConfig['model'] ?? '';
    }

    /**
     * 发送聊天请求（抽象方法，子类实现）
     */
    abstract public function chat(array $messages, array $options = []): array;

    /**
     * 发送请求（通用方法）
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('AI请求失败: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception('AI请求失败，HTTP ' . $httpCode . ': ' . $response);
        }

        return json_decode($response, true) ?: [];
    }

    /**
     * 构建消息历史（保留最近N条）
     */
    protected function buildHistory(array $history, int $keep = 10): array
    {
        if (count($history) > $keep) {
            return array_slice($history, -$keep);
        }
        return $history;
    }
}

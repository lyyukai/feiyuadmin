<?php
/**
 * AI服务工厂
 */

declare(strict_types=1);

namespace app\service\ai;

use think\facade\Config;

class AiFactory
{
    /**
     * 获取AI服务实例
     * @param string $provider wenxin|qwen|openai
     */
    public static function getService(string $provider = ''): AiService
    {
        if (empty($provider)) {
            $provider = Config::get('site.ai.provider', 'wenxin');
        }

        return match ($provider) {
            'qwen' => new QwenService(),
            'openai' => new OpenaiService(),
            default => new WenxinService(),
        };
    }

    /**
     * 获取支持的模型列表
     */
    public static function getSupportedProviders(): array
    {
        return [
            'wenxin' => '文心一言',
            'qwen' => '通义千问',
            'openai' => 'OpenAI GPT',
        ];
    }
}

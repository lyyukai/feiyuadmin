<?php
/**
 * 站点配置
 * 
 * AI服务配置:
 * - provider: wenxin|qwen|openai
 * - api_key: API密钥
 * - base_url: API地址
 * - model: 模型名称
 * - wenxin_ak: 文心一言AK (百度云)
 * - wenxin_sk: 文心一言SK (百度云)
 */

declare(strict_types=1);

return [
    // 默认AI提供商
    'ai_provider' => env('AI_PROVIDER', 'wenxin'),
    
    // AI配置
    'ai' => [
        'provider' => env('AI_PROVIDER', 'wenxin'),
        'api_key' => env('AI_API_KEY', ''),
        'base_url' => env('AI_BASE_URL', 'https://aip.baidubce.com'),
        'model' => env('AI_MODEL', 'ernie-4.0'),
        // 文心一言专用
        'wenxin_ak' => env('WENXIN_AK', ''),
        'wenxin_sk' => env('WENXIN_SK', ''),
    ],
];

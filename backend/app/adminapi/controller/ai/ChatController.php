<?php
/**
 * AI助手控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\ai;

use app\adminapi\controller\BaseAdminController;
use app\service\ai\AiFactory;
use think\response\Json;

/**
 * AI助手
 */
class ChatController extends BaseAdminController
{
    // 继承父类的 notNeedLogin = ['account']

    /**
     * AI对话首页
     * GET /adminapi/ai/chat/index
     */
    public function index(): Json
    {
        return $this->success('获取成功', [
            'providers' => AiFactory::getSupportedProviders(),
        ]);
    }
    
    /**
     * 发送对话请求
     */
    public function chat(): Json
    {
        $params = $this->request->param();
        
        $messages = $params['messages'] ?? [];
        $provider = $params['provider'] ?? '';
        
        if (empty($messages)) {
            return $this->fail('消息不能为空');
        }

        // 前端传入的API配置覆盖
        $config = [];
        if (!empty($params['api_key'])) $config['api_key'] = $params['api_key'];
        if (!empty($params['base_url'])) $config['base_url'] = $params['base_url'];
        if (!empty($params['model'])) $config['model'] = $params['model'];
        if (!empty($params['wenxin_ak'])) $config['wenxin_ak'] = $params['wenxin_ak'];
        if (!empty($params['wenxin_sk'])) $config['wenxin_sk'] = $params['wenxin_sk'];

        try {
            $aiService = AiFactory::getService($provider, $config);
            $result = $aiService->chat($messages);
            
            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取支持的模型列表
     */
    public function providers(): Json
    {
        return $this->success('获取成功', AiFactory::getSupportedProviders());
    }
}

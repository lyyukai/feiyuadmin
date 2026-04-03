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

        try {
            $aiService = AiFactory::getService($provider);
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

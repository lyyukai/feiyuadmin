<?php
/**
 * PC前台 AI 控制器
 * 
 * 路由: /pcapi/ai/nl2sql
 *       /pcapi/ai/lowcode
 */

declare(strict_types=1);

namespace app\adminapi\controller\pc;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\ai\Nl2SqlLogic;
use think\response\Json;

/**
 * PC前台 AI 控制器
 */
class AiController extends BaseAdminController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['nl2sql', 'lowcode'];

    /**
     * NL2SQL 自然语言转 SQL
     * POST /pcapi/ai/nl2sql
     * Body: { prompt: "查询所有用户" }
     * Response: { code: 0, data: { sql: "SELECT ..." } }
     */
    public function nl2sql(): Json
    {
        $prompt = $this->request->param('prompt', '');
        
        if (empty($prompt)) {
            return $this->fail('问题不能为空');
        }

        try {
            // 调用 NL2SQL 逻辑层
            $logic = new Nl2SqlLogic();
            $result = $logic->convert($prompt);
            
            return $this->success('转换成功', [
                'sql' => $result['sql'] ?? '',
                'explanation' => $result['explanation'] ?? '',
                'tables' => $result['tables'] ?? [],
            ]);
        } catch (\Exception $e) {
            return $this->fail('转换失败: ' . $e->getMessage());
        }
    }

    /**
     * 低代码保存
     * POST /pcapi/ai/lowcode
     * Body: { page_id: "", config: {} }
     * Response: { code: 0, data: { id: 1 } }
     */
    public function lowcode(): Json
    {
        $pageId = $this->request->param('page_id', '');
        $config = $this->request->param('config/a', []);

        // TODO: 实际保存到数据库
        // 这里先返回模拟成功
        return $this->success('保存成功', [
            'id' => $pageId ?: 'temp_' . time(),
            'saved' => true,
        ]);
    }
}

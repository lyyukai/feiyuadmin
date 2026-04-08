<?php
/**
 * PC前台 AI 控制器
 *
 * 路由: /pcapi/ai/nl2sql
 *       /pcapi/ai/lowcode
 *       /pcapi/ai/execute
 *       /pcapi/ai/tables
 */

declare(strict_types=1);

namespace app\adminapi\controller\pc;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\ai\Nl2SqlLogic;
use app\adminapi\logic\ai\LowcodeLogic;
use think\response\Json;

/**
 * PC前台 AI 控制器
 */
class AiController extends BaseAdminController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['nl2sql', 'lowcode', 'execute', 'tables'];

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
            $logic = new Nl2SqlLogic();
            $result = $logic->convert($prompt);

            return $this->success('转换成功', [
                'sql' => $result['sql'] ?? '',
                'explanation' => $result['explanation'] ?? '',
                'tables' => $result['tables'] ?? [],
                'mode' => $result['mode'] ?? ($result['ai_model'] ?? 'unknown'),
            ]);
        } catch (\Exception $e) {
            return $this->fail('转换失败: ' . $e->getMessage());
        }
    }

    /**
     * 执行 SQL 查询
     * POST /pcapi/ai/execute
     * Body: { sql: "SELECT ..." }
     * Response: { code: 0, data: { rows: [], columns: [] } }
     */
    public function execute(): Json
    {
        $sql = $this->request->param('sql', '');

        if (empty($sql)) {
            return $this->fail('SQL 语句不能为空');
        }

        try {
            $logic = new Nl2SqlLogic();
            $result = $logic->execute($sql);

            return $this->success('执行成功', $result);
        } catch (\Exception $e) {
            return $this->fail('执行失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取数据库表结构
     * GET /pcapi/ai/tables
     * Response: { code: 0, data: [{ name: "fy_user", columns: [] }] }
     */
    public function tables(): Json
    {
        try {
            $logic = new Nl2SqlLogic();
            $tables = $logic->getTables();

            return $this->success('获取成功', $tables);
        } catch (\Exception $e) {
            return $this->fail('获取失败: ' . $e->getMessage());
        }
    }

    /**
     * 低代码保存
     * POST /pcapi/ai/lowcode
     * Body: { page_id: "", config: {} }
     * Response: { code: 0, data: { id: 1, saved: true, created: true/false } }
     */
    public function lowcode(): Json
    {
        $pageId = $this->request->param('page_id', '');
        $config = $this->request->param('config/a', []);

        try {
            $result = LowcodeLogic::save($pageId, $config);
            return $this->success('保存成功', $result);
        } catch (\Exception $e) {
            return $this->fail('保存失败: ' . $e->getMessage());
        }
    }
}

<?php
/**
 * NL2SQL 逻辑层
 * 
 * 基于 AI 的自然语言转 SQL 实现
 * 支持文心一言、通义千问、OpenAI
 */

declare(strict_types=1);

namespace app\adminapi\logic\ai;

use app\service\ai\AiFactory;
use think\facade\Db;
use think\facade\Config;

class Nl2SqlLogic
{
    /**
     * AI 服务实例
     */
    protected $aiService;

    /**
     * 允许执行的 SQL 类型
     */
    protected array $allowedSqlTypes = ['SELECT', 'SHOW', 'DESCRIBE', 'DESC'];

    public function __construct()
    {
        $provider = Config::get('site.ai.provider', 'wenxin');
        $this->aiService = AiFactory::getService($provider);
    }

    /**
     * 自然语言转 SQL
     */
    public function convert(string $prompt): array
    {
        $prompt = trim($prompt);
        if (empty($prompt)) {
            throw new \Exception('问题不能为空');
        }

        // 获取表结构
        $tables = $this->getTables();

        // 检查是否配置了 AI
        $aiConfig = Config::get('site.ai', []);
        $hasAiConfig = !empty($aiConfig['wenxin_ak']) || !empty($aiConfig['api_key']);

        if ($hasAiConfig) {
            try {
                return $this->convertWithAi($prompt, $tables);
            } catch (\Exception $e) {
                // AI 失败时降级到关键词匹配
                return $this->convertWithKeywords($prompt);
            }
        }

        // 无 AI 配置，使用关键词匹配
        return $this->convertWithKeywords($prompt);
    }

    /**
     * 使用 AI 转换
     */
    protected function convertWithAi(string $prompt, array $tables): array
    {
        // 构建提示词
        $systemPrompt = '你是一个 SQL 专家。根据用户的问题和数据库表结构，生成准确的 SQL 查询语句。';
        $systemPrompt .= "\n\n只返回 SQL 语句，不要其他解释。";
        $systemPrompt .= "\n\n注意：";
        $systemPrompt .= "\n1. 只生成 SELECT 查询语句，不要生成 UPDATE/INSERT/DELETE";
        $systemPrompt .= "\n2. 表名使用数据库中的实际表名";
        $systemPrompt .= "\n3. 字段名使用数据库中的实际字段名";
        $systemPrompt .= "\n4. 使用 LIMIT 限制返回行数，默认 100 行";
        $systemPrompt .= "\n5. 如果涉及多表查询，确保关联条件正确";

        $userPrompt = "数据库表结构：\n" . $this->formatTables($tables);
        $userPrompt .= "\n\n用户问题：" . $prompt;
        $userPrompt .= "\n\n请生成 SQL 语句：";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        $result = $this->aiService->chat($messages);

        $sql = $this->extractSql($result['content'] ?? '');

        return [
            'sql' => $sql,
            'explanation' => $this->explainSql($sql, $prompt),
            'tables' => $this->detectTables($sql, $tables),
            'executed' => false,
            'ai_model' => Config::get('site.ai.provider', 'wenxin'),
        ];
    }

    /**
     * 使用关键词匹配转换（降级方案）
     */
    protected function convertWithKeywords(string $prompt): array
    {
        $lowerPrompt = mb_strtolower($prompt);

        // 根据关键词生成 SQL
        if (str_contains($lowerPrompt, '用户') || str_contains($lowerPrompt, 'user')) {
            return [
                'sql' => "SELECT `id`, `username`, `nickname`, `email`, `status`, `create_time`\nFROM `fy_user`\nWHERE `status` = 1\nORDER BY `create_time` DESC\nLIMIT 100;",
                'explanation' => '查询所有启用状态的用户，按创建时间倒序排列',
                'tables' => ['fy_user'],
                'executed' => false,
                'mode' => 'keyword',
            ];
        }

        if (str_contains($lowerPrompt, '角色') || str_contains($lowerPrompt, 'role')) {
            return [
                'sql' => "SELECT `id`, `name`, `code`, `status`, `create_time`\nFROM `fy_role`\nWHERE `status` = 1\nORDER BY `id` ASC\nLIMIT 100;",
                'explanation' => '查询所有启用状态的角色',
                'tables' => ['fy_role'],
                'executed' => false,
                'mode' => 'keyword',
            ];
        }

        if (str_contains($lowerPrompt, '订单') || str_contains($lowerPrompt, 'order')) {
            return [
                'sql' => "SELECT `id`, `order_no`, `user_id`, `amount`, `status`, `create_time`\nFROM `fy_pay_order`\nWHERE `status` IN (1, 2)\nORDER BY `create_time` DESC\nLIMIT 100;",
                'explanation' => '查询最近100条进行中的订单',
                'tables' => ['fy_pay_order'],
                'executed' => false,
                'mode' => 'keyword',
            ];
        }

        if (str_contains($lowerPrompt, '统计') || str_contains($lowerPrompt, 'count')) {
            return [
                'sql' => "SELECT COUNT(*) AS total_count, \n  DATE_FORMAT(`create_time`, '%Y-%m') AS month\nFROM `fy_user`\nGROUP BY DATE_FORMAT(`create_time`, '%Y-%m')\nORDER BY month DESC\nLIMIT 12;",
                'explanation' => '按月统计用户注册数量',
                'tables' => ['fy_user'],
                'executed' => false,
                'mode' => 'keyword',
            ];
        }

        // 默认查询用户表
        return [
            'sql' => "SELECT *\nFROM `fy_user`\nWHERE `status` = 1\nLIMIT 100;",
            'explanation' => '查询用户列表，默认返回前100条',
            'tables' => ['fy_user'],
            'executed' => false,
            'mode' => 'keyword',
        ];
    }

    /**
     * 执行 SQL 查询
     */
    public function execute(string $sql): array
    {
        $sql = trim($sql);

        // 安全检查：只允许 SELECT 类型
        $sqlType = strtoupper(explode(' ', $sql)[0]);
        if (!in_array($sqlType, $this->allowedSqlTypes)) {
            throw new \Exception('只允许执行 SELECT 查询语句');
        }

        // 防止 SQL 注入：检查危险关键字
        $dangerous = ['DROP', 'TRUNCATE', 'ALTER', 'CREATE', 'INSERT', 'UPDATE', 'DELETE', 'GRANT', 'REVOKE'];
        foreach ($dangerous as $keyword) {
            if (preg_match('/\b' . $keyword . '\b/i', $sql)) {
                throw new \Exception('不允许执行危险 SQL 操作');
            }
        }

        try {
            $result = Db::query($sql);

            return [
                'success' => true,
                'data' => $result,
                'row_count' => count($result),
                'columns' => !empty($result) ? array_keys($result[0] ?? []) : [],
            ];
        } catch (\Exception $e) {
            throw new \Exception('SQL 执行失败：' . $e->getMessage());
        }
    }

    /**
     * 获取数据库表结构
     */
    public function getTables(): array
    {
        try {
            $tables = Db::query('SHOW TABLE STATUS WHERE Name LIKE "fy_%"');

            $result = [];
            foreach ($tables as $table) {
                $tableName = $table['Name'];
                $columns = Db::query("SHOW FULL COLUMNS FROM `{$tableName}`");

                $result[] = [
                    'name' => $tableName,
                    'comment' => $table['Comment'] ?? '',
                    'columns' => array_map(function ($col) {
                        return [
                            'name' => $col['Field'],
                            'type' => $col['Type'],
                            'comment' => $col['Comment'] ?: $col['Field'],
                            'null' => $col['Null'],
                            'key' => $col['Key'],
                            'default' => $col['Default'],
                        ];
                    }, $columns),
                ];
            }

            return $result;
        } catch (\Exception $e) {
            // 如果无法连接数据库，返回默认表结构
            return $this->getDefaultTables();
        }
    }

    /**
     * 获取默认表结构（当无法连接数据库时）
     */
    protected function getDefaultTables(): array
    {
        return [
            [
                'name' => 'fy_user',
                'comment' => '用户表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '用户ID'],
                    ['name' => 'username', 'type' => 'varchar', 'comment' => '用户名'],
                    ['name' => 'nickname', 'type' => 'varchar', 'comment' => '昵称'],
                    ['name' => 'email', 'type' => 'varchar', 'comment' => '邮箱'],
                    ['name' => 'mobile', 'type' => 'varchar', 'comment' => '手机号'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态（1启用0禁用）'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
            [
                'name' => 'fy_role',
                'comment' => '角色表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '角色ID'],
                    ['name' => 'name', 'type' => 'varchar', 'comment' => '角色名'],
                    ['name' => 'code', 'type' => 'varchar', 'comment' => '角色代码'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
            [
                'name' => 'fy_pay_order',
                'comment' => '订单表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => '订单ID'],
                    ['name' => 'order_no', 'type' => 'varchar', 'comment' => '订单号'],
                    ['name' => 'user_id', 'type' => 'int', 'comment' => '用户ID'],
                    ['name' => 'amount', 'type' => 'decimal', 'comment' => '金额'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ],
            ],
        ];
    }

    /**
     * 格式化表结构为字符串
     */
    protected function formatTables(array $tables): string
    {
        $result = [];
        foreach ($tables as $table) {
            $columns = [];
            foreach ($table['columns'] as $col) {
                $columns[] = "  - {$col['name']} ({$col['type']})" . ($col['comment'] ? " # {$col['comment']}" : '');
            }
            $result[] = "表: {$table['name']}" . ($table['comment'] ? " [{$table['comment']}]" : '') . "\n" . implode("\n", $columns);
        }
        return implode("\n\n", $result);
    }

    /**
     * 从 AI 响应中提取 SQL
     */
    protected function extractSql(string $content): string
    {
        // 尝试提取 ```sql ... ``` 包裹的内容
        if (preg_match('/```sql\s*(.*?)\s*```/is', $content, $matches)) {
            return trim($matches[1]);
        }

        // 尝试直接提取 SELECT 开头的语句
        if (preg_match('/(SELECT[\s\S]+?)(?:;|$)/i', $content, $matches)) {
            return trim($matches[0]);
        }

        return trim($content);
    }

    /**
     * 检测 SQL 涉及的表
     */
    protected function detectTables(string $sql, array $tables): array
    {
        $usedTables = [];
        foreach ($tables as $table) {
            if (stripos($sql, $table['name']) !== false) {
                $usedTables[] = $table['name'];
            }
        }
        return $usedTables;
    }

    /**
     * 解释 SQL
     */
    protected function explainSql(string $sql, string $prompt): string
    {
        // 简单分析 SQL 结构
        $explanations = [];

        if (stripos($sql, 'COUNT') !== false) {
            $explanations[] = '统计查询';
        }

        if (stripos($sql, 'GROUP BY') !== false) {
            $explanations[] = '分组统计';
        }

        if (stripos($sql, 'ORDER BY') !== false) {
            $explanations[] = '排序';
        }

        if (stripos($sql, 'WHERE') !== false) {
            $explanations[] = '条件筛选';
        }

        if (stripos($sql, 'JOIN') !== false) {
            $explanations[] = '多表关联';
        }

        $base = empty($explanations) ? '数据查询' : implode('、', $explanations);
        return "这是一条{$base}，用于回答：{$prompt}";
    }
}

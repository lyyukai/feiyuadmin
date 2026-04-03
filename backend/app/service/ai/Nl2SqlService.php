<?php
/**
 * NL2SQL服务 - 自然语言转SQL
 */

declare(strict_types=1);

namespace app\service\ai;

class Nl2SqlService
{
    protected AiService $aiService;
    protected array $dbSchema = [];

    public function __construct()
    {
        $this->aiService = AiFactory::getService();
    }

    /**
     * 设置数据库表结构
     */
    public function setSchema(array $tables): void
    {
        $this->dbSchema = $tables;
    }

    /**
     * 自然语言转SQL
     * @param string $question 用户问题
     * @param string $sqlType SELECT/INSERT/UPDATE/DELETE
     * @return array ['sql' => 'SELECT * FROM ...', 'desc' => '解释']
     */
    public function convert(string $question, string $sqlType = 'SELECT'): array
    {
        // 构建提示词
        $prompt = $this->buildPrompt($question, $sqlType);
        
        // 调用AI
        $messages = [
            ['role' => 'system', 'content' => '你是一个SQL专家，根据用户问题生成SQL查询。'],
            ['role' => 'user', 'content' => $prompt],
        ];
        
        $result = $this->aiService->chat($messages);
        
        return [
            'sql' => $this->extractSql($result['content']),
            'desc' => $this->explainSql($result['content']),
            'raw' => $result['content'],
        ];
    }

    /**
     * 构建提示词
     */
    protected function buildPrompt(string $question, string $sqlType): string
    {
        $schemaStr = '';
        foreach ($this->dbSchema as $table) {
            $schemaStr .= "表: {$table['name']} 字段: " . implode(', ', $table['columns']) . "\n";
        }
        
        return <<<SQL
请根据以下数据库结构和用户问题，生成SQL语句。

数据库结构:
{$schemaStr}

用户问题: {$question}
SQL类型: {$sqlType}

请只返回SQL语句，不需要其他解释。
SQL:
SQL;
    }

    /**
     * 提取SQL
     */
    protected function extractSql(string $content): string
    {
        // 尝试提取 ```sql ... ``` 包裹的内容
        if (preg_match('/```sql\s*(.*?)\s*```/is', $content, $matches)) {
            return trim($matches[1]);
        }
        
        // 尝试直接提取 SELECT/UPDATE/INSERT/DELETE 开头的语句
        if (preg_match('/(SELECT|UPDATE|INSERT|DELETE)[^;]+;/is', $content, $matches)) {
            return trim($matches[0]);
        }
        
        return trim($content);
    }

    /**
     * 解释SQL
     */
    protected function explainSql(string $content): string
    {
        // 去掉SQL代码块，保留解释
        $explain = preg_replace('/```sql.*?```/is', '', $content);
        return trim($explain) ?: '这是一条数据查询语句';
    }
}

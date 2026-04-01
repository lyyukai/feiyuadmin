<?php
declare(strict_types=1);

namespace app\adminapi\service;

use app\model\GeneratorConfig;
use app\model\GeneratorTemplate;
use think\exception\PDOException;

/**
 * 代码生成器服务
 */
class GeneratorService
{
    private ?GeneratorConfig $dbConfig = null;
    private ?\PDO $pdo = null;

    /**
     * 设置数据库配置
     */
    public function setDbConfig(GeneratorConfig $config): void
    {
        $this->dbConfig = $config;
        $this->connect();
    }

    /**
     * 连接数据库
     */
    private function connect(): void
    {
        if (!$this->dbConfig) {
            throw new \InvalidArgumentException('数据库配置不存在');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->dbConfig->host,
            $this->dbConfig->port,
            $this->dbConfig->database_name,
            $this->dbConfig->charset
        );

        $this->pdo = new \PDO($dsn, $this->dbConfig->username, $this->dbConfig->password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * 测试数据库连接
     */
    public function testConnection(array $params): bool
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $params['host'] ?? '127.0.0.1',
            $params['port'] ?? 3306,
            $params['database_name'] ?? '',
            $params['charset'] ?? 'utf8mb4'
        );

        try {
            $pdo = new \PDO($dsn, $params['username'] ?? 'root', $params['password'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            $pdo = null;
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException('数据库连接失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取所有数据表
     */
    public function getTables(): array
    {
        if (!$this->pdo) {
            throw new \RuntimeException('数据库未连接');
        }

        $prefix = $this->dbConfig->prefix ?? '';
        $stmt = $this->pdo->prepare("SHOW TABLE STATUS WHERE Name LIKE ?");
        $stmt->execute([$prefix . '%']);
        $tables = $stmt->fetchAll();

        $result = [];
        foreach ($tables as $table) {
            $name = $table['Name'];
            // 去除前缀
            if ($prefix && strpos($name, $prefix) === 0) {
                $name = substr($name, strlen($prefix));
            }
            $result[] = [
                'name' => $name,
                'comment' => $table['Comment'] ?: $name,
                'engine' => $table['Engine'] ?? '',
                'rows' => $table['Rows'] ?? 0,
                'create_time' => $table['Create_time'] ?? '',
            ];
        }

        return $result;
    }

    /**
     * 获取表结构
     */
    public function getTableColumns(string $tableName): array
    {
        if (!$this->pdo) {
            throw new \RuntimeException('数据库未连接');
        }

        $prefix = $this->dbConfig->prefix ?? '';
        $fullTable = $prefix . $tableName;

        $stmt = $this->pdo->prepare("SHOW FULL COLUMNS FROM `{$fullTable}`");
        $stmt->execute();
        $columns = $stmt->fetchAll();

        $result = [];
        foreach ($columns as $col) {
            $type = $col['Type'];
            $comment = $col['Comment'] ?: '';

            // 解析字段类型
            $unsigned = stripos($type, 'unsigned') !== false;
            $type = preg_replace('/\s*unsigned\s*/i', '', $type);

            // 判断PHP类型
            $phpType = 'string';
            if (preg_match('/^(int|bigint|tinyint|smallint|mediumint)/i', $type)) {
                $phpType = 'integer';
            } elseif (preg_match('/^(float|double|decimal)/i', $type)) {
                $phpType = 'float';
            }

            $result[] = [
                'name' => $col['Field'],
                'type' => $type,
                'php_type' => $phpType,
                'comment' => $comment,
                'nullable' => $col['Null'] === 'YES',
                'key' => $col['Key'],
                'default' => $col['Default'],
                'extra' => $col['Extra'],
                'unsigned' => $unsigned,
            ];
        }

        return $result;
    }

    /**
     * 生成代码
     */
    public function generate(array $params): array
    {
        $tableName = $params['table_name'] ?? '';
        $module = $params['module'] ?? 'admin';
        $genTypes = $params['gen_types'] ?? [];

        if (empty($tableName)) {
            throw new \InvalidArgumentException('表名不能为空');
        }

        // 获取表信息
        $columns = $this->getTableColumns($tableName);
        if (empty($columns)) {
            throw new \RuntimeException('表结构获取失败');
        }

        // 转换表名为类名
        $className = $this->toPascalCase($tableName);
        $lowerName = $this->toCamelCase($tableName);
        $comment = $this->getTableComment($tableName);

        // 获取模板
        $templates = GeneratorTemplate::getDefaults();

        $files = [];
        foreach ($templates as $template) {
            if (!in_array($template['code'], $genTypes)) {
                continue;
            }

            $content = $this->renderTemplate($template['content'], [
                'table_name' => $this->dbConfig->prefix . $tableName,
                'class_name' => $className,
                'lower_name' => $lowerName,
                'module' => $module,
                'comment' => $comment,
                'columns' => $columns,
                'create_time' => date('Y-m-d H:i:s'),
            ], $columns);

            $files[] = [
                'name' => $this->getFileName($template['code'], $tableName, $module),
                'path' => $this->getFilePath($template['code'], $tableName, $module),
                'content' => $content,
                'type' => $template['type'],
            ];
        }

        return $files;
    }

    /**
     * 渲染模板
     */
    private function renderTemplate(string $template, array $vars, array $columns): string
    {
        // 替换简单变量
        foreach ($vars as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $template = str_replace('{{' . $key . '}}', (string) $value, $template);
            }
        }

        // 处理表单字段
        $formFields = [];
        $formDefaults = [];
        $formAssignEdit = [];
        $formItems = [];
        $formRules = [];
        $tableColumns = [];
        $searchWhere = '';
        $assignFields = '';
        $assignFieldsEdit = '';
        $validateRules = '';
        $validateRuleList = [];
        $validateMessages = [];
        $typeCast = [];

        foreach ($columns as $col) {
            $name = $col['name'];
            $comment = $col['comment'] ?: $name;
            $phpType = $col['php_type'];

            // 跳过非编辑字段
            if (in_array($name, ['id', 'create_time', 'update_time', 'delete_time'])) {
                $typeCast[] = sprintf("        '%s' => '%s'", $name, $phpType === 'integer' ? 'integer' : 'string');
                continue;
            }

            // 表单字段
            $formFields[] = $name;
            $formDefaults[] = sprintf('        %s: %s', $name, $this->getDefaultValue($col));
            $formAssignEdit[] = sprintf('        %s: row.%s ?? \'\'', $name, $name);

            // 表格列
            $tableColumns[] = sprintf('        <el-table-column prop=\"%s\" label=\"%s\" min-width=\"120\" />', $name, $comment);

            // 搜索条件
            if (in_array($phpType, ['string'])) {
                $searchWhere .= sprintf("        if (!empty(\$keyword)) {\n            \$query->whereLike('%s', \"%%{\$keyword}%%\");\n        }\n\n", $name);
            }

            // 赋值语句
            if (stripos($name, 'time') !== false || stripos($name, 'date') !== false) {
                $assignFields .= sprintf("        \$model->%s = \$params['%s'] ?? '';\n", $name, $name);
                $assignFieldsEdit .= sprintf("        if (isset(\$params['%s'])) \$model->%s = \$params['%s'];\n", $name, $name, $name);
            } elseif ($phpType === 'integer') {
                $assignFields .= sprintf("        \$model->%s = (int) (\$params['%s'] ?? 0);\n", $name, $name);
                $assignFieldsEdit .= sprintf("        if (isset(\$params['%s'])) \$model->%s = (int) \$params['%s'];\n", $name, $name, $name);
            } else {
                $assignFields .= sprintf("        \$model->%s = \$params['%s'] ?? '';\n", $name, $name);
                $assignFieldsEdit .= sprintf("        if (isset(\$params['%s'])) \$model->%s = \$params['%s'];\n", $name, $name, $name);
            }

            // 验证规则
            if (!in_array($name, ['id', 'sort', 'status'])) {
                $validateRuleList[] = sprintf("        '%s' => '%s',", $name, $this->getValidateRule($col));
                $validateMessages[] = sprintf("        '%s' => '%s',", $name, '请输入' . $comment);
            }

            // 类型转换
            $typeCast[] = sprintf("        '%s' => '%s'", $name, $phpType === 'integer' ? 'integer' : 'string');

            // 表单项
            if ($phpType === 'integer') {
                if (stripos($name, 'status') !== false) {
                    $formItems[] = sprintf('        <el-form-item label=\"%s\" prop=\"%s\">\n          <el-radio-group v-model=\"form.%s\">\n            <el-radio :value=\"1\">启用</el-radio>\n            <el-radio :value=\"0\">禁用</el-radio>\n          </el-radio-group>\n        </el-form-item>', $comment, $name, $name);
                    $formRules[] = sprintf('        %s: [{ required: true, message: \'请选择%s\', trigger: \'change\' }],', $name, $comment);
                } else {
                    $formItems[] = sprintf('        <el-form-item label=\"%s\" prop=\"%s\">\n          <el-input-number v-model=\"form.%s\" :min=\"0\" />\n        </el-form-item>', $comment, $name, $name);
                    $formRules[] = sprintf('        %s: [{ required: true, message: \'请输入%s\', trigger: \'blur\' }],', $name, $comment);
                }
            } else {
                $formItems[] = sprintf('        <el-form-item label=\"%s\" prop=\"%s\">\n          <el-input v-model=\"form.%s\" placeholder=\"请输入%s\" />\n        </el-form-item>', $comment, $name, $name, $comment);
                $formRules[] = sprintf('        %s: [{ required: true, message: \'请输入%s\', trigger: \'blur\' }],', $name, $comment);
            }
        }

        // 替换模板片段
        $template = str_replace('{{search_where}}', $searchWhere ?: "        // 无搜索条件", $template);
        $template = str_replace('{{assign_fields}}', $assignFields ?: "        // 字段赋值", $template);
        $template = str_replace('{{assign_fields_edit}}', $assignFieldsEdit ?: "        // 字段更新", $template);
        $template = str_replace('{{validate_rules}}', $validateRules ?: "// 无需验证", $template);
        $template = str_replace('{{validate_rule_list}}', implode("\n", $validateRuleList) ?: "        // ", $template);
        $template = str_replace('{{validate_messages}}', implode("\n", $validateMessages) ?: "        // ", $template);
        $template = str_replace('{{type_cast}}', implode("\n", $typeCast) ?: "        // ", $template);
        $template = str_replace('{{form_fields}}', ', ' . implode(', ', $formFields), $template);
        $template = str_replace('{{form_defaults}}', ', ' . implode(",\n            ", $formDefaults), $template);
        $template = str_replace('{{form_assign_edit}}', ', ' . implode(",\n            ", $formAssignEdit), $template);
        $template = str_replace('{{table_columns}}', implode("\n", $tableColumns), $template);
        $template = str_replace('{{form_items}}', implode("\n", $formItems), $template);
        $template = str_replace('{{form_rules}}', implode("\n", $formRules), $template);
        $template = str_replace('{{title_field}}', $columns[1]['name'] ?? 'id', $template);

        return $template;
    }

    /**
     * 获取文件路径
     */
    private function getFilePath(string $code, string $tableName, string $module): string
    {
        $className = $this->toPascalCase($tableName);
        $lowerName = $this->toCamelCase($tableName);

        $paths = [
            'controller' => "app/adminapi/controller/{$module}/{$className}Controller.php",
            'logic' => "app/adminapi/logic/{$module}/{$className}Logic.php",
            'model' => "app/model/{$className}.php",
            'validate' => "app/adminapi/validate/{$module}/{$className}Validate.php",
            'route' => "route/{$module}_{$lowerName}.php",
            'vue_list' => "frontend/src/views/{$module}/{$lowerName}/index.vue",
            'api_js' => "frontend/src/api/{$module}/{$lowerName}.js",
        ];

        return $paths[$code] ?? "generated/{$code}/{$tableName}";
    }

    /**
     * 获取文件名
     */
    private function getFileName(string $code, string $tableName, string $module): string
    {
        $className = $this->toPascalCase($tableName);
        $lowerName = $this->toCamelCase($tableName);

        $names = [
            'controller' => "{$className}Controller.php",
            'logic' => "{$className}Logic.php",
            'model' => "{$className}.php",
            'validate' => "{$className}Validate.php",
            'route' => "{$module}_{$lowerName}.php",
            'vue_list' => "index.vue",
            'api_js' => "{$lowerName}.js",
        ];

        return $names[$code] ?? "{$tableName}_{$code}";
    }

    /**
     * 帕斯卡命名
     */
    private function toPascalCase(string $str): string
    {
        return str_replace(['_', '-'], '', ucwords($str, '_-'));
    }

    /**
     * 驼峰命名
     */
    private function toCamelCase(string $str): string
    {
        return lcfirst($this->toPascalCase($str));
    }

    /**
     * 获取表注释
     */
    private function getTableComment(string $tableName): string
    {
        $columns = $this->getTableColumns($tableName);
        return ''; // 从表注释中获取
    }

    /**
     * 获取默认值
     */
    private function getDefaultValue(array $col): string
    {
        if ($col['default'] !== null) {
            if ($col['php_type'] === 'integer') {
                return $col['default'];
            }
            return "'" . $col['default'] . "'";
        }
        if ($col['php_type'] === 'integer') {
            return 0;
        }
        return "''";
    }

    /**
     * 获取验证规则
     */
    private function getValidateRule(array $col): string
    {
        if (!$col['nullable']) {
            return 'require';
        }
        return '';
    }
}

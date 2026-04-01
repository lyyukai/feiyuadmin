<?php
declare(strict_types=1);

namespace app\adminapi\logic\generator;

use app\adminapi\service\GeneratorService;
use app\common\service\JsonService;
use app\model\GeneratorConfig;
use app\model\GeneratorTemplate;

/**
 * 代码生成器逻辑
 */
class GeneratorLogic
{
    /**
     * 获取数据库配置列表
     */
    public static function getConfigList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;

        $query = GeneratorConfig::order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 获取数据库配置信息
     */
    public static function getConfigInfo(int $id): array
    {
        $model = GeneratorConfig::find($id);
        if (empty($model)) {
            JsonService::throwFail('配置不存在');
        }
        return $model->toArray();
    }

    /**
     * 添加数据库配置
     */
    public static function addConfig(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('配置名称不能为空');
        }
        if (empty($params['host'])) {
            JsonService::throwFail('数据库主机不能为空');
        }
        if (empty($params['database_name'])) {
            JsonService::throwFail('数据库名不能为空');
        }

        // 测试连接
        $service = new GeneratorService();
        $service->testConnection($params);

        // 如果是默认配置，先取消其他默认
        if (!empty($params['is_default'])) {
            GeneratorConfig::where('is_default', 1)->update(['is_default' => 0]);
        }

        $model = new GeneratorConfig();
        $model->name = $params['name'];
        $model->host = $params['host'] ?? '127.0.0.1';
        $model->port = (int) ($params['port'] ?? 3306);
        $model->database_name = $params['database_name'];
        $model->username = $params['username'] ?? 'root';
        $model->password = $params['password'] ?? '';
        $model->charset = $params['charset'] ?? 'utf8mb4';
        $model->prefix = $params['prefix'] ?? '';
        $model->is_default = !empty($params['is_default']) ? 1 : 0;
        $model->status = isset($params['status']) ? (int) $params['status'] : 1;
        $model->save();
    }

    /**
     * 编辑数据库配置
     */
    public static function editConfig(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $model = GeneratorConfig::find($id);
        if (empty($model)) {
            JsonService::throwFail('配置不存在');
        }

        // 如果是默认配置，先取消其他默认
        if (!empty($params['is_default'])) {
            GeneratorConfig::where('is_default', 1)->update(['is_default' => 0]);
        }

        $fields = ['name', 'host', 'port', 'database_name', 'username', 'password', 'charset', 'prefix', 'is_default', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'is_default' || $field === 'status' || $field === 'port') {
                    $model->$field = (int) $params[$field];
                } else {
                    $model->$field = $params[$field];
                }
            }
        }
        $model->save();
    }

    /**
     * 删除数据库配置
     */
    public static function deleteConfig(int $id): void
    {
        $model = GeneratorConfig::find($id);
        if (empty($model)) {
            JsonService::throwFail('配置不存在');
        }
        $model->delete();
    }

    /**
     * 测试数据库连接
     */
    public static function testConnection(array $params): array
    {
        $service = new GeneratorService();
        $service->testConnection($params);
        return ['success' => true, 'message' => '连接成功'];
    }

    /**
     * 获取模板列表
     */
    public static function getTemplateList(array $params): array
    {
        $type = $params['type'] ?? '';

        $query = GeneratorTemplate::order('sort', 'asc');
        if ($type) {
            $query->where('type', $type);
        }

        $total = $query->count();
        $list = $query->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
        ];
    }

    /**
     * 获取模板信息
     */
    public static function getTemplateInfo(int $id): array
    {
        $model = GeneratorTemplate::find($id);
        if (empty($model)) {
            JsonService::throwFail('模板不存在');
        }
        return $model->toArray();
    }

    /**
     * 添加模板
     */
    public static function addTemplate(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('模板名称不能为空');
        }
        if (empty($params['code'])) {
            JsonService::throwFail('模板代码不能为空');
        }
        if (empty($params['type'])) {
            JsonService::throwFail('模板类型不能为空');
        }
        if (empty($params['content'])) {
            JsonService::throwFail('模板内容不能为空');
        }

        $model = new GeneratorTemplate();
        $model->name = $params['name'];
        $model->code = $params['code'];
        $model->type = $params['type'];
        $model->content = $params['content'];
        $model->sort = (int) ($params['sort'] ?? 0);
        $model->is_default = !empty($params['is_default']) ? 1 : 0;
        $model->status = isset($params['status']) ? (int) $params['status'] : 1;
        $model->save();
    }

    /**
     * 编辑模板
     */
    public static function editTemplate(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $model = GeneratorTemplate::find($id);
        if (empty($model)) {
            JsonService::throwFail('模板不存在');
        }

        $fields = ['name', 'code', 'type', 'content', 'sort', 'is_default', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if (in_array($field, ['sort', 'is_default', 'status'])) {
                    $model->$field = (int) $params[$field];
                } else {
                    $model->$field = $params[$field];
                }
            }
        }
        $model->save();
    }

    /**
     * 删除模板
     */
    public static function deleteTemplate(int $id): void
    {
        $model = GeneratorTemplate::find($id);
        if (empty($model)) {
            JsonService::throwFail('模板不存在');
        }
        $model->delete();
    }

    /**
     * 获取数据表列表
     */
    public static function getTableList(int $configId): array
    {
        $config = GeneratorConfig::find($configId);
        if (empty($config)) {
            JsonService::throwFail('数据库配置不存在');
        }

        $service = new GeneratorService();
        $service->setDbConfig($config);

        return $service->getTables();
    }

    /**
     * 获取表结构
     */
    public static function getTableColumns(int $configId, string $tableName): array
    {
        $config = GeneratorConfig::find($configId);
        if (empty($config)) {
            JsonService::throwFail('数据库配置不存在');
        }

        $service = new GeneratorService();
        $service->setDbConfig($config);

        return $service->getTableColumns($tableName);
    }

    /**
     * 预览代码
     */
    public static function preview(array $params): array
    {
        $configId = (int) ($params['config_id'] ?? 0);
        $tableName = $params['table_name'] ?? '';
        $module = $params['module'] ?? 'admin';
        $genTypes = $params['gen_types'] ?? [];

        if (empty($configId)) {
            JsonService::throwFail('请选择数据库配置');
        }
        if (empty($tableName)) {
            JsonService::throwFail('请选择数据表');
        }

        $config = GeneratorConfig::find($configId);
        if (empty($config)) {
            JsonService::throwFail('数据库配置不存在');
        }

        $service = new GeneratorService();
        $service->setDbConfig($config);

        return $service->generate([
            'table_name' => $tableName,
            'module' => $module,
            'gen_types' => $genTypes,
        ]);
    }

    /**
     * 生成代码
     */
    public static function generate(array $params): array
    {
        $configId = (int) ($params['config_id'] ?? 0);
        $tableName = $params['table_name'] ?? '';
        $module = $params['module'] ?? 'admin';
        $genTypes = $params['gen_types'] ?? [];

        if (empty($configId)) {
            JsonService::throwFail('请选择数据库配置');
        }
        if (empty($tableName)) {
            JsonService::throwFail('请选择数据表');
        }

        $config = GeneratorConfig::find($configId);
        if (empty($config)) {
            JsonService::throwFail('数据库配置不存在');
        }

        $service = new GeneratorService();
        $service->setDbConfig($config);

        return $service->generate([
            'table_name' => $tableName,
            'module' => $module,
            'gen_types' => $genTypes,
        ]);
    }

    /**
     * 获取可用生成类型
     */
    public static function getGenTypes(): array
    {
        return [
            ['code' => 'controller', 'name' => 'Controller', 'type' => 'backend_php'],
            ['code' => 'logic', 'name' => 'Logic', 'type' => 'backend_php'],
            ['code' => 'model', 'name' => 'Model', 'type' => 'backend_php'],
            ['code' => 'validate', 'name' => 'Validate', 'type' => 'backend_php'],
            ['code' => 'route', 'name' => '路由', 'type' => 'backend_php'],
            ['code' => 'vue_list', 'name' => 'Vue列表页', 'type' => 'frontend_vue'],
            ['code' => 'api_js', 'name' => 'API接口文件', 'type' => 'frontend_vue'],
        ];
    }
}

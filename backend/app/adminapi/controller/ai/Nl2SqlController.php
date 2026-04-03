<?php
/**
 * NL2SQL控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\ai;

use app\adminapi\controller\BaseAdminController;
use app\service\ai\Nl2SqlService;
use think\response\Json;

class Nl2SqlController extends BaseAdminController
{
    /**
     * 自然语言转SQL
     */
    public function convert(): Json
    {
        $question = $this->request->param('question', '');
        $sqlType = $this->request->param('sql_type', 'SELECT');
        
        if (empty($question)) {
            return $this->fail('问题不能为空');
        }

        try {
            $nl2sql = new Nl2SqlService();
            
            // TODO: 从数据库获取表结构
            $tables = [
                ['name' => 'sys_user', 'columns' => ['id', 'username', 'password', 'status', 'create_time']],
                ['name' => 'sys_role', 'columns' => ['id', 'name', 'code', 'status']],
            ];
            $nl2sql->setSchema($tables);
            
            $result = $nl2sql->convert($question, $sqlType);
            
            return $this->success('转换成功', $result);
        } catch (\Exception $e) {
            return $this->fail('转换失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取支持的数据库表
     */
    public function tables(): Json
    {
        // TODO: 从数据库读取表结构
        $tables = [
            [
                'name' => 'sys_user',
                'comment' => '用户表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => 'ID'],
                    ['name' => 'username', 'type' => 'varchar', 'comment' => '用户名'],
                    ['name' => 'nickname', 'type' => 'varchar', 'comment' => '昵称'],
                    ['name' => 'status', 'type' => 'tinyint', 'comment' => '状态'],
                    ['name' => 'create_time', 'type' => 'datetime', 'comment' => '创建时间'],
                ]
            ],
            [
                'name' => 'sys_role',
                'comment' => '角色表',
                'columns' => [
                    ['name' => 'id', 'type' => 'int', 'comment' => 'ID'],
                    ['name' => 'name', 'type' => 'varchar', 'comment' => '角色名'],
                    ['name' => 'code', 'type' => 'varchar', 'comment' => '角色代码'],
                ]
            ],
        ];
        
        return $this->success('获取成功', $tables);
    }
}

<?php
// +----------------------------------------------------------------------
// | 操作日志表 sys_log
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysLogTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_log', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '操作日志表',
        ]);

        $table->addColumn('user_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '用户ID'])
              ->addColumn('username', 'string', ['limit' => 50, 'null' => true, 'comment' => '用户名'])
              ->addColumn('method', 'string', ['limit' => 10, 'comment' => '请求方法'])
              ->addColumn('url', 'string', ['limit' => 500, 'comment' => '请求地址'])
              ->addColumn('ip', 'string', ['limit' => 50, 'null' => true, 'comment' => 'IP地址'])
              ->addColumn('location', 'string', ['limit' => 255, 'null' => true, 'comment' => '操作地点'])
              ->addColumn('user_agent', 'string', ['limit' => 500, 'null' => true, 'comment' => 'UserAgent'])
              ->addColumn('param', 'text', ['null' => true, 'comment' => '请求参数'])
              ->addColumn('result', 'text', ['null' => true, 'comment' => '返回结果'])
              ->addColumn('error', 'text', ['null' => true, 'comment' => '错误信息'])
              ->addColumn('duration', 'integer', ['signed' => false, 'default' => 0, 'comment' => '执行时长(ms)'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '操作时间'])
              ->addIndex('user_id')
              ->addIndex('create_time')
              ->addIndex('url')
              ->create();
    }
}

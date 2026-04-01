<?php
// +----------------------------------------------------------------------
// | 登录日志表 sys_login_log
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysLoginLogTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_login_log', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '登录日志表',
        ]);

        $table->addColumn('username', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户名'])
              ->addColumn('status', 'string', ['limit' => 20, 'default' => '', 'comment' => '状态: success=成功, fail=失败'])
              ->addColumn('ip', 'string', ['limit' => 50, 'null' => true, 'comment' => 'IP地址'])
              ->addColumn('location', 'string', ['limit' => 255, 'null' => true, 'comment' => '登录地点'])
              ->addColumn('user_agent', 'string', ['limit' => 500, 'null' => true, 'comment' => 'UserAgent'])
              ->addColumn('msg', 'string', ['limit' => 255, 'null' => true, 'comment' => '提示信息'])
              ->addColumn('login_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '登录时间'])
              ->addIndex('username')
              ->addIndex('login_time')
              ->addIndex('ip')
              ->create();
    }
}

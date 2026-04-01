<?php
// +----------------------------------------------------------------------
// | 管理员表 sys_user
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysUserTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_user', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '管理员表',
        ]);

        $table->addColumn('username', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户名'])
              ->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码'])
              ->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '昵称'])
              ->addColumn('realname', 'string', ['limit' => 50, 'default' => '', 'comment' => '真实姓名'])
              ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
              ->addColumn('mobile', 'string', ['limit' => 20, 'null' => true, 'comment' => '手机号'])
              ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像'])
              ->addColumn('dept_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '部门ID'])
              ->addColumn('post_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '岗位ID'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('login_ip', 'string', ['limit' => 50, 'null' => true, 'comment' => '最后登录IP'])
              ->addColumn('login_time', 'datetime', ['null' => true, 'comment' => '最后登录时间'])
              ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addColumn('delete_time', 'datetime', ['null' => true, 'comment' => '删除时间'])
              ->addIndex('username', ['unique' => true])
              ->addIndex('dept_id')
              ->addIndex('status')
              ->create();
    }
}

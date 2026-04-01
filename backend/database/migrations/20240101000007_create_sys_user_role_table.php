<?php
// +----------------------------------------------------------------------
// | 用户角色关联表 sys_user_role
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysUserRoleTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_user_role', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '用户角色关联表',
            'id' => false,
        ]);

        $table->addColumn('user_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '用户ID'])
              ->addColumn('role_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '角色ID'])
              ->addPrimaryKey(['user_id', 'role_id'])
              ->addIndex('user_id')
              ->addIndex('role_id')
              ->create();
    }
}

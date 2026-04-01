<?php
// +----------------------------------------------------------------------
// | 角色菜单关联表 sys_role_menu
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysRoleMenuTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_role_menu', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '角色菜单关联表',
            'id' => false,
        ]);

        $table->addColumn('role_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '角色ID'])
              ->addColumn('menu_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '菜单ID'])
              ->addPrimaryKey(['role_id', 'menu_id'])
              ->addIndex('role_id')
              ->addIndex('menu_id')
              ->create();
    }
}

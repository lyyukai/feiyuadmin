<?php
// +----------------------------------------------------------------------
// | 菜单表 sys_menu (支持二级菜单)
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysMenuTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_menu', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '菜单表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '菜单名称'])
              ->addColumn('pid', 'integer', ['signed' => false, 'default' => 0, 'comment' => '父级ID'])
              ->addColumn('path', 'string', ['limit' => 255, 'default' => '', 'comment' => '路由路径'])
              ->addColumn('component', 'string', ['limit' => 255, 'null' => true, 'comment' => '组件路径'])
              ->addColumn('redirect', 'string', ['limit' => 255, 'null' => true, 'comment' => '重定向路径'])
              ->addColumn('icon', 'string', ['limit' => 50, 'null' => true, 'comment' => '菜单图标'])
              ->addColumn('menu_type', 'string', ['limit' => 10, 'default' => 'menu', 'comment' => '类型: menu=菜单, iframe=iframe, link=外链, button=按钮'])
              ->addColumn('is_hidden', 'boolean', ['default' => 0, 'comment' => '是否隐藏: 0=显示, 1=隐藏'])
              ->addColumn('is_full', 'boolean', ['default' => 0, 'comment' => '是否全屏: 0=否, 1=是'])
              ->addColumn('is_cache', 'boolean', ['default' => 0, 'comment' => '是否缓存: 0=否, 1=是'])
              ->addColumn('permission', 'string', ['limit' => 100, 'null' => true, 'comment' => '权限标识'])
              ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addColumn('delete_time', 'datetime', ['null' => true, 'comment' => '删除时间'])
              ->addIndex('pid')
              ->addIndex('path')
              ->addIndex('permission')
              ->addIndex('status')
              ->create();
    }
}

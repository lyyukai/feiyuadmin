<?php
// +----------------------------------------------------------------------
// | 部门表 sys_dept
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysDeptTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_dept', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '部门表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '部门名称'])
              ->addColumn('pid', 'integer', ['signed' => false, 'default' => 0, 'comment' => '父级ID'])
              ->addColumn('path', 'string', ['limit' => 255, 'default' => '', 'comment' => '路径'])
              ->addColumn('leader', 'string', ['limit' => 50, 'null' => true, 'comment' => '负责人'])
              ->addColumn('mobile', 'string', ['limit' => 20, 'null' => true, 'comment' => '联系电话'])
              ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
              ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addColumn('delete_time', 'datetime', ['null' => true, 'comment' => '删除时间'])
              ->addIndex('pid')
              ->addIndex('status')
              ->create();
    }
}

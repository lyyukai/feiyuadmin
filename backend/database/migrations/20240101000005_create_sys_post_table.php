<?php
// +----------------------------------------------------------------------
// | 岗位表 sys_post
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysPostTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_post', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '岗位表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '岗位名称'])
              ->addColumn('code', 'string', ['limit' => 50, 'default' => '', 'comment' => '岗位代码'])
              ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('remark', 'text', ['null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addColumn('delete_time', 'datetime', ['null' => true, 'comment' => '删除时间'])
              ->addIndex('code')
              ->addIndex('status')
              ->create();
    }
}

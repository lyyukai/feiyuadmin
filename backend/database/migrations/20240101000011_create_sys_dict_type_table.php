<?php
// +----------------------------------------------------------------------
// | 数据字典类型表 sys_dict_type
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysDictTypeTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_dict_type', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '数据字典类型表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '字典名称'])
              ->addColumn('type', 'string', ['limit' => 50, 'default' => '', 'comment' => '字典类型'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addIndex('type', ['unique' => true])
              ->addIndex('status')
              ->create();
    }
}

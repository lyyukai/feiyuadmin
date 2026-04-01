<?php
// +----------------------------------------------------------------------
// | 数据字典数据表 sys_dict_data
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysDictDataTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_dict_data', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '数据字典数据表',
        ]);

        $table->addColumn('dict_type', 'string', ['limit' => 50, 'default' => '', 'comment' => '字典类型'])
              ->addColumn('label', 'string', ['limit' => 100, 'default' => '', 'comment' => '字典标签'])
              ->addColumn('value', 'string', ['limit' => 100, 'default' => '', 'comment' => '字典值'])
              ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态: 0=禁用, 1=正常'])
              ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addIndex('dict_type')
              ->addIndex('status')
              ->addIndex(['dict_type', 'value'], ['unique' => false])
              ->create();
    }
}

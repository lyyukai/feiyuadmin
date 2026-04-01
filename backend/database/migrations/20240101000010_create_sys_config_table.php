<?php
// +----------------------------------------------------------------------
// | 参数配置表 sys_config
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysConfigTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_config', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '参数配置表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '配置名称'])
              ->addColumn('group', 'string', ['limit' => 50, 'default' => 'basic', 'comment' => '配置分组'])
              ->addColumn('key', 'string', ['limit' => 100, 'default' => '', 'comment' => '配置键'])
              ->addColumn('value', 'text', ['null' => true, 'comment' => '配置值'])
              ->addColumn('type', 'string', ['limit' => 50, 'default' => 'text', 'comment' => '类型: text, textarea, password, number, radio, checkbox, select, switch, json'])
              ->addColumn('options', 'text', ['null' => true, 'comment' => '选项JSON(用于radio/checkbox/select等)'])
              ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('remark', 'string', ['limit' => 255, 'null' => true, 'comment' => '备注'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addIndex('group')
              ->addIndex('key', ['unique' => true])
              ->create();
    }
}

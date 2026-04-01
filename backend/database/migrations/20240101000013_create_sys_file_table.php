<?php
// +----------------------------------------------------------------------
// | 文件管理表 sys_file
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class CreateSysFileTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sys_file', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '文件管理表',
        ]);

        $table->addColumn('name', 'string', ['limit' => 255, 'default' => '', 'comment' => '文件名'])
              ->addColumn('original', 'string', ['limit' => 255, 'default' => '', 'comment' => '原始文件名'])
              ->addColumn('type', 'string', ['limit' => 20, 'default' => 'file', 'comment' => '类型: image=图片, video=视频, audio=音频, file=文件'])
              ->addColumn('size', 'integer', ['signed' => false, 'default' => 0, 'comment' => '文件大小(字节)'])
              ->addColumn('path', 'string', ['limit' => 255, 'default' => '', 'comment' => '存储路径'])
              ->addColumn('url', 'string', ['limit' => 500, 'null' => true, 'comment' => '访问URL'])
              ->addColumn('extension', 'string', ['limit' => 20, 'null' => true, 'comment' => '文件扩展名'])
              ->addColumn('mime_type', 'string', ['limit' => 100, 'null' => true, 'comment' => 'MIME类型'])
              ->addColumn('user_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '上传用户ID'])
              ->addColumn('storage', 'string', ['limit' => 50, 'default' => 'local', 'comment' => '存储方式: local=本地, oss=阿里云, cos=腾讯云, qiniu=七牛云'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '上传时间'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addColumn('delete_time', 'datetime', ['null' => true, 'comment' => '删除时间'])
              ->addIndex('type')
              ->addIndex('user_id')
              ->addIndex('create_time')
              ->create();
    }
}

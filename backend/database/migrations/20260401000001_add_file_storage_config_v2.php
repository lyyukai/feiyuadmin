<?php
// +----------------------------------------------------------------------
// | 文件上传V2 - 存储配置
// +----------------------------------------------------------------------
use Phinx\Migration\AbstractMigration;

class AddFileStorageConfigV2 extends AbstractMigration
{
    public function change(): void
    {
        // 插入存储配置项
        $this->table('sys_config')
            ->insert([
                // 存储方式配置
                [
                    'name' => 'file_storage_type',
                    'group' => 'file',
                    'key' => 'storage_type',
                    'value' => 'local',
                    'type' => 'radio',
                    'options' => json_encode([
                        ['label' => '本地存储', 'value' => 'local'],
                        ['label' => '阿里云OSS', 'value' => 'oss'],
                        ['label' => '腾讯云COS', 'value' => 'cos'],
                        ['label' => '七牛云', 'value' => 'qiniu'],
                    ]),
                    'sort' => 100,
                    'remark' => '文件存储方式',
                ],
                // 阿里云OSS配置
                [
                    'name' => '阿里云OSS访问密钥ID',
                    'group' => 'file',
                    'key' => 'oss_access_key_id',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 101,
                    'remark' => '阿里云OSS AccessKey ID',
                ],
                [
                    'name' => '阿里云OSS访问密钥',
                    'group' => 'file',
                    'key' => 'oss_access_key_secret',
                    'value' => '',
                    'type' => 'password',
                    'sort' => 102,
                    'remark' => '阿里云OSS AccessKey Secret',
                ],
                [
                    'name' => '阿里云OSS Bucket',
                    'group' => 'file',
                    'key' => 'oss_bucket',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 103,
                    'remark' => 'OSS Bucket名称',
                ],
                [
                    'name' => '阿里云OSS地域',
                    'group' => 'file',
                    'key' => 'oss_region',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 104,
                    'remark' => 'OSS地域节点，如: oss-cn-hangzhou',
                ],
                [
                    'name' => '阿里云OSS域名',
                    'group' => 'file',
                    'key' => 'oss_domain',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 105,
                    'remark' => 'OSS自定义域名，留空使用默认域名',
                ],
                // 腾讯云COS配置
                [
                    'name' => '腾讯云COS密钥ID',
                    'group' => 'file',
                    'key' => 'cos_secret_id',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 110,
                    'remark' => '腾讯云 SecretId',
                ],
                [
                    'name' => '腾讯云COS密钥',
                    'group' => 'file',
                    'key' => 'cos_secret_key',
                    'value' => '',
                    'type' => 'password',
                    'sort' => 111,
                    'remark' => '腾讯云 SecretKey',
                ],
                [
                    'name' => '腾讯云COS Bucket',
                    'group' => 'file',
                    'key' => 'cos_bucket',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 112,
                    'remark' => 'COS Bucket名称',
                ],
                [
                    'name' => '腾讯云COS地域',
                    'group' => 'file',
                    'key' => 'cos_region',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 113,
                    'remark' => 'COS地域，如: ap-beijing',
                ],
                [
                    'name' => '腾讯云COS域名',
                    'group' => 'file',
                    'key' => 'cos_domain',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 114,
                    'remark' => 'COS自定义域名，留空使用默认域名',
                ],
                // 七牛云配置
                [
                    'name' => '七牛云AccessKey',
                    'group' => 'file',
                    'key' => 'qiniu_access_key',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 120,
                    'remark' => '七牛云 AccessKey',
                ],
                [
                    'name' => '七牛云SecretKey',
                    'group' => 'file',
                    'key' => 'qiniu_secret_key',
                    'value' => '',
                    'type' => 'password',
                    'sort' => 121,
                    'remark' => '七牛云 SecretKey',
                ],
                [
                    'name' => '七牛云Bucket',
                    'group' => 'file',
                    'key' => 'qiniu_bucket',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 122,
                    'remark' => '七牛云存储空间名称',
                ],
                [
                    'name' => '七牛云域名',
                    'group' => 'file',
                    'key' => 'qiniu_domain',
                    'value' => '',
                    'type' => 'text',
                    'sort' => 123,
                    'remark' => '七牛云加速域名',
                ],
                // 文件限制配置
                [
                    'name' => '文件存储路径',
                    'group' => 'file',
                    'key' => 'file_save_path',
                    'value' => 'uploads/{year}/{month}',
                    'type' => 'text',
                    'sort' => 130,
                    'remark' => '文件存储路径，支持变量: {year},{month},{day},{module}',
                ],
                [
                    'name' => '图片大小限制',
                    'group' => 'file',
                    'key' => 'file_image_max_size',
                    'value' => '5242880',
                    'type' => 'number',
                    'sort' => 131,
                    'remark' => '图片最大大小(字节)，默认5MB',
                ],
                [
                    'name' => '文件大小限制',
                    'group' => 'file',
                    'key' => 'file_max_size',
                    'value' => '52428800',
                    'type' => 'number',
                    'sort' => 132,
                    'remark' => '文件最大大小(字节)，默认50MB',
                ],
                [
                    'name' => '允许的图片格式',
                    'group' => 'file',
                    'key' => 'file_image_ext',
                    'value' => 'jpg,jpeg,png,gif,bmp,webp,svg,ico',
                    'type' => 'text',
                    'sort' => 133,
                    'remark' => '允许上传的图片格式，逗号分隔',
                ],
                [
                    'name' => '允许的文件格式',
                    'group' => 'file',
                    'key' => 'file_ext',
                    'value' => 'jpg,jpeg,png,gif,bmp,webp,svg,ico,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,7z,mp4,avi,mov,wmv,mp3,wav,ogg',
                    'type' => 'text',
                    'sort' => 134,
                    'remark' => '允许上传的文件格式，逗号分隔',
                ],
                [
                    'name' => '允许的视频格式',
                    'group' => 'file',
                    'key' => 'file_video_ext',
                    'value' => 'mp4,avi,mov,wmv,flv,mkv,webm',
                    'type' => 'text',
                    'sort' => 135,
                    'remark' => '允许上传的视频格式，逗号分隔',
                ],
            ])
            ->save();
    }
}

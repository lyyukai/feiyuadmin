<?php
declare(strict_types=1);

namespace app\service\file;

/**
 * 存储驱动接口
 */
interface StorageDriverInterface
{
    /**
     * 上传文件
     * @param string $filePath 本地文件路径
     * @param string $savePath 保存路径
     * @param string $fileName 文件名
     * @return array ['url' => '访问URL', 'path' => '存储路径']
     */
    public function upload(string $filePath, string $savePath, string $fileName): array;

    /**
     * 删除文件
     * @param string $path 存储路径
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * 获取文件访问URL
     * @param string $path 存储路径
     * @return string
     */
    public function getUrl(string $path): string;

    /**
     * 检查文件是否存在
     * @param string $path 存储路径
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * 获取驱动名称
     * @return string
     */
    public function getName(): string;
}

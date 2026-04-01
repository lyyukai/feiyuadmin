<?php
declare(strict_types=1);

namespace app\service\file;

use think\file\UploadedFile;

/**
 * 本地存储驱动
 */
class LocalDriver implements StorageDriverInterface
{
    /**
     * @var string 存储根目录
     */
    protected string $rootPath;

    /**
     * @var string 访问域名
     */
    protected string $domain;

    public function __construct()
    {
        $this->rootPath = root_path() . 'public/';
        $this->domain = request()->domain();
    }

    /**
     * 上传文件到本地
     */
    public function upload(string $filePath, string $savePath, string $fileName): array
    {
        $fullPath = $this->rootPath . $savePath;
        
        // 确保目录存在
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $targetPath = $fullPath . $fileName;
        $relativePath = $savePath . $fileName;

        // 移动文件
        if (!rename($filePath, $targetPath)) {
            throw new \Exception('文件保存失败');
        }

        return [
            'url' => $this->domain . '/' . ltrim($relativePath, '/'),
            'path' => $relativePath,
        ];
    }

    /**
     * 删除本地文件
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->rootPath . ltrim($path, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * 获取本地文件访问URL
     */
    public function getUrl(string $path): string
    {
        return $this->domain . '/' . ltrim($path, '/');
    }

    /**
     * 检查本地文件是否存在
     */
    public function exists(string $path): bool
    {
        return file_exists($this->rootPath . ltrim($path, '/'));
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'local';
    }

    /**
     * 处理上传文件
     */
    public function handleUpload(UploadedFile $file, string $savePath): array
    {
        $extension = $file->getOriginalExtension();
        $fileName = md5((string) microtime(true) . $file->getPathname()) . '.' . $extension;
        
        $tempPath = $file->getPathname();
        
        return $this->upload($tempPath, $savePath, $fileName);
    }
}

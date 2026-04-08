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

    public function getDisplayName(): string
    {
        return '本地存储';
    }

    public function test(): array
    {
        $rootPath = root_path() . 'public/';
        $domain = request()->domain();

        if (!is_dir($rootPath)) {
            if (!mkdir($rootPath, 0755, true)) {
                throw new \think\Exception('存储目录不存在且无法创建：' . $rootPath);
            }
        }

        if (!is_writable($rootPath)) {
            throw new \think\Exception('存储目录无写入权限：' . $rootPath);
        }

        // 写入测试
        $testFile = $rootPath . '/.feiyu_test_' . time() . '.txt';
        if (@file_put_contents($testFile, 'feiyuadmin test') === false) {
            throw new \think\Exception('无法写入测试文件，请检查目录权限');
        }
        @unlink($testFile);

        return [
            'name' => '本地存储',
            'root_path' => $rootPath,
            'url' => '/uploads/',
        ];
    }

    /**
     * 处理上传文件
     */
    public function handleUpload($file, string $savePath): array
    {
        $extension = $file->getOriginalExtension();
        $fileName = md5((string) microtime(true) . $file->getPathname()) . '.' . $extension;
        
        $tempPath = $file->getPathname();
        
        return $this->upload($tempPath, $savePath, $fileName);
    }
}

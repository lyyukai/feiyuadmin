<?php
declare(strict_types=1);

namespace app\service\file;

use Qcloud\Cos\Client;
use think\file\UploadedFile;

/**
 * 腾讯云COS存储驱动
 */
class QcloudCosDriver implements StorageDriverInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string Bucket名称
     */
    protected string $bucket;

    /**
     * @var string 地域
     */
    protected string $region;

    /**
     * @var string 自定义域名
     */
    protected string $domain;

    /**
     * @var array 配置信息
     */
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->bucket = $config['cos_bucket'] ?? '';
        $this->region = $config['cos_region'] ?? '';
        $this->domain = $config['cos_domain'] ?? '';
        
        $this->initClient();
    }

    /**
     * 初始化COS客户端
     */
    protected function initClient(): void
    {
        $secretId = $this->config['cos_secret_id'] ?? '';
        $secretKey = $this->config['cos_secret_key'] ?? '';
        
        $region = 'ap-' . str_replace('ap-', '', $this->region);
        
        $this->client = new Client([
            'region' => $region,
            'credentials' => [
                'secretId' => $secretId,
                'secretKey' => $secretKey,
            ],
        ]);
    }

    /**
     * 上传文件到COS
     */
    public function upload(string $filePath, string $savePath, string $fileName): array
    {
        $key = ltrim($savePath . $fileName, '/');
        
        try {
            $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'Body' => fopen($filePath, 'rb'),
            ]);
            
            return [
                'url' => $this->getUrl($key),
                'path' => $key,
            ];
        } catch (\Exception $e) {
            throw new \Exception('COS上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除COS文件
     */
    public function delete(string $path): bool
    {
        $key = ltrim($path, '/');
        
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取COS文件访问URL
     */
    public function getUrl(string $path): string
    {
        $key = ltrim($path, '/');
        
        if (!empty($this->domain)) {
            return rtrim($this->domain, '/') . '/' . $key;
        }
        
        return 'https://' . $this->bucket . '.cos.' . $this->region . '.myqcloud.com/' . $key;
    }

    /**
     * 检查COS文件是否存在
     */
    public function exists(string $path): bool
    {
        $key = ltrim($path, '/');
        
        try {
            $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'cos';
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

    /**
     * 验证配置是否正确
     */
    public static function validateConfig(array $config): bool
    {
        return !empty($config['cos_secret_id'])
            && !empty($config['cos_secret_key'])
            && !empty($config['cos_bucket'])
            && !empty($config['cos_region']);
    }
}

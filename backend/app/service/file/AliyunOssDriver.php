<?php
declare(strict_types=1);

namespace app\service\file;

use OSS\OssClient;
use OSS\Core\OssException;
use think\file\UploadedFile;

/**
 * 阿里云OSS存储驱动
 */
class AliyunOssDriver implements StorageDriverInterface
{
    /**
     * @var OssClient
     */
    protected $client;

    /**
     * @var string Bucket名称
     */
    protected string $bucket;

    /**
     * @var string 地域节点
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
        $this->bucket = $config['oss_bucket'] ?? '';
        $this->region = $config['oss_region'] ?? '';
        $this->domain = $config['oss_domain'] ?? '';
        
        $this->initClient();
    }

    /**
     * 初始化OSS客户端
     */
    protected function initClient(): void
    {
        $accessKeyId = $this->config['oss_access_key_id'] ?? '';
        $accessKeySecret = $this->config['oss_access_key_secret'] ?? '';
        
        $endpoint = 'oss-' . $this->region . '.aliyuncs.com';
        
        $this->client = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
    }

    /**
     * 上传文件到OSS
     */
    public function upload(string $filePath, string $savePath, string $fileName): array
    {
        $object = ltrim($savePath . $fileName, '/');
        
        try {
            $this->client->uploadFile($this->bucket, $object, $filePath);
            
            return [
                'url' => $this->getUrl($object),
                'path' => $object,
            ];
        } catch (OssException $e) {
            throw new \Exception('OSS上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除OSS文件
     */
    public function delete(string $path): bool
    {
        $object = ltrim($path, '/');
        
        try {
            $this->client->deleteObject($this->bucket, $object);
            return true;
        } catch (OssException $e) {
            return false;
        }
    }

    /**
     * 获取OSS文件访问URL
     */
    public function getUrl(string $path): string
    {
        $object = ltrim($path, '/');
        
        if (!empty($this->domain)) {
            return rtrim($this->domain, '/') . '/' . $object;
        }
        
        return 'https://' . $this->bucket . '.oss-' . $this->region . '.aliyuncs.com/' . $object;
    }

    /**
     * 检查OSS文件是否存在
     */
    public function exists(string $path): bool
    {
        $object = ltrim($path, '/');
        
        try {
            return $this->client->doesObjectExist($this->bucket, $object);
        } catch (OssException $e) {
            return false;
        }
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'oss';
    }

    public function getDisplayName(): string
    {
        return '阿里云OSS';
    }

    public function test(): array
    {
        if (!self::validateConfig($this->config)) {
            throw new \think\Exception('OSS配置不完整，请检查AK/SK/Bucket/地域');
        }

        $client = new OssClient(
            $this->config['oss_access_key_id'],
            $this->config['oss_access_key_secret'],
            $this->config['oss_region']
        );

        // 列出所有bucket验证连接
        $buckets = $client->listBuckets();
        $found = false;
        foreach ($buckets as $bucket) {
            if ($bucket->getName() === $this->config['oss_bucket']) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \think\Exception('Bucket不存在或无访问权限：' . $this->config['oss_bucket']);
        }

        $bucket = $this->config['oss_bucket'];
        $testKey = '.feiyu_test_' . time() . '.txt';
        $client->putContent($bucket, $testKey, 'feiyuadmin test connection');
        $client->deleteObject($bucket, $testKey);

        return [
            'name' => '阿里云OSS',
            'bucket' => $this->config['oss_bucket'],
            'region' => $this->config['oss_region'],
            'domain' => $this->config['oss_domain'] ?? '',
        ];
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
        return !empty($config['oss_access_key_id'])
            && !empty($config['oss_access_key_secret'])
            && !empty($config['oss_bucket'])
            && !empty($config['oss_region']);
    }
}

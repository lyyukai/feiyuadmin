<?php
declare(strict_types=1);

namespace app\service\file;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use think\file\UploadedFile;

/**
 * 七牛云存储驱动
 */
class QiniuDriver implements StorageDriverInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var string Bucket名称
     */
    protected string $bucket;

    /**
     * @var string 访问域名
     */
    protected string $domain;

    /**
     * @var array 配置信息
     */
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->bucket = $config['qiniu_bucket'] ?? '';
        $this->domain = $config['qiniu_domain'] ?? '';
        
        $this->initAuth();
    }

    /**
     * 初始化七牛认证
     */
    protected function initAuth(): void
    {
        $accessKey = $this->config['qiniu_access_key'] ?? '';
        $secretKey = $this->config['qiniu_secret_key'] ?? '';
        
        $this->auth = new Auth($accessKey, $secretKey);
    }

    /**
     * 上传文件到七牛云
     */
    public function upload(string $filePath, string $savePath, string $fileName): array
    {
        $key = ltrim($savePath . $fileName, '/');
        $token = $this->auth->uploadToken($this->bucket);
        
        $uploadMgr = new UploadManager();
        
        try {
            list($result, $error) = $uploadMgr->putFile($token, $key, $filePath);
            
            if ($error !== null) {
                throw new \Exception('七牛上传失败: ' . $error->message());
            }
            
            return [
                'url' => $this->getUrl($key),
                'path' => $key,
            ];
        } catch (\Exception $e) {
            throw new \Exception('七牛上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除七牛云文件
     */
    public function delete(string $path): bool
    {
        $key = ltrim($path, '/');
        
        try {
            $bucketManager = new BucketManager($this->auth);
            $bucketManager->delete($this->bucket, $key);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取七牛云文件访问URL
     */
    public function getUrl(string $path): string
    {
        $key = ltrim($path, '/');
        
        if (empty($this->domain)) {
            throw new \Exception('七牛云域名未配置');
        }
        
        // 如果是私有的bucket，需要生成带token的URL
        // 这里简化处理，默认是公开空间
        return rtrim($this->domain, '/') . '/' . $key;
    }

    /**
     * 获取私有bucket的访问URL
     */
    public function getPrivateUrl(string $path, int $expires = 3600): string
    {
        $key = ltrim($path, '/');
        
        if (empty($this->domain)) {
            throw new \Exception('七牛云域名未配置');
        }
        
        $baseUrl = rtrim($this->domain, '/') . '/' . $key;
        
        return $this->auth->privateDownloadUrl($baseUrl, $expires);
    }

    /**
     * 检查七牛云文件是否存在
     */
    public function exists(string $path): bool
    {
        $key = ltrim($path, '/');
        
        try {
            $bucketManager = new BucketManager($this->auth);
            $info = $bucketManager->stat($this->bucket, $key);
            return !empty($info[0]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'qiniu';
    }

    public function getDisplayName(): string
    {
        return '七牛云';
    }

    public function test(): array
    {
        if (!self::validateConfig($this->config)) {
            throw new \think\Exception('七牛云配置不完整，请检查AccessKey/SecretKey/Bucket/域名');
        }

        $auth = new Auth($this->config['qiniu_access_key'], $this->config['qiniu_secret_key']);
        $bucketManager = new BucketManager($auth);

        // 获取bucket信息验证连接
        list($bucket, $err) = $bucketManager->stat($this->config['qiniu_bucket'], '');
        if ($err !== null) {
            throw new \think\Exception('七牛云连接失败：' . $err->message());
        }

        return [
            'name' => '七牛云',
            'bucket' => $this->config['qiniu_bucket'],
            'domain' => $this->config['qiniu_domain'],
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
        return !empty($config['qiniu_access_key'])
            && !empty($config['qiniu_secret_key'])
            && !empty($config['qiniu_bucket'])
            && !empty($config['qiniu_domain']);
    }
}

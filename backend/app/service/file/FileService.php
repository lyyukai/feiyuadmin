<?php
declare(strict_types=1);

namespace app\service\file;

use app\model\File as FileModel;
use app\service\ConfigService;
use think\file\UploadedFile;
use think\Exception;

/**
 * 统一文件服务
 */
class FileService
{
    /**
     * 存储驱动实例
     */
    protected static ?StorageDriverInterface $driver = null;

    /**
     * 配置缓存
     */
    protected static array $configCache = [];

    /**
     * 获取当前存储驱动
     */
    public static function getDriver(): StorageDriverInterface
    {
        if (self::$driver === null) {
            $type = self::getConfig('storage_type', 'local');
            self::$driver = self::createDriver($type);
        }
        
        return self::$driver;
    }

    /**
     * 创建存储驱动实例
     */
    public static function createDriver(string $type): StorageDriverInterface
    {
        $config = self::getStorageConfig();
        
        return match ($type) {
            'oss' => new AliyunOssDriver($config),
            'cos' => new QcloudCosDriver($config),
            'qiniu' => new QiniuDriver($config),
            default => new LocalDriver(),
        };
    }

    /**
     * 获取存储配置
     */
    public static function getStorageConfig(): array
    {
        return [
            // 阿里云OSS
            'oss_access_key_id' => self::getConfig('oss_access_key_id', ''),
            'oss_access_key_secret' => self::getConfig('oss_access_key_secret', ''),
            'oss_bucket' => self::getConfig('oss_bucket', ''),
            'oss_region' => self::getConfig('oss_region', ''),
            'oss_domain' => self::getConfig('oss_domain', ''),
            
            // 腾讯云COS
            'cos_secret_id' => self::getConfig('cos_secret_id', ''),
            'cos_secret_key' => self::getConfig('cos_secret_key', ''),
            'cos_bucket' => self::getConfig('cos_bucket', ''),
            'cos_region' => self::getConfig('cos_region', ''),
            'cos_domain' => self::getConfig('cos_domain', ''),
            
            // 七牛云
            'qiniu_access_key' => self::getConfig('qiniu_access_key', ''),
            'qiniu_secret_key' => self::getConfig('qiniu_secret_key', ''),
            'qiniu_bucket' => self::getConfig('qiniu_bucket', ''),
            'qiniu_domain' => self::getConfig('qiniu_domain', ''),
        ];
    }

    /**
     * 获取配置项
     */
    public static function getConfig(string $key, mixed $default = null): mixed
    {
        if (empty(self::$configCache)) {
            self::loadConfig();
        }
        
        return self::$configCache[$key] ?? self::getConfigFromDb($key, $default);
    }

    /**
     * 从数据库加载配置
     */
    protected static function loadConfig(): void
    {
        try {
            $configs = \think\facade\Db::name('sys_config')
                ->where('`group`', 'file')
                ->column('value', 'key');
            
            self::$configCache = $configs;
        } catch (\Exception $e) {
            self::$configCache = [];
        }
    }

    /**
     * 从数据库获取单个配置
     */
    protected static function getConfigFromDb(string $key, mixed $default = null): mixed
    {
        try {
            $value = \think\facade\Db::name('sys_config')
                ->where('`group`', 'file')
                ->where('`key`', $key)
                ->value('value');
            
            if ($value !== null) {
                self::$configCache[$key] = $value;
            }
            
            return $value ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * 上传文件
     */
    public static function upload(UploadedFile $file, array $options = []): array
    {
        // 验证文件
        self::validateFile($file, $options);
        
        // 获取文件信息
        $extension = strtolower($file->getOriginalExtension());
        $originalName = $file->getOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMime();
        
        // 识别文件类型
        $fileType = self::detectType($extension);
        
        // 生成存储路径
        $savePath = self::generateSavePath($options);
        $fileName = md5((string) microtime(true) . $file->getPathname()) . '.' . $extension;
        
        // 执行上传
        $driver = self::getDriver();
        $result = $driver->handleUpload($file, $savePath);
        
        // 保存文件记录
        $model = new FileModel();
        $model->save([
            'name' => $fileName,
            'original' => $originalName,
            'type' => $fileType,
            'size' => $fileSize,
            'path' => $result['path'],
            'url' => $result['url'],
            'extension' => $extension,
            'mime_type' => $mimeType,
            'user_id' => self::getUserId(),
            'storage' => $driver->getName(),
        ]);
        
        return [
            'id' => $model->id,
            'name' => $fileName,
            'original' => $originalName,
            'type' => $fileType,
            'size' => $fileSize,
            'size_format' => format_bytes((int) $fileSize),
            'url' => $result['url'],
            'path' => $result['path'],
            'extension' => $extension,
            'storage' => $driver->getName(),
        ];
    }

    /**
     * 验证文件
     */
    public static function validateFile(UploadedFile $file, array $options = []): void
    {
        $extension = strtolower($file->getOriginalExtension());
        
        // 验证扩展名
        $allowedExt = self::getAllowedExt($options);
        if (!empty($allowedExt) && !in_array($extension, $allowedExt)) {
            throw new Exception('不允许的文件格式: ' . $extension);
        }
        
        // 验证文件大小
        $maxSize = self::getMaxSize($options);
        if ($file->getSize() > $maxSize) {
            throw new Exception('文件大小超过限制，最大允许: ' . format_bytes($maxSize));
        }
    }

    /**
     * 获取允许的扩展名
     */
    public static function getAllowedExt(array $options = []): array
    {
        if (!empty($options['type'])) {
            return match ($options['type']) {
                'image' => explode(',', self::getConfig('file_image_ext', 'jpg,jpeg,png,gif,bmp,webp,svg,ico')),
                'video' => explode(',', self::getConfig('file_video_ext', 'mp4,avi,mov,wmv,flv,mkv,webm')),
                default => explode(',', self::getConfig('file_ext', '')),
            };
        }
        
        $extStr = self::getConfig('file_ext', '');
        return $extStr ? explode(',', $extStr) : [];
    }

    /**
     * 获取文件大小限制
     */
    public static function getMaxSize(array $options = []): int
    {
        if (!empty($options['type']) && $options['type'] === 'image') {
            return (int) self::getConfig('file_image_max_size', 5242880);
        }
        
        return (int) self::getConfig('file_max_size', 52428800);
    }

    /**
     * 识别文件类型
     */
    public static function detectType(string $extension): string
    {
        $image = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
        $video = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        $audio = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'];
        
        $ext = strtolower($extension);
        
        if (in_array($ext, $image)) {
            return 'image';
        }
        if (in_array($ext, $video)) {
            return 'video';
        }
        if (in_array($ext, $audio)) {
            return 'audio';
        }
        
        return 'file';
    }

    /**
     * 生成分享路径
     */
    public static function generateSavePath(array $options = []): string
    {
        $template = self::getConfig('file_save_path', 'uploads/{year}/{month}');
        
        $replace = [
            '{year}' => date('Y'),
            '{month}' => date('m'),
            '{day}' => date('d'),
            '{module}' => $options['module'] ?? 'common',
        ];
        
        return str_replace(array_keys($replace), array_values($replace), $template) . '/';
    }

    /**
     * 删除文件
     */
    public static function delete(int $id): bool
    {
        $file = FileModel::find($id);
        if (!$file) {
            return false;
        }
        
        // 删除存储文件
        $driver = self::createDriver($file->storage);
        $driver->delete($file->path);
        
        // 删除数据库记录
        $file->delete_time = date('Y-m-d H:i:s');
        $file->save();
        
        return true;
    }

    /**
     * 批量删除文件
     */
    public static function batchDelete(array $ids): int
    {
        $count = 0;
        
        foreach ($ids as $id) {
            if (self::delete((int) $id)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * 获取文件预览信息
     */
    public static function getPreviewInfo(int $id): ?array
    {
        $file = FileModel::find($id);
        if (!$file) {
            return null;
        }
        
        return [
            'id' => $file->id,
            'name' => $file->name,
            'original' => $file->original,
            'type' => $file->type,
            'size' => $file->size,
            'size_format' => format_bytes((int) $file->size),
            'url' => $file->url,
            'path' => $file->path,
            'extension' => $file->extension,
            'mime_type' => $file->mime_type,
            'storage' => $file->storage,
            'create_time' => $file->create_time,
            // 预览相关
            'preview_type' => self::getPreviewType($file->type, $file->extension),
            'is_image' => $file->type === 'image',
            'is_video' => $file->type === 'video',
            'is_pdf' => $file->extension === 'pdf',
            'is_office' => in_array($file->extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']),
        ];
    }

    /**
     * 获取预览类型
     */
    public static function getPreviewType(string $fileType, string $extension): string
    {
        if ($fileType === 'image') {
            return 'image';
        }
        
        if ($fileType === 'video') {
            return 'video';
        }
        
        if ($extension === 'pdf') {
            return 'pdf';
        }
        
        if (in_array($extension, ['doc', 'docx'])) {
            return 'word';
        }
        
        if (in_array($extension, ['xls', 'xlsx'])) {
            return 'excel';
        }
        
        if (in_array($extension, ['ppt', 'pptx'])) {
            return 'ppt';
        }
        
        return 'file';
    }

    /**
     * 获取当前用户ID
     */
    protected static function getUserId(): int
    {
        try {
            return (int) (request()->adminId ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 清除配置缓存
     */
    public static function clearConfigCache(): void
    {
        self::$configCache = [];
        self::$driver = null;
    }
}

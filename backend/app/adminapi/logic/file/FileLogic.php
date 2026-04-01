<?php
declare(strict_types=1);

namespace app\adminapi\logic\file;

use app\model\File as FileModel;
use app\service\file\FileService;
use think\Paginator;

/**
 * 文件管理业务逻辑
 */
class FileLogic
{
    /**
     * 获取文件列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $pageSize = (int) ($params['page_size'] ?? 20);
        $type = $params['type'] ?? '';
        $keyword = $params['keyword'] ?? '';
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $storage = $params['storage'] ?? '';
        
        $where = [];
        
        if (!empty($type)) {
            $where[] = ['type', '=', $type];
        }
        
        if (!empty($storage)) {
            $where[] = ['storage', '=', $storage];
        }
        
        if (!empty($keyword)) {
            $where[] = function ($q) use ($keyword) {
                $q->whereOr('name', 'like', '%' . $keyword . '%')
                  ->whereOr('original', 'like', '%' . $keyword . '%');
            };
        }
        
        if (!empty($startTime)) {
            $where[] = ['create_time', '>=', $startTime . ' 00:00:00'];
        }
        
        if (!empty($endTime)) {
            $where[] = ['create_time', '<=', $endTime . ' 23:59:59'];
        }
        
        $query = FileModel::where($where)
            ->order('id', 'desc');
        
        $total = $query->count();
        $list = $query->page($page, $pageSize)->select()->toArray();
        
        // 格式化数据
        foreach ($list as &$item) {
            $item['size_format'] = format_bytes((int) ($item['size'] ?? 0));
            $item['type_text'] = self::getTypeText($item['type']);
            $item['storage_text'] = self::getStorageText($item['storage']);
            $item['preview_type'] = FileService::getPreviewType($item['type'], $item['extension']);
            $item['is_image'] = $item['type'] === 'image';
            $item['is_video'] = $item['type'] === 'video';
            $item['is_pdf'] = $item['extension'] === 'pdf';
            $item['is_office'] = in_array($item['extension'], ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
        }
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * 上传文件
     */
    public static function upload(array $params): array
    {
        $file = $params['file'] ?? null;
        
        if (!$file) {
            throw new \InvalidArgumentException('请选择要上传的文件');
        }
        
        $options = [];
        
        if (!empty($params['type'])) {
            $options['type'] = $params['type'];
        }
        
        if (!empty($params['module'])) {
            $options['module'] = $params['module'];
        }
        
        return FileService::upload($file, $options);
    }

    /**
     * 删除文件
     */
    public static function delete(int $id): bool
    {
        return FileService::delete($id);
    }

    /**
     * 批量删除
     */
    public static function batchDelete(array $ids): int
    {
        return FileService::batchDelete($ids);
    }

    /**
     * 获取文件详情
     */
    public static function getDetail(int $id): ?array
    {
        return FileService::getPreviewInfo($id);
    }

    /**
     * 获取存储配置
     */
    public static function getStorageConfig(): array
    {
        return [
            'storage_type' => FileService::getConfig('storage_type', 'local'),
            'file_save_path' => FileService::getConfig('file_save_path', 'uploads/{year}/{month}'),
            'file_image_max_size' => FileService::getConfig('file_image_max_size', 5242880),
            'file_max_size' => FileService::getConfig('file_max_size', 52428800),
            'file_image_ext' => FileService::getConfig('file_image_ext', 'jpg,jpeg,png,gif,bmp,webp,svg,ico'),
            'file_ext' => FileService::getConfig('file_ext', ''),
            'file_video_ext' => FileService::getConfig('file_video_ext', 'mp4,avi,mov,wmv,flv,mkv,webm'),
            
            // 云存储配置状态
            'oss_configured' => !empty(FileService::getConfig('oss_access_key_id', '')),
            'cos_configured' => !empty(FileService::getConfig('cos_secret_id', '')),
            'qiniu_configured' => !empty(FileService::getConfig('qiniu_access_key', '')),
            
            // 格式限制说明
            'image_max_size_format' => format_bytes((int) FileService::getConfig('file_image_max_size', 5242880)),
            'file_max_size_format' => format_bytes((int) FileService::getConfig('file_max_size', 52428800)),
        ];
    }

    /**
     * 获取文件类型文本
     */
    protected static function getTypeText(string $type): string
    {
        $types = [
            'image' => '图片',
            'video' => '视频',
            'audio' => '音频',
            'file' => '文件',
        ];
        
        return $types[$type] ?? '文件';
    }

    /**
     * 获取存储方式文本
     */
    protected static function getStorageText(string $storage): string
    {
        $storages = [
            'local' => '本地存储',
            'oss' => '阿里云OSS',
            'cos' => '腾讯云COS',
            'qiniu' => '七牛云',
        ];
        
        return $storages[$storage] ?? '本地存储';
    }

    /**
     * 获取统计信息
     */
    public static function getStatistics(): array
    {
        $totalCount = FileModel::count();
        $totalSize = FileModel::sum('size');
        
        $imageCount = FileModel::where('type', 'image')->count();
        $videoCount = FileModel::where('type', 'video')->count();
        $fileCount = FileModel::where('type', 'file')->count();
        
        $ossCount = FileModel::where('storage', 'oss')->count();
        $cosCount = FileModel::where('storage', 'cos')->count();
        $qiniuCount = FileModel::where('storage', 'qiniu')->count();
        $localCount = FileModel::where('storage', 'local')->count();
        
        return [
            'total_count' => $totalCount,
            'total_size' => $totalSize,
            'total_size_format' => format_bytes((int) $totalSize),
            'image_count' => $imageCount,
            'video_count' => $videoCount,
            'file_count' => $fileCount,
            'oss_count' => $ossCount,
            'cos_count' => $cosCount,
            'qiniu_count' => $qiniuCount,
            'local_count' => $localCount,
        ];
    }
}

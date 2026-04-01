<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 文件模型
 */
class File extends Model
{
    protected $name = 'file';

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    protected $deleteTime = 'delete_time';

    /**
     * 仅获取未删除的记录
     */
    public function scopeNotDeleted($query)
    {
        return $query->whereNull('delete_time');
    }

    /**
     * 文件类型获取器
     */
    public function getTypeTextAttr($value, $data): string
    {
        $types = [
            'image' => '图片',
            'video' => '视频',
            'audio' => '音频',
            'file' => '文件',
        ];
        return $types[$data['type']] ?? '文件';
    }

    /**
     * 文件大小格式化
     */
    public function getSizeFormatAttr($value, $data): string
    {
        return format_bytes((int) ($data['size'] ?? 0));
    }

    /**
     * 存储方式获取器
     */
    public function getStorageTextAttr($value, $data): string
    {
        $storages = [
            'local' => '本地存储',
            'oss' => '阿里云OSS',
            'cos' => '腾讯云COS',
            'qiniu' => '七牛云',
        ];
        return $storages[$data['storage']] ?? '本地存储';
    }

    /**
     * 按条件搜索文件
     */
    public function search(array $params): \think\db\Query
    {
        $query = $this->db()->scope('notDeleted');

        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->whereOr('name', 'like', '%' . $params['keyword'] . '%')
                  ->whereOr('original', 'like', '%' . $params['keyword'] . '%');
            });
        }

        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }

        if (!empty($params['start_time'])) {
            $query->where('create_time', '>=', $params['start_time'] . ' 00:00:00');
        }

        if (!empty($params['end_time'])) {
            $query->where('create_time', '<=', $params['end_time'] . ' 23:59:59');
        }

        return $query->order('id', 'desc');
    }

    /**
     * 自动识别文件类型
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
}

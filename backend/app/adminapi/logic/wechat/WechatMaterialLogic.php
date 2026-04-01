<?php
/**
 * 微信素材逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\WechatMaterial;
use app\common\model\wechat\WechatAccount;

/**
 * 素材管理逻辑
 * Class WechatMaterialLogic
 * @package app\adminapi\logic\wechat
 */
class WechatMaterialLogic
{
    /**
     * 获取素材列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $account_id = (int) ($params['account_id'] ?? 0);
        $type = $params['type'] ?? '';
        $is_permanent = $params['is_permanent'] ?? '';

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }
        if ($is_permanent !== '') {
            $where[] = ['is_permanent', '=', (int) $is_permanent];
        }

        $list = WechatMaterial::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 上传素材
     */
    public static function upload(array $data, array $file): bool
    {
        try {
            $account_id = (int) ($data['account_id'] ?? 0);
            $type = $data['type'] ?? 'image';
            $title = $data['title'] ?? '';
            $introduction = $data['introduction'] ?? '';

            if ($account_id <= 0) {
                throw new \Exception('请选择公众号账号');
            }

            // 验证账号
            $account = WechatAccount::find($account_id);
            if (!$account || $account->status != 1) {
                throw new \Exception('公众号账号不可用');
            }

            // 处理文件上传
            $filePath = $file['file']['tmp_name'] ?? '';
            $originalName = $file['file']['name'] ?? '';
            $fileSize = $file['file']['size'] ?? 0;
            $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);

            // TODO: 调用微信API上传素材
            // $wechat = new \EasyWeChat\OfficialAccount\Application($accountConfig);
            // $result = $wechat->material->upload($type, $filePath);

            // 临时保存到本地
            $uploadDir = root_path() . 'runtime/uploads/wechat/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = md5(uniqid((string) mt_rand(), true)) . '.' . $fileExt;
            $newFilePath = $uploadDir . $newFileName;

            if (!move_uploaded_file($filePath, $newFilePath)) {
                throw new \Exception('文件保存失败');
            }

            // 保存素材记录
            $model = new WechatMaterial();
            $model->account_id = $account_id;
            $model->type = $type;
            $model->title = $title;
            $model->introduction = $introduction;
            $model->file_path = $newFilePath;
            $model->url = '/uploads/wechat/' . $newFileName;
            $model->format = $fileExt;
            $model->size = $fileSize;
            $model->is_permanent = 0;
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 删除素材
     */
    public static function delete(int $id): bool
    {
        try {
            $model = WechatMaterial::find($id);
            if (!$model) {
                return false;
            }

            // 删除本地文件
            if ($model->file_path && file_exists($model->file_path)) {
                unlink($model->file_path);
            }

            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取素材详情
     */
    public static function getDetail(int $id): ?WechatMaterial
    {
        return WechatMaterial::find($id);
    }

    /**
     * 获取素材统计
     */
    public static function getStatistics(int $accountId = 0): array
    {
        $where = [];
        if ($accountId > 0) {
            $where[] = ['account_id', '=', $accountId];
        }

        $total = WechatMaterial::where($where)->count();

        $imageCount = WechatMaterial::where(array_merge($where, [['type', '=', 'image']]))->count();
        $voiceCount = WechatMaterial::where(array_merge($where, [['type', '=', 'voice']]))->count();
        $videoCount = WechatMaterial::where(array_merge($where, [['type', '=', 'video']]))->count();
        $newsCount = WechatMaterial::where(array_merge($where, [['type', '=', 'news']]))->count();

        $totalSize = WechatMaterial::where($where)->sum('size');

        return [
            'total' => $total,
            'image_count' => $imageCount,
            'voice_count' => $voiceCount,
            'video_count' => $videoCount,
            'news_count' => $newsCount,
            'total_size' => $totalSize,
            'total_size_format' => self::formatSize($totalSize),
        ];
    }

    /**
     * 格式化文件大小
     */
    private static function formatSize(int $size): string
    {
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 2) . ' KB';
        } elseif ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024), 2) . ' MB';
        } else {
            return round($size / (1024 * 1024 * 1024), 2) . ' GB';
        }
    }
}

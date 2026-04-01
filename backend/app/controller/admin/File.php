<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\File as FileModel;
use think\Request;
use think\Response;
use think\exception\FileException;

/**
 * 文件上传控制器
 */
class File extends Base
{
    /**
     * 文件列表
     */
    public function list(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new FileModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        // 格式化
        foreach ($list as &$item) {
            $item['size_format'] = format_bytes((int) $item['size']);
        }

        return $this->page($total, $list);
    }

    /**
     * 上传文件
     */
    public function upload(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->error('请选择要上传的文件', 400);
        }

        // 验证文件大小 10MB
        $validate = ['size' => 10 * 1024 * 1024];
        $type = $request->param('type', '');

        // 文件类型验证
        $extRule = $request->param('ext', '');
        if ($extRule) {
            $validate['ext'] = $extRule;
        }

        if (!$file->validate($validate)) {
            return $this->error($file->getError(), 400);
        }

        // 自动识别扩展名
        $extension = $file->extension();
        $fileType = FileModel::detectType($extension);

        // 覆盖类型
        if (!empty($type) && in_array($type, ['image', 'video', 'audio', 'file'])) {
            $fileType = $type;
        }

        // 生成存储路径
        $savePath = 'uploads/' . date('Y/m/');
        $saveName = md5((string) microtime(true) . $file->getPathname()) . '.' . $extension;

        try {
            // 上传到本地
            $info = $file->move(root_path() . 'public/' . $savePath, $saveName);
            if (!$info) {
                return $this->error('文件保存失败', 500);
            }

            $relativePath = $savePath . $info->getSaveName();
            $absolutePath = root_path() . 'public/' . $relativePath;
            $url = request()->domain() . '/' . $relativePath;

            // 保存文件记录
            $model = new FileModel();
            $model->save([
                'name' => $info->getSaveName(),
                'original' => $file->getOriginalName(),
                'type' => $fileType,
                'size' => $info->getSize(),
                'path' => $relativePath,
                'url' => $url,
                'extension' => $extension,
                'mime_type' => $file->getMimeType(),
                'user_id' => $this->userId,
                'storage' => 'local',
            ]);

            return $this->success([
                'id' => $model->id,
                'name' => $model->name,
                'original' => $model->original,
                'type' => $model->type,
                'size' => $model->size,
                'size_format' => format_bytes((int) $model->size),
                'url' => $url,
                'extension' => $extension,
            ], '上传成功');

        } catch (FileException $e) {
            return $this->error('上传失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 下载文件
     */
    public function download(int $id): Response
    {
        $file = FileModel::find($id);
        if (!$file) {
            return $this->error('文件不存在', 404);
        }

        $fullPath = root_path() . 'public/' . $file->path;

        if (!file_exists($fullPath)) {
            return $this->error('文件已丢失', 404);
        }

        return download($fullPath, $file->original);
    }

    /**
     * 删除文件
     */
    public function delete(int $id): Response
    {
        $file = FileModel::find($id);
        if (!$file) {
            return $this->error('文件不存在', 404);
        }

        // 删除物理文件
        $fullPath = root_path() . 'public/' . $file->path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // 软删除记录
        $file->delete_time = date('Y-m-d H:i:s');
        $file->save();

        return $this->success([], '删除成功');
    }

    /**
     * 批量删除文件
     */
    public function batchDelete(Request $request): Response
    {
        $ids = $request->param('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return $this->error('请选择要删除的文件');
        }

        $count = 0;
        foreach ($ids as $id) {
            $file = FileModel::find((int) $id);
            if ($file) {
                // 删除物理文件
                $fullPath = root_path() . 'public/' . $file->path;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }

                $file->delete_time = date('Y-m-d H:i:s');
                $file->save();
                $count++;
            }
        }

        return $this->success(['count' => $count], '批量删除成功');
    }

    /**
     * 云存储上传（接口预留）
     * 支持阿里云OSS、腾讯云COS、七牛云
     */
    protected function uploadToCloud(string $filePath, string $extension, string $storage = 'oss'): array
    {
        // 接口预留，暂未实现
        // 可扩展：
        // - 阿里云OSS: 使用 aliyun-sdk
        // - 腾讯云COS: 使用 cos-sdk
        // - 七牛云: 使用 qiniu-sdk
        throw new \Exception('云存储上传暂未实现，请联系管理员配置');
    }
}

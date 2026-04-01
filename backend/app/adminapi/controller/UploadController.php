<?php
/**
 * 飞羽后台管理系统 - 文件上传控制器 V2
 */

declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\file\FileLogic;
use app\service\file\FileService;
use think\file\UploadedFile;
use think\Response;

/**
 * 文件上传控制器 V2
 * 支持本地存储、阿里云OSS、腾讯云COS、七牛云
 * Class UploadController
 * @package app\adminapi\controller
 */
class UploadController extends BaseAdminController
{
    /**
     * 上传图片
     * @return Response
     */
    public function image(): Response
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->fail('请选择要上传的图片');
            }

            $result = FileLogic::upload([
                'file' => $file,
                'type' => 'image',
            ]);

            return $this->success('上传成功', $result);
        } catch (\InvalidArgumentException $e) {
            return $this->fail($e->getMessage());
        } catch (\Exception $e) {
            return $this->fail('图片上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 上传文件
     * @return Response
     */
    public function file(): Response
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->fail('请选择要上传的文件');
            }

            $result = FileLogic::upload([
                'file' => $file,
                'type' => 'file',
            ]);

            return $this->success('上传成功', $result);
        } catch (\InvalidArgumentException $e) {
            return $this->fail($e->getMessage());
        } catch (\Exception $e) {
            return $this->fail('文件上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 上传视频
     * @return Response
     */
    public function video(): Response
    {
        try {
            $file = $this->request->file('file');
            if (!$file) {
                return $this->fail('请选择要上传的视频');
            }

            $result = FileLogic::upload([
                'file' => $file,
                'type' => 'video',
            ]);

            return $this->success('上传成功', $result);
        } catch (\InvalidArgumentException $e) {
            return $this->fail($e->getMessage());
        } catch (\Exception $e) {
            return $this->fail('视频上传失败: ' . $e->getMessage());
        }
    }

    /**
     * 文件列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        
        try {
            $result = FileLogic::getList($params);
            return $this->success('获取成功', $result);
        } catch (\Exception $e) {
            return $this->fail('获取列表失败');
        }
    }

    /**
     * 删除文件
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('文件ID不能为空');
        }

        try {
            $result = FileLogic::delete($id);
            if ($result) {
                return $this->success('删除成功');
            }
            return $this->fail('文件不存在');
        } catch (\Exception $e) {
            return $this->fail('删除失败');
        }
    }

    /**
     * 批量删除
     * @return Response
     */
    public function batchDelete(): Response
    {
        $ids = $this->param('ids', []);
        
        if (!is_array($ids) || empty($ids)) {
            return $this->fail('请选择要删除的文件');
        }

        try {
            $count = FileLogic::batchDelete($ids);
            return $this->success('批量删除成功', ['count' => $count]);
        } catch (\Exception $e) {
            return $this->fail('批量删除失败');
        }
    }

    /**
     * 获取文件详情/预览信息
     * @return Response
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('文件ID不能为空');
        }

        try {
            $detail = FileLogic::getDetail($id);
            if ($detail) {
                return $this->success('获取成功', $detail);
            }
            return $this->fail('文件不存在');
        } catch (\Exception $e) {
            return $this->fail('获取详情失败');
        }
    }

    /**
     * 获取存储配置
     * @return Response
     */
    public function config(): Response
    {
        try {
            $config = FileLogic::getStorageConfig();
            return $this->success('获取成功', $config);
        } catch (\Exception $e) {
            return $this->fail('获取配置失败');
        }
    }

    /**
     * 获取统计信息
     * @return Response
     */
    public function statistics(): Response
    {
        try {
            $stats = FileLogic::getStatistics();
            return $this->success('获取成功', $stats);
        } catch (\Exception $e) {
            return $this->fail('获取统计失败');
        }
    }

    /**
     * 清除配置缓存
     * @return Response
     */
    public function clearCache(): Response
    {
        try {
            FileService::clearConfigCache();
            return $this->success('缓存已清除');
        } catch (\Exception $e) {
            return $this->fail('清除缓存失败');
        }
    }
}

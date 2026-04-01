<?php
/**
 * 微信素材控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use think\Response;

/**
 * 素材管理
 * Class WechatMaterialController
 * @package app\adminapi\controller\wechat
 */
class WechatMaterialController extends BaseAdminController
{
    /**
     * 素材列表
     */
    public function lists(): Response
    {
        $page = (int) $this->request->get('page', 1);
        $limit = (int) $this->request->get('limit', 10);
        $account_id = (int) $this->request->get('account_id', 0);
        $type = $this->request->get('type', '');

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }

        $list = \app\common\model\wechat\WechatMaterial::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 上传素材
     */
    public function upload(): Response
    {
        $account_id = (int) $this->request->post('account_id', 0);
        $type = $this->request->post('type', 'image');
        $title = $this->request->post('title', '');
        $introduction = $this->request->post('introduction', '');

        if ($account_id <= 0) {
            return $this->fail('请选择公众号账号');
        }

        // 获取账号配置
        $account = \app\common\model\wechat\WechatAccount::find($account_id);
        if (!$account || $account->status != 1) {
            return $this->fail('公众号账号不可用');
        }

        // TODO: 调用微信API上传素材
        // $wechat = new \EasyWeChat\OfficialAccount\Application($accountConfig);
        // $result = $wechat->material->upload($type, $filePath);

        return $this->success('上传成功');
    }

    /**
     * 删除素材
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatMaterial::find($id);
        if (!$model) {
            return $this->fail('素材不存在');
        }

        // 删除本地文件
        if ($model->file_path && file_exists($model->file_path)) {
            unlink($model->file_path);
        }

        return $model->delete() ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 素材统计
     */
    public function statistics(): Response
    {
        $account_id = (int) $this->request->get('account_id', 0);

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }

        $total = \app\common\model\wechat\WechatMaterial::where($where)->count();
        $imageCount = \app\common\model\wechat\WechatMaterial::where(array_merge($where, [['type', '=', 'image']]))->count();
        $voiceCount = \app\common\model\wechat\WechatMaterial::where(array_merge($where, [['type', '=', 'voice']]))->count();
        $videoCount = \app\common\model\wechat\WechatMaterial::where(array_merge($where, [['type', '=', 'video']]))->count();
        $newsCount = \app\common\model\wechat\WechatMaterial::where(array_merge($where, [['type', '=', 'news']]))->count();
        $totalSize = \app\common\model\wechat\WechatMaterial::where($where)->sum('size');

        $data = [
            'total' => $total,
            'image_count' => $imageCount,
            'voice_count' => $voiceCount,
            'video_count' => $videoCount,
            'news_count' => $newsCount,
            'total_size' => $totalSize,
            'total_size_format' => $this->formatSize($totalSize),
        ];

        return $this->success('获取成功', $data);
    }

    /**
     * 格式化文件大小
     */
    private function formatSize(int $size): string
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

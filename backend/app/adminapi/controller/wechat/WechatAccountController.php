<?php
/**
 * 微信公众号账号管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use think\Response;

/**
 * 公众号账号管理
 * Class WechatAccountController
 * @package app\adminapi\controller\wechat
 */
class WechatAccountController extends BaseAdminController
{
    /**
     * 账号列表
     */
    public function lists(): Response
    {
        $page = (int) $this->request->get('page', 1);
        $limit = (int) $this->request->get('limit', 10);
        $name = $this->request->get('name', '');
        $status = $this->request->get('status', '');

        $where = [];
        if ($name !== '') {
            $where[] = ['name', 'like', "%{$name}%"];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }

        $list = \app\common\model\wechat\WechatAccount::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加账号
     */
    public function add(): Response
    {
        $data = $this->request->post();

        $validate = new \app\adminapi\validate\wechat\WechatAccountValidate();
        if (!$validate->scene('add')->check($data)) {
            return $this->fail($validate->getError());
        }

        $result = \app\common\model\wechat\WechatAccount::create([
            'name' => $data['name'] ?? '',
            'appid' => $data['appid'] ?? '',
            'appsecret' => $data['appsecret'] ?? '',
            'token' => $data['token'] ?? '',
            'encoding_aeskey' => $data['encoding_aeskey'] ?? '',
            'type' => (int) ($data['type'] ?? 1),
            'status' => (int) ($data['status'] ?? 1),
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ]);

        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 编辑账号
     */
    public function edit(): Response
    {
        $data = $this->request->post();

        $validate = new \app\adminapi\validate\wechat\WechatAccountValidate();
        if (!$validate->scene('edit')->check($data)) {
            return $this->fail($validate->getError());
        }

        $id = $data['id'] ?? 0;
        $model = \app\common\model\wechat\WechatAccount::find($id);
        if (!$model) {
            return $this->fail('账号不存在');
        }

        $model->name = $data['name'] ?? '';
        $model->appid = $data['appid'] ?? '';
        $model->appsecret = $data['appsecret'] ?? '';
        $model->token = $data['token'] ?? '';
        $model->encoding_aeskey = $data['encoding_aeskey'] ?? '';
        $model->type = (int) ($data['type'] ?? 1);
        $model->status = (int) ($data['status'] ?? 1);
        $model->update_time = date('Y-m-d H:i:s');

        return $model->save() ? $this->success('编辑成功') : $this->fail('编辑失败');
    }

    /**
     * 删除账号
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatAccount::find($id);
        if (!$model) {
            return $this->fail('账号不存在');
        }

        return $model->delete() ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 获取微信配置（用于生成微信服务器配置信息）
     */
    public function config(): Response
    {
        $id = (int) $this->request->get('id', 0);

        $model = \app\common\model\wechat\WechatAccount::find($id);
        if (!$model) {
            return $this->fail('账号不存在');
        }

        // 生成微信服务器配置URL（需要配置到微信公众平台）
        $baseUrl = request()->domain();
        $data = [
            'id' => $model->id,
            'name' => $model->name,
            'appid' => $model->appid,
            'token' => $model->token,
            'encoding_aeskey' => $model->encoding_aeskey,
            'callback_url' => $baseUrl . '/api/wechat/callback/' . $model->id,
        ];

        return $this->success('获取成功', $data);
    }
}

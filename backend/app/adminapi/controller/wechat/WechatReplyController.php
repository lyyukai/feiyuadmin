<?php
/**
 * 微信自动回复控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use think\Response;

/**
 * 自动回复管理
 * Class WechatReplyController
 * @package app\adminapi\controller\wechat
 */
class WechatReplyController extends BaseAdminController
{
    /**
     * 回复规则列表
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

        $list = \app\common\model\wechat\WechatReply::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加规则
     */
    public function add(): Response
    {
        $data = $this->request->post();

        $validate = new \app\adminapi\validate\wechat\WechatReplyValidate();
        if (!$validate->check($data)) {
            return $this->fail($validate->getError());
        }

        $result = \app\common\model\wechat\WechatReply::create([
            'account_id' => (int) ($data['account_id'] ?? 0),
            'type' => $data['type'] ?? 'keyword',
            'keyword' => $data['keyword'] ?? '',
            'reply_type' => $data['reply_type'] ?? 'text',
            'content' => $data['content'] ?? '',
            'status' => (int) ($data['status'] ?? 1),
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 编辑规则
     */
    public function edit(): Response
    {
        $data = $this->request->post();

        $validate = new \app\adminapi\validate\wechat\WechatReplyValidate();
        if (!$validate->check($data)) {
            return $this->fail($validate->getError());
        }

        $id = $data['id'] ?? 0;
        $model = \app\common\model\wechat\WechatReply::find($id);
        if (!$model) {
            return $this->fail('规则不存在');
        }

        $model->account_id = (int) ($data['account_id'] ?? 0);
        $model->type = $data['type'] ?? 'keyword';
        $model->keyword = $data['keyword'] ?? '';
        $model->reply_type = $data['reply_type'] ?? 'text';
        $model->content = $data['content'] ?? '';
        $model->status = (int) ($data['status'] ?? 1);

        return $model->save() ? $this->success('编辑成功') : $this->fail('编辑失败');
    }

    /**
     * 删除规则
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatReply::find($id);
        if (!$model) {
            return $this->fail('规则不存在');
        }

        return $model->delete() ? $this->success('删除成功') : $this->fail('删除失败');
    }
}

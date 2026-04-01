<?php
declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\logic\admin\NoticeChannelLogic;
use think\Response;

/**
 * 通知渠道控制器
 */
class NoticeChannelController extends BaseAdminController
{
    /**
     * 渠道列表
     */
    public function lists(): Response
    {
        $params = $this->param();
        $result = NoticeChannelLogic::getList($params);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $result['list']
        ])->header(['X-Total' => $result['total']]);
    }

    /**
     * 渠道详情
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('渠道ID不能为空');
        }
        $info = NoticeChannelLogic::getInfo($id);
        if (empty($info)) {
            return $this->fail('渠道不存在');
        }
        return $this->data($info);
    }

    /**
     * 新增渠道
     */
    public function add(): Response
    {
        $params = $this->param();
        if (empty($params['name'])) {
            return $this->fail('渠道名称不能为空');
        }
        if (empty($params['code'])) {
            return $this->fail('渠道编码不能为空');
        }
        if (empty($params['type'])) {
            return $this->fail('请选择渠道类型');
        }
        try {
            $id = NoticeChannelLogic::add($params);
            return $this->success('添加成功', ['id' => $id]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 编辑渠道
     */
    public function edit(): Response
    {
        $params = $this->param();
        if (empty($params['id'])) {
            return $this->fail('渠道ID不能为空');
        }
        try {
            $result = NoticeChannelLogic::edit($params);
            return $result ? $this->success('编辑成功') : $this->fail('编辑失败');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 删除渠道
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('渠道ID不能为空');
        }
        $result = NoticeChannelLogic::delete($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 渠道类型枚举
     */
    public function types(): Response
    {
        $types = [
            ['value' => 1, 'label' => '邮件'],
            ['value' => 2, 'label' => '短信'],
            ['value' => 3, 'label' => '企微机器人'],
            ['value' => 4, 'label' => '站内信'],
        ];
        return $this->data($types);
    }
}

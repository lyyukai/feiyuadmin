<?php
declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\logic\admin\NoticeTemplateLogic;
use think\Response;

/**
 * 消息模板控制器
 */
class NoticeTemplateController extends BaseAdminController
{
    /**
     * 模板列表
     */
    public function lists(): Response
    {
        $params = $this->param();
        $result = NoticeTemplateLogic::getList($params);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $result['list']
        ])->header(['X-Total' => $result['total']]);
    }

    /**
     * 模板详情
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('模板ID不能为空');
        }
        $info = NoticeTemplateLogic::getInfo($id);
        if (empty($info)) {
            return $this->fail('模板不存在');
        }
        return $this->data($info);
    }

    /**
     * 新增模板
     */
    public function add(): Response
    {
        $params = $this->param();
        if (empty($params['name'])) {
            return $this->fail('模板名称不能为空');
        }
        if (empty($params['code'])) {
            return $this->fail('模板编码不能为空');
        }
        if (empty($params['channel_id'])) {
            return $this->fail('请选择通知渠道');
        }
        if (empty($params['content'])) {
            return $this->fail('模板内容不能为空');
        }
        try {
            $id = NoticeTemplateLogic::add($params);
            return $this->success('添加成功', ['id' => $id]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 编辑模板
     */
    public function edit(): Response
    {
        $params = $this->param();
        if (empty($params['id'])) {
            return $this->fail('模板ID不能为空');
        }
        try {
            $result = NoticeTemplateLogic::edit($params);
            return $result ? $this->success('编辑成功') : $this->fail('编辑失败');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 删除模板
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('模板ID不能为空');
        }
        $result = NoticeTemplateLogic::delete($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }
}

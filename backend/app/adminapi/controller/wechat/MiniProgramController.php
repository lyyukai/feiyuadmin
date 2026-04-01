<?php
/**
 * 小程序控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\wechat\MiniProgramLogic;
use think\Response;

/**
 * 小程序管理
 * Class MiniProgramController
 * @package app\adminapi\controller\wechat
 */
class MiniProgramController extends BaseAdminController
{
    /**
     * 小程序列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $list = MiniProgramLogic::getList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加小程序
     */
    public function add(): Response
    {
        $data = $this->request->post();

        if (empty($data['name']) || empty($data['appid'])) {
            return $this->fail('请填写完整信息');
        }

        $result = MiniProgramLogic::add($data);
        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 编辑小程序
     */
    public function edit(): Response
    {
        $data = $this->request->post();
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = MiniProgramLogic::edit($id, $data);
        return $result ? $this->success('编辑成功') : $this->fail('编辑失败');
    }

    /**
     * 删除小程序
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = MiniProgramLogic::delete($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 小程序详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $detail = MiniProgramLogic::getDetail($id);
        if (!$detail) {
            return $this->fail('小程序不存在');
        }

        return $this->success('获取成功', $detail);
    }

    /**
     * 版本列表
     */
    public function versionLists(): Response
    {
        $params = $this->request->get();
        $list = MiniProgramLogic::getVersionList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加版本
     */
    public function addVersion(): Response
    {
        $data = $this->request->post();

        if (empty($data['mini_program_id']) || empty($data['version'])) {
            return $this->fail('请填写完整信息');
        }

        $result = MiniProgramLogic::addVersion($data);
        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 删除版本
     */
    public function deleteVersion(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = MiniProgramLogic::deleteVersion($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 成员列表
     */
    public function memberLists(): Response
    {
        $params = $this->request->get();
        $list = MiniProgramLogic::getMemberList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加成员
     */
    public function addMember(): Response
    {
        $data = $this->request->post();

        if (empty($data['mini_program_id']) || empty($data['username'])) {
            return $this->fail('请填写完整信息');
        }

        $result = MiniProgramLogic::addMember($data);
        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 删除成员
     */
    public function deleteMember(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = MiniProgramLogic::deleteMember($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }
}

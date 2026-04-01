<?php
/**
 * 微信粉丝控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\wechat\WechatFansLogic;
use think\Response;

/**
 * 粉丝管理
 * Class WechatFansController
 * @package app\adminapi\controller\wechat
 */
class WechatFansController extends BaseAdminController
{
    /**
     * 粉丝列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $list = WechatFansLogic::getList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 粉丝详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $detail = WechatFansLogic::getDetail($id);
        if (!$detail) {
            return $this->fail('粉丝不存在');
        }

        return $this->success('获取成功', $detail);
    }

    /**
     * 更新备注
     */
    public function updateRemark(): Response
    {
        $id = (int) $this->request->post('id', 0);
        $remark = $this->request->post('remark', '');

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = WechatFansLogic::updateRemark($id, $remark);
        return $result ? $this->success('更新成功') : $this->fail('更新失败');
    }

    /**
     * 设置黑名单
     */
    public function setBlacklist(): Response
    {
        $id = (int) $this->request->post('id', 0);
        $blacklist = (int) $this->request->post('blacklist', 1);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = WechatFansLogic::setBlacklist($id, $blacklist);
        return $result ? $this->success('设置成功') : $this->fail('设置失败');
    }

    /**
     * 同步粉丝
     */
    public function sync(): Response
    {
        $account_id = (int) $this->request->post('account_id', 0);

        if ($account_id <= 0) {
            return $this->fail('请选择公众号');
        }

        // TODO: 调用微信API同步粉丝
        $result = WechatFansLogic::syncFans($account_id);
        return $result ? $this->success('同步成功') : $this->fail('同步失败');
    }

    /**
     * 粉丝统计
     */
    public function statistics(): Response
    {
        $account_id = (int) $this->request->get('account_id', 0);
        $data = WechatFansLogic::getStatistics($account_id);

        return $this->success('获取成功', $data);
    }

    /**
     * 标签列表
     */
    public function tagLists(): Response
    {
        $params = $this->request->get();
        $list = WechatFansLogic::getTagList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 创建标签
     */
    public function createTag(): Response
    {
        $data = $this->request->post();

        $result = WechatFansLogic::createTag($data);
        return $result ? $this->success('创建成功') : $this->fail('创建失败');
    }

    /**
     * 删除标签
     */
    public function deleteTag(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = WechatFansLogic::deleteTag($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }
}

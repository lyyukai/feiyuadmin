<?php
/**
 * 开放平台控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\wechat\OpenPlatformLogic;
use think\Response;

/**
 * 开放平台管理
 * Class OpenPlatformController
 * @package app\adminapi\controller\wechat
 */
class OpenPlatformController extends BaseAdminController
{
    /**
     * 平台列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $list = OpenPlatformLogic::getList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 添加平台
     */
    public function add(): Response
    {
        $data = $this->request->post();

        if (empty($data['name']) || empty($data['appid']) || empty($data['appsecret'])) {
            return $this->fail('请填写完整信息');
        }

        $result = OpenPlatformLogic::add($data);
        return $result ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 编辑平台
     */
    public function edit(): Response
    {
        $data = $this->request->post();
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = OpenPlatformLogic::edit($id, $data);
        return $result ? $this->success('编辑成功') : $this->fail('编辑失败');
    }

    /**
     * 删除平台
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = OpenPlatformLogic::delete($id);
        return $result ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 平台详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $detail = OpenPlatformLogic::getDetail($id);
        if (!$detail) {
            return $this->fail('平台不存在');
        }

        return $this->success('获取成功', $detail);
    }

    /**
     * 授权列表
     */
    public function authLists(): Response
    {
        $params = $this->request->get();
        $list = OpenPlatformLogic::getAuthList($params);

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 授权详情
     */
    public function authDetail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $detail = OpenPlatformLogic::getAuthDetail($id);
        if (!$detail) {
            return $this->fail('授权不存在');
        }

        return $this->success('获取成功', $detail);
    }

    /**
     * 获取预授权URL
     */
    public function getPreAuthUrl(): Response
    {
        $platform_id = (int) $this->request->post('platform_id', 0);
        $redirect_uri = $this->request->post('redirect_uri', '');

        if ($platform_id <= 0) {
            return $this->fail('请选择开放平台');
        }

        $url = OpenPlatformLogic::getPreAuthUrl($platform_id, $redirect_uri);
        if (empty($url)) {
            return $this->fail('生成授权链接失败');
        }

        return $this->success('获取成功', ['url' => $url]);
    }
}

<?php
/**
 * 微信自定义菜单控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\wechat;

use app\adminapi\controller\BaseAdminController;
use think\Response;

/**
 * 自定义菜单管理
 * Class WechatMenuController
 * @package app\adminapi\controller\wechat
 */
class WechatMenuController extends BaseAdminController
{
    /**
     * 菜单列表
     */
    public function lists(): Response
    {
        $page = (int) $this->request->get('page', 1);
        $limit = (int) $this->request->get('limit', 10);
        $account_id = (int) $this->request->get('account_id', 0);

        $where = [];
        if ($account_id > 0) {
            $where[] = ['account_id', '=', $account_id];
        }

        $list = \app\common\model\wechat\WechatMenu::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $this->responseList($list['data'], $list['total']);
    }

    /**
     * 保存菜单
     */
    public function save(): Response
    {
        $data = $this->request->post();

        $id = $data['id'] ?? 0;
        $account_id = $data['account_id'] ?? 0;

        if ($account_id <= 0) {
            return $this->fail('请选择公众号账号');
        }

        $menuData = [
            'account_id' => $account_id,
            'name' => $data['name'] ?? '',
            'menu_data' => json_encode($data['menu_data'] ?? [], JSON_UNESCAPED_UNICODE),
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $model = \app\common\model\wechat\WechatMenu::find($id);
            if (!$model) {
                return $this->fail('菜单不存在');
            }
            $model->name = $menuData['name'];
            $model->menu_data = $menuData['menu_data'];
            $model->update_time = $menuData['update_time'];
            $result = $model->save();
        } else {
            $menuData['create_time'] = date('Y-m-d H:i:s');
            $model = new \app\common\model\wechat\WechatMenu();
            $result = $model->save($menuData);
        }

        return $result ? $this->success('保存成功') : $this->fail('保存失败');
    }

    /**
     * 删除菜单
     */
    public function delete(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatMenu::find($id);
        if (!$model) {
            return $this->fail('菜单不存在');
        }

        return $model->delete() ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 推送菜单到微信
     */
    public function push(): Response
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatMenu::find($id);
        if (!$model) {
            return $this->fail('菜单不存在');
        }

        // 获取账号配置
        $account = \app\common\model\wechat\WechatAccount::find($model->account_id);
        if (!$account || $account->status != 1) {
            return $this->fail('公众号账号不可用');
        }

        // TODO: 调用微信API推送菜单
        // $wechat = new \EasyWeChat\OfficialAccount\Application($accountConfig);
        // $wechat->menu->create($menuData);

        return $this->success('推送成功');
    }

    /**
     * 获取菜单详情
     */
    public function detail(): Response
    {
        $id = (int) $this->request->get('id', 0);

        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $model = \app\common\model\wechat\WechatMenu::find($id);
        if (!$model) {
            return $this->fail('菜单不存在');
        }

        // 解析菜单数据
        $menuData = json_decode($model->menu_data, true) ?: [];

        return $this->success('获取成功', [
            'id' => $model->id,
            'account_id' => $model->account_id,
            'name' => $model->name,
            'menu_data' => $menuData,
            'status' => $model->status,
            'create_time' => $model->create_time,
            'update_time' => $model->update_time,
        ]);
    }
}

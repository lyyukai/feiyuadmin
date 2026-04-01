<?php
/**
 * 支付配置控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\pay;

use app\adminapi\controller\BaseAdminController;
use app\service\pay\PayConfigService;
use think\Response;

/**
 * 支付配置管理
 * Class PayConfigController
 * @package app\adminapi\controller\pay
 */
class PayConfigController extends BaseAdminController
{
    protected PayConfigService $service;

    protected function initialize(): void
    {
        parent::initialize();
        $this->service = new PayConfigService();
    }

    /**
     * 配置列表
     */
    public function lists(): Response
    {
        $params = $this->request->get();
        $result = $this->service->lists($params);
        return $this->success('获取成功', $result['list'], $result['total']);
    }

    /**
     * 保存配置
     */
    public function save(): Response
    {
        $data = $this->request->post();

        if (empty($data['channel']) || empty($data['name'])) {
            return $this->fail('参数错误');
        }

        try {
            $this->service->save($data);
            return $this->success('保存成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取配置详情
     */
    public function info(): Response
    {
        $id = (int) $this->request->get('id', 0);
        $channel = $this->request->get('channel', 'wechat');

        $data = $this->service->info($id, $channel);
        return $this->success('获取成功', $data);
    }
}

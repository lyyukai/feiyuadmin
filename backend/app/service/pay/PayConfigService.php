<?php
/**
 * 支付配置服务层
 */

declare(strict_types=1);

namespace app\service\pay;

use app\common\model\pay\PayConfig as PayConfigModel;
use think\facade\Db;

/**
 * 支付配置服务
 * Class PayConfigService
 * @package app\service\pay
 */
class PayConfigService
{
    /**
     * 获取配置列表
     */
    public function lists(array $params = []): array
    {
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 10);
        $channel = $params['channel'] ?? '';
        $status = $params['status'] ?? '';

        $where = [];
        if ($channel !== '') {
            $where[] = ['channel', '=', $channel];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int)$status];
        }

        $list = PayConfigModel::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return ['list' => $list['data'], 'total' => $list['total']];
    }

    /**
     * 获取配置详情
     */
    public function info(int $id = 0, string $channel = 'wechat'): array
    {
        if ($id > 0) {
            $model = PayConfigModel::find($id);
        } else {
            $model = PayConfigModel::where('channel', $channel)->where('status', 1)->find();
        }

        if (!$model) {
            return [];
        }

        $data = $model->toArray();
        // 敏感字段脱敏
        if (!empty($data['api_key'])) {
            $data['api_key'] = substr($data['api_key'], 0, 8) . '****' . substr($data['api_key'], -8);
        }
        if (!empty($data['alipay_public_key'])) {
            $data['alipay_public_key'] = substr($data['alipay_public_key'], 0, 20) . '****';
        }
        return $data;
    }

    /**
     * 保存配置
     */
    public function save(array $data): bool
    {
        $id = (int)($data['id'] ?? 0);
        $saveData = [
            'channel' => $data['channel'] ?? '',
            'name' => $data['name'] ?? '',
            'appid' => $data['appid'] ?? '',
            'mchid' => $data['mchid'] ?? '',
            'api_key' => $data['api_key'] ?? '',
            'api_secret' => $data['api_secret'] ?? '',
            'cert_path' => $data['cert_path'] ?? '',
            'key_path' => $data['key_path'] ?? '',
            'alipay_public_key' => $data['alipay_public_key'] ?? '',
            'notify_url' => $data['notify_url'] ?? '',
            'profit_sharing' => $data['profit_sharing'] ?? 'off',
            'status' => (int)($data['status'] ?? 1),
            'remark' => $data['remark'] ?? '',
            'update_time' => date('Y-m-d H:i:s'),
        ];

        Db::startTrans();
        try {
            if ($id > 0) {
                $model = PayConfigModel::find($id);
                if (!$model) {
                    throw new \Exception('配置不存在');
                }
                $model->save($saveData);
            } else {
                $saveData['create_time'] = date('Y-m-d H:i:s');
                PayConfigModel::create($saveData);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 获取可用配置
     */
    public function getActiveConfig(string $channel): ?PayConfigModel
    {
        return PayConfigModel::where('channel', $channel)
            ->where('status', 1)
            ->find();
    }
}

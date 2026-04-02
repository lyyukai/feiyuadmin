<?php
/**
 * 飞鱼后台管理系统 - 租户套餐管理逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\tenant;

use app\common\service\JsonService;
use app\model\TenantPackage;

/**
 * 租户套餐管理逻辑
 * Class TenantPackageLogic
 * @package app\adminapi\logic\tenant
 */
class TenantPackageLogic
{
    /**
     * 获取套餐列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;
        $keyword = $params['keyword'] ?? '';
        $status = isset($params['status']) ? (int) $params['status'] : null;

        $where = function ($query) use ($keyword, $status) {
            if (!empty($keyword)) {
                $query->whereLike('name|code', "%{$keyword}%");
            }
            if ($status !== null) {
                $query->where('status', $status);
            }
        };

        $query = TenantPackage::where($where)
            ->order('sort', 'asc')
            ->order('id', 'asc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        // 处理数据
        foreach ($list as &$item) {
            $item['status_name'] = $item['status'] === 1 ? '正常' : '禁用';
            $item['price_text'] = self::formatPrice($item['price']);
            $item['storage_text'] = self::formatStorage($item['storage_limit']);
            $item['duration_text'] = $item['duration'] > 0 ? $item['duration'] . '天' : '永久';
        }

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 获取套餐信息
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $package = TenantPackage::find($id);
        if (empty($package)) {
            JsonService::throwFail('套餐不存在');
        }
        $data = $package->toArray();
        $data['status_name'] = $data['status'] === 1 ? '正常' : '禁用';
        $data['price_text'] = self::formatPrice($data['price']);
        $data['storage_text'] = self::formatStorage($data['storage_limit']);
        $data['duration_text'] = $data['duration'] > 0 ? $data['duration'] . '天' : '永久';
        return $data;
    }

    /**
     * 添加套餐
     * @param array $params
     */
    public static function add(array $params): void
    {
        self::validate($params);

        // 检查编码唯一性
        if (TenantPackage::where('code', $params['code'])->find()) {
            JsonService::throwFail('套餐编码已存在');
        }

        $package = new TenantPackage();
        $package->name = $params['name'];
        $package->code = $params['code'];
        $package->price = (float) ($params['price'] ?? 0);
        $package->duration = (int) ($params['duration'] ?? 365);
        $package->user_limit = (int) ($params['user_limit'] ?? 0);
        $package->storage_limit = (int) ($params['storage_limit'] ?? 0);
        $package->api_limit = (int) ($params['api_limit'] ?? 0);
        $package->status = (int) ($params['status'] ?? TenantPackage::STATUS_ENABLE);
        $package->sort = (int) ($params['sort'] ?? 100);
        $package->remark = $params['remark'] ?? '';
        $package->save();
    }

    /**
     * 编辑套餐
     * @param array $params
     */
    public static function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $package = TenantPackage::find($id);
        if (empty($package)) {
            JsonService::throwFail('套餐不存在');
        }

        // 更新字段
        if (!empty($params['name'])) {
            $package->name = $params['name'];
        }
        if (!empty($params['code']) && $params['code'] !== $package->code) {
            if (TenantPackage::where('code', $params['code'])->where('id', '<>', $id)->find()) {
                JsonService::throwFail('套餐编码已存在');
            }
            $package->code = $params['code'];
        }
        if (isset($params['price'])) {
            $package->price = (float) $params['price'];
        }
        if (isset($params['duration'])) {
            $package->duration = (int) $params['duration'];
        }
        if (isset($params['user_limit'])) {
            $package->user_limit = (int) $params['user_limit'];
        }
        if (isset($params['storage_limit'])) {
            $package->storage_limit = (int) $params['storage_limit'];
        }
        if (isset($params['api_limit'])) {
            $package->api_limit = (int) $params['api_limit'];
        }
        if (isset($params['status'])) {
            $package->status = (int) $params['status'];
        }
        if (isset($params['sort'])) {
            $package->sort = (int) $params['sort'];
        }
        if (isset($params['remark'])) {
            $package->remark = $params['remark'];
        }

        $package->save();
    }

    /**
     * 删除套餐
     * @param int $id
     */
    public static function delete(int $id): void
    {
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $package = TenantPackage::find($id);
        if (empty($package)) {
            JsonService::throwFail('套餐不存在');
        }

        // 检查是否有租户使用此套餐
        $tenantCount = \app\model\Tenant::where('package_id', $id)->count();
        if ($tenantCount > 0) {
            JsonService::throwFail('有' . $tenantCount . '个租户正在使用此套餐，无法删除');
        }

        $package->delete();
    }

    /**
     * 验证参数
     * @param array $params
     */
    protected static function validate(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('套餐名称不能为空');
        }
        if (empty($params['code'])) {
            JsonService::throwFail('套餐编码不能为空');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $params['code'])) {
            JsonService::throwFail('套餐编码只能包含字母、数字和下划线');
        }
    }

    /**
     * 格式化价格
     * @param float $price
     * @return string
     */
    protected static function formatPrice(float $price): string
    {
        if ($price <= 0) {
            return '免费';
        }
        return '¥' . number_format($price, 2);
    }

    /**
     * 格式化存储
     * @param int $storage
     * @return string
     */
    protected static function formatStorage(int $storage): string
    {
        if ($storage <= 0) {
            return '无限制';
        }
        if ($storage >= 1024) {
            return round($storage / 1024, 1) . ' GB';
        }
        return $storage . ' MB';
    }
}

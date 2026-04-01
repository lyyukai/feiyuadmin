<?php
declare(strict_types=1);

namespace app\adminapi\logic\screen;

use app\adminapi\model\DataScreen;
use app\adminapi\model\ScreenComponent;
use think\Model;

/**
 * 大屏逻辑
 */
class ScreenLogic
{
    /**
     * 获取大屏列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);
        $keyword = $params['keyword'] ?? '';
        $status = $params['status'] ?? '';

        $where = [];
        if ($keyword !== '') {
            $where[] = ['name|code|description', 'like', '%' . $keyword . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }

        $list = DataScreen::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return [
            'list' => $list['data'],
            'total' => $list['total'],
        ];
    }

    /**
     * 获取大屏详情
     */
    public static function getDetail(int $id): array
    {
        $screen = DataScreen::find($id);
        if (!$screen) {
            throw new \Exception('大屏不存在');
        }

        $data = $screen->toArray();
        $data['components'] = ScreenComponent::where('screen_id', $id)
            ->order('sort', 'asc')
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 创建大屏
     */
    public static function create(array $data): DataScreen
    {
        // 检查编码唯一性
        if (DataScreen::where('code', $data['code'])->find()) {
            throw new \Exception('大屏编码已存在');
        }

        $screen = new DataScreen();
        $screen->name = $data['name'];
        $screen->code = $data['code'];
        $screen->description = $data['description'] ?? '';
        $screen->config = $data['config'] ?? [];
        $screen->status = (int) ($data['status'] ?? 1);
        $screen->save();

        return $screen;
    }

    /**
     * 更新大屏
     */
    public static function update(int $id, array $data): bool
    {
        $screen = DataScreen::find($id);
        if (!$screen) {
            throw new \Exception('大屏不存在');
        }

        // 检查编码唯一性
        if (isset($data['code']) && $data['code'] !== $screen->code) {
            if (DataScreen::where('code', $data['code'])->find()) {
                throw new \Exception('大屏编码已存在');
            }
        }

        if (isset($data['name'])) $screen->name = $data['name'];
        if (isset($data['code'])) $screen->code = $data['code'];
        if (isset($data['description'])) $screen->description = $data['description'];
        if (isset($data['config'])) $screen->config = $data['config'];
        if (isset($data['status'])) $screen->status = (int) $data['status'];

        return $screen->save();
    }

    /**
     * 删除大屏
     */
    public static function delete(int $id): bool
    {
        $screen = DataScreen::find($id);
        if (!$screen) {
            throw new \Exception('大屏不存在');
        }

        // 删除关联组件
        ScreenComponent::where('screen_id', $id)->delete();

        return $screen->delete();
    }

    /**
     * 保存大屏配置（包含组件）
     */
    public static function saveConfig(int $id, array $config, array $components): bool
    {
        $screen = DataScreen::find($id);
        if (!$screen) {
            throw new \Exception('大屏不存在');
        }

        $screen->config = $config;
        $screen->save();

        // 删除旧组件
        ScreenComponent::where('screen_id', $id)->delete();

        // 添加新组件
        foreach ($components as $index => $component) {
            $model = new ScreenComponent();
            $model->screen_id = $id;
            $model->type = $component['type'] ?? '';
            $model->name = $component['name'] ?? '';
            $model->config = $component['config'] ?? [];
            $model->data_source = $component['data_source'] ?? [];
            $model->sort = $component['sort'] ?? ($index + 1);
            $model->save();
        }

        return true;
    }

    /**
     * 添加组件
     */
    public static function addComponent(int $screenId, array $data): ScreenComponent
    {
        $screen = DataScreen::find($screenId);
        if (!$screen) {
            throw new \Exception('大屏不存在');
        }

        $component = new ScreenComponent();
        $component->screen_id = $screenId;
        $component->type = $data['type'] ?? '';
        $component->name = $data['name'] ?? '';
        $component->config = $data['config'] ?? [];
        $component->data_source = $data['data_source'] ?? [];
        $component->sort = (int) ($data['sort'] ?? 100);
        $component->save();

        return $component;
    }

    /**
     * 更新组件
     */
    public static function updateComponent(int $id, array $data): bool
    {
        $component = ScreenComponent::find($id);
        if (!$component) {
            throw new \Exception('组件不存在');
        }

        if (isset($data['type'])) $component->type = $data['type'];
        if (isset($data['name'])) $component->name = $data['name'];
        if (isset($data['config'])) $component->config = $data['config'];
        if (isset($data['data_source'])) $component->data_source = $data['data_source'];
        if (isset($data['sort'])) $component->sort = (int) $data['sort'];

        return $component->save();
    }

    /**
     * 删除组件
     */
    public static function deleteComponent(int $id): bool
    {
        $component = ScreenComponent::find($id);
        if (!$component) {
            throw new \Exception('组件不存在');
        }

        return $component->delete();
    }
}

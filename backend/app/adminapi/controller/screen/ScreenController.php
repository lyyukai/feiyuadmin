<?php
declare(strict_types=1);

namespace app\adminapi\controller\screen;

use app\adminapi\controller\Base;
use app\adminapi\logic\screen\ScreenLogic;
use think\Request;

/**
 * 数据大屏控制器
 */
class ScreenController extends Base
{
    /**
     * 大屏列表
     */
    public function list(Request $request): \think\Response
    {
        $params = $request->param();
        $result = ScreenLogic::getList($params);
        return $this->success($result);
    }

    /**
     * 大屏详情
     */
    public function detail(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        try {
            $data = ScreenLogic::getDetail($id);
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 创建大屏
     */
    public function create(Request $request): \think\Response
    {
        $data = [
            'name' => $request->param('name', ''),
            'code' => $request->param('code', ''),
            'description' => $request->param('description', ''),
            'config' => $request->param('config', []),
            'status' => $request->param('status', 1),
        ];

        if (empty($data['name'])) {
            return $this->error('大屏名称不能为空');
        }
        if (empty($data['code'])) {
            return $this->error('大屏编码不能为空');
        }

        try {
            $screen = ScreenLogic::create($data);
            return $this->success(['id' => $screen->id], '创建成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 更新大屏
     */
    public function update(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $data = [
            'name' => $request->param('name', ''),
            'code' => $request->param('code', ''),
            'description' => $request->param('description', ''),
            'config' => $request->param('config', []),
            'status' => $request->param('status', 1),
        ];

        if (empty($data['name'])) {
            return $this->error('大屏名称不能为空');
        }
        if (empty($data['code'])) {
            return $this->error('大屏编码不能为空');
        }

        try {
            ScreenLogic::update($id, $data);
            return $this->success([], '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 删除大屏
     */
    public function delete(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        try {
            ScreenLogic::delete($id);
            return $this->success([], '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 保存大屏配置
     */
    public function saveConfig(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $config = $request->param('config', []);
        $components = $request->param('components', []);

        try {
            ScreenLogic::saveConfig($id, $config, $components);
            return $this->success([], '保存成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 添加组件
     */
    public function addComponent(Request $request): \think\Response
    {
        $screenId = (int) $request->param('screen_id', 0);
        if ($screenId <= 0) {
            return $this->error('参数错误');
        }

        $data = [
            'type' => $request->param('type', ''),
            'name' => $request->param('name', ''),
            'config' => $request->param('config', []),
            'data_source' => $request->param('data_source', []),
            'sort' => $request->param('sort', 100),
        ];

        if (empty($data['type'])) {
            return $this->error('组件类型不能为空');
        }
        if (empty($data['name'])) {
            return $this->error('组件名称不能为空');
        }

        try {
            $component = ScreenLogic::addComponent($screenId, $data);
            return $this->success(['id' => $component->id], '添加成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 更新组件
     */
    public function updateComponent(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $data = [
            'type' => $request->param('type', ''),
            'name' => $request->param('name', ''),
            'config' => $request->param('config', []),
            'data_source' => $request->param('data_source', []),
            'sort' => $request->param('sort', 100),
        ];

        try {
            ScreenLogic::updateComponent($id, $data);
            return $this->success([], '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 删除组件
     */
    public function deleteComponent(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        try {
            ScreenLogic::deleteComponent($id);
            return $this->success([], '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 修改状态
     */
    public function setStatus(Request $request): \think\Response
    {
        $id = (int) $request->param('id', 0);
        $status = (int) $request->param('status', 0);

        if ($id <= 0) {
            return $this->error('参数错误');
        }

        try {
            ScreenLogic::update($id, ['status' => $status]);
            return $this->success([], '状态更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

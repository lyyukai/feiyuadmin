<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\DictType as DictTypeModel;
use app\model\DictData as DictDataModel;
use think\Request;
use think\Response;

/**
 * 数据字典控制器
 */
class Dict extends Base
{
    /**
     * 字典类型列表
     */
    public function typeList(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new DictTypeModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        return $this->page($total, $list);
    }

    /**
     * 新增字典类型
     */
    public function typeSave(Request $request): Response
    {
        $data = $request->param([
            'name' => '',
            'type' => '',
            'status' => 1,
            'remark' => '',
        ]);

        if (empty($data['name'])) {
            return $this->error('字典名称不能为空');
        }
        if (empty($data['type'])) {
            return $this->error('字典类型不能为空');
        }

        $exists = DictTypeModel::where('type', $data['type'])->find();
        if ($exists) {
            return $this->error('字典类型已存在');
        }

        $model = new DictTypeModel();
        $model->save($data);

        return $this->success(['id' => $model->id], '新增成功');
    }

    /**
     * 编辑字典类型
     */
    public function typeUpdate(Request $request, int $id): Response
    {
        $model = DictTypeModel::find($id);
        if (!$model) {
            return $this->error('字典类型不存在', 404);
        }

        $data = $request->param();
        unset($data['id']);

        if (!empty($data['type']) && $data['type'] !== $model->type) {
            $exists = DictTypeModel::where('type', $data['type'])->where('id', '<>', $id)->find();
            if ($exists) {
                return $this->error('字典类型已存在');
            }
        }

        $model->save($data);

        return $this->success([], '更新成功');
    }

    /**
     * 删除字典类型
     */
    public function typeDelete(int $id): Response
    {
        $model = DictTypeModel::find($id);
        if (!$model) {
            return $this->error('字典类型不存在', 404);
        }

        // 检查是否有关联数据
        $hasData = DictDataModel::where('dict_type', $model->type)->find();
        if ($hasData) {
            return $this->error('该类型下存在字典数据，请先删除');
        }

        $model->delete();

        return $this->success([], '删除成功');
    }

    /**
     * 字典数据列表
     */
    public function dataList(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new DictDataModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        return $this->page($total, $list);
    }

    /**
     * 按类型获取字典数据
     */
    public function dataByType(string $type): Response
    {
        $list = DictDataModel::getByType($type);

        return $this->success($list);
    }

    /**
     * 新增字典数据
     */
    public function dataSave(Request $request): Response
    {
        $data = $request->param([
            'dict_type' => '',
            'label' => '',
            'value' => '',
            'sort' => 0,
            'status' => 1,
            'remark' => '',
        ]);

        if (empty($data['dict_type'])) {
            return $this->error('字典类型不能为空');
        }
        if (empty($data['label'])) {
            return $this->error('字典标签不能为空');
        }
        if ($data['value'] === '') {
            return $this->error('字典值不能为空');
        }

        // 检查类型是否存在
        $type = DictTypeModel::where('type', $data['dict_type'])->find();
        if (!$type) {
            return $this->error('字典类型不存在');
        }

        $model = new DictDataModel();
        $model->save($data);

        return $this->success(['id' => $model->id], '新增成功');
    }

    /**
     * 编辑字典数据
     */
    public function dataUpdate(Request $request, int $id): Response
    {
        $model = DictDataModel::find($id);
        if (!$model) {
            return $this->error('字典数据不存在', 404);
        }

        $data = $request->param();
        unset($data['id']);

        $model->save($data);

        return $this->success([], '更新成功');
    }

    /**
     * 删除字典数据
     */
    public function dataDelete(int $id): Response
    {
        $model = DictDataModel::find($id);
        if (!$model) {
            return $this->error('字典数据不存在', 404);
        }

        $model->delete();

        return $this->success([], '删除成功');
    }
}

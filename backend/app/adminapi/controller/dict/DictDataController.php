<?php
/**
 * 飞羽后台管理系统 - 字典数据管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\dict;

use app\adminapi\controller\BaseAdminController;
use app\model\DictData;
use app\model\DictType;
use think\Response;

/**
 * 字典数据管理控制器
 * Class DictDataController
 * @package app\adminapi\controller\dict
 */
class DictDataController extends BaseAdminController
{
    /**
     * 字典数据列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        $page = (int) ($params['page'] ?? 1);
        $pageSize = (int) ($params['page_size'] ?? 20);
        $keyword = $params['keyword'] ?? '';
        $type = $params['type'] ?? '';

        $where = [];
        if (!empty($type)) {
            $where[] = ['type_id', '=', $type];
        }
        if (!empty($keyword)) {
            $where[] = ['label|value', 'like', '%' . $keyword . '%'];
        }

        $total = DictData::where($where)->count();
        $list = DictData::where($where)
            ->order('sort', 'asc')
            ->order('id', 'asc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();

        return $this->responseList($list, $total);
    }

    /**
     * 根据字典类型获取数据（用于前端-select组件）
     * @return Response
     */
    public function type(): Response
    {
        $type = $this->param('type', '');
        if (empty($type)) {
            return $this->fail('字典类型不能为空');
        }

        $list = DictData::where('type_id', $type)
            ->where('status', 1)
            ->order('sort', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();

        return $this->data($list);
    }

    /**
     * 添加字典数据
     * @return Response
     */
    public function add(): Response
    {
        $params = $this->param();
        $dictType = isset($params["type_id"]) ? (int)$params["type_id"] : 0;
        $label = trim($params['label'] ?? '');
        $value = $params['value'] ?? '';
        $sort = (int) ($params['sort'] ?? 0);
        $status = (int) ($params['status'] ?? 1);
        $remark = $params['remark'] ?? '';

        if (empty($dictType)) {
            return $this->fail('字典类型不能为空');
        }
        if (empty($label)) {
            return $this->fail('字典标签不能为空');
        }
        if ($value === '') {
            return $this->fail('字典值不能为空');
        }

        // 检查字典类型是否存在
        $typeExists = DictType::where('id', $dictType)->find();
        if (!$typeExists) {
            return $this->fail('字典类型不存在');
        }

        $model = new DictData();
        $model->save([
            'type_id' => $dictType,
            'label' => $label,
            'value' => $value,
            'sort' => $sort,
            'status' => $status,
            'remark' => $remark,
        ]);

        return $this->success('添加成功');
    }

    /**
     * 编辑字典数据
     * @return Response
     */
    public function edit(): Response
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        $label = trim($params['label'] ?? '');
        $value = $params['value'] ?? '';
        $sort = (int) ($params['sort'] ?? 0);
        $status = (int) ($params['status'] ?? 1);
        $remark = $params['remark'] ?? '';

        if ($id <= 0) {
            return $this->fail('ID不能为空');
        }
        if (empty($label)) {
            return $this->fail('字典标签不能为空');
        }
        if ($value === '') {
            return $this->fail('字典值不能为空');
        }

        $model = DictData::find($id);
        if (!$model) {
            return $this->fail('字典数据不存在');
        }

        $model->save([
            'label' => $label,
            'value' => $value,
            'sort' => $sort,
            'status' => $status,
            'remark' => $remark,
        ]);

        return $this->success('编辑成功');
    }

    /**
     * 删除字典数据
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('ID不能为空');
        }

        $model = DictData::find($id);
        if (!$model) {
            return $this->fail('字典数据不存在');
        }

        $model->delete();

        return $this->success('删除成功');
    }
}

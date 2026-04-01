<?php
/**
 * 飞羽后台管理系统 - 字典类型管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\dict;

use app\adminapi\controller\BaseAdminController;
use app\model\DictType;
use think\Request;
use think\Response;

/**
 * 字典类型管理控制器
 * Class DictTypeController
 * @package app\adminapi\controller\dict
 */
class DictTypeController extends BaseAdminController
{
    /**
     * 字典类型列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        $page = (int) ($params['page'] ?? 1);
        $pageSize = (int) ($params['page_size'] ?? 20);
        $keyword = $params['keyword'] ?? '';

        $where = [];
        if (!empty($keyword)) {
            $where[] = ['name|type', 'like', '%' . $keyword . '%'];
        }

        $total = DictType::where($where)->count();
        $list = DictType::where($where)
            ->order('id', 'asc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();

        return $this->responseList($list, $total);
    }

    /**
     * 字典类型全部列表
     * @return Response
     */
    public function all(): Response
    {
        $list = DictType::order('id', 'asc')->select()->toArray();
        return $this->data($list);
    }

    /**
     * 添加字典类型
     * @return Response
     */
    public function add(): Response
    {
        $params = $this->param();
        $name = trim($params['name'] ?? '');
        $type = trim($params['code'] ?? '');
        $remark = $params['remark'] ?? '';
        $status = (int) ($params['status'] ?? 1);

        if (empty($name)) {
            return $this->fail('字典名称不能为空');
        }
        if (empty($type)) {
            return $this->fail('字典类型不能为空');
        }

        // 检查类型是否已存在
        $exists = DictType::where('code', $type)->find();
        if ($exists) {
            return $this->fail('字典类型已存在');
        }

        $model = new DictType();
        $model->save([
            'name' => $name,
            'code' => $type,
            'remark' => $remark,
            'status' => $status,
        ]);

        return $this->success('添加成功');
    }

    /**
     * 编辑字典类型
     * @return Response
     */
    public function edit(): Response
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        $name = trim($params['name'] ?? '');
        $type = trim($params['code'] ?? '');
        $remark = $params['remark'] ?? '';
        $status = (int) ($params['status'] ?? 1);

        if ($id <= 0) {
            return $this->fail('ID不能为空');
        }
        if (empty($name)) {
            return $this->fail('字典名称不能为空');
        }
        if (empty($type)) {
            return $this->fail('字典类型不能为空');
        }

        $model = DictType::find($id);
        if (!$model) {
            return $this->fail('字典类型不存在');
        }

        // 检查类型是否与其他记录冲突
        $exists = DictType::where('code', $type)->where('id', '<>', $id)->find();
        if ($exists) {
            return $this->fail('字典类型已存在');
        }

        $model->save([
            'name' => $name,
            'code' => $type,
            'remark' => $remark,
            'status' => $status,
        ]);

        return $this->success('编辑成功');
    }

    /**
     * 删除字典类型
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('ID不能为空');
        }

        $model = DictType::find($id);
        if (!$model) {
            return $this->fail('字典类型不存在');
        }

        $model->delete();

        return $this->success('删除成功');
    }
}

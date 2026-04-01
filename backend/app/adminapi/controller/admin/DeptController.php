<?php
/**
 * 飞羽后台管理系统 - 部门管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;

/**
 * 部门管理控制器
 * Class DeptController
 * @package app\adminapi\controller\admin
 */
class DeptController extends BaseAdminController
{
    /**
     * 部门列表
     */
    public function lists()
    {
        $list = \app\model\Dept::order('sort', 'asc')->select()->toArray();
        return json(['code' => 0, 'msg' => '', 'data' => $list, 'total' => count($list)]);
    }

    /**
     * 所有部门（无分页）
     */
    public function all(): \think\Response
    {
        $list = \app\model\Dept::where('status', 1)->order('sort', 'asc')->select()->toArray();
        return $this->data($list);
    }

    /**
     * 部门树
     */
    public function tree()
    {
        $list = \app\model\Dept::order('sort', 'asc')->select()->toArray();
        return $this->data($this->buildTree($list));
    }

    /**
     * 添加部门
     */
    public function add()
    {
        $params = $this->param();
        $dept = new \app\model\Dept();
        $dept->pid = (int) ($params['pid'] ?? 0);
        $dept->name = $params['name'] ?? '';
        $dept->leader = $params['leader'] ?? '';
        $dept->mobile = $params['mobile'] ?? '';
        $dept->sort = (int) ($params['sort'] ?? 0);
        $dept->status = (int) ($params['status'] ?? 1);
        $dept->save();
        return $this->success('添加成功');
    }

    /**
     * 编辑部门
     */
    public function edit()
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        $dept = \app\model\Dept::find($id);
        if (!$dept) {
            return $this->fail('部门不存在');
        }
        if (!empty($params['name'])) $dept->name = $params['name'];
        if (isset($params['leader'])) $dept->leader = $params['leader'];
        if (isset($params['mobile'])) $dept->mobile = $params['mobile'];
        if (isset($params['sort'])) $dept->sort = (int) $params['sort'];
        if (isset($params['status'])) $dept->status = (int) $params['status'];
        $dept->save();
        return $this->success('编辑成功');
    }

    /**
     * 删除部门
     */
    public function delete()
    {
        $id = (int) ($this->param('id', 0));
        $childCount = \app\model\Dept::where('pid', $id)->count();
        if ($childCount > 0) {
            return $this->fail('请先删除子部门');
        }
        $dept = \app\model\Dept::find($id);
        if (!$dept) {
            return $this->fail('部门不存在');
        }
        $dept->delete();
        return $this->success('删除成功');
    }

    /**
     * 构建树形结构
     */
    protected function buildTree(array $list, int $pid = 0): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ($item['pid'] === $pid) {
                $children = $this->buildTree($list, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
}

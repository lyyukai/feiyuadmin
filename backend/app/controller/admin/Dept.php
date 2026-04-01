<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Dept as DeptModel;
use think\Request;
use think\Response;

/**
 * 部门控制器 - 部门CRUD
 */
class Dept
{
    protected Request $request;

    public function __construct()
    {
        $this->request = request();
    }

    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function error(string $msg = '操作失败', int $code = 400): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => []]);
    }

    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    /**
     * 部门树形列表
     * GET /api/dept/tree
     */
    public function tree(): Response
    {
        $tree = DeptModel::getTree();
        return $this->success($tree);
    }

    /**
     * 部门平铺列表
     * GET /api/dept/list
     */
    public function list(): Response
    {
        $keyword = $this->param('keyword', '');

        $query = DeptModel::whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('name', "%{$keyword}%")
                  ->whereOr('leader', 'like', "%{$keyword}%");
            });
        }

        $list = $query->order('sort', 'asc')->select()->toArray();

        return $this->success($list);
    }

    /**
     * 新增部门
     * POST /api/dept
     */
    public function save(): Response
    {
        $name = $this->param('name', '');
        if (empty($name)) {
            return $this->error('部门名称不能为空');
        }

        $dept = new DeptModel();
        $dept->name   = $name;
        $dept->pid    = (int) $this->param('pid', 0);
        $dept->path   = (string) $this->param('path', '');
        $dept->leader = $this->param('leader', '');
        $dept->mobile = $this->param('mobile', '');
        $dept->email  = $this->param('email', '');
        $dept->sort   = (int) $this->param('sort', 0);
        $dept->status = (int) $this->param('status', 1);
        $dept->save();

        // 更新path
        if ($dept->pid == 0) {
            $dept->path = (string) $dept->id;
        } else {
            $parent = DeptModel::find($dept->pid);
            $dept->path = $parent ? $parent->path . ',' . $dept->id : (string) $dept->id;
        }
        $dept->save();

        return $this->success(['id' => $dept->id], '新增成功');
    }

    /**
     * 编辑部门
     * PUT /api/dept/:id
     */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $dept = DeptModel::find($id);
        if (!$dept) {
            return $this->error('部门不存在');
        }

        $name   = $this->param('name', '');
        $pid    = $this->param('pid', '');
        $path   = $this->param('path', '');
        $leader = $this->param('leader', '');
        $mobile = $this->param('mobile', '');
        $email  = $this->param('email', '');
        $sort   = $this->param('sort', '');
        $status = $this->param('status', '');

        if ($name !== '')   $dept->name   = $name;
        if ($leader !== '') $dept->leader = $leader;
        if ($mobile !== '')$dept->mobile = $mobile;
        if ($email !== '') $dept->email  = $email;
        if ($sort !== '')   $dept->sort   = (int) $sort;
        if ($status !== '') $dept->status = (int) $status;

        // 防止将父级设为子级
        if ($pid !== '' && (int) $pid !== $dept->pid) {
            $newPid = (int) $pid;
            if ($newPid == $id) {
                return $this->error('父级不能是自己');
            }
            // 检查是否是将祖先设为父级
            $ancestorPath = DeptModel::where('id', $newPid)->value('path');
            if ($ancestorPath && str_contains($ancestorPath, (string) $id)) {
                return $this->error('不能将子级设为父级');
            }
            $dept->pid = $newPid;
        }

        if ($dept->pid == 0) {
            $dept->path = (string) $dept->id;
        } else {
            $parent = DeptModel::find($dept->pid);
            $dept->path = $parent ? $parent->path . ',' . $dept->id : (string) $dept->id;
        }

        $dept->save();

        return $this->success([], '更新成功');
    }

    /**
     * 删除部门
     * DELETE /api/dept/:id
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        // 检查是否有子部门
        if (DeptModel::hasChildren($id)) {
            return $this->error('该部门下存在子部门，请先删除', 400);
        }

        // 检查部门下是否有用户
        if (DeptModel::hasUsers($id)) {
            return $this->error('该部门下存在用户，请先转移', 400);
        }

        $dept = DeptModel::find($id);
        if (!$dept) {
            return $this->error('部门不存在');
        }

        $dept->delete();

        return $this->success([], '删除成功');
    }
}

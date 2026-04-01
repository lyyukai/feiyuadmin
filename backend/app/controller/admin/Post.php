<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\Post as PostModel;
use think\Request;
use think\Response;

/**
 * 岗位控制器 - 岗位CRUD
 */
class Post
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

    protected function page(int $total, array $list): Response
    {
        return json(['code' => 0, 'msg' => 'success', 'total' => $total, 'data' => $list]);
    }

    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    protected function getPageParam(): array
    {
        $page  = (int) $this->param('page', 1);
        $limit = (int) $this->param('limit', 20);
        $limit = $limit > 100 ? 100 : $limit;
        return [$page, $limit];
    }

    /**
     * 岗位列表
     * GET /api/post/list
     */
    public function list(): Response
    {
        [$page, $limit] = $this->getPageParam();
        $keyword = $this->param('keyword', '');
        $status  = $this->param('status', '');

        $query = PostModel::whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('name', "%{$keyword}%")
                  ->whereOr('code', 'like', "%{$keyword}%");
            });
        }

        if ($status !== '') {
            $query->where('status', (int) $status);
        }

        $total = $query->count();
        $list  = $query->page($page, $limit)
            ->order('sort', 'asc')
            ->select()
            ->toArray();

        return $this->page($total, $list);
    }

    /**
     * 新增岗位
     * POST /api/post
     */
    public function save(): Response
    {
        $name = $this->param('name', '');
        $code = $this->param('code', '');

        if (empty($name) || empty($code)) {
            return $this->error('岗位名称和岗位代码不能为空');
        }

        // 检查岗位代码唯一性
        if (PostModel::where('code', $code)->whereNull('delete_time')->find()) {
            return $this->error('岗位代码已存在');
        }

        $post = new PostModel();
        $post->name   = $name;
        $post->code   = $code;
        $post->sort   = (int) $this->param('sort', 0);
        $post->status = (int) $this->param('status', 1);
        $post->remark = $this->param('remark', '');
        $post->save();

        return $this->success(['id' => $post->id], '新增成功');
    }

    /**
     * 编辑岗位
     * PUT /api/post/:id
     */
    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $post = PostModel::find($id);
        if (!$post) {
            return $this->error('岗位不存在');
        }

        $name   = $this->param('name', '');
        $code   = $this->param('code', '');
        $sort   = $this->param('sort', '');
        $status = $this->param('status', '');
        $remark = $this->param('remark', '');

        if ($name !== '')   $post->name   = $name;
        if ($code !== '')   $post->code   = $code;
        if ($sort !== '')   $post->sort   = (int) $sort;
        if ($status !== '') $post->status = (int) $status;
        if ($remark !== '') $post->remark = $remark;

        $post->save();

        return $this->success([], '更新成功');
    }

    /**
     * 删除岗位
     * DELETE /api/post/:id
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $post = PostModel::find($id);
        if (!$post) {
            return $this->error('岗位不存在');
        }

        $post->delete();

        return $this->success([], '删除成功');
    }

    /**
     * 获取所有岗位（下拉框）
     * GET /api/post/all
     */
    public function all(): Response
    {
        $list = PostModel::whereNull('delete_time')
            ->where('status', 1)
            ->order('sort', 'asc')
            ->column('name', 'id');

        $result = [];
        foreach ($list as $id => $name) {
            $result[] = ['id' => $id, 'name' => $name];
        }

        return $this->success($result);
    }
}

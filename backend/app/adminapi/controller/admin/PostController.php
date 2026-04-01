<?php
/**
 * 飞羽后台管理系统 - 岗位管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;

/**
 * 岗位管理控制器
 * Class PostController
 * @package app\adminapi\controller\admin
 */
class PostController extends BaseAdminController
{
    /**
     * 岗位列表
     */
    public function lists()
    {
        $list = \app\model\Post::order('id', 'asc')->select()->toArray();
        return json(['code' => 0, 'msg' => '', 'data' => $list, 'total' => count($list)]);
    }

    /**
     * 所有岗位（无分页）
     */
    public function all(): \think\Response
    {
        $list = \app\model\Post::where('status', 1)->order('id', 'asc')->select()->toArray();
        return $this->data($list);
    }

    /**
     * 添加岗位
     */
    public function add()
    {
        $params = $this->param();
        $post = new \app\model\Post();
        $post->name = $params['name'] ?? '';
        $post->code = $params['code'] ?? '';
        $post->sort = (int) ($params['sort'] ?? 0);
        $post->status = (int) ($params['status'] ?? 1);
        $post->save();
        return $this->success('添加成功');
    }

    /**
     * 编辑岗位
     */
    public function edit()
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        $post = \app\model\Post::find($id);
        if (!$post) {
            return $this->fail('岗位不存在');
        }
        if (!empty($params['name'])) $post->name = $params['name'];
        if (!empty($params['code'])) $post->code = $params['code'];
        if (isset($params['sort'])) $post->sort = (int) $params['sort'];
        if (isset($params['status'])) $post->status = (int) $params['status'];
        $post->save();
        return $this->success('编辑成功');
    }

    /**
     * 删除岗位
     */
    public function delete()
    {
        $id = (int) ($this->param('id', 0));
        $post = \app\model\Post::find($id);
        if (!$post) {
            return $this->fail('岗位不存在');
        }
        $post->delete();
        return $this->success('删除成功');
    }
}

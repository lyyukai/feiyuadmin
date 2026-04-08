<?php
/**
 * 移动端H5 - 文章分类控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

use app\model\ContentCategory as CategoryModel;
use think\response\Json;

class CategoryController extends BaseMobileController
{
    /**
     * 分类列表（树形）
     */
    public function lists(): Json
    {
        $list = CategoryModel::order('sort', 'asc')->select()->toArray();

        // 构建树形结构
        $tree = $this->buildTree($list, 0);
        return $this->success('获取成功', $tree);
    }

    /**
     * 递归构建树形
     */
    private function buildTree(array $list, int $pid): array
    {
        $result = [];
        foreach ($list as $item) {
            if ((int) $item['pid'] === $pid) {
                $children = $this->buildTree($list, (int) $item['id']);
                $item['children'] = $children;
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 新增/编辑分类
     */
    public function save(): Json
    {
        $id   = (int) $this->request->put('id', $this->request->post('id', 0));
        $data = [
            'name'  => $this->request->param('name', ''),
            'code'  => $this->request->param('code', ''),
            'pid'   => (int) $this->request->param('pid', 0),
            'sort'  => (int) $this->request->param('sort', 100),
            'status'=> (int) $this->request->param('status', 1),
        ];

        if (!$data['name']) {
            return $this->fail('请填写分类名称');
        }
        if (!$data['code']) {
            return $this->fail('请填写分类编码');
        }

        // 编码唯一性
        $exist = CategoryModel::where('code', $data['code'])->where('id', '<>', $id)->find();
        if ($exist) {
            return $this->fail('分类编码已存在');
        }

        try {
            if ($id > 0) {
                $category = CategoryModel::find($id);
                if (!$category) {
                    return $this->fail('分类不存在');
                }
                $category->save($data);
            } else {
                CategoryModel::create($data);
            }
            return $this->success('保存成功');
        } catch (\Throwable $e) {
            return $this->fail('保存失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除分类
     */
    public function delete(): Json
    {
        $id = (int) $this->request->delete('id', $this->request->param('id', 0));
        if (!$id) {
            return $this->fail('缺少分类ID');
        }

        // 检查是否有子分类
        $childCount = CategoryModel::where('pid', $id)->count();
        if ($childCount > 0) {
            return $this->fail('请先删除子分类');
        }

        // 检查是否有文章
        $articleCount = \app\model\ContentArticle::where('category_id', $id)->count();
        if ($articleCount > 0) {
            return $this->fail('该分类下有文章，请先移除文章');
        }

        $category = CategoryModel::find($id);
        if (!$category) {
            return $this->fail('分类不存在');
        }

        $category->delete();
        return $this->success('删除成功');
    }
}

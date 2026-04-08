<?php
/**
 * 移动端H5 - 文章控制器
 */
declare(strict_types=1);

namespace app\adminapi\controller\mobile;

use app\model\ContentArticle as ArticleModel;
use app\model\ContentCategory as CategoryModel;
use think\facade\Db;
use think\response\Json;

class ArticleController extends BaseMobileController
{
    /**
     * 文章列表
     */
    public function lists(): Json
    {
        $page      = (int) $this->request->get('page', 1);
        $pageSize  = (int) $this->request->get('page_size', 10);
        $keyword   = $this->request->get('keyword', '');
        $categoryId = $this->request->get('category_id', '');
        $status    = $this->request->get('status', '');
        $startDate = $this->request->get('start_date', '');
        $endDate   = $this->request->get('end_date', '');

        $where = [];
        if ($keyword !== '') {
            $where[] = ['title|author', 'like', "%{$keyword}%"];
        }
        if ($categoryId !== '') {
            $where[] = ['category_id', '=', (int) $categoryId];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }
        if ($startDate) {
            $where[] = ['create_time', '>=', $startDate . ' 00:00:00'];
        }
        if ($endDate) {
            $where[] = ['create_time', '<=', $endDate . ' 23:59:59'];
        }

        $total = ArticleModel::where($where)->count();
        $list   = ArticleModel::where($where)
            ->order('id', 'desc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();

        // 补充分类名称
        $categoryIds = array_filter(array_column($list, 'category_id'));
        $categories  = [];
        if ($categoryIds) {
            $cats = CategoryModel::whereIn('id', $categoryIds)->column('name', 'id');
            foreach ($categories as $id => $name) {
                // already set
            }
            $categories = $cats;
        }

        foreach ($list as &$item) {
            $item['category_name'] = $categories[$item['category_id']] ?? '';
        }

        return $this->success('获取成功', [
            'list'  => $list,
            'total' => $total,
            'page'  => $page,
        ]);
    }

    /**
     * 文章详情
     */
    public function detail(): Json
    {
        $id = (int) $this->request->get('id', 0);
        if (!$id) {
            return $this->fail('缺少文章ID');
        }

        $article = ArticleModel::find($id);
        if (!$article) {
            return $this->fail('文章不存在');
        }

        // 浏览量+1
        $article->views = $article->views + 1;
        $article->save();

        $data = $article->toArray();
        $data['category_name'] = CategoryModel::where('id', $data['category_id'] ?? 0)->value('name') ?? '';

        return $this->success('获取成功', $data);
    }

    /**
     * 新增/编辑文章
     */
    public function save(): Json
    {
        $id = (int) $this->request->put('id', $this->request->post('id', 0));

        $data = [
            'title'       => $this->request->param('title', ''),
            'category_id' => (int) $this->request->param('category_id', 0) ?: null,
            'author'      => $this->request->param('author', ''),
            'summary'     => $this->request->param('summary', ''),
            'content'     => $this->request->param('content', ''),
            'cover_image' => $this->request->param('cover_image', ''),
            'tags'        => $this->request->param('tags', ''),
            'status'      => (int) $this->request->param('status', 0),
        ];

        if (!$data['title']) {
            return $this->fail('请填写标题');
        }

        try {
            if ($id > 0) {
                $article = ArticleModel::find($id);
                if (!$article) {
                    return $this->fail('文章不存在');
                }
                $article->save($data);
            } else {
                ArticleModel::create($data);
            }
            return $this->success('保存成功');
        } catch (\Throwable $e) {
            return $this->fail('保存失败: ' . $e->getMessage());
        }
    }

    /**
     * 删除文章
     */
    public function delete(): Json
    {
        $id = (int) $this->request->delete('id', $this->request->param('id', 0));
        if (!$id) {
            return $this->fail('缺少文章ID');
        }

        $article = ArticleModel::find($id);
        if (!$article) {
            return $this->fail('文章不存在');
        }

        $article->delete();
        return $this->success('删除成功');
    }
}

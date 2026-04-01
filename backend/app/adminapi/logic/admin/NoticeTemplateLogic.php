<?php
declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\model\NoticeTemplate;

/**
 * 通知模板逻辑
 */
class NoticeTemplateLogic
{
    /**
     * 获取模板列表
     */
    public static function getList(array $params): array
    {
        $page = (int)($params['page'] ?? 1);
        $pageSize = min((int)($params['page_size'] ?? 20), 100);
        $offset = ($page - 1) * $pageSize;

        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['name|code', 'like', "%{$params['keyword']}%"];
        }
        if (!empty($params['channel_id'])) {
            $where[] = ['channel_id', '=', (int)$params['channel_id']];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', (int)$params['status']];
        }

        $list = NoticeTemplate::where($where)
            ->order('id', 'desc')
            ->limit($offset, $pageSize)
            ->select()
            ->toArray();

        $total = NoticeTemplate::where($where)->count();

        return ['list' => $list, 'total' => $total, 'page' => $page, 'page_size' => $pageSize];
    }

    /**
     * 获取模板详情
     */
    public static function getInfo(int $id): array
    {
        $template = NoticeTemplate::find($id);
        return $template ? $template->toArray() : [];
    }

    /**
     * 添加模板
     */
    public static function add(array $params): int
    {
        $name = $params['name'] ?? '';
        $code = $params['code'] ?? '';
        
        if (empty($name)) {
            throw new \Exception('模板名称不能为空');
        }
        if (empty($code)) {
            throw new \Exception('模板编码不能为空');
        }
        
        // 检查编码唯一性
        $exists = NoticeTemplate::where('code', $code)->find();
        if ($exists) {
            throw new \Exception('模板编码已存在');
        }

        $template = new NoticeTemplate();
        $template->name = $name;
        $template->code = $code;
        $template->channel_id = (int)($params['channel_id'] ?? 0);
        $template->content = $params['content'] ?? '';
        $vars = $params['vars'] ?? '';
        if (is_array($vars)) {
            $vars = implode(',', $vars);
        }
        $template->vars = $vars;
        $template->status = (int)($params['status'] ?? 1);
        $template->save();

        return $template->id;
    }

    /**
     * 编辑模板
     */
    public static function edit(array $params): bool
    {
        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            return false;
        }

        $template = NoticeTemplate::find($id);
        if (!$template) {
            return false;
        }

        if (isset($params['name'])) {
            $template->name = $params['name'];
        }
        if (isset($params['code'])) {
            if ($params['code'] !== $template->code) {
                $exists = NoticeTemplate::where('code', $params['code'])->where('id', '<>', $id)->find();
                if ($exists) {
                    throw new \Exception('模板编码已存在');
                }
            }
            $template->code = $params['code'];
        }
        if (isset($params['channel_id'])) {
            $template->channel_id = (int)$params['channel_id'];
        }
        if (isset($params['content'])) {
            $template->content = $params['content'];
        }
        if (isset($params['vars'])) {
            $vars = $params['vars'];
            $template->vars = is_array($vars) ? implode(',', $vars) : $vars;
        }
        if (isset($params['status'])) {
            $template->status = (int)$params['status'];
        }

        return $template->save();
    }

    /**
     * 删除模板
     */
    public static function delete(int $id): bool
    {
        $template = NoticeTemplate::find($id);
        if (!$template) {
            return false;
        }
        return $template->delete();
    }
}

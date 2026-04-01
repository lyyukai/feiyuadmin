<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\Config as ConfigModel;
use think\Request;
use think\Response;

/**
 * 参数配置控制器
 */
class Config extends Base
{
    /**
     * 配置分组列表
     */
    public function groups(): Response
    {
        $groups = [
            ['key' => 'basic', 'name' => '基础配置'],
            ['key' => 'upload', 'name' => '上传配置'],
            ['key' => 'email', 'name' => '邮件配置'],
            ['key' => 'sms', 'name' => '短信配置'],
            ['key' => 'oss', 'name' => '对象存储'],
        ];

        return $this->success($groups);
    }

    /**
     * 配置列表
     */
    public function list(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new ConfigModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        return $this->page($total, $list);
    }

    /**
     * 按分组获取配置
     */
    public function group(string $group): Response
    {
        $configs = ConfigModel::getByGroup($group);

        return $this->success($configs);
    }

    /**
     * 新增配置
     */
    public function save(Request $request): Response
    {
        $data = $request->param([
            'name' => '',
            'group' => 'basic',
            'key' => '',
            'value' => '',
            'type' => 'text',
            'options' => '',
            'sort' => 0,
            'remark' => '',
        ]);

        if (empty($data['name'])) {
            return $this->error('配置名称不能为空');
        }
        if (empty($data['key'])) {
            return $this->error('配置键不能为空');
        }

        // 检查键唯一性
        $exists = ConfigModel::where('key', $data['key'])->find();
        if ($exists) {
            return $this->error('配置键已存在');
        }

        $model = new ConfigModel();
        $model->save($data);

        return $this->success(['id' => $model->id], '新增成功');
    }

    /**
     * 编辑配置
     */
    public function update(Request $request, int $id): Response
    {
        $model = ConfigModel::find($id);
        if (!$model) {
            return $this->error('配置不存在', 404);
        }

        $data = $request->param([
            'name' => '',
            'group' => '',
            'key' => '',
            'value' => '',
            'type' => '',
            'options' => '',
            'sort' => 0,
            'remark' => '',
        ]);

        if (!empty($data['key']) && $data['key'] !== $model->key) {
            $exists = ConfigModel::where('key', $data['key'])->where('id', '<>', $id)->find();
            if ($exists) {
                return $this->error('配置键已存在');
            }
        }

        // 过滤空字段，保留原值
        $data = array_filter($data, fn($v) => $v !== '');
        $model->save($data);

        return $this->success([], '更新成功');
    }

    /**
     * 删除配置
     */
    public function delete(int $id): Response
    {
        $model = ConfigModel::find($id);
        if (!$model) {
            return $this->error('配置不存在', 404);
        }

        $model->delete();

        return $this->success([], '删除成功');
    }

    /**
     * 批量保存配置
     */
    public function batch(Request $request): Response
    {
        $group = $request->param('group', '');
        $configs = $request->param('configs', []);

        if (empty($group)) {
            return $this->error('配置分组不能为空');
        }

        if (!is_array($configs) || empty($configs)) {
            return $this->error('配置数据无效');
        }

        foreach ($configs as $key => $value) {
            ConfigModel::where('group', $group)
                ->where('key', $key)
                ->update(['value' => $value, 'update_time' => date('Y-m-d H:i:s')]);
        }

        return $this->success([], '保存成功');
    }
}

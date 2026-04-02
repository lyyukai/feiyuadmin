<?php
/**
 * 飞鱼后台管理系统 - 字典管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;

/**
 * 字典管理控制器
 * Class DictController
 * @package app\adminapi\controller\admin
 */
class DictController extends BaseAdminController
{
    /**
     * 字典类型列表
     */
    public function typeLists()
    {
        $list = \think\facade\Db::name('dict_type')->order('id', 'asc')->select()->toArray();
        return $this->data(['list' => $list, 'total' => count($list)]);
    }

    /**
     * 添加字典类型
     */
    public function typeAdd()
    {
        $params = $this->param();
        \think\facade\Db::name('dict_type')->insert([
            'name' => $params['name'] ?? '',
            'type' => $params['type'] ?? '',
            'remark' => $params['remark'] ?? '',
            'status' => (int) ($params['status'] ?? 1),
            'create_time' => time(),
        ]);
        return $this->success('添加成功');
    }

    /**
     * 编辑字典类型
     */
    public function typeEdit()
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        \think\facade\Db::name('dict_type')->where('id', $id)->update([
            'name' => $params['name'] ?? '',
            'type' => $params['type'] ?? '',
            'remark' => $params['remark'] ?? '',
            'status' => (int) ($params['status'] ?? 1),
        ]);
        return $this->success('编辑成功');
    }

    /**
     * 删除字典类型
     */
    public function typeDelete()
    {
        $id = (int) ($this->param('id', 0));
        \think\facade\Db::name('dict_type')->where('id', $id)->delete();
        return $this->success('删除成功');
    }

    /**
     * 字典数据列表
     */
    public function dataLists()
    {
        $list = \think\facade\Db::name('dict_data')->order('id', 'asc')->select()->toArray();
        return $this->data(['list' => $list, 'total' => count($list)]);
    }

    /**
     * 根据类型获取字典数据
     */
    public function dataByType()
    {
        $type = $this->param('type', '');
        $list = \think\facade\Db::name('dict_data')->where('type', $type)->where('status', 1)->order('sort', 'asc')->select()->toArray();
        return $this->data($list);
    }

    /**
     * 添加字典数据
     */
    public function dataAdd()
    {
        $params = $this->param();
        \think\facade\Db::name('dict_data')->insert([
            'type' => $params['type'] ?? '',
            'name' => $params['name'] ?? '',
            'value' => $params['value'] ?? '',
            'sort' => (int) ($params['sort'] ?? 0),
            'status' => (int) ($params['status'] ?? 1),
            'create_time' => time(),
        ]);
        return $this->success('添加成功');
    }

    /**
     * 编辑字典数据
     */
    public function dataEdit()
    {
        $params = $this->param();
        $id = (int) ($params['id'] ?? 0);
        \think\facade\Db::name('dict_data')->where('id', $id)->update([
            'name' => $params['name'] ?? '',
            'value' => $params['value'] ?? '',
            'sort' => (int) ($params['sort'] ?? 0),
            'status' => (int) ($params['status'] ?? 1),
        ]);
        return $this->success('编辑成功');
    }

    /**
     * 删除字典数据
     */
    public function dataDelete()
    {
        $id = (int) ($this->param('id', 0));
        \think\facade\Db::name('dict_data')->where('id', $id)->delete();
        return $this->success('删除成功');
    }
}

<?php
declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;
use app\common\service\JsonService;
use think\facade\Db;

class SystemConfigController extends BaseAdminController
{
    // 获取配置列表
    public function lists()
    {
        $list = Db::name('config')->order('id', 'asc')->select()->toArray();
        $data = [];
        foreach ($list as $item) {
            $data[$item['key']] = $item['value'];
        }
        return JsonService::success('获取成功', $data);
    }

    // 保存配置
    public function save()
    {
        $params = $this->request->post();
        
        foreach ($params as $key => $value) {
            if (in_array($key, ['token', 'sign', 'timestamp'])) continue;
            
            $exists = Db::name('config')->where('key', $key)->find();
            if ($exists) {
                Db::name('config')->where('key', $key)->update(['value' => $value, 'update_time' => date('Y-m-d H:i:s')]);
            } else {
                Db::name('config')->insert(['key' => $key, 'value' => $value, 'create_time' => date('Y-m-d H:i:s')]);
            }
        }
        
        return JsonService::success('保存成功');
    }
}

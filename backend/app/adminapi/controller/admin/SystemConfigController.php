<?php
declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;
use app\common\service\JsonService;
use think\facade\Db;
use think\Response;

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
        
        // 如果有config嵌套对象，需要展开处理
        if (isset($params['config']) && is_array($params['config'])) {
            $group = $params['group'] ?? '';
            foreach ($params['config'] as $key => $value) {
                $configKey = $group . '_' . $key;
                $this->saveConfigItem($configKey, $value);
            }
            unset($params['config']);
        }
        
        // 处理顶层配置项
        foreach ($params as $key => $value) {
            if (in_array($key, ['token', 'sign', 'timestamp', 'group'])) continue;
            $this->saveConfigItem($key, $value);
        }
        
        return JsonService::success('保存成功');
    }
    
    // 保存单个配置项
    private function saveConfigItem(string $key, $value)
    {
        if (is_array($value)) {
            // 如果是数组，转换为JSON字符串存储
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        $exists = Db::name('config')->where('key', $key)->find();
        if ($exists) {
            Db::name('config')->where('key', $key)->update(['value' => $value, 'update_time' => date('Y-m-d H:i:s')]);
        } else {
            Db::name('config')->insert(['key' => $key, 'value' => $value, 'create_time' => date('Y-m-d H:i:s')]);
        }
    }

    // 测试存储连接
    public function testStorage(): Response
    {
        $params = $this->request->param();

        try {
            $result = \app\service\file\FileService::testConnection($params);

            if ($result['success']) {
                return JsonService::success('连接成功', $result['data']);
            } else {
                return json(['code' => 1, 'msg' => $result['message']]);
            }
        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}

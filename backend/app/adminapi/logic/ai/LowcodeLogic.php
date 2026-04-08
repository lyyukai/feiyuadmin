<?php
/**
 * 低代码平台逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\ai;

use app\adminapi\logic\FormLogic;
use app\common\service\JsonService;

/**
 * 低代码保存逻辑
 */
class LowcodeLogic
{
    /**
     * 保存低代码页面配置
     * @param string $pageId 页面ID（空则创建新页面）
     * @param array $config Amis JSON Schema 配置
     * @param int $adminId 管理员ID
     * @return array { id: int, saved: bool }
     */
    public static function save(string $pageId, array $config, int $adminId = 0): array
    {
        if (empty($config)) {
            JsonService::throwFail('配置不能为空');
        }

        // 生成唯一编码
        $code = 'page_' . date('YmdHis') . '_' . mt_rand(1000, 9999);
        $name = '低代码页面_' . date('Y-m-d H:i:s');

        $params = [
            'name' => $name,
            'code' => $code,
            'description' => '低代码页面',
            'config' => $config,
            'status' => 1,
        ];

        if (!empty($pageId) && is_numeric($pageId)) {
            // 更新现有页面
            try {
                FormLogic::edit($params + ['id' => (int) $pageId], $adminId);
                return [
                    'id' => (int) $pageId,
                    'saved' => true,
                    'created' => false,
                ];
            } catch (\Exception $e) {
                // 如果更新失败，尝试创建
                try {
                    $id = FormLogic::add($params, $adminId);
                    return [
                        'id' => $id,
                        'saved' => true,
                        'created' => true,
                    ];
                } catch (\Exception $e2) {
                    JsonService::throwFail('保存失败: ' . $e2->getMessage());
                }
            }
        } else {
            // 创建新页面
            FormLogic::add($params, $adminId);
            // 获取刚创建的页面ID
            $form = \app\model\FormDesign::where('code', $code)->find();
            $id = $form ? $form->id : 0;
            return [
                'id' => $id,
                'saved' => true,
                'created' => true,
            ];
        }
    }

    /**
     * 加载低代码页面配置
     * @param int $pageId 页面ID
     * @return array
     */
    public static function load(int $pageId): array
    {
        return FormLogic::getInfo($pageId);
    }

    /**
     * 获取低代码页面列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        // 只查询低代码类型的表单（编码以 page_ 开头）
        $list = FormLogic::getList($params);
        
        // 过滤只返回低代码页面
        $list['list'] = array_filter($list['list'], function ($item) {
            return isset($item['code']) && strpos($item['code'], 'page_') === 0;
        });
        
        return $list;
    }
}

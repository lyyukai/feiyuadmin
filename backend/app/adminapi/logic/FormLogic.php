<?php
/**
 * 飞鱼后台管理系统 - 表单设计逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic;

use app\common\service\JsonService;
use app\model\FormDesign;
use app\model\FormData;

/**
 * 表单设计逻辑
 * Class FormLogic
 * @package app\adminapi\logic
 */
class FormLogic
{
    /**
     * 获取表单列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;
        $keyword = $params['keyword'] ?? '';
        $status = isset($params['status']) ? (int) $params['status'] : null;

        $where = function ($query) use ($keyword, $status) {
            if (!empty($keyword)) {
                $query->whereLike('name|code|description', "%{$keyword}%");
            }
            if ($status !== null) {
                $query->where('status', $status);
            }
        };

        $query = FormDesign::where($where)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 获取表单详情
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $form = FormDesign::find($id);
        if (empty($form)) {
            JsonService::throwFail('表单不存在');
        }
        return $form->toArray();
    }

    /**
     * 添加表单
     * @param array $params
     * @param int $adminId
     */
    public static function add(array $params, int $adminId): void
    {
        self::validate($params);

        // 检查编码唯一性
        if (FormDesign::where('code', $params['code'])->find()) {
            JsonService::throwFail('表单编码已存在');
        }

        $form = new FormDesign();
        $form->name = $params['name'];
        $form->code = $params['code'];
        $form->description = $params['description'] ?? '';
        $form->config = $params['config'] ?? [];
        $form->status = (int) ($params['status'] ?? 1);
        $form->create_user = $adminId;
        $form->save();
    }

    /**
     * 编辑表单
     * @param array $params
     * @param int $adminId
     */
    public static function edit(array $params, int $adminId): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $form = FormDesign::find($id);
        if (empty($form)) {
            JsonService::throwFail('表单不存在');
        }

        // 检查编码唯一性
        if (FormDesign::where('code', $params['code'])->where('id', '<>', $id)->find()) {
            JsonService::throwFail('表单编码已存在');
        }

        $form->name = $params['name'];
        $form->code = $params['code'];
        $form->description = $params['description'] ?? '';
        $form->config = $params['config'] ?? [];
        $form->status = (int) ($params['status'] ?? 1);
        $form->update_user = $adminId;
        $form->save();
    }

    /**
     * 删除表单
     * @param int $id
     */
    public static function delete(int $id): void
    {
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $form = FormDesign::find($id);
        if (empty($form)) {
            JsonService::throwFail('表单不存在');
        }

        // 删除关联的表单数据
        FormData::where('form_id', $id)->delete();

        $form->delete();
    }

    /**
     * 切换状态
     * @param int $id
     */
    public static function toggleStatus(int $id): void
    {
        $form = FormDesign::find($id);
        if (empty($form)) {
            JsonService::throwFail('表单不存在');
        }

        $form->status = $form->status === 1 ? 0 : 1;
        $form->save();
    }

    /**
     * 验证参数
     * @param array $params
     */
    protected static function validate(array $params): void
    {
        if (empty($params['name'])) {
            JsonService::throwFail('表单名称不能为空');
        }
        if (empty($params['code'])) {
            JsonService::throwFail('表单编码不能为空');
        }
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $params['code'])) {
            JsonService::throwFail('表单编码格式错误，只能包含小写字母、数字和下划线，且以字母开头');
        }
        if (empty($params['config'])) {
            JsonService::throwFail('表单配置不能为空');
        }
    }

    /**
     * 获取表单数据列表
     * @param array $params
     * @return array
     */
    public static function getDataList(array $params): array
    {
        $formId = (int) ($params['form_id'] ?? 0);
        if (empty($formId)) {
            JsonService::throwFail('表单ID不能为空');
        }

        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;

        $query = FormData::where('form_id', $formId)->order('id', 'desc');

        $total = $query->count();
        $list = $query->limit($offset, $limit)->select()->toArray();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * 提交表单数据
     * @param array $params
     */
    public static function submitData(array $params): void
    {
        $formId = (int) ($params['form_id'] ?? 0);
        if (empty($formId)) {
            JsonService::throwFail('表单ID不能为空');
        }

        $form = FormDesign::find($formId);
        if (empty($form)) {
            JsonService::throwFail('表单不存在');
        }
        if ($form->status !== 1) {
            JsonService::throwFail('表单已禁用');
        }

        $formData = new FormData();
        $formData->form_id = $formId;
        $formData->data = $params['data'] ?? [];
        $formData->ip = request()->ip();
        $formData->user_id = (int) (request()->userId ?? 0);
        $formData->save();
    }

    /**
     * 删除表单数据
     * @param int $id
     */
    public static function deleteData(int $id): void
    {
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $formData = FormData::find($id);
        if (empty($formData)) {
            JsonService::throwFail('数据不存在');
        }

        $formData->delete();
    }
}

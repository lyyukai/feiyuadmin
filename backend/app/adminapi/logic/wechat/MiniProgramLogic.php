<?php
/**
 * 小程序逻辑层
 */

declare(strict_types=1);

namespace app\adminapi\logic\wechat;

use app\common\model\wechat\MiniProgram;
use app\common\model\wechat\MiniProgramVersion;
use app\common\model\wechat\MiniProgramMember;

/**
 * 小程序管理逻辑
 * Class MiniProgramLogic
 * @package app\adminapi\logic\wechat
 */
class MiniProgramLogic
{
    /**
     * 获取小程序列表
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $name = $params['name'] ?? '';
        $status = $params['status'] ?? '';

        $where = [];
        if ($name !== '') {
            $where[] = ['name', 'like', "%{$name}%"];
        }
        if ($status !== '') {
            $where[] = ['status', '=', (int) $status];
        }

        $list = MiniProgram::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加小程序
     */
    public static function add(array $data): bool
    {
        try {
            $model = new MiniProgram();
            $model->name = $data['name'] ?? '';
            $model->appid = $data['appid'] ?? '';
            $model->appsecret = $data['appsecret'] ?? '';
            $model->logo = $data['logo'] ?? '';
            $model->description = $data['description'] ?? '';
            $model->status = (int) ($data['status'] ?? 1);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 编辑小程序
     */
    public static function edit(int $id, array $data): bool
    {
        try {
            $model = MiniProgram::find($id);
            if (!$model) {
                return false;
            }

            $model->name = $data['name'] ?? '';
            $model->appid = $data['appid'] ?? '';
            if (!empty($data['appsecret'])) {
                $model->appsecret = $data['appsecret'];
            }
            $model->logo = $data['logo'] ?? '';
            $model->description = $data['description'] ?? '';
            $model->status = (int) ($data['status'] ?? 1);
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除小程序
     */
    public static function delete(int $id): bool
    {
        try {
            $model = MiniProgram::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取小程序详情
     */
    public static function getDetail(int $id): ?MiniProgram
    {
        return MiniProgram::find($id);
    }

    /**
     * 获取版本列表
     */
    public static function getVersionList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $mini_program_id = (int) ($params['mini_program_id'] ?? 0);

        $where = [];
        if ($mini_program_id > 0) {
            $where[] = ['mini_program_id', '=', $mini_program_id];
        }

        $list = MiniProgramVersion::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加版本
     */
    public static function addVersion(array $data): bool
    {
        try {
            $model = new MiniProgramVersion();
            $model->mini_program_id = (int) ($data['mini_program_id'] ?? 0);
            $model->version = $data['version'] ?? '';
            $model->version_desc = $data['version_desc'] ?? '';
            $model->template_id = $data['template_id'] ?? '';
            $model->audit_status = 0;
            $model->status = 0;
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除版本
     */
    public static function deleteVersion(int $id): bool
    {
        try {
            $model = MiniProgramVersion::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取成员列表
     */
    public static function getMemberList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 10);
        $mini_program_id = (int) ($params['mini_program_id'] ?? 0);

        $where = [];
        if ($mini_program_id > 0) {
            $where[] = ['mini_program_id', '=', $mini_program_id];
        }

        $list = MiniProgramMember::where($where)
            ->order('id', 'desc')
            ->paginate($limit)
            ->toArray();

        return $list;
    }

    /**
     * 添加成员
     */
    public static function addMember(array $data): bool
    {
        try {
            $model = new MiniProgramMember();
            $model->mini_program_id = (int) ($data['mini_program_id'] ?? 0);
            $model->user_id = (int) ($data['user_id'] ?? 0);
            $model->username = $data['username'] ?? '';
            $model->role = $data['role'] ?? 'developer';
            $model->create_time = date('Y-m-d H:i:s');

            return $model->save() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除成员
     */
    public static function deleteMember(int $id): bool
    {
        try {
            $model = MiniProgramMember::find($id);
            if (!$model) {
                return false;
            }
            return $model->delete() !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

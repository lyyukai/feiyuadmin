<?php
/**
 * 飞鱼后台管理系统 - 用户管理逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic\admin;

use app\common\service\JsonService;
use app\model\User;

/**
 * 用户管理逻辑
 * Class UserLogic
 * @package app\adminapi\logic\admin
 */
class UserLogic
{
    /**
     * 获取用户列表
     * @param array $params
     * @return array
     */
    public static function getList(array $params): array
    {
        $page = (int) ($params['page'] ?? 1);
        $limit = min((int) ($params['limit'] ?? 15), 100);
        $offset = ($page - 1) * $limit;
        $keyword = $params['keyword'] ?? '';

        $where = function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->whereLike('username|nickname', "%{$keyword}%");
            }
        };

        $query = User::where($where)->order('id', 'desc');

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
     * 获取用户信息
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id): array
    {
        $user = User::with(['dept', 'post'])->find($id);
        if (empty($user)) {
            JsonService::throwFail('用户不存在');
        }
        return $user->toArray();
    }

    /**
     * 添加用户
     * @param array $params
     */
    public static function add(array $params): void
    {
        // 验证
        self::validate($params);

        // 检查用户名
        if (User::where('username', $params['username'])->find()) {
            JsonService::throwFail('用户名已存在');
        }

        // 创建用户
        $user = new User();
        $user->username = $params['username'];
        $user->password = $params['password'];
        $user->nickname = $params['nickname'] ?? $params['username'];
        $user->email = $params['email'] ?? '';
        $user->mobile = $params['mobile'] ?? '';
        $user->dept_id = (int) ($params['dept_id'] ?? 0);
        $user->post_id = (int) ($params['post_id'] ?? 0);
        $user->status = (int) ($params['status'] ?? 1);
        $user->save();
    }

    /**
     * 编辑用户
     * @param array $params
     */
    public static function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if (empty($id)) {
            JsonService::throwFail('参数错误');
        }

        $user = User::find($id);
        if (empty($user)) {
            JsonService::throwFail('用户不存在');
        }

        // 更新字段
        if (!empty($params['nickname'])) {
            $user->nickname = $params['nickname'];
        }
        if (!empty($params['email'])) {
            $user->email = $params['email'];
        }
        if (!empty($params['mobile'])) {
            $user->mobile = $params['mobile'];
        }
        if (isset($params['dept_id'])) {
            $user->dept_id = (int) $params['dept_id'];
        }
        if (isset($params['post_id'])) {
            $user->post_id = (int) $params['post_id'];
        }
        if (isset($params['status'])) {
            $user->status = (int) $params['status'];
        }
        if (!empty($params['password'])) {
            $user->password = $params['password'];
        }

        $user->save();
    }

    /**
     * 删除用户
     * @param int $id
     * @param int $adminId
     */
    public static function delete(int $id, int $adminId): void
    {
        if ($id === $adminId) {
            JsonService::throwFail('不能删除当前用户');
        }

        if ($id === 1) {
            JsonService::throwFail('超级管理员不能删除');
        }

        $user = User::find($id);
        if (empty($user)) {
            JsonService::throwFail('用户不存在');
        }

        $user->delete();
    }

    /**
     * 验证参数
     * @param array $params
     */
    protected static function validate(array $params): void
    {
        if (empty($params['username'])) {
            JsonService::throwFail('用户名不能为空');
        }
        if (empty($params['password'])) {
            JsonService::throwFail('密码不能为空');
        }
        if (strlen($params['username']) < 4 || strlen($params['username']) > 20) {
            JsonService::throwFail('用户名长度需在4-20位之间');
        }
        if (strlen($params['password']) < 6 || strlen($params['password']) > 20) {
            JsonService::throwFail('密码长度需在6-20位之间');
        }
    }
}

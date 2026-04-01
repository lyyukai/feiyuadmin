<?php
/**
 * 飞羽后台管理系统 - 登录逻辑
 */

declare(strict_types=1);

namespace app\adminapi\logic;

use app\common\service\JsonService;
use app\model\User;
use app\service\TokenService;

/**
 * 登录逻辑
 * Class LoginLogic
 * @package app\adminapi\logic
 */
class LoginLogic
{
    /**
     * 登录
     * @param array $params
     * @return array
     */
    public static function login(array $params): array
    {
        // 查询用户
        $user = User::where('username', $params['username'])->find();
        if (empty($user)) {
            JsonService::throwFail('用户名或密码错误');
        }

        // 验证密码
        if (!password_verify($params['password'], $user->password)) {
            JsonService::throwFail('用户名或密码错误');
        }

        // 检查状态
        if ($user->status !== 1) {
            JsonService::throwFail('账号已被禁用');
        }

        // 生成 Token
        $token = TokenService::generate((int) $user->id);

        // 更新登录信息
        $user->login_ip = request()->ip();
        $user->login_time = date('Y-m-d H:i:s');
        $user->save();

        return [
            'token' => $token,
            'expire' => 604800, // 7天
            'user_info' => [
                'id' => $user->id,
                'username' => $user->username,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar,
            ],
        ];
    }

    /**
     * 退出登录
     * @param array $adminInfo
     */
    public static function logout(array $adminInfo): void
    {
        $token = self::getToken();
        if ($token) {
            TokenService::delete($token);
        }
    }

    /**
     * 获取 Token
     * @return string|null
     */
    protected static function getToken(): ?string
    {
        $auth = request()->header('Authorization', '');
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
        return null;
    }
}

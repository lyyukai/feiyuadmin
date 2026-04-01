<?php
declare(strict_types=1);
namespace app\controller\admin;

use app\model\User as UserModel;
use app\model\LoginLog;
use app\service\TokenService;
use think\facade\Cache;
use think\facade\Request;
use think\Response;

class Login
{
    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function error(string $msg = '操作失败', int $code = 400): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => []]);
    }

    public function login(): Response
    {
        $request = Request::instance();
        
        $ip = $request->ip();
        $cacheKey = 'login_fail_' . $ip;
        $failCount = (int) Cache::get($cacheKey, 0);
        if ($failCount >= 5) {
            return $this->error('登录失败次数过多，请30分钟后再试');
        }
        
        $username = $request->param('username', '');
        $password = $request->param('password', '');
        
        if (empty($username) || empty($password)) {
            return $this->error('用户名和密码不能为空');
        }
        
        $user = UserModel::where('username', $username)->find();
        if (!$user) {
            Cache::inc($cacheKey);
            Cache::expire($cacheKey, 1800);
            return $this->error('用户名或密码错误');
        }
        
        if (!password_verify($password, $user->password)) {
            Cache::inc($cacheKey);
            Cache::expire($cacheKey, 1800);
            return $this->error('用户名或密码错误');
        }
        
        Cache::delete($cacheKey);
        
        $token = TokenService::generate($user->id);
        $user->login_ip = $ip;
        $user->login_time = date('Y-m-d H:i:s');
        $user->save();
        
        LoginLog::create([
            'username' => $username,
            'ip' => $ip,
            'user_agent' => $request->header('user-agent', ''),
            'status' => 1,
            'login_time' => date('Y-m-d H:i:s'),
        ]);
        
        return $this->success([
            'token' => $token,
            'userId' => $user->id,
            'expire' => 604800,
            'userInfo' => $user->getInfo(),
        ], '登录成功');
    }
}

<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\User as UserModel;
use app\model\Dept;
use app\model\Post;
use app\model\Role;
use app\model\Log;
use think\Request;
use think\facade\Db;
use think\Response;

/**
 * 管理员控制器 - 用户CRUD + 角色分配
 */
class User
{
    protected Request $request;
    protected int $userId = 0;

    public function __construct()
    {
        $this->request = request();
        $this->userId = (int) ($this->request->userId ?? 0);
    }

    protected function success(mixed $data = [], string $msg = '操作成功', int $code = 0): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function error(string $msg = '操作失败', int $code = 400): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => []]);
    }

    protected function page(int $total, array $list): Response
    {
        return json(['code' => 0, 'msg' => 'success', 'total' => $total, 'data' => $list]);
    }

    protected function param(string $name = '', mixed $default = null): mixed
    {
        return $this->request->param($name, $default);
    }

    protected function logSensitive(string $type, string $content): void
    {
        Log::create([
            'username'    => $this->request->userInfo['username'] ?? 'unknown',
            'ip'          => $this->request->ip(),
            'type'        => $type,
            'content'     => $content,
            'method'      => $this->request->method(),
            'url'         => $this->request->url(),
            'user_agent'  => $this->request->header('user-agent', ''),
        ]);
    }

    public function info(): Response
    {
        $user = UserModel::with(['dept', 'post'])->find($this->userId);
        if (!$user) {
            return $this->error('用户不存在');
        }
        return $this->success($user);
    }

    public function list(): Response
    {
        [$page, $limit] = $this->getPageParam();
        $keyword = $this->param('keyword', '');
        $deptId  = (int) $this->param('dept_id', 0);
        $status  = $this->param('status', '');

        $query = UserModel::with(['dept', 'post'])->whereNull('delete_time');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->whereLike('username', "%{$keyword}%")
                  ->whereOr('nickname', 'like', "%{$keyword}%")
                  ->whereOr('mobile', 'like', "%{$keyword}%");
            });
        }

        if ($deptId > 0) {
            $deptIds = Dept::getChildIds($deptId);
            $query->whereIn('dept_id', $deptIds);
        }

        if ($status !== '') {
            $query->where('status', (int) $status);
        }

        $total = $query->count();
        $list  = $query->page($page, $limit)
            ->order('id', 'asc')
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $roles = Db::name('user_role')
                ->alias('ur')
                ->join('role r', 'r.id=ur.role_id', 'left')
                ->where('ur.user_id', $item['id'])
                ->whereNull('r.delete_time')
                ->column('r.name');
            $item['roles'] = $roles;
        }

        return $this->page($total, $list);
    }

    public function save(): Response
    {
        $username = $this->param('username', '');
        $password = $this->param('password', '');
        $nickname = $this->param('nickname', '');
        $roleIds  = $this->param('role_ids', []);

        if (empty($username) || empty($password) || empty($nickname)) {
            return $this->error('用户名、密码、昵称不能为空');
        }

        if (strlen($username) < 4 || strlen($username) > 20) {
            return $this->error('用户名长度需在4-20位之间');
        }

        if (strlen($password) < 6 || strlen($password) > 20) {
            return $this->error('密码长度需在6-20位之间');
        }

        if (UserModel::where('username', $username)->find()) {
            return $this->error('用户名已存在');
        }

        $user = new UserModel();
        $user->username = $username;
        $user->password = $password;
        $user->nickname = $nickname;
        $user->realname = $this->param('realname', '');
        $user->email    = $this->param('email', '');
        $user->mobile   = $this->param('mobile', '');
        $user->avatar   = $this->param('avatar', '');
        $user->dept_id  = (int) $this->param('dept_id', 0);
        $user->post_id  = (int) $this->param('post_id', 0);
        $user->status   = (int) $this->param('status', 1);
        $user->remark   = $this->param('remark', '');
        $user->save();

        if (!empty($roleIds)) {
            $user->roles()->attach($roleIds);
        }

        $this->logSensitive('create_user', '新增用户ID: ' . $user->id . '，用户名: ' . $username);

        return $this->success(['id' => $user->id], '新增成功');
    }

    public function update(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $user = UserModel::find($id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $nickname = $this->param('nickname', '');
        $roleIds  = $this->param('role_ids', []);

        if ($nickname !== '') {
            $user->nickname = $nickname;
        }

        $email   = $this->param('email', '');
        $mobile  = $this->param('mobile', '');
        $avatar  = $this->param('avatar', '');
        $deptId  = $this->param('dept_id', '');
        $postId  = $this->param('post_id', '');
        $status  = $this->param('status', '');
        $remark  = $this->param('remark', '');

        if ($email  !== '') $user->email   = $email;
        if ($mobile !== '') $user->mobile  = $mobile;
        if ($avatar !== '') $user->avatar  = $avatar;
        if ($deptId !== '') $user->dept_id = (int) $deptId;
        if ($postId !== '') $user->post_id = (int) $postId;
        if ($status !== '') $user->status  = (int) $status;
        if ($remark !== '') $user->remark  = $remark;

        $user->save();

        if (!empty($roleIds)) {
            $user->roles()->detach();
            $user->roles()->attach($roleIds);
            $this->logSensitive('update_user_role', '更新用户ID: ' . $id . ' 的角色，角色IDs: ' . implode(',', $roleIds));
        }

        return $this->success([], '更新成功');
    }

    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        if ($id === $this->userId) {
            return $this->error('不能删除当前登录用户', 400);
        }

        if ($id === 1) {
            return $this->error('超级管理员不可删除', 400);
        }

        $user = UserModel::find($id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $user->roles()->detach();
        $user->delete();

        $this->logSensitive('delete_user', '删除用户ID: ' . $id . '，用户名: ' . $user->username);

        return $this->success([], '删除成功');
    }

    public function roles(): Response
    {
        $id = (int) $this->param('id', 0);
        if (!$id) {
            return $this->error('参数错误');
        }

        $user = UserModel::find($id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $roleIds = $user->getRoleIds();

        return $this->success($roleIds);
    }

    public function saveRoles(): Response
    {
        $id      = (int) $this->param('id', 0);
        $roleIds = $this->param('role_ids', []);

        if (!$id) {
            return $this->error('参数错误');
        }

        $user = UserModel::find($id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $user->roles()->detach();
        if (!empty($roleIds)) {
            $user->roles()->attach($roleIds);
        }

        $this->logSensitive('assign_role', '为用户ID: ' . $id . ' 分配角色IDs: ' . implode(',', $roleIds));

        return $this->success([], '角色分配成功');
    }

    public function resetPassword(): Response
    {
        $id       = (int) $this->param('id', 0);
        $password = $this->param('password', '123456');

        if (!$id) {
            return $this->error('参数错误');
        }

        if (strlen($password) < 6 || strlen($password) > 20) {
            return $this->error('密码长度需在6-20位之间');
        }

        $user = UserModel::find($id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        $user->password = $password;
        $user->save();

        $this->logSensitive('reset_password', '重置用户ID: ' . $id . ' 的密码，用户名: ' . $user->username);

        return $this->success(['password' => $password], '密码重置成功');
    }

    protected function getPageParam(): array
    {
        $page  = (int) $this->param('page', 1);
        $limit = (int) $this->param('limit', 20);
        $limit = $limit > 100 ? 100 : $limit;
        return [$page, $limit];
    }
}

<?php
/**
 * 飞羽后台管理系统 - 用户管理控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\admin;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\admin\UserLogic;
use think\Response;

/**
 * 用户管理控制器
 * Class UserController
 * @package app\adminapi\controller\admin
 */
class UserController extends BaseAdminController
{
    /**
     * 用户列表
     * @return Response
     */
    public function lists(): Response
    {
        $result = UserLogic::getList($this->param());
        // 返回格式兼容前端：{ data: [...], total: N }
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
        ]);
    }

    /**
     * 用户信息
     * @return Response
     */
    public function info(): Response
    {
        $result = UserLogic::getInfo($this->adminId);
        return $this->data($result);
    }

    /**
     * 添加用户
     * @return Response
     */
    public function add(): Response
    {
        UserLogic::add($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑用户
     * @return Response
     */
    public function edit(): Response
    {
        UserLogic::edit($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除用户
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) $this->param('id', 0);
        UserLogic::delete($id, $this->adminId);
        return $this->success('删除成功');
    }

    /**
     * 修改密码
     * @return Response
     */
    public function password(): Response
    {
        $oldPassword = $this->param('old_password', '');
        $newPassword = $this->param('new_password', '');

        if (empty($oldPassword)) {
            return $this->fail('旧密码不能为空');
        }
        if (empty($newPassword)) {
            return $this->fail('新密码不能为空');
        }
        if (strlen($newPassword) < 6 || strlen($newPassword) > 20) {
            return $this->fail('新密码长度需在6-20位之间');
        }

        $user = \app\model\User::find($this->adminId);
        if (!$user) {
            return $this->fail('用户不存在');
        }
        if (!password_verify($oldPassword, $user->password)) {
            return $this->fail('旧密码错误');
        }

        $user->password = $newPassword;
        $user->save();

        return $this->success('密码修改成功');
    }

    /**
     * 获取个人中心信息
     * @return Response
     */
    public function personal(): Response
    {
        $user = \app\model\User::with(['dept', 'post'])->find($this->adminId);
        if (!$user) {
            return $this->fail('用户不存在');
        }
        $data = $user->toArray();
        $data['phone'] = $data['mobile'] ?? '';
        $data['dept_name'] = $user->dept ? $user->dept->name : '';
        $data['post_name'] = $user->post ? $user->post->name : '';
        unset($data['mobile'], $data['password'], $data['delete_time']);
        return $this->data($data);
    }

    /**
     * 保存个人中心信息
     * @return Response
     */
    public function editPersonal(): Response
    {
        $params = $this->param();
        $user = \app\model\User::find($this->adminId);
        if (!$user) {
            return $this->fail('用户不存在');
        }
        $allowFields = ['nickname', 'mobile', 'avatar', 'remark'];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $user->$field = $params[$field];
            }
        }
        $user->save();
        return $this->success('保存成功');
    }

    /**
     * 重置密码（管理员操作）
     * @return Response
     */
    public function resetPassword(): Response
    {
        $id = (int) $this->param('id', 0);
        if ($id <= 0) {
            return $this->fail('用户ID不能为空');
        }

        // 不能重置超级管理员
        if ($id === 1) {
            return $this->fail('超级管理员不能重置密码');
        }

        $user = \app\model\User::find($id);
        if (!$user) {
            return $this->fail('用户不存在');
        }

        // 重置为默认密码 "123456"，setPasswordAttr 会自动加密
        $user->password = '123456';
        $user->save();

        // 记录操作日志
        self::saveLog('重置用户密码', $id, 'user', $id);

        return $this->success('密码重置成功，默认密码为 123456');
    }

    /**
     * 批量操作
     * @return Response
     */
    public function batch(): Response
    {
        $ids = $this->param('ids', []);
        $action = $this->param('action', '');

        if (empty($ids) || !is_array($ids)) {
            return $this->fail('请选择要操作的用户');
        }
        if (empty($action)) {
            return $this->fail('操作类型不能为空');
        }

        // 过滤掉超级管理员ID
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($id) => $id !== 1);

        if (empty($ids)) {
            return $this->fail('没有可操作的用户');
        }

        $count = 0;
        switch ($action) {
            case 'enable':
                $count = \app\model\User::whereIn('id', $ids)->update(['status' => 1]);
                self::saveLog('批量启用用户', 0, 'user', implode(',', $ids));
                break;
            case 'disable':
                $count = \app\model\User::whereIn('id', $ids)->update(['status' => 0]);
                self::saveLog('批量禁用用户', 0, 'user', implode(',', $ids));
                break;
            case 'delete':
                $count = \app\model\User::whereIn('id', $ids)->delete();
                self::saveLog('批量删除用户', 0, 'user', implode(',', $ids));
                break;
            default:
                return $this->fail('不支持的操作类型');
        }

        return $this->success('操作成功', ['count' => $count]);
    }

    /**
     * 记录操作日志
     * @param string $content
     * @param int $userId
     * @param string $type
     * @param string|int $targetId
     */
    protected static function saveLog(string $content, int $userId, string $type, string|int $targetId): void
    {
        try {
            \think\facade\Db::name('log')->insert([
                'user_id' => $userId ?: 0,
                'username' => $userId ? \app\model\User::where('id', $userId)->value('username') : 'system',
                'url' => request()->url(),
                'method' => request()->method(),
                'content' => $content,
                'ip' => request()->ip(),
                'type' => $type,
                'target_id' => $targetId,
                'create_time' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // 日志写入失败不影响主流程
        }
    }
}

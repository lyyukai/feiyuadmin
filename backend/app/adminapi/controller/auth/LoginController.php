<?php
/**
 * 飞羽后台管理系统 - 登录控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\auth;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\validate\LoginValidate;
use app\adminapi\logic\LoginLogic;
use app\common\service\JsonService;

/**
 * 登录控制器
 * Class LoginController
 * @package app\adminapi\controller\auth
 */
class LoginController extends BaseAdminController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['account', 'logout'];

    /**
     * 账号登录
     * @return \think\response\Json
     */
    public function account()
    {
        $params = (new LoginValidate())->post()->goCheck();
        $data = LoginLogic::login($params);
        return $this->data($data);
    }

    /**
     * 退出登录
     * @return \think\response\Json
     */
    public function logout()
    {
        return $this->success('退出成功');
    }
}

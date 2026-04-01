<?php
/**
 * 飞羽后台管理系统 - 基础验证器
 */

declare(strict_types=1);

namespace app\common\validate;

use think\Validate;
use app\common\service\JsonService;

/**
 * 基础验证器
 * Class BaseValidate
 * @package app\common\validate
 */
class BaseValidate extends Validate
{
    /** @var string 请求方式 */
    protected string $method = 'GET';

    /** @var object 请求对象 */
    protected $request;

    public function __construct()
    {
        parent::__construct();
        $this->request = request();
    }

    /**
     * 设置为 POST 请求
     * @return $this
     */
    public function post(): self
    {
        $this->method = 'POST';
        return $this;
    }

    /**
     * 设置为 GET 请求
     * @return $this
     */
    public function get(): self
    {
        $this->method = 'GET';
        return $this;
    }

    /**
     * 验证并返回参数
     * @param string|null $scene
     * @param array $validateData
     * @return array
     */
    public function goCheck(?string $scene = null, array $validateData = []): array
    {
        if (!$this->request) {
            $this->request = request();
        }
        // 获取参数
        $params = $this->method === 'POST' ? $this->request->post() : $this->request->param();
        $params = array_merge($params, $validateData);

        // 场景验证
        if ($scene && isset($this->scene[$scene])) {
            $rules = array_merge($this->rule, $this->scene[$scene]);
            $validate = new self();
            $validate->rule = $rules;
            $result = $validate->check($params);
        } else {
            $result = $this->check($params);
        }

        if (!$result) {
            JsonService::throwFail($this->getError());
        }

        return $params;
    }
}

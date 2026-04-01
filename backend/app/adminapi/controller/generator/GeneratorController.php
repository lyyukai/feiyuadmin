<?php
/**
 * 代码生成器控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\generator;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\generator\GeneratorLogic;
use think\Response;

/**
 * 代码生成器控制器
 * Class GeneratorController
 * @package app\adminapi\controller\generator
 */
class GeneratorController extends BaseAdminController
{
    /**
     * 数据库配置列表
     */
    public function configLists(): Response
    {
        $result = GeneratorLogic::getConfigList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'],
            'total' => $result['total'],
        ]);
    }

    /**
     * 数据库配置信息
     */
    public function configInfo(): Response
    {
        $id = (int) $this->param('id', 0);
        $result = GeneratorLogic::getConfigInfo($id);
        return $this->data($result);
    }

    /**
     * 添加数据库配置
     */
    public function configAdd(): Response
    {
        GeneratorLogic::addConfig($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑数据库配置
     */
    public function configEdit(): Response
    {
        GeneratorLogic::editConfig($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除数据库配置
     */
    public function configDelete(): Response
    {
        $id = (int) $this->param('id', 0);
        GeneratorLogic::deleteConfig($id);
        return $this->success('删除成功');
    }

    /**
     * 测试数据库连接
     */
    public function testConnection(): Response
    {
        $result = GeneratorLogic::testConnection($this->param());
        return $this->success('连接成功', $result);
    }

    /**
     * 模板列表
     */
    public function templateLists(): Response
    {
        $result = GeneratorLogic::getTemplateList($this->param());
        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'],
            'total' => $result['total'],
        ]);
    }

    /**
     * 模板信息
     */
    public function templateInfo(): Response
    {
        $id = (int) $this->param('id', 0);
        $result = GeneratorLogic::getTemplateInfo($id);
        return $this->data($result);
    }

    /**
     * 添加模板
     */
    public function templateAdd(): Response
    {
        GeneratorLogic::addTemplate($this->param());
        return $this->success('添加成功');
    }

    /**
     * 编辑模板
     */
    public function templateEdit(): Response
    {
        GeneratorLogic::editTemplate($this->param());
        return $this->success('编辑成功');
    }

    /**
     * 删除模板
     */
    public function templateDelete(): Response
    {
        $id = (int) $this->param('id', 0);
        GeneratorLogic::deleteTemplate($id);
        return $this->success('删除成功');
    }

    /**
     * 数据表列表
     */
    public function tableLists(): Response
    {
        $configId = (int) $this->param('config_id', 0);
        $result = GeneratorLogic::getTableList($configId);
        return $this->data($result);
    }

    /**
     * 表结构
     */
    public function tableColumns(): Response
    {
        $configId = (int) $this->param('config_id', 0);
        $tableName = $this->param('table_name', '');
        $result = GeneratorLogic::getTableColumns($configId, $tableName);
        return $this->data($result);
    }

    /**
     * 预览代码
     */
    public function preview(): Response
    {
        $result = GeneratorLogic::preview($this->param());
        return $this->data($result);
    }

    /**
     * 生成代码
     */
    public function generate(): Response
    {
        $result = GeneratorLogic::generate($this->param());
        return $this->success('代码生成成功', $result);
    }

    /**
     * 获取生成类型
     */
    public function genTypes(): Response
    {
        $result = GeneratorLogic::getGenTypes();
        return $this->data($result);
    }
}

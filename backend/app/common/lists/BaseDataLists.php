<?php
/**
 * 飞鱼后台管理系统 - 数据列表基类
 */

declare(strict_types=1);

namespace app\common\lists;

use think\Request;
use think\facade\Db;

/**
 * 数据列表基类
 * Class BaseDataLists
 * @package app\common\lists
 */
abstract class BaseDataLists
{
    /** @var Request */
    protected Request $request;

    /** @var array 请求参数 */
    protected array $params = [];

    /** @var int 页码 */
    protected int $pageNo = 1;

    /** @var int 每页数量 */
    protected int $pageSize = 15;

    /** @var int LIMIT offset */
    protected int $offset = 0;

    /** @var string 排序字段 */
    protected string $orderField = 'id';

    /** @var string 排序方向 */
    protected string $orderType = 'desc';

    /** @var string|array 搜索条件 */
    protected $where = [];

    /** @var string|array 查询字段 */
    protected string $field = '*';

    /** @var string 表名 */
    protected string $tableName = '';

    public function __construct()
    {
        $this->request = request();
        $this->params = $this->request->param();

        // 分页参数
        $this->pageNo = (int) ($this->params['page_no'] ?? 1);
        $this->pageSize = min((int) ($this->params['page_size'] ?? 15), 100);
        $this->offset = ($this->pageNo - 1) * $this->pageSize;

        // 排序
        if (!empty($this->params['order_field'])) {
            $this->orderField = $this->params['order_field'];
        }
        if (!empty($this->params['order_type'])) {
            $this->orderType = $this->params['order_type'];
        }

        // 初始化
        $this->initWhere();
    }

    /**
     * 初始化搜索条件
     */
    protected function initWhere(): void
    {
        // 子类可重写
    }

    /**
     * 获取列表数据
     * @return array
     */
    abstract public function getList(): array;

    /**
     * 获取总数
     * @return int
     */
    public function getTotal(): int
    {
        if (empty($this->tableName)) {
            return 0;
        }
        return Db::name($this->tableName)->where($this->where)->count();
    }

    /**
     * 构建查询
     * @return \think\db\Query
     */
    protected function buildQuery()
    {
        $query = Db::name($this->tableName)
            ->where($this->where)
            ->field($this->field)
            ->order($this->orderField, $this->orderType)
            ->limit($this->offset, $this->pageSize);
        return $query;
    }

    /**
     * 获取分页数据
     * @return array
     */
    protected function getPageData(): array
    {
        $total = $this->getTotal();
        $list = $this->buildQuery()->select()->toArray();
        return [
            'list' => $list,
            'total' => $total,
            'page_no' => $this->pageNo,
            'page_size' => $this->pageSize,
        ];
    }
}

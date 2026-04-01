<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\Log as LogModel;
use think\Request;
use think\Response;

/**
 * 操作日志控制器
 */
class Log extends Base
{
    /**
     * 操作日志列表
     */
    public function list(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new LogModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        return $this->page($total, $list);
    }

    /**
     * 操作日志详情
     */
    public function detail(int $id): Response
    {
        $log = LogModel::find($id);
        if (!$log) {
            return $this->error('日志不存在', 404);
        }

        return $this->success($log->toArray());
    }

    /**
     * 清空操作日志
     */
    public function clean(Request $request): Response
    {
        $days = (int) $request->param('days', 30);
        $days = $days > 0 ? $days : 30;

        $beforeDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $count = LogModel::where('create_time', '<', $beforeDate)->delete();

        return $this->success(['count' => $count], '清空成功');
    }

    /**
     * 导出操作日志（CSV格式）
     */
    public function export(Request $request): Response
    {
        $params = $request->param();
        $query = (new LogModel())->search($params);
        $list = $query->limit(10000)->select()->toArray();

        if (empty($list)) {
            return $this->error('没有数据可导出');
        }

        $csvData = "ID,用户名,请求方法,URL,IP地址,操作地点,执行时长(ms),操作时间\n";
        foreach ($list as $row) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%d,%s\n",
                $row['id'],
                $row['username'] ?? '',
                $row['method'],
                $row['url'],
                $row['ip'] ?? '',
                $row['location'] ?? '',
                $row['duration'] ?? 0,
                $row['create_time']
            );
        }

        $filename = 'operation_log_' . date('YmdHis') . '.csv';
        return download($csvData, $filename, true)
            ->header('Content-Type', 'text/csv');
    }
}

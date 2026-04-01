<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\LoginLog as LoginLogModel;
use think\Request;
use think\Response;

/**
 * 登录日志控制器
 */
class LoginLog extends Base
{
    /**
     * 登录日志列表
     */
    public function list(Request $request): Response
    {
        [$page, $limit] = $this->pageParam();

        $params = $request->param();
        $query = (new LoginLogModel())->search($params);

        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();

        return $this->page($total, $list);
    }

    /**
     * 登录日志详情
     */
    public function detail(int $id): Response
    {
        $log = LoginLogModel::find($id);
        if (!$log) {
            return $this->error('日志不存在', 404);
        }

        return $this->success($log->toArray());
    }

    /**
     * 导出登录日志（CSV格式）
     */
    public function export(Request $request): Response
    {
        $params = $request->param();
        $query = (new LoginLogModel())->search($params);
        $list = $query->limit(10000)->select()->toArray();

        if (empty($list)) {
            return $this->error('没有数据可导出');
        }

        $csvData = "ID,用户名,状态,IP地址,登录地点,提示信息,登录时间\n";
        foreach ($list as $row) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $row['id'],
                $row['username'],
                $row['status'],
                $row['ip'] ?? '',
                $row['location'] ?? '',
                $row['msg'] ?? '',
                $row['login_time']
            );
        }

        $filename = 'login_log_' . date('YmdHis') . '.csv';
        return download($csvData, $filename, true)
            ->header('Content-Type', 'text/csv');
    }

    /**
     * 清空登录日志
     */
    public function clean(Request $request): Response
    {
        $days = (int) $request->param('days', 90);
        $days = $days > 0 ? $days : 90;

        $beforeDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $count = LoginLogModel::where('login_time', '<', $beforeDate)->delete();

        return $this->success(['count' => $count], '清空成功');
    }
}

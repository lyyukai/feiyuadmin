<?php
declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\logic\admin\NoticeRecordLogic;
use think\Response;

/**
 * 发送记录控制器
 */
class NoticeRecordController extends BaseAdminController
{
    /**
     * 发送记录列表
     */
    public function lists(): Response
    {
        $params = $this->param();
        $result = NoticeRecordLogic::getList($params);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $result['list']
        ])->header(['X-Total' => $result['total']]);
    }

    /**
     * 发送记录详情
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('记录ID不能为空');
        }
        $info = NoticeRecordLogic::getInfo($id);
        if (empty($info)) {
            return $this->fail('记录不存在');
        }
        return $this->data($info);
    }

    /**
     * 发送统计
     */
    public function statistics(): Response
    {
        $params = $this->param();
        $stats = NoticeRecordLogic::getStatistics($params);
        return $this->data($stats);
    }
}

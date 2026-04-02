<?php
/**
 * 飞鱼后台管理系统 - 消息通知控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\admin\NoticeLogic;
use app\service\SendMessageService;
use think\Response;

/**
 * 消息通知控制器
 * Class NoticeController
 * @package app\adminapi\controller
 */
class NoticeController extends BaseAdminController
{
    /**
     * 获取当前登录用户ID
     */
    protected function getAdminId(): int
    {
        return (int) ($this->adminId ?? 0);
    }

    /**
     * 消息列表
     * @return Response
     */
    public function lists(): Response
    {
        $params = $this->param();
        $params['receiver_id'] = $this->getAdminId();

        $result = NoticeLogic::getList($params);

        return json([
            'code' => 0,
            'msg' => '',
            'data' => $result['list'] ?? [],
            'total' => $result['total'] ?? 0,
            'page' => $result['page'] ?? 1,
            'page_size' => $result['page_size'] ?? 20,
        ]);
    }

    /**
     * 消息详情
     * @return Response
     */
    public function detail(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('消息ID不能为空');
        }

        $info = NoticeLogic::getInfo($id);
        if (empty($info)) {
            return $this->fail('消息不存在');
        }

        return $this->data($info);
    }

    /**
     * 发送消息
     * @return Response
     */
    public function send(): Response
    {
        $params = $this->param();

        if (empty($params['title'])) {
            return $this->fail('消息标题不能为空');
        }
        if (empty($params['content'])) {
            return $this->fail('消息内容不能为空');
        }

        $params['sender_id'] = $this->getAdminId();
        $params['sender_name'] = $params['sender_name'] ?? '管理员';

        $id = NoticeLogic::send($params);
        if ($id <= 0) {
            return $this->fail('发送失败');
        }

        return $this->success('发送成功', ['id' => $id]);
    }

    /**
     * 编辑消息
     * @return Response
     */
    public function edit(): Response
    {
        $params = $this->param();

        if (empty($params['id'])) {
            return $this->fail('消息ID不能为空');
        }

        $result = NoticeLogic::edit($params);
        if (!$result) {
            return $this->fail('编辑失败');
        }

        return $this->success('编辑成功');
    }

    /**
     * 删除消息
     * @return Response
     */
    public function delete(): Response
    {
        $id = (int) ($this->param('id', 0));
        if ($id <= 0) {
            return $this->fail('消息ID不能为空');
        }

        $result = NoticeLogic::delete($id);
        if (!$result) {
            return $this->fail('删除失败');
        }

        return $this->success('删除成功');
    }

    /**
     * 标记已读
     * @return Response
     */
    public function read(): Response
    {
        $params = $this->param();
        $params['receiver_id'] = $this->getAdminId();

        $affected = NoticeLogic::markRead($params);
        return $this->success('标记成功', ['affected' => $affected]);
    }

    /**
     * 获取未读消息数量
     * @return Response
     */
    public function unreadCount(): Response
    {
        $count = NoticeLogic::getUnreadCount($this->getAdminId());
        return $this->data(['unread_count' => $count]);
    }

    /**
     * 统一发送消息接口
     * 支持模板发送和直接发送
     * @return Response
     */
    public function sendMessage(): Response
    {
        $params = $this->param();
        $channelCode = $params['channel_code'] ?? '';
        $receiver = $params['receiver'] ?? '';
        $templateCode = $params['template_code'] ?? '';
        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';
        $vars = $params['vars'] ?? [];

        if (empty($channelCode)) {
            return $this->fail('请选择发送渠道');
        }
        if (empty($receiver)) {
            return $this->fail('接收者不能为空');
        }

        // 如果指定了模板，使用模板发送
        if (!empty($templateCode)) {
            $result = SendMessageService::sendByTemplate(
                $templateCode,
                $receiver,
                $vars,
                $this->getAdminId(),
                $params['sender_name'] ?? '管理员'
            );
        } else {
            // 直接发送
            if (empty($title)) {
                return $this->fail('消息标题不能为空');
            }
            if (empty($content)) {
                return $this->fail('消息内容不能为空');
            }

            $result = SendMessageService::send(
                $channelCode,
                $receiver,
                $title,
                $content,
                $params['config'] ?? [],
                [
                    'sender_id' => $this->getAdminId(),
                    'sender_name' => $params['sender_name'] ?? '管理员',
                ]
            );
        }

        return $result['status']
            ? $this->success($result['msg'], ['record_id' => $result['record_id'] ?? 0])
            : $this->fail($result['msg']);
    }
}

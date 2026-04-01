<?php
declare(strict_types=1);

namespace app\service;

use app\model\NoticeChannel;
use app\model\NoticeTemplate;
use app\model\NoticeRecord;
use app\sender\EmailSender;
use app\sender\SmsSender;
use app\sender\WechatSender;
use app\sender\MessageSender;
use think\facade\Log;

/**
 * 统一消息发送服务
 */
class SendMessageService
{
    /**
     * 发送器实例缓存
     */
    protected static array $senders = [];

    /**
     * 获取发送器
     */
    protected static function getSender(string $channelCode): ?object
    {
        if (isset(self::$senders[$channelCode])) {
            return self::$senders[$channelCode];
        }

        $map = [
            'email' => EmailSender::class,
            'sms' => SmsSender::class,
            'wechat' => WechatSender::class,
            'message' => MessageSender::class,
        ];

        $class = $map[$channelCode] ?? null;
        if ($class && class_exists($class)) {
            self::$senders[$channelCode] = new $class();
            return self::$senders[$channelCode];
        }

        return null;
    }

    /**
     * 发送消息（通过模板）
     * @param string $templateCode 模板编码
     * @param string $receiver 接收者
     * @param array $vars 变量值
     * @param int $senderId 发送者ID
     * @param string $senderName 发送者名称
     * @return array ['status' => bool, 'msg' => string, 'record_id' => int]
     */
    public static function sendByTemplate(
        string $templateCode,
        string $receiver,
        array $vars = [],
        int $senderId = 0,
        string $senderName = '系统'
    ): array {
        $template = NoticeTemplate::getByCode($templateCode);
        if (empty($template)) {
            return ['status' => false, 'msg' => '模板不存在: ' . $templateCode, 'record_id' => 0];
        }

        $channel = NoticeChannel::find((int) $template->channel_id);
        if (empty($channel) || $channel->status != NoticeChannel::STATUS_ENABLED) {
            return ['status' => false, 'msg' => '渠道不可用', 'record_id' => 0];
        }

        // 渲染模板
        $rendered = $template->render($vars);

        return self::send(
            $channel->code,
            $receiver,
            $rendered['title'],
            $rendered['content'],
            $channel->config,
            [
                'template_id' => $template->id,
                'template_code' => $template->code,
                'channel_type' => $channel->type,
                'vars' => $vars,
                'sender_id' => $senderId,
                'sender_name' => $senderName,
            ]
        );
    }

    /**
     * 直接发送消息
     */
    public static function send(
        string $channelCode,
        string $receiver,
        string $title,
        string $content,
        array $config = [],
        array $extra = []
    ): array {
        $sender = self::getSender($channelCode);
        if (empty($sender)) {
            return ['status' => false, 'msg' => '不支持的渠道: ' . $channelCode, 'record_id' => 0];
        }

        // 追加站内信专用配置
        if ($channelCode === 'message') {
            $config['sender_id'] = $extra['sender_id'] ?? 0;
            $config['sender_name'] = $extra['sender_name'] ?? '系统';
            $config['type'] = $extra['type'] ?? 1;
        }

        // 记录发送
        $record = new NoticeRecord();
        $record->template_id = $extra['template_id'] ?? null;
        $record->template_code = $extra['template_code'] ?? null;
        $record->channel_code = $channelCode;
        $record->channel_type = $extra['channel_type'] ?? 0;
        $record->receiver = $receiver;
        $record->title = $title;
        $record->content = $content;
        $record->vars = $extra['vars'] ?? [];
        $record->status = NoticeRecord::STATUS_PENDING;
        $record->send_time = date('Y-m-d H:i:s');
        $record->save();

        try {
            // 执行发送
            $result = $sender->send($receiver, $title, $content, $config);

            // 更新记录状态
            $record->status = $result['status']
                ? NoticeRecord::STATUS_SUCCESS
                : NoticeRecord::STATUS_FAILED;
            $record->error_msg = $result['status'] ? null : $result['msg'];
            $record->save();

            return [
                'status' => $result['status'],
                'msg' => $result['msg'],
                'record_id' => $record->id,
            ];
        } catch (\Throwable $e) {
            Log::error('SendMessageService error: ' . $e->getMessage(), [
                'channel' => $channelCode,
                'receiver' => $receiver,
            ]);

            $record->status = NoticeRecord::STATUS_FAILED;
            $record->error_msg = $e->getMessage();
            $record->save();

            return ['status' => false, 'msg' => $e->getMessage(), 'record_id' => $record->id];
        }
    }

    /**
     * 批量发送（通过模板）
     */
    public static function batchSendByTemplate(
        string $templateCode,
        array $receivers,
        array $vars = [],
        int $senderId = 0,
        string $senderName = '系统'
    ): array {
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($receivers as $receiver) {
            $result = self::sendByTemplate($templateCode, (string) $receiver, $vars, $senderId, $senderName);
            if ($result['status']) {
                $success++;
            } else {
                $failed++;
                $errors[] = $receiver . ': ' . $result['msg'];
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }
}

<?php
declare(strict_types=1);

namespace app\sender;

use app\model\Notice;

/**
 * 站内信发送器
 */
class MessageSender implements NoticeSenderInterface
{
    public function getChannelCode(): string
    {
        return 'message';
    }

    public function send(string $receiver, string $title, string $content, array $config = []): array
    {
        try {
            // receiver 为用户ID (支持逗号分隔的多个ID)
            $receiverIds = array_filter(array_map('intval', explode(',', $receiver)));
            
            if (empty($receiverIds)) {
                return ['status' => false, 'msg' => '接收者ID不能为空'];
            }

            $type = $config['type'] ?? Notice::TYPE_SYSTEM;
            $senderId = $config['sender_id'] ?? 0;
            $senderName = $config['sender_name'] ?? '系统';

            foreach ($receiverIds as $receiverId) {
                $notice = new Notice();
                $notice->title = $title;
                $notice->content = $content;
                $notice->type = $type;
                $notice->sender_id = $senderId;
                $notice->sender_name = $senderName;
                $notice->receiver_id = $receiverId;
                $notice->status = Notice::STATUS_ENABLED;
                $notice->is_read = Notice::UNREAD;
                $notice->save();
            }

            return ['status' => true, 'msg' => '站内信发送成功，共发送' . count($receiverIds) . '条'];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => '站内信发送失败: ' . $e->getMessage()];
        }
    }
}

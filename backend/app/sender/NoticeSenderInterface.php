<?php
declare(strict_types=1);

namespace app\sender;

/**
 * 通知发送器接口
 */
interface NoticeSenderInterface
{
    /**
     * 获取渠道编码
     */
    public function getChannelCode(): string;

    /**
     * 发送消息
     * @param string $receiver 接收者 (邮箱/手机号/企微webhook/user_id)
     * @param string $title 标题
     * @param string $content 内容
     * @param array $config 渠道配置
     * @return array ['status' => true/false, 'msg' => '...']
     */
    public function send(string $receiver, string $title, string $content, array $config = []): array;
}

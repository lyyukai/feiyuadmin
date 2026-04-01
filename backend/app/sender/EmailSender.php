<?php
declare(strict_types=1);

namespace app\sender;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * 邮件发送器
 */
class EmailSender implements NoticeSenderInterface
{
    public function getChannelCode(): string
    {
        return 'email';
    }

    public function send(string $receiver, string $title, string $content, array $config = []): array
    {
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';

            // 服务器配置
            $mail->isSMTP();
            $mail->Host = $config['host'] ?? 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'] ?? '';
            $mail->Password = $config['password'] ?? '';
            $mail->SMTPSecure = $config['secure'] ?? 'tls';
            $mail->Port = (int) ($config['port'] ?? 587);

            // 发件人和收件人
            $mail->setFrom($config['from_email'] ?? $mail->Username, $config['from_name'] ?? '飞羽系统');
            $mail->addAddress($receiver);

            // 内容
            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body = nl2br($content);
            $mail->AltBody = strip_tags($content);

            $mail->send();

            return ['status' => true, 'msg' => '邮件发送成功'];
        } catch (Exception $e) {
            return ['status' => false, 'msg' => '邮件发送失败: ' . $e->getMessage()];
        }
    }
}

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

$envConfig = EnvSetup::env(__DIR__);

class MailerService
{
    public static function getMailer()
    {
        $mail = new PHPMailer(true);

        $envConfig = EnvSetup::env(__DIR__);
        
        try {
            // Cấu hình Server (SMTP)
            $mail->isSMTP();
            $mail->Host       = $envConfig('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = $envConfig('MAIL_USERNAME');
            $mail->Password   = $envConfig('MAIL_PASSWORD');
            $mail->SMTPSecure = $envConfig('MAIL_ENCRYPTION'); // tls hoặc ssl
            $mail->Port       = $envConfig('MAIL_PORT');
            $mail->CharSet    = 'UTF-8'; // Hỗ trợ gửi tiếng Việt

            return $mail;
        } catch (Exception $e) {
            error_log("Lỗi cấu hình Mailer: {$mail->ErrorInfo}");
            return null;
        }
    }
}

// Cách sử dụng ở nơi khác:
// $mail = MailerService::getMailer();
// $mail->setFrom('admin@example.com', 'Admin');
// $mail->addAddress('khachhang@example.com');
// $mail->Subject = 'Tiêu đề email';
// $mail->Body    = 'Nội dung email';
// $mail->send();
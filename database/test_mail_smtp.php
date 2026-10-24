<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
    // Xóa khoảng trắng trong mật khẩu ứng dụng Gmail nếu có
    $mail->Password   = str_replace(' ', '', $_ENV['MAIL_PASSWORD'] ?? '');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 587);
    $mail->CharSet    = 'UTF-8';
    $mail->Timeout    = 10;

    echo "Host: " . $mail->Host . ":" . $mail->Port . PHP_EOL;
    echo "User: " . $mail->Username . PHP_EOL;

    // Test connect
    if ($mail->smtpConnect()) {
        echo "SUCCESS: SMTP Connected and Authenticated Successfully!" . PHP_EOL;
        $mail->smtpClose();
    } else {
        echo "FAILED: Could not connect to SMTP server." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}


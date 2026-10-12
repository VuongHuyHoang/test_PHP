<?php

namespace App\Helpers;

class MailHelper
{
    /**
     * Gửi email xác nhận đặt vé và E-Voucher cho khách hàng
     */
    public static function guiEmailXacNhan(array $donHang, array $chiTiet = []): bool
    {
        $toEmail = $donHang['email_khach_hang'] ?? '';
        $toName  = $donHang['ten_khach_hang'] ?? 'Quý khách';
        $maDon   = $donHang['ma_don_hang'] ?? '';
        $tongTien = FormatHelper::tienTe((float)($donHang['tong_tien'] ?? 0));

        if (empty($toEmail)) {
            return false;
        }

        $subject = "[Nha Trang Discovery] Xác nhận đặt vé thành công - Mã đơn #{$maDon}";

        $messageBody = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b;'>
                <div style='max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;'>
                    <h2 style='color: #137a8e; margin-top: 0;'>Xác Nhận Phiếu E-Voucher Điện Tử</h2>
                    <p>Xin chào <strong>{$toName}</strong>,</p>
                    <p>Đơn đặt vé tham quan tại Nha Trang của bạn đã được xác nhận thành công!</p>
                    
                    <div style='background: #f8fafc; border-left: 4px solid #137a8e; padding: 12px 16px; margin: 20px 0;'>
                        <p style='margin: 4px 0;'><strong>Mã đơn hàng:</strong> <span style='font-size: 16px; color: #0b2545;'>{$maDon}</span></p>
                        <p style='margin: 4px 0;'><strong>Tổng thanh toán:</strong> {$tongTien}</p>
                        <p style='margin: 4px 0;'><strong>Trạng thái:</strong> Đã thanh toán</p>
                    </div>

                    <p>Vui lòng xuất trình mã đơn hàng này hoặc mã QR E-Voucher tại quầy lễ tân để nhận vé vào cổng.</p>
                    <p style='margin-top: 24px; font-size: 13px; color: #64748b;'>Cảm ơn bạn đã đồng hành cùng Nha Trang Discovery!<br>Hotline hỗ trợ: 1900 1234</p>
                </div>
            </body>
            </html>
        ";

        // Ghi nhật ký email cục bộ để kiểm tra trong môi trường phát triển
        $logDir = dirname(__DIR__, 2) . '/public/uploads';
        if (is_dir($logDir)) {
            $logContent = "[" . date('Y-m-d H:i:s') . "] Gửi tới: {$toEmail} | Mã đơn: {$maDon} | Tiêu đề: {$subject}\n";
            @file_put_contents($logDir . '/email_logs.txt', $logContent, FILE_APPEND);
        }

        // Nếu có cấu hình tài khoản SMTP thực tế, gửi qua mail()
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . ($_ENV['MAIL_FROM_NAME'] ?? 'Nha Trang Tourism') . ' <' . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'support@nhatrangtourism.local') . '>'
        ];

        if (!empty($_ENV['MAIL_USERNAME']) && !empty($_ENV['MAIL_PASSWORD'])) {
            return @mail($toEmail, $subject, $messageBody, implode("\r\n", $headers));
        }

        return true;
    }
}


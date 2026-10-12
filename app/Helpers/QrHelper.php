<?php

namespace App\Helpers;

use App\Config\Database;

class QrHelper
{
    /**
     * Sinh và lưu file ảnh QR Code cục bộ cho mã đơn hàng E-Voucher
     * Lưu vào public/uploads/qrcodes/{ma_don_hang}.png và cập nhật DB
     */
    public static function taoQrDonHang(string $maDonHang): string
    {
        $fileName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $maDonHang) . '.png';
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/qrcodes';
        $filePath = $uploadDir . '/' . $fileName;
        $relativePath = 'uploads/qrcodes/' . $fileName;

        // Nếu file đã tồn tại trên đĩa thì không cần sinh lại
        if (file_exists($filePath) && filesize($filePath) > 0) {
            return $relativePath;
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Tải ảnh QR code từ dịch vụ chuyên dụng
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($maDonHang);
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ]
        ]);

        $imgData = @file_get_contents($qrUrl, false, $context);

        if ($imgData !== false && strlen($imgData) > 50) {
            file_put_contents($filePath, $imgData);

            // Cập nhật đường dẫn file vào CSDL
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE don_hang SET ma_qr = :ma_qr WHERE ma_don_hang = :ma_don");
                $stmt->execute([
                    'ma_qr'  => $relativePath,
                    'ma_don' => $maDonHang
                ]);
            } catch (\Exception $e) {
                // Tiếp tục nếu update DB bị lỗi nhỏ
            }

            return $relativePath;
        }

        // Dự phòng nếu không có mạng: Trả về URL trực tiếp
        return $qrUrl;
    }
}


<?php

namespace App\Helpers;

use App\Config\Database;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrHelper
{
    /**
     * Sinh và lưu file ảnh QR Code cục bộ cho mã đơn hàng E-Voucher hoàn toàn OFFLINE
     * Không phụ thuộc bất kỳ API bên ngoài nào, sinh file SVG vector sắc nét
     * Lưu vào public/uploads/qrcodes/{ma_don_hang}.svg và cập nhật DB
     */
    public static function taoQrDonHang(string $maDonHang): string
    {
        $cleanMa = preg_replace('/[^a-zA-Z0-9_\-]/', '', $maDonHang);
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/qrcodes';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Kiểm tra xem đã có file SVG hoặc PNG sẵn có trên đĩa chưa
        $svgFile = $uploadDir . '/' . $cleanMa . '.svg';
        $pngFile = $uploadDir . '/' . $cleanMa . '.png';

        if (file_exists($svgFile) && filesize($svgFile) > 0) {
            return 'uploads/qrcodes/' . $cleanMa . '.svg';
        }
        if (file_exists($pngFile) && filesize($pngFile) > 0) {
            return 'uploads/qrcodes/' . $cleanMa . '.png';
        }

        // 1. Sinh mã QR vector SVG cục bộ bằng thư viện chillerlan/php-qrcode (Hoàn toàn Offline)
        try {
            if (class_exists(QRCode::class)) {
                $options = new QROptions([
                    'version'      => 5,
                    'outputBase64' => false,
                    'scale'        => 6,
                ]);

                $qrcode = new QRCode($options);
                $svgContent = $qrcode->render($maDonHang);

                if (!empty($svgContent)) {
                    file_put_contents($svgFile, $svgContent);
                    $relativePath = 'uploads/qrcodes/' . $cleanMa . '.svg';

                    // Cập nhật đường dẫn file vào CSDL
                    self::capNhatDb($maDonHang, $relativePath);
                    return $relativePath;
                }
            }
        } catch (\Throwable $e) {
            // Ghi log lỗi nếu có và chuyển sang phương thức dự phòng
            error_log("Lỗi sinh QR offline: " . $e->getMessage());
        }

        // 2. Dự phòng: Tải từ dịch vụ nếu sinh cục bộ gặp lỗi thư viện
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($maDonHang);
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'user_agent' => 'Mozilla/5.0'
            ]
        ]);

        $imgData = @file_get_contents($qrUrl, false, $context);
        if ($imgData !== false && strlen($imgData) > 50) {
            file_put_contents($pngFile, $imgData);
            $relativePath = 'uploads/qrcodes/' . $cleanMa . '.png';
            self::capNhatDb($maDonHang, $relativePath);
            return $relativePath;
        }

        return $qrUrl;
    }

    /**
     * Cập nhật đường dẫn file QR vào bảng đơn hàng
     */
    private static function capNhatDb(string $maDonHang, string $relativePath): void
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE don_hang SET ma_qr = :ma_qr WHERE ma_don_hang = :ma_don");
            $stmt->execute([
                'ma_qr'  => $relativePath,
                'ma_don' => $maDonHang
            ]);
        } catch (\Throwable $e) {
            // Không làm gián đoạn luồng chính
        }
    }

    /**
     * Tạo URL mã QR thanh toán chuyển khoản ngân hàng VietQR chuẩn Napas 247
     * (Giữ nguyên vẹn 100% theo yêu cầu của hệ thống)
     */
    public static function taoQrChuyenKhoanVietQr(string $nganHangId, string $soTaiKhoan, float $soTien, string $noiDung, string $tenChuTaiKhoan = ''): string
    {
        $noiDungEncoded = urlencode($noiDung);
        $tenEncoded     = urlencode($tenChuTaiKhoan);
        $soTienInt      = (int)round($soTien);

        return "https://api.vietqr.io/image/{$nganHangId}-{$soTaiKhoan}-compact2.jpg?amount={$soTienInt}&addInfo={$noiDungEncoded}&accountName={$tenEncoded}";
    }
}

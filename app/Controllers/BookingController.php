<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Models\GoiVe;
use App\Models\TonKhoVe;
use App\Models\DonHang;
use App\Services\BookingService;
use Exception;

class BookingController
{
    private GoiVe $goiVeModel;
    private TonKhoVe $tonKhoModel;
    private DonHang $donHangModel;
    private BookingService $bookingService;

    public function __construct()
    {
        $this->goiVeModel = new GoiVe();
        $this->tonKhoModel = new TonKhoVe();
        $this->donHangModel = new DonHang();
        $this->bookingService = new BookingService();
    }

    public function showForm(string|int $goiVeId): void
    {
        AuthMiddleware::checkCustomer();

        $goiVe = $this->goiVeModel->timTheoId((int)$goiVeId);
        if (!$goiVe) {
            flash('error', 'Gói vé không tồn tại hoặc đã ngừng phục vụ.');
            redirect(url(''));
        }

        view('client.booking.form', [
            'title' => 'Đặt Vé Trực Tuyến - ' . $goiVe['ten_goi_ve'],
            'goiVe' => $goiVe,
        ]);
    }

    public function submitBooking(): void
    {
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ. Vui lòng thử lại.');
            $goiVeId = (int)($_POST['goi_ve_id'] ?? 0);
            redirect(url('booking/' . $goiVeId));
            return;
        }

        $goiVeId = (int)($_POST['goi_ve_id'] ?? 0);
        $ngayThamQuan = trim($_POST['ngay_tham_quan'] ?? '');
        $slNguoiLon = (int)($_POST['so_luong_nguoi_lon'] ?? 0);
        $slTreEm = (int)($_POST['so_luong_tre_em'] ?? 0);
        $phuongThuc = $_POST['phuong_thuc_thanh_toan'] ?? 'chuyen_khoan';

        if ($goiVeId <= 0 || empty($ngayThamQuan) || ($slNguoiLon + $slTreEm <= 0)) {
            flash('error', 'Vui lòng chọn ngày tham quan và số lượng vé hợp lệ.');
            redirect(url('booking/' . $goiVeId));
        }

        // Lấy thông tin gói vé để lấy đơn giá phía server
        $goiVe = $this->goiVeModel->timTheoId($goiVeId);
        if (!$goiVe) {
            flash('error', 'Gói vé không hợp lệ.');
            redirect(url(''));
        }

        // Tìm hoặc tự động khởi tạo tồn kho theo ngày
        $tonKho = $this->tonKhoModel->layHoacTaoTheoGoiVeVaNgay($goiVeId, $ngayThamQuan);
        if (!$tonKho) {
            flash('error', 'Ngày được chọn không hợp lệ hoặc đã qua thời hạn đặt vé.');
            redirect(url('booking/' . $goiVeId));
            return;
        }

        $thongTinKhach = [
            'ten'         => trim($_POST['ten_khach_hang'] ?? $_SESSION['user_name']),
            'email'       => trim($_POST['email_khach_hang'] ?? $_SESSION['user_email']),
            'sdt'         => trim($_POST['sdt_khach_hang'] ?? ''),
            'phuong_thuc' => $phuongThuc
        ];

        try {
            $maDonHang = $this->bookingService->datVe(
                (int)$_SESSION['user_id'],
                (int)$tonKho['id'],
                $slNguoiLon,
                $slTreEm,
                (float)$goiVe['gia_nguoi_lon'],
                (float)$goiVe['gia_tre_em'],
                $ngayThamQuan,
                $thongTinKhach
            );

            // Sinh mã QR cục bộ trước
            \App\Helpers\QrHelper::taoQrDonHang($maDonHang);

            flash('success', 'Đặt vé thành công! Mã đơn hàng của bạn là: ' . $maDonHang);

            if ($phuongThuc === 'tai_quay') {
                redirect(url('voucher/' . $maDonHang));
            } else {
                redirect(url('booking/payment/' . $maDonHang));
            }
        } catch (Exception $e) {
            flash('error', 'Lỗi đặt vé: ' . $e->getMessage());
            redirect(url('booking/' . $goiVeId));
        }
    }

    public function showPayment(string $maDonHang): void
    {
        AuthMiddleware::checkCustomer();

        $donHang = $this->donHangModel->timTheoMa($maDonHang);
        if (!$donHang) {
            http_response_code(404);
            view('layouts.404', ['title' => 'Không tìm thấy đơn hàng']);
            return;
        }

        // Chỉ chủ đơn hàng hoặc Admin mới có quyền xem
        if ($donHang['nguoi_dung_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Bạn không có quyền truy cập đơn hàng này.");
        }

        // Nếu đã hủy, chuyển hướng về profile
        if ($donHang['trang_thai_don_hang'] === 'da_huy') {
            flash('warning', 'Đơn hàng ' . $maDonHang . ' đã bị hủy hoặc hết hạn thời gian giữ chỗ.');
            redirect(url('profile'));
            return;
        }

        // Nếu đã thanh toán, chuyển hướng trực tiếp tới voucher
        if ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan') {
            redirect(url('voucher/' . $maDonHang));
            return;
        }

        // Tính thời gian còn lại của thời hạn giữ chỗ 10 phút (600 giây)
        $createdTimestamp = strtotime($donHang['ngay_tao']);
        $elapsed = time() - $createdTimestamp;
        $remainingSeconds = max(0, 600 - $elapsed);

        // Tự động hủy nếu đã quá 10 phút
        if ($remainingSeconds <= 0 && $donHang['trang_thai_thanh_toan'] === 'cho_thanh_toan') {
            $this->donHangModel->huyDonHang((int)$donHang['id'], 'Hết hạn giữ chỗ 10 phút');
            flash('error', 'Đơn hàng ' . $maDonHang . ' đã hết hạn thời gian giữ chỗ (10 phút) và đã được tự động hủy để giải phóng vé.');
            redirect(url('profile'));
            return;
        }

        view('client.booking.payment', [
            'title'            => 'Cổng Thanh Toán - ' . $maDonHang,
            'donHang'          => $donHang,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function confirmPayment(): void
    {
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ. Vui lòng thử lại.');
            redirect(url(''));
            return;
        }

        $maDonHang = trim($_POST['ma_don_hang'] ?? '');
        if (empty($maDonHang)) {
            flash('error', 'Mã đơn hàng không hợp lệ.');
            redirect(url(''));
            return;
        }

        $donHang = $this->donHangModel->timTheoMa($maDonHang);
        if (!$donHang) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect(url(''));
            return;
        }

        if ($donHang['nguoi_dung_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Bạn không có quyền cập nhật đơn hàng này.");
        }

        try {
            $db = \App\Config\Database::getConnection();
            $db->beginTransaction();

            // Cập nhật trạng thái đơn hàng
            $stmt = $db->prepare("
                UPDATE don_hang 
                SET trang_thai_thanh_toan = 'da_thanh_toan', 
                    trang_thai_don_hang = 'da_xac_nhan' 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $donHang['id']]);

            // Thêm bản ghi giao dịch thanh toán
            $maGiaoDich = 'TXN' . date('YmdHis') . rand(100, 999);
            $stmtGiaoDich = $db->prepare("
                INSERT INTO giao_dich_thanh_toan 
                (don_hang_id, cong_thanh_toan, ma_giao_dich, so_tien, loai_tien_te, du_lieu_phan_hoi, trang_thai) 
                VALUES 
                (:don_hang_id, :cong, :ma_gd, :so_tien, 'VND', :phan_hoi, 'thanh_cong')
            ");
            $stmtGiaoDich->execute([
                'don_hang_id' => $donHang['id'],
                'cong'        => $donHang['phuong_thuc_thanh_toan'],
                'ma_gd'       => $maGiaoDich,
                'so_tien'     => $donHang['tong_tien'],
                'phan_hoi'    => json_encode([
                    'hinh_thuc' => 'VietQR Manual Transfer',
                    'thoi_gian' => date('Y-m-d H:i:s'),
                    'nguoi_xac_nhan_id' => $_SESSION['user_id']
                ], JSON_UNESCAPED_UNICODE)
            ]);

            $db->commit();

            // Sinh mã QR E-Voucher trên máy chủ
            \App\Helpers\QrHelper::taoQrDonHang($maDonHang);

            // Gửi email xác nhận kèm chi tiết vé
            $chiTiet = $this->donHangModel->chiTiet((int)$donHang['id']);
            \App\Helpers\MailHelper::guiEmailXacNhan($donHang, $chiTiet);

            flash('success', 'Thanh toán thành công! E-Voucher của bạn đã được kích hoạt.');
            redirect(url('voucher/' . $maDonHang));
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            flash('error', 'Lỗi khi xác nhận thanh toán: ' . $e->getMessage());
            redirect(url('booking/payment/' . $maDonHang));
        }
    }

    /**
     * API Giả lập quét mã VietQR chuyển tiền thành công (Demo Sandbox cho Học tập/Đồ án)
     */
    public function simulatePayment(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $maDonHang = trim($_POST['ma_don_hang'] ?? '');
        if (empty($maDonHang)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Mã đơn hàng không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $donHang = $this->donHangModel->timTheoMa($maDonHang);
        if (!$donHang) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Không tìm thấy đơn hàng trong hệ thống.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($donHang['nguoi_dung_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Bạn không có quyền cập nhật đơn hàng này.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Nếu đã thanh toán rồi thì trả về success ngay
        if ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan') {
            echo json_encode([
                'status'      => 'success',
                'message'     => 'Đơn hàng này đã được thanh toán thành công trước đó.',
                'voucher_url' => url('voucher/' . $maDonHang)
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $db = \App\Config\Database::getConnection();
            $db->beginTransaction();

            // 1. Cập nhật trạng thái đơn hàng
            $stmt = $db->prepare("
                UPDATE don_hang 
                SET trang_thai_thanh_toan = 'da_thanh_toan', 
                    trang_thai_don_hang = 'da_xac_nhan' 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $donHang['id']]);

            // 2. Thêm bản ghi giao dịch mô phỏng Sandbox
            $maGiaoDich = 'SIM_NAPAS_' . date('YmdHis') . rand(100, 999);
            $stmtGiaoDich = $db->prepare("
                INSERT INTO giao_dich_thanh_toan 
                (don_hang_id, cong_thanh_toan, ma_giao_dich, so_tien, loai_tien_te, du_lieu_phan_hoi, trang_thai) 
                VALUES 
                (:don_hang_id, 'vietqr_sandbox', :ma_gd, :so_tien, 'VND', :phan_hoi, 'thanh_cong')
            ");
            $stmtGiaoDich->execute([
                'don_hang_id' => $donHang['id'],
                'ma_gd'       => $maGiaoDich,
                'so_tien'     => $donHang['tong_tien'],
                'phan_hoi'    => json_encode([
                    'hinh_thuc'       => 'VietQR Sandbox Simulator (Chế Độ Học Tập)',
                    'ma_giao_dich'    => $maGiaoDich,
                    'thoi_gian'       => date('Y-m-d H:i:s'),
                    'nguoi_thuc_hien' => $_SESSION['user_name'] ?? 'Khách hàng'
                ], JSON_UNESCAPED_UNICODE)
            ]);

            $db->commit();

            // 3. Sinh mã QR E-Voucher
            \App\Helpers\QrHelper::taoQrDonHang($maDonHang);

            // 4. Gửi email xác nhận kèm chi tiết vé
            $chiTiet = $this->donHangModel->chiTiet((int)$donHang['id']);
            \App\Helpers\MailHelper::guiEmailXacNhan($donHang, $chiTiet);

            flash('success', 'Thanh toán thành công! E-Voucher của bạn đã được kích hoạt.');

            echo json_encode([
                'status'      => 'success',
                'message'     => 'Xác nhận thanh toán mô phỏng thành công!',
                'voucher_url' => url('voucher/' . $maDonHang)
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            echo json_encode([
                'status'  => 'error',
                'message' => 'Lỗi khi xử lý giao dịch: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function showVoucher(string $maDonHang): void
    {
        AuthMiddleware::checkCustomer();

        $donHang = $this->donHangModel->timTheoMa($maDonHang);
        if (!$donHang) {
            http_response_code(404);
            view('layouts.404', ['title' => 'Không tìm thấy phiếu đặt vé']);
            return;
        }

        // Chỉ chủ đơn hàng hoặc Admin mới có quyền xem
        if ($donHang['nguoi_dung_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Bạn không có quyền truy cập đơn hàng này.");
        }

        $chiTiet = $this->donHangModel->chiTiet((int)$donHang['id']);

        view('client.booking.voucher', [
            'title'   => 'Phiếu Xác Nhận E-Voucher - ' . $maDonHang,
            'donHang' => $donHang,
            'chiTiet' => $chiTiet,
        ]);
    }

    /**
     * Hủy đơn hàng chủ động bởi người dùng
     */
    public function cancelBooking(): void
    {
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('profile'));
            return;
        }

        $maDonHang = trim($_POST['ma_don_hang'] ?? '');
        $donHang = $this->donHangModel->timTheoMa($maDonHang);

        if (!$donHang) {
            flash('error', 'Không tìm thấy đơn hàng cần hủy.');
            redirect(url('profile'));
            return;
        }

        if ($donHang['nguoi_dung_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Bạn không có quyền hủy đơn hàng này.");
        }

        if ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan') {
            flash('error', 'Đơn hàng đã thanh toán thành công, không thể hủy trực tiếp tại đây.');
            redirect(url('profile'));
            return;
        }

        $lyDo = trim($_POST['ly_do'] ?? 'Người dùng chủ động hủy');
        $success = $this->donHangModel->huyDonHang((int)$donHang['id'], $lyDo);

        if ($success) {
            flash('success', 'Đã hủy đơn hàng ' . $maDonHang . ' thành công. Số lượng vé giữ chỗ đã được giải phóng.');
        } else {
            flash('error', 'Không thể hủy đơn hàng vào lúc này.');
        }

        redirect(url('profile'));
    }

    /**
     * API kiểm tra trạng thái đơn hàng & thời gian giữ chỗ theo thời gian thực (Polling)
     */
    public function checkOrderStatus(string $maDonHang): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $donHang = $this->donHangModel->timTheoMa($maDonHang);
        if (!$donHang) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng']);
            exit;
        }

        $createdTime = strtotime($donHang['ngay_tao']);
        $elapsed = time() - $createdTime;
        $remainingSeconds = max(0, 600 - $elapsed);

        $isCancelled = ($donHang['trang_thai_don_hang'] === 'da_huy');
        $isPaid = ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan');

        // Tự động hủy nếu đã hết 10 phút
        if ($remainingSeconds <= 0 && !$isPaid && !$isCancelled) {
            $this->donHangModel->huyDonHang((int)$donHang['id'], 'Hết hạn giữ chỗ 10 phút');
            $isCancelled = true;
        }

        $mins = floor($remainingSeconds / 60);
        $secs = $remainingSeconds % 60;
        $formattedTime = sprintf('%02d:%02d', $mins, $secs);

        echo json_encode([
            'status'            => 'success',
            'ma_don_hang'       => $maDonHang,
            'is_paid'           => $isPaid,
            'is_cancelled'      => $isCancelled,
            'remaining_seconds' => $remainingSeconds,
            'formatted_time'    => $formattedTime
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}


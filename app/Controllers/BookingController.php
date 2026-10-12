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

        // Tìm hoặc kiểm tra tồn kho theo ngày
        $tonKho = $this->tonKhoModel->layTheoGoiVeVaNgay($goiVeId, $ngayThamQuan);
        if (!$tonKho) {
            flash('error', 'Ngày được chọn hiện chưa mở bán vé hoặc đã đóng cổng đặt vé.');
            redirect(url('booking/' . $goiVeId));
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

        // Nếu đã thanh toán, chuyển hướng trực tiếp tới voucher
        if ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan') {
            redirect(url('voucher/' . $maDonHang));
            return;
        }

        view('client.booking.payment', [
            'title'   => 'Cổng Thanh Toán - ' . $maDonHang,
            'donHang' => $donHang,
        ]);
    }

    public function confirmPayment(): void
    {
        AuthMiddleware::checkCustomer();

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
}


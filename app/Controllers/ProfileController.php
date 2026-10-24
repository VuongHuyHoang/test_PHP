<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Models\NguoiDung;
use App\Models\DonHang;
use App\Models\YeuThich;
use Exception;

class ProfileController
{
    private NguoiDung $nguoiDungModel;
    private DonHang $donHangModel;
    private YeuThich $yeuThichModel;

    public function __construct()
    {
        $this->nguoiDungModel = new NguoiDung();
        $this->donHangModel = new DonHang();
        $this->yeuThichModel = new YeuThich();
    }

    public function index(): void
    {
        AuthMiddleware::checkCustomer();

        // Nếu là Quản trị viên, tự động chuyển về Admin Portal (Ẩn chức năng Vé & Hồ sơ khách hàng)
        if (($_SESSION['user_role'] ?? '') === 'admin') {
            redirect(url('admin/dashboard'));
            return;
        }

        $userId = (int)$_SESSION['user_id'];

        $user = $this->nguoiDungModel->timTheoId($userId);
        if (!$user) {
            flash('error', 'Tài khoản không tồn tại hoặc đã bị khóa.');
            redirect(url('logout'));
            return;
        }

        // Lấy danh sách vé đã đặt
        $orders = $this->donHangModel->theoNguoiDungKemChiTiet($userId);

        // Lấy lịch sử giao dịch thanh toán
        $transactions = $this->donHangModel->layGiaoDichNguoiDung($userId);

        // Tính toán các chỉ số tóm tắt (KPIs)
        $validTicketsCount = 0;
        $totalSpent = 0;
        $pendingPaymentCount = 0;

        foreach ($orders as $o) {
            if ($o['trang_thai_thanh_toan'] === 'da_thanh_toan') {
                $totalSpent += (float)$o['tong_tien'];
                if ($o['trang_thai_don_hang'] !== 'da_huy') {
                    $validTicketsCount += (int)($o['tong_so_ve'] ?? 1);
                }
            } elseif ($o['trang_thai_thanh_toan'] === 'cho_thanh_toan') {
                $pendingPaymentCount++;
            }
        }

        // Lấy danh sách địa điểm yêu thích (Wishlist)
        $favorites = $this->yeuThichModel->danhSachTheoNguoiDung($userId);

        // Lấy danh sách đánh giá & địa điểm của người dùng
        $danhGiaModel = new \App\Models\DanhGia();
        $myReviews = $danhGiaModel->layTheoNguoiDung($userId);

        // Lấy danh sách lịch trình tour AI đã lưu
        $aiTourService = new \App\Services\AiTourService();
        $savedTours = $aiTourService->getSavedItineraries($userId);

        $activeTab = $_GET['tab'] ?? 'tickets';

        view('client.profile.index', [
            'title'               => 'Vé Của Tôi & Hồ Sơ Cá Nhân',
            'user'                => $user,
            'orders'              => $orders,
            'transactions'        => $transactions,
            'favorites'           => $favorites,
            'myReviews'           => $myReviews,
            'savedTours'          => $savedTours,
            'validTicketsCount'   => $validTicketsCount,
            'totalSpent'          => $totalSpent,
            'pendingPaymentCount' => $pendingPaymentCount,
            'activeTab'           => $activeTab,
        ]);
    }

    public function updateProfile(): void
    {
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('profile?tab=account'));
            return;
        }

        $userId = (int)$_SESSION['user_id'];

        $hoTen = trim($_POST['ho_ten'] ?? '');
        $sdt = trim($_POST['so_dien_thoai'] ?? '');

        if (empty($hoTen)) {
            flash('error', 'Họ và tên không được để trống.');
            redirect(url('profile?tab=account'));
            return;
        }

        try {
            $this->nguoiDungModel->capNhatThongTin($userId, $hoTen, $sdt ?: null);
            $_SESSION['user_name'] = $hoTen;
            flash('success', 'Cập nhật thông tin tài khoản thành công!');
        } catch (Exception $e) {
            flash('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }

        redirect(url('profile?tab=account'));
    }

    public function changePassword(): void
    {
        AuthMiddleware::checkCustomer();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('profile?tab=account'));
            return;
        }

        $userId = (int)$_SESSION['user_id'];

        $matKhauCu = $_POST['mat_khau_cu'] ?? '';
        $matKhauMoi = $_POST['mat_khau_moi'] ?? '';
        $xacNhan = $_POST['xac_nhan_mat_khau'] ?? '';

        if (empty($matKhauCu) || empty($matKhauMoi)) {
            flash('error', 'Vui lòng điền đầy đủ mật khẩu hiện tại và mật khẩu mới.');
            redirect(url('profile?tab=account'));
            return;
        }

        if (strlen($matKhauMoi) < 6) {
            flash('error', 'Mật khẩu mới phải có độ dài từ 6 ký tự trở lên.');
            redirect(url('profile?tab=account'));
            return;
        }

        if ($matKhauMoi !== $xacNhan) {
            flash('error', 'Xác nhận mật khẩu mới không trùng khớp.');
            redirect(url('profile?tab=account'));
            return;
        }

        $currentHash = $this->nguoiDungModel->layMatKhau($userId);
        if (!$currentHash || !password_verify($matKhauCu, $currentHash)) {
            flash('error', 'Mật khẩu hiện tại không chính xác.');
            redirect(url('profile?tab=account'));
            return;
        }

        try {
            $this->nguoiDungModel->doiMatKhau($userId, $matKhauMoi);
            flash('success', 'Đổi mật khẩu thành công!');
        } catch (Exception $e) {
            flash('error', 'Lỗi khi đổi mật khẩu: ' . $e->getMessage());
        }

        redirect(url('profile?tab=account'));
    }
}


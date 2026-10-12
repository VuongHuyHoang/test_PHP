<?php

namespace App\Controllers;

use App\Models\NguoiDung;

class AuthController
{
    private NguoiDung $userModel;

    public function __construct()
    {
        $this->userModel = new NguoiDung();
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect(url(''));
        }
        view('client.auth.login', ['title' => 'Đăng nhập tài khoản']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $matKhau = $_POST['mat_khau'] ?? '';

        if (empty($email) || empty($matKhau)) {
            flash('error', 'Vui lòng nhập đầy đủ Email và Mật khẩu.');
            redirect(url('login'));
        }

        $user = $this->userModel->timTheoEmail($email);

        if (!$user || !password_verify($matKhau, $user['mat_khau'])) {
            flash('error', 'Email hoặc mật khẩu không chính xác.');
            redirect(url('login'));
        }

        if ($user['trang_thai'] === 'bi_khoa') {
            flash('error', 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ ban quản trị.');
            redirect(url('login'));
        }

        // Lưu thông tin phiên đăng nhập
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['ho_ten'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['vai_tro'];

        flash('success', 'Đăng nhập thành công! Chào mừng ' . htmlspecialchars($user['ho_ten']) . '.');

        $redirectUrl = $_SESSION['redirect_after_login'] ?? url('');
        unset($_SESSION['redirect_after_login']);
        redirect($redirectUrl);
    }

    public function showRegister(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect(url(''));
        }
        view('client.auth.register', ['title' => 'Đăng ký tài khoản mới']);
    }

    public function register(): void
    {
        $hoTen = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sdt = trim($_POST['so_dien_thoai'] ?? '');
        $matKhau = $_POST['mat_khau'] ?? '';
        $xacNhanMatKhau = $_POST['xac_nhan_mat_khau'] ?? '';

        if (empty($hoTen) || empty($email) || empty($matKhau)) {
            flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            redirect(url('register'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Địa chỉ email không đúng định dạng.');
            redirect(url('register'));
        }

        if (strlen($matKhau) < 6) {
            flash('error', 'Mật khẩu phải có độ dài ít nhất 6 ký tự.');
            redirect(url('register'));
        }

        if ($matKhau !== $xacNhanMatKhau) {
            flash('error', 'Xác nhận mật khẩu không khớp.');
            redirect(url('register'));
        }

        if ($this->userModel->timTheoEmail($email)) {
            flash('error', 'Email này đã được sử dụng. Vui lòng chọn email khác hoặc đăng nhập.');
            redirect(url('register'));
        }

        $userId = $this->userModel->taoMoi($hoTen, $email, $matKhau, $sdt, 'khach_hang');

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $hoTen;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'khach_hang';

        flash('success', 'Đăng ký tài khoản thành công!');
        redirect(url(''));
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role']);
        flash('success', 'Đã đăng xuất tài khoản thành công.');
        redirect(url(''));
    }
}


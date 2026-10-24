<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                    <h3 class="fw-bold mt-2">Đăng Nhập Tài Khoản</h3>
                    <p class="text-muted small">Đăng nhập để đặt vé trực tuyến và quản lý đơn hàng</p>
                </div>

                <form action="<?= url('login') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="vidu@gmail.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="mat_khau" class="form-control border-start-0" placeholder="Nhập mật khẩu" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-pill mb-3">
                        Đăng Nhập
                    </button>
                </form>

                <div class="text-center mt-3 pt-3 border-top">
                    <p class="small text-muted mb-0">
                        Chưa có tài khoản? <a href="<?= url('register') ?>" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
                    </p>
                    <div class="mt-3 p-2 bg-light rounded text-start small">
                        <strong>Tài khoản thử nghiệm:</strong><br>
                        - Admin: <code>admin@nhatrangtourism.vn</code> / <code>Admin@123</code><br>
                        - Khách hàng: <code>nguyenan@gmail.com</code> / <code>Khach@123</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>


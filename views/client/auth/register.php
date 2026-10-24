<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill fs-1 text-success"></i>
                    <h3 class="fw-bold mt-2">Đăng Ký Tài Khoản</h3>
                    <p class="text-muted small">Tạo tài khoản để trải nghiệm đặt vé và khám phá Nha Trang</p>
                </div>

                <form action="<?= url('register') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="nguyenvana@gmail.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Số điện thoại</label>
                        <input type="tel" name="so_dien_thoai" class="form-control" placeholder="0901234567">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="mat_khau" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="xac_nhan_mat_khau" class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-pill mb-3">
                        Tạo Tài Khoản
                    </button>
                </form>

                <div class="text-center mt-2 pt-3 border-top">
                    <p class="small text-muted mb-0">
                        Đã có tài khoản? <a href="<?= url('login') ?>" class="text-primary fw-bold text-decoration-none">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>


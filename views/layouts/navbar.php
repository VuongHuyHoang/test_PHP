<nav class="navbar navbar-expand-lg site-navbar sticky-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 brand-title text-decoration-none" href="<?= url('') ?>">
            <i class="bi bi-compass text-info fs-4"></i>
            <span>Nha Trang Discovery</span>
        </a>
        
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="<?= url('') ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="<?= url('locations') ?>">Bản đồ & Địa điểm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="<?= url('locations?loai=am_thuc') ?>">Ẩm thực đặc sản</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="<?= url('locations?loai=du_lich') ?>">Du lịch & Vé tham quan</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1 btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><h6 class="dropdown-header"><?= htmlspecialchars($_SESSION['user_email']) ?></h6></li>
                            <li><a class="dropdown-item fw-semibold" href="<?= url('profile') ?>"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Vé của tôi & Hồ sơ</a></li>
                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger fw-semibold" href="<?= url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Trang quản trị Admin</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/orders') ?>"><i class="bi bi-qr-code-scan me-2"></i>Tra cứu & Soát vé</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">Đăng nhập</a>
                    <a href="<?= url('register') ?>" class="btn btn-coral btn-sm rounded-pill px-3">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Thông báo Flash Messages -->
<?php if ($success = flash('success')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
<?php endif; ?>

<?php if ($error = flash('error')): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
<?php endif; ?>

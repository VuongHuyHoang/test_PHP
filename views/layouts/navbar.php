<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= url('') ?>">
            <i class="bi bi-water fs-3 text-warning"></i>
            <span>NhaTrang<span class="text-warning">Discovery</span></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="<?= url('') ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="<?= url('locations') ?>">Tất cả địa điểm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="<?= url('locations?loai=am_thuc') ?>">Ẩm thực đặc sản</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="<?= url('locations?loai=du_lich') ?>">Du lịch & Đặt vé</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="fw-semibold"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><h6 class="dropdown-header">Tài khoản: <?= htmlspecialchars($_SESSION['user_email']) ?></h6></li>
                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <li><a class="dropdown-item text-danger fw-semibold" href="<?= url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/orders') ?>"><i class="bi bi-qr-code-scan me-2"></i>Tra cứu & Soát vé</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">Đăng nhập</a>
                    <a href="<?= url('register') ?>" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Thông báo Flash Messages -->
<?php if ($success = flash('success')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<?php if ($error = flash('error')): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

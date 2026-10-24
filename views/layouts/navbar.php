<nav class="navbar navbar-expand-lg site-navbar sticky-top py-2">
    <div class="container-fluid px-lg-4 px-xl-5">
        <a class="navbar-brand d-flex align-items-center gap-2 brand-title text-decoration-none text-nowrap" href="<?= url('') ?>">
            <i class="bi bi-compass text-info fs-4"></i>
            <span>Nha Trang Staycation</span>
        </a>

        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1 flex-nowrap align-items-center">
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap" href="<?= url('') ?>">Trang chủ</a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'gioi-thieu') || str_contains($_SERVER['REQUEST_URI'] ?? '', 'about')) ? 'active' : '' ?>" href="<?= url('gioi-thieu') ?>">
                        Giới thiệu
                    </a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap" href="<?= url('locations') ?>">Bản đồ & Địa điểm</a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap" href="<?= url('locations?loai=am_thuc') ?>">Ẩm thực đặc sản</a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap" href="<?= url('locations?loai=du_lich') ?>">Du lịch & Vé tham quan</a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'huong-dan')) ? 'active' : '' ?>" href="<?= url('huong-dan') ?>">
                        <i class="bi bi-journal-bookmark me-1 text-info"></i>Hướng dẫn
                    </a>
                </li>
                <li class="nav-item flex-shrink-0">
                    <a class="nav-link nav-link-custom text-nowrap <?= (str_contains($_SERVER['REQUEST_URI'] ?? '', 'ai-tour')) ? 'active' : '' ?>" href="<?= url('ai-tour') ?>">
                        <i class="bi bi-stars text-warning me-1"></i>Trợ lý AI Tour
                        <span class="badge bg-warning text-dark rounded-pill ms-1 fw-bold" style="font-size: 9px; padding: 2px 6px;">AI</span>
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0 ms-auto">
                <!-- Thanh Tìm Kiếm Nhanh Gợi Ý Tức Thì (Live Auto-Suggest Search) -->
                <div class="live-search-wrapper position-relative me-lg-2 d-none d-md-block" style="width: 280px; min-width: 240px; max-width: 360px;">
                    <div class="input-group input-group-sm live-search-input-group shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-white search-glass-icon"><i class="bi bi-search text-white"></i></span>
                        <input type="text" class="form-control bg-transparent border-0 text-white live-search-input placeholder-white" placeholder="Tìm kiếm nhanh..." style="border-radius: 0 50rem 50rem 0;" autocomplete="off">
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown flex-shrink-0">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1 btn-sm text-nowrap" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span class="text-truncate d-inline-block" style="max-width: 130px;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <h6 class="dropdown-header d-flex align-items-center justify-content-between">
                                    <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
                                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                        <span class="badge bg-danger rounded-pill px-2 py-0" style="font-size: 10px;">Admin</span>
                                    <?php endif; ?>
                                </h6>
                            </li>

                            <?php if (($_SESSION['user_role'] ?? '') !== 'admin'): ?>
                                <!-- Menu dành riêng cho Khách hàng -->
                                <li><a class="dropdown-item fw-semibold" href="<?= url('profile') ?>"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Vé của tôi & Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="<?= url('profile?tab=reviews') ?>"><i class="bi bi-star-fill me-2 text-warning"></i>Địa điểm đã đánh giá</a></li>
                                <li><a class="dropdown-item" href="<?= url('profile?tab=favorites') ?>"><i class="bi bi-heart-fill me-2 text-danger"></i>Địa điểm yêu thích</a></li>
                            <?php else: ?>
                                <!-- Menu dành riêng cho Quản trị viên (Ẩn Vé của tôi & Hồ sơ) -->
                                <li><a class="dropdown-item text-danger fw-semibold" href="<?= url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Trang quản trị Admin</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/categories') ?>"><i class="bi bi-tags-fill me-2 text-primary"></i>Quản lý danh mục</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/reports') ?>"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Báo cáo thống kê</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/orders') ?>"><i class="bi bi-qr-code-scan me-2 text-info"></i>Tra cứu & Soát vé</a></li>
                            <?php endif; ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
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
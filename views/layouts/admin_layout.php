<?php
/**
 * @var string|null $title
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Quản Trị Hệ Thống - Nha Trang Tourism') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --font-family-sans: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --admin-sidebar-bg: #0b1329;
            --admin-sidebar-hover: #1e293b;
            --admin-primary: #0284c7;
        }
        body {
            font-family: var(--font-family-sans);
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0b1329 0%, #0f172a 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 18px;
            font-weight: 500;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateX(3px);
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.15rem;
        }
        .card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 8px 24px -4px rgba(11, 37, 69, 0.04);
            border-radius: 16px;
        }
        .table > :not(caption) > * > * {
            padding: 14px 16px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <div class="p-3 text-white border-bottom border-secondary d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
                <span class="fs-5 fw-bold">Admin Portal</span>
            </div>
            <?php $currentUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/dashboard') ? 'active' : '' ?>" href="<?= url('admin/dashboard') ?>">
                        <i class="bi bi-speedometer2"></i>Tổng Quan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/locations') ? 'active' : '' ?>" href="<?= url('admin/locations') ?>">
                        <i class="bi bi-geo-alt-fill"></i>Quản Lý Địa Điểm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/categories') ? 'active' : '' ?>" href="<?= url('admin/categories') ?>">
                        <i class="bi bi-tags-fill"></i>Quản Lý Danh Mục
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/tickets') ? 'active' : '' ?>" href="<?= url('admin/tickets') ?>">
                        <i class="bi bi-ticket-detailed-fill"></i>Gói Vé & Tồn Kho
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/orders') ? 'active' : '' ?>" href="<?= url('admin/orders') ?>">
                        <i class="bi bi-qr-code-scan"></i>Tra Cứu & Soát Vé
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/reviews') ? 'active' : '' ?>" href="<?= url('admin/reviews') ?>">
                        <i class="bi bi-star-fill text-warning"></i>Quản Lý Đánh Giá
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/reports') ? 'active' : '' ?>" href="<?= url('admin/reports') ?>">
                        <i class="bi bi-bar-chart-line-fill text-success"></i>Báo Cáo Thống Kê
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($currentUri, 'admin/guide') ? 'active' : '' ?>" href="<?= url('admin/guide') ?>">
                        <i class="bi bi-journal-bookmark-fill text-warning"></i>Hướng Dẫn Sử Dụng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('locations') ?>" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i>Xem Website
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="<?= url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>Đăng Xuất
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Offcanvas Mobile Drawer cho màn hình nhỏ (< 768px) -->
        <div class="offcanvas offcanvas-start bg-dark text-white d-md-none" tabindex="-1" id="adminMobileSidebar" aria-labelledby="adminMobileSidebarLabel" style="width: 280px; background-color: #0f172a !important;">
            <div class="offcanvas-header border-bottom border-secondary">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
                    <h5 class="offcanvas-title fw-bold text-white mb-0" id="adminMobileSidebarLabel">Admin Portal</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/dashboard') ? 'active' : '' ?>" href="<?= url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i>Tổng Quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/locations') ? 'active' : '' ?>" href="<?= url('admin/locations') ?>">
                            <i class="bi bi-geo-alt-fill"></i>Quản Lý Địa Điểm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/categories') ? 'active' : '' ?>" href="<?= url('admin/categories') ?>">
                            <i class="bi bi-tags-fill"></i>Quản Lý Danh Mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/tickets') ? 'active' : '' ?>" href="<?= url('admin/tickets') ?>">
                            <i class="bi bi-ticket-detailed-fill"></i>Gói Vé & Tồn Kho
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/orders') ? 'active' : '' ?>" href="<?= url('admin/orders') ?>">
                            <i class="bi bi-qr-code-scan"></i>Tra Cứu & Soát Vé
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/reviews') ? 'active' : '' ?>" href="<?= url('admin/reviews') ?>">
                            <i class="bi bi-star-fill text-warning"></i>Quản Lý Đánh Giá
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/reports') ? 'active' : '' ?>" href="<?= url('admin/reports') ?>">
                            <i class="bi bi-bar-chart-line-fill text-success"></i>Báo Cáo Thống Kê
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains($currentUri, 'admin/guide') ? 'active' : '' ?>" href="<?= url('admin/guide') ?>">
                            <i class="bi bi-journal-bookmark-fill text-warning"></i>Hướng Dẫn Sử Dụng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-info" href="<?= url('locations') ?>" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>Xem Website
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="<?= url('logout') ?>">
                            <i class="bi bi-box-arrow-right"></i>Đăng Xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Navbar Admin Top -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-dark d-md-none rounded-3 px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar" title="Mở menu quản trị">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h2 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($title ?? 'Dashboard') ?></h2>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-sm-inline">Xin chào, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
                    <a href="<?= url('logout') ?>" class="btn btn-outline-danger btn-sm rounded-pill">Đăng xuất</a>
                </div>
            </div>

            <!-- Flash messages -->
            <?php if ($success = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>


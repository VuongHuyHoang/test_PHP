<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Quản Trị Hệ Thống - Nha Trang Tourism') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .sidebar { min-height: 100vh; background-color: #0f172a; color: #94a3b8; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; font-weight: 500; border-radius: 8px; margin: 4px 12px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #1e293b; color: #f8fafc; }
        .sidebar .nav-link i { margin-right: 10px; }
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

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Navbar Admin Top -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h2 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($title ?? 'Dashboard') ?></h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted">Xin chào, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
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


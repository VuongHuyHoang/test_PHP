<?php require_once __DIR__ . '/header.php'; ?>
<?php require_once __DIR__ . '/navbar.php'; ?>

<main class="container my-5 py-5 text-center flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="bi bi-geo-alt-fill text-danger display-1 mb-3"></i>
            <h1 class="display-4 fw-bold">404</h1>
            <h3 class="mb-3 text-secondary">Trang bạn tìm kiếm không tồn tại!</h3>
            <p class="text-muted mb-4">Địa điểm hoặc trang liên kết có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng.</p>
            <a href="<?= url('') ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-house-door me-2"></i>Về Trang Chủ
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>


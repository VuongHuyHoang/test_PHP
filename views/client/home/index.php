<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<!-- Hero Banner -->
<section class="hero-banner text-center">
    <div class="container py-4">
        <h1 class="hero-title mb-3">Khám Phá Thiên Đường Biển Nha Trang</h1>
        <p class="lead mb-4 mx-auto text-white-50" style="max-width: 720px;">
            Tra cứu định vị tọa độ các quán ăn đặc sản trứ danh, thắng cảnh nổi tiếng và đặt vé vui chơi, tour đảo trực tuyến nhanh chóng với E-Voucher.
        </p>
        
        <!-- Thanh tìm kiếm nhanh -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="<?= url('locations') ?>" method="GET" class="card p-2 shadow-lg border-0 rounded-pill">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-3">
                            <i class="bi bi-search text-muted fs-5"></i>
                        </span>
                        <input type="text" name="tu_khoa" class="form-control border-0 shadow-none" placeholder="Tìm kiếm hải sản, nem nướng, VinWonders, Tháp Bà...">
                        <button class="btn btn-warning rounded-pill px-4 fw-bold" type="submit">
                            Tìm Kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Bản Đồ Số Nha Trang Section -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-2 rounded-pill">Bản đồ số tương tác</span>
            <h2 class="fw-bold mt-2 mb-0">Tọa Độ Ẩm Thực & Du Lịch</h2>
        </div>
        <a href="<?= url('locations') ?>" class="btn btn-outline-primary rounded-pill btn-sm">Xem tất cả địa điểm &rarr;</a>
    </div>

    <div class="card shadow-sm border-0 p-2">
        <div id="map"></div>
    </div>
</section>

<!-- Danh Mục Khám Phá -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold text-uppercase px-3 py-2 rounded-pill">Danh mục trải nghiệm</span>
            <h2 class="fw-bold mt-2">Bạn Muốn Trải Nghiệm Gì Hôm Nay?</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($danhMucList as $dm): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?= url('locations?danh_muc_id=' . $dm['id']) ?>" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm p-4 text-center hover-shadow transition">
                            <div class="mb-3">
                                <i class="bi <?= htmlspecialchars($dm['bieu_tuong'] ?? 'bi-geo-alt') ?> fs-1 <?= $dm['loai_danh_muc'] === 'am_thuc' ? 'text-warning' : 'text-primary' ?>"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($dm['ten_danh_muc']) ?></h5>
                            <span class="badge <?= $dm['loai_danh_muc'] === 'am_thuc' ? 'badge-am-thuc' : 'badge-du-lich' ?> mx-auto">
                                <?= $dm['loai_danh_muc'] === 'am_thuc' ? 'Ẩm Thực' : 'Du Lịch' ?>
                            </span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Địa Điểm Nổi Bật -->
<section class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-success-subtle text-success fw-bold text-uppercase px-3 py-2 rounded-pill">Điểm đến hàng đầu</span>
        <h2 class="fw-bold mt-2">Địa Điểm Được Yêu Thích Nhất</h2>
    </div>

    <div class="row g-4">
        <?php foreach ($diaDiemNoiBat as $dd): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 location-card shadow-sm">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($dd['anh_dai_dien']) ?>" class="card-img-top" alt="<?= htmlspecialchars($dd['ten_dia_diem']) ?>">
                        <span class="position-absolute top-0 start-0 m-2 badge <?= $dd['loai_danh_muc'] === 'am_thuc' ? 'badge-am-thuc' : 'badge-du-lich' ?>">
                            <?= htmlspecialchars($dd['ten_danh_muc']) ?>
                        </span>
                        <?php if ($dd['co_ban_ve']): ?>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                                <i class="bi bi-ticket-perforated me-1"></i>Có vé online
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-truncate mb-1" title="<?= htmlspecialchars($dd['ten_dia_diem']) ?>">
                            <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($dd['ten_dia_diem']) ?>
                            </a>
                        </h5>
                        <p class="text-muted small text-truncate mb-2">
                            <i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                        </p>
                        
                        <div class="d-flex align-items-center justify-content-between mb-3 mt-auto">
                            <span class="text-primary fw-bold small">
                                <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_cao_nhat']) ?>
                            </span>
                            <div class="small">
                                <?= \App\Helpers\FormatHelper::saoHtml((float)$dd['diem_danh_gia']) ?>
                            </div>
                        </div>

                        <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                            Khám Phá Chi Tiết
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<!-- Hero Section mang âm hưởng vịnh biển Nha Trang -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-heading">Khám phá ẩm thực & danh thắng phố biển Nha Trang</h1>
        <p class="hero-subtitle">
            Bản đồ định vị quán ăn đặc sản truyền thống, bãi lặn san hô và dịch vụ đặt vé tour vịnh đảo nhanh chóng với E-Voucher.
        </p>
        
        <!-- Thanh tìm kiếm nhanh -->
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <form action="<?= url('locations') ?>" method="GET" class="bg-white p-2 shadow-sm rounded-pill d-flex align-items-center">
                    <span class="ps-3 pe-2 text-muted"><i class="bi bi-search fs-5"></i></span>
                    <input type="text" name="tu_khoa" class="form-control border-0 shadow-none ps-0" placeholder="Tìm hải sản, bánh căn, nem nướng, VinWonders, Hòn Mun...">
                    <button class="btn btn-coral rounded-pill px-4 ms-2" type="submit">
                        Tìm kiếm
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Bản Đồ Tương Tác Nha Trang -->
<section class="container my-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">Tọa độ ẩm thực & điểm đến</h2>
            <p class="text-muted small mb-0">Bấm vào từng ghim trên bản đồ để xem chi tiết quán ăn và khoảng giá</p>
        </div>
        <div>
            <a href="<?= url('locations') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                Mở rộng bản đồ toàn màn hình
            </a>
        </div>
    </div>

    <div class="map-frame">
        <div id="map"></div>
    </div>
</section>

<!-- Danh Mục Trải Nghiệm -->
<section class="py-5" style="background-color: #f1ede4;">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="h4 fw-bold text-dark mb-1">Danh mục trải nghiệm đặc trưng</h2>
            <p class="text-muted small">Khám phá theo phong vị ẩm thực miền biển hoặc tour du lịch danh lam</p>
        </div>

        <div class="row g-3">
            <?php foreach ($danhMucList as $dm): ?>
                <?php 
                    $isFood = $dm['loai_danh_muc'] === 'am_thuc';
                    $badgeTag = $isFood ? 'badge-tag-food' : 'badge-tag-travel';
                    $loaiText = $isFood ? 'Ẩm thực' : 'Du lịch';
                ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?= url('locations?danh_muc_id=' . $dm['id']) ?>" class="text-decoration-none">
                        <div class="bg-white p-4 rounded-3 border h-100 d-flex flex-column justify-content-between transition">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <span class="badge-tag <?= $badgeTag ?>"><?= $loaiText ?></span>
                                <i class="bi <?= htmlspecialchars($dm['bieu_tuong'] ?? 'bi-geo-alt') ?> fs-4 <?= $isFood ? 'text-danger' : 'text-primary' ?>"></i>
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-0"><?= htmlspecialchars($dm['ten_danh_muc']) ?></h3>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Địa Điểm Nổi Bật Được Yêu Thích -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">Địa điểm tiêu biểu được quan tâm</h2>
            <p class="text-muted small mb-0">Dựa trên lượt quan tâm và đánh giá của du khách thực tế</p>
        </div>
        <a href="<?= url('locations') ?>" class="text-muted small text-decoration-none fw-semibold">
            Xem toàn bộ địa điểm
        </a>
    </div>

    <div class="row g-4">
        <?php foreach ($diaDiemNoiBat as $dd): ?>
            <?php 
                $isFood = $dd['loai_danh_muc'] === 'am_thuc';
                $badgeTag = $isFood ? 'badge-tag-food' : 'badge-tag-travel';
                $loaiLabel = $isFood ? 'Ẩm thực' : 'Du lịch';
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="location-item-card">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($dd['anh_dai_dien']) ?>" class="location-thumb" alt="<?= htmlspecialchars($dd['ten_dia_diem']) ?>" loading="lazy">
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge-tag <?= $badgeTag ?>"><?= $loaiLabel ?></span>
                        </div>
                        <?php if ($dd['co_ban_ve']): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success-subtle text-success small fw-semibold">
                                    <i class="bi bi-ticket-perforated me-1"></i>Có vé online
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small"><?= htmlspecialchars($dd['ten_danh_muc']) ?></span>
                            <div class="small">
                                <?= \App\Helpers\FormatHelper::saoHtml((float)$dd['diem_danh_gia']) ?>
                            </div>
                        </div>
                        
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 1rem;">
                            <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($dd['ten_dia_diem']) ?>
                            </a>
                        </h3>
                        
                        <p class="text-muted small text-truncate mb-3">
                            <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                            <span class="fw-bold small text-dark">
                                <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_cao_nhat']) ?>
                            </span>
                            <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                Chi tiết
                            </a>
                        </div>
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

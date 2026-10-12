<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-4 flex-grow-1">
    <!-- Header trang khám phá -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Bản đồ Ẩm thực & Danh thắng Nha Trang</h1>
            <p class="text-muted small mb-0">Định vị các điểm ăn uống đặc sản, danh thắng và tour biển đảo trên bản đồ số tương tác</p>
        </div>
        <div>
            <button type="button" id="btn-locate-me" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-crosshair me-1"></i>Định vị vị trí của tôi
            </button>
        </div>
    </div>

    <!-- Khung Bản Đồ Leaflet -->
    <div class="map-frame mb-4">
        <div id="map"></div>
    </div>

    <!-- Bộ Lọc Trực Tiếp (Live Filter Panel) -->
    <div class="filter-panel mb-4 shadow-sm">
        <form id="live-filter-form" class="row g-3 align-items-center">
            <!-- Tìm kiếm từ khóa -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-semibold text-muted mb-1">Tìm theo tên hoặc đường</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="tu_khoa" class="form-control border-start-0" placeholder="Nem nướng, Bờ Kè, Tháp Bà..." value="<?= htmlspecialchars($filters['tu_khoa'] ?? '') ?>">
                </div>
            </div>

            <!-- Phân loại hình -->
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold text-muted mb-1">Phân loại</label>
                <select name="loai" class="form-select form-select-sm">
                    <option value="">Tất cả loại hình</option>
                    <option value="am_thuc" <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực & Quán ăn</option>
                    <option value="du_lich" <?= ($filters['loai'] ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch & Điểm đến</option>
                </select>
            </div>

            <!-- Danh mục cụ thể -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-semibold text-muted mb-1">Danh mục trải nghiệm</label>
                <select name="danh_muc_id" class="form-select form-select-sm">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($danhMucList as $dm): ?>
                        <option value="<?= $dm['id'] ?>" <?= ($filters['danh_muc_id'] ?? '') == $dm['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Khung giá -->
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold text-muted mb-1">Khoảng giá</label>
                <select name="muc_gia" class="form-select form-select-sm">
                    <option value="">Tất cả mức giá</option>
                    <option value="duoi_50k" <?= ($filters['muc_gia'] ?? '') === 'duoi_50k' ? 'selected' : '' ?>>Dưới 50.000 đ</option>
                    <option value="50k_200k" <?= ($filters['muc_gia'] ?? '') === '50k_200k' ? 'selected' : '' ?>>50.000 đ - 200.000 đ</option>
                    <option value="tren_200k" <?= ($filters['muc_gia'] ?? '') === 'tren_200k' ? 'selected' : '' ?>>Trên 200.000 đ</option>
                </select>
            </div>

            <!-- Cờ có vé online -->
            <div class="col-lg-2 col-md-12 d-flex align-items-center mt-lg-4 pt-lg-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="co_ban_ve" value="1" id="checkCoBanVe" <?= (!empty($filters['co_ban_ve'])) ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-medium" for="checkCoBanVe">
                        Có bán vé online
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Thanh tiêu đề kết quả -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="h5 fw-bold text-dark mb-0">
            Danh sách địa điểm (<span id="locations-count"><?= count($diaDiemList) ?></span>)
        </h4>
        <span class="text-muted small">Bấm vào thẻ để di chuyển nhanh trên bản đồ</span>
    </div>

    <!-- Danh sách thẻ địa điểm (được render động và đồng bộ với bản đồ) -->
    <div class="row g-4" id="locations-container">
        <?php foreach ($diaDiemList as $dd): ?>
            <?php 
                $isFood = $dd['loai_danh_muc'] === 'am_thuc';
                $badgeTag = $isFood ? 'badge-tag-food' : 'badge-tag-travel';
                $loaiLabel = $isFood ? 'Ẩm thực' : 'Du lịch';
            ?>
            <div class="col-lg-4 col-md-6" id="card-location-<?= $dd['id'] ?>">
                <div class="location-item-card" onclick="focusOnMap(<?= $dd['id'] ?>, <?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>)" style="cursor: pointer;">
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
                        
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">
                            <?= htmlspecialchars($dd['ten_dia_diem']) ?>
                        </h5>
                        
                        <p class="text-muted small text-truncate mb-3">
                            <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                            <span class="fw-bold small text-dark">
                                <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_cao_nhat']) ?>
                            </span>
                            <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="event.stopPropagation();">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

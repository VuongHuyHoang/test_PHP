<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-4 flex-grow-1">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <!-- Form Bộ Lọc -->
            <div class="card shadow-sm border-0 p-3">
                <form action="<?= url('locations') ?>" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Loại hình</label>
                        <select name="loai" class="form-select">
                            <option value="">-- Tất cả loại hình --</option>
                            <option value="am_thuc" <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực & Quán ăn</option>
                            <option value="du_lich" <?= ($filters['loai'] ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch & Thắng cảnh</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Danh mục cụ thể</label>
                        <select name="danh_muc_id" class="form-select">
                            <option value="">-- Tất cả danh mục --</option>
                            <?php foreach ($danhMucList as $dm): ?>
                                <option value="<?= $dm['id'] ?>" <?= ($filters['danh_muc_id'] ?? '') == $dm['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Từ khóa tìm kiếm</label>
                        <input type="text" name="tu_khoa" class="form-control" placeholder="Tên quán, địa chỉ..." value="<?= htmlspecialchars($filters['tu_khoa'] ?? '') ?>">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-funnel me-1"></i>Lọc Kết Quả
                        </button>
                        <a href="<?= url('locations') ?>" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bản đồ số Leaflet -->
        <div class="col-12">
            <div class="card shadow-sm border-0 p-2">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- Kết quả tìm kiếm -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Địa Điểm Khám Phá (<?= count($diaDiemList) ?> kết quả)</h4>
    </div>

    <div class="row g-4">
        <?php if (empty($diaDiemList)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-3 text-muted mb-3"></i>
                <h5 class="text-secondary">Không tìm thấy địa điểm phù hợp với bộ lọc</h5>
                <p class="text-muted">Vui lòng thử lại với từ khóa hoặc tùy chọn danh mục khác.</p>
            </div>
        <?php else: ?>
            <?php foreach ($diaDiemList as $dd): ?>
                <div class="col-lg-4 col-md-6">
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
                            <h5 class="card-title fw-bold mb-1">
                                <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($dd['ten_dia_diem']) ?>
                                </a>
                            </h5>
                            <p class="text-muted small text-truncate mb-2">
                                <i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                            </p>
                            
                            <p class="text-secondary small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($dd['mo_ta_ngan'] ?? '') ?>
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
                                Xem Thông Tin & Bản Đồ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

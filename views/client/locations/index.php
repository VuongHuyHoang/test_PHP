<?php
/**
 * @var string $title
 * @var array $diaDiemList
 * @var array $danhMucList
 * @var array $filters
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
require_once dirname(__DIR__, 2) . '/layouts/navbar.php';
?>

<!-- ========================================================
     1. SUB-HEADER & QUICK CHIPS / VIEW MODE SWITCHER
     ======================================================== -->
<section class="locations-explorer-header">
    <div class="container-fluid px-lg-4 px-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">Bản đồ & Địa điểm</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-0">Bản Đồ Ẩm Thực & Du Lịch Nha Trang</h1>
                <p class="text-muted small mb-0">Khám phá hơn 30 tọa độ đặc sản biển, danh thắng vịnh đảo và đặt vé E-Voucher tiện lợi</p>
            </div>

            <!-- Điều khiển Chế độ Xem (Split / Grid / Map) -->
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group btn-group-sm view-mode-btn-group shadow-sm rounded-pill overflow-hidden bg-white p-1 border" role="group">
                    <button type="button" class="btn btn-primary rounded-pill active" id="btn-view-split" title="Xem danh sách & bản đồ song song">
                        <i class="bi bi-layout-split me-1"></i>Song song
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill border-0" id="btn-view-grid" title="Xem danh sách toàn màn hình">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Chỉ danh sách
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill border-0" id="btn-view-map" title="Xem bản đồ toàn màn hình">
                        <i class="bi bi-map me-1"></i>Chỉ bản đồ
                    </button>
                </div>

                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilterCard" aria-expanded="false">
                    <i class="bi bi-sliders me-1"></i>Bộ lọc nâng cao
                </button>
            </div>
        </div>

        <!-- Thanh Quick Filter Chips 1 Chạm -->
        <div class="quick-chips-wrapper">
            <button type="button" class="chip-btn <?= empty($filters['loai']) && empty($filters['co_ban_ve']) ? 'active' : '' ?>" data-filter-chip="all">
                <i class="bi bi-grid me-1"></i>Tất cả (30)
            </button>
            <button type="button" class="chip-btn <?= ($filters['loai'] ?? '') === 'du_lich' ? 'active' : '' ?>" data-filter-chip="du_lich">
                <i class="bi bi-compass me-1 text-info"></i>Du lịch & Danh thắng
            </button>
            <button type="button" class="chip-btn <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'active' : '' ?>" data-filter-chip="am_thuc">
                <i class="bi bi-egg-fried me-1 text-danger"></i>Ẩm thực đặc sản
            </button>
            <button type="button" class="chip-btn <?= !empty($filters['co_ban_ve']) ? 'active' : '' ?>" data-filter-chip="co_ban_ve">
                <i class="bi bi-ticket-perforated me-1 text-success"></i>Có vé online E-Voucher
            </button>
            <button type="button" class="chip-btn <?= !empty($filters['dang_mo_cua']) ? 'active' : '' ?>" data-filter-chip="dang_mo_cua">
                <i class="bi bi-clock-fill me-1 text-warning"></i>Đang mở cửa
            </button>
            <button type="button" class="chip-btn" id="btn-locate-me">
                <i class="bi bi-crosshair me-1 text-primary"></i>Gần vị trí của tôi
            </button>
        </div>

        <!-- Thanh Chọn Bán Kính Định Vị GPS Quanh Người Dùng (Radius Filter) -->
        <div class="radius-filter-wrapper d-none align-items-center gap-2 mt-2 pt-2 border-top flex-wrap bg-light p-2 rounded-3" id="radiusFilterBar">
            <span class="small fw-bold text-primary me-1"><i class="bi bi-broadcast-pin me-1"></i>Bán kính quanh bạn:</span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 small radius-chip" data-radius="1000">1 km</button>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-0.5 small radius-chip active" data-radius="3000">3 km</button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 small radius-chip" data-radius="5000">5 km</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0.5 small radius-chip" data-radius="all">Tất cả</button>
            <span class="small text-muted ms-2" id="radiusResultText" style="font-size: 11.5px;"></span>
        </div>

        <!-- Khung Bộ Lọc Nâng Cao (Collapse) -->
        <div class="collapse <?= (!empty($filters['tu_khoa']) || !empty($filters['danh_muc_id']) || !empty($filters['muc_gia'])) ? 'show' : '' ?> mt-3" id="advancedFilterCard">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <form id="live-filter-form" action="<?= url('locations') ?>" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Từ khóa tìm kiếm</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="tu_khoa" class="form-control border-start-0" placeholder="Nem nướng, I-Resort, Tháp Bà..." value="<?= htmlspecialchars($filters['tu_khoa'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Phân loại</label>
                            <select name="loai" class="form-select form-select-sm">
                                <option value="">Tất cả loại hình</option>
                                <option value="am_thuc" <?= ($filters['loai'] ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực & Quán ăn</option>
                                <option value="du_lich" <?= ($filters['loai'] ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch & Danh thắng</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Danh mục trải nghiệm</label>
                            <select name="danh_muc_id" class="form-select form-select-sm">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($danhMucList as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" data-loai="<?= htmlspecialchars($dm['loai_danh_muc']) ?>" <?= ($filters['danh_muc_id'] ?? '') == $dm['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Khoảng giá</label>
                            <select name="muc_gia" class="form-select form-select-sm">
                                <option value="">Tất cả mức giá</option>
                                <option value="duoi_50k" <?= ($filters['muc_gia'] ?? '') === 'duoi_50k' ? 'selected' : '' ?>>Dưới 50.000 đ</option>
                                <option value="50k_200k" <?= ($filters['muc_gia'] ?? '') === '50k_200k' ? 'selected' : '' ?>>50.000 đ - 200.000 đ</option>
                                <option value="tren_200k" <?= ($filters['muc_gia'] ?? '') === 'tren_200k' ? 'selected' : '' ?>>Trên 200.000 đ</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="co_ban_ve" value="1" id="checkCoBanVe" <?= (!empty($filters['co_ban_ve'])) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-dark" for="checkCoBanVe">
                                    Có vé online
                                </label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="dang_mo_cua" value="1" id="checkDangMoCua" <?= (!empty($filters['dang_mo_cua'])) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-success" for="checkDangMoCua">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Đang mở cửa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-2 mt-3 pt-2 border-top">
                        <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                        </button>
                        <button type="submit" id="btn-submit-filter" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i>Lọc kết quả
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     2. BỐ CỤC CHIA ĐÔI HIỆN ĐẠI (SPLIT VIEW)
     ======================================================== -->
<main class="container-fluid px-lg-4 px-3 my-3 flex-grow-1">
    <div class="row g-4" id="main-explorer-row">
        <!-- Cột Danh Sách Địa Điểm (Bên Trái) -->
        <div class="col-lg-7 col-xl-7" id="col-locations-list">
            <!-- Header tóm tắt danh sách -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold fs-6" id="locations-count">
                        <?= count($diaDiemList) ?>
                    </span>
                    <span class="fw-bold text-dark">địa điểm phù hợp</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-sm-inline">Sắp xếp:</span>
                    <select id="sort-selector" class="form-select form-select-sm rounded-pill" style="width: auto;">
                        <option value="popular">Lượt xem & Phổ biến</option>
                        <option value="rating">Đánh giá sao cao nhất</option>
                        <option value="price_asc">Giá từ thấp đến cao</option>
                        <option value="price_desc">Giá từ cao đến thấp</option>
                    </select>
                </div>
            </div>

            <!-- Lưới Card Danh Sách Địa Điểm -->
            <div class="row g-3" id="locations-container">
                <?php foreach ($diaDiemList as $dd): ?>
                    <?php 
                        $isFood = $dd['loai_danh_muc'] === 'am_thuc';
                        $loaiLabel = $isFood ? 'Ẩm thực' : 'Du lịch';
                        $catBadgeClass = $isFood ? 'badge-tag-food text-coral' : 'badge-tag-travel text-teal';
                    ?>
                    <div class="col-md-6 col-sm-12" id="card-location-<?= $dd['id'] ?>">
                        <div class="location-card-modern" onclick="focusOnMap(<?= $dd['id'] ?>, <?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>)" style="cursor: pointer;">
                            <div class="location-card-img-wrap">
                                <img src="<?= htmlspecialchars(hinh_anh($dd['anh_dai_dien'])) ?>" class="location-card-img" alt="<?= htmlspecialchars($dd['ten_dia_diem']) ?>" loading="lazy">
                                <span class="card-category-glass <?= $catBadgeClass ?>">
                                    <?= $loaiLabel ?> &bull; <?= htmlspecialchars($dd['ten_danh_muc']) ?>
                                </span>
                                <?php if ($dd['co_ban_ve']): ?>
                                    <span class="card-ticket-glass">
                                        <i class="bi bi-ticket-perforated me-1"></i>Đặt vé E-Voucher
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="location-card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="small">
                                        <?= \App\Helpers\FormatHelper::saoHtml((float)$dd['diem_danh_gia']) ?>
                                        <span class="text-muted ms-1">(<?= (int)$dd['tong_danh_gia'] ?>)</span>
                                    </div>
                                    <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= (int)$dd['luot_xem'] ?></span>
                                </div>

                                <h3 class="location-card-title"><?= htmlspecialchars($dd['ten_dia_diem']) ?></h3>

                                <div class="location-card-address">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($dd['dia_chi']) ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                    <span class="location-card-price">
                                        <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$dd['gia_cao_nhat']) ?>
                                    </span>
                                    <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" onclick="focusOnMap(<?= $dd['id'] ?>, <?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>)" title="Xem vị trí trên bản đồ">
                                            <i class="bi bi-geo-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" onclick="window.startMapRouting(<?= $dd['vi_do'] ?>, <?= $dd['kinh_do'] ?>, '<?= addslashes(htmlspecialchars($dd['ten_dia_diem'])) ?>')" title="Chỉ đường OSRM nội bộ">
                                            <i class="bi bi-sign-turn-right-fill"></i>
                                        </button>
                                        <a href="<?= url('dia-diem/' . $dd['duong_dan']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">
                                            Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cột Bản Đồ Cố Định (Bên Phải - Sticky Map) -->
        <div class="col-lg-5 col-xl-5" id="col-locations-map">
            <div class="sticky-map-wrapper">
                <!-- Thanh công cụ nổi trên bản đồ -->
                <div class="map-floating-bar">
                    <span class="map-pill-badge" id="map-counter-badge">
                        <i class="bi bi-geo-fill text-warning me-1"></i><span id="map-pin-count"><?= count($diaDiemList) ?></span> ghim tọa độ
                    </span>
                    <button type="button" class="btn btn-light btn-sm rounded-pill shadow-sm fw-semibold" id="btn-fit-bounds" title="Thu phóng thấy tất cả điểm">
                        <i class="bi bi-arrows-fullscreen me-1"></i>Toàn cảnh
                    </button>
                </div>

                <!-- Canvas Bản đồ Leaflet -->
                <div id="map"></div>
            </div>
        </div>
    </div>
</main>

<!-- Nút Nổi Xem Bản Đồ Trên Màn Hình Di Động (Floating Action Button) -->
<button type="button" class="btn btn-primary rounded-pill px-4 py-2 mobile-map-toggle-fab" id="btn-mobile-map-toggle">
    <i class="bi bi-map-fill me-1"></i>Xem bản đồ
</button>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
    window.INITIAL_LOCATIONS = <?= json_encode($diaDiemList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

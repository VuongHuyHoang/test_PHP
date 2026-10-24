<?php
/**
 * @var string $title
 * @var array $diaDiem
 * @var array $goiVeList
 * @var array $danhGiaList
 * @var array $gallery
 * @var array $diaDiemLienQuan
 * @var bool $isFavorite
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';

$hasTickets = !empty($diaDiem['co_ban_ve']) && !empty($goiVeList);
$minTicketPrice = 0;
if ($hasTickets) {
    $minTicketPrice = min(array_map(fn($g) => (float)$g['gia_nguoi_lon'], $goiVeList));
} elseif (!empty($diaDiem['gia_thap_nhat']) && (float)$diaDiem['gia_thap_nhat'] > 0) {
    $minTicketPrice = (float)$diaDiem['gia_thap_nhat'];
}
?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-4 flex-grow-1">
    <!-- Breadcrumb & Action Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= url('locations') ?>" class="text-decoration-none text-muted">Địa điểm</a></li>
                <li class="breadcrumb-item"><a href="<?= url('locations?danh_muc_id=' . $diaDiem['danh_muc_id']) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($diaDiem['ten_danh_muc']) ?></a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <!-- Nút Yêu thích (Wishlist) -->
            <button type="button" 
                    class="btn btn-sm rounded-pill px-3 <?= $isFavorite ? 'btn-danger text-white' : 'btn-outline-danger' ?> d-flex align-items-center gap-1 shadow-sm" 
                    id="btn-toggle-favorite" 
                    data-id="<?= (int)$diaDiem['id'] ?>">
                <i class="bi <?= $isFavorite ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                <span id="fav-text" class="fw-semibold small"><?= $isFavorite ? 'Đã lưu yêu thích' : 'Lưu yêu thích' ?></span>
            </button>

            <!-- Nút Chia sẻ -->
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm" onclick="chiaSeDiaDiem()">
                <i class="bi bi-share"></i>
                <span class="small fw-semibold">Chia sẻ</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột Nội Dung Trải Nghiệm Chính (Col-8) -->
        <div class="col-lg-8">
            <!-- 1. BỘ SƯU TẬP ẢNH & THƯ VIỆN GALLERY -->
            <div class="bg-white border rounded-4 overflow-hidden shadow-sm mb-4">
                <div class="position-relative">
                    <img id="main-gallery-img" 
                         src="<?= htmlspecialchars(hinh_anh($diaDiem['anh_dai_dien'])) ?>" 
                         class="w-100" 
                         style="height: 440px; object-fit: cover; transition: opacity 0.3s ease;" 
                         alt="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">

                    <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.65), transparent);">
                        <span class="badge bg-dark bg-opacity-75 text-white small px-3 py-1 rounded-pill">
                            <i class="bi bi-camera me-1"></i><?= count($gallery) + 1 ?> hình ảnh thực tế
                        </span>
                    </div>
                </div>

                <!-- Dải Thumbnail hình ảnh bổ sung -->
                <?php if (!empty($gallery)): ?>
                    <div class="p-3 bg-light border-top d-flex gap-2 overflow-x-auto" style="scrollbar-width: thin;">
                        <!-- Thumbnail ảnh đại diện gốc -->
                        <img src="<?= htmlspecialchars(hinh_anh($diaDiem['anh_dai_dien'])) ?>" 
                             class="gallery-thumb rounded-3 border active-thumb" 
                             style="width: 80px; height: 56px; object-fit: cover; cursor: pointer; flex-shrink: 0;"
                             onclick="chuyenAnhGallery(this.src, this)" 
                             alt="Ảnh chính">
                        
                        <!-- Các ảnh gallery khác -->
                        <?php foreach ($gallery as $g): ?>
                            <img src="<?= htmlspecialchars(hinh_anh($g['duong_dan_anh'])) ?>" 
                                 class="gallery-thumb rounded-3 border" 
                                 style="width: 80px; height: 56px; object-fit: cover; cursor: pointer; flex-shrink: 0; opacity: 0.75;"
                                 onclick="chuyenAnhGallery(this.src, this)" 
                                 alt="Ảnh phụ">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <!-- Badges & Ratings -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-tag <?= $diaDiem['loai_danh_muc'] === 'am_thuc' ? 'badge-tag-food' : 'badge-tag-travel' ?>">
                                <?= htmlspecialchars($diaDiem['ten_danh_muc']) ?>
                            </span>
                            <?= \App\Helpers\FormatHelper::trangThaiHoatDongHtml($diaDiem['gio_mo_cua'], $diaDiem['gio_dong_cua']) ?>
                        </div>

                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <div><?= \App\Helpers\FormatHelper::saoHtml((float)$diaDiem['diem_danh_gia']) ?></div>
                            <span class="fw-bold text-dark"><?= number_format((float)$diaDiem['diem_danh_gia'], 1) ?></span>
                            <span>(<?= (int)$diaDiem['tong_danh_gia'] ?> đánh giá)</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-eye me-1"></i><?= number_format((int)$diaDiem['luot_xem']) ?> lượt xem</span>
                        </div>
                    </div>

                    <h1 class="h2 fw-bold text-dark mb-3"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></h1>

                    <!-- Khung Thông Tin Nhanh Chuẩn Hóa -->
                    <div class="bg-light p-3 rounded-3 mb-4 border">
                        <div class="row g-3 small">
                            <div class="col-md-6 d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-danger fs-5 mt-n1"></i>
                                <div>
                                    <strong class="text-dark d-block">Địa chỉ:</strong>
                                    <span class="text-secondary"><?= htmlspecialchars($diaDiem['dia_chi']) ?></span>
                                    <button type="button" class="btn btn-link p-0 text-primary small d-block mt-1 text-decoration-none" onclick="saoChepDiaChi('<?= addslashes(htmlspecialchars($diaDiem['dia_chi'])) ?>')">
                                        <i class="bi bi-clipboard me-1"></i>Sao chép địa chỉ
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-start gap-2">
                                <i class="bi bi-clock-fill text-warning fs-5 mt-n1"></i>
                                <div>
                                    <strong class="text-dark d-block">Khung giờ mở cửa:</strong>
                                    <?php if ($diaDiem['gio_mo_cua'] && $diaDiem['gio_dong_cua']): ?>
                                        <span class="text-secondary"><?= \App\Helpers\FormatHelper::gio($diaDiem['gio_mo_cua']) ?> - <?= \App\Helpers\FormatHelper::gio($diaDiem['gio_dong_cua']) ?> (Hàng ngày)</span>
                                    <?php else: ?>
                                        <span class="text-muted">Đang cập nhật lịch chi tiết</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-start gap-2">
                                <i class="bi bi-cash-stack text-success fs-5 mt-n1"></i>
                                <div>
                                    <strong class="text-dark d-block">Chi phí tham khảo:</strong>
                                    <span class="text-success fw-bold">
                                        <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_cao_nhat']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-start gap-2">
                                <i class="bi bi-telephone-fill text-primary fs-5 mt-n1"></i>
                                <div>
                                    <strong class="text-dark d-block">Hotline hỗ trợ:</strong>
                                    <a href="tel:19001999" class="text-decoration-none fw-semibold text-primary">1900 1999 (Trung tâm Hỗ trợ Du khách)</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tiện ích & Đặc Điểm Nổi Bật -->
                    <div class="mb-4">
                        <h2 class="h6 fw-bold text-dark text-uppercase mb-2" style="letter-spacing: 0.5px;">Tiện ích & Dịch vụ đi kèm</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($diaDiem['loai_danh_muc'] === 'am_thuc'): ?>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-wifi text-primary me-1"></i>Wifi miễn phí</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-snow text-info me-1"></i>Phòng máy lạnh mát mẻ</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-p-square text-success me-1"></i>Bãi đỗ ô tô & xe máy</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-qr-code-scan text-dark me-1"></i>Thanh toán QR / Thẻ</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-bag-check text-warning me-1"></i>Phục vụ mang về</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-qr-code text-success me-1"></i>Xuất vé E-Voucher QR tức thì</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-camera text-danger me-1"></i>Nhiều điểm check-in độc đáo</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-shield-check text-primary me-1"></i>Bảo hiểm du lịch trọn gói</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-p-square text-info me-1"></i>Bãi xe rộng rãi</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-person-badge text-secondary me-1"></i>Hướng dẫn viên bản địa</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Nội Dung Chi Tiết -->
                    <h2 class="h5 fw-bold text-dark mb-3">Giới thiệu & Điểm đặc sắc</h2>
                    <div class="article-content text-secondary lh-lg mb-4" style="font-size: 0.975rem;">
                        <?= nl2br(htmlspecialchars($diaDiem['noi_dung_chi_tiet'] ?? $diaDiem['mo_ta_ngan'] ?? 'Đang cập nhật nội dung...')) ?>
                    </div>
                </div>
            </div>

            <!-- 2. GÓI VÉ DỊCH VỤ TRỰC TUYẾN (NẾU CÓ BÁN VÉ) -->
            <?php if (!empty($diaDiem['co_ban_ve'])): ?>
                <div class="bg-white border rounded-4 p-4 shadow-sm mb-4" id="booking-section">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-ticket-perforated-fill fs-3 text-success"></i>
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-0">Vé Tham Quan & Tour Khám Phá Trực Tuyến</h2>
                                <p class="text-muted small mb-0">Nhận mã E-Voucher quét vào cổng tức thì, không cần xếp hàng mua vé</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <?php foreach ($goiVeList as $gv): ?>
                            <div class="col-12">
                                <div class="card border rounded-3 p-3 bg-light transition-hover">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h3 class="h6 fw-bold text-dark mb-0"><?= htmlspecialchars($gv['ten_goi_ve']) ?></h3>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle small">Có sẵn vé</span>
                                            </div>
                                            <p class="text-muted small mb-2"><?= htmlspecialchars($gv['mo_ta'] ?? 'Bao gồm trọn gói dịch vụ tham quan theo tiêu chuẩn.') ?></p>
                                            <div class="d-flex flex-wrap gap-3 small">
                                                <span class="text-dark">Người lớn: <strong class="text-success fs-6"><?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_nguoi_lon']) ?></strong></span>
                                                <span class="text-muted">Trẻ em: <strong class="text-dark"><?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_tre_em']) ?></strong></span>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="<?= url('booking/' . $gv['id']) ?>" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                                                <i class="bi bi-cart-check"></i>Đặt vé ngay
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. ĐÁNH GIÁ CỘNG ĐỒNG -->
            <?php
            $totalReviews = count($danhGiaList);
            $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            $photoCount = 0;
            $verifiedCount = 0;
            foreach ($danhGiaList as $dg) {
                $s = (int)$dg['so_sao'];
                if (isset($starCounts[$s])) $starCounts[$s]++;
                $photos = !empty($dg['hinh_anh']) ? json_decode($dg['hinh_anh'], true) : [];
                if (!empty($photos) && is_array($photos)) $photoCount++;
                if (!empty($dg['da_mua_ve'])) $verifiedCount++;
            }
            ?>
            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4" id="reviewSection">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 fw-bold text-dark mb-0">
                            <i class="bi bi-chat-heart-fill text-danger me-2"></i>Đánh giá từ cộng đồng (<?= $totalReviews ?>)
                        </h2>
                        <span class="text-muted small">Chia sẻ chân thực từ khách du lịch đã trải nghiệm thực tế</span>
                    </div>
                </div>

                <!-- Bảng tổng quan phân bố điểm số & Tỷ lệ sao -->
                <?php if ($totalReviews > 0): ?>
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-4 text-center border-md-end">
                                <div class="display-5 fw-bold text-dark mb-1"><?= number_format((float)$diaDiem['diem_danh_gia'], 1) ?></div>
                                <div class="text-warning fs-5 mb-1"><?= \App\Helpers\FormatHelper::saoHtml((float)$diaDiem['diem_danh_gia']) ?></div>
                                <span class="text-muted small d-block"><?= $totalReviews ?> lượt đánh giá cộng đồng</span>
                                <?php if ($verifiedCount > 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-1 small">
                                        <i class="bi bi-patch-check-fill me-1"></i><?= $verifiedCount ?> đã mua vé xác thực
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex flex-column gap-1">
                                    <?php for ($star = 5; $star >= 1; $star--): 
                                        $cnt = $starCounts[$star];
                                        $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                                    ?>
                                        <div class="d-flex align-items-center gap-2 small">
                                            <span class="fw-semibold text-dark text-nowrap" style="width: 42px;"><?= $star ?> <i class="bi bi-star-fill text-warning"></i></span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted text-end text-nowrap" style="width: 45px; font-size: 11px;"><?= $cnt ?> (<?= $pct ?>%)</span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Gửi Đánh Giá Mới -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?= url('dia-diem/danh-gia') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="dia_diem_id" value="<?= (int)$diaDiem['id'] ?>">
                            <input type="hidden" name="slug" value="<?= htmlspecialchars($diaDiem['duong_dan']) ?>">
                            
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold small text-dark">Gửi nhận xét của bạn:</span>
                                
                                <div class="star-rating-select">
                                    <input type="radio" id="star5" name="so_sao" value="5" checked><label for="star5" title="5 sao"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star4" name="so_sao" value="4"><label for="star4" title="4 sao"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star3" name="so_sao" value="3"><label for="star3" title="3 sao"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star2" name="so_sao" value="2"><label for="star2" title="2 sao"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star1" name="so_sao" value="1"><label for="star1" title="1 sao"><i class="bi bi-star-fill"></i></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <textarea name="noi_dung" rows="3" class="form-control form-control-sm" placeholder="Chia sẻ cảm nhận về món ăn, dịch vụ hoặc trải nghiệm tham quan của bạn..." required></textarea>
                            </div>

                            <!-- Đính kèm ảnh thực tế -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted d-flex align-items-center justify-content-between mb-1">
                                    <span><i class="bi bi-camera me-1 text-primary"></i>Đính kèm hình ảnh thực tế (tối đa 3 ảnh):</span>
                                    <span class="small text-secondary" style="font-size: 11px;">JPG, PNG, WEBP (tối đa 5MB)</span>
                                </label>
                                <input type="file" name="hinh_anh[]" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif" multiple id="reviewPhotoInput">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-coastal btn-sm rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-send me-1"></i>Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-2">
                            <p class="text-muted small mb-2">Đăng nhập tài khoản để chia sẻ nhận xét và xếp hạng sao cho địa điểm này.</p>
                            <a href="<?= url('login') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập để đánh giá
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- THANH BỘ LỌC ĐÁNH GIÁ (FILTER CHIPS) -->
                <?php if ($totalReviews > 0): ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom" id="reviewFilterChips">
                        <span class="small fw-bold text-muted me-1"><i class="bi bi-funnel me-1"></i>Lọc theo:</span>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold review-filter-btn active" data-filter="all">
                            Tất cả (<?= $totalReviews ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="5">
                            5 <i class="bi bi-star-fill text-warning"></i> (<?= $starCounts[5] ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="4">
                            4 <i class="bi bi-star-fill text-warning"></i> (<?= $starCounts[4] ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="3">
                            3 <i class="bi bi-star-fill text-warning"></i> (<?= $starCounts[3] ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="2">
                            2 <i class="bi bi-star-fill text-warning"></i> (<?= $starCounts[2] ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="1">
                            1 <i class="bi bi-star-fill text-warning"></i> (<?= $starCounts[1] ?>)
                        </button>
                        <?php if ($photoCount > 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="photo">
                                <i class="bi bi-camera-fill me-1"></i>Có ảnh (<?= $photoCount ?>)
                            </button>
                        <?php endif; ?>
                        <?php if ($verifiedCount > 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 small review-filter-btn" data-filter="verified">
                                <i class="bi bi-patch-check-fill me-1"></i>Đã mua vé (<?= $verifiedCount ?>)
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Danh sách bình luận -->
                <?php if (empty($danhGiaList)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-heart fs-1 text-muted d-block mb-2"></i>
                        <p class="small mb-0">Chưa có đánh giá nào cho địa điểm này. Hãy là người đầu tiên trải nghiệm và để lại nhận xét nhé!</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3" id="reviewItemsList">
                        <?php foreach ($danhGiaList as $dg): 
                            $photos = !empty($dg['hinh_anh']) ? json_decode($dg['hinh_anh'], true) : [];
                            $hasPhoto = (!empty($photos) && is_array($photos)) ? '1' : '0';
                            $isVerified = !empty($dg['da_mua_ve']) ? '1' : '0';
                        ?>
                            <div class="border-bottom pb-3 review-card-item" 
                                 data-star="<?= (int)$dg['so_sao'] ?>" 
                                 data-has-photo="<?= $hasPhoto ?>" 
                                 data-verified="<?= $isVerified ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <strong class="text-dark small">
                                            <i class="bi bi-person-circle me-1 text-primary"></i><?= htmlspecialchars($dg['ho_ten']) ?>
                                        </strong>
                                        <?php if (!empty($dg['da_mua_ve'])): ?>
                                            <!-- Verified Buyer Badge (Đánh Giá Minh Bạch) -->
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 11px;">
                                                <i class="bi bi-patch-check-fill me-1"></i>Đã mua vé &amp; trải nghiệm
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="small text-muted"><?= \App\Helpers\FormatHelper::ngayThang($dg['ngay_tao']) ?></span>
                                </div>
                                <div class="mb-2"><?= \App\Helpers\FormatHelper::saoHtml((int)$dg['so_sao']) ?></div>
                                <p class="text-secondary small mb-2"><?= nl2br(htmlspecialchars($dg['noi_dung'])) ?></p>
                                
                                <?php if (!empty($photos) && is_array($photos)): ?>
                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        <?php foreach ($photos as $p): ?>
                                            <a href="<?= url(htmlspecialchars($p)) ?>" target="_blank" class="d-inline-block rounded-3 overflow-hidden border shadow-sm" title="Bấm để xem ảnh phóng to">
                                                <img src="<?= url(htmlspecialchars($p)) ?>" alt="Ảnh trải nghiệm thực tế" style="width: 80px; height: 80px; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" loading="lazy">
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Trạng thái trống khi lọc không ra kết quả -->
                    <div id="reviewEmptyFilterState" class="text-center py-4 text-muted d-none">
                        <i class="bi bi-filter-circle fs-2 text-secondary opacity-50 d-block mb-2"></i>
                        <p class="small mb-2">Không tìm thấy đánh giá nào phù hợp với bộ lọc hiện tại.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="resetReviewFilter()">
                            Xem tất cả đánh giá
                        </button>
                    </div>

                    <!-- THANH PHÂN TRANG ĐÁNH GIÁ (PAGINATION) -->
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2" id="reviewPaginationWrapper">
                        <span class="small text-muted" id="reviewPageInfo" style="font-size: 12px;">Đang xem 1 - 4 của <?= $totalReviews ?> đánh giá</span>
                        <nav aria-label="Phân trang đánh giá">
                            <ul class="pagination pagination-sm mb-0 gap-1" id="reviewPagination">
                                <!-- Rendered dynamically by JS -->
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>

            <!-- JAVASCRIPT BỘ LỌC & PHÂN TRANG ĐÁNH GIÁ TỨC THÌ -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const items = Array.from(document.querySelectorAll('.review-card-item'));
                if (items.length === 0) return;

                const PER_PAGE = 4;
                let currentFilter = 'all';
                let currentPage = 1;
                let filteredItems = [...items];

                const paginationUl = document.getElementById('reviewPagination');
                const pageInfoEl = document.getElementById('reviewPageInfo');
                const emptyEl = document.getElementById('reviewEmptyFilterState');
                const filterBtns = document.querySelectorAll('.review-filter-btn');

                function applyFilter(filter) {
                    currentFilter = filter;
                    currentPage = 1;

                    if (filter === 'all') {
                        filteredItems = [...items];
                    } else if (filter === 'photo') {
                        filteredItems = items.filter(el => el.getAttribute('data-has-photo') === '1');
                    } else if (filter === 'verified') {
                        filteredItems = items.filter(el => el.getAttribute('data-verified') === '1');
                    } else {
                        // Số sao: '5', '4', '3', '2', '1'
                        filteredItems = items.filter(el => el.getAttribute('data-star') === filter);
                    }

                    renderPage();
                }

                function renderPage() {
                    const total = filteredItems.length;
                    const totalPages = Math.ceil(total / PER_PAGE);

                    // Ẩn toàn bộ items
                    items.forEach(el => el.style.display = 'none');

                    if (total === 0) {
                        if (emptyEl) emptyEl.classList.remove('d-none');
                        if (paginationUl) paginationUl.innerHTML = '';
                        if (pageInfoEl) pageInfoEl.textContent = '0 đánh giá';
                        return;
                    }

                    if (emptyEl) emptyEl.classList.add('d-none');

                    // Giới hạn currentPage
                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    const startIdx = (currentPage - 1) * PER_PAGE;
                    const endIdx = Math.min(startIdx + PER_PAGE, total);

                    for (let i = startIdx; i < endIdx; i++) {
                        filteredItems[i].style.display = 'block';
                    }

                    if (pageInfoEl) {
                        pageInfoEl.textContent = `Hiển thị ${startIdx + 1} - ${endIdx} trong số ${total} đánh giá`;
                    }

                    // Render các nút phân trang
                    renderPaginationButtons(totalPages);
                }

                function renderPaginationButtons(totalPages) {
                    if (!paginationUl) return;
                    if (totalPages <= 1) {
                        paginationUl.innerHTML = '';
                        return;
                    }

                    let html = '';
                    // Nút Trước
                    html += `
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <button class="page-link rounded-pill px-2.5 py-1" onclick="window.goReviewPage(${currentPage - 1})" aria-label="Trước">&laquo;</button>
                        </li>
                    `;

                    for (let p = 1; p <= totalPages; p++) {
                        html += `
                            <li class="page-item ${p === currentPage ? 'active' : ''}">
                                <button class="page-link rounded-pill px-3 py-1 fw-semibold" onclick="window.goReviewPage(${p})">${p}</button>
                            </li>
                        `;
                    }

                    // Nút Sau
                    html += `
                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <button class="page-link rounded-pill px-2.5 py-1" onclick="window.goReviewPage(${currentPage + 1})" aria-label="Sau">&raquo;</button>
                        </li>
                    `;

                    paginationUl.innerHTML = html;
                }

                window.goReviewPage = function(page) {
                    currentPage = page;
                    renderPage();
                    const section = document.getElementById('reviewSection');
                    if (section) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                };

                window.resetReviewFilter = function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        if (b.getAttribute('data-filter') === 'all') {
                            b.classList.add('btn-primary', 'active');
                        } else {
                            b.classList.add(b.getAttribute('data-filter') === 'photo' ? 'btn-outline-primary' : (b.getAttribute('data-filter') === 'verified' ? 'btn-outline-success' : 'btn-outline-secondary'));
                        }
                    });
                    applyFilter('all');
                };

                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        filterBtns.forEach(b => {
                            b.classList.remove('btn-primary', 'active');
                            const f = b.getAttribute('data-filter');
                            if (f === 'photo') {
                                b.className = 'btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 small review-filter-btn';
                            } else if (f === 'verified') {
                                b.className = 'btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 small review-filter-btn';
                            } else {
                                b.className = 'btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small review-filter-btn';
                            }
                        });
                        this.className = 'btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold review-filter-btn active';
                        applyFilter(this.getAttribute('data-filter'));
                    });
                });

                // Khởi tạo hiển thị trang đầu tiên
                renderPage();
            });
            </script>

            <!-- 4. ĐỊA ĐIỂM TƯƠNG TỰ CÙNG CHỦ ĐỀ -->
            <?php if (!empty($diaDiemLienQuan)): ?>
                <div class="mb-4">
                    <h2 class="h5 fw-bold text-dark mb-3">Có thể bạn cũng thích</h2>
                    <div class="row g-3">
                        <?php foreach ($diaDiemLienQuan as $rel): ?>
                            <div class="col-md-6">
                                <div class="card border rounded-3 overflow-hidden h-100 shadow-sm d-flex flex-row">
                                    <img src="<?= htmlspecialchars(hinh_anh($rel['anh_dai_dien'])) ?>" 
                                         alt="<?= htmlspecialchars($rel['ten_dia_diem']) ?>" 
                                         style="width: 110px; height: 100%; object-fit: cover; flex-shrink: 0;" loading="lazy">
                                    <div class="p-3 d-flex flex-column justify-content-between w-100">
                                        <div>
                                            <span class="badge bg-light text-secondary border small mb-1" style="font-size: 10px;">
                                                <?= htmlspecialchars($rel['ten_danh_muc']) ?>
                                            </span>
                                            <h4 class="fw-bold text-dark mb-1 line-clamp-1" style="font-size: 14px;">
                                                <a href="<?= url('dia-diem/' . $rel['duong_dan']) ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($rel['ten_dia_diem']) ?>
                                                </a>
                                            </h4>
                                            <div class="small text-warning mb-1">
                                                <i class="bi bi-star-fill"></i> <?= number_format((float)$rel['diem_danh_gia'], 1) ?>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-1 border-top mt-1">
                                            <span class="text-danger fw-bold small" style="font-size: 12px;">
                                                <?= \App\Helpers\FormatHelper::tienTe((float)$rel['gia_thap_nhat']) ?>
                                            </span>
                                            <a href="<?= url('dia-diem/' . $rel['duong_dan']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 11px;">
                                                Xem
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cột Bên Phải: Bản Đồ Mini & Hotline Hỗ Trợ (Col-4) -->
        <div class="col-lg-4">
            <div class="detail-sticky-sidebar" style="position: sticky; top: 85px; z-index: 10;">
                <?php if ($hasTickets): ?>
                    <!-- Card Đặt Vé Nhanh (Desktop Sticky Booking Card) -->
                    <div class="card border-0 rounded-4 shadow-sm mb-3 bg-white sticky-booking-card overflow-hidden">
                        <div class="p-3 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-ticket-perforated-fill fs-5"></i>
                                <span class="fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Đặt Vé Trực Tuyến</span>
                            </div>
                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small fw-semibold">
                                <i class="bi bi-lightning-charge-fill me-1"></i>QR tức thì
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-baseline justify-content-between mb-1">
                                <span class="text-muted small">Giá chỉ từ</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-semibold">
                                    <i class="bi bi-fire me-1"></i>Ưu đãi tốt nhất
                                </span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mb-3">
                                <span class="fs-3 fw-bolder text-success lh-1"><?= \App\Helpers\FormatHelper::tienTe($minTicketPrice) ?></span>
                                <span class="text-muted small">/ khách</span>
                            </div>

                            <div class="bg-light p-2.5 rounded-3 mb-3 small text-secondary border">
                                <div class="d-flex align-items-center gap-2 mb-1.5">
                                    <i class="bi bi-qr-code text-primary fs-6"></i>
                                    <span>E-Voucher QR quét vào cổng ngay</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-1.5">
                                    <i class="bi bi-shield-check text-success fs-6"></i>
                                    <span>Thanh toán VietQR tiện lợi & an toàn</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-info fs-6"></i>
                                    <span>Không cần xếp hàng chờ đợi</span>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 py-2.5 text-uppercase" onclick="scrollToBookingSection()" style="letter-spacing: 0.3px; font-size: 0.95rem;">
                                <i class="bi bi-cart-check-fill fs-5"></i>
                                <span>Chọn Vé & Đặt Ngay</span>
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted" style="font-size: 0.78rem;">
                                    <i class="bi bi-info-circle me-1"></i>Có <?= count($goiVeList) ?> gói vé đang mở bán
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Card Bản Đồ Vị Trí & Dẫn Đường OSRM Nội Bộ -->
                <div class="bg-white border rounded-4 p-3 shadow-sm mb-3">
                    <h5 class="fw-bold mb-2 text-dark fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-danger"></i>Vị trí thực tế trên bản đồ
                    </h5>
                    <p class="text-muted small mb-3">Tọa độ chính xác theo hệ bản đồ số Nha Trang</p>
                    
                    <div id="detail-map" 
                         style="height: 250px; border-radius: 12px; overflow: hidden; position: relative; z-index: 1; isolation: isolate;"
                         data-lat="<?= htmlspecialchars($diaDiem['vi_do']) ?>" 
                         data-lng="<?= htmlspecialchars($diaDiem['kinh_do']) ?>"
                         data-title="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">
                    </div>

                    <!-- Khung Dẫn Đường OSRM Từng Chặng (Turn-by-Turn) -->
                    <div id="detail-osrm-panel" class="mt-3 p-3 bg-light rounded-4 border" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 me-1" id="detail-route-dist">0 km</span>
                                <span class="badge bg-info text-dark rounded-pill px-2 py-1" id="detail-route-time">0 phút</span>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" onclick="closeDetailRouting()">
                                <i class="bi bi-x-lg"></i> Đóng
                            </button>
                        </div>
                        <div id="detail-route-steps" class="small text-secondary" style="max-height: 200px; overflow-y: auto;">
                            <!-- Danh sách các bước rẽ OSRM -->
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-column gap-2">
                        <button type="button" class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" id="btn-detail-osrm" onclick="startDetailRouting()">
                            <i class="bi bi-sign-turn-right-fill"></i>Chỉ Đường OSRM Nội Bộ
                        </button>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?= $diaDiem['vi_do'] ?>,<?= $diaDiem['kinh_do'] ?>" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn btn-outline-secondary btn-sm w-100 rounded-pill fw-semibold py-1">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Mở trên Google Maps
                        </a>
                    </div>
                </div>

                <!-- Card Tư Vấn & Hỗ Trợ Trực Tiếp -->
                <div class="bg-light border rounded-4 p-3 shadow-sm">
                    <h5 class="fw-bold mb-2 text-dark fs-6"><i class="bi bi-headset me-2 text-success"></i>Hỗ trợ tư vấn tour & vé</h5>
                    <p class="text-muted small mb-3">Cần tư vấn lịch trình hoặc đặt vé cho đoàn thể lớn? Hãy kết nối với chúng tôi ngay:</p>
                    <a href="tel:19001999" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="bi bi-telephone-outbound"></i>Gọi tổng đài: 1900 1999
                    </a>
                    <a href="https://zalo.me" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary w-100 rounded-pill btn-sm fw-semibold">
                        <i class="bi bi-chat-dots me-1 text-primary"></i>Hỏi nhanh qua Zalo Official
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if ($hasTickets): ?>
        <!-- Mobile Sticky Booking Bottom Bar (Dành cho Mobile < 992px) -->
        <div id="mobile-sticky-booking-bar" class="d-lg-none">
            <div class="container-fluid py-2 px-3">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="min-w-0">
                        <div class="text-muted" style="font-size: 0.72rem; line-height: 1.1;">Giá chỉ từ</div>
                        <div class="fw-bold text-success fs-5 lh-1 mt-1 text-truncate"><?= \App\Helpers\FormatHelper::tienTe($minTicketPrice) ?></div>
                        <div class="text-secondary small d-flex align-items-center gap-1 mt-1" style="font-size: 0.7rem;">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5"><i class="bi bi-lightning-fill"></i> QR tức thì</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success fw-bold px-4 py-2.5 rounded-pill shadow-sm d-flex align-items-center gap-2 flex-shrink-0" onclick="scrollToBookingSection()">
                        <i class="bi bi-ticket-perforated-fill fs-5"></i>
                        <span>Đặt Vé Ngay</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";

    // 0. Cuộn mượt tới phần Đặt Vé và tạo hiệu ứng nhấp nháy thu hút sự chú ý
    function scrollToBookingSection() {
        const bookingSec = document.getElementById('booking-section');
        if (bookingSec) {
            const navbarOffset = 85;
            const elementPosition = bookingSec.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - navbarOffset;
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
            bookingSec.classList.add('booking-highlight');
            setTimeout(() => {
                bookingSec.classList.remove('booking-highlight');
            }, 1800);
        } else {
            const mapSec = document.getElementById('detail-map');
            if (mapSec) {
                mapSec.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    // Tự động kích hoạt hiển thị Mobile Sticky Booking Bar khi cuộn trang
    document.addEventListener('DOMContentLoaded', function() {
        const mobileBar = document.getElementById('mobile-sticky-booking-bar');
        if (!mobileBar) return;

        document.body.classList.add('has-mobile-booking-bar');

        function updateMobileStickyBar() {
            const currentScrollY = window.scrollY || window.pageYOffset;
            if (currentScrollY > 220) {
                mobileBar.classList.add('show-bar');
            } else {
                mobileBar.classList.remove('show-bar');
            }
        }

        window.addEventListener('scroll', updateMobileStickyBar, { passive: true });
        updateMobileStickyBar();
    });

    // 1. Chuyển ảnh trong Gallery
    function chuyenAnhGallery(src, thumbEl) {
        const mainImg = document.getElementById('main-gallery-img');
        if (!mainImg) return;
        mainImg.style.opacity = '0.3';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 150);

        document.querySelectorAll('.gallery-thumb').forEach(t => {
            t.style.opacity = '0.75';
            t.classList.remove('active-thumb');
        });
        thumbEl.style.opacity = '1';
        thumbEl.classList.add('active-thumb');
    }

    // 2. Sao chép địa chỉ
    function saoChepDiaChi(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Đã sao chép địa chỉ: ' + text);
        }).catch(() => {
            prompt('Sao chép địa chỉ:', text);
        });
    }

    // 3. Chia sẻ địa điểm
    function chiaSeDiaDiem() {
        if (navigator.share) {
            navigator.share({
                title: "<?= addslashes(htmlspecialchars($diaDiem['ten_dia_diem'])) ?> - Du Lịch Nha Trang",
                url: window.location.href
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Đã sao chép liên kết địa điểm vào bộ nhớ tạm!');
            });
        }
    }

    // 4. AJAX Toggle Yêu thích (Wishlist)
    document.addEventListener('DOMContentLoaded', function () {
        const btnFav = document.getElementById('btn-toggle-favorite');
        if (btnFav) {
            btnFav.addEventListener('click', function () {
                const diaDiemId = this.getAttribute('data-id');
                const csrfToken = "<?= csrf_token() ?>";

                this.disabled = true;
                fetch("<?= url('api/favorites/toggle') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        dia_diem_id: diaDiemId,
                        csrf_token: csrfToken
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btnFav.disabled = false;
                    if (data.require_login) {
                        if (confirm(data.message + '\nBạn có muốn chuyển đến trang đăng nhập ngay không?')) {
                            window.location.href = "<?= url('login') ?>";
                        }
                        return;
                    }
                    if (data.success) {
                        const icon = btnFav.querySelector('i');
                        const text = btnFav.querySelector('#fav-text');
                        if (data.is_favorite) {
                            btnFav.classList.remove('btn-outline-danger');
                            btnFav.classList.add('btn-danger', 'text-white');
                            icon.className = 'bi bi-heart-fill';
                            if (text) text.textContent = 'Đã lưu yêu thích';
                        } else {
                            btnFav.classList.remove('btn-danger', 'text-white');
                            btnFav.classList.add('btn-outline-danger');
                            icon.className = 'bi bi-heart';
                            if (text) text.textContent = 'Lưu yêu thích';
                        }
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                })
                .catch(err => {
                    btnFav.disabled = false;
                    console.error('Favorite error:', err);
                });
            });
        }
    });

    // 5. Dẫn Đường OSRM Từng Chặng (In-app Turn-by-Turn Routing)
    let detailRouteLayer = null;

    function startDetailRouting() {
        const mapEl = document.getElementById('detail-map');
        if (!mapEl) return;
        const destLat = parseFloat(mapEl.dataset.lat);
        const destLng = parseFloat(mapEl.dataset.lng);
        const destTitle = mapEl.dataset.title;

        const btn = document.getElementById('btn-detail-osrm');
        const panel = document.getElementById('detail-osrm-panel');
        const distEl = document.getElementById('detail-route-dist');
        const timeEl = document.getElementById('detail-route-time');
        const stepsEl = document.getElementById('detail-route-steps');

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tính lộ trình OSRM...';
        }

        function calculateRoute(startLat, startLng, isFallback = false) {
            const url = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${destLng},${destLat}?overview=full&geometries=geojson&steps=true`;

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Làm mới lộ trình OSRM';
                    }

                    if (!data || data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        alert('Không tìm thấy đường đi từ vị trí này.');
                        return;
                    }

                    const route = data.routes[0];
                    const km = (route.distance / 1000).toFixed(1);
                    const mins = Math.max(1, Math.round(route.duration / 60));

                    if (distEl) distEl.innerText = km + ' km';
                    if (timeEl) timeEl.innerText = '~' + mins + ' phút';

                    const mapObj = window.detailMapInstance;
                    if (mapObj) {
                        if (detailRouteLayer) {
                            mapObj.removeLayer(detailRouteLayer);
                        }

                        const latLngs = route.geometry.coordinates.map(c => [c[1], c[0]]);
                        const casing = L.polyline(latLngs, { color: '#091e3a', weight: 6, opacity: 0.8 });
                        const line = L.polyline(latLngs, { color: '#0284c7', weight: 4, opacity: 0.95 });

                        const startIcon = L.divIcon({
                            className: 'custom-leaflet-marker-wrapper',
                            html: '<div class="custom-marker" style="background:#16a34a; border-color:#ffffff; color:#fff; width:28px; height:28px; font-size:12px; display:flex; align-items:center; justify-content:center; border-radius:50%;"><i class="bi bi-geo-alt-fill"></i></div>',
                            iconSize: [28, 28]
                        });
                        const startMarker = L.marker([startLat, startLng], { icon: startIcon })
                            .bindPopup('<strong>Điểm xuất phát</strong>' + (isFallback ? ' (Quảng trường 2/4 Demo)' : ' (Vị trí của bạn)'));

                        detailRouteLayer = L.layerGroup([casing, line, startMarker]).addTo(mapObj);
                        mapObj.fitBounds(casing.getBounds(), { padding: [25, 25] });
                    }

                    if (stepsEl && route.legs && route.legs[0] && route.legs[0].steps) {
                        const steps = route.legs[0].steps;
                        let html = '';
                        steps.forEach((st) => {
                            const m = st.maneuver || {};
                            let icon = '<i class="bi bi-arrow-up"></i>';
                            let desc = '';
                            const name = st.name ? `<strong>${st.name}</strong>` : 'đoạn đường';
                            const distStep = st.distance >= 1000 ? (st.distance / 1000).toFixed(1) + ' km' : Math.round(st.distance) + ' m';

                            if (m.type === 'depart') {
                                icon = '<i class="bi bi-play-circle-fill text-success"></i>';
                                desc = 'Bắt đầu di chuyển vào ' + name;
                            } else if (m.type === 'arrive') {
                                icon = '<i class="bi bi-geo-alt-fill text-danger"></i>';
                                desc = 'Đến điểm hẹn: ' + destTitle;
                            } else if (m.modifier && m.modifier.includes('left')) {
                                icon = '<i class="bi bi-arrow-90deg-left text-primary"></i>';
                                desc = 'Rẽ trái vào ' + name;
                            } else if (m.modifier && m.modifier.includes('right')) {
                                icon = '<i class="bi bi-arrow-90deg-right text-primary"></i>';
                                desc = 'Rẽ phải vào ' + name;
                            } else {
                                icon = '<i class="bi bi-arrow-up text-secondary"></i>';
                                desc = 'Đi thẳng tiếp tục ' + name;
                            }

                            html += `
                                <div class="osrm-step-item">
                                    <div class="osrm-step-icon">${icon}</div>
                                    <div class="flex-grow-1">
                                        <div class="text-dark">${desc}</div>
                                        <span class="text-muted" style="font-size: 11px;">(khoảng ${distStep})</span>
                                    </div>
                                </div>
                            `;
                        });
                        stepsEl.innerHTML = html;
                    }

                    if (panel) panel.style.display = 'block';
                })
                .catch(err => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-sign-turn-right-fill me-1"></i>Chỉ Đường OSRM Nội Bộ';
                    }
                    alert('Không thể kết nối đến máy chủ OSRM.');
                });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    calculateRoute(pos.coords.latitude, pos.coords.longitude, false);
                },
                () => {
                    // Fallback demo: Quảng trường 2/4 Nha Trang
                    calculateRoute(12.2388, 109.1961, true);
                },
                { timeout: 5000 }
            );
        } else {
            calculateRoute(12.2388, 109.1961, true);
        }
    }

    function closeDetailRouting() {
        const panel = document.getElementById('detail-osrm-panel');
        if (panel) panel.style.display = 'none';

        if (window.detailMapInstance && detailRouteLayer) {
            window.detailMapInstance.removeLayer(detailRouteLayer);
            detailRouteLayer = null;

            const mapEl = document.getElementById('detail-map');
            if (mapEl) {
                const lat = parseFloat(mapEl.dataset.lat);
                const lng = parseFloat(mapEl.dataset.lng);
                window.detailMapInstance.setView([lat, lng], 15);
            }
        }
    }
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

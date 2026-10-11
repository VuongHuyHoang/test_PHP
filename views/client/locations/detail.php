<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-4 flex-grow-1">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= url('locations') ?>">Địa điểm</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Cột Nội Dung Chính -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <img src="<?= htmlspecialchars($diaDiem['anh_dai_dien']) ?>" class="w-100" style="max-height: 420px; object-fit: cover;" alt="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">
                
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <span class="badge <?= $diaDiem['loai_danh_muc'] === 'am_thuc' ? 'badge-am-thuc' : 'badge-du-lich' ?> fs-6">
                            <?= htmlspecialchars($diaDiem['ten_danh_muc']) ?>
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <?= \App\Helpers\FormatHelper::saoHtml((float)$diaDiem['diem_danh_gia']) ?>
                            <span class="small text-muted">(<?= (int)$diaDiem['tong_danh_gia'] ?> đánh giá)</span>
                            <span class="text-muted small">&bull;</span>
                            <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= (int)$diaDiem['luot_xem'] ?> lượt xem</span>
                        </div>
                    </div>

                    <h1 class="fw-bold mb-3"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></h1>

                    <div class="d-flex flex-column gap-2 text-muted mb-4 pb-3 border-bottom">
                        <div><i class="bi bi-geo-alt-fill text-danger me-2"></i><strong>Địa chỉ:</strong> <?= htmlspecialchars($diaDiem['dia_chi']) ?></div>
                        <?php if ($diaDiem['gio_mo_cua'] && $diaDiem['gio_dong_cua']): ?>
                            <div><i class="bi bi-clock-fill text-warning me-2"></i><strong>Giờ hoạt động:</strong> <?= \App\Helpers\FormatHelper::gio($diaDiem['gio_mo_cua']) ?> - <?= \App\Helpers\FormatHelper::gio($diaDiem['gio_dong_cua']) ?></div>
                        <?php endif; ?>
                        <div><i class="bi bi-cash-stack text-success me-2"></i><strong>Mức giá tham khảo:</strong> <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_cao_nhat']) ?></div>
                    </div>

                    <h4 class="fw-bold mb-3">Giới Thiệu Chi Tiết</h4>
                    <div class="article-content text-secondary lh-lg mb-4">
                        <?= nl2br(htmlspecialchars($diaDiem['noi_dung_chi_tiet'] ?? $diaDiem['mo_ta_ngan'] ?? 'Đang cập nhật...')) ?>
                    </div>
                </div>
            </div>

            <!-- Phân hệ Đặt Vé Nếu Địa Điểm Có Bán Vé -->
            <?php if (!empty($diaDiem['co_ban_ve'])): ?>
                <div class="card border-0 shadow-sm p-4 mb-4" id="booking-section">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-ticket-perforated-fill fs-3 text-success"></i>
                        <h4 class="fw-bold mb-0">Vé Dịch Vụ & Tour Khám Phá</h4>
                    </div>
                    <p class="text-muted small mb-4">Đặt vé online nhận ngay mã QR E-Voucher tiện lợi, không lo xếp hàng chờ đợi.</p>

                    <div class="row g-3">
                        <?php foreach ($goiVeList as $gv): ?>
                            <div class="col-12">
                                <div class="border rounded-3 p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($gv['ten_goi_ve']) ?></h5>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($gv['mo_ta'] ?? '') ?></p>
                                        <div class="d-flex gap-3 small">
                                            <span class="text-primary fw-semibold">Người lớn: <?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_nguoi_lon']) ?></span>
                                            <span class="text-secondary">Trẻ em: <?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_tre_em']) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="<?= url('booking/' . $gv['id']) ?>" class="btn btn-success fw-bold px-4 rounded-pill">
                                            <i class="bi bi-cart-plus me-1"></i>Đặt Vé Ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Đánh Giá Cộng Đồng -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h4 class="fw-bold mb-3">Đánh Giá Từ Cộng Đồng (<?= count($danhGiaList) ?>)</h4>
                
                <?php if (empty($danhGiaList)): ?>
                    <p class="text-muted">Chưa có đánh giá nào cho địa điểm này. Hãy là người đầu tiên trải nghiệm và chia sẻ nhé!</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($danhGiaList as $dg): ?>
                            <div class="border-bottom pb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($dg['ho_ten']) ?></strong>
                                    <span class="small text-muted"><?= \App\Helpers\FormatHelper::ngayThang($dg['ngay_tao']) ?></span>
                                </div>
                                <div class="mb-2"><?= \App\Helpers\FormatHelper::saoHtml((int)$dg['so_sao']) ?></div>
                                <p class="text-secondary small mb-0"><?= nl2br(htmlspecialchars($dg['noi_dung'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột Bên Phải: Định Vị Bản Đồ & Thông Tin Hỗ Trợ -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-3 mb-4 sticky-top" style="top: 85px;">
                <h5 class="fw-bold mb-3"><i class="bi bi-map me-2 text-primary"></i>Vị Trí Bản Đồ</h5>
                <div id="detail-map" 
                     data-lat="<?= htmlspecialchars($diaDiem['vi_do']) ?>" 
                     data-lng="<?= htmlspecialchars($diaDiem['kinh_do']) ?>"
                     data-title="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">
                </div>
                <div class="mt-3 text-center">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $diaDiem['vi_do'] ?>,<?= $diaDiem['kinh_do'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                        <i class="bi bi-compass me-1"></i>Chỉ Đường Trên Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

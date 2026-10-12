<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-4 flex-grow-1">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= url('locations') ?>" class="text-decoration-none">Địa điểm</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Cột Nội Dung Trải Nghiệm Chính -->
        <div class="col-lg-8">
            <div class="bg-white border rounded-3 overflow-hidden shadow-sm mb-4">
                <img src="<?= htmlspecialchars($diaDiem['anh_dai_dien']) ?>" class="w-100" style="max-height: 420px; object-fit: cover;" alt="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">
                
                <div class="p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <span class="badge-tag <?= $diaDiem['loai_danh_muc'] === 'am_thuc' ? 'badge-tag-food' : 'badge-tag-travel' ?>">
                            <?= htmlspecialchars($diaDiem['ten_danh_muc']) ?>
                        </span>
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <div><?= \App\Helpers\FormatHelper::saoHtml((float)$diaDiem['diem_danh_gia']) ?></div>
                            <span>(<?= (int)$diaDiem['tong_danh_gia'] ?> đánh giá)</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-eye me-1"></i><?= (int)$diaDiem['luot_xem'] ?> lượt xem</span>
                        </div>
                    </div>

                    <h1 class="h2 fw-bold text-dark mb-3"><?= htmlspecialchars($diaDiem['ten_dia_diem']) ?></h1>

                    <!-- Thông tin nhanh về địa chỉ, giờ giấc, mức giá -->
                    <div class="d-flex flex-column gap-2 text-muted mb-4 pb-3 border-bottom small">
                        <div>
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <strong class="text-dark">Địa chỉ:</strong> <?= htmlspecialchars($diaDiem['dia_chi']) ?>
                        </div>
                        <?php if ($diaDiem['gio_mo_cua'] && $diaDiem['gio_dong_cua']): ?>
                            <div>
                                <i class="bi bi-clock-fill text-warning me-2"></i>
                                <strong class="text-dark">Giờ hoạt động:</strong> 
                                <?= \App\Helpers\FormatHelper::gio($diaDiem['gio_mo_cua']) ?> - <?= \App\Helpers\FormatHelper::gio($diaDiem['gio_dong_cua']) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <i class="bi bi-cash-stack text-success me-2"></i>
                            <strong class="text-dark">Mức giá tham khảo:</strong> 
                            <span class="text-dark fw-semibold">
                                <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$diaDiem['gia_cao_nhat']) ?>
                            </span>
                        </div>
                    </div>

                    <h2 class="h5 fw-bold text-dark mb-3">Giới thiệu & Điểm đặc sắc</h2>
                    <div class="article-content text-secondary lh-lg mb-4" style="font-size: 0.975rem;">
                        <?= nl2br(htmlspecialchars($diaDiem['noi_dung_chi_tiet'] ?? $diaDiem['mo_ta_ngan'] ?? 'Đang cập nhật nội dung...')) ?>
                    </div>
                </div>
            </div>

            <!-- Phân hệ Vé Dịch Vụ Nếu Địa Điểm Có Bán Vé -->
            <?php if (!empty($diaDiem['co_ban_ve'])): ?>
                <div class="bg-white border rounded-3 p-4 shadow-sm mb-4" id="booking-section">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-ticket-perforated-fill fs-4 text-success"></i>
                        <h2 class="h5 fw-bold text-dark mb-0">Vé Tham Quan & Tour Khám Phá</h2>
                    </div>
                    <p class="text-muted small mb-4">Đặt vé online nhận mã QR E-Voucher xác nhận ngay lập tức, không lo xếp hàng tại quầy.</p>

                    <div class="row g-3">
                        <?php foreach ($goiVeList as $gv): ?>
                            <div class="col-12">
                                <div class="border rounded-3 p-3 d-flex flex-wrap justify-content-between align-items-center gap-3 bg-light">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;"><?= htmlspecialchars($gv['ten_goi_ve']) ?></h5>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($gv['mo_ta'] ?? '') ?></p>
                                        <div class="d-flex gap-3 small">
                                            <span class="text-dark fw-semibold">Người lớn: <strong class="text-success"><?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_nguoi_lon']) ?></strong></span>
                                            <span class="text-muted">Trẻ em: <strong><?= \App\Helpers\FormatHelper::tienTe((float)$gv['gia_tre_em']) ?></strong></span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="<?= url('booking/' . $gv['id']) ?>" class="btn btn-success fw-semibold px-4 rounded-pill btn-sm">
                                            <i class="bi bi-cart-plus me-1"></i>Đặt vé ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Khu Vực Đánh Giá Cộng Đồng -->
            <div class="bg-white border rounded-3 p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-dark mb-0">Đánh giá từ cộng đồng (<?= count($danhGiaList) ?>)</h2>
                </div>

                <!-- Form Gửi Đánh Giá Mới -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?= url('dia-diem/danh-gia') ?>" method="POST">
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
                                <textarea name="noi_dung" rows="3" class="form-control form-control-sm" placeholder="Chia sẻ cảm nhận về món ăn, chất lượng phục vụ hoặc trải nghiệm tham quan..." required></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-coastal btn-sm rounded-pill px-4">
                                    Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-2">
                            <p class="text-muted small mb-2">Đăng nhập tài khoản để chia sẻ trải nghiệm và đánh giá địa điểm này.</p>
                            <a href="<?= url('login') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                Đăng nhập để đánh giá
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sách bình luận -->
                <?php if (empty($danhGiaList)): ?>
                    <p class="text-muted small mb-0">Chưa có đánh giá nào cho địa điểm này. Hãy là người đầu tiên trải nghiệm nhé!</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($danhGiaList as $dg): ?>
                            <div class="border-bottom pb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark small"><i class="bi bi-person-circle me-1 text-muted"></i><?= htmlspecialchars($dg['ho_ten']) ?></strong>
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

        <!-- Cột Bên Phải: Bản Đồ Mini & Chỉ Đường -->
        <div class="col-lg-4">
            <div class="bg-white border rounded-3 p-3 shadow-sm sticky-top" style="top: 85px;">
                <h5 class="fw-bold mb-3 text-dark fs-6"><i class="bi bi-geo-alt me-2 text-primary"></i>Vị trí trên bản đồ</h5>
                <div id="detail-map" 
                     data-lat="<?= htmlspecialchars($diaDiem['vi_do']) ?>" 
                     data-lng="<?= htmlspecialchars($diaDiem['kinh_do']) ?>"
                     data-title="<?= htmlspecialchars($diaDiem['ten_dia_diem']) ?>">
                </div>
                <div class="mt-3">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $diaDiem['vi_do'] ?>,<?= $diaDiem['kinh_do'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                        <i class="bi bi-signpost-split me-1"></i>Mở chỉ đường trên Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    window.APP_URL = "<?= rtrim(url(''), '/') ?>";
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

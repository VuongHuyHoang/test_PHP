<?php
/**
 * @var string $title
 * @var array $reviews
 * @var array $stats
 * @var string $tuKhoa
 * @var string $trangThai
 * @var string $soSao
 * @var int|null $page
 * @var int|null $perPage
 * @var int|null $totalReviews
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';
?>

<!-- Thống Kê Nhanh Đánh Giá -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Tổng đánh giá</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total']) ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="bi bi-chat-left-text-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Đang hiển thị</span>
                    <h3 class="fw-bold mb-0 text-success"><?= number_format($stats['approved']) ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Đã ẩn kiểm duyệt</span>
                    <h3 class="fw-bold mb-0 text-secondary"><?= number_format($stats['hidden']) ?></h3>
                </div>
                <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle">
                    <i class="bi bi-eye-slash-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Điểm hài lòng TB</span>
                    <h3 class="fw-bold mb-0 text-warning"><i class="bi bi-star-fill me-1"></i><?= number_format($stats['avgRating'], 1) ?> / 5</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ Lọc Tra Cứu -->
<div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-funnel me-2 text-primary"></i>Tra Cứu & Lọc Đánh Giá Khách Hàng
        </h5>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Kiểm duyệt nội dung</span>
    </div>

    <form action="<?= url('admin/reviews') ?>" method="GET" class="row g-3">
        <!-- Ô tìm kiếm từ khóa -->
        <div class="col-lg-5">
            <label class="form-label small fw-semibold text-muted">Từ khóa (Khách hàng / Email / Tên địa điểm / Nội dung)</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên khách, địa danh hoặc nội dung..." value="<?= htmlspecialchars($tuKhoa ?? '') ?>">
            </div>
        </div>

        <!-- Lọc theo trạng thái -->
        <div class="col-sm-6 col-lg-3">
            <label class="form-label small fw-semibold text-muted">Trạng thái hiển thị</label>
            <select name="trang_thai" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="da_duyet" <?= ($trangThai ?? '') === 'da_duyet' ? 'selected' : '' ?>>Đã duyệt (Công khai)</option>
                <option value="da_an" <?= ($trangThai ?? '') === 'da_an' ? 'selected' : '' ?>>Đã ẩn</option>
            </select>
        </div>

        <!-- Lọc theo số sao -->
        <div class="col-sm-6 col-lg-2">
            <label class="form-label small fw-semibold text-muted">Xếp hạng sao</label>
            <select name="so_sao" class="form-select">
                <option value="">-- Tất cả mức sao --</option>
                <option value="5" <?= ($soSao ?? '') === '5' ? 'selected' : '' ?>>5 sao (Tuyệt vời)</option>
                <option value="4" <?= ($soSao ?? '') === '4' ? 'selected' : '' ?>>4 sao (Rất tốt)</option>
                <option value="3" <?= ($soSao ?? '') === '3' ? 'selected' : '' ?>>3 sao (Bình thường)</option>
                <option value="2" <?= ($soSao ?? '') === '2' ? 'selected' : '' ?>>2 sao (Kém)</option>
                <option value="1" <?= ($soSao ?? '') === '1' ? 'selected' : '' ?>>1 sao (Tệ)</option>
            </select>
        </div>

        <!-- Nút lọc & Đặt lại -->
        <div class="col-lg-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-pill">
                <i class="bi bi-funnel me-1"></i>Lọc
            </button>
            <?php if (!empty($tuKhoa) || !empty($trangThai) || !empty($soSao)): ?>
                <a href="<?= url('admin/reviews') ?>" class="btn btn-outline-secondary rounded-pill" title="Đặt lại bộ lọc">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Bảng Danh Sách Đánh Giá -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-chat-quote-fill me-2 text-warning"></i>Danh Sách Đánh Giá (<?= number_format($totalReviews ?? count($reviews)) ?>)
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4" style="width: 60px;">#ID</th>
                    <th style="min-width: 160px;">Khách hàng</th>
                    <th style="min-width: 180px;">Địa điểm</th>
                    <th style="width: 110px;">Xếp hạng</th>
                    <th style="min-width: 260px;">Nội dung nhận xét</th>
                    <th style="width: 120px;">Thời gian</th>
                    <th style="width: 110px;">Trạng thái</th>
                    <th class="pe-4 text-end" style="min-width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Không tìm thấy đánh giá nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">#<?= $rev['id'] ?></td>
                            <td>
                                <strong class="text-dark d-block"><?= htmlspecialchars($rev['ho_ten']) ?></strong>
                                <span class="small text-muted"><?= htmlspecialchars($rev['email']) ?></span>
                            </td>
                            <td>
                                <a href="<?= url('dia-diem/' . $rev['dia_diem_slug']) ?>" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                    <?= htmlspecialchars($rev['ten_dia_diem']) ?> <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            </td>
                            <td>
                                <div><?= \App\Helpers\FormatHelper::saoHtml((int)$rev['so_sao']) ?></div>
                                <span class="badge bg-warning-subtle text-warning-emphasis fw-bold" style="font-size: 11px;">
                                    <?= (int)$rev['so_sao'] ?> / 5 sao
                                </span>
                            </td>
                            <td>
                                <p class="text-secondary small mb-1 lh-base"><?= nl2br(htmlspecialchars($rev['noi_dung'])) ?></p>
                                <?php 
                                $adminPhotos = !empty($rev['hinh_anh']) ? json_decode($rev['hinh_anh'], true) : [];
                                if (!empty($adminPhotos) && is_array($adminPhotos)):
                                ?>
                                    <div class="d-flex gap-1 mt-1 flex-wrap align-items-center">
                                        <span class="badge bg-light text-secondary border small me-1"><i class="bi bi-camera me-1"></i><?= count($adminPhotos) ?> ảnh:</span>
                                        <?php foreach ($adminPhotos as $ap): ?>
                                            <a href="<?= url(htmlspecialchars($ap)) ?>" target="_blank" title="Xem ảnh đính kèm của khách">
                                                <img src="<?= url(htmlspecialchars($ap)) ?>" alt="Ảnh đính kèm" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px;" class="border shadow-sm">
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('d/m/Y', strtotime($rev['ngay_tao'])) ?><br>
                                <span style="font-size: 11px;"><?= date('H:i', strtotime($rev['ngay_tao'])) ?></span>
                            </td>
                            <td>
                                <?php if ($rev['trang_thai'] === 'da_duyet'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                        <i class="bi bi-check-circle me-1"></i>Công khai
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill small">
                                        <i class="bi bi-eye-slash me-1"></i>Đã ẩn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <?php if ($rev['trang_thai'] === 'da_duyet'): ?>
                                        <form action="<?= url('admin/reviews/update-status') ?>" method="POST" class="d-inline" onsubmit="return confirm('Ẩn nhận xét này khỏi trang công khai?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$rev['id'] ?>">
                                            <input type="hidden" name="trang_thai" value="da_an">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" title="Ẩn nhận xét">
                                                <i class="bi bi-eye-slash me-1"></i>Ẩn
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= url('admin/reviews/update-status') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$rev['id'] ?>">
                                            <input type="hidden" name="trang_thai" value="da_duyet">
                                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-2 py-1" title="Duyệt hiển thị">
                                                <i class="bi bi-check-lg me-1"></i>Duyệt
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Nút Xóa -->
                                    <form action="<?= url('admin/reviews/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn nhận xét #<?= $rev['id'] ?>?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$rev['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" title="Xóa vĩnh viễn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân Trang Dữ Liệu -->
    <div class="pb-3">
        <?= phan_trang($totalReviews ?? count($reviews), $perPage ?? 10, $page ?? 1, 'admin/reviews', $_GET) ?>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>


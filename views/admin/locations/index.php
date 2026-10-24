<?php
/**
 * @var string $title
 * @var array $locations
 * @var array $categories
 * @var string|null $tuKhoa
 * @var string|null $loai
 * @var int|null $danhMucId
 * @var int|null $page
 * @var int|null $perPage
 * @var int|null $totalLocations
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';
?>

<!-- Thanh Công Cụ & Thêm Mới -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Quản Lý Địa Điểm</h4>
        <p class="text-muted small mb-0">Quản lý danh sách các điểm du lịch tham quan và quán ăn đặc sản Nha Trang</p>
    </div>
    <a href="<?= url('admin/locations/create') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm Địa Điểm
    </a>
</div>

<!-- Bộ Lọc Tìm Kiếm -->
<div class="card border-0 shadow-sm p-3 mb-4 rounded-4">
    <form action="<?= url('admin/locations') ?>" method="GET" class="row g-3">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên địa điểm, địa chỉ..." value="<?= htmlspecialchars($tuKhoa ?? '') ?>">
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <select name="loai" class="form-select">
                <option value="">-- Tất cả phân loại --</option>
                <option value="am_thuc" <?= ($loai ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực & Quán ăn</option>
                <option value="du_lich" <?= ($loai ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch & Tham quan</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-2">
            <select name="danh_muc_id" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($danhMucId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['ten_danh_muc']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-pill">Lọc</button>
            <?php if (!empty($tuKhoa) || !empty($loai) || !empty($danhMucId)): ?>
                <a href="<?= url('admin/locations') ?>" class="btn btn-outline-secondary rounded-pill" title="Đặt lại">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Bảng Danh Sách Địa Điểm -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-dark">Danh Sách Địa Điểm (<?= number_format($totalLocations ?? count($locations)) ?> điểm)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Tên Địa Điểm</th>
                    <th>Danh Mục</th>
                    <th>Địa Chỉ</th>
                    <th>Khoảng Giá</th>
                    <th>Vé Trực Tuyến</th>
                    <th>Trạng Thái</th>
                    <th class="text-end">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($locations)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Chưa tìm thấy địa điểm nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($locations as $loc): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars(hinh_anh($loc['anh_dai_dien'])) ?>"
                                    alt="<?= htmlspecialchars($loc['ten_dia_diem']) ?>"
                                    class="rounded-3 shadow-sm object-fit-cover"
                                    style="width: 55px; height: 55px;">
                            </td>
                            <td>
                                <strong class="text-dark fs-6"><?= htmlspecialchars($loc['ten_dia_diem']) ?></strong><br>
                                <span class="text-muted small">
                                    <i class="bi bi-geo me-1"></i><?= $loc['vi_do'] ?>, <?= $loc['kinh_do'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $loc['loai_danh_muc'] === 'am_thuc' ? 'bg-warning text-dark' : 'bg-info text-dark' ?>">
                                    <?= htmlspecialchars($loc['ten_danh_muc']) ?>
                                </span>
                            </td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <?= htmlspecialchars($loc['dia_chi']) ?>
                            </td>
                            <td class="small">
                                <span class="fw-semibold text-danger">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$loc['gia_thap_nhat']) ?>
                                </span>
                                <?php if ($loc['gia_cao_nhat'] > $loc['gia_thap_nhat']): ?>
                                    <br><span class="text-muted">~ <?= \App\Helpers\FormatHelper::tienTe((float)$loc['gia_cao_nhat']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($loc['co_ban_ve']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-ticket-perforated me-1"></i>Có bán vé
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">Không vé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($loc['trang_thai'] === 'hien_thi'): ?>
                                    <span class="badge bg-success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Đã ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= url('dia-diem/' . $loc['duong_dan']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill" title="Xem trên web">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= url('admin/locations/edit/' . $loc['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= url('admin/locations/delete/' . $loc['id']) ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hoặc ẩn địa điểm này?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Xóa / Ẩn">
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
        <?= phan_trang($totalLocations ?? count($locations), $perPage ?? 10, $page ?? 1, 'admin/locations', $_GET) ?>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
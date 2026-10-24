<?php
/**
 * Quản Lý Danh Mục (Admin)
 * @var string $title
 * @var array $categories
 * @var array $stats
 * @var string|null $tuKhoa
 * @var string|null $loai
 * @var int $page
 * @var int $perPage
 * @var int $totalCategories
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';

$totalPages = ceil($totalCategories / $perPage);
?>

<!-- Tiêu Đề & Thao Tác Thêm Mới -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-tags-fill me-2 text-primary"></i>Quản Lý Danh Mục</h4>
        <p class="text-muted small mb-0">Thiết lập các nhóm trải nghiệm ẩm thực và du lịch biển đảo tại Nha Trang</p>
    </div>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i>Thêm Danh Mục Mới
    </a>
</div>

<!-- 4 Thẻ Thống Kê Nhanh (KPIs) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng Danh Mục</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($stats['total'] ?? 0) ?></h3>
                </div>
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-tags fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Danh Mục Ẩm Thực</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1"><?= number_format($stats['foodCount'] ?? 0) ?></h3>
                </div>
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cup-hot-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Danh Mục Du Lịch</span>
                    <h3 class="fw-bold text-info mb-0 mt-1"><?= number_format($stats['travelCount'] ?? 0) ?></h3>
                </div>
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info">
                    <i class="bi bi-compass-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Độ Phủ Hệ Thống</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">100%</h3>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ Lọc Tìm Kiếm Danh Mục -->
<div class="card border-0 shadow-sm p-3 mb-4 rounded-4 bg-white">
    <form action="<?= url('admin/categories') ?>" method="GET" class="row g-3">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên danh mục, đường dẫn slug..." value="<?= htmlspecialchars($tuKhoa ?? '') ?>">
            </div>
        </div>

        <div class="col-md-3">
            <select name="loai" class="form-select">
                <option value="">-- Tất cả phân loại --</option>
                <option value="am_thuc" <?= ($loai ?? '') === 'am_thuc' ? 'selected' : '' ?>>Ẩm thực &amp; Món ngon</option>
                <option value="du_lich" <?= ($loai ?? '') === 'du_lich' ? 'selected' : '' ?>>Du lịch &amp; Trải nghiệm</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-pill">
                <i class="bi bi-funnel me-1"></i>Lọc
            </button>
            <?php if (!empty($tuKhoa) || !empty($loai)): ?>
                <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary rounded-pill" title="Xóa bộ lọc">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Bảng Danh Sách Danh Mục -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 70px;">ID</th>
                    <th style="width: 80px;">Icon</th>
                    <th>Tên Danh Mục &amp; Slug</th>
                    <th>Phân Loại</th>
                    <th>Địa Điểm Trực Thuộc</th>
                    <th>Lượt Xem Gắn Kèm</th>
                    <th class="text-end pe-4" style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Không tìm thấy danh mục nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="ps-4 text-muted fw-bold">#<?= $cat['id'] ?></td>
                            <td>
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm <?= ($cat['loai_danh_muc'] === 'am_thuc') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-info bg-opacity-10 text-info' ?>" style="width: 44px; height: 44px;">
                                    <i class="bi <?= htmlspecialchars($cat['bieu_tuong'] ?: 'bi-tag') ?> fs-5"></i>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($cat['ten_danh_muc']) ?></div>
                                <code class="small text-muted">/locations?danh_muc_id=<?= $cat['id'] ?></code> &bull;
                                <span class="badge bg-light text-secondary border small"><?= htmlspecialchars($cat['duong_dan']) ?></span>
                            </td>
                            <td>
                                <?php if ($cat['loai_danh_muc'] === 'am_thuc'): ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                        <i class="bi bi-cup-hot me-1"></i>Ẩm thực
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1">
                                        <i class="bi bi-compass me-1"></i>Du lịch
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url('admin/locations?danh_muc_id=' . $cat['id']) ?>" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none rounded-pill px-3 py-2 fw-bold" title="Xem các địa điểm thuộc danh mục này">
                                    <i class="bi bi-geo-alt me-1"></i><?= number_format((int)($cat['so_dia_diem'] ?? 0)) ?> địa điểm
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-eye me-1"></i><?= number_format((int)($cat['tong_luot_xem'] ?? 0)) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('admin/categories/edit/' . $cat['id']) ?>" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDeleteCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['ten_danh_muc'])) ?>', <?= (int)($cat['so_dia_diem'] ?? 0) ?>)" 
                                            title="Xóa danh mục">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân Trang -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="text-muted small">
                    Hiển thị <strong><?= count($categories) ?></strong> / <strong><?= $totalCategories ?></strong> danh mục
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('admin/categories?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">Trước</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('admin/categories?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('admin/categories?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Form Xóa Ẩn (CSRF Protected) -->
<form id="deleteCategoryForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
</form>

<script>
function confirmDeleteCategory(id, name, locationCount) {
    if (locationCount > 0) {
        alert("KHÔNG THỂ XÓA!\n\nDanh mục '" + name + "' đang có " + locationCount + " địa điểm trực thuộc.\nVui lòng chuyển hoặc xóa các địa điểm này trước khi thực hiện xóa danh mục.");
        return;
    }
    
    if (confirm("Bạn có chắc chắn muốn xóa vĩnh viễn danh mục '" + name + "'?\nThao tác này không thể hoàn tác.")) {
        const form = document.getElementById('deleteCategoryForm');
        form.action = "<?= url('admin/categories/delete/') ?>" + id;
        form.submit();
    }
}
</script>


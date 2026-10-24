<?php
/**
 * Form Thêm Mới / Chỉnh Sửa Danh Mục (Admin)
 * @var string $title
 * @var string $mode ('create'|'edit')
 * @var array|null $category
 * @var int|null $locationCount
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';

$isEdit = ($mode === 'edit');
$actionUrl = $isEdit ? url('admin/categories/edit/' . $category['id']) : url('admin/categories/create');
$currentIcon = $category['bieu_tuong'] ?? 'bi-tag';
$currentLoai = $category['loai_danh_muc'] ?? 'du_lich';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Breadcrumb & Tiêu Đề -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= url('admin/categories') ?>" class="text-decoration-none">Danh Mục</a></li>
                        <li class="breadcrumb-item active"><?= $isEdit ? 'Chỉnh Sửa' : 'Thêm Mới' ?></li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle-fill' ?> me-2 text-primary"></i>
                    <?= $isEdit ? 'Chỉnh Sửa Danh Mục: ' . htmlspecialchars($category['ten_danh_muc']) : 'Thêm Mới Danh Mục Trải Nghiệm' ?>
                </h4>
            </div>
            <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

        <?php if ($isEdit && isset($locationCount) && $locationCount > 0): ?>
            <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                <div class="small">
                    Danh mục này đang được liên kết với <strong><?= $locationCount ?> địa điểm</strong> trong hệ thống.
                    Việc đổi tên hoặc icon sẽ tự động cập nhật đồng bộ trên trang chủ và trang lọc của khách hàng.
                </div>
            </div>
        <?php endif; ?>

        <!-- Khung Form Chính -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <form action="<?= $actionUrl ?>" method="POST" id="categoryForm">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <!-- 1. Tên danh mục -->
                <div class="mb-4">
                    <label for="ten_danh_muc" class="form-label fw-bold text-dark">
                        Tên Danh Mục <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg rounded-3" 
                           id="ten_danh_muc" 
                           name="ten_danh_muc" 
                           placeholder="Ví dụ: Lặn ngắm san hô &amp; Thể thao nước, Quán hải sản bờ kè..."
                           value="<?= htmlspecialchars($category['ten_danh_muc'] ?? '') ?>" 
                           required 
                           oninput="autoGenerateSlug(this.value)">
                    <div class="form-text">Tên hiển thị trên menu điều hướng và bộ lọc khám phá của du khách.</div>
                </div>

                <!-- 2. Đường dẫn tĩnh (Slug) -->
                <div class="mb-4">
                    <label for="duong_dan" class="form-label fw-bold text-dark">
                        Đường Dẫn URL (Slug) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted">/locations?slug=</span>
                        <input type="text" 
                               class="form-control rounded-end-3" 
                               id="duong_dan" 
                               name="duong_dan" 
                               placeholder="lan-ngam-san-ho-the-thao-nuoc" 
                               value="<?= htmlspecialchars($category['duong_dan'] ?? '') ?>" 
                               required>
                    </div>
                    <div class="form-text">Đường dẫn thân thiện SEO (viết thường không dấu, nối nhau bằng dấu gạch ngang).</div>
                </div>

                <!-- 3. Phân loại danh mục -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark d-block">
                        Phân Loại Trải Nghiệm <span class="text-danger">*</span>
                    </label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="card p-3 border rounded-3 h-100 cursor-pointer <?= ($currentLoai === 'du_lich') ? 'border-primary bg-primary bg-opacity-10' : '' ?>" id="label_du_lich" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="loai_danh_muc" value="du_lich" class="form-check-input mt-0" <?= ($currentLoai === 'du_lich') ? 'checked' : '' ?> onchange="updateLoaiStyle()">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-compass text-info me-1"></i>Du Lịch &amp; Tham Quan</div>
                                        <div class="text-muted small">Di tích, biển đảo, khu vui chơi, công viên, danh lam...</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="card p-3 border rounded-3 h-100 cursor-pointer <?= ($currentLoai === 'am_thuc') ? 'border-danger bg-danger bg-opacity-10' : '' ?>" id="label_am_thuc" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="loai_danh_muc" value="am_thuc" class="form-check-input mt-0" <?= ($currentLoai === 'am_thuc') ? 'checked' : '' ?> onchange="updateLoaiStyle()">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-cup-hot text-danger me-1"></i>Ẩm Thực &amp; Đặc Sản</div>
                                        <div class="text-muted small">Hải sản tươi sống, nem nướng, bún cá, đồ ăn vặt...</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4. Biểu tượng Bootstrap Icon -->
                <div class="mb-4">
                    <label for="bieu_tuong" class="form-label fw-bold text-dark">
                        Biểu Tượng (Bootstrap Icon Class)
                    </label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-light border shadow-sm d-flex align-items-center justify-content-center text-primary" style="width: 54px; height: 54px; flex-shrink: 0;" id="iconPreviewBox">
                            <i class="bi <?= htmlspecialchars($currentIcon) ?> fs-3" id="iconPreview"></i>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" 
                                   class="form-control rounded-3" 
                                   id="bieu_tuong" 
                                   name="bieu_tuong" 
                                   placeholder="bi-compass, bi-water, bi-cup-hot..." 
                                   value="<?= htmlspecialchars($currentIcon) ?>" 
                                   oninput="updateIconPreview(this.value)">
                        </div>
                    </div>

                    <!-- Gợi ý biểu tượng phổ biến một chạm -->
                    <div class="bg-light p-3 rounded-3 border">
                        <span class="small fw-bold text-muted d-block mb-2">
                            <i class="bi bi-magic me-1 text-warning"></i>Chọn nhanh icon gợi ý phổ biến:
                        </span>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $presetIcons = [
                                'bi-water' => 'Nước / Biển',
                                'bi-compass' => 'La bàn / Tour',
                                'bi-cup-hot' => 'Ẩm thực / Cà phê',
                                'bi-egg-fried' => 'Món ăn / Đặc sản',
                                'bi-bank' => 'Di tích / Bảo tàng',
                                'bi-sun' => 'Vui chơi / Ánh nắng',
                                'bi-tree' => 'Thiên nhiên / Sinh thái',
                                'bi-camera' => 'Check-in / Chụp ảnh',
                                'bi-shop' => 'Chợ / Mua sắm',
                                'bi-tsunami' => 'Sóng biển / Thể thao',
                                'bi-bicycle' => 'Khám phá / Dạo phố',
                                'bi-flower1' => 'Thư giãn / Spa khoáng'
                            ];
                            foreach ($presetIcons as $ico => $desc):
                            ?>
                                <button type="button" 
                                        class="btn btn-sm btn-white bg-white border rounded-pill d-flex align-items-center gap-1 shadow-sm px-3 py-1" 
                                        onclick="pickIcon('<?= $ico ?>')">
                                    <i class="bi <?= $ico ?> text-primary"></i>
                                    <span class="small text-secondary"><?= $desc ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Nút Lưu / Hủy -->
                <div class="pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                    <a href="<?= url('admin/categories') ?>" class="btn btn-light rounded-pill px-4">
                        Hủy Bỏ
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Lưu Thay Đổi' : 'Thêm Mới Danh Mục' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function autoGenerateSlug(text) {
    <?php if (!$isEdit): ?>
    let slug = text.toLowerCase()
        .replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
        .replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
        .replace(/ì|í|ị|ỉ|ĩ/g, 'i')
        .replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
        .replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
        .replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('duong_dan').value = slug;
    <?php endif; ?>
}

function updateIconPreview(iconClass) {
    const iconEl = document.getElementById('iconPreview');
    iconEl.className = 'bi fs-3 ' + (iconClass.trim() || 'bi-tag');
}

function pickIcon(iconClass) {
    document.getElementById('bieu_tuong').value = iconClass;
    updateIconPreview(iconClass);
}

function updateLoaiStyle() {
    const isAmThuc = document.querySelector('input[name="loai_danh_muc"]:checked').value === 'am_thuc';
    const labelAmThuc = document.getElementById('label_am_thuc');
    const labelDuLich = document.getElementById('label_du_lich');

    if (isAmThuc) {
        labelAmThuc.className = 'card p-3 border rounded-3 h-100 cursor-pointer border-danger bg-danger bg-opacity-10';
        labelDuLich.className = 'card p-3 border rounded-3 h-100 cursor-pointer';
    } else {
        labelDuLich.className = 'card p-3 border rounded-3 h-100 cursor-pointer border-primary bg-primary bg-opacity-10';
        labelAmThuc.className = 'card p-3 border rounded-3 h-100 cursor-pointer';
    }
}
</script>


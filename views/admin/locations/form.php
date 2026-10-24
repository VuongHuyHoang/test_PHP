<?php
/**
 * @var string $title
 * @var string $mode
 * @var array|null $location
 * @var array $categories
 * @var array $gallery
 * @var array $errors
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';

$isEdit = ($mode === 'edit');
$actionUrl = $isEdit ? url('admin/locations/edit/' . $location['id']) : url('admin/locations/create');
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2 text-primary"></i>
            <?= $isEdit ? 'Chỉnh Sửa Địa Điểm: ' . htmlspecialchars($location['ten_dia_diem']) : 'Thêm Địa Điểm Mới' ?>
        </h4>
        <p class="text-muted small mb-0">Cung cấp đầy đủ thông tin vị trí tọa độ GPS và hình ảnh để hiển thị trên bản đồ tương tác</p>
    </div>
    <a href="<?= url('admin/locations') ?>" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <!-- Cột Thông Tin Cơ Bản (Bên trái) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thông Tin Chung</h5>

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Tên địa điểm <span class="text-danger">*</span></label>
                        <input type="text" name="ten_dia_diem" class="form-control" required 
                               value="<?= htmlspecialchars($location['ten_dia_diem'] ?? '') ?>" 
                               placeholder="VD: VinWonders Nha Trang, Nem Nướng Đặng Văn Quyên...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Danh mục <span class="text-danger">*</span></label>
                        <select name="danh_muc_id" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (($location['danh_muc_id'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                                    [<?= $cat['loai_danh_muc'] === 'am_thuc' ? 'Ẩm thực' : 'Du lịch' ?>] <?= htmlspecialchars($cat['ten_danh_muc']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($isEdit): ?>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">Đường dẫn tĩnh (Slug URL)</label>
                            <input type="text" name="duong_dan" class="form-control" 
                                   value="<?= htmlspecialchars($location['duong_dan'] ?? '') ?>">
                            <div class="form-text small">Để trống nếu muốn hệ thống tự động sinh từ tên địa điểm.</div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="dia_chi" class="form-control" required 
                               value="<?= htmlspecialchars($location['dia_chi'] ?? '') ?>" 
                               placeholder="VD: Đảo Hòn Tre, Vĩnh Nguyên, Nha Trang, Khánh Hòa">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Vĩ độ GPS (Latitude) <span class="text-danger">*</span></label>
                        <input type="number" step="0.00000001" name="vi_do" class="form-control" required 
                               value="<?= htmlspecialchars($location['vi_do'] ?? '12.247500') ?>" placeholder="12.2xxxx">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Kinh độ GPS (Longitude) <span class="text-danger">*</span></label>
                        <input type="number" step="0.00000001" name="kinh_do" class="form-control" required 
                               value="<?= htmlspecialchars($location['kinh_do'] ?? '109.196700') ?>" placeholder="109.1xxxx">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Khoảng giá thấp nhất (VNĐ)</label>
                        <input type="number" step="1000" name="gia_thap_nhat" class="form-control" 
                               value="<?= htmlspecialchars($location['gia_thap_nhat'] ?? 0) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Khoảng giá cao nhất (VNĐ)</label>
                        <input type="number" step="1000" name="gia_cao_nhat" class="form-control" 
                               value="<?= htmlspecialchars($location['gia_cao_nhat'] ?? 0) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Giờ mở cửa</label>
                        <input type="time" name="gio_mo_cua" class="form-control" 
                               value="<?= htmlspecialchars($location['gio_mo_cua'] ?? '08:00') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Giờ đóng cửa</label>
                        <input type="time" name="gio_dong_cua" class="form-control" 
                               value="<?= htmlspecialchars($location['gio_dong_cua'] ?? '21:00') ?>">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Nội Dung Giới Thiệu</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mô tả ngắn (Hiển thị ở danh sách & popup bản đồ)</label>
                    <textarea name="mo_ta_ngan" rows="3" class="form-control" 
                              placeholder="Tóm tắt điểm đặc sắc nhất..."><?= htmlspecialchars($location['mo_ta_ngan'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nội dung chi tiết (Bài viết review chi tiết)</label>
                    <textarea name="noi_dung_chi_tiet" rows="8" class="form-control" 
                              placeholder="Chi tiết kinh nghiệm, lịch sử, menu món ăn, hướng dẫn tham quan..."><?= htmlspecialchars($location['noi_dung_chi_tiet'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Cột Ảnh & Cài Đặt (Bên phải) -->
        <div class="col-lg-4">
            <!-- Cài đặt trạng thái -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Cài Đặt Dịch Vụ</h6>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="coBanVe" name="co_ban_ve" value="1" 
                           <?= !empty($location['co_ban_ve']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold small" for="coBanVe">
                        Kích hoạt bán vé E-Voucher trực tuyến
                    </label>
                    <div class="form-text small">Nếu bật, địa điểm sẽ xuất hiện nút "Đặt Vé Ngay" và có thể gắn các Gói vé.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Trạng thái hiển thị</label>
                    <select name="trang_thai" class="form-select">
                        <option value="hien_thi" <?= (($location['trang_thai'] ?? '') === 'hien_thi') ? 'selected' : '' ?>>Hiển thị công khai</option>
                        <option value="an" <?= (($location['trang_thai'] ?? '') === 'an') ? 'selected' : '' ?>>Tạm ẩn</option>
                    </select>
                </div>
            </div>

            <!-- Ảnh đại diện -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Ảnh Đại Diện</h6>

                <?php if ($isEdit && !empty($location['anh_dai_dien'])): ?>
                    <div class="mb-3 text-center">
                        <img src="<?= htmlspecialchars(hinh_anh($location['anh_dai_dien'])) ?>" 
                             alt="Ảnh hiện tại" class="img-fluid rounded-3 shadow-sm" style="max-height: 180px; object-fit: cover;">
                        <div class="small text-muted mt-1">Ảnh đại diện hiện tại</div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tải lên file ảnh mới</label>
                    <input type="file" name="anh_dai_dien" class="form-control" accept="image/*">
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-semibold">Hoặc nhập URL ảnh trực tiếp</label>
                    <input type="url" name="anh_dai_dien_url" class="form-control" placeholder="https://..." 
                           value="<?= ($isEdit && str_starts_with($location['anh_dai_dien'] ?? '', 'http')) ? htmlspecialchars($location['anh_dai_dien']) : '' ?>">
                </div>
            </div>

            <!-- Thư viện ảnh phụ (Gallery) -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Thư Viện Ảnh (Gallery)</h6>

                <?php if ($isEdit && !empty($gallery)): ?>
                    <div class="row g-2 mb-3">
                        <?php foreach ($gallery as $img): ?>
                            <div class="col-4 position-relative">
                                <img src="<?= htmlspecialchars(hinh_anh($img['duong_dan_anh'])) ?>" 
                                     alt="Gallery" class="rounded w-100 object-fit-cover" style="height: 70px;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label small fw-semibold">Tải thêm nhiều ảnh</label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    <div class="form-text small">Có thể chọn nhiều file ảnh cùng lúc (JPG, PNG, WebP).</div>
                </div>
            </div>

            <!-- Nút bấm lưu -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm fs-6">
                    <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Lưu Thay Đổi' : 'Thêm Địa Điểm' ?>
                </button>
                <a href="<?= url('admin/locations') ?>" class="btn btn-outline-secondary py-2 rounded-pill">Hủy bỏ</a>
            </div>
        </div>
    </div>
</form>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>


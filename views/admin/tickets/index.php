<?php require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-ticket-detailed-fill me-2 text-primary"></i>Quản Lý Gói Vé & Hạn Mức Tồn Kho
        </h4>
        <p class="text-muted small mb-0">Thiết lập các gói dịch vụ bán vé tham quan và giới hạn số lượng vé phát hành theo từng ngày</p>
    </div>
</div>

<div class="row g-4">
    <!-- Cột 1: Quản lý Gói Vé (Bên trái) -->
    <div class="col-lg-6">
        <!-- Card Thêm Gói Vé Mới -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-plus-circle me-2 text-success"></i>Thêm Gói Vé Mới
            </h5>

            <form action="<?= url('admin/tickets/create-package') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Địa điểm áp dụng <span class="text-danger">*</span></label>
                    <select name="dia_diem_id" class="form-select" required>
                        <option value="">-- Chọn địa điểm có bán vé --</option>
                        <?php foreach ($ticketLocations as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['ten_dia_diem']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tên gói vé <span class="text-danger">*</span></label>
                    <input type="text" name="ten_goi_ve" class="form-control" required placeholder="VD: Vé Tiêu Chuẩn Cáp Treo 2 Chiều...">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Giá người lớn (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" step="1000" name="gia_nguoi_lon" class="form-control" required value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Giá trẻ em (VNĐ)</label>
                        <input type="number" step="1000" name="gia_tre_em" class="form-control" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Mô tả quyền lợi</label>
                    <textarea name="mo_ta" rows="2" class="form-control" placeholder="Bao gồm: Cáp treo vượt biển, trò chơi cảm giác mạnh..."></textarea>
                </div>

                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold w-100">
                    <i class="bi bi-plus-lg me-1"></i>Lưu Gói Vé
                </button>
            </form>
        </div>

        <!-- Danh sách gói vé hiện có -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark">Danh Sách Gói Vé (<?= count($packages) ?> gói)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tên Gói Vé / Địa Điểm</th>
                            <th class="text-end">Đơn Giá</th>
                            <th class="text-center">Trạng Thái</th>
                            <th class="text-end">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($packages)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Chưa có gói vé nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($packages as $pkg): ?>
                                <tr>
                                    <td>
                                        <strong class="text-dark"><?= htmlspecialchars($pkg['ten_goi_ve']) ?></strong><br>
                                        <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($pkg['ten_dia_diem']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="text-primary fw-semibold">NL: <?= \App\Helpers\FormatHelper::tienTe((float)$pkg['gia_nguoi_lon']) ?></div>
                                        <div class="text-secondary small">TE: <?= \App\Helpers\FormatHelper::tienTe((float)$pkg['gia_tre_em']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $pkg['trang_thai'] === 'dang_ban' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $pkg['trang_thai'] === 'dang_ban' ? 'Đang bán' : 'Tạm dừng' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="<?= url('admin/tickets/delete-package') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn tạm dừng / xóa gói vé này?');" class="d-inline">
                                            <input type="hidden" name="id" value="<?= (int)$pkg['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Tạm dừng/Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cột 2: Cấu Hình Hạn Mức Tồn Kho Theo Ngày (Bên phải) -->
    <div class="col-lg-6">
        <!-- Form Thiết lập hạn mức -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-calendar-check me-2 text-primary"></i>Thiết Lập Hạn Mức Vé Theo Ngày
            </h5>

            <form action="<?= url('admin/tickets/set-inventory') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Chọn gói vé áp dụng <span class="text-danger">*</span></label>
                    <select name="goi_ve_id" class="form-select" required>
                        <option value="">-- Chọn gói vé --</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?= $pkg['id'] ?>">
                                <?= htmlspecialchars($pkg['ten_dia_diem']) ?> - <?= htmlspecialchars($pkg['ten_goi_ve']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Ngày sử dụng <span class="text-danger">*</span></label>
                        <input type="date" name="ngay_su_dung" class="form-control" required value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tổng số vé phát hành <span class="text-danger">*</span></label>
                        <input type="number" name="tong_so_ve" class="form-control" required value="500" min="1">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold w-100">
                    <i class="bi bi-save me-1"></i>Lưu Hạn Mức Tồn Kho
                </button>
            </form>
        </div>

        <!-- Bảng Danh Sách Hạn Mức Tồn Kho -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Hạn Mức Theo Ngày Gần Đây</h5>
                <form action="<?= url('admin/tickets') ?>" method="GET" class="d-flex gap-2">
                    <input type="date" name="ngay" class="form-control form-control-sm" value="<?= htmlspecialchars($filterNgay ?? '') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">Lọc</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày Sử Dụng</th>
                            <th>Gói Vé / Địa Điểm</th>
                            <th class="text-center">Đã Bán / Tổng</th>
                            <th class="text-end">Vé Còn Lại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventories)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu hạn mức tồn kho.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventories as $inv): ?>
                                <?php 
                                    $daBan = (int)$inv['tong_so_ve'] - (int)$inv['so_ve_con_lai']; 
                                    $phanTram = ($inv['tong_so_ve'] > 0) ? round(($daBan / $inv['tong_so_ve']) * 100) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <strong class="text-dark"><?= \App\Helpers\FormatHelper::ngayThang($inv['ngay_su_dung']) ?></strong>
                                    </td>
                                    <td>
                                        <strong class="text-primary"><?= htmlspecialchars($inv['ten_goi_ve']) ?></strong><br>
                                        <span class="text-muted small"><?= htmlspecialchars($inv['ten_dia_diem']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold"><?= $daBan ?></span> / <?= $inv['tong_so_ve'] ?>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar bg-info" style="width: <?= $phanTram ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge <?= $inv['so_ve_con_lai'] > 50 ? 'bg-success' : ($inv['so_ve_con_lai'] > 0 ? 'bg-warning text-dark' : 'bg-danger') ?> fs-6">
                                            <?= number_format($inv['so_ve_con_lai']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>

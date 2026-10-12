<?php require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php'; ?>

<!-- Bộ Lọc Tra Cứu Đơn Hàng Tại Quầy -->
<div class="card border-0 shadow-sm p-3 mb-4 rounded-3">
    <form action="<?= url('admin/orders') ?>" method="GET" class="row g-3">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Tra cứu nhanh theo Mã đơn hàng (VD: NT26...) hoặc Số điện thoại khách..." value="<?= htmlspecialchars($tuKhoa ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="bi bi-search me-1"></i>Tra Cứu
            </button>
            <?php if (!empty($tuKhoa)): ?>
                <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary">Xóa</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Danh Sách Đơn Hàng -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-dark">Danh Sách Đơn Đặt Vé (<?= count($orders) ?> đơn)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Khách Hàng</th>
                    <th>Số Điện Thoại</th>
                    <th>Tổng Tiền</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái Đơn</th>
                    <th>Ngày Đặt</th>
                    <th class="text-end">Soát Vé / Cập Nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($o['ma_don_hang']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($o['ten_khach_hang']) ?><br>
                                <span class="text-muted small"><?= htmlspecialchars($o['email_khach_hang']) ?></span>
                            </td>
                            <td><strong><?= htmlspecialchars($o['sdt_khach_hang']) ?></strong></td>
                            <td class="fw-bold text-dark"><?= \App\Helpers\FormatHelper::tienTe((float)$o['tong_tien']) ?></td>
                            <td>
                                <span class="badge <?= $o['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= htmlspecialchars($o['trang_thai_thanh_toan']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $o['trang_thai_don_hang'] === 'hoan_thanh' ? 'bg-success' : ($o['trang_thai_don_hang'] === 'da_huy' ? 'bg-danger' : 'bg-info text-dark') ?>">
                                    <?= htmlspecialchars($o['trang_thai_don_hang']) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= \App\Helpers\FormatHelper::ngayThang($o['ngay_tao']) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= url('voucher/' . $o['ma_don_hang']) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Xem chi tiết voucher">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if ($o['trang_thai_don_hang'] !== 'hoan_thanh'): ?>
                                        <form action="<?= url('admin/orders/update-status') ?>" method="POST" onsubmit="return confirm('Xác nhận đã đổi vé vật lý cho khách?');">
                                            <input type="hidden" name="don_hang_id" value="<?= (int)$o['id'] ?>">
                                            <input type="hidden" name="trang_thai" value="hoan_thanh">
                                            <button type="submit" class="btn btn-sm btn-success" title="Xác nhận hoàn thành soát vé">
                                                <i class="bi bi-check2-circle"></i> Hoàn Thành
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>


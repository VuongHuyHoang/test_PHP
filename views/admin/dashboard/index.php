<?php require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php'; ?>

<!-- 4 Thẻ Thống Kê -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng địa điểm</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0"><?= number_format($totalLocations) ?></h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng đơn đặt vé</span>
                    <h3 class="fw-bold text-success mt-1 mb-0"><?= number_format($totalOrders) ?></h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle">
                    <i class="bi bi-ticket-perforated-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Doanh thu đối soát</span>
                    <h3 class="fw-bold text-warning-emphasis mt-1 mb-0"><?= \App\Helpers\FormatHelper::tienTe($totalRevenue) ?></h3>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle">
                    <i class="bi bi-cash-coin fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng người dùng</span>
                    <h3 class="fw-bold text-info mt-1 mb-0"><?= number_format($totalUsers) ?></h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bảng Đơn Hàng Mới Nhất -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h5 class="fw-bold mb-0 text-dark">Đơn Đặt Vé Gần Đây</h5>
        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-primary btn-sm rounded-pill">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Số Điện Thoại</th>
                    <th>Tổng Tiền</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái Đơn</th>
                    <th class="text-end">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào được ghi nhận.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><strong class="text-primary"><?= htmlspecialchars($order['ma_don_hang']) ?></strong></td>
                            <td><?= htmlspecialchars($order['ten_khach_hang']) ?></td>
                            <td><?= htmlspecialchars($order['sdt_khach_hang']) ?></td>
                            <td class="fw-bold"><?= \App\Helpers\FormatHelper::tienTe((float)$order['tong_tien']) ?></td>
                            <td>
                                <span class="badge <?= $order['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= htmlspecialchars($order['trang_thai_thanh_toan']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $order['trang_thai_don_hang'] === 'hoan_thanh' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= htmlspecialchars($order['trang_thai_don_hang']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('voucher/' . $order['ma_don_hang']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Xem Phiếu
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>

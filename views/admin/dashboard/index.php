<?php
/**
 * @var string $title
 * @var int $totalLocations
 * @var int $totalOrders
 * @var float|int $totalRevenue
 * @var float|int $todayRevenue
 * @var int $totalTicketsSold
 * @var int $totalUsers
 * @var array $recentOrders
 * @var string $chartLabels
 * @var string $chartRevenue
 * @var string $chartOrders
 * @var string $chartStatusData
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';
?>

<!-- 4 Thẻ Thống Kê KPI -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng doanh thu</span>
                    <h4 class="fw-bold text-success mt-1 mb-0"><?= \App\Helpers\FormatHelper::tienTe($totalRevenue) ?></h4>
                    <span class="badge bg-success-subtle text-success small mt-2">Hôm nay: <?= \App\Helpers\FormatHelper::tienTe($todayRevenue) ?></span>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Vé đã phát hành</span>
                    <h4 class="fw-bold text-primary mt-1 mb-0"><?= number_format($totalTicketsSold) ?> <small class="fs-6 fw-normal text-muted">vé</small></h4>
                    <span class="badge bg-primary-subtle text-primary small mt-2"><?= number_format($totalOrders) ?> đơn đặt</span>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="bi bi-ticket-perforated-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Địa điểm phục vụ</span>
                    <h4 class="fw-bold text-warning-emphasis mt-1 mb-0"><?= number_format($totalLocations) ?> <small class="fs-6 fw-normal text-muted">điểm</small></h4>
                    <a href="<?= url('admin/locations') ?>" class="small text-decoration-none text-warning-emphasis d-block mt-2">Quản lý danh sách &rarr;</a>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Thành viên đăng ký</span>
                    <h4 class="fw-bold text-info mt-1 mb-0"><?= number_format($totalUsers) ?></h4>
                    <span class="badge bg-info-subtle text-info small mt-2">Khách & Quản trị</span>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Khối Biểu Đồ Thống Kê (Chart.js) -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ Doanh Thu & Đơn Hàng 7 Ngày -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Biến Động Doanh Thu 7 Ngày Gần Nhất</h5>
                <span class="badge bg-light text-secondary border">Đơn vị: VNĐ</span>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Cơ Cấu Trạng Thái Đơn Hàng -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill me-2 text-info"></i>Trạng Thái Đơn Hàng</h5>
            <div style="position: relative; height: 240px;" class="d-flex align-items-center justify-content-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <div class="text-center mt-3 small text-muted">
                Tỷ lệ phân bổ trạng thái đơn đặt vé toàn hệ thống
            </div>
        </div>
    </div>
</div>

<!-- Bảng Đơn Hàng Mới Nhất -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-secondary"></i>Đơn Đặt Vé Gần Đây</h5>
        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Xem tất cả &rarr;</a>
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
                            <td class="fw-bold text-dark"><?= \App\Helpers\FormatHelper::tienTe((float)$order['tong_tien']) ?></td>
                            <td>
                                <span class="badge <?= $order['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= htmlspecialchars($order['trang_thai_thanh_toan']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $order['trang_thai_don_hang'] === 'hoan_thanh' ? 'bg-success' : ($order['trang_thai_don_hang'] === 'da_huy' ? 'bg-danger' : 'bg-info text-dark') ?>">
                                    <?= htmlspecialchars($order['trang_thai_don_hang']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('voucher/' . $order['ma_don_hang']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="bi bi-eye me-1"></i>Xem Phiếu
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN và Khởi Tạo Dữ Liệu -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = <?= $chartLabels ?>;
    const revenueData = <?= $chartRevenue ?>;
    const ordersData = <?= $chartOrders ?>;
    const statusData = <?= $chartStatusData ?>;

    // 1. Line/Bar Chart Doanh Thu
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    backgroundColor: 'rgba(19, 122, 142, 0.75)',
                    borderColor: '#137a8e',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Số đơn thành công',
                    data: ordersData,
                    type: 'line',
                    borderColor: '#e27d60',
                    backgroundColor: '#e27d60',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000).toLocaleString() + 'k';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // 2. Doughnut Chart Trạng Thái Đơn
    const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Mới tạo', 'Đã xác nhận', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#94a3b8',
                    '#38bdf8',
                    '#22c55e',
                    '#ef4444'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>

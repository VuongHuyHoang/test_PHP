<?php
/**
 * Báo Cáo & Phân Tích Thống Kê Hệ Thống (Admin)
 * @var string $title
 * @var string $ky
 * @var string $tuNgay
 * @var string $denNgay
 * @var string $labelPeriod
 * @var int $tongDon
 * @var int $donThanhCong
 * @var int $donHuy
 * @var float $doanhThu
 * @var float $giaTriDonTB
 * @var float $tyLeThanhCong
 * @var int $tongVe
 * @var int $veLon
 * @var int $veTreEm
 * @var string $chartLabels
 * @var string $chartRevenue
 * @var string $chartOrders
 * @var array $topGoiVe
 * @var array $categoryStats
 * @var array $topLocations
 * @var array $ratingCounts
 * @var float $satisfactionRate
 * @var int $totalApprovedReviews
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';
?>

<!-- ========================================== -->
<!-- 1. TIÊU ĐỀ & THANH CÔNG CỤ THỜI GIAN -->
<!-- ========================================== -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Báo Cáo &amp; Thống Kê Hệ Thống</h4>
        <p class="text-muted small mb-0">Theo dõi doanh thu bán vé, mức độ hài lòng du khách và hiệu suất các điểm tham quan Nha Trang</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>In Báo Cáo
        </button>
        <a href="<?= url('admin/reports/export-csv?tu_ngay=' . $tuNgay . '&den_ngay=' . $denNgay) ?>" class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Xuất File CSV
        </a>
    </div>
</div>

<!-- Thanh Lọc Thời Gian Nhanh -->
<div class="card border-0 shadow-sm p-3 mb-4 rounded-4 bg-white">
    <form action="<?= url('admin/reports') ?>" method="GET" class="row g-3 align-items-center">
        <div class="col-lg-6">
            <div class="btn-group w-100 flex-wrap" role="group">
                <a href="<?= url('admin/reports?ky=7_ngay') ?>" class="btn btn-sm <?= ($ky === '7_ngay') ? 'btn-primary' : 'btn-outline-primary' ?>">7 ngày qua</a>
                <a href="<?= url('admin/reports?ky=30_ngay') ?>" class="btn btn-sm <?= ($ky === '30_ngay') ? 'btn-primary' : 'btn-outline-primary' ?>">30 ngày qua</a>
                <a href="<?= url('admin/reports?ky=thang_nay') ?>" class="btn btn-sm <?= ($ky === 'thang_nay') ? 'btn-primary' : 'btn-outline-primary' ?>">Tháng này</a>
                <a href="<?= url('admin/reports?ky=quy_nay') ?>" class="btn btn-sm <?= ($ky === 'quy_nay') ? 'btn-primary' : 'btn-outline-primary' ?>">Quý này</a>
                <a href="<?= url('admin/reports?ky=nam_nay') ?>" class="btn btn-sm <?= ($ky === 'nam_nay') ? 'btn-primary' : 'btn-outline-primary' ?>">Năm nay</a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" name="ky" value="tuy_chon">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">Từ</span>
                    <input type="date" name="tu_ngay" class="form-control" value="<?= htmlspecialchars($tuNgay) ?>">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">Đến</span>
                    <input type="date" name="den_ngay" class="form-control" value="<?= htmlspecialchars($denNgay) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold">
                    Xem
                </button>
            </div>
        </div>
    </form>
    <div class="mt-2 pt-2 border-top text-muted small d-flex align-items-center justify-content-between">
        <span><i class="bi bi-calendar3 me-1 text-primary"></i>Kỳ báo cáo: <strong class="text-dark"><?= $labelPeriod ?></strong></span>
        <span>Cập nhật lúc: <strong><?= date('H:i d/m/Y') ?></strong></span>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. 4 THẺ CHỈ SỐ KPI TÀI CHÍNH & VẬN HÀNH -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <!-- Doanh thu thực thu -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Doanh Thu Thực Thu</span>
                    <h3 class="fw-bold text-success mb-0 mt-1"><?= \App\Helpers\FormatHelper::tienTe($doanhThu) ?></h3>
                    <span class="text-muted" style="font-size: 11.5px;">Từ các đơn đã thanh toán</span>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Số đơn thành công -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Đơn Hàng Thành Công</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1"><?= number_format($donThanhCong) ?> <span class="fs-6 text-muted fw-normal">/ <?= $tongDon ?> đơn</span></h3>
                    <span class="badge bg-primary-subtle text-primary mt-1">Tỷ lệ: <?= $tyLeThanhCong ?>%</span>
                </div>
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-cart-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng số vé phát hành -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Vé Đã Phát Hành</span>
                    <h3 class="fw-bold text-info mb-0 mt-1"><?= number_format($tongVe) ?> <span class="fs-6 text-muted fw-normal">vé</span></h3>
                    <span class="text-muted" style="font-size: 11.5px;">
                        <?= number_format($veLon) ?> vé lớn &bull; <?= number_format($veTreEm) ?> vé trẻ em
                    </span>
                </div>
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info">
                    <i class="bi bi-ticket-perforated-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Giá trị đơn bình quân (AOV) -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Giá Trị Đơn TB (AOV)</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= \App\Helpers\FormatHelper::tienTe($giaTriDonTB) ?></h3>
                    <span class="text-muted" style="font-size: 11.5px;">Trung bình mỗi đơn đặt vé</span>
                </div>
                <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-calculator-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. BIỂU ĐỒ TRỰC QUAN DOANH THU & ĐÁNH GIÁ -->
<!-- ========================================== -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ Xu hướng Doanh thu & Đơn hàng -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Biểu Đồ Xu Hướng Doanh Thu &amp; Đơn Hàng</h6>
                <span class="badge bg-light text-secondary border small">Thời gian thực</span>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Phân bổ Mức độ Hài lòng Du khách -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Mức Độ Hài Lòng</h6>
                <span class="badge bg-success-subtle text-success small fw-semibold"><?= $satisfactionRate ?>% Hài lòng</span>
            </div>
            <div style="height: 220px; position: relative;">
                <canvas id="ratingBarChart"></canvas>
            </div>
            <div class="pt-3 border-top mt-3 text-center">
                <span class="small text-muted">
                    Tổng số: <strong><?= number_format($totalApprovedReviews) ?></strong> đánh giá đã được duyệt công khai
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. BẢNG PHÂN TÍCH CHUYÊN SÂU -->
<!-- ========================================== -->
<div class="row g-4 mb-4">
    <!-- Top 5 Gói Vé Bán Chạy -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Gói Vé Bán Chạy Nhất Trong Kỳ</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Gói Vé &amp; Địa Điểm</th>
                            <th class="text-center">Số Vé Bán</th>
                            <th class="text-end pe-3">Doanh Số</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topGoiVe)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Chưa có giao dịch bán vé nào trong khoảng thời gian này.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topGoiVe as $gv): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars($gv['ten_goi_ve']) ?></div>
                                        <span class="text-muted" style="font-size: 11px;">
                                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($gv['ten_dia_diem']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">
                                            <?= number_format((int)$gv['so_ve_ban']) ?> vé
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-success small">
                                        <?= \App\Helpers\FormatHelper::tienTe((float)$gv['doanh_so']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Phân Bổ Danh Mục Trải Nghiệm -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill text-info me-2"></i>Phân Bổ Danh Mục Trải Nghiệm &amp; Ẩm Thực</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Danh Mục</th>
                            <th class="text-center">Địa Điểm</th>
                            <th class="text-center">Lượt Xem</th>
                            <th class="text-end pe-3">Doanh Thu Vé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryStats as $cs): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi <?= htmlspecialchars($cs['bieu_tuong'] ?: 'bi-tag') ?> <?= ($cs['loai_danh_muc'] === 'am_thuc') ? 'text-danger' : 'text-info' ?>"></i>
                                        <span class="fw-semibold text-dark small"><?= htmlspecialchars($cs['ten_danh_muc']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border rounded-pill"><?= number_format((int)$cs['so_dia_diem']) ?></span>
                                </td>
                                <td class="text-center text-muted small">
                                    <?= number_format((int)$cs['tong_luot_xem']) ?>
                                </td>
                                <td class="text-end pe-3 fw-semibold text-dark small">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$cs['doanh_thu_ve']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. TOP 10 ĐỊA ĐIỂM ĐƯỢC DU KHÁCH QUAN TÂM NHẤT -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-compass-fill text-danger me-2"></i>Top 10 Điểm Đến &amp; Quán Ăn Hút Khách Nhất Hệ Thống</h6>
            <span class="text-muted small">Xếp hạng dựa trên lưu lượng truy cập, đánh giá người dùng và số lượt yêu thích</span>
        </div>
        <a href="<?= url('admin/locations') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            Quản lý địa điểm &rarr;
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-3" style="width: 50px;">#</th>
                    <th>Tên Địa Điểm</th>
                    <th>Danh Mục</th>
                    <th>Lượt Xem</th>
                    <th>Đánh Giá</th>
                    <th>Yêu Thích</th>
                    <th class="text-center">Bán Vé Trực Tuyến</th>
                    <th class="text-end pe-3">Xem Trên Web</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($topLocations as $loc): ?>
                    <tr>
                        <td class="ps-3 fw-bold <?= ($rank <= 3) ? 'text-danger' : 'text-muted' ?>">
                            #<?= $rank++ ?>
                        </td>
                        <td>
                            <strong class="text-dark"><?= htmlspecialchars($loc['ten_dia_diem']) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border small">
                                <?= htmlspecialchars($loc['ten_danh_muc']) ?>
                            </span>
                        </td>
                        <td>
                            <i class="bi bi-eye text-primary me-1"></i><?= number_format((int)$loc['luot_xem']) ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-star-fill text-warning small"></i>
                                <span class="fw-bold small"><?= number_format((float)$loc['diem_sao'], 1) ?></span>
                                <span class="text-muted small">(<?= (int)$loc['tong_danh_gia'] ?>)</span>
                            </div>
                        </td>
                        <td>
                            <i class="bi bi-heart-fill text-danger me-1 small"></i><?= number_format((int)$loc['tong_yeu_thich']) ?>
                        </td>
                        <td class="text-center">
                            <?php if ($loc['co_ban_ve']): ?>
                                <span class="badge bg-success-subtle text-success small rounded-pill px-2 py-1"><i class="bi bi-check-lg me-1"></i>Có</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted small rounded-pill px-2 py-1">Không</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <a href="<?= url('dia-diem/' . $loc['duong_dan']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Xem trang khách hàng">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPT VẼ BIỂU ĐỒ (CHART.JS) -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart Doanh thu & Đơn hàng
    const ctxRev = document.getElementById('revenueTrendChart').getContext('2d');
    const chartLabels = <?= $chartLabels ?>;
    const chartRevenue = <?= $chartRevenue ?>;
    const chartOrders = <?= $chartOrders ?>;

    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Doanh thu (VNĐ)',
                    data: chartRevenue,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y',
                },
                {
                    label: 'Số đơn hàng',
                    data: chartOrders,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    borderWidth: 2,
                    type: 'bar',
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                            return value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 2. Chart Mức độ hài lòng đánh giá
    const ctxRating = document.getElementById('ratingBarChart').getContext('2d');
    new Chart(ctxRating, {
        type: 'bar',
        data: {
            labels: ['1 sao', '2 sao', '3 sao', '4 sao', '5 sao'],
            datasets: [{
                label: 'Lượt đánh giá',
                data: [
                    <?= (int)$ratingCounts[1] ?>,
                    <?= (int)$ratingCounts[2] ?>,
                    <?= (int)$ratingCounts[3] ?>,
                    <?= (int)$ratingCounts[4] ?>,
                    <?= (int)$ratingCounts[5] ?>
                ],
                backgroundColor: [
                    '#ef4444',
                    '#f97316',
                    '#eab308',
                    '#84cc16',
                    '#10b981'
                ],
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>


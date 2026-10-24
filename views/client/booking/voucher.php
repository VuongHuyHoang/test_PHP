<?php
/**
 * @var string $title
 * @var array $donHang
 * @var array $chiTiet
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<style>
    @media print {
        body { background: #ffffff !important; font-size: 13px !important; }
        .site-navbar, footer, .d-print-none { display: none !important; }
        .container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .voucher-card { 
            border: 2px dashed #0b2545 !important; 
            box-shadow: none !important; 
            border-radius: 8px !important; 
            padding: 24px !important; 
        }
        .table { border: 1px solid #000 !important; }
    }
</style>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Stepper Tiến Trình Đặt Vé (Bước 3/3 - Hoàn Tất) -->
            <?php 
            $currentStep = 3;
            include __DIR__ . '/_stepper.php'; 
            ?>

            <!-- Phiếu E-Voucher -->
            <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 position-relative voucher-card" id="print-area">
                <div class="text-center border-bottom pb-4 mb-4">
                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill mb-2">
                        <i class="bi bi-shield-check me-1"></i>Phiếu Xác Nhận Đặt Vé Điện Tử (E-Voucher)
                    </span>
                    <h1 class="h3 fw-bold text-dark mt-2 mb-1">Mã Đơn: <?= htmlspecialchars($donHang['ma_don_hang']) ?></h1>
                    <p class="text-muted small mb-0">Thời gian khởi tạo: <?= \App\Helpers\FormatHelper::ngayThang($donHang['ngay_tao']) ?></p>
                </div>

                <div class="row g-4 align-items-center mb-4">
                    <!-- Mã QR Voucher -->
                    <div class="col-md-4 text-center">
                        <?php 
                            $qrImgSrc = '';
                            if (!empty($donHang['ma_qr']) && file_exists(dirname(__DIR__, 3) . '/public/' . $donHang['ma_qr'])) {
                                $qrImgSrc = url($donHang['ma_qr']);
                            } else {
                                $qrImgSrc = url(\App\Helpers\QrHelper::taoQrDonHang($donHang['ma_don_hang']));
                            }
                        ?>
                        <div class="p-2 bg-white border rounded-3 shadow-sm d-inline-block">
                            <img src="<?= $qrImgSrc ?>" 
                                 alt="Mã QR E-Voucher <?= htmlspecialchars($donHang['ma_don_hang']) ?>" 
                                 class="img-fluid" style="width: 180px; height: 180px; object-fit: contain;">
                        </div>
                        <p class="text-muted small mt-2 mb-0 fw-semibold">Xuất trình mã QR tại quầy</p>
                    </div>

                    <!-- Thông Tin Người Nhận -->
                    <div class="col-md-8">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>Thông Tin Hành Khách</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                            <li><strong>Khách hàng:</strong> <?= htmlspecialchars($donHang['ten_khach_hang']) ?></li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($donHang['email_khach_hang']) ?></li>
                            <li><strong>Số điện thoại:</strong> <?= htmlspecialchars($donHang['sdt_khach_hang']) ?></li>
                            <li><strong>Phương thức:</strong> 
                                <span class="badge bg-secondary"><?= strtoupper($donHang['phuong_thuc_thanh_toan']) ?></span>
                            </li>
                            <li><strong>Thanh toán:</strong> 
                                <span class="badge <?= $donHang['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= $donHang['trang_thai_thanh_toan'] === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán' ?>
                                </span>
                                <?php if ($donHang['trang_thai_thanh_toan'] !== 'da_thanh_toan' && $donHang['phuong_thuc_thanh_toan'] !== 'tai_quay'): ?>
                                    <a href="<?= url('booking/payment/' . $donHang['ma_don_hang']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2 ms-2 d-print-none">Thanh toán ngay</a>
                                <?php endif; ?>
                            </li>
                            <li><strong>Trạng thái vé:</strong> 
                                <span class="badge <?= $donHang['trang_thai_don_hang'] === 'hoan_thanh' ? 'bg-success' : 'bg-info text-dark' ?>">
                                    <?= $donHang['trang_thai_don_hang'] === 'hoan_thanh' ? 'Đã đổi vé vào cổng' : 'Hợp lệ (Chờ soát vé)' ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bảng Chi Tiết Vé -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped small align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dịch vụ / Gói vé</th>
                                <th>Ngày sử dụng</th>
                                <th class="text-center">Vé Người lớn</th>
                                <th class="text-center">Vé Trẻ em</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chiTiet as $item): ?>
                                <tr>
                                    <td>
                                        <strong class="text-dark"><?= htmlspecialchars($item['ten_goi_ve']) ?></strong><br>
                                        <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['ten_dia_diem']) ?></span>
                                    </td>
                                    <td><strong><?= \App\Helpers\FormatHelper::ngayThang($item['ngay_tham_quan']) ?></strong></td>
                                    <td class="text-center">
                                        <?= (int)$item['so_luong_nguoi_lon'] ?> x <?= \App\Helpers\FormatHelper::tienTe((float)$item['don_gia_nguoi_lon']) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= (int)$item['so_luong_tre_em'] ?> x <?= \App\Helpers\FormatHelper::tienTe((float)$item['don_gia_tre_em']) ?>
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        <?= \App\Helpers\FormatHelper::tienTe((float)$item['thanh_tien']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end fs-6">Tổng cộng thanh toán:</th>
                                <th class="text-end fs-5 text-danger fw-bold">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$donHang['tong_tien']) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hướng dẫn sử dụng tại quầy -->
                <div class="alert alert-info small mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Hướng dẫn nhận vé:</strong> Quý khách xuất trình ảnh phiếu E-Voucher này trên điện thoại hoặc đọc <strong>Mã đơn hàng: <?= htmlspecialchars($donHang['ma_don_hang']) ?></strong> kèm <strong>Số điện thoại</strong> tại quầy tiếp đón của địa điểm để nhận vé vật lý vào cổng.
                </div>

                <!-- Nút thao tác -->
                <div class="d-flex justify-content-between gap-2 d-print-none">
                    <a href="<?= url('') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-house me-1"></i>Về Trang Chủ
                    </a>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>In Phiếu / Lưu PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if ($donHang['trang_thai_thanh_toan'] === 'da_thanh_toan'): ?>
    <!-- Thư viện Canvas Confetti bắn pháo hoa chào mừng tại trang E-Voucher -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof confetti === 'function') {
                confetti({
                    particleCount: 80,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6']
                });
            }
        });
    </script>
<?php endif; ?>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

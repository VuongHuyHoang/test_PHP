<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Phiếu E-Voucher -->
            <div class="card border-0 shadow-lg p-4 rounded-4 position-relative" id="print-area">
                <div class="text-center border-bottom pb-4 mb-4">
                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill mb-2">
                        <i class="bi bi-check-circle me-1"></i>Phiếu Xác Nhận Đặt Vé Điện Tử (E-Voucher)
                    </span>
                    <h2 class="fw-bold text-dark mt-2 mb-1">Mã Đơn: <?= htmlspecialchars($donHang['ma_don_hang']) ?></h2>
                    <p class="text-muted small mb-0">Thời gian tạo: <?= \App\Helpers\FormatHelper::ngayThang($donHang['ngay_tao']) ?></p>
                </div>

                <div class="row g-4 align-items-center mb-4">
                    <!-- Mã QR Voucher -->
                    <div class="col-md-4 text-center">
                        <div class="p-3 bg-white border rounded-3 shadow-sm d-inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode($donHang['ma_don_hang']) ?>" 
                                 alt="QR Code <?= htmlspecialchars($donHang['ma_don_hang']) ?>" 
                                 class="img-fluid" style="width: 160px; height: 160px;">
                        </div>
                        <p class="text-muted small mt-2 mb-0">Quét mã đối soát tại quầy</p>
                    </div>

                    <!-- Thông Tin Người Nhận -->
                    <div class="col-md-8">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-lines-fill me-2"></i>Thông Tin Khách Hàng</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                            <li><strong>Khách hàng:</strong> <?= htmlspecialchars($donHang['ten_khach_hang']) ?></li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($donHang['email_khach_hang']) ?></li>
                            <li><strong>Số điện thoại:</strong> <?= htmlspecialchars($donHang['sdt_khach_hang']) ?></li>
                            <li><strong>Phương thức thanh toán:</strong> <span class="badge bg-secondary"><?= strtoupper($donHang['phuong_thuc_thanh_toan']) ?></span></li>
                            <li><strong>Trạng thái đơn:</strong> 
                                <span class="badge <?= $donHang['trang_thai_don_hang'] === 'hoan_thanh' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= $donHang['trang_thai_don_hang'] === 'hoan_thanh' ? 'Đã hoàn thành' : 'Đang xử lý / Mới tạo' ?>
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
                                        <strong><?= htmlspecialchars($item['ten_goi_ve']) ?></strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($item['ten_dia_diem']) ?></span>
                                    </td>
                                    <td><?= \App\Helpers\FormatHelper::ngayThang($item['ngay_tham_quan']) ?></td>
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
                                <th colspan="4" class="text-end fs-6">Tổng cộng:</th>
                                <th class="text-end fs-5 text-danger fw-bold">
                                    <?= \App\Helpers\FormatHelper::tienTe((float)$donHang['tong_tien']) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hướng dẫn sử dụng -->
                <div class="alert alert-info small mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Hướng dẫn nhận vé:</strong> Quý khách vui lòng xuất trình ảnh E-Voucher này hoặc đọc <strong>Mã đơn hàng</strong> kèm <strong>Số điện thoại</strong> tại quầy lễ tân để nhận vé vật lý vào cổng.
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

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

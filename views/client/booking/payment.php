<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header thanh toán -->
                <div class="bg-primary text-white p-4 text-center">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">Thanh toán an toàn</span>
                    <h2 class="h4 fw-bold mb-1">Cổng Thanh Toán Đơn Đặt Vé</h2>
                    <p class="small text-white-50 mb-0">Mã đơn hàng: <strong class="text-white"><?= htmlspecialchars($donHang['ma_don_hang']) ?></strong></p>
                </div>

                <div class="p-4 p-md-5">
                    <?php 
                        $bankId = $_ENV['BANK_ID'] ?? 'MB';
                        $bankAccountNo = $_ENV['BANK_ACCOUNT_NO'] ?? '0901234567';
                        $bankAccountName = $_ENV['BANK_ACCOUNT_NAME'] ?? 'NHA TRANG TOURISM';
                        $soTien = (float)$donHang['tong_tien'];
                        $maDon = $donHang['ma_don_hang'];
                        $phuongThuc = $donHang['phuong_thuc_thanh_toan'];

                        // Đường dẫn VietQR động chuẩn Napas
                        $vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$bankAccountNo}-compact2.jpg?amount={$soTien}&addInfo=" . urlencode($maDon) . "&accountName=" . urlencode($bankAccountName);
                    ?>

                    <div class="row g-4 align-items-center mb-4">
                        <!-- Cột Mã QR Thanh Toán -->
                        <div class="col-md-5 text-center">
                            <div class="p-2 border rounded-3 shadow-sm bg-white d-inline-block">
                                <img src="<?= $vietQrUrl ?>" alt="Mã VietQR Thanh Toán" class="img-fluid rounded" style="max-width: 240px;">
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="bi bi-qr-code-scan me-1"></i>Quét bằng App Ngân hàng hoặc MoMo
                            </p>
                        </div>

                        <!-- Cột Thông Tin Chuyển Khoản -->
                        <div class="col-md-7">
                            <h5 class="fw-bold text-dark mb-3">Thông Tin Chuyển Khoản</h5>
                            
                            <div class="bg-light p-3 rounded-3 border d-flex flex-column gap-2 small">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Ngân hàng thụ hưởng:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($bankId) ?> Bank</strong>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Số tài khoản:</span>
                                    <div>
                                        <strong class="text-primary fs-6 me-2" id="copy-stk"><?= htmlspecialchars($bankAccountNo) ?></strong>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="copyText('copy-stk', this)">Copy</button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Chủ tài khoản:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($bankAccountName) ?></strong>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Số tiền thanh toán:</span>
                                    <strong class="text-danger fs-5"><?= \App\Helpers\FormatHelper::tienTe($soTien) ?></strong>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="text-muted">Nội dung chuyển khoản:</span>
                                    <div>
                                        <strong class="text-dark me-2" id="copy-noidung"><?= htmlspecialchars($maDon) ?></strong>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="copyText('copy-noidung', this)">Copy</button>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning small mt-3 mb-0 py-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Vui lòng giữ nguyên <strong>Nội dung chuyển khoản</strong> để hệ thống tự động đối soát vé.
                            </div>
                        </div>
                    </div>

                    <!-- Nút Xác Nhận Thanh Toán -->
                    <div class="d-flex flex-column gap-2 pt-3 border-top">
                        <form action="<?= url('booking/confirm-payment') ?>" method="POST">
                            <input type="hidden" name="ma_don_hang" value="<?= htmlspecialchars($maDon) ?>">
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-pill fs-5">
                                <i class="bi bi-check2-circle me-2"></i>Tôi Đã Hoàn Tất Chuyển Khoản
                            </button>
                        </form>

                        <div class="text-center mt-2">
                            <a href="<?= url('voucher/' . $maDon) ?>" class="text-muted small text-decoration-none">
                                Xem trước phiếu E-Voucher & thanh toán tại quầy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function copyText(elementId, btn) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.innerText = 'Đã chép!';
        btn.classList.replace('btn-outline-secondary', 'btn-success');
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    });
}
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

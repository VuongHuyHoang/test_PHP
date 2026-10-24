<?php
/**
 * @var string $title
 * @var array $donHang
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Stepper Tiến Trình Đặt Vé (Bước 2/3) -->
            <?php 
            $currentStep = 2;
            include __DIR__ . '/_stepper.php'; 
            ?>

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
                    $remSeconds = (int)($remainingSeconds ?? 600);

                    // Đường dẫn VietQR động chuẩn Napas
                    $vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$bankAccountNo}-compact2.jpg?amount={$soTien}&addInfo=" . urlencode($maDon) . "&accountName=" . urlencode($bankAccountName);
                    ?>

                    <!-- Đồng Hồ Đếm Ngược Giữ Chỗ (10-Minute Reservation Timer) -->
                    <div class="reservation-timer-banner mb-4 p-3 rounded-4 border d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm" id="timer-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #86efac !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="timer-icon-wrap rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 48px; height: 48px; font-size: 22px; color: #16a34a;" id="timer-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" id="timer-title">Thời Gian Giữ Vé Tạm Thời</h6>
                                <small class="text-muted" id="timer-sub">Vui lòng chuyển khoản trong vòng 10 phút để hệ thống giữ vé cho bạn</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white px-3 py-2 rounded-3 border shadow-sm font-monospace fs-4 fw-bold text-success text-center" style="min-width: 110px;" id="timer-display">
                                <?= sprintf('%02d:%02d', floor($remSeconds / 60), $remSeconds % 60) ?>
                            </div>
                        </div>
                    </div>

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

                    <!-- Nút Xác Nhận Thanh Toán & Hủy Đơn -->
                    <div class="d-flex flex-column gap-2 pt-3 border-top">
                        <!-- Khối Giả Lập Sandbox (Dành cho Học tập & Đồ án) -->
                        <div class="card border border-warning-subtle shadow-sm rounded-4 p-3 mb-2" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill">
                                    <i class="bi bi-code-slash me-1"></i>Chế Độ Học Tập / Demo Mode
                                </span>
                                <span class="text-secondary small" style="font-size: 0.75rem;"><i class="bi bi-shield-check text-success me-1"></i>Không cần chuyển tiền thật</span>
                            </div>
                            <p class="small text-secondary mb-2 lh-sm">
                                Bấm nút bên dưới để hệ thống kích hoạt ngầm tín hiệu Webhook ngân hàng xác nhận đã nhận tiền:
                            </p>
                            <button type="button" class="btn btn-warning w-100 py-2.5 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 border border-warning text-dark" id="btn-simulate-pay" onclick="simulatePaymentSuccess()">
                                <i class="bi bi-lightning-charge-fill text-danger fs-5"></i>
                                <span>⚡ Giả Lập Quét QR Thành Công (Demo Sandbox)</span>
                            </button>
                        </div>

                        <form action="<?= url('booking/confirm-payment') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="ma_don_hang" value="<?= htmlspecialchars($maDon) ?>">
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-pill fs-5 shadow-sm" id="btn-confirm-pay">
                                <i class="bi bi-check2-circle me-2"></i>Tôi Đã Hoàn Tất Chuyển Khoản
                            </button>
                        </form>

                        <div class="row g-2 mt-1">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                    <i class="bi bi-x-circle me-1"></i>Hủy đơn hàng này
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <a href="<?= url('voucher/' . $maDon) ?>" class="btn btn-light border w-100 rounded-pill py-2 text-muted fw-semibold">
                                    <i class="bi bi-receipt me-1"></i>Xem phiếu vé tạm
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-2">
                            <span class="text-muted small">
                                <i class="bi bi-shield-lock me-1"></i>Thanh toán bảo mật chuẩn VietQR NAPAS 247
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Xác Nhận Hủy Đơn Hàng -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="cancelOrderModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác Nhận Hủy Đơn Đặt Vé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary mb-2">Bạn có chắc chắn muốn hủy đơn hàng <strong><?= htmlspecialchars($maDon) ?></strong>?</p>
                <div class="alert alert-warning small mb-3">
                    <i class="bi bi-info-circle me-1"></i>Khi hủy, số lượng vé giữ chỗ sẽ được tự động hoàn trả lại kho cho các du khách khác.
                </div>
                <form action="<?= url('booking/cancel') ?>" method="POST" id="form-cancel-order">
                    <?= csrf_field() ?>
                    <input type="hidden" name="ma_don_hang" value="<?= htmlspecialchars($maDon) ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Lý do hủy (không bắt buộc):</label>
                        <select name="ly_do" class="form-select form-select-sm">
                            <option value="Đổi ý định tham quan">Đổi ý định tham quan</option>
                            <option value="Muốn thay đổi ngày / số lượng vé">Muốn thay đổi ngày / số lượng vé</option>
                            <option value="Lỗi thanh toán ngân hàng">Lỗi thanh toán ngân hàng</option>
                            <option value="Khác">Lý do khác</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Không, giữ lại</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Xác Nhận Hủy Đơn</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal / Overlay Chúc Mừng & Pháo Hoa Khi Thanh Toán Thành Công -->
<div id="payment-success-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.78); backdrop-filter: blur(8px); z-index: 9999; justify-content: center; align-items: center;">
    <div class="card border-0 rounded-4 shadow-lg p-4 p-md-5 text-center mx-3 bg-white" style="max-width: 460px; animation: popInCelebration 0.35s cubic-bezier(0.16, 1, 0.3, 1);">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success p-3 shadow-sm" style="width: 86px; height: 86px;">
                <i class="bi bi-check-circle-fill display-4 text-success"></i>
            </div>
        </div>
        <h3 class="h4 fw-bold text-dark mb-1">🎉 Giao Dịch Thành Công!</h3>
        <p class="text-secondary small mb-4">Hệ thống đã tự động ghi nhận thanh toán thành công. Mã vé E-Voucher QR của bạn đã được kích hoạt.</p>
        <div class="d-flex align-items-center justify-content-center gap-2 text-primary fw-semibold small bg-light py-2 px-3 rounded-pill">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span>Đang chuyển hướng tới Phiếu E-Voucher...</span>
        </div>
    </div>
</div>

<style>
@keyframes popInCelebration {
    0% { transform: scale(0.85); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<!-- Thư viện Canvas Confetti tạo hiệu ứng pháo hoa nhẹ, mượt -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

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

    // Hiệu ứng pháo hoa Confetti rực rỡ chào mừng thanh toán thành công (không có âm thanh)
    function launchFireworks() {
        if (typeof confetti !== 'function') return;

        const duration = 2.4 * 1000;
        const end = Date.now() + duration;

        (function frame() {
            // Chùm pháo bên trái
            confetti({
                particleCount: 6,
                angle: 60,
                spread: 60,
                origin: { x: 0.05, y: 0.75 },
                colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6', '#10b981']
            });
            // Chùm pháo bên phải
            confetti({
                particleCount: 6,
                angle: 120,
                spread: 60,
                origin: { x: 0.95, y: 0.75 },
                colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6', '#10b981']
            });
            // Chùm pháo giữa
            confetti({
                particleCount: 4,
                angle: 90,
                spread: 100,
                origin: { x: 0.5, y: 0.6 },
                colors: ['#ffd700', '#ff4500', '#00e5ff', '#ff007f']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    }

    // Xử lý Giả lập Quét QR Chuyển Tiền Thành Công (Demo Sandbox cho Học tập/Đồ án)
    function simulatePaymentSuccess() {
        const btn = document.getElementById('btn-simulate-pay');
        const overlay = document.getElementById('payment-success-overlay');
        const voucherUrl = "<?= url('voucher/' . $maDon) ?>";

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý giao dịch mô phỏng...';
        }

        const formData = new FormData();
        formData.append('ma_don_hang', '<?= htmlspecialchars($maDon) ?>');
        formData.append('csrf_token', '<?= csrf_token() ?>');

        fetch("<?= url('booking/simulate-payment') ?>", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                // 1. Hiện overlay chúc mừng
                if (overlay) overlay.style.display = 'flex';
                // 2. Kích hoạt hiệu ứng pháo hoa
                launchFireworks();
                // 3. Tự động chuyển hướng sau 2.4 giây
                setTimeout(() => {
                    window.location.href = res.voucher_url || voucherUrl;
                }, 2400);
            } else {
                alert(res.message || 'Lỗi khi mô phỏng thanh toán.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-lightning-charge-fill text-danger fs-5"></i><span>⚡ Giả Lập Quét QR Thành Công (Demo Sandbox)</span>';
                }
            }
        })
        .catch(err => {
            alert('Lỗi kết nối máy chủ!');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-lightning-charge-fill text-danger fs-5"></i><span>⚡ Giả Lập Quét QR Thành Công (Demo Sandbox)</span>';
            }
        });
    }

    // Logic Đếm Ngược 10 Phút & Auto-Polling
    (function() {
        let remainingSeconds = <?= $remSeconds ?>;
        const displayEl = document.getElementById('timer-display');
        const boxEl = document.getElementById('timer-box');
        const iconEl = document.getElementById('timer-icon');
        const titleEl = document.getElementById('timer-title');
        const subEl = document.getElementById('timer-sub');
        const confirmBtn = document.getElementById('btn-confirm-pay');
        const checkUrl = "<?= url('booking/check-status/' . $maDon) ?>";
        const profileUrl = "<?= url('profile') ?>";
        const voucherUrl = "<?= url('voucher/' . $maDon) ?>";
        const overlay = document.getElementById('payment-success-overlay');

        function updateDisplay() {
            if (remainingSeconds <= 0) {
                if (displayEl) displayEl.innerText = "00:00";
                if (boxEl) {
                    boxEl.style.background = "#fee2e2";
                    boxEl.style.borderColor = "#f87171";
                }
                if (iconEl) {
                    iconEl.style.color = "#dc2626";
                    iconEl.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
                }
                if (titleEl) titleEl.innerText = "Đã Hết Hạn Giữ Chỗ";
                if (subEl) subEl.innerText = "Đơn hàng đã hết hạn 10 phút và tự động hủy vé";
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerText = "Đơn hàng đã hết hạn";
                }
                return;
            }

            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            const str = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
            if (displayEl) displayEl.innerText = str;

            // Dưới 2 phút: đổi màu cảnh báo
            if (remainingSeconds <= 120) {
                if (boxEl) {
                    boxEl.style.background = "#fff1f2";
                    boxEl.style.borderColor = "#fda4af";
                }
                if (displayEl) {
                    displayEl.className = "bg-white px-3 py-2 rounded-3 border shadow-sm font-monospace fs-4 fw-bold text-danger text-center animate-pulse";
                }
                if (iconEl) iconEl.style.color = "#e11d48";
            }
        }

        // Đếm ngược mỗi giây
        const timerInterval = setInterval(function() {
            remainingSeconds--;
            updateDisplay();

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                clearInterval(pollInterval);
                setTimeout(function() {
                    alert("Thời gian giữ chỗ 10 phút đã kết thúc! Hệ thống sẽ chuyển hướng bạn về trang quản lý vé.");
                    window.location.href = profileUrl;
                }, 1000);
            }
        }, 1000);

        // Polling trạng thái đơn hàng mỗi 5 giây (nếu admin xác nhận ở tab khác, tự nổ pháo hoa và chuyển hướng)
        const pollInterval = setInterval(function() {
            fetch(checkUrl)
                .then(r => r.json())
                .then(res => {
                    if (res && res.status === 'success') {
                        if (res.is_paid) {
                            clearInterval(timerInterval);
                            clearInterval(pollInterval);
                            if (overlay) overlay.style.display = 'flex';
                            launchFireworks();
                            setTimeout(() => {
                                window.location.href = voucherUrl;
                            }, 2400);
                        } else if (res.is_cancelled) {
                            clearInterval(timerInterval);
                            clearInterval(pollInterval);
                            alert("Đơn hàng này đã bị hủy hoặc hết hạn giữ chỗ.");
                            window.location.href = profileUrl;
                        }
                    }
                })
                .catch(() => {});
        }, 5000);
    })();
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>
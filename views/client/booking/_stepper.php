<?php
/**
 * Instant Checkout Stepper UX Component
 * @var int $currentStep 1 = Chọn vé, 2 = Thanh toán QR, 3 = Nhận E-Voucher
 */
$currentStep = $currentStep ?? 1;
?>
<div class="checkout-stepper-container mb-4 d-print-none">
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4">
        <div class="d-flex align-items-center justify-content-between position-relative checkout-stepper-track">
            <!-- Đường kẻ tiến trình nền -->
            <div class="checkout-progress-bar-bg position-absolute start-0 end-0" style="height: 4px; background: #e2e8f0; z-index: 1; top: 22px;"></div>
            <!-- Đường kẻ tiến trình active -->
            <div class="checkout-progress-bar-fill position-absolute start-0" style="height: 4px; background: linear-gradient(90deg, #0284c7, #16a34a); z-index: 2; top: 22px; width: <?= $currentStep === 1 ? '16%' : ($currentStep === 2 ? '50%' : '100%') ?>; transition: width 0.4s ease;"></div>

            <!-- BƯỚC 1 -->
            <div class="step-item text-center position-relative" style="z-index: 3; flex: 1;">
                <div class="step-circle rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep > 1 ? 'bg-success text-white' : ($currentStep === 1 ? 'bg-primary text-white border border-3 border-light shadow' : 'bg-white text-muted border') ?>" style="width: 44px; height: 44px; font-size: 18px; transition: all 0.3s ease;">
                    <i class="bi <?= $currentStep > 1 ? 'bi-check-lg fw-bold' : 'bi-ticket-perforated-fill' ?>"></i>
                </div>
                <div class="step-label mt-2">
                    <span class="d-block fw-bold small <?= $currentStep >= 1 ? 'text-dark' : 'text-muted' ?>" style="font-size: 13px;">1. Chọn Vé &amp; Ngày</span>
                    <small class="text-muted d-none d-md-block" style="font-size: 11px;">Số lượng &amp; thông tin</small>
                </div>
            </div>

            <!-- BƯỚC 2 -->
            <div class="step-item text-center position-relative" style="z-index: 3; flex: 1;">
                <div class="step-circle rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep > 2 ? 'bg-success text-white' : ($currentStep === 2 ? 'bg-warning text-dark border border-3 border-light shadow' : 'bg-white text-muted border') ?>" style="width: 44px; height: 44px; font-size: 18px; transition: all 0.3s ease;">
                    <i class="bi <?= $currentStep > 2 ? 'bi-check-lg fw-bold' : 'bi-qr-code-scan' ?>"></i>
                </div>
                <div class="step-label mt-2">
                    <span class="d-block fw-bold small <?= $currentStep >= 2 ? 'text-dark' : 'text-muted' ?>" style="font-size: 13px;">2. Thanh Toán VietQR</span>
                    <small class="text-muted d-none d-md-block" style="font-size: 11px;">Giữ chỗ an toàn 10p</small>
                </div>
            </div>

            <!-- BƯỚC 3 -->
            <div class="step-item text-center position-relative" style="z-index: 3; flex: 1;">
                <div class="step-circle rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep === 3 ? 'bg-success text-white border border-3 border-light shadow' : 'bg-white text-muted border' ?>" style="width: 44px; height: 44px; font-size: 18px; transition: all 0.3s ease;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div class="step-label mt-2">
                    <span class="d-block fw-bold small <?= $currentStep === 3 ? 'text-success' : 'text-muted' ?>" style="font-size: 13px;">3. Nhận E-Voucher</span>
                    <small class="text-muted d-none d-md-block" style="font-size: 11px;">Mã QR vào cổng</small>
                </div>
            </div>
        </div>
    </div>
</div>


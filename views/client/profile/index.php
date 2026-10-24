<?php
/**
 * @var string $title
 * @var array $user
 * @var array $orders
 * @var array $transactions
 * @var array $favorites
 * @var int $validTicketsCount
 * @var float|int $totalSpent
 * @var int $pendingPaymentCount
 * @var string $activeTab
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
?>
<?php require_once dirname(__DIR__, 2) . '/layouts/navbar.php'; ?>

<main class="container my-5 flex-grow-1">
    <!-- Header Thông Tin Tài Khoản & KPI Tóm Tắt -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #0b2545 0%, #137a8e 100%);">
            <div class="row align-items-center g-4">
                <div class="col-md-7 d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 72px; height: 72px; font-size: 32px;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h3 class="fw-bold mb-0 text-white"><?= htmlspecialchars($user['ho_ten']) ?></h3>
                            <span class="badge bg-warning text-dark fw-semibold px-2 py-1 rounded-pill small">
                                <?= $user['vai_tro'] === 'admin' ? 'Quản Trị Viên' : 'Thành Viên' ?>
                            </span>
                        </div>
                        <p class="mb-0 text-white-50 small">
                            <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']) ?>
                            <?php if (!empty($user['so_dien_thoai'])): ?>
                                <span class="mx-2">•</span><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($user['so_dien_thoai']) ?>
                            <?php endif; ?>
                            <span class="mx-2">•</span><i class="bi bi-calendar3 me-1"></i>Tham gia: <?= date('d/m/Y', strtotime($user['ngay_tao'])) ?>
                        </p>
                    </div>
                </div>

                <!-- 4 Thẻ Tóm Tắt Nhanh -->
                <div class="col-md-6 col-lg-5">
                    <div class="row g-2 text-center">
                        <div class="col-3">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25 h-100 d-flex flex-column justify-content-center">
                                <span class="small text-white-50 d-block" style="font-size: 11px;">Vé sở hữu</span>
                                <strong class="fs-5 text-warning"><?= number_format($validTicketsCount) ?></strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25 h-100 d-flex flex-column justify-content-center">
                                <span class="small text-white-50 d-block" style="font-size: 11px;">Đã đánh giá</span>
                                <strong class="fs-5 text-warning"><?= count($myReviews ?? []) ?></strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25 h-100 d-flex flex-column justify-content-center">
                                <span class="small text-white-50 d-block" style="font-size: 11px;">Yêu thích</span>
                                <strong class="fs-5 text-danger"><?= count($favorites) ?></strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25 h-100 d-flex flex-column justify-content-center">
                                <span class="small text-white-50 d-block" style="font-size: 11px;">Chi tiêu</span>
                                <strong class="small text-white text-truncate d-block fw-bold"><?= \App\Helpers\FormatHelper::tienTe($totalSpent) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Tab Điều Hướng (Nằm trên 1 hàng duy nhất) -->
        <div class="px-3 px-md-4 py-2 bg-light border-top">
            <ul class="nav nav-pills flex-nowrap align-items-center gap-1 gap-md-2 overflow-x-auto w-100 mb-0" id="profileTabs" role="tablist">
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link rounded-pill px-2 px-md-3 py-1.5 fw-semibold <?= ($activeTab === 'tickets') ? 'active' : '' ?>" 
                            id="tickets-tab" data-tab-name="tickets" onclick="switchProfileTab('tickets')" type="button" role="tab" style="font-size: 13px; white-space: nowrap;">
                        <i class="bi bi-ticket-perforated me-1 text-primary"></i>Vé &amp; E-Voucher (<?= count($orders) ?>)
                    </button>
                </li>
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link rounded-pill px-2 px-md-3 py-1.5 fw-semibold <?= ($activeTab === 'reviews') ? 'active' : '' ?>" 
                            id="reviews-tab" data-tab-name="reviews" onclick="switchProfileTab('reviews')" type="button" role="tab" style="font-size: 13px; white-space: nowrap;">
                        <i class="bi bi-star-fill me-1 text-warning"></i>Đã Đánh Giá (<?= count($myReviews ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link rounded-pill px-2 px-md-3 py-1.5 fw-semibold <?= ($activeTab === 'favorites') ? 'active' : '' ?>" 
                            id="favorites-tab" data-tab-name="favorites" onclick="switchProfileTab('favorites')" type="button" role="tab" style="font-size: 13px; white-space: nowrap;">
                        <i class="bi bi-heart-fill me-1 text-danger"></i>Yêu Thích (<?= count($favorites) ?>)
                    </button>
                </li>
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link rounded-pill px-2 px-md-3 py-1.5 fw-semibold <?= ($activeTab === 'transactions') ? 'active' : '' ?>" 
                            id="transactions-tab" data-tab-name="transactions" onclick="switchProfileTab('transactions')" type="button" role="tab" style="font-size: 13px; white-space: nowrap;">
                        <i class="bi bi-clock-history me-1 text-secondary"></i>Lịch Sử Giao Dịch (<?= count($transactions) ?>)
                    </button>
                </li>
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link rounded-pill px-2 px-md-3 py-1.5 fw-semibold <?= ($activeTab === 'account') ? 'active' : '' ?>" 
                            id="account-tab" data-tab-name="account" onclick="switchProfileTab('account')" type="button" role="tab" style="font-size: 13px; white-space: nowrap;">
                        <i class="bi bi-gear me-1 text-secondary"></i>Cài Đặt Tài Khoản
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Nội Dung Các Tab -->
    <div class="tab-content" id="profileTabsContent">
        <!-- ========================================== -->
        <!-- TAB 1: VÉ & E-VOUCHER CỦA TÔI -->
        <!-- ========================================== -->
        <?php
        $today = date('Y-m-d');
        $countValid = 0;
        $countExpired = 0;
        $countCancelled = 0;
        $countPending = 0;

        foreach ($orders as &$ord) {
            $isCancelled = ($ord['trang_thai_don_hang'] === 'da_huy');
            $isUsed = ($ord['trang_thai_don_hang'] === 'hoan_thanh');
            $isPaid = ($ord['trang_thai_thanh_toan'] === 'da_thanh_toan' || $ord['phuong_thuc_thanh_toan'] === 'tai_quay');
            $vDate = !empty($ord['ngay_tham_quan']) ? date('Y-m-d', strtotime($ord['ngay_tham_quan'])) : null;
            $isExpiredDate = ($vDate !== null && $vDate < $today);

            if ($isCancelled) {
                $ord['ticket_status'] = 'cancelled';
                $countCancelled++;
            } elseif ($isUsed || $isExpiredDate) {
                $ord['ticket_status'] = 'expired';
                $countExpired++;
            } elseif ($isPaid) {
                $ord['ticket_status'] = 'valid';
                $countValid++;
            } else {
                $ord['ticket_status'] = 'pending';
                $countPending++;
            }
        }
        unset($ord);
        ?>
        <div class="tab-pane fade <?= ($activeTab === 'tickets') ? 'show active' : '' ?>" id="tab-tickets" role="tabpanel" style="<?= ($activeTab === 'tickets') ? 'display: block;' : 'display: none;' ?>">
            <?php if (empty($orders)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="text-muted mb-3">
                        <i class="bi bi-ticket-detailed fs-1 text-secondary opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Bạn chưa có phiếu đặt vé nào</h5>
                    <p class="text-muted small">Hãy khám phá các danh lam thắng cảnh và đặt vé trực tuyến ngay hôm nay!</p>
                    <div>
                        <a href="<?= url('locations?loai=du_lich') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-compass me-1"></i>Khám phá điểm du lịch
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Thanh Lọc Trạng Thái Vé (Tabs Phân Loại) -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 bg-white rounded-4 border shadow-sm">
                    <div class="d-flex flex-wrap align-items-center gap-1.5" id="ticketFilterChips">
                        <span class="small fw-bold text-muted me-2"><i class="bi bi-funnel me-1"></i>Xem vé:</span>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active" data-ticket-filter="all">
                            Tất cả (<?= count($orders) ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold ticket-filter-btn" data-ticket-filter="valid">
                            <i class="bi bi-shield-check me-1"></i>Chưa sử dụng (<?= $countValid ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn" data-ticket-filter="expired">
                            <i class="bi bi-clock-history me-1"></i>Hết hạn / Đã dùng (<?= $countExpired ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold ticket-filter-btn" data-ticket-filter="cancelled">
                            <i class="bi bi-x-circle me-1"></i>Đã hủy (<?= $countCancelled ?>)
                        </button>
                        <?php if ($countPending > 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1 fw-semibold ticket-filter-btn" data-ticket-filter="pending">
                                <i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán (<?= $countPending ?>)
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row g-4" id="ticketCardsGrid">
                    <?php foreach ($orders as $o): 
                        $status = $o['ticket_status'] ?? 'valid';
                        $vDate = !empty($o['ngay_tham_quan']) ? date('Y-m-d', strtotime($o['ngay_tham_quan'])) : null;
                        $isExpiredDate = ($vDate !== null && $vDate < $today);
                        $isUsed = ($o['trang_thai_don_hang'] === 'hoan_thanh');
                        $isCancelled = ($status === 'cancelled');

                        // Đường dẫn đặt lại vé
                        $rebookUrl = !empty($o['goi_ve_id']) ? url('booking/' . $o['goi_ve_id']) : (!empty($o['dia_diem_slug']) ? url('dia-diem/' . $o['dia_diem_slug'] . '#ve-tham-quan') : url('locations?loai=du_lich'));
                    ?>
                        <div class="col-md-6 col-lg-4 ticket-card-col" data-ticket-status="<?= $status ?>">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column bg-white position-relative <?= $isCancelled ? 'opacity-75' : '' ?>">
                                <!-- Ảnh địa điểm & Nhãn trạng thái -->
                                <div class="position-relative" style="height: 160px;">
                                    <?php 
                                        $locImg = $o['anh_dia_diem'] ?? 'assets/images/placeholder.jpg';
                                        $imgUrl = str_starts_with($locImg, 'http') ? $locImg : url($locImg);
                                    ?>
                                    <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($o['ten_dia_diem'] ?? 'Vé tham quan') ?>" class="w-100 h-100 object-fit-cover <?= $isCancelled ? 'grayscale-image' : '' ?>">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <?php if ($status === 'cancelled'): ?>
                                            <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-x-circle me-1"></i>Đã hủy
                                            </span>
                                        <?php elseif ($status === 'expired'): ?>
                                            <?php if ($isUsed): ?>
                                                <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm">
                                                    <i class="bi bi-check2-all me-1"></i>Đã đổi vé vào cổng
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-dark-subtle text-dark border border-secondary px-3 py-2 rounded-pill shadow-sm">
                                                    <i class="bi bi-clock-history me-1"></i>Đã hết hạn ngày đi
                                                </span>
                                            <?php endif; ?>
                                        <?php elseif ($status === 'valid'): ?>
                                            <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-shield-check me-1"></i>Chưa sử dụng
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Nội dung đơn vé -->
                                <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted small fw-semibold">Mã đơn: <strong class="text-primary"><?= htmlspecialchars($o['ma_don_hang']) ?></strong></span>
                                            <span class="badge bg-light text-secondary border small"><?= strtoupper($o['phuong_thuc_thanh_toan']) ?></span>
                                        </div>

                                        <h5 class="fw-bold text-dark mb-1 line-clamp-1">
                                            <?= htmlspecialchars($o['ten_dia_diem'] ?? 'Điểm tham quan Nha Trang') ?>
                                        </h5>
                                        <p class="text-muted small mb-3">
                                            <i class="bi bi-ticket-perforated me-1"></i><?= htmlspecialchars($o['ten_goi_ve'] ?? 'Vé tham quan tiêu chuẩn') ?>
                                        </p>

                                        <div class="bg-light rounded-3 p-3 mb-3 small d-flex flex-column gap-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Ngày tham quan:</span>
                                                <strong class="<?= $isExpiredDate ? 'text-danger' : 'text-dark' ?>">
                                                    <?= !empty($o['ngay_tham_quan']) ? \App\Helpers\FormatHelper::ngayThang($o['ngay_tham_quan']) : 'Theo ngày đặt' ?>
                                                    <?php if ($isExpiredDate): ?>
                                                        <span class="badge bg-danger-subtle text-danger rounded-pill ms-1" style="font-size: 10px;">Quá hạn</span>
                                                    <?php endif; ?>
                                                </strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Số lượng vé:</span>
                                                <strong class="text-dark"><?= (int)($o['tong_so_ve'] ?? 1) ?> vé</strong>
                                            </div>
                                            <div class="d-flex justify-content-between pt-1 border-top">
                                                <span class="text-muted">Tổng thanh toán:</span>
                                                <strong class="text-danger fs-6"><?= \App\Helpers\FormatHelper::tienTe((float)$o['tong_tien']) ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Thao tác vé theo trạng thái -->
                                    <div class="pt-2">
                                        <?php if ($status === 'cancelled'): ?>
                                            <!-- Vé đã hủy: Hiển thị thông báo và Nút Đặt lại -->
                                            <div class="p-2 mb-2 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle text-danger small text-center fw-semibold">
                                                <i class="bi bi-info-circle me-1"></i>Đơn hàng này đã bị hủy
                                            </div>
                                            <a href="<?= $rebookUrl ?>" class="btn btn-primary rounded-pill w-100 fw-semibold shadow-sm">
                                                <i class="bi bi-arrow-repeat me-1"></i>Đặt lại vé này
                                            </a>
                                        <?php elseif ($status === 'expired'): ?>
                                            <!-- Vé hết hạn / đã dùng -->
                                            <div class="d-flex gap-2">
                                                <a href="<?= url('voucher/' . $o['ma_don_hang']) ?>" class="btn btn-outline-secondary rounded-pill flex-grow-1 fw-semibold small">
                                                    <i class="bi bi-file-earmark-text me-1"></i>Chi tiết vé
                                                </a>
                                                <a href="<?= $rebookUrl ?>" class="btn btn-primary rounded-pill px-3 fw-semibold small" title="Đặt lại vé cho chuyến đi mới">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Đặt lại
                                                </a>
                                            </div>
                                        <?php elseif ($status === 'valid'): ?>
                                            <!-- Vé chưa sử dụng (Sẵn sàng) -->
                                            <a href="<?= url('voucher/' . $o['ma_don_hang']) ?>" class="btn btn-success rounded-pill w-100 fw-semibold shadow-sm">
                                                <i class="bi bi-qr-code me-1"></i>Xem E-Voucher &amp; Mã QR
                                            </a>
                                        <?php else: ?>
                                            <!-- Chờ thanh toán -->
                                            <div class="d-flex gap-2">
                                                <a href="<?= url('booking/payment/' . $o['ma_don_hang']) ?>" class="btn btn-warning text-dark rounded-pill flex-grow-1 fw-bold">
                                                    <i class="bi bi-credit-card me-1"></i>Thanh Toán
                                                </a>
                                                <form action="<?= url('booking/cancel') ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng <?= htmlspecialchars($o['ma_don_hang']) ?> không? Số lượng vé giữ chỗ sẽ được giải phóng lại hệ thống.');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="ma_don_hang" value="<?= htmlspecialchars($o['ma_don_hang']) ?>">
                                                    <input type="hidden" name="ly_do" value="Khách hàng hủy từ danh sách vé">
                                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3" title="Hủy đơn giữ chỗ này">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Trạng thái trống khi lọc không có vé -->
                <div id="ticketFilterEmptyState" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white d-none mt-4">
                    <div class="text-muted mb-2">
                        <i class="bi bi-ticket-perforated fs-1 text-secondary opacity-50"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Không có vé nào thuộc danh mục này</h6>
                    <p class="text-muted small mb-3">Bạn chưa có đơn đặt vé nào ở trạng thái đã chọn.</p>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="document.querySelector('[data-ticket-filter=\'all\']').click();">
                            Xem tất cả các vé
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: LỊCH SỬ GIAO DỊCH -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'transactions') ? 'show active' : '' ?>" id="tab-transactions" role="tabpanel" style="<?= ($activeTab === 'transactions') ? 'display: block;' : 'display: none;' ?>">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-receipt me-2 text-primary"></i>Nhật Ký Giao Dịch & Đối Soát Thanh Toán
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Mã Giao Dịch</th>
                                <th>Mã Đơn Hàng</th>
                                <th>Cổng / Phương Thức</th>
                                <th>Số Tiền</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        Chưa có bản ghi giao dịch nào được hoàn tất.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td><strong class="text-dark"><?= htmlspecialchars($t['ma_giao_dich'] ?? 'TXN---') ?></strong></td>
                                        <td>
                                            <a href="<?= url('voucher/' . $t['ma_don_hang']) ?>" class="text-primary fw-semibold text-decoration-none">
                                                <?= htmlspecialchars($t['ma_don_hang']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <?= strtoupper($t['cong_thanh_toan']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-danger"><?= \App\Helpers\FormatHelper::tienTe((float)$t['so_tien']) ?></td>
                                        <td>
                                            <?php if ($t['trang_thai'] === 'thanh_cong'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    <i class="bi bi-check-circle me-1"></i>Thành công
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis">
                                                    <?= htmlspecialchars($t['trang_thai']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= \App\Helpers\FormatHelper::ngayThang($t['ngay_tao']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: ĐỊA ĐIỂM YÊU THÍCH (WISHLIST) -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'favorites') ? 'show active' : '' ?>" id="tab-favorites" role="tabpanel" style="<?= ($activeTab === 'favorites') ? 'display: block;' : 'display: none;' ?>">
            <?php if (empty($favorites)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="text-muted mb-3">
                        <i class="bi bi-heart fs-1 text-danger opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Danh sách yêu thích đang trống</h5>
                    <p class="text-muted small mb-4">Bạn chưa lưu địa điểm nào. Hãy khám phá và nhấn nút trái tim để lưu lại những tọa độ muốn ghé thăm nhé!</p>
                    <div>
                        <a href="<?= url('locations') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-compass me-1"></i>Khám phá địa điểm ngay
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($favorites as $fav): ?>
                        <div class="col-lg-6 col-xl-4 fav-item-col" id="fav-item-<?= (int)$fav['id'] ?>">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                                <div class="position-relative">
                                    <img src="<?= htmlspecialchars(hinh_anh($fav['anh_dai_dien'])) ?>" 
                                         alt="<?= htmlspecialchars($fav['ten_dia_diem']) ?>" 
                                         style="height: 180px; width: 100%; object-fit: cover;" loading="lazy">
                                    
                                    <span class="badge position-absolute top-0 start-0 m-2 <?= $fav['loai_danh_muc'] === 'am_thuc' ? 'bg-warning text-dark' : 'bg-primary text-white' ?> rounded-pill px-3 py-1 fw-bold small shadow-sm">
                                        <?= htmlspecialchars($fav['ten_danh_muc']) ?>
                                    </span>

                                    <!-- Nút Bỏ thích nhanh -->
                                    <button type="button" 
                                            class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm text-danger d-flex align-items-center justify-content-center" 
                                            style="width: 34px; height: 34px;" 
                                            title="Bỏ khỏi yêu thích"
                                            onclick="xoaYeuThich(<?= (int)$fav['id'] ?>)">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="small text-muted"><i class="bi bi-calendar-check me-1"></i>Đã lưu: <?= date('d/m/Y', strtotime($fav['ngay_yeu_thich'])) ?></span>
                                            <span class="small text-warning fw-bold"><i class="bi bi-star-fill me-1"></i><?= number_format((float)$fav['diem_danh_gia'], 1) ?></span>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-1 line-clamp-1" style="font-size: 1.05rem;">
                                            <a href="<?= url('dia-diem/' . $fav['duong_dan']) ?>" class="text-dark text-decoration-none">
                                                <?= htmlspecialchars($fav['ten_dia_diem']) ?>
                                            </a>
                                        </h4>
                                        <p class="text-muted small line-clamp-2 mb-3" style="font-size: 12px;">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($fav['dia_chi']) ?>
                                        </p>
                                    </div>

                                    <div class="pt-2 border-top d-flex align-items-center justify-content-between mt-auto">
                                        <span class="text-danger fw-bold small">
                                            <?= \App\Helpers\FormatHelper::tienTe((float)$fav['gia_thap_nhat']) ?> - <?= \App\Helpers\FormatHelper::tienTe((float)$fav['gia_cao_nhat']) ?>
                                        </span>
                                        <a href="<?= url('dia-diem/' . $fav['duong_dan']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Chi tiết &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB: ĐÁNH GIÁ CỦA TÔI -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'reviews') ? 'show active' : '' ?>" id="tab-reviews" role="tabpanel" style="<?= ($activeTab === 'reviews') ? 'display: block;' : 'display: none;' ?>">
            <?php if (empty($myReviews)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="mb-3">
                        <i class="bi bi-chat-square-heart text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Bạn chưa có đánh giá nào</h5>
                    <p class="text-muted small mb-4">Hãy chia sẻ trải nghiệm thực tế của bạn tại các địa điểm tham quan & ẩm thực Nha Trang nhé!</p>
                    <div>
                        <a href="<?= url('locations') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-compass me-2"></i>Khám phá địa điểm ngay
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($myReviews as $rev): ?>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img src="<?= htmlspecialchars(\App\Helpers\FormatHelper::hinhAnh($rev['anh_dai_dien'] ?? '')) ?>" 
                                             alt="<?= htmlspecialchars($rev['ten_dia_diem']) ?>" 
                                             class="rounded-3 object-fit-cover shadow-sm" style="width: 64px; height: 64px; flex-shrink: 0;">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <span class="badge bg-light text-secondary border small mb-1"><?= htmlspecialchars($rev['ten_danh_muc']) ?></span>
                                            <h6 class="fw-bold text-dark text-truncate mb-1">
                                                <a href="<?= url('dia-diem/' . $rev['duong_dan']) ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?= htmlspecialchars($rev['ten_dia_diem']) ?>
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 small flex-wrap">
                                                <div class="text-warning"><?= \App\Helpers\FormatHelper::saoHtml((float)$rev['so_sao']) ?></div>
                                                <span class="fw-bold text-dark"><?= number_format((float)$rev['so_sao'], 1) ?>/5</span>
                                                <span class="text-muted">&bull; <?= date('d/m/Y', strtotime($rev['ngay_tao'])) ?></span>
                                                <?php if (!empty($rev['da_mua_ve'])): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 10px;">
                                                        <i class="bi bi-patch-check-fill me-1"></i>Đã mua vé
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                        <p class="text-secondary small mb-0 lh-base">
                                            "<?= nl2br(htmlspecialchars($rev['noi_dung'])) ?>"
                                        </p>
                                        <?php if (!empty($rev['hinh_anh'])): ?>
                                            <div class="mt-2 pt-2 border-top">
                                                <span class="text-muted small d-block mb-1"><i class="bi bi-camera-fill me-1 text-primary"></i>Ảnh bạn đã đăng:</span>
                                                <img src="<?= htmlspecialchars(\App\Helpers\FormatHelper::hinhAnh($rev['hinh_anh'])) ?>" 
                                                     alt="Ảnh đánh giá" 
                                                     class="rounded-3 border object-fit-cover shadow-sm" 
                                                     style="max-height: 90px; max-width: 130px; cursor: pointer;"
                                                     onclick="window.open('<?= htmlspecialchars(\App\Helpers\FormatHelper::hinhAnh($rev['hinh_anh'])) ?>', '_blank')">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                    <span class="badge <?= ($rev['trang_thai'] === 'da_duyet') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill px-3 py-1 fw-semibold small">
                                        <i class="bi <?= ($rev['trang_thai'] === 'da_duyet') ? 'bi-check-circle-fill' : 'bi-clock-history' ?> me-1"></i>
                                        <?= ($rev['trang_thai'] === 'da_duyet') ? 'Đã duyệt công khai' : 'Đang chờ duyệt' ?>
                                    </span>
                                    <a href="<?= url('dia-diem/' . $rev['duong_dan']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Xem địa điểm &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: CÀI ĐẶT TÀI KHOẢN -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'account') ? 'show active' : '' ?>" id="tab-account" role="tabpanel" style="<?= ($activeTab === 'account') ? 'display: block;' : 'display: none;' ?>">
            <div class="row g-4">
                <!-- Cập nhật thông tin cơ bản -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-person-gear me-2 text-primary"></i>Thông Tin Cá Nhân
                        </h5>

                        <form action="<?= url('profile/update') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Địa chỉ Email (Đăng nhập)</label>
                                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                                <div class="form-text small">Email không thể thay đổi để đảm bảo xác minh tài khoản.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required value="<?= htmlspecialchars($user['ho_ten']) ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Số điện thoại liên hệ</label>
                                <input type="tel" name="so_dien_thoai" class="form-control" placeholder="0912..." value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>">
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i>Lưu Thay Đổi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Đổi mật khẩu -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-shield-lock me-2 text-danger"></i>Đổi Mật Khẩu
                        </h5>

                        <form action="<?= url('profile/change-password') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" name="mat_khau_cu" class="form-control" required placeholder="Nhập mật khẩu hiện tại...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="mat_khau_moi" class="form-control" required placeholder="Tối thiểu 6 ký tự...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="xac_nhan_mat_khau" class="form-control" required placeholder="Nhập lại mật khẩu mới...">
                            </div>

                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-semibold">
                                <i class="bi bi-key me-1"></i>Cập Nhật Mật Khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function switchProfileTab(tabName) {
        if (!tabName) return;

        // 1. Cập nhật trạng thái active của các nút tab
        const allBtns = document.querySelectorAll('#profileTabs .nav-link');
        allBtns.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
        });

        const activeBtn = document.getElementById(tabName + '-tab') || document.querySelector(`[data-tab-name="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.setAttribute('aria-selected', 'true');
        }

        // 2. Chuyển đổi hiển thị các tab-pane
        const allPanes = document.querySelectorAll('#profileTabsContent .tab-pane');
        allPanes.forEach(pane => {
            pane.classList.remove('show', 'active');
            pane.style.display = 'none';
        });

        const activePane = document.getElementById('tab-' + tabName);
        if (activePane) {
            activePane.style.display = 'block';
            void activePane.offsetWidth; // Trigger reflow để fade mượt mà
            activePane.classList.add('show', 'active');
        }

        // 3. Cập nhật URL trình duyệt mà không tải lại trang
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }
    }

    // Tự động đồng bộ tab dựa trên tham số URL khi trang tải
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam && document.getElementById('tab-' + tabParam)) {
            switchProfileTab(tabParam);
        }

        // Khởi tạo bộ lọc trạng thái vé & E-Voucher
        const ticketFilterBtns = document.querySelectorAll('.ticket-filter-btn');
        const ticketCards = document.querySelectorAll('.ticket-card-col');
        const ticketEmptyState = document.getElementById('ticketFilterEmptyState');

        ticketFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                ticketFilterBtns.forEach(b => {
                    b.classList.remove('btn-primary', 'btn-success', 'btn-secondary', 'btn-danger', 'btn-warning', 'text-dark', 'active');
                    const f = b.getAttribute('data-ticket-filter');
                    if (f === 'valid') b.className = 'btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold ticket-filter-btn';
                    else if (f === 'expired') b.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn';
                    else if (f === 'cancelled') b.className = 'btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold ticket-filter-btn';
                    else if (f === 'pending') b.className = 'btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1 fw-semibold ticket-filter-btn';
                    else b.className = 'btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn';
                });

                const filter = this.getAttribute('data-ticket-filter');
                if (filter === 'valid') this.className = 'btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active';
                else if (filter === 'expired') this.className = 'btn btn-sm btn-secondary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active';
                else if (filter === 'cancelled') this.className = 'btn btn-sm btn-danger rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active';
                else if (filter === 'pending') this.className = 'btn btn-sm btn-warning text-dark rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active';
                else this.className = 'btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold ticket-filter-btn active';

                let visibleCount = 0;
                ticketCards.forEach(card => {
                    const status = card.getAttribute('data-ticket-status');
                    if (filter === 'all' || status === filter) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (ticketEmptyState) {
                    if (visibleCount === 0) {
                        ticketEmptyState.classList.remove('d-none');
                    } else {
                        ticketEmptyState.classList.add('d-none');
                    }
                }
            });
        });
    });

    function xoaYeuThich(id) {
        if (!confirm('Bạn có chắc chắn muốn bỏ địa điểm này khỏi danh sách yêu thích?')) return;
        const csrfToken = "<?= csrf_token() ?>";

        fetch("<?= url('api/favorites/toggle') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                dia_diem_id: id,
                csrf_token: csrfToken
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const el = document.getElementById('fav-item-' + id);
                if (el) {
                    el.style.transition = 'all 0.3s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        el.remove();
                        const remaining = document.querySelectorAll('.fav-item-col').length;
                        const tabBtn = document.getElementById('favorites-tab');
                        if (tabBtn) {
                            tabBtn.innerHTML = '<i class="bi bi-heart-fill me-2 text-danger"></i>Địa Điểm Yêu Thích (' + remaining + ')';
                        }
                    }, 300);
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối máy chủ.');
        });
    }
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>


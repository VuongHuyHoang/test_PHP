<?php require_once dirname(__DIR__, 2) . '/layouts/header.php'; ?>
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

                <!-- 3 Thẻ Tóm Tắt Nhanh -->
                <div class="col-md-5">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25">
                                <span class="small text-white-50 d-block">Vé sở hữu</span>
                                <strong class="fs-4 text-warning"><?= number_format($validTicketsCount) ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25">
                                <span class="small text-white-50 d-block">Đã thanh toán</span>
                                <strong class="fs-6 text-white"><?= \App\Helpers\FormatHelper::tienTe($totalSpent) ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 border border-white border-opacity-25">
                                <span class="small text-white-50 d-block">Chờ xử lý</span>
                                <strong class="fs-4 text-info"><?= number_format($pendingPaymentCount) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh Tab Điều Hướng -->
        <div class="px-4 py-2 bg-light border-top">
            <ul class="nav nav-pills gap-2" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 fw-semibold <?= ($activeTab === 'tickets') ? 'active' : '' ?>" 
                            id="tickets-tab" data-bs-toggle="pill" data-bs-target="#tab-tickets" type="button" role="tab">
                        <i class="bi bi-ticket-perforated me-2"></i>Vé & E-Voucher Của Tôi (<?= count($orders) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 fw-semibold <?= ($activeTab === 'transactions') ? 'active' : '' ?>" 
                            id="transactions-tab" data-bs-toggle="pill" data-bs-target="#tab-transactions" type="button" role="tab">
                        <i class="bi bi-clock-history me-2"></i>Lịch Sử Giao Dịch (<?= count($transactions) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 fw-semibold <?= ($activeTab === 'account') ? 'active' : '' ?>" 
                            id="account-tab" data-bs-toggle="pill" data-bs-target="#tab-account" type="button" role="tab">
                        <i class="bi bi-gear me-2"></i>Cài Đặt Tài Khoản
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
        <div class="tab-pane fade <?= ($activeTab === 'tickets') ? 'show active' : '' ?>" id="tab-tickets" role="tabpanel">
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
                <div class="row g-4">
                    <?php foreach ($orders as $o): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column bg-white">
                                <!-- Ảnh địa điểm -->
                                <div class="position-relative" style="height: 160px;">
                                    <?php 
                                        $locImg = $o['anh_dia_diem'] ?? 'assets/images/placeholder.jpg';
                                        $imgUrl = str_starts_with($locImg, 'http') ? $locImg : url($locImg);
                                    ?>
                                    <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($o['ten_dia_diem'] ?? 'Vé tham quan') ?>" class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <?php if ($o['trang_thai_thanh_toan'] === 'da_thanh_toan'): ?>
                                            <?php if ($o['trang_thai_don_hang'] === 'hoan_thanh'): ?>
                                                <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm">
                                                    <i class="bi bi-check2-all me-1"></i>Đã đổi vé vào cổng
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                                    <i class="bi bi-shield-check me-1"></i>Vé hợp lệ
                                                </span>
                                            <?php endif; ?>
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
                                                <strong class="text-dark">
                                                    <?= !empty($o['ngay_tham_quan']) ? \App\Helpers\FormatHelper::ngayThang($o['ngay_tham_quan']) : 'Theo ngày đặt' ?>
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

                                    <!-- Thao tác vé -->
                                    <div class="d-flex gap-2 pt-2">
                                        <?php if ($o['trang_thai_thanh_toan'] === 'da_thanh_toan' || $o['phuong_thuc_thanh_toan'] === 'tai_quay'): ?>
                                            <a href="<?= url('voucher/' . $o['ma_don_hang']) ?>" class="btn btn-primary rounded-pill w-100 fw-semibold">
                                                <i class="bi bi-qr-code me-1"></i>Xem E-Voucher & QR
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= url('booking/payment/' . $o['ma_don_hang']) ?>" class="btn btn-warning text-dark rounded-pill w-100 fw-bold">
                                                <i class="bi bi-credit-card me-1"></i>Thanh Toán Ngay
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: LỊCH SỬ GIAO DỊCH -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'transactions') ? 'show active' : '' ?>" id="tab-transactions" role="tabpanel">
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
        <!-- TAB 3: CÀI ĐẶT TÀI KHOẢN -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'account') ? 'show active' : '' ?>" id="tab-account" role="tabpanel">
            <div class="row g-4">
                <!-- Cập nhật thông tin cơ bản -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-person-gear me-2 text-primary"></i>Thông Tin Cá Nhân
                        </h5>

                        <form action="<?= url('profile/update') ?>" method="POST">
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

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

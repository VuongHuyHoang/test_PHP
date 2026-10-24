<?php
/**
 * @var string $title
 */
require_once dirname(__DIR__, 2) . '/layouts/admin_layout.php';
?>

<div class="container-fluid p-0">
    <!-- Hero Banner / Chào Mừng -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #0284c7 100%);">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-3">
                        <i class="bi bi-book-half me-1"></i>Tài Liệu Hướng Dẫn Vận Hành
                    </span>
                    <h1 class="h2 fw-bold text-white mb-2">Cẩm Nang Quản Trị & Vận Hành Hệ Thống</h1>
                    <p class="text-white-50 fs-6 mb-4 lh-base" style="max-width: 680px;">
                        Chào mừng bạn đến với trung tâm tài liệu quản trị <strong>Nha Trang Staycation</strong>. Trang này tổng hợp hướng dẫn chi tiết từng phân hệ chức năng, quy trình kiểm duyệt dữ liệu, quản lý vé và các mẹo tối ưu trải nghiệm người dùng.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#sec-overview" class="btn btn-light btn-sm rounded-pill fw-semibold px-3 py-2 shadow-sm">
                            <i class="bi bi-speedometer2 text-primary me-1"></i>Tổng Quan
                        </a>
                        <a href="#sec-locations" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3 py-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa Điểm
                        </a>
                        <a href="#sec-tickets" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3 py-2">
                            <i class="bi bi-ticket-detailed-fill text-info me-1"></i>Gói Vé & Tồn Kho
                        </a>
                        <a href="#sec-orders" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3 py-2">
                            <i class="bi bi-qr-code-scan text-success me-1"></i>Soát Vé QR
                        </a>
                        <a href="#sec-reviews" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3 py-2">
                            <i class="bi bi-star-fill text-warning me-1"></i>Đánh Giá
                        </a>
                        <a href="#sec-reports" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3 py-2">
                            <i class="bi bi-bar-chart-fill text-info me-1"></i>Báo Cáo
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-center">
                    <div class="text-center p-3 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-20 shadow">
                        <i class="bi bi-shield-check display-3 text-warning"></i>
                        <h6 class="text-white fw-bold mt-2 mb-1">Hệ Thống Đạt Chuẩn</h6>
                        <small class="text-white-50">Phiên bản Admin Portal 2.0</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards Summary of Sections -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-primary-subtle text-primary">
                        <i class="bi bi-geo-alt-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Địa Điểm & Bản Đồ</h6>
                        <p class="text-muted small mb-0">Cập nhật tọa độ GPS, nội dung, bộ sưu tập ảnh và bật/tắt bán vé.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-success-subtle text-success">
                        <i class="bi bi-ticket-perforated-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Gói Vé & Tồn Kho Theo Ngày</h6>
                        <p class="text-muted small mb-0">Thiết lập quota vé cho từng ngày cụ thể, phòng chống quá tải du lịch.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-warning-subtle text-warning">
                        <i class="bi bi-qr-code-scan fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Soát Vé Tức Thì (Check-in)</h6>
                        <p class="text-muted small mb-0">Tìm kiếm theo mã vé / SĐT và đổi vé 1 chạm chống tái sử dụng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi Tiết Hướng Dẫn Từng Phân Hệ -->
    <div class="row g-4">
        <!-- Cột Nội Dung Chính -->
        <div class="col-lg-12">

            <!-- 1. TỔNG QUAN HỆ THỐNG -->
            <section id="sec-overview" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1">Phân hệ 1</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Bảng Điều Khiển Tổng Quan (Dashboard)</h2>
                        </div>
                        <a href="<?= url('admin/dashboard') ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold">
                            Đi tới Tổng Quan <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-3">
                        Đây là trung tâm điều khiển chính hiển thị tình trạng sức khỏe kinh doanh của hệ thống theo thời gian thực:
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-cash-stack text-success me-1"></i> Tổng Doanh Thu</span>
                                <small class="text-muted">Tổng số tiền từ tất cả các đơn hàng đã thanh toán thành công (kèm chỉ số doanh thu hôm nay).</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-ticket-perforated-fill text-primary me-1"></i> Vé Phát Hành</span>
                                <small class="text-muted">Tổng số lượng vé người lớn + trẻ em đã phát hành và tổng số đơn đặt trực tuyến.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-geo-alt-fill text-warning me-1"></i> Địa Điểm</span>
                                <small class="text-muted">Tổng số địa điểm du lịch & ẩm thực đang hoạt động trên bản đồ số Nha Trang.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-people-fill text-info me-1"></i> Người Dùng</span>
                                <small class="text-muted">Số lượng tài khoản khách hàng đã đăng ký trong hệ sinh thái Staycation.</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-info-circle-fill fs-5 text-primary flex-shrink-0"></i>
                        <span class="small">Biểu đồ doanh thu 7 ngày và biểu đồ tròn phân bổ trạng thái đơn hàng giúp quản trị viên nắm bắt nhanh xu hướng bán vé trong tuần.</span>
                    </div>
                </div>
            </section>

            <!-- 2. QUẢN LÝ ĐỊA ĐIỂM -->
            <section id="sec-locations" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger text-white rounded-pill px-3 py-1">Phân hệ 2</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Quản Lý Địa Điểm (Locations)</h2>
                        </div>
                        <a href="<?= url('admin/locations') ?>" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold">
                            Đi tới Danh Sách Địa Điểm <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-3">
                        Quản lý cơ sở dữ liệu các điểm tham quan, di tích văn hóa, bãi biển và nhà hàng đặc sản Nha Trang:
                    </p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-plus-circle-fill text-success me-1"></i> 1. Thêm mới / Chỉnh sửa địa điểm</h6>
                                <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                    <li><strong>Tên địa điểm:</strong> Đặt tên rõ ràng, chuẩn tiếng Việt (ví dụ: <em>Tháp Bà Ponagar Nha Trang</em>).</li>
                                    <li><strong>Danh mục:</strong> Chọn đúng nhóm phân loại để hiển thị bộ lọc chính xác trên trang chủ.</li>
                                    <li><strong>Đánh dấu nổi bật (Featured):</strong> Bật tùy chọn này để ưu tiên hiển thị ở các vị trí vàng trang chủ.</li>
                                    <li><strong>Bật tính năng Bán vé:</strong> Kích hoạt ô <code>Có bán vé trực tuyến</code> nếu địa điểm này cung cấp vé vào cổng hoặc tour trải nghiệm.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pin-map-fill text-danger me-1"></i> 2. Nhập tọa độ GPS & Bộ sưu tập ảnh</h6>
                                <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                    <li><strong>Vĩ độ (Latitude) & Kinh độ (Longitude):</strong> Nhập chính xác tọa độ thập phân (ví dụ: <code>12.2654, 109.1958</code>). Tọa độ này được dùng trực tiếp cho bản đồ vệ tinh và chỉ đường OSRM.</li>
                                    <li><strong>Ảnh đại diện & Gallery:</strong> Cho phép tải lên nhiều ảnh chất lượng cao cùng lúc. Khách hàng có thể chuyển ảnh mượt mà trên trang chi tiết.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-warning-subtle border border-warning-subtle text-dark p-3 rounded-3 mt-3 small">
                        <strong><i class="bi bi-lightbulb-fill text-warning me-1"></i> Mẹo lấy tọa độ nhanh:</strong> Mở <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer" class="text-decoration-underline text-dark fw-bold">Google Maps</a>, tìm địa điểm cần thêm, bấm chuột phải vào điểm đó &rarr; chọn dòng tọa độ đầu tiên để sao chép, sau đó dán vào 2 ô Vĩ độ và Kinh độ trong form admin.
                    </div>
                </div>
            </section>

            <!-- 3. QUẢN LÝ DANH MỤC -->
            <section id="sec-categories" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1">Phân hệ 3</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-tags-fill text-secondary me-2"></i>Quản Lý Danh Mục (Categories)</h2>
                        </div>
                        <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold">
                            Đi tới Quản Lý Danh Mục <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8">
                            <p class="text-secondary mb-2">
                                Giúp phân loại khoa học các dịch vụ theo thị hiếu du khách:
                            </p>
                            <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                <li>Thiết lập danh mục chính: <em>Du Lịch & Vé Tham Quan</em>, <em>Ẩm Thực & Đặc Sản Nha Trang</em>, <em>Di Tích Lịch Sử</em>, <em>Khu Nghỉ Dưỡng Biển</em>...</li>
                                <li>Tùy chọn icon Bootstrap biểu trưng và thứ tự sắp xếp ưu tiên hiển thị trên thanh Menu và bộ lọc khám phá.</li>
                                <li>Slug URL chuẩn SEO thân thiện, tự động tạo theo tên tiếng Việt không dấu.</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded-4 border">
                                <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Icon Bootstrap 5</span>
                                <p class="small text-muted mb-0">Sử dụng mã icon như <code>bi-water</code>, <code>bi-cup-hot</code>, <code>bi-compass</code> để hiển thị trực quan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. GÓI VÉ & TỒN KHO THEO NGÀY -->
            <section id="sec-tickets" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success text-white rounded-pill px-3 py-1">Phân hệ 4</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-ticket-detailed-fill text-success me-2"></i>Gói Vé & Tồn Kho Theo Ngày (Tickets & Quota)</h2>
                        </div>
                        <a href="<?= url('admin/tickets') ?>" class="btn btn-outline-success btn-sm rounded-pill fw-semibold">
                            Đi tới Quản Lý Gói Vé <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-3">
                        Đây là phân hệ cốt lõi quyết định doanh thu bán vé trực tuyến của từng địa điểm:
                    </p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-success-subtle bg-light p-3 rounded-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-success rounded-circle p-2"><i class="bi bi-1-circle fs-6"></i></span>
                                    <h6 class="fw-bold text-dark mb-0">Cấu hình gói vé (Ticket Packages)</h6>
                                </div>
                                <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                    <li>Tạo nhiều gói vé cho một địa điểm (ví dụ: Vé vào cổng tiêu chuẩn, Vé VIP kèm buffet, Combo tàu đáy kính).</li>
                                    <li>Nhập đơn giá riêng cho <strong>Người lớn</strong> và <strong>Trẻ em</strong>.</li>
                                    <li>Mô tả chi tiết quyền lợi bao gồm trong vé để khách hàng an tâm lựa chọn.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-primary-subtle bg-light p-3 rounded-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-2-circle fs-6"></i></span>
                                    <h6 class="fw-bold text-dark mb-0">Kiểm soát Quota / Tồn kho theo ngày</h6>
                                </div>
                                <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                    <li>Chọn ngày sử dụng trong lịch và nhập số lượng vé tối đa được phép bán.</li>
                                    <li>Khi số lượng người đặt đạt giới hạn, hệ thống sẽ tự động khóa ngày đó để tránh quá tải dịch vụ.</li>
                                    <li>Tránh hoàn toàn rủi ro bán vượt quá sức chứa (Overbooking) trong mùa du lịch cao điểm.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. TRA CỨU & SOÁT VÉ QR -->
            <section id="sec-orders" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1">Phân hệ 5</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-qr-code-scan text-primary me-2"></i>Tra Cứu & Soát Vé QR Vào Cổng (Check-in)</h2>
                        </div>
                        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold">
                            Đi tới Bàn Soát Vé <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-3">
                        Quy trình chuẩn dành cho nhân viên soát vé tại cửa soát vé của các điểm du lịch:
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center h-100 bg-white">
                                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle mb-2">
                                    <i class="bi bi-search fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Bước 1: Tra cứu mã vé</h6>
                                <p class="small text-muted mb-0">Quét mã QR từ điện thoại của khách hoặc nhập Mã đơn hàng / Số điện thoại vào ô tìm kiếm.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center h-100 bg-white">
                                <div class="badge bg-warning-subtle text-warning p-3 rounded-circle mb-2">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Bước 2: Đối chiếu hợp lệ</h6>
                                <p class="small text-muted mb-0">Kiểm tra trạng thái thanh toán là <strong>Đã thanh toán</strong>, ngày sử dụng vé trùng với ngày thực tế và số lượng khách.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 text-center h-100 bg-white">
                                <div class="badge bg-success-subtle text-success p-3 rounded-circle mb-2">
                                    <i class="bi bi-check2-circle fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Bước 3: Xác nhận đổi vé</h6>
                                <p class="small text-muted mb-0">Bấm nút <strong>Xác nhận hoàn thành</strong> để đóng vé. Vé sẽ chuyển sang trạng thái <em>Đã sử dụng</em> để chống tái dùng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-check-circle-fill fs-5 text-success flex-shrink-0"></i>
                        <span class="small">Sau khi vé được đổi thành công, khách hàng có thể đăng nhập vào tài khoản để viết đánh giá kèm huy hiệu <strong>Đã mua vé & trải nghiệm</strong>.</span>
                    </div>
                </div>
            </section>

            <!-- 6. QUẢN LÝ ĐÁNH GIÁ -->
            <section id="sec-reviews" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Phân hệ 6</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Quản Lý Đánh Giá & Phản Hồi Khách Hàng (Reviews)</h2>
                        </div>
                        <a href="<?= url('admin/reviews') ?>" class="btn btn-outline-warning btn-sm rounded-pill fw-semibold text-dark">
                            Đi tới Quản Lý Đánh Giá <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark mb-2">Nguyên tắc kiểm duyệt đánh giá:</h6>
                            <ul class="small text-secondary ps-3 mb-0 lh-lg">
                                <li><strong>Huy hiệu minh bạch (Verified Buyer):</strong> Các đánh giá từ khách hàng đã mua và thanh toán vé hợp lệ sẽ tự động hiển thị nhãn <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-patch-check-fill me-1"></i>Đã mua vé & trải nghiệm</span>.</li>
                                <li><strong>Kiểm tra ảnh đính kèm:</strong> Nhấp vào ảnh để phóng to kiểm tra chất lượng hình ảnh trải nghiệm thực tế do khách chụp.</li>
                                <li><strong>Ẩn/Hiện đánh giá:</strong> Quản trị viên có quyền bấm <strong>Ẩn</strong> các nhận xét chứa từ ngữ thiếu văn minh, spam hoặc thông tin sai lệch trước khi công khai ra website.</li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-4 border text-center">
                                <div class="text-warning fs-3 mb-1">★★★★★</div>
                                <h6 class="fw-bold text-dark mb-1">Xây dựng niềm tin số</h6>
                                <p class="small text-muted mb-0">Hệ sinh thái đánh giá minh bạch giúp tăng tỷ lệ chốt vé lên hơn 35% trên các nền tảng OTA.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. BÁO CÁO THỐNG KÊ -->
            <section id="sec-reports" class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info text-dark rounded-pill px-3 py-1">Phân hệ 7</span>
                            <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-bar-chart-line-fill text-success me-2"></i>Báo Cáo Doanh Thu & Xuất Dữ Liệu (Reports)</h2>
                        </div>
                        <a href="<?= url('admin/reports') ?>" class="btn btn-outline-info btn-sm rounded-pill fw-semibold text-dark">
                            Đi tới Báo Cáo Thống Kê <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-3">
                        Cung cấp bức tranh toàn cảnh về hiệu quả vận hành và dòng tiền doanh thu:
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100 bg-white">
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-calendar3-range text-primary me-1"></i> Lọc theo khoảng thời gian</h6>
                                <p class="small text-muted mb-0">Chọn khoảng ngày bắt đầu và kết thúc tùy ý (theo ngày, tuần, tháng hoặc quý) để phân tích biến động sức mua của du khách.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100 bg-white">
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-spreadsheet-fill text-success me-1"></i> Xuất dữ liệu CSV / Excel</h6>
                                <p class="small text-muted mb-0">Bấm nút <strong>Xuất CSV</strong> để tải toàn bộ bảng kê đơn hàng phục vụ công tác đối soát kế toán tài chính và lưu trữ nghiệp vụ.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. FAQ & HỖ TRỢ KỸ THUẬT -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-question-diamond-fill text-primary me-2"></i>Câu Hỏi Thường Gặp & Xử Lý Sự Cố (FAQ)</h5>
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    1. Khách hàng đến cổng nhưng quên mang điện thoại hoặc điện thoại hết pin thì xử lý thế nào?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-secondary small">
                                    Nhân viên soát vé chỉ cần hỏi <strong>Số điện thoại</strong> hoặc <strong>Họ tên khách hàng</strong> đã dùng khi đặt vé, sau đó nhập vào thanh tìm kiếm trong phân hệ <a href="<?= url('admin/orders') ?>" class="text-primary fw-semibold">Tra Cứu & Soát Vé</a>. Khi tìm thấy đơn hàng tương ứng, đối chiếu thông tin cá nhân và bấm xác nhận đổi vé bình thường.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    2. Khách hàng đã chuyển khoản quét VietQR nhưng trạng thái đơn chưa tự đổi sang Đã thanh toán?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-secondary small">
                                    Trong trường hợp ngân hàng chuyển tiền bị trễ mạng hoặc khách quên ghi đúng nội dung chuyển khoản, quản trị viên có thể kiểm tra biến động số dư tài khoản ngân hàng thực tế. Nếu tiền đã vào, quản trị viên bấm vào chi tiết đơn hàng và cập nhật thủ công trạng thái thanh toán sang <strong>Đã thanh toán</strong> để hệ thống tự cấp mã QR E-Voucher cho khách.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent fw-bold text-dark px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    3. Khi nào cần điều chỉnh Quota số lượng vé theo ngày?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-secondary small">
                                    Vào các dịp lễ hội như Festival Biển Nha Trang, Tết Nguyên Đán hoặc các ngày cuối tuần thời tiết đẹp, ban quản lý các điểm tham quan có thể tăng hoặc giảm mức giới hạn vé phát hành trong mục <a href="<?= url('admin/tickets') ?>" class="text-primary fw-semibold">Gói Vé & Tồn Kho</a> để kiểm soát lượng khách tập trung cùng thời điểm, đảm bảo an toàn trải nghiệm.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>


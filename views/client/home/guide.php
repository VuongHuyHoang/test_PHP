<?php
/**
 * Trang Hướng Dẫn Sử Dụng Nền Tảng - Nha Trang Staycation
 * @var string $title
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
require_once dirname(__DIR__, 2) . '/layouts/navbar.php';
?>

<main class="flex-grow-1 bg-light">
    <!-- Hero Banner Hướng Dẫn -->
    <section class="guide-hero position-relative py-5 text-white" style="background: linear-gradient(135deg, #091e3a 0%, #0d3b66 50%, #0284c7 100%);">
        <div class="container py-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold text-uppercase mb-3 shadow-sm" style="font-size: 11.5px; letter-spacing: 0.5px;">
                        <i class="bi bi-book-half me-1"></i>Trung Tâm Hướng Dẫn &amp; Trợ Giúp
                    </span>
                    <h1 class="display-5 fw-bold mb-3">
                        Cẩm Nang Sử Dụng Nền Tảng <br class="d-none d-md-inline">
                        <span class="text-warning">Nha Trang Staycation</span>
                    </h1>
                    <p class="lead text-white-50 mb-4" style="max-width: 650px; font-size: 16px; line-height: 1.6;">
                        Tất cả những gì bạn cần biết để khám phá du lịch &amp; ẩm thực Nha Trang: từ tìm kiếm địa điểm, đặt vé E-Voucher trong 3 phút, thanh toán VietQR tự động, đến lập tour thông minh bằng Trợ lý AI và dẫn đường OSRM.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#guide-steps" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-arrow-down-circle me-1"></i>Xem các bước hướng dẫn
                        </a>
                        <a href="#guide-faq" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-question-circle me-1"></i>Câu hỏi thường gặp
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                        <i class="bi bi-compass display-1 text-warning d-block mb-3"></i>
                        <h5 class="fw-bold text-white mb-1">Du Lịch Thông Minh</h5>
                        <p class="small text-white-50 mb-0">Tiết kiệm thời gian, bảo mật 100% &amp; không lo xếp hàng mua vé</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sóng biển trang trí -->
        <div class="position-absolute bottom-0 start-0 end-0 overflow-hidden" style="height: 24px; pointer-events: none;">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="width: 100%; height: 24px; fill: #f8fafc;">
                <path d="M0,0 C150,90 350,-40 500,40 C650,120 900,20 1200,40 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </section>

    <!-- Thanh Lối Tắt Nhanh (Anchor Bar) -->
    <div class="bg-white border-bottom sticky-top shadow-xs" style="top: 60px; z-index: 1010;">
        <div class="container py-2 overflow-x-auto">
            <div class="d-flex align-items-center gap-2 flex-nowrap" style="white-space: nowrap;">
                <span class="small fw-bold text-muted me-1"><i class="bi bi-signpost-split me-1 text-primary"></i>Nội dung:</span>
                <a href="#step-1" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small">1. Tìm kiếm địa điểm</a>
                <a href="#step-2" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small">2. Bản đồ &amp; Dẫn đường</a>
                <a href="#step-3" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small">3. Đặt vé &amp; VietQR</a>
                <a href="#step-4" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small">4. Sử dụng E-Voucher</a>
                <a href="#step-5" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small">5. Trợ lý AI Tour</a>
                <a href="#guide-faq" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">Câu hỏi thường gặp</a>
            </div>
        </div>
    </div>

    <!-- Danh Sách Các Bước Hướng Dẫn -->
    <section class="container py-5" id="guide-steps">
        <div class="row g-4 justify-content-center">

            <!-- BƯỚC 1: TÌM KIẾM & KHÁM PHÁ -->
            <div class="col-lg-10" id="step-1">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5 p-4 p-lg-5 bg-primary bg-opacity-10 d-flex flex-column justify-content-center text-center">
                            <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="bi bi-search-heart"></i>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3 py-1 mx-auto mb-2 fw-semibold">Bước 1</span>
                            <h4 class="fw-bold text-dark mb-1">Khám Phá Địa Điểm</h4>
                            <p class="text-muted small mb-0">Tìm kiếm nhanh theo tên, món ăn, danh lam thắng cảnh</p>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5">
                            <h5 class="fw-bold text-dark mb-3">Tìm Kiếm Thông Minh &amp; Đa Tiêu Chí</h5>
                            <ul class="d-flex flex-column gap-2.5 small text-secondary mb-4 list-unstyled">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Tìm kiếm Live Auto-Suggest:</strong> Gõ từ khóa bất kỳ (ví dụ: <em>Nem nướng, VinWonders, Tháp Bà</em>) vào thanh tìm kiếm ở đầu trang, hệ thống sẽ gợi ý ngay tức thì kèm giá vé và ảnh đại diện.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Bộ lọc danh mục 1 chạm:</strong> Lọc riêng danh mục <em>Ẩm thực đặc sản</em> hoặc <em>Du lịch &amp; Danh thắng</em>, lọc các điểm đang mở cửa hoặc có vé E-voucher trực tuyến.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Xem đánh giá minh bạch:</strong> Kiểm tra nhận xét thực tế từ cộng đồng du khách có gắn huy hiệu <code>✓ Đã mua vé &amp; trải nghiệm</code> và lọc xem nhận xét có hình ảnh.</div>
                                </li>
                            </ul>
                            <a href="<?= url('locations') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold">
                                <i class="bi bi-compass me-1"></i>Trải nghiệm tìm kiếm ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BƯỚC 2: BẢN ĐỒ & DẪN ĐƯỜNG OSRM -->
            <div class="col-lg-10" id="step-2">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="row g-0 align-items-center flex-md-row-reverse">
                        <div class="col-md-5 p-4 p-lg-5 bg-info bg-opacity-10 d-flex flex-column justify-content-center text-center">
                            <div class="rounded-circle bg-white text-info d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="bi bi-map"></i>
                            </div>
                            <span class="badge bg-info text-dark rounded-pill px-3 py-1 mx-auto mb-2 fw-semibold">Bước 2</span>
                            <h4 class="fw-bold text-dark mb-1">Bản Đồ &amp; Dẫn Đường</h4>
                            <p class="text-muted small mb-0">Định vị GPS &amp; Chỉ đường đường bộ nội bộ OSRM</p>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5">
                            <h5 class="fw-bold text-dark mb-3">Không Lo Lạc Đường Tại Nha Trang</h5>
                            <ul class="d-flex flex-column gap-2.5 small text-secondary mb-4 list-unstyled">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Định vị GPS &amp; Lọc bán kính:</strong> Bấm nút <code>Gần vị trí của tôi</code> và chọn bán kính <em>1 km, 3 km, 5 km</em>, bản đồ sẽ tự động vẽ vòng tròn và tìm các quán ăn ngon nhất quanh bạn.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Dẫn đường nội bộ OSRM:</strong> Bấm icon mũi tên chỉ đường tại bất kỳ thẻ địa điểm nào, hệ thống sẽ vẽ cung đường di chuyển tối ưu nhất kèm khoảng cách km và thời gian ước tính.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Tùy chọn phương tiện:</strong> Dễ dàng chuyển đổi giữa <code>🛵 Xe máy / Ô tô</code> và <code>🚶 Đi bộ ven biển</code> để xem lộ trình phù hợp.</div>
                                </li>
                            </ul>
                            <a href="<?= url('locations') ?>" class="btn btn-outline-info btn-sm rounded-pill px-4 fw-semibold text-dark">
                                <i class="bi bi-geo-alt me-1"></i>Mở bản đồ số Nha Trang
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BƯỚC 3: ĐẶT VÉ & THANH TOÁN VIETQR -->
            <div class="col-lg-10" id="step-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5 p-4 p-lg-5 bg-success bg-opacity-10 d-flex flex-column justify-content-center text-center">
                            <div class="rounded-circle bg-white text-success d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="bi bi-qr-code-scan"></i>
                            </div>
                            <span class="badge bg-success rounded-pill px-3 py-1 mx-auto mb-2 fw-semibold">Bước 3</span>
                            <h4 class="fw-bold text-dark mb-1">Đặt Vé &amp; VietQR</h4>
                            <p class="text-muted small mb-0">Thanh toán không tiền mặt an toàn trong 10 phút</p>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5">
                            <h5 class="fw-bold text-dark mb-3">Quy Trình Đặt Vé Tức Thì 3 Bước</h5>
                            <ul class="d-flex flex-column gap-2.5 small text-secondary mb-4 list-unstyled">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Chọn ngày &amp; số lượng vé:</strong> Hệ thống kiểm tra số lượng vé còn lại thực tế theo ngày (Atomic Stock) để đảm bảo không bị quá tải.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Đồng hồ giữ chỗ 10 phút:</strong> Sau khi tạo đơn, hệ thống sẽ tạm giữ vé cho bạn trong vòng 10 phút để bạn an tâm chuyển khoản.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Thanh toán VietQR NAPAS 24/7:</strong> Chỉ cần mở ứng dụng ngân hàng bất kỳ (MB, Vietcombank, Techcombank, Momo...), quét mã QR đã có sẵn số tiền và nội dung chuyển khoản.</div>
                                </li>
                            </ul>
                            <a href="<?= url('locations?loai=du_lich') ?>" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-semibold">
                                <i class="bi bi-ticket-perforated me-1"></i>Đặt vé tham quan ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BƯỚC 4: SỬ DỤNG E-VOUCHER TẠI CỔNG -->
            <div class="col-lg-10" id="step-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="row g-0 align-items-center flex-md-row-reverse">
                        <div class="col-md-5 p-4 p-lg-5 bg-warning bg-opacity-10 d-flex flex-column justify-content-center text-center">
                            <div class="rounded-circle bg-white text-warning d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="bi bi-phone"></i>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 mx-auto mb-2 fw-semibold">Bước 4</span>
                            <h4 class="fw-bold text-dark mb-1">Sử Dụng E-Voucher</h4>
                            <p class="text-muted small mb-0">Quét mã QR tại cổng kiểm soát - Không cần vé giấy</p>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5">
                            <h5 class="fw-bold text-dark mb-3">Vào Cổng Siêu Tốc Trong 30 Giây</h5>
                            <ul class="d-flex flex-column gap-2.5 small text-secondary mb-4 list-unstyled">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Xem vé mọi lúc trong Profile:</strong> Mở mục <em>Vé của tôi &amp; E-Voucher</em> trên thanh menu, bạn có thể lọc nhanh vé <code>Chưa sử dụng</code>, <code>Hết hạn</code> hoặc <code>Đã hủy</code>.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Quét mã QR tại quầy:</strong> Đưa màn hình điện thoại có mã QR E-Voucher cho nhân viên lễ tân/soát vé quét kiểm tra tính hợp lệ mà không cần in giấy.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Đặt lại vé đã hủy:</strong> Nếu một đơn vé của bạn bị hủy do quá giờ thanh toán, bạn có thể bấm nút <code>Đặt lại vé này</code> để tạo lại đơn chỉ với 1 chạm.</div>
                                </li>
                            </ul>
                            <a href="<?= url('profile') ?>" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-4 fw-semibold">
                                <i class="bi bi-person-circle me-1"></i>Xem ví vé của tôi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BƯỚC 5: TRỢ LÝ AI TOUR 24/7 -->
            <div class="col-lg-10" id="step-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5 p-4 p-lg-5 text-white d-flex flex-column justify-content-center text-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%);">
                            <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="bi bi-robot"></i>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 mx-auto mb-2 fw-semibold">Bước 5</span>
                            <h4 class="fw-bold mb-1 text-white">Trợ Lý Dịch Vụ AI Tour</h4>
                            <p class="small text-white-50 mb-0">Hỏi đáp &amp; Tự động lập lịch trình du lịch cá nhân hóa</p>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5">
                            <h5 class="fw-bold text-dark mb-3">Trí Tuệ Nhân Tạo Đồng Hành Cùng Bạn</h5>
                            <ul class="d-flex flex-column gap-2.5 small text-secondary mb-4 list-unstyled">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Tư vấn không ảo giác (RAG):</strong> AI được kết nối trực tiếp với 30 địa điểm và giá vé thực tế trong cơ sở dữ liệu Nha Trang Staycation, tuyệt đối không bịa đặt thông tin.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Đặt vé trực tiếp từ đoạn chat:</strong> Khi AI gợi ý các điểm đến, bạn có thể click nút <code>🎟️ Đặt vé ngay</code> ngay trên khung chat để mua vé.</div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                    <div><strong>Lưu lịch trình vào tài khoản:</strong> Bấm nút <code>Lưu lịch trình này</code> để lưu bản kế hoạch vào hồ sơ cá nhân và xem lại bất cứ lúc nào.</div>
                                </li>
                            </ul>
                            <a href="<?= url('ai-tour') ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-stars text-warning me-1"></i>Trò chuyện cùng AI Tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- KHỐI CÂU HỎI THƯỜNG GẶP (FAQ) -->
    <section class="py-5 bg-white border-top" id="guide-faq">
        <div class="container" style="max-width: 860px;">
            <div class="text-center mb-5">
                <span class="badge bg-light text-primary border rounded-pill px-3 py-1 fw-bold text-uppercase small mb-2">Giải Đáp Thắc Mắc</span>
                <h2 class="fw-bold text-dark">Câu Hỏi Thường Gặp (FAQ)</h2>
                <p class="text-muted small">Những điều du khách thường quan tâm nhất khi đặt dịch vụ trên Nha Trang Staycation</p>
            </div>

            <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border" id="faqAccordion">
                
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq-h1">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c1" aria-expanded="true" aria-controls="faq-c1">
                            1. Tôi có cần in phiếu E-Voucher ra giấy khi đến tham quan không?
                        </button>
                    </h3>
                    <div id="faq-c1" class="accordion-collapse collapse show" aria-labelledby="faq-h1" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small lh-base">
                            <strong>Hoàn toàn không cần in giấy!</strong> Bạn chỉ cần mở điện thoại, vào mục <em>Vé của tôi</em> và xuất trình mã QR E-Voucher cho nhân viên soát vé quét bằng máy quét hoặc điện thoại kiểm soát tại cổng. Bạn cũng có thể chụp ảnh màn hình lưu vào thư viện ảnh để phòng khi điện thoại mất sóng mạng.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq-h2">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c2" aria-expanded="false" aria-controls="faq-c2">
                            2. Chuyện gì xảy ra nếu tôi không thanh toán trong vòng 10 phút đếm ngược?
                        </button>
                    </h3>
                    <div id="faq-c2" class="accordion-collapse collapse" aria-labelledby="faq-h2" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small lh-base">
                            Nếu sau 10 phút bạn chưa thực hiện chuyển khoản, đơn hàng sẽ tự động chuyển sang trạng thái <code>Đã hủy</code> và số lượng vé đang giữ sẽ được tự động hoàn trả lại kho vé để người khác có thể đặt. Nếu bạn vẫn muốn mua, bạn chỉ cần vào mục <em>Vé của tôi &gt; Đã hủy</em> và bấm nút <strong>"Đặt lại vé này"</strong> để tạo đơn mới.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq-h3">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c3" aria-expanded="false" aria-controls="faq-c3">
                            3. Làm sao để tôi lưu lại lịch trình du lịch do Trợ lý AI tư vấn?
                        </button>
                    </h3>
                    <div id="faq-c3" class="accordion-collapse collapse" aria-labelledby="faq-h3" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small lh-base">
                            Ngay bên dưới mỗi câu trả lời gợi ý lịch trình của AI (ở cả trang <code>/ai-tour</code> và nút tròn nổi), luôn có nút <strong>"Lưu lịch trình này"</strong>. Khi bạn bấm vào, lịch trình sẽ được lưu vào cơ sở dữ liệu tài khoản của bạn và xuất hiện ngay ở cột bên phải mục <em>Lịch Trình Đã Lưu</em> để bạn mở xem lại bất cứ lúc nào.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq-h4">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c4" aria-expanded="false" aria-controls="faq-c4">
                            4. Tôi có thể chủ động hủy vé đã đặt nếu bận việc đột xuất không?
                        </button>
                    </h3>
                    <div id="faq-c4" class="accordion-collapse collapse" aria-labelledby="faq-h4" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small lh-base">
                            Đối với các đơn vé chưa thanh toán (đang giữ chỗ), bạn có thể bấm nút <strong>"Hủy đơn hàng này"</strong> ngay trên trang thanh toán hoặc trong danh sách vé. Đối với các vé đã thanh toán thành công, vui lòng liên hệ hotline ban quản lý được ghi dưới chân trang ít nhất 24 giờ trước ngày tham quan để được hỗ trợ theo quy định.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Khối Kêu Gọi Hành Động (CTA Bottom) -->
    <section class="py-5 text-center text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
        <div class="container py-3">
            <h3 class="fw-bold mb-2">Bạn Đã Sẵn Sàng Khám Phá Nha Trang?</h3>
            <p class="text-white-50 mb-4 small" style="max-width: 500px; margin: 0 auto;">Bắt đầu chuyến nghỉ dưỡng staycation tuyệt vời với hàng chục ưu đãi vé tham quan và ẩm thực hải sản tươi ngon nhất.</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= url('locations') ?>" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                    <i class="bi bi-compass me-1"></i>Khám phá địa điểm ngay
                </a>
                <a href="<?= url('ai-tour') ?>" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-stars me-1 text-warning"></i>Nhờ AI lên lịch trình
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>


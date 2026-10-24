<?php
/**
 * @var bool $isLoggedIn
 * @var array $myBookings
 * @var array $initialMessages
 * @var string $title
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
require_once dirname(__DIR__, 2) . '/layouts/navbar.php';
?>

<!-- ========================================================
     GIAO DIỆN TRỢ LÝ DỊCH VỤ AI TOUR (CHUẨN THEO MẪU TRIP.COM)
     ======================================================== -->
<div class="ai-page-backdrop py-4 py-md-5">
    <div class="container">
        <!-- Hộp Thoại Chat Trung Tâm Nổi Bật -->
        <div class="ai-trip-modal card border-0 shadow-2xl mx-auto overflow-hidden">
            
            <!-- 1. HEADER CHÍNH -->
            <div class="ai-trip-header px-4 py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                <!-- Bên Trái: Avatar Trợ Lý & Tên -->
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" 
                             style="width: 44px; height: 44px; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border: 2px solid #38bdf8;">
                            <img src="<?= url('assets/images/ai-avatar.png') ?>" 
                                 alt="AI Avatar" 
                                 class="rounded-circle w-100 h-100 object-fit-cover"
                                 onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/4712/4712035.png';">
                        </div>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" title="Trực tuyến 24/7"></span>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 16px;">
                            Trợ lý dịch vụ AI
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0 fw-semibold" style="font-size: 10px;">
                                Staycation Concierge
                            </span>
                        </h5>
                        <small class="text-muted" style="font-size: 11.5px;">Tư vấn điểm đến, ẩm thực &amp; lịch trình du lịch Nha Trang</small>
                    </div>
                </div>

                <!-- Bên Phải: QR Code / Ứng Dụng & Nút Thao Tác -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-md-flex align-items-center gap-2 text-muted pe-3 border-end" style="font-size: 12.5px;">
                        <span>Tải Ứng Dụng Staycation</span>
                        <div class="p-1 rounded bg-light border border-secondary border-opacity-25" title="Quét mã QR để mở trên di động">
                            <i class="bi bi-qr-code text-dark fs-5"></i>
                        </div>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 d-flex align-items-center gap-1" 
                                onclick="handleClearAiHistory()" 
                                title="Xóa lịch sử trò chuyện" 
                                style="font-size: 12px;">
                            <i class="bi bi-trash3"></i>
                            <span class="d-none d-sm-inline">Làm mới đoạn chat</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. KHUNG NỘI DUNG CHÍNH (2 CỘT: 70% KHUNG CHAT - 30% ĐẶT CHỖ & CAM KẾT) -->
            <div class="row g-0 ai-trip-body-row">
                
                <!-- CỘT TRÁI (MAIN CHAT AREA - 70%) -->
                <div class="col-lg-8 d-flex flex-column position-relative ai-chat-main-col">
                    
                    <!-- Khung Cuộn Tin Nhắn -->
                    <div class="ai-chat-scrollable p-3 p-md-4 flex-grow-1" id="aiMainMessagesArea">
                        
                        <!-- Nút Tải Tin Nhắn Cũ Nhất Trên Cùng -->
                        <?php if ($isLoggedIn && !empty($initialMessages)): ?>
                            <div class="text-center mb-3" id="loadMoreMessagesWrapper">
                                <button type="button" 
                                        class="btn btn-link text-primary text-decoration-none fw-semibold p-0 btn-load-prev-msg" 
                                        id="btnLoadPrevMsg" 
                                        onclick="loadPreviousMessages()" 
                                        style="font-size: 13.5px;">
                                    Xem Tin Nhắn Trước
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Tin Nhắn Khởi Đầu Mặc Định -->
                        <div class="ai-chat-bubble-row ai-msg-bot d-flex align-items-start gap-2 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 36px; height: 36px; background: #e0f2fe; color: #0284c7;">
                                <i class="bi bi-robot fs-5"></i>
                            </div>
                            <div class="ai-bubble-box p-3 rounded-4 bg-white border shadow-xs" style="max-width: 85%;">
                                <p class="mb-1 text-dark fw-bold" style="font-size: 14px;">
                                    👋 Xin chào<?= $isLoggedIn ? ' ' . htmlspecialchars($_SESSION['user_name']) : '' ?>! Tôi là Trợ lý AI du lịch Nha Trang.
                                </p>
                                <p class="text-secondary small mb-2" style="font-size: 13px; line-height: 1.55;">
                                    Tôi có thể giúp bạn lập lịch trình du lịch biển đảo, gợi ý đặc sản bánh căn, nem nướng, hải sản bờ kè, hoặc giải đáp mọi thông tin vé tham quan thực tế tại Nha Trang.
                                </p>
                                
                                <div class="ai-quick-prompts-panel mt-2 pt-2 border-top">
                                    <span class="text-muted small d-block mb-2" style="font-size: 11.5px;">
                                        💡 Chọn nhanh chủ đề bạn đang quan tâm:
                                    </span>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ai-quick-chip" onclick="clickQuickPrompt('Lập tour 1 ngày khám phá di tích & ẩm thực Nha Trang')">
                                            🧭 Tour 1 ngày di tích & ẩm thực
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ai-quick-chip" onclick="clickQuickPrompt('Gợi ý lịch trình 2 ngày 1 đêm biển đảo & hải sản')">
                                            🏝️ Tour 2N1Đ biển đảo & hải sản
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ai-quick-chip" onclick="clickQuickPrompt('Gợi ý quán hải sản tươi sống ngon rẻ gần biển')">
                                            🦞 Quán hải sản ngon gần biển
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ai-quick-chip" onclick="clickQuickPrompt('Lịch trình phù hợp cho gia đình có trẻ nhỏ')">
                                            👨‍👩‍👧 Lịch trình cho gia đình
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Render Lịch Sử Tin Nhắn Cũ Từ Database (Nếu Đã Đăng Nhập) -->
                        <?php if ($isLoggedIn && !empty($initialMessages)): ?>
                            <?php foreach ($initialMessages as $msg): ?>
                                <?php if ($msg['role'] === 'user'): ?>
                                    <div class="ai-chat-bubble-row ai-msg-user d-flex justify-content-end mb-3" data-msg-id="<?= $msg['id'] ?>">
                                        <div class="ai-bubble-box p-3 rounded-4 shadow-xs text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); max-width: 82%;">
                                            <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="ai-chat-bubble-row ai-msg-bot d-flex align-items-start gap-2 mb-3" data-msg-id="<?= $msg['id'] ?>">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #e0f2fe; color: #0284c7;">
                                            <i class="bi bi-robot fs-5"></i>
                                        </div>
                                        <div class="ai-bubble-box p-3 rounded-4 bg-white border shadow-xs" style="max-width: 88%;">
                                            <div class="ai-msg-content text-dark small" style="font-size: 13px; line-height: 1.6;">
                                                <?= \App\Helpers\FormatHelper::markdownToHtml($msg['message']) ?>
                                            </div>

                                            <?php if (!empty($msg['matched_locations'])): ?>
                                                <div class="mt-3 pt-2 border-top">
                                                    <span class="small fw-bold text-dark d-block mb-2" style="font-size: 11.5px;">
                                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa điểm được gợi ý:
                                                    </span>
                                                    <div class="row g-2">
                                                        <?php foreach (array_slice($msg['matched_locations'], 0, 4) as $loc): ?>
                                                            <div class="col-6 col-sm-4">
                                                                <div class="ai-loc-card p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between">
                                                                    <div>
                                                                        <img src="<?= htmlspecialchars(hinh_anh($loc['anh_dai_dien'] ?? '')) ?>" 
                                                                             alt="<?= htmlspecialchars($loc['ten_dia_diem']) ?>" 
                                                                             class="rounded-2 w-100 mb-1" 
                                                                             style="height: 60px; object-fit: cover;" loading="lazy">
                                                                        <strong class="text-truncate d-block text-dark small" style="font-size: 11.5px;" title="<?= htmlspecialchars($loc['ten_dia_diem']) ?>">
                                                                            <?= htmlspecialchars($loc['ten_dia_diem']) ?>
                                                                        </strong>
                                                                        <span class="text-muted d-block small" style="font-size: 10px;">
                                                                            <?= !empty($loc['co_ban_ve']) ? 'Vé: ' . number_format($loc['gia_thap_nhat']) . 'đ' : 'Ẩm thực / Tự do' ?>
                                                                        </span>
                                                                    </div>
                                                                    <div>
                                                                        <?php if (!empty($loc['co_ban_ve'])): ?>
                                                                            <?php $bUrl = !empty($loc['goi_ve_id']) ? url('booking/' . $loc['goi_ve_id']) : url('dia-diem/' . $loc['duong_dan'] . '#ve-tham-quan'); ?>
                                                                            <a href="<?= $bUrl ?>" class="btn btn-success btn-sm rounded-pill w-100 py-0.5 mt-1 fw-semibold text-truncate" style="font-size: 10px;">
                                                                                <i class="bi bi-cart-check me-1"></i>Đặt vé ngay
                                                                            </a>
                                                                        <?php endif; ?>
                                                                        <a href="<?= url('dia-diem/' . $loc['duong_dan']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill w-100 py-0 mt-1" style="font-size: 10.5px;">
                                                                            Chi tiết &rarr;
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Thanh tác vụ nhanh cho tin nhắn AI -->
                                            <div class="ai-msg-actions d-flex flex-wrap align-items-center gap-1.5 mt-2.5 pt-2 border-top">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5 text-nowrap" style="font-size: 11px;" onclick="saveCurrentItinerary(this)">
                                                    <i class="bi bi-bookmark-plus me-1"></i>Lưu lịch trình này
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2 text-nowrap" style="font-size: 11px;" onclick="copyAiMessage(this)">
                                                    <i class="bi bi-clipboard me-1"></i>Sao chép
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>

                    <!-- Màn Che Nhắc Đăng Nhập (Nếu Chưa Đăng Nhập) -->
                    <?php if (!$isLoggedIn): ?>
                        <div class="ai-login-barrier position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                            <div class="card border-0 shadow-lg rounded-4 p-4 text-center bg-white" style="max-width: 440px;">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 68px; height: 68px; background: #e0f2fe; color: #0284c7;">
                                    <i class="bi bi-shield-lock-fill display-6"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Đăng Nhập Để Trò Chuyện Cùng AI</h5>
                                <p class="text-muted small mb-4">
                                    Vui lòng đăng nhập để Trợ lý AI có thể ghi nhớ lịch sử đoạn chat của bạn, đồng bộ đa thiết bị và hỗ trợ lịch trình dựa trên các vé bạn đã đặt.
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="<?= url('login') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng Nhập Ngay
                                    </a>
                                    <a href="<?= url('register') ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                                        Đăng Ký
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 3. THANH NHẬP LIỆU FOOTER (CHUẨN FORM TRIP.COM) -->
                    <div class="ai-chat-input-toolbar p-3 bg-white border-top">
                        <!-- Hàng icon công cụ bên trên ô nhập -->
                        <div class="d-flex align-items-center gap-3 mb-2 text-secondary ps-1">
                            <button type="button" class="btn p-0 text-secondary border-0 ai-tool-icon" title="Biểu tượng cảm xúc" onclick="insertEmoji('😊')">
                                <i class="bi bi-emoji-smile fs-5"></i>
                            </button>
                            <button type="button" class="btn p-0 text-secondary border-0 ai-tool-icon" title="Gợi ý danh mục câu hỏi" onclick="toggleQuickChips()">
                                <i class="bi bi-folder2-open fs-5"></i>
                            </button>
                            <small class="text-muted ms-auto" style="font-size: 11px;">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Đồng bộ tự động với nút chat góc phải
                            </small>
                        </div>

                        <!-- Ô nhập liệu & Nút gửi -->
                        <form id="aiMainChatForm" onsubmit="handleMainChatSubmit(event)" class="d-flex align-items-center gap-2">
                            <input type="text" 
                                   id="aiMainInput" 
                                   class="form-control form-control-lg border-0 bg-light rounded-4 px-3 py-2 fs-6" 
                                   placeholder="Hãy miêu tả câu hỏi trong một câu..." 
                                   autocomplete="off"
                                   <?= !$isLoggedIn ? 'disabled' : '' ?>
                                   maxlength="500">

                            <button type="submit" 
                                    id="aiMainSendBtn" 
                                    class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-1 flex-shrink-0"
                                    <?= !$isLoggedIn ? 'disabled' : '' ?>>
                                <span>Gửi</span>
                                <i class="bi bi-caret-down-fill small" style="font-size: 10px;"></i>
                            </button>
                        </form>
                    </div>

                </div>

                <!-- CỘT PHẢI (SIDEBAR: ĐẶT CHỖ CỦA TÔI & CAM KẾT - 30%) -->
                <div class="col-lg-4 bg-light border-start p-4 d-flex flex-column justify-content-between ai-sidebar-col">
                    
                    <!-- Phần Trên: Đặt Chỗ Của Tôi (Trip.com Suitcase Style) -->
                    <div>
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-ticket-perforated text-primary me-2"></i>Đặt Chỗ Của Tôi</span>
                            <?php if ($isLoggedIn): ?>
                                <a href="<?= url('profile') ?>" class="small text-primary text-decoration-none" style="font-size: 11.5px;">Xem tất cả &rarr;</a>
                            <?php endif; ?>
                        </h6>

                        <?php if (empty($myBookings)): ?>
                            <!-- Trạng Thái Trống: Vali du lịch + "Không có đặt chỗ" (Y hệt mẫu Trip.com) -->
                            <div class="text-center py-4 my-2">
                                <div class="ai-suitcase-illustration mb-3 position-relative d-inline-block">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3" 
                                         style="width: 110px; height: 110px; background: rgba(224, 242, 254, 0.6); border: 2px dashed #93c5fd;">
                                        <i class="bi bi-briefcase text-primary" style="font-size: 50px;"></i>
                                    </div>
                                    <!-- Máy bay giấy trang trí -->
                                    <div class="position-absolute top-0 end-0 text-warning" style="font-size: 24px; transform: rotate(15deg);">
                                        <i class="bi bi-send-fill"></i>
                                    </div>
                                </div>
                                <h6 class="text-secondary fw-semibold mb-1" style="font-size: 14px;">Không có đặt chỗ</h6>
                                <p class="text-muted small mb-3" style="font-size: 12px;">Bạn chưa có vé hoặc dịch vụ nào đang chờ thực hiện.</p>
                                <a href="<?= url('locations?loai=du_lich') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                    <i class="bi bi-compass me-1"></i>Khám phá vé tham quan
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Danh Sách Đặt Chỗ Hiện Tại Của Khách Hàng -->
                            <div class="d-flex flex-column gap-2 mb-3" style="max-height: 280px; overflow-y: auto;">
                                <?php foreach (array_slice($myBookings, 0, 3) as $bk): ?>
                                    <div class="p-2 bg-white rounded-3 border shadow-xs d-flex gap-2 align-items-center">
                                        <img src="<?= htmlspecialchars(hinh_anh($bk['anh_dia_diem'] ?? '')) ?>" 
                                             class="rounded-2 border flex-shrink-0" 
                                             style="width: 45px; height: 45px; object-fit: cover;" loading="lazy">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <strong class="d-block text-dark small text-truncate" style="font-size: 12px;">
                                                <?= htmlspecialchars($bk['ten_dia_diem'] ?? 'Vé tham quan') ?>
                                            </strong>
                                            <span class="text-muted small d-block" style="font-size: 11px;">
                                                <i class="bi bi-calendar-event me-1"></i><?= !empty($bk['ngay_tham_quan']) ? date('d/m/Y', strtotime($bk['ngay_tham_quan'])) : 'Vé mở' ?>
                                            </span>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small" style="font-size: 10px;">
                                            Đã mua
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Danh Sách Vé Tham Quan Gợi Ý Trong Tour (Tự động tổng hợp khi AI gợi ý) -->
                    <div class="mt-3 pt-3 border-top" id="tourTicketsBox">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-cart-check-fill text-success me-2"></i>Vé Tham Quan Trong Tour</span>
                            <span class="badge bg-success-subtle text-success rounded-pill" id="tourTicketCountBadge" style="font-size: 10px;">0 vé</span>
                        </h6>
                        <div id="tourTicketsList" class="d-flex flex-column gap-2 mb-2" style="max-height: 200px; overflow-y: auto;">
                            <div class="text-muted small p-2 text-center bg-white rounded-3 border" id="tourTicketPlaceholder" style="font-size: 11px;">
                                <i class="bi bi-info-circle me-1 text-primary"></i>Khi AI tư vấn các điểm bán vé, các gói vé sẽ tự động xuất hiện tại đây để bạn đặt vé tức thì.
                            </div>
                        </div>
                    </div>

                    <!-- Danh Sách Lịch Trình Đã Lưu -->
                    <?php if ($isLoggedIn): ?>
                        <div class="mt-3 pt-3 border-top" id="savedToursBox">
                            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-bookmark-star-fill text-warning me-2"></i>Lịch Trình Đã Lưu</span>
                                <span class="badge bg-warning-subtle text-dark rounded-pill" id="savedTourCountBadge" style="font-size: 10px;"><?= count($savedTours ?? []) ?></span>
                            </h6>
                            <div class="d-flex flex-column gap-2" style="max-height: 180px; overflow-y: auto;" id="savedToursList">
                                <?php if (empty($savedTours)): ?>
                                    <small class="text-muted text-center d-block p-2 bg-white rounded-3 border" id="savedTourEmptyMsg" style="font-size: 11px;">Chưa có lịch trình nào được lưu. Bấm "Lưu lịch trình này" dưới câu trả lời của AI để lưu.</small>
                                <?php else: ?>
                                    <?php foreach ($savedTours as $st): ?>
                                        <div class="p-2 bg-white rounded-3 border shadow-xs d-flex align-items-center justify-content-between gap-2 saved-tour-row" id="savedTourItem-<?= (int)$st['id'] ?>">
                                            <div class="overflow-hidden">
                                                <strong class="d-block text-dark small text-truncate" style="font-size: 11.5px; max-width: 160px;" title="<?= htmlspecialchars($st['tieu_de']) ?>">
                                                    <?= htmlspecialchars($st['tieu_de']) ?>
                                                </strong>
                                                <span class="text-muted small" style="font-size: 10px;">
                                                    <i class="bi bi-calendar-event me-1"></i><?= (int)$st['so_ngay'] ?> ngày &bull; <?= date('d/m/Y', strtotime($st['created_at'])) ?>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1.5 rounded-pill" onclick="viewSavedTour(<?= (int)$st['id'] ?>)" title="Xem lại">
                                                    <i class="bi bi-eye" style="font-size: 11px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1.5 rounded-pill" onclick="deleteSavedTour(<?= (int)$st['id'] ?>)" title="Xóa">
                                                    <i class="bi bi-trash3" style="font-size: 11px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Phần Dưới: Tự Tin Đặt Chỗ (Cam Kết Dịch Vụ) -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14.5px;">Tự Tin Đặt Chỗ</h6>
                        
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning mt-1 fs-6"></i>
                                <div>
                                    <strong class="text-dark d-block">Vô Tư Du Lịch</strong>
                                    <span class="text-muted" style="font-size: 11.5px;">Hệ thống vé trực tiếp liên kết các danh thắng Nha Trang.</span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1 fs-6"></i>
                                <div>
                                    <strong class="text-dark d-block">Soát Vé Siêu Tốc (30 Giây)</strong>
                                    <span class="text-muted" style="font-size: 11.5px;">Xuất mã QR E-Voucher quét tại cổng không cần xếp hàng.</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info mt-1 fs-6"></i>
                                <div>
                                    <strong class="text-dark d-block">Hỗ Trợ &amp; Hủy Vé Linh Hoạt</strong>
                                    <span class="text-muted" style="font-size: 11.5px;">Hủy vé tự động nếu chưa kích hoạt trước ngày đi.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal Xem Chi Tiết Lịch Trình Đã Lưu -->
<div class="modal fade" id="savedTourModal" tabindex="-1" aria-labelledby="savedTourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="savedTourModalLabel">
                    <i class="bi bi-bookmark-star-fill text-warning me-2"></i>Chi Tiết Lịch Trình Đã Lưu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4" id="savedTourModalBody" style="font-size: 13.5px; line-height: 1.65;">
                <!-- Nội dung Markdown render tại đây -->
            </div>
            <div class="modal-footer border-top bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copySavedTourModal()">
                    <i class="bi bi-clipboard me-1"></i>Sao chép toàn bộ
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" data-bs-dismiss="modal">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================================
   CSS GIAO DIỆN TRIP.COM CHO TRANG AI TOUR
   ======================================================== */
.ai-page-backdrop {
    background: linear-gradient(180deg, #0b1f3a 0%, #172554 40%, #1e3a8a 100%),
                url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
    background-attachment: fixed;
    min-height: calc(100vh - 140px);
}

.ai-trip-modal {
    max-width: 1080px;
    height: 720px;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.ai-trip-header {
    height: 68px;
}

.ai-trip-body-row {
    height: calc(720px - 68px);
}

.ai-chat-main-col {
    height: 100%;
}

.ai-chat-scrollable {
    overflow-y: auto;
    background-color: #fafbfc;
    scroll-behavior: smooth;
}

.ai-chat-input-toolbar {
    background: #ffffff;
}

.ai-tool-icon {
    transition: transform 0.15s, color 0.15s;
}

.ai-tool-icon:hover {
    color: #0284c7 !important;
    transform: scale(1.15);
}

.btn-load-prev-msg {
    font-size: 13px;
    color: #0284c7;
    transition: all 0.2s;
}

.btn-load-prev-msg:hover {
    text-decoration: underline !important;
}

.ai-quick-chip {
    font-size: 11.5px;
    padding: 3px 10px;
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #475569;
    transition: all 0.2s;
}

.ai-quick-chip:hover {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
    transform: translateY(-1px);
}

.ai-loc-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.ai-loc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

/* Lớp chắn nhắc đăng nhập */
.ai-login-barrier {
    background: rgba(255, 255, 255, 0.82);
    backdrop-filter: blur(8px);
    z-index: 10;
}

@media (max-width: 991px) {
    .ai-trip-modal {
        height: auto;
        min-height: 600px;
    }
    .ai-trip-body-row {
        height: auto;
    }
    .ai-chat-scrollable {
        min-height: 400px;
        max-height: 500px;
    }
    .ai-sidebar-col {
        border-top: 1px solid #e2e8f0;
        border-left: none !important;
    }
}
</style>

<!-- ========================================================
     JAVASCRIPT XỬ LÝ ĐOẠN CHAT & ĐỒNG BỘ NÚT TRÒN
     ======================================================== -->
<script>
let oldestMessageId = <?= !empty($initialMessages) ? (int)$initialMessages[0]['id'] : 0 ?>;
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    scrollToBottomChat();
});

function scrollToBottomChat() {
    const chatArea = document.getElementById('aiMainMessagesArea');
    if (chatArea) {
        chatArea.scrollTop = chatArea.scrollHeight;
    }
}

function clickQuickPrompt(text) {
    const input = document.getElementById('aiMainInput');
    if (input) {
        input.value = text;
        handleMainChatSubmit(new Event('submit'));
    }
}

function insertEmoji(emoji) {
    const input = document.getElementById('aiMainInput');
    if (input) {
        input.value += emoji;
        input.focus();
    }
}

function toggleQuickChips() {
    const chipsPanel = document.querySelector('.ai-quick-prompts-panel');
    if (chipsPanel) {
        chipsPanel.style.display = (chipsPanel.style.display === 'none') ? 'block' : 'none';
    }
}

// 1. Tải Thêm Tin Nhắn Cũ Hơn (Khi bấm "Xem Tin Nhắn Trước")
function loadPreviousMessages() {
    if (!isLoggedIn || oldestMessageId <= 0) return;

    const btn = document.getElementById('btnLoadPrevMsg');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tải tin nhắn trước...';
    }

    const chatArea = document.getElementById('aiMainMessagesArea');
    const prevScrollHeight = chatArea ? chatArea.scrollHeight : 0;

    fetch(`<?= url('api/ai/messages') ?>?before_id=${oldestMessageId}&limit=10`)
    .then(r => r.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Xem Tin Nhắn Trước';
        }

        if (data.success && data.messages && data.messages.length > 0) {
            // Cập nhật mốc tin nhắn cũ nhất
            oldestMessageId = data.messages[0].id;

            // Render các tin nhắn cũ chèn vào phía trên
            let oldHtml = '';
            data.messages.forEach(msg => {
                oldHtml += renderMessageRowHtml(msg);
            });

            const loadMoreWrapper = document.getElementById('loadMoreMessagesWrapper');
            if (loadMoreWrapper) {
                loadMoreWrapper.insertAdjacentHTML('afterend', oldHtml);
            }

            // Ẩn nút nếu không còn tin nhắn cũ hơn
            if (!data.has_more && loadMoreWrapper) {
                loadMoreWrapper.style.display = 'none';
            }

            // Giữ nguyên vị trí cuộn chuột sau khi chèn thêm tin nhắn phía trên
            if (chatArea) {
                const newScrollHeight = chatArea.scrollHeight;
                chatArea.scrollTop = newScrollHeight - prevScrollHeight;
            }
        } else {
            const loadMoreWrapper = document.getElementById('loadMoreMessagesWrapper');
            if (loadMoreWrapper) loadMoreWrapper.style.display = 'none';
        }
    })
    .catch(err => {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Xem Tin Nhắn Trước';
        }
    });
}

// 2. Gửi Tin Nhắn Mới
function handleMainChatSubmit(e) {
    if (e) e.preventDefault();
    if (!isLoggedIn) {
        alert('Vui lòng đăng nhập để trò chuyện với Trợ lý AI.');
        window.location.href = "<?= url('login') ?>";
        return;
    }

    const input = document.getElementById('aiMainInput');
    const sendBtn = document.getElementById('aiMainSendBtn');
    const chatArea = document.getElementById('aiMainMessagesArea');
    if (!input || !chatArea) return;

    const userText = input.value.trim();
    if (!userText) return;

    // Append tin nhắn người dùng
    const userRowHtml = `
        <div class="ai-chat-bubble-row ai-msg-user d-flex justify-content-end mb-3">
            <div class="ai-bubble-box p-3 rounded-4 shadow-xs text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); max-width: 82%;">
                <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">${escapeHtml(userText)}</p>
            </div>
        </div>
    `;
    chatArea.insertAdjacentHTML('beforeend', userRowHtml);
    input.value = '';
    scrollToBottomChat();

    // Append typing indicator
    const typingId = 'typing-' + Date.now();
    const typingHtml = `
        <div class="ai-chat-bubble-row ai-msg-bot d-flex align-items-start gap-2 mb-3" id="${typingId}">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-robot fs-5"></i>
            </div>
            <div class="ai-bubble-box p-3 rounded-4 bg-white border shadow-xs">
                <div class="d-flex align-items-center gap-1 text-muted small py-1" aria-busy="true">
                    <span>Trợ lý đang suy nghĩ</span>
                    <span class="spinner-grow spinner-grow-sm text-primary" style="width: 8px; height: 8px;"></span>
                </div>
            </div>
        </div>
    `;
    chatArea.insertAdjacentHTML('beforeend', typingHtml);
    scrollToBottomChat();

    if (sendBtn) sendBtn.disabled = true;

    // Gửi AJAX POST đến API
    fetch("<?= url('api/ai/chat') ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: userText
        })
    })
    .then(r => r.json())
    .then(data => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        if (sendBtn) sendBtn.disabled = false;

        if (data.require_login) {
            alert(data.message || 'Phiên làm việc hết hạn. Vui lòng đăng nhập lại!');
            window.location.href = "<?= url('login') ?>";
            return;
        }

        if (data.success) {
            const botRowHtml = renderMessageRowHtml({
                id: data.message_id || Date.now(),
                role: 'model',
                message: data.reply,
                matched_locations: data.matched_locations || []
            }, data);

            chatArea.insertAdjacentHTML('beforeend', botRowHtml);
            scrollToBottomChat();
            if (data.matched_locations && data.matched_locations.length > 0) {
                updateTourTicketSummary(data.matched_locations);
            }
        } else {
            alert(data.message || 'Lỗi khi gửi câu hỏi.');
        }
    })
    .catch(err => {
        console.error(err);
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        if (sendBtn) sendBtn.disabled = false;
        alert('Lỗi kết nối máy chủ.');
    });
}

// 3. Xóa Lịch Sử Chat
function handleClearAiHistory() {
    if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ lịch sử đoạn chat của mình?')) return;

    fetch("<?= url('api/ai/clear') ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Lỗi khi xóa.');
        }
    });
}

// Helper render từng hàng tin nhắn
function renderMessageRowHtml(msg, extraData) {
    if (msg.role === 'user') {
        return `
            <div class="ai-chat-bubble-row ai-msg-user d-flex justify-content-end mb-3" data-msg-id="${msg.id}">
                <div class="ai-bubble-box p-3 rounded-4 shadow-xs text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); max-width: 82%;">
                    <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">${escapeHtml(msg.message)}</p>
                </div>
            </div>
        `;
    }

    // Role model
    let locsHtml = '';
    if (msg.matched_locations && msg.matched_locations.length > 0) {
        locsHtml = `<div class="mt-3 pt-2 border-top"><span class="small fw-bold text-dark d-block mb-2" style="font-size: 11.5px;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa điểm được gợi ý:</span><div class="row g-2">`;
        msg.matched_locations.slice(0, 4).forEach(loc => {
            const imgUrl = (loc.anh_dai_dien && loc.anh_dai_dien.startsWith('http')) ? loc.anh_dai_dien : "<?= url('') ?>/" + (loc.anh_dai_dien || 'assets/images/placeholder.jpg');
            const detailUrl = "<?= url('dia-diem') ?>/" + loc.duong_dan;
            const priceText = loc.co_ban_ve ? ('Vé: ' + Number(loc.gia_thap_nhat).toLocaleString('vi-VN') + 'đ') : 'Ẩm thực / Tự do';
            const bookBtn = loc.co_ban_ve ? `
                <a href="${loc.goi_ve_id ? ('<?= url('booking') ?>/' + loc.goi_ve_id) : (detailUrl + '#ve-tham-quan')}" class="btn btn-success btn-sm rounded-pill w-100 py-0.5 mt-1 fw-semibold text-truncate" style="font-size: 10px;">
                    <i class="bi bi-cart-check me-1"></i>Đặt vé ngay
                </a>
            ` : '';
            locsHtml += `
                <div class="col-6 col-sm-4">
                    <div class="ai-loc-card p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <img src="${imgUrl}" class="rounded-2 w-100 mb-1" style="height: 60px; object-fit: cover;" loading="lazy">
                            <strong class="text-truncate d-block text-dark small" style="font-size: 11.5px;">${escapeHtml(loc.ten_dia_diem)}</strong>
                            <span class="text-muted d-block small" style="font-size: 10px;">${priceText}</span>
                        </div>
                        <div>
                            ${bookBtn}
                            <a href="${detailUrl}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill w-100 py-0 mt-1" style="font-size: 10.5px;">
                                Chi tiết &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        locsHtml += `</div></div>`;
    }

    const actionsHtml = `
        <div class="ai-msg-actions d-flex flex-wrap align-items-center gap-1.5 mt-2.5 pt-2 border-top">
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5 text-nowrap" style="font-size: 11px;" onclick="saveCurrentItinerary(this)">
                <i class="bi bi-bookmark-plus me-1"></i>Lưu lịch trình này
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2 text-nowrap" style="font-size: 11px;" onclick="copyAiMessage(this)">
                <i class="bi bi-clipboard me-1"></i>Sao chép
            </button>
        </div>
    `;

    return `
        <div class="ai-chat-bubble-row ai-msg-bot d-flex align-items-start gap-2 mb-3" data-msg-id="${msg.id}">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-robot fs-5"></i>
            </div>
            <div class="ai-bubble-box p-3 rounded-4 bg-white border shadow-xs" style="max-width: 88%;">
                <div class="ai-msg-content text-dark small" style="font-size: 13px; line-height: 1.6;">
                    ${renderMarkdownSimple(msg.message)}
                </div>
                ${locsHtml}
                ${actionsHtml}
            </div>
        </div>
    `;
}

// Lưu trữ dữ liệu lịch trình đã lưu để xem lại trong Modal
const savedToursData = <?= json_encode($savedTours ?? [], JSON_UNESCAPED_UNICODE) ?>;
const tourTicketMap = new Map();

function updateTourTicketSummary(locations) {
    if (!locations || !Array.isArray(locations) || locations.length === 0) return;
    const box = document.getElementById('tourTicketsList');
    const badge = document.getElementById('tourTicketCountBadge');
    const placeholder = document.getElementById('tourTicketPlaceholder');
    if (!box) return;

    let hasNew = false;
    locations.forEach(loc => {
        if (loc.co_ban_ve && !tourTicketMap.has(loc.id)) {
            tourTicketMap.set(loc.id, loc);
            hasNew = true;
        }
    });

    if (tourTicketMap.size > 0 && placeholder) {
        placeholder.remove();
    }

    if (badge) {
        badge.textContent = tourTicketMap.size + ' vé';
    }

    if (hasNew) {
        box.innerHTML = '';
        tourTicketMap.forEach(loc => {
            const imgUrl = (loc.anh_dai_dien && loc.anh_dai_dien.startsWith('http')) ? loc.anh_dai_dien : "<?= url('') ?>/" + (loc.anh_dai_dien || 'assets/images/placeholder.jpg');
            const bookUrl = loc.goi_ve_id ? ("<?= url('booking') ?>/" + loc.goi_ve_id) : ("<?= url('dia-diem') ?>/" + loc.duong_dan + "#ve-tham-quan");
            const price = loc.gia_thap_nhat ? Number(loc.gia_thap_nhat).toLocaleString('vi-VN') + 'đ' : 'Liên hệ';

            const item = document.createElement('div');
            item.className = 'p-2 bg-white rounded-3 border shadow-xs d-flex align-items-center justify-content-between gap-2';
            item.innerHTML = `
                <img src="${imgUrl}" class="rounded-2 border flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;" loading="lazy">
                <div class="flex-grow-1 overflow-hidden">
                    <strong class="d-block text-dark small text-truncate" style="font-size: 11.5px;">${escapeHtml(loc.ten_dia_diem)}</strong>
                    <span class="text-success small fw-semibold" style="font-size: 10.5px;">Từ ${price}</span>
                </div>
                <a href="${bookUrl}" class="btn btn-sm btn-success rounded-pill py-1 px-2.5 fw-semibold flex-shrink-0 text-nowrap" style="font-size: 10.5px;">
                    <i class="bi bi-cart-check me-1"></i>Đặt vé
                </a>
            `;
            box.appendChild(item);
        });
    }
}

function saveCurrentItinerary(btn) {
    const bubbleBox = btn.closest('.ai-bubble-box');
    if (!bubbleBox) return;
    const contentEl = bubbleBox.querySelector('.ai-msg-content');
    const text = contentEl ? contentEl.innerText : '';
    if (!text.trim()) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Đang lưu...';

    const lines = text.trim().split('\n');
    let title = 'Lịch trình Tour Nha Trang Staycation';
    for (const l of lines) {
        const cleaned = l.replace(/[#*>\-]/g, '').trim();
        if (cleaned.length > 5) {
            title = cleaned.substring(0, 80);
            break;
        }
    }

    fetch('<?= url("api/ai/save-tour") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            title: title,
            markdown: text,
            days: (text.includes('Ngày 3') ? 3 : (text.includes('Ngày 2') ? 2 : 1))
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.require_login) {
            alert('Vui lòng đăng nhập để lưu lịch trình vào tài khoản!');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-bookmark-plus me-1"></i>Lưu lịch trình này';
            return;
        }
        if (data.success) {
            btn.className = 'btn btn-sm btn-success rounded-pill py-0.5 px-2.5 text-nowrap';
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã lưu vào hồ sơ';
            appendSavedTourItem(data.id, title, text);
        } else {
            alert(data.message || 'Lỗi khi lưu lịch trình.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-bookmark-plus me-1"></i>Lưu lịch trình này';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-bookmark-plus me-1"></i>Lưu lịch trình này';
    });
}

function appendSavedTourItem(id, title, markdown) {
    savedToursData.unshift({
        id: id,
        tieu_de: title,
        noi_dung_markdown: markdown,
        so_ngay: 1,
        created_at: new Date().toISOString()
    });

    const list = document.getElementById('savedToursList');
    const badge = document.getElementById('savedTourCountBadge');
    const emptyMsg = document.getElementById('savedTourEmptyMsg');
    if (emptyMsg) emptyMsg.remove();
    if (badge) badge.textContent = savedToursData.length;

    if (list) {
        const row = document.createElement('div');
        row.className = 'p-2 bg-white rounded-3 border shadow-xs d-flex align-items-center justify-content-between gap-2 saved-tour-row';
        row.id = `savedTourItem-${id}`;
        row.innerHTML = `
            <div class="overflow-hidden">
                <strong class="d-block text-dark small text-truncate" style="font-size: 11.5px; max-width: 160px;" title="${escapeHtml(title)}">
                    ${escapeHtml(title)}
                </strong>
                <span class="text-muted small" style="font-size: 10px;">
                    <i class="bi bi-calendar-event me-1"></i>Vừa lưu xong
                </span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1.5 rounded-pill" onclick="viewSavedTour(${id})" title="Xem lại">
                    <i class="bi bi-eye" style="font-size: 11px;"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1.5 rounded-pill" onclick="deleteSavedTour(${id})" title="Xóa">
                    <i class="bi bi-trash3" style="font-size: 11px;"></i>
                </button>
            </div>
        `;
        list.prepend(row);
    }
}

function viewSavedTour(id) {
    const tour = savedToursData.find(t => Number(t.id) === Number(id));
    if (!tour) return;

    const modalTitle = document.getElementById('savedTourModalLabel');
    const modalBody = document.getElementById('savedTourModalBody');
    if (modalTitle) modalTitle.innerHTML = `<i class="bi bi-bookmark-star-fill text-warning me-2"></i>${escapeHtml(tour.tieu_de)}`;
    if (modalBody) modalBody.innerHTML = renderMarkdownSimple(tour.noi_dung_markdown);

    const modal = new bootstrap.Modal(document.getElementById('savedTourModal'));
    modal.show();
}

function deleteSavedTour(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa lịch trình này khỏi hồ sơ đã lưu?')) return;

    fetch('<?= url("api/ai/delete-tour") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const item = document.getElementById(`savedTourItem-${id}`);
            if (item) item.remove();
            const idx = savedToursData.findIndex(t => Number(t.id) === Number(id));
            if (idx !== -1) savedToursData.splice(idx, 1);
            const badge = document.getElementById('savedTourCountBadge');
            if (badge) badge.textContent = savedToursData.length;
        } else {
            alert(data.message || 'Không thể xóa lịch trình.');
        }
    });
}

function copySavedTourModal() {
    const modalBody = document.getElementById('savedTourModalBody');
    if (!modalBody) return;
    navigator.clipboard.writeText(modalBody.innerText).then(() => {
        alert('Đã sao chép toàn bộ nội dung lịch trình vào bộ nhớ tạm!');
    });
}

function copyAiMessage(btn) {
    const bubbleBox = btn.closest('.ai-bubble-box');
    if (!bubbleBox) return;
    const contentEl = bubbleBox.querySelector('.ai-msg-content');
    if (!contentEl) return;
    navigator.clipboard.writeText(contentEl.innerText).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 text-success me-1"></i>Đã chép!';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
    });
}

// Khi trang load xong, quét toàn bộ địa điểm gợi ý có sẵn để cập nhật bảng vé
document.addEventListener('DOMContentLoaded', function() {
    const initialMatched = [];
    <?php if (!empty($initialMessages)): ?>
        <?php foreach ($initialMessages as $m): ?>
            <?php if (!empty($m['matched_locations']) && is_array($m['matched_locations'])): ?>
                <?php foreach ($m['matched_locations'] as $lc): ?>
                    initialMatched.push(<?= json_encode($lc, JSON_UNESCAPED_UNICODE) ?>);
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    if (initialMatched.length > 0) {
        updateTourTicketSummary(initialMatched);
    }
});

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function renderMarkdownSimple(md) {
    if (!md) return '';
    let html = escapeHtml(md);
    html = html.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-dark mt-3 mb-1">$1</h6>');
    html = html.replace(/^#### (.*$)/gim, '<strong class="d-block text-primary mt-2 mb-1">$1</strong>');
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    html = html.replace(/^---$/gim, '<hr class="my-2 border-secondary opacity-25">');
    html = html.replace(/^> (.*$)/gim, '<div class="p-2 my-1 rounded bg-light border-start border-3 border-primary text-secondary">$1</div>');
    html = html.replace(/^\- (.*$)/gim, '<div class="d-flex align-items-start gap-1 my-1"><span>•</span><span>$1</span></div>');
    html = html.replace(/\n\n/g, '<br>');
    return html;
}
</script>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

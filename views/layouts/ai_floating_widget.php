<!-- ========================================================
     TRỢ LÝ AI TOUR NHA TRANG (FLOATING WIDGET)
     ======================================================== -->
<?php $aiWidgetLoggedIn = !empty($_SESSION['user_id']); ?>

<div id="aiTourWidget" class="ai-tour-widget-container">
    <!-- Nút bấm nổi bật mở widget (Floating Action Button) -->
    <button type="button" 
            id="aiWidgetToggleBtn" 
            class="ai-widget-toggle-btn shadow-lg" 
            onclick="toggleAiWidget()" 
            aria-label="Mở Trợ Lý AI Tour Nha Trang"
            title="Trò chuyện với Trợ Lý AI Tour">
        <span class="ai-pulse-ring"></span>
        <div class="ai-btn-icon-wrapper">
            <i class="bi bi-robot fs-4 text-white" id="aiBtnIcon"></i>
        </div>
        <span class="ai-widget-pill d-none d-md-inline-block">
            <i class="bi bi-stars text-warning me-1"></i>Trợ lý AI Tour
        </span>
    </button>

    <!-- Khung Chatbot Nổi (Floating Chat Drawer) -->
    <div id="aiChatDrawer" class="ai-chat-drawer shadow-2xl" style="display: none;" role="dialog" aria-labelledby="aiChatTitle" aria-modal="true">
        <!-- Header -->
        <div class="ai-drawer-header d-flex align-items-center justify-content-between p-3 text-white">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="rounded-circle bg-warning bg-opacity-25 border border-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-robot text-warning fs-5"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" title="AI đang trực tuyến"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-1" id="aiChatTitle" style="font-size: 14px;">
                        Trợ Lý AI Tour Nha Trang
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 9px; padding: 2px 6px;">AI</span>
                    </h6>
                    <small class="text-white-50" style="font-size: 11px;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Tư vấn lịch trình &amp; ẩm thực 24/7
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">
                <a href="<?= url('ai-tour') ?>" class="btn btn-sm btn-icon-round text-white" title="Mở trang lập tour toàn màn hình" style="width: 32px; height: 32px;">
                    <i class="bi bi-arrows-angle-expand" style="font-size: 13px;"></i>
                </a>
                <?php if ($aiWidgetLoggedIn): ?>
                    <button type="button" class="btn btn-sm btn-icon-round text-white" onclick="clearAiChat()" title="Xóa lịch sử đoạn chat" style="width: 32px; height: 32px;">
                        <i class="bi bi-trash3" style="font-size: 13px;"></i>
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-icon-round text-white" onclick="toggleAiWidget()" title="Thu nhỏ" style="width: 32px; height: 32px;">
                    <i class="bi bi-x-lg" style="font-size: 14px;"></i>
                </button>
            </div>
        </div>

        <!-- Body / Danh sách tin nhắn -->
        <div class="ai-drawer-body p-3 position-relative" id="aiChatMessages">
            <?php if (!$aiWidgetLoggedIn): ?>
                <!-- Thông báo yêu cầu đăng nhập khi chưa login -->
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-3">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 58px; height: 58px; background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px;">Vui Lòng Đăng Nhập</h6>
                    <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.5;">
                        Đăng nhập tài khoản để trò chuyện cùng Trợ lý AI và tự động lưu lại lịch sử hội thoại của bạn.
                    </p>
                    <a href="<?= url('login') ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng Nhập Ngay
                    </a>
                </div>
            <?php else: ?>
                <!-- Tin nhắn chào mừng mặc định -->
                <div class="ai-msg-row ai-msg-bot mb-3">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div class="ai-bubble ai-bubble-bot p-3 rounded-4 shadow-xs">
                            <p class="mb-2 fw-semibold text-dark" style="font-size: 13px;">
                                👋 Chào <?= htmlspecialchars($_SESSION['user_name'] ?? 'bạn') ?>! Tôi là <strong>Trợ Lý AI Tour Nha Trang</strong>.
                            </p>
                            <p class="small text-secondary mb-2" style="font-size: 12px; line-height: 1.5;">
                                Hãy hỏi tôi bất kỳ điều gì về lịch trình du lịch, quán ăn ngon, hoặc vé tham quan Nha Trang nhé!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Chips câu hỏi gợi ý nhanh -->
                <div class="ai-prompt-chips d-flex flex-wrap gap-1 mb-3" id="aiPromptChips">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ai-chip" onclick="sendAiPrompt('Lập tour 1 ngày khám phá di tích & ẩm thực Nha Trang')">
                        🧭 Tour 1 ngày di tích & ẩm thực
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ai-chip" onclick="sendAiPrompt('Gợi ý lịch trình 2 ngày 1 đêm biển đảo & hải sản')">
                        🏝️ Tour 2N1Đ biển đảo & hải sản
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ai-chip" onclick="sendAiPrompt('Quán hải sản nào tươi ngon giá hợp lý gần bờ kè?')">
                        🦞 Quán hải sản ngon gần biển
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer / Input Form -->
        <div class="ai-drawer-footer p-2 bg-light border-top">
            <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" class="d-flex align-items-center gap-2">
                <input type="text" 
                       id="aiChatInput" 
                       class="form-control form-control-sm rounded-pill px-3 py-2 border-1" 
                       placeholder="<?= $aiWidgetLoggedIn ? 'Hỏi lịch trình, quán ăn, điểm đến...' : 'Vui lòng đăng nhập để gửi câu hỏi' ?>" 
                       autocomplete="off"
                       <?= !$aiWidgetLoggedIn ? 'disabled' : '' ?>
                       maxlength="500">
                <button type="submit" 
                        id="aiChatSendBtn" 
                        class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                        style="width: 38px; height: 38px;" 
                        title="Gửi câu hỏi"
                        aria-label="Gửi câu hỏi"
                        <?= !$aiWidgetLoggedIn ? 'disabled' : '' ?>>
                    <i class="bi bi-send-fill" style="font-size: 13px;"></i>
                </button>
            </form>
            <div class="d-flex justify-content-between align-items-center px-2 pt-1 text-muted" style="font-size: 10px;">
                <span>⚡ Hybrid: Gemini AI + Smart Engine</span>
                <a href="<?= url('ai-tour') ?>" class="text-primary text-decoration-none">
                    Mở Giao Diện Trip &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================================
   CSS FLOATING AI TOUR ASSISTANT WIDGET
   ======================================================== */
.ai-tour-widget-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1040;
    font-family: inherit;
}

.ai-widget-toggle-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #0284c7 0%, #4f46e5 100%);
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 50rem;
    padding: 6px 14px 6px 8px;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.ai-widget-toggle-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 28px rgba(79, 70, 229, 0.45) !important;
}

.ai-btn-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-widget-pill {
    font-weight: 600;
    font-size: 13.5px;
    letter-spacing: -0.01em;
}

.ai-pulse-ring {
    position: absolute;
    top: 50%;
    left: 24px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    border: 2px solid rgba(56, 189, 248, 0.8);
    animation: ai-pulse 2.2s infinite cubic-bezier(0.25, 0, 0, 1);
    pointer-events: none;
}

@keyframes ai-pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.9; }
    70% { transform: translate(-50%, -50%) scale(1.7); opacity: 0; }
    100% { transform: translate(-50%, -50%) scale(1.7); opacity: 0; }
}

.ai-chat-drawer {
    position: fixed;
    bottom: 88px;
    right: 24px;
    width: 410px;
    max-width: calc(100vw - 32px);
    height: 580px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 20px 45px rgba(11, 31, 58, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.05);
    z-index: 1045;
    animation: aiDrawerSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes aiDrawerSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.ai-drawer-header {
    background: linear-gradient(135deg, #0b1f3a 0%, #1e3a8a 100%);
}

.btn-icon-round {
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-icon-round:hover {
    background: rgba(255, 255, 255, 0.25);
}

.ai-drawer-body {
    flex: 1;
    overflow-y: auto;
    background-color: #f8fafc;
    scroll-behavior: smooth;
}

.ai-bubble-bot {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    max-width: 88%;
}

.ai-bubble-user {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    max-width: 85%;
    margin-left: auto;
}

.ai-chip {
    font-size: 11.5px;
    background: #ffffff;
    border-color: #cbd5e1;
    color: #334155;
    padding: 3px 10px;
    transition: all 0.2s;
}

.ai-chip:hover {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
    transform: translateY(-1px);
}

.ai-mini-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.ai-mini-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.ai-typing-indicator span {
    display: inline-block;
    width: 6px;
    height: 6px;
    background-color: #0284c7;
    border-radius: 50%;
    animation: aiTyping 1.4s infinite ease-in-out both;
    margin: 0 2px;
}

.ai-typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
.ai-typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

@keyframes aiTyping {
    0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

@media (max-width: 576px) {
    .ai-chat-drawer {
        right: 12px;
        bottom: 80px;
        width: calc(100vw - 24px);
        height: 80vh;
    }
}
</style>

<script>
// ========================================================
// JAVASCRIPT ĐIỀU KHIỂN TRỢ LÝ AI TOUR WIDGET
// ========================================================
let isAiWidgetOpen = false;
let widgetMessagesLoaded = false;
const isWidgetLoggedIn = <?= $aiWidgetLoggedIn ? 'true' : 'false' ?>;

function toggleAiWidget() {
    const drawer = document.getElementById('aiChatDrawer');
    const icon = document.getElementById('aiBtnIcon');
    if (!drawer) return;

    isAiWidgetOpen = !isAiWidgetOpen;
    if (isAiWidgetOpen) {
        drawer.style.display = 'flex';
        if (icon) icon.className = 'bi bi-dash-lg fs-4 text-white';
        setTimeout(() => {
            const input = document.getElementById('aiChatInput');
            if (input && isWidgetLoggedIn) input.focus();
        }, 100);

        // Tải tin nhắn gần nhất từ DB lần đầu mở widget
        if (isWidgetLoggedIn && !widgetMessagesLoaded) {
            loadWidgetLatestMessages();
        }
    } else {
        drawer.style.display = 'none';
        if (icon) icon.className = 'bi bi-robot fs-4 text-white';
    }
}

function loadWidgetLatestMessages() {
    fetch("<?= url('api/ai/messages') ?>?limit=8")
    .then(r => r.json())
    .then(data => {
        widgetMessagesLoaded = true;
        if (data.success && data.messages && data.messages.length > 0) {
            const msgArea = document.getElementById('aiChatMessages');
            if (!msgArea) return;

            // Xóa prompt chips ban đầu nếu đã có lịch sử
            const chips = document.getElementById('aiPromptChips');
            if (chips) chips.style.display = 'none';

            let historyHtml = '<div class="text-center text-muted small my-2" style="font-size: 10px;">Lịch sử hội thoại gần đây</div>';
            data.messages.forEach(msg => {
                if (msg.role === 'user') {
                    historyHtml += `
                        <div class="ai-msg-row ai-msg-user mb-3 text-end">
                            <div class="ai-bubble ai-bubble-user p-2.5 rounded-4 shadow-xs text-start d-inline-block">
                                <p class="mb-0 small" style="font-size: 12.5px;">${escapeHtml(msg.message)}</p>
                            </div>
                        </div>
                    `;
                } else {
                    historyHtml += `
                        <div class="ai-msg-row ai-msg-bot mb-3">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                                    <i class="bi bi-robot" style="font-size: 13px;"></i>
                                </div>
                                <div class="ai-bubble ai-bubble-bot p-2.5 rounded-4 shadow-xs">
                                    <div class="small" style="font-size: 12px; line-height: 1.5;">
                                        ${renderMarkdownSimple(msg.message)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            msgArea.insertAdjacentHTML('beforeend', historyHtml);
            msgArea.scrollTop = msgArea.scrollHeight;
        }
    })
    .catch(err => console.error(err));
}

function clearAiChat() {
    if (!confirm('Bạn có muốn xóa toàn bộ lịch sử trò chuyện với Trợ lý AI?')) return;
    
    fetch("<?= url('api/ai/clear') ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const msgArea = document.getElementById('aiChatMessages');
            if (msgArea) {
                msgArea.innerHTML = `
                    <div class="ai-msg-row ai-msg-bot mb-3">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                <i class="bi bi-stars"></i>
                            </div>
                            <div class="ai-bubble ai-bubble-bot p-3 rounded-4 shadow-xs">
                                <p class="mb-1 fw-semibold text-dark" style="font-size: 13px;">Đoạn hội thoại đã được làm mới!</p>
                                <p class="small text-muted mb-0" style="font-size: 11.5px;">Hãy nhập câu hỏi bất kỳ về du lịch và ẩm thực Nha Trang để bắt đầu.</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    });
}

function sendAiPrompt(promptText) {
    const input = document.getElementById('aiChatInput');
    if (input) {
        input.value = promptText;
        handleAiChatSubmit(new Event('submit'));
    }
}

function handleAiChatSubmit(e) {
    if (e) e.preventDefault();
    if (!isWidgetLoggedIn) {
        alert('Vui lòng đăng nhập để trò chuyện với Trợ lý AI.');
        window.location.href = "<?= url('login') ?>";
        return;
    }

    const input = document.getElementById('aiChatInput');
    const msgArea = document.getElementById('aiChatMessages');
    const sendBtn = document.getElementById('aiChatSendBtn');
    if (!input || !msgArea) return;

    const userText = input.value.trim();
    if (!userText) return;

    // 1. Thêm bubble tin nhắn của người dùng
    const userMsgHtml = `
        <div class="ai-msg-row ai-msg-user mb-3 text-end">
            <div class="ai-bubble ai-bubble-user p-2.5 rounded-4 shadow-xs text-start d-inline-block">
                <p class="mb-0 small" style="font-size: 12.5px; line-height: 1.45;">${escapeHtml(userText)}</p>
            </div>
        </div>
    `;
    msgArea.insertAdjacentHTML('beforeend', userMsgHtml);
    input.value = '';
    msgArea.scrollTop = msgArea.scrollHeight;

    // 2. Thêm indicator đang trả lời
    const typingId = 'typing-' + Date.now();
    const typingHtml = `
        <div class="ai-msg-row ai-msg-bot mb-3" id="${typingId}">
            <div class="d-flex gap-2 align-items-start">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                    <i class="bi bi-robot" style="font-size: 13px;"></i>
                </div>
                <div class="ai-bubble ai-bubble-bot p-2 rounded-3 shadow-xs">
                    <div class="ai-typing-indicator d-flex align-items-center py-1" aria-busy="true">
                        <span class="small text-muted me-2" style="font-size: 11px;">Đang suy nghĩ...</span>
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
    `;
    msgArea.insertAdjacentHTML('beforeend', typingHtml);
    msgArea.scrollTop = msgArea.scrollHeight;

    if (sendBtn) sendBtn.disabled = true;

    // 3. Gửi yêu cầu AJAX đến API
    fetch("<?= url('api/ai/chat') ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: userText
        })
    })
    .then(res => res.json())
    .then(data => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        if (sendBtn) sendBtn.disabled = false;

        if (data.require_login) {
            alert(data.message || 'Vui lòng đăng nhập để tiếp tục.');
            window.location.href = "<?= url('login') ?>";
            return;
        }

        if (data.success) {
            const formattedReply = renderMarkdownSimple(data.reply);
            
            let locationsHtml = '';
            if (data.matched_locations && data.matched_locations.length > 0) {
                locationsHtml = `<div class="mt-2 border-top pt-2"><div class="fw-bold small text-dark mb-1" style="font-size: 11px;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa điểm gợi ý:</div><div class="row g-1">`;
                data.matched_locations.slice(0, 2).forEach(loc => {
                    const imgUrl = (loc.anh_dai_dien && loc.anh_dai_dien.startsWith('http')) ? loc.anh_dai_dien : "<?= url('') ?>/" + (loc.anh_dai_dien || 'assets/images/placeholder.jpg');
                    const detailUrl = "<?= url('dia-diem') ?>/" + loc.duong_dan;
                    const bookBtn = loc.co_ban_ve ? `
                        <a href="${loc.goi_ve_id ? ('<?= url('booking') ?>/' + loc.goi_ve_id) : (detailUrl + '#ve-tham-quan')}" class="btn btn-success btn-sm rounded-pill py-0 px-1 mt-1 w-100 fw-semibold" style="font-size: 9px;">
                            <i class="bi bi-cart-check me-1"></i>Đặt vé
                        </a>
                    ` : '';
                    locationsHtml += `
                        <div class="col-6">
                            <div class="ai-mini-card p-1.5 d-flex flex-column justify-content-between h-100">
                                <div>
                                    <img src="${imgUrl}" class="w-100 rounded-1 mb-1" style="height: 50px; object-fit: cover;" loading="lazy">
                                    <strong class="text-truncate d-block text-dark small" style="font-size: 11px;">${escapeHtml(loc.ten_dia_diem)}</strong>
                                </div>
                                <div>
                                    ${bookBtn}
                                    <a href="${detailUrl}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-1 mt-1 w-100" style="font-size: 9.5px;">
                                        Xem &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                locationsHtml += `</div></div>`;
            }

            const botMsgHtml = `
                <div class="ai-msg-row ai-msg-bot mb-3">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                            <i class="bi bi-robot" style="font-size: 13px;"></i>
                        </div>
                        <div class="ai-bubble ai-bubble-bot p-2.5 rounded-4 shadow-xs" style="max-width: 90%;">
                            <div class="small" style="font-size: 12px; line-height: 1.5;">
                                ${formattedReply}
                            </div>
                            ${locationsHtml}
                        </div>
                    </div>
                </div>
            `;
            msgArea.insertAdjacentHTML('beforeend', botMsgHtml);
        } else {
            msgArea.insertAdjacentHTML('beforeend', `
                <div class="ai-msg-row ai-msg-bot mb-3">
                    <div class="ai-bubble ai-bubble-bot p-2 rounded-3 border-danger bg-danger-subtle text-danger small">
                        ${data.message || 'Lỗi kết nối trợ lý.'}
                    </div>
                </div>
            `);
        }
        msgArea.scrollTop = msgArea.scrollHeight;
    })
    .catch(err => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        if (sendBtn) sendBtn.disabled = false;
        alert('Lỗi kết nối máy chủ.');
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function renderMarkdownSimple(md) {
    if (!md) return '';
    let html = escapeHtml(md);
    html = html.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-dark mt-2 mb-1">$1</h6>');
    html = html.replace(/^#### (.*$)/gim, '<strong class="d-block text-primary mt-1 mb-1">$1</strong>');
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    html = html.replace(/^---$/gim, '<hr class="my-1 border-secondary opacity-25">');
    html = html.replace(/^> (.*$)/gim, '<div class="p-1.5 my-1 rounded bg-light border-start border-3 border-primary text-secondary">$1</div>');
    html = html.replace(/^\- (.*$)/gim, '<div class="d-flex align-items-start gap-1 my-0.5"><span>•</span><span>$1</span></div>');
    html = html.replace(/\n\n/g, '<br>');
    return html;
}
</script>

<?php
/**
 * Trang Giới Thiệu Tổng Quan Về Nha Trang
 * Lịch Sử • Văn Hóa Di Tích • Khí Hậu • Mẹo Du Lịch Theo Mùa • Ẩm Thực • Con Người
 * @var string $title
 * @var array $weather
 */
require_once dirname(__DIR__, 2) . '/layouts/header.php';
require_once dirname(__DIR__, 2) . '/layouts/navbar.php';
?>

<!-- ========================================================
     1. HERO BANNER VỀ NHA TRANG (HIGH CONTRAST & EDITORIAL)
     ======================================================== -->
<section class="position-relative text-white py-5" style="background: linear-gradient(135deg, rgba(11,37,69,0.92) 0%, rgba(19,122,142,0.85) 100%), url('<?= url('assets/images/nhatrang-hero.jpg') ?>') center/cover no-repeat;">
    <div class="container py-4 py-lg-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Giới thiệu về Nha Trang</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-3 shadow-sm">
                    <i class="bi bi-star-fill me-1"></i>Vịnh Biển Đẹp Nhất Thế Giới • Xứ Sở Trầm Hương &amp; Biển Yến
                </span>
                <h1 class="display-5 fw-bold mb-3 text-white">
                    Nha Trang — Hòn Ngọc Biển Đông &amp; Xứ Trầm Biển Yến
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 680px; font-size: 1.15rem; line-height: 1.7;">
                    Nằm bên bờ biển duyên hải Nam Trung Bộ, Nha Trang là tuyệt tác của thiên nhiên với bãi cát vàng mịn màng uốn cong theo vịnh biển êm đềm, bề dày trầm tích văn hóa Champa nghìn năm cùng ẩm thực biển tươi ngon nức tiếng bậc nhất Việt Nam.
                </p>

                <!-- Các Badge Nổi Bật Với Độ Tương Phản Cao (Nền Trắng Sáng - Chữ Đậm Nét) -->
                <div class="d-flex flex-wrap gap-2">
                    <div class="bg-white text-dark rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 border" style="font-size: 13.5px; font-weight: 600;">
                        <i class="bi bi-brightness-high-fill text-warning fs-5"></i>
                        <span>300+ Ngày Nắng Rực Rỡ/Năm</span>
                    </div>
                    <div class="bg-white text-dark rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 border" style="font-size: 13.5px; font-weight: 600;">
                        <i class="bi bi-water text-info fs-5"></i>
                        <span>19 Hòn Đảo Vịnh Biển Tuyệt Đẹp</span>
                    </div>
                    <div class="bg-white text-dark rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 border" style="font-size: 13.5px; font-weight: 600;">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>1.200 Năm Di Sản Tháp Bà Ponagar</span>
                    </div>
                </div>
            </div>

            <!-- Card Thời Tiết Biển Thời Gian Thực Thu Nhỏ -->
            <?php if (!empty($weather)): ?>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-lg rounded-4 p-3 bg-white text-dark">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                            <span class="small fw-bold text-uppercase text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Thời Tiết Vịnh Nha Trang</span>
                            <span class="badge bg-success-subtle text-success small fw-semibold">Biển êm du lịch</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi <?= htmlspecialchars($weather['icon'] ?? 'bi-sun-fill text-warning') ?> fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-dark"><?= $weather['temp'] ?? 29 ?>°C</h3>
                                <div class="small fw-semibold text-secondary">
                                    <?= htmlspecialchars($weather['condition_vi']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                            <span><i class="bi bi-tsunami me-1 text-info"></i>Sóng: <?= htmlspecialchars($weather['wave_height']) ?></span>
                            <span><i class="bi bi-wind me-1 text-primary"></i>Gió: <?= htmlspecialchars($weather['wind_speed']) ?> km/h</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ========================================================
     2. KHÍ HẬU & THIÊN NHIÊN VỊNH BIỂN NHA TRANG
     ======================================================== -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="text-primary fw-bold small text-uppercase" style="letter-spacing: 1px;">
                    <i class="bi bi-cloud-sun me-1"></i>Khí Hậu &amp; Thiên Nhiên Vịnh Biển
                </span>
                <h2 class="fw-bold text-dark mt-2 mb-3">
                    Khí Hậu Ôn Hòa Cận Xích Đạo, Hơn 300 Ngày Nắng Vàng Biển Êm
                </h2>
                <p class="text-secondary lh-lg mb-3">
                    Nha Trang sở hữu điều kiện khí hậu nhiệt đới gió mùa nhưng chịu ảnh hưởng sâu sắc của khí hậu đại dương nên quanh năm ôn hòa, nhiệt độ trung bình từ <strong>26°C - 28°C</strong>. Khác với các vùng biển miền Trung khác, Nha Trang rất hiếm khi chịu bão lớn nhờ có dãy núi bao bọc hình cánh cung và 19 hòn đảo lớn nhỏ che chắn ngoài khơi.
                </p>
                <p class="text-secondary lh-lg mb-3">
                    Nước biển Nha Trang quanh năm có độ mặn vừa phải, mặt nước êm ả, trong suốt có thể nhìn thấy những rạn san hô kỳ thú ở độ sâu 5 - 10 mét. Đây chính là yếu tố tự nhiên trời ban giúp Nha Trang trở thành kinh đô của các tour lặn biển và thể thao mặt nước tại Việt Nam.
                </p>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="rounded-4 overflow-hidden shadow-sm h-100">
                            <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80" alt="Resort biển Nha Trang" class="w-100 h-100 object-fit-cover" style="min-height: 240px;" loading="lazy">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-4 overflow-hidden shadow-sm h-100">
                            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80" alt="Bãi biển Nha Trang" class="w-100 h-100 object-fit-cover" style="min-height: 240px;" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     3. MẸO DU LỊCH NHA TRANG THEO MÙA & THEO THỜI TIẾT (PRACTICAL TIPS)
     ======================================================== -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5" style="max-width: 720px; margin: 0 auto;">
            <span class="text-primary fw-bold small text-uppercase" style="letter-spacing: 1px;">
                <i class="bi bi-compass me-1"></i>Cẩm Nang Bỏ Túi
            </span>
            <h2 class="fw-bold text-dark mt-2">Mẹo Du Lịch Nha Trang Theo Mùa &amp; Thời Tiết</h2>
            <p class="text-muted small">Những bí quyết thực chiến từ người bản địa giúp bạn có chuyến du ngoạn trọn vẹn, an toàn và tiết kiệm nhất.</p>
        </div>

        <div class="row g-4">
            <!-- Mẹo Mùa Khô -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-sun-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark small fw-bold">Tháng 1 - Tháng 8</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6 mt-1">Mùa Khô &amp; Nắng Vàng Biển Ngọc</h5>
                        </div>
                    </div>
                    <ul class="text-secondary small lh-lg ps-3 mb-0">
                        <li><strong>Giờ xuất bến cano đảo:</strong> Nên xuất bến tại Cảng Vĩnh Trường trước <strong>08:30 sáng</strong> để tận hưởng lúc mặt nước êm ả nhất, nắng dịu và nước trong nhìn thấu đáy san hô.</li>
                        <li><strong>Chống nắng an toàn:</strong> Chỉ số UV buổi trưa thường từ 8.0 - 9.5; hãy mang nón vành rộng, áo khoác mỏng và ưu tiên kem chống nắng lành tính cho rạn san hô (Reef-safe).</li>
                        <li><strong>Lưu ý lặn Hòn Mun:</strong> Không chạm tay vào san hô sống, không giẫm đạp đáy biển và không cho cá ăn thức ăn lạ để bảo tồn hệ sinh thái.</li>
                    </ul>
                </div>
            </div>

            <!-- Mẹo Mùa Mưa Nhẹ -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info">
                            <i class="bi bi-cloud-drizzle-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-info text-white small fw-bold">Tháng 9 - Tháng 12</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6 mt-1">Mùa Dịu Mát &amp; Khoáng Nóng</h5>
                        </div>
                    </div>
                    <ul class="text-secondary small lh-lg ps-3 mb-0">
                        <li><strong>Thiên đường ngâm bùn khoáng:</strong> Tiết trời se lạnh là thời điểm tuyệt vời nhất để ngâm khoáng nóng tự nhiên tại <strong>I-Resort</strong> (38°C - 40°C), giúp thư giãn cơ khớp và phục hồi da.</li>
                        <li><strong>Thưởng thức ẩm thực ấm cúng:</strong> Thích hợp thưởng thức bò nướng than hoa Lạc Cảnh, nem nướng Ninh Hòa nóng hổi hoặc các nồi lẩu mực chua cay ven đường Tháp Bà.</li>
                        <li><strong>Xem show trong nhà hát:</strong> Xem show <strong>Rối Mơ tại Nhà Hát Đó</strong> hoặc tham quan Bảo tàng Hải dương học, Viện Pasteur Nha Trang mà không lo thời tiết.</li>
                    </ul>
                </div>
            </div>

            <!-- Mẹo Đi Lại & Mua Sắm -->
            <div class="col-lg-4 col-md-12">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                            <i class="bi bi-bag-check-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-success text-white small fw-bold">Mua Sắm &amp; Di Chuyển</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6 mt-1">Đặc Sản Chợ Đầm &amp; Đi Cano</h5>
                        </div>
                    </div>
                    <ul class="text-secondary small lh-lg ps-3 mb-0">
                        <li><strong>Tránh say sóng cano:</strong> Nên ngồi ở hàng ghế giữa thân cano (vị trí ổn định nhất, ít nảy sóng), uống thuốc chống say trước 30 phút và nhìn thẳng ra đường chân trời.</li>
                        <li><strong>Chọn Chả cá &amp; Mực một nắng:</strong> Ghé Chợ Đầm hoặc Chợ Xóm Mới vào buổi sáng. Chọn chả cá quết tay vàng tự nhiên, mực một nắng câu tay thân dày dẻo có phấn trắng mịn.</li>
                        <li><strong>Yến sào Khánh Hòa:</strong> Mua tại các đại lý chính thức của Công ty Yến Sào Khánh Hòa (Sanest) để đảm bảo 100% yến đảo thiên nhiên chuẩn nguồn gốc.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     4. LỊCH SỬ & VĂN HÓA CHI TIẾT CÁC DI TÍCH BIỂU TƯỢNG
     ======================================================== -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" style="max-width: 720px; margin: 0 auto;">
            <span class="text-primary fw-bold small text-uppercase" style="letter-spacing: 1px;">
                <i class="bi bi-bank me-1"></i>Trầm Tích Lịch Sử
            </span>
            <h2 class="fw-bold text-dark mt-2">Dấu Ấn Văn Hóa &amp; Các Di Tích Lịch Sử Ngàn Năm</h2>
            <p class="text-muted small">Từ cội nguồn xứ Kauthara thời Champa cổ kính đến những công trình kiến trúc phương Tây nhuốm màu thời gian.</p>
        </div>

        <div class="row g-4">
            <!-- 1. Tháp Bà Ponagar -->
            <div class="col-lg-6">
                <div class="card border rounded-4 overflow-hidden h-100 shadow-sm d-flex flex-column flex-md-row">
                    <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=600&q=80" alt="Tháp Bà Ponagar" class="w-100 object-fit-cover" style="min-height: 220px; max-width: 220px;" loading="lazy">
                    <div class="p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle small mb-2">Thế kỷ VIII - XIII</span>
                            <h4 class="fw-bold text-dark fs-5 mb-2">Quần Thể Tháp Bà Ponagar</h4>
                            <p class="text-secondary small mb-0 lh-base">
                                Tọa lạc trên đồi Cù Lao soi bóng xuống sông Cái, Tháp Bà Ponagar là kiệt tác kiến trúc gạch nung Champa không dùng vữa kết dính mạch. Nơi đây thờ Mẹ Xứ Sở Po Ino Nagar – người dạy nhân dân cày cấy, dệt vải. Lễ hội Tháp Bà (20-23/3 âm lịch) là di sản văn hóa phi vật thể quốc gia thu hút hàng vạn đồng bào về chiêm bái.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Chùa Long Sơn -->
            <div class="col-lg-6">
                <div class="card border rounded-4 overflow-hidden h-100 shadow-sm d-flex flex-column flex-md-row">
                    <img src="https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=600&q=80" alt="Chùa Long Sơn tượng Phật Trắng" class="w-100 object-fit-cover" style="min-height: 220px; max-width: 220px;" loading="lazy">
                    <div class="p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small mb-2">Thành lập 1886</span>
                            <h4 class="fw-bold text-dark fs-5 mb-2">Chùa Long Sơn &amp; Kim Thân Phật Tổ</h4>
                            <p class="text-secondary small mb-0 lh-base">
                                Còn gọi là Chùa Đăng Long Cổ Tự dưới chân đồi Trại Thủy. Điểm nhấn là bức tượng Kim Thân Phật Tổ (Phật Trắng) ngồi thuyết pháp cao 24m uy nghiêm trên đỉnh đồi. Du khách vượt qua 193 bậc tam cấp sẽ được chiêm ngưỡng toàn cảnh thành phố biển Nha Trang thanh bình từ trên cao.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Nhà Thờ Núi Nha Trang -->
            <div class="col-lg-6">
                <div class="card border rounded-4 overflow-hidden h-100 shadow-sm d-flex flex-column flex-md-row">
                    <img src="https://images.unsplash.com/photo-1548625361-165c711a684b?auto=format&fit=crop&w=600&q=80" alt="Nhà thờ Núi Nha Trang" class="w-100 object-fit-cover" style="min-height: 220px; max-width: 220px;" loading="lazy">
                    <div class="p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-info-subtle text-info border border-info-subtle small mb-2">Xây dựng 1928 - 1933</span>
                            <h4 class="fw-bold text-dark fs-5 mb-2">Nhà Thờ Chánh Tòa Kitô Vua (Nhà Thờ Núi)</h4>
                            <p class="text-secondary small mb-0 lh-base">
                                Công trình kiến trúc tôn giáo Gothic đá Pháp tuyệt mỹ do linh mục Louis Vallet khởi công. Tọa lạc trên mỏm núi Bông cao 12m, nhà thờ sở hữu tháp chuông cao 38m với dàn chuông đồng Carillon ngân vang trầm bổng và những ô cửa kính hoa hồng ngập tràn ánh sáng thiêng liêng.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Viện Hải Dương Học -->
            <div class="col-lg-6">
                <div class="card border rounded-4 overflow-hidden h-100 shadow-sm d-flex flex-column flex-md-row">
                    <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=600&q=80" alt="Viện Hải dương học Nha Trang" class="w-100 object-fit-cover" style="min-height: 220px; max-width: 220px;" loading="lazy">
                    <div class="p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small mb-2">Thành lập 1922</span>
                            <h4 class="fw-bold text-dark fs-5 mb-2">Viện Hải Dương Học Nha Trang</h4>
                            <p class="text-secondary small mb-0 lh-base">
                                Cơ sở nghiên cứu biển đầu tiên và quy mô nhất Đông Nam Á với hơn 100 năm lịch sử. Nơi đây lưu giữ hơn 20.000 mẫu vật sinh vật biển của hơn 4.000 loài quý hiếm, cùng bộ xương cá voi lưng gù khổng lồ dài 26m cao 3m được phục chế nguyên vẹn kỳ vĩ.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     5. BẢN ĐỒ ẨM THỰC ĐẶC SẢN NHA TRANG TRỨ DANH
     ======================================================== -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                <span class="text-primary fw-bold small text-uppercase" style="letter-spacing: 1px;">
                    <i class="bi bi-egg-fried me-1"></i>Hương Vị Trầm Biển
                </span>
                <h2 class="fw-bold text-dark mt-2 mb-3">
                    Thiên Đường Ẩm Thực Biển Khơi Gây Thương Nhớ
                </h2>
                <p class="text-secondary lh-lg mb-4">
                    Ẩm thực Nha Trang chinh phục du khách bởi độ tươi ngon nguyên bản của cá tôm vừa kéo lưới từ vịnh biển, kết hợp kỹ thuật chế biến thanh nhã, hài hòa giữa vị ngọt của hải sản và mắm ớt thơm cay nồng ấm.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger mt-1">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block">Nem nướng Ninh Hòa</strong>
                            <span class="small text-muted">Thịt heo xay ướp gia vị nướng trên than hoa, cuốn bánh tráng chiên giòn, rau sống xoài non và chấm nước sốt tương đậu bí truyền nóng hổi.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger mt-1">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block">Bún chả cá sứa Nha Trang</strong>
                            <span class="small text-muted">Nước dùng nấu từ xương cá cờ ngọt thanh không mỡ, chả cá thu chiên thơm lừng và từng miếng sứa trắng trong giòn sần sật mát lạnh.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger mt-1">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block">Bánh căn mực nhảy &amp; Bò nướng Lạc Cảnh</strong>
                            <span class="small text-muted">Bánh căn nướng lò đất giòn tan với mực trứng tươi rói chấm mắm hẹ; bò ướp mật ong trên lò than rực hồng hơn 40 năm tuổi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <img src="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80" alt="Nem nướng Nha Trang" style="height: 220px; object-fit: cover;" loading="lazy">
                            <div class="p-3">
                                <h6 class="fw-bold mb-1">Nem Nướng Ninh Hòa</h6>
                                <small class="text-muted">Đặc sản trứ danh ẩm thực Nam Trung Bộ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80" alt="Hải sản tươi sống Nha Trang" style="height: 220px; object-fit: cover;" loading="lazy">
                            <div class="p-3">
                                <h6 class="fw-bold mb-1">Hải Sản Tươi Sống Bờ Kè</h6>
                                <small class="text-muted">Tôm hùm, ghẹ Cam Ranh, sò điệp mỡ hành</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     6. CON NGƯỜI & LỐI SỐNG XỨ BIỂN TRẦM HƯƠNG
     ======================================================== -->
<section class="py-5 bg-white">
    <div class="container text-center" style="max-width: 820px; margin: 0 auto;">
        <span class="text-primary fw-bold small text-uppercase" style="letter-spacing: 1px;">
            <i class="bi bi-people me-1"></i>Con Người &amp; Lối Sống
        </span>
        <h2 class="fw-bold text-dark mt-2 mb-3">
            Nét Chân Chất, Hào Sảng Của Người Xứ Biển Trầm Hương
        </h2>
        <p class="text-secondary lh-lg mb-4">
            Điều níu giữ bước chân du khách sâu đậm nhất sau mỗi chuyến ghé thăm Nha Trang không chỉ là biển xanh cát trắng, mà chính là nét tính cách hiền hòa, chân chất, nụ cười rạng rỡ và sự hào sảng của người dân xứ biển. Đời sống gắn liền với ngọn sóng ngọn gió qua bao thế hệ đã hun đúc nên một tâm hồn cởi mở, chân thành và lòng mến khách vô bờ bến.
        </p>

        <!-- CTA BẮT ĐẦU CHUYẾN ĐI -->
        <div class="d-flex flex-wrap justify-content-center gap-3 pt-2">
            <a href="<?= url('locations') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="bi bi-map me-2"></i>Khám Phá Toàn Bộ Địa Điểm Trên Bản Đồ
            </a>
            <a href="<?= url('locations?loai=am_thuc') ?>" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-cup-hot me-2"></i>Tìm Quán Ăn Đặc Sản Nha Trang
            </a>
            <a href="<?= url('locations?co_ban_ve=1') ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-ticket-perforated me-2"></i>Đặt Vé Vui Chơi &amp; Tour Đảo
            </a>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__, 2) . '/layouts/footer.php'; ?>

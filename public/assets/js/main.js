/**
 * Hệ thống Bản đồ Tương tác & Khám phá Địa điểm Nha Trang
 * Tuân thủ nguyên tắc thiết kế Distinctive & Intentional (Skill.md)
 */

document.addEventListener('DOMContentLoaded', function () {
    let map = null;
    let markersLayer = null;
    let userMarker = null;
    let userCoords = null;
    let allLocations = [];

    const mapElement = document.getElementById('map');

    // 1. Khởi tạo Bản đồ Tổng quan Nha Trang
    if (mapElement) {
        // Tọa độ trung tâm thành phố Nha Trang: [12.2475, 109.1967]
        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([12.2475, 109.1967], 13);

        // Tạo lớp nền bản đồ dựa trên cấu hình MAP_PROVIDER và MAP_API_KEY
        const primaryTileLayer = taoTileLayer();
        primaryTileLayer.addTo(map);

        // Lớp nền phụ: Bản đồ sáng Positron
        const keyParam = (window.MAP_API_KEY) ? '?key=' + encodeURIComponent(window.MAP_API_KEY) : '';
        const cartoPositron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png' + keyParam, {
            maxZoom: 20,
            subdomains: 'abcd',
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        });

        // Bổ sung điều khiển chuyển đổi lớp nền bản đồ
        L.control.layers({
            "Bản đồ Mặc định": primaryTileLayer,
            "Bản đồ Sáng (Positron)": cartoPositron
        }, null, { position: 'topright' }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);

        // Đảm bảo kích thước bản đồ được tính toán chuẩn xác, tránh bị ô xám
        setTimeout(() => {
            map.invalidateSize();
        }, 200);

        // Đọc tham số bộ lọc từ URL ban đầu (ví dụ: ?loai=am_thuc khi bấm từ Navbar)
        const initialFilters = docThamSoUrl();

        // Đồng bộ giá trị vào Form nếu có
        dongBoFormTuUrl(initialFilters);

        // Tải dữ liệu ban đầu theo tham số URL
        taiDuLieuBanDo(initialFilters);

        // Gắn sự kiện bộ lọc (Nút Lọc Kết Quả & Đặt Lại)
        ganSuKienBoLoc();

        // Gắn sự kiện định vị GPS
        ganSuKienDinhVi();

        // Gắn sự kiện chuyển đổi chế độ xem (Split, Grid, Map)
        ganSuKienCheDoXem();

        // Gắn sự kiện quick filter chips
        ganSuKienQuickChips();

        // Gắn sự kiện sắp xếp
        ganSuKienSapXep();
    }

    // 2. Hàm tạo tile layer theo cấu hình MAP_PROVIDER và MAP_API_KEY
    function taoTileLayer() {
        const provider = (window.MAP_PROVIDER || 'cartodb').toLowerCase();
        const apiKey = (window.MAP_API_KEY || '').trim();
        const keyParam = apiKey ? '?key=' + encodeURIComponent(apiKey) : '';

        if (apiKey) {
            if (provider === 'mapbox') {
                return L.tileLayer(`https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${apiKey}`, {
                    maxZoom: 20,
                    attribution: '&copy; Mapbox &copy; OpenStreetMap'
                });
            }
            if (provider === 'maptiler') {
                return L.tileLayer(`https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=${apiKey}`, {
                    maxZoom: 20,
                    attribution: '&copy; MapTiler &copy; OpenStreetMap'
                });
            }
            if (provider === 'goong') {
                return L.tileLayer(`https://tiles.goong.io/{z}/{x}/{y}.png?api_key=${apiKey}`, {
                    maxZoom: 20,
                    attribution: '&copy; Goong Maps'
                });
            }
        }

        // Mặc định: CartoDB Voyager có gắn API Key để xóa watermark
        return L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png' + keyParam, {
            maxZoom: 20,
            subdomains: 'abcd',
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        });
    }

    // 3. Hàm đọc tham số từ URL hiện tại
    function docThamSoUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const filters = {};
        if (urlParams.get('loai')) filters.loai = urlParams.get('loai');
        if (urlParams.get('danh_muc_id')) filters.danh_muc_id = urlParams.get('danh_muc_id');
        if (urlParams.get('tu_khoa')) filters.tu_khoa = urlParams.get('tu_khoa');
        if (urlParams.get('muc_gia')) filters.muc_gia = urlParams.get('muc_gia');
        if (urlParams.get('co_ban_ve')) filters.co_ban_ve = urlParams.get('co_ban_ve');
        return filters;
    }

    // 3. Đồng bộ giá trị vào Form bộ lọc
    function dongBoFormTuUrl(filters) {
        const form = document.getElementById('live-filter-form');
        if (!form) return;

        if (filters.loai && form.elements['loai']) {
            form.elements['loai'].value = filters.loai;
        }
        if (filters.danh_muc_id && form.elements['danh_muc_id']) {
            form.elements['danh_muc_id'].value = filters.danh_muc_id;
        }
        if (filters.tu_khoa && form.elements['tu_khoa']) {
            form.elements['tu_khoa'].value = filters.tu_khoa;
        }
        if (filters.muc_gia && form.elements['muc_gia']) {
            form.elements['muc_gia'].value = filters.muc_gia;
        }
        if (form.elements['co_ban_ve']) {
            form.elements['co_ban_ve'].checked = (filters.co_ban_ve === '1');
        }

        locDanhMucTheoLoai();
    }

    // 4. Lọc dropdown Danh mục khi chọn Phân loại hình để tránh mâu thuẫn
    function locDanhMucTheoLoai() {
        const form = document.getElementById('live-filter-form');
        if (!form || !form.elements['loai'] || !form.elements['danh_muc_id']) return;

        const loaiDuocChon = form.elements['loai'].value;
        const selectDanhMuc = form.elements['danh_muc_id'];
        const options = selectDanhMuc.querySelectorAll('option');

        options.forEach(opt => {
            if (opt.value === '') {
                opt.style.display = '';
                return;
            }
            const optLoai = opt.getAttribute('data-loai');
            if (!loaiDuocChon || optLoai === loaiDuocChon) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
                if (opt.selected) {
                    selectDanhMuc.value = '';
                }
            }
        });
    }

    // 5. Hàm tải dữ liệu địa điểm từ API
    function taiDuLieuBanDo(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        const apiUrl = (window.APP_URL || '') + '/api/locations' + (query ? '?' + query : '');

        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    allLocations = res.data;
                    renderMarkers(allLocations);
                    capNhatDanhSachCards(allLocations);

                    // Tự động căn chỉnh tầm nhìn bản đồ nếu có địa điểm
                    if (allLocations.length > 0 && map) {
                        const bounds = [];
                        allLocations.forEach(item => {
                            const lat = parseFloat(item.vi_do);
                            const lng = parseFloat(item.kinh_do);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                bounds.push([lat, lng]);
                            }
                        });
                        if (bounds.length > 0) {
                            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                        }
                    }
                }
            })
            .catch(err => console.error('Lỗi tải dữ liệu địa điểm:', err));
    }

    // 6. Render Custom Markers trên Leaflet
    function renderMarkers(locations) {
        if (!markersLayer) return;
        markersLayer.clearLayers();

        locations.forEach(item => {
            const lat = parseFloat(item.vi_do);
            const lng = parseFloat(item.kinh_do);

            if (isNaN(lat) || isNaN(lng)) return;

            const isFood = item.loai_danh_muc === 'am_thuc';
            const markerClass = isFood ? 'marker-food' : 'marker-travel';
            const iconHtml = isFood 
                ? '<i class="bi bi-egg-fried fs-6"></i>' 
                : '<i class="bi bi-compass fs-6"></i>';

            const customIcon = L.divIcon({
                className: 'custom-leaflet-marker-wrapper',
                html: `<div class="custom-marker ${markerClass}" id="marker-point-${item.id}">${iconHtml}</div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 18],
                popupAnchor: [0, -20]
            });

            // Tính khoảng cách nếu đã có vị trí người dùng
            let distanceHtml = '';
            if (userCoords) {
                const dist = tinhKhoangCachKm(userCoords.lat, userCoords.lng, lat, lng);
                distanceHtml = `<div class="distance-pill mt-1"><i class="bi bi-geo"></i> Cách bạn ${dist.toFixed(1)} km</div>`;
            }

            const giaFormatted = formatTienTe(item.gia_thap_nhat) + ' - ' + formatTienTe(item.gia_cao_nhat);
            const badgeTag = isFood ? 'badge-tag-food' : 'badge-tag-travel';
            const loaiLabel = isFood ? 'Ẩm thực' : 'Du lịch';

            const popupContent = `
                <div class="popup-card">
                    <img src="${item.anh_dai_dien}" class="popup-img" alt="${item.ten_dia_diem}">
                    <div class="popup-body">
                        <span class="badge-tag ${badgeTag} mb-1 d-inline-block">${loaiLabel} &bull; ${item.ten_danh_muc}</span>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">${item.ten_dia_diem}</h6>
                        <p class="text-muted small mb-1 text-truncate">${item.dia_chi}</p>
                        <div class="text-dark fw-semibold small mb-2">${giaFormatted}</div>
                        ${distanceHtml}
                        <div class="mt-2 pt-2 border-top">
                            <a href="${(window.APP_URL || '')}/dia-diem/${item.duong_dan}" class="btn btn-sm btn-coastal w-100 py-1" style="font-size: 13px;">
                                Xem chi tiết & Tọa độ
                            </a>
                        </div>
                    </div>
                </div>
            `;

            const marker = L.marker([lat, lng], { icon: customIcon })
                .bindPopup(popupContent);

            marker.locationId = item.id;
            markersLayer.addLayer(marker);
        });
    }

    // 7. Cập nhật thẻ địa điểm (Cards) đồng bộ trên giao diện
    function capNhatDanhSachCards(locations) {
        const container = document.getElementById('locations-container');
        const countBadge = document.getElementById('locations-count');
        const mapPinCount = document.getElementById('map-pin-count');
        
        if (countBadge) {
            countBadge.innerText = locations.length;
        }
        if (mapPinCount) {
            mapPinCount.innerText = locations.length;
        }

        if (!container) return;

        if (locations.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="p-4 bg-white rounded-4 border shadow-sm">
                        <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold text-secondary">Không tìm thấy địa điểm phù hợp</h5>
                        <p class="text-muted small mb-3">Hãy thử đổi từ khóa, mở rộng khung giá hoặc chọn đặt lại bộ lọc.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="document.getElementById('btn-reset-filter')?.click();">
                            Xem tất cả địa điểm
                        </button>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        locations.forEach(item => {
            const isFood = item.loai_danh_muc === 'am_thuc';
            const badgeTag = isFood ? 'badge-tag-food text-coral' : 'badge-tag-travel text-teal';
            const loaiLabel = isFood ? 'Ẩm thực' : 'Du lịch';
            const giaFormatted = formatTienTe(item.gia_thap_nhat) + ' - ' + formatTienTe(item.gia_cao_nhat);

            let distanceHtml = '';
            if (userCoords) {
                const dist = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(item.vi_do), parseFloat(item.kinh_do));
                distanceHtml = `<span class="distance-pill"><i class="bi bi-geo"></i> Cách ${dist.toFixed(1)} km</span>`;
            }

            const ticketBadge = item.co_ban_ve == 1 
                ? '<span class="card-ticket-glass"><i class="bi bi-ticket-perforated me-1"></i>Đặt vé E-Voucher</span>' 
                : '';

            const ratingVal = parseFloat(item.diem_danh_gia || 0);
            const reviewCount = parseInt(item.tong_danh_gia || 0);
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(ratingVal)) {
                    starsHtml += '<i class="bi bi-star-fill text-warning"></i>';
                } else if (i === Math.ceil(ratingVal) && ratingVal % 1 >= 0.5) {
                    starsHtml += '<i class="bi bi-star-half text-warning"></i>';
                } else {
                    starsHtml += '<i class="bi bi-star text-warning"></i>';
                }
            }

            html += `
                <div class="col-md-6 col-sm-12" id="card-location-${item.id}">
                    <div class="location-card-modern" onclick="focusOnMap(${item.id}, ${item.vi_do}, ${item.kinh_do})" style="cursor: pointer;">
                        <div class="location-card-img-wrap">
                            <img src="${item.anh_dai_dien}" class="location-card-img" alt="${item.ten_dia_diem}" loading="lazy">
                            <span class="card-category-glass ${badgeTag}">
                                ${loaiLabel} &bull; ${item.ten_danh_muc}
                            </span>
                            ${ticketBadge}
                        </div>

                        <div class="location-card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small">
                                    ${starsHtml}
                                    <span class="text-muted ms-1">(${reviewCount})</span>
                                </div>
                                ${distanceHtml}
                            </div>
                            
                            <h3 class="location-card-title">${item.ten_dia_diem}</h3>
                            
                            <div class="location-card-address">
                                <i class="bi bi-geo-alt text-danger me-1"></i>${item.dia_chi}
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <span class="location-card-price">${giaFormatted}</span>
                                <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" onclick="focusOnMap(${item.id}, ${item.vi_do}, ${item.kinh_do})" title="Xem vị trí trên bản đồ">
                                        <i class="bi bi-geo-alt"></i>
                                    </button>
                                    <a href="${(window.APP_URL || '')}/dia-diem/${item.duong_dan}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // 8. Tập trung vào bản đồ khi click thẻ (Bidirectional Interaction)
    window.focusOnMap = function(id, lat, lng) {
        if (!map || !markersLayer) return;

        map.flyTo([lat, lng], 15, { duration: 0.8 });

        markersLayer.eachLayer(marker => {
            if (marker.locationId === id) {
                marker.openPopup();
            }
        });

        // Đánh dấu viền card
        document.querySelectorAll('.location-card-modern').forEach(el => el.classList.remove('highlighted-card'));
        const targetCard = document.getElementById(`card-location-${id}`);
        if (targetCard) {
            targetCard.querySelector('.location-card-modern')?.classList.add('highlighted-card');
            targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // 9. Gắn sự kiện bộ lọc có Nút Bấm xác nhận và Nút Đặt lại
    function ganSuKienBoLoc() {
        const filterForm = document.getElementById('live-filter-form');
        if (!filterForm) return;

        // Tự động lọc các danh mục khi chọn Phân loại hình
        const selectLoai = filterForm.elements['loai'];
        if (selectLoai) {
            selectLoai.addEventListener('change', function () {
                locDanhMucTheoLoai();
            });
        }

        // Tự động nhận diện phân loại nếu chọn thẳng Danh mục
        const selectDanhMuc = filterForm.elements['danh_muc_id'];
        if (selectDanhMuc) {
            selectDanhMuc.addEventListener('change', function () {
                const opt = selectDanhMuc.options[selectDanhMuc.selectedIndex];
                const optLoai = opt?.getAttribute('data-loai');
                if (optLoai && selectLoai && !selectLoai.value) {
                    selectLoai.value = optLoai;
                }
            });
        }

        // Xử lý khi nhấn nút Lọc Kết Quả hoặc Submit Form
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            thucHienLocDuLieu();
        });

        // Xử lý nút Đặt lại bộ lọc
        const btnReset = document.getElementById('btn-reset-filter');
        if (btnReset) {
            btnReset.addEventListener('click', function () {
                filterForm.reset();
                locDanhMucTheoLoai();
                if (window.history.pushState) {
                    window.history.pushState({}, '', window.location.pathname);
                }
                taiDuLieuBanDo({});
            });
        }

        function thucHienLocDuLieu() {
            const formData = new FormData(filterForm);
            const params = {};
            formData.forEach((val, key) => {
                if (val !== '') params[key] = val;
            });

            // Cập nhật URL trình duyệt để lưu lại trạng thái bộ lọc
            const newQuery = new URLSearchParams(params).toString();
            if (window.history.pushState) {
                const newUrl = window.location.pathname + (newQuery ? '?' + newQuery : '');
                window.history.pushState({}, '', newUrl);
            }

            taiDuLieuBanDo(params);
        }
    }

    // 10. Định vị vị trí hiện tại của du khách (Geolocation API)
    function ganSuKienDinhVi() {
        const btnLocate = document.getElementById('btn-locate-me');
        if (!btnLocate) return;

        btnLocate.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Trình duyệt của bạn không hỗ trợ định vị GPS.');
                return;
            }

            btnLocate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang định vị...';
            btnLocate.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    btnLocate.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> Đã định vị vị trí của bạn';
                    btnLocate.disabled = false;

                    userCoords = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Đặt marker vị trí người dùng
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }

                    const userIcon = L.divIcon({
                        className: 'custom-leaflet-marker-wrapper',
                        html: '<div class="custom-marker marker-user"><i class="bi bi-person-fill fs-6"></i></div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    });

                    userMarker = L.marker([userCoords.lat, userCoords.lng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup('<strong>Vị trí hiện tại của bạn</strong>')
                        .openPopup();

                    map.flyTo([userCoords.lat, userCoords.lng], 14, { duration: 1.2 });

                    // Tính lại khoảng cách cho toàn bộ địa điểm và sắp xếp theo gần nhất
                    if (allLocations.length > 0) {
                        allLocations.sort((a, b) => {
                            const distA = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(a.vi_do), parseFloat(a.kinh_do));
                            const distB = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(b.vi_do), parseFloat(b.kinh_do));
                            return distA - distB;
                        });
                        renderMarkers(allLocations);
                        capNhatDanhSachCards(allLocations);
                    }
                },
                function (error) {
                    btnLocate.innerHTML = '<i class="bi bi-crosshair me-1"></i> Định vị vị trí của tôi';
                    btnLocate.disabled = false;
                    alert('Không thể lấy vị trí hiện tại: ' + error.message);
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        });
    }

    // 11. Bản đồ chi tiết địa điểm đơn lẻ
    const detailMapEl = document.getElementById('detail-map');
    if (detailMapEl) {
        const lat = parseFloat(detailMapEl.dataset.lat);
        const lng = parseFloat(detailMapEl.dataset.lng);
        const title = detailMapEl.dataset.title;

        if (!isNaN(lat) && !isNaN(lng)) {
            const detailMap = L.map('detail-map', {
                scrollWheelZoom: false
            }).setView([lat, lng], 15);

            taoTileLayer().addTo(detailMap);

            setTimeout(() => {
                detailMap.invalidateSize();
            }, 200);

            const detailIcon = L.divIcon({
                className: 'custom-leaflet-marker-wrapper',
                html: '<div class="custom-marker marker-travel"><i class="bi bi-geo-alt-fill fs-6"></i></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
                popupAnchor: [0, -20]
            });

            L.marker([lat, lng], { icon: detailIcon }).addTo(detailMap)
                .bindPopup(`<strong>${title}</strong>`)
                .openPopup();
        }
    }

    // Helper: Tính khoảng cách theo công thức Haversine
    function tinhKhoangCachKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Bán kính Trái Đất theo km
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    // 12. Gắn sự kiện chuyển đổi chế độ xem (Split / Grid / Map)
    function ganSuKienCheDoXem() {
        const btnSplit = document.getElementById('btn-view-split');
        const btnGrid = document.getElementById('btn-view-grid');
        const btnMap = document.getElementById('btn-view-map');
        const colList = document.getElementById('col-locations-list');
        const colMap = document.getElementById('col-locations-map');

        if (!btnSplit || !btnGrid || !btnMap || !colList || !colMap) return;

        function setMode(mode) {
            btnSplit.classList.remove('active', 'btn-primary');
            btnSplit.classList.add('btn-outline-secondary');
            btnGrid.classList.remove('active', 'btn-primary');
            btnGrid.classList.add('btn-outline-secondary');
            btnMap.classList.remove('active', 'btn-primary');
            btnMap.classList.add('btn-outline-secondary');

            colList.classList.remove('d-none', 'col-12', 'col-lg-7', 'col-xl-7');
            colMap.classList.remove('d-none', 'col-12', 'col-lg-5', 'col-xl-5');

            if (mode === 'split') {
                btnSplit.classList.add('active', 'btn-primary');
                btnSplit.classList.remove('btn-outline-secondary');
                colList.classList.add('col-lg-7', 'col-xl-7');
                colMap.classList.add('col-lg-5', 'col-xl-5');
            } else if (mode === 'grid') {
                btnGrid.classList.add('active', 'btn-primary');
                btnGrid.classList.remove('btn-outline-secondary');
                colList.classList.add('col-12');
                colMap.classList.add('d-none');
            } else if (mode === 'map') {
                btnMap.classList.add('active', 'btn-primary');
                btnMap.classList.remove('btn-outline-secondary');
                colList.classList.add('d-none');
                colMap.classList.add('col-12');
            }

            if (map) {
                setTimeout(() => map.invalidateSize(), 200);
            }
        }

        btnSplit.addEventListener('click', () => setMode('split'));
        btnGrid.addEventListener('click', () => setMode('grid'));
        btnMap.addEventListener('click', () => setMode('map'));

        // Nút fit bounds toàn cảnh
        const btnFit = document.getElementById('btn-fit-bounds');
        if (btnFit && map) {
            btnFit.addEventListener('click', function () {
                if (allLocations.length > 0) {
                    const bounds = [];
                    allLocations.forEach(item => {
                        const lat = parseFloat(item.vi_do);
                        const lng = parseFloat(item.kinh_do);
                        if (!isNaN(lat) && !isNaN(lng)) bounds.push([lat, lng]);
                    });
                    if (bounds.length > 0) map.fitBounds(bounds, { padding: [40, 40] });
                }
            });
        }

        // Nút mobile toggle
        const btnMobileToggle = document.getElementById('btn-mobile-map-toggle');
        if (btnMobileToggle) {
            btnMobileToggle.addEventListener('click', function () {
                const mapCol = document.getElementById('col-locations-map');
                if (mapCol) {
                    mapCol.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    }

    // 13. Gắn sự kiện Quick Filter Chips
    function ganSuKienQuickChips() {
        const chips = document.querySelectorAll('[data-filter-chip]');
        const filterForm = document.getElementById('live-filter-form');
        if (!chips.length || !filterForm) return;

        chips.forEach(chip => {
            chip.addEventListener('click', function () {
                const type = chip.getAttribute('data-filter-chip');
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');

                if (type === 'all') {
                    if (filterForm.elements['loai']) filterForm.elements['loai'].value = '';
                    if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                } else if (type === 'du_lich') {
                    if (filterForm.elements['loai']) filterForm.elements['loai'].value = 'du_lich';
                    if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                } else if (type === 'am_thuc') {
                    if (filterForm.elements['loai']) filterForm.elements['loai'].value = 'am_thuc';
                    if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                } else if (type === 'co_ban_ve') {
                    if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = true;
                }

                locDanhMucTheoLoai();
                filterForm.dispatchEvent(new Event('submit'));
            });
        });
    }

    // 14. Gắn sự kiện sắp xếp Client-side
    function ganSuKienSapXep() {
        const sortSelect = document.getElementById('sort-selector');
        if (!sortSelect) return;

        sortSelect.addEventListener('change', function () {
            const val = this.value;
            if (!allLocations || !allLocations.length) return;

            let sorted = [...allLocations];
            if (val === 'rating') {
                sorted.sort((a, b) => parseFloat(b.diem_danh_gia || 0) - parseFloat(a.diem_danh_gia || 0));
            } else if (val === 'price_asc') {
                sorted.sort((a, b) => parseFloat(a.gia_thap_nhat || 0) - parseFloat(b.gia_thap_nhat || 0));
            } else if (val === 'price_desc') {
                sorted.sort((a, b) => parseFloat(b.gia_cao_nhat || 0) - parseFloat(a.gia_cao_nhat || 0));
            } else {
                sorted.sort((a, b) => parseInt(b.luot_xem || 0) - parseInt(a.luot_xem || 0));
            }

            capNhatDanhSachCards(sorted);
        });
    }
});

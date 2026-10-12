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

        // Lớp nền bản đồ OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);

        // Tải dữ liệu ban đầu
        taiDuLieuBanDo();

        // Gắn sự kiện bộ lọc nếu có form lọc
        ganSuKienBoLoc();

        // Gắn sự kiện định vị GPS
        ganSuKienDinhVi();
    }

    // 2. Hàm tải dữ liệu địa điểm từ API
    function taiDuLieuBanDo(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        const apiUrl = (window.APP_URL || '') + '/api/locations?' + query;

        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    allLocations = res.data;
                    renderMarkers(allLocations);
                    capNhatDanhSachCards(allLocations);
                }
            })
            .catch(err => console.error('Lỗi tải dữ liệu địa điểm:', err));
    }

    // 3. Render Custom Markers trên Leaflet
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

    // 4. Cập nhật thẻ địa điểm (Cards) đồng bộ trên giao diện
    function capNhatDanhSachCards(locations) {
        const container = document.getElementById('locations-container');
        const countBadge = document.getElementById('locations-count');
        
        if (countBadge) {
            countBadge.innerText = locations.length;
        }

        if (!container) return;

        if (locations.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="p-4 bg-white rounded-3 border">
                        <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold text-secondary">Không tìm thấy địa điểm nào</h5>
                        <p class="text-muted small mb-0">Hãy thử đổi từ khóa, mở rộng khung giá hoặc chọn tất cả danh mục.</p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        locations.forEach(item => {
            const isFood = item.loai_danh_muc === 'am_thuc';
            const badgeTag = isFood ? 'badge-tag-food' : 'badge-tag-travel';
            const loaiLabel = isFood ? 'Ẩm thực' : 'Du lịch';
            const giaFormatted = formatTienTe(item.gia_thap_nhat) + ' - ' + formatTienTe(item.gia_cao_nhat);

            let distanceHtml = '';
            if (userCoords) {
                const dist = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(item.vi_do), parseFloat(item.kinh_do));
                distanceHtml = `<span class="distance-pill"><i class="bi bi-geo"></i> Cách ${dist.toFixed(1)} km</span>`;
            }

            const ticketBadge = item.co_ban_ve == 1 
                ? '<span class="badge bg-success-subtle text-success small fw-semibold"><i class="bi bi-ticket-perforated me-1"></i>Có vé online</span>' 
                : '';

            html += `
                <div class="col-lg-4 col-md-6" id="card-location-${item.id}">
                    <div class="location-item-card" onclick="focusOnMap(${item.id}, ${item.vi_do}, ${item.kinh_do})" style="cursor: pointer;">
                        <div class="position-relative">
                            <img src="${item.anh_dai_dien}" class="location-thumb" alt="${item.ten_dia_diem}" loading="lazy">
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge-tag ${badgeTag}">${loaiLabel}</span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-2">
                                ${ticketBadge}
                            </div>
                        </div>

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">${item.ten_danh_muc}</span>
                                ${distanceHtml}
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">
                                ${item.ten_dia_diem}
                            </h5>
                            
                            <p class="text-muted small text-truncate mb-3">
                                <i class="bi bi-geo-alt text-danger me-1"></i>${item.dia_chi}
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <span class="fw-bold small text-dark">${giaFormatted}</span>
                                <a href="${(window.APP_URL || '')}/dia-diem/${item.duong_dan}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="event.stopPropagation();">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // 5. Tập trung vào bản đồ khi click thẻ (Bidirectional Interaction)
    window.focusOnMap = function(id, lat, lng) {
        if (!map || !markersLayer) return;

        map.flyTo([lat, lng], 15, { duration: 1 });

        markersLayer.eachLayer(marker => {
            if (marker.locationId === id) {
                marker.openPopup();
            }
        });

        // Đánh dấu viền card
        document.querySelectorAll('.location-item-card').forEach(el => el.classList.remove('highlighted-card'));
        const targetCard = document.getElementById(`card-location-${id}`);
        if (targetCard) {
            targetCard.querySelector('.location-item-card')?.classList.add('highlighted-card');
        }
    };

    // 6. Gắn sự kiện bộ lọc trực tiếp không reload (Live Filter)
    function ganSuKienBoLoc() {
        const filterForm = document.getElementById('live-filter-form');
        if (!filterForm) return;

        const inputs = filterForm.querySelectorAll('select, input');
        inputs.forEach(input => {
            input.addEventListener('change', function () {
                guiYeuCauLoc();
            });
            if (input.type === 'text') {
                input.addEventListener('keyup', debounce(function () {
                    guiYeuCauLoc();
                }, 300));
            }
        });

        function guiYeuCauLoc() {
            const formData = new FormData(filterForm);
            const params = {};
            formData.forEach((val, key) => {
                if (val !== '') params[key] = val;
            });
            taiDuLieuBanDo(params);
        }
    }

    // 7. Định vị vị trí hiện tại của du khách (Geolocation API)
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

    // 8. Bản đồ chi tiết địa điểm đơn lẻ
    const detailMapEl = document.getElementById('detail-map');
    if (detailMapEl) {
        const lat = parseFloat(detailMapEl.dataset.lat);
        const lng = parseFloat(detailMapEl.dataset.lng);
        const title = detailMapEl.dataset.title;

        if (!isNaN(lat) && !isNaN(lng)) {
            const detailMap = L.map('detail-map', {
                scrollWheelZoom: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(detailMap);

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

    function formatTienTe(val) {
        const num = parseFloat(val) || 0;
        if (num <= 0) return 'Miễn phí';
        return num.toLocaleString('vi-VN') + ' đ';
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});

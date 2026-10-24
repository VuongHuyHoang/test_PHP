/**
 * Hệ thống Bản đồ Tương tác & Khám phá Địa điểm Nha Trang
 * Tuân thủ nguyên tắc thiết kế Distinctive & Intentional (Skill.md)
 */

document.addEventListener('DOMContentLoaded', function () {
    let map = null;
    let markersLayer = null;
    let userMarker = null;
    let userCoords = null;
    let userRadiusCircle = null;
    let currentRadius = 3000;
    let allLocations = [];

    // Helper tiền tệ VNĐ (Định nghĩa ở scope cao nhất để tránh ReferenceError)
    function formatTienTe(val) {
        const num = parseFloat(val) || 0;
        if (num <= 0) return 'Miễn phí';
        return num.toLocaleString('vi-VN') + ' đ';
    }

    // Helper phòng chống XSS DOM Injection
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Helper tạo URL tuyệt đối an toàn cho subfolder / virtual host
    function getAppUrl(path) {
        const base = (window.APP_URL || '').replace(/\/$/, '');
        const cleanPath = (path || '').replace(/^\//, '');
        return (base ? base : '') + '/' + cleanPath;
    }

    // Helper chuẩn hóa đường dẫn ảnh (tuyệt đối / tương đối / fallback)
    function formatImageUrl(url) {
        if (!url) return getAppUrl('assets/images/placeholder.jpg');
        if (url.startsWith('http://') || url.startsWith('https://')) return url;
        return getAppUrl(url);
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function tinhKhoangCachKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Bán kính Trái Đất (km)
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

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

        // Điều khiển chuyển đổi lớp nền bản đồ
        L.control.layers({
            "Bản đồ Mặc định": primaryTileLayer,
            "Bản đồ Sáng (Positron)": cartoPositron
        }, null, { position: 'topright' }).addTo(map);

        // Khởi tạo Marker Cluster gom cụm hoặc fallback về LayerGroup
        if (typeof L.markerClusterGroup === 'function') {
            markersLayer = L.markerClusterGroup({
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                spiderfyOnMaxZoom: true,
                maxClusterRadius: 40,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    let sizeClass = 'marker-cluster-small';
                    if (count > 10) sizeClass = 'marker-cluster-medium';
                    if (count > 25) sizeClass = 'marker-cluster-large';
                    return new L.DivIcon({
                        html: '<div><span>' + count + '</span></div>',
                        className: 'marker-cluster ' + sizeClass,
                        iconSize: new L.Point(40, 40)
                    });
                }
            }).addTo(map);
        } else {
            markersLayer = L.layerGroup().addTo(map);
        }

        setTimeout(() => {
            map.invalidateSize();
        }, 200);

        // Đọc tham số bộ lọc từ URL ban đầu
        const initialFilters = docThamSoUrl();
        const hasUrlFilters = Object.keys(initialFilters).length > 0;

        // Đồng bộ giá trị vào Form nếu có
        dongBoFormTuUrl(initialFilters);

        // Nếu trang đã có sẵn dữ liệu server-side và không có bộ lọc URL, render markers ngay lập tức!
        if (window.INITIAL_LOCATIONS && Array.isArray(window.INITIAL_LOCATIONS) && window.INITIAL_LOCATIONS.length > 0 && !hasUrlFilters) {
            allLocations = window.INITIAL_LOCATIONS;
            renderMarkers(allLocations);
            if (allLocations.length > 0) {
                fitMapToBounds(allLocations);
            }
        } else {
            // Tải dữ liệu từ API theo tham số URL
            taiDuLieuBanDo(initialFilters);
        }

        // Gắn sự kiện bộ lọc (Submit Form & Reset)
        ganSuKienBoLoc();

        // Gắn sự kiện định vị GPS
        ganSuKienDinhVi();

        // Gắn sự kiện chuyển đổi chế độ xem (Split, Grid, Map)
        ganSuKienCheDoXem();

        // Gắn sự kiện quick filter chips 1 chạm
        ganSuKienQuickChips();

        // Gắn sự kiện sắp xếp kết quả
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

        // Mặc định: CartoDB Voyager
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
        if (urlParams.get('dang_mo_cua')) filters.dang_mo_cua = urlParams.get('dang_mo_cua');
        return filters;
    }

    // 4. Đồng bộ giá trị vào Form bộ lọc
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
        if (form.elements['dang_mo_cua']) {
            form.elements['dang_mo_cua'].checked = (filters.dang_mo_cua === '1');
        }

        locDanhMucTheoLoai();
        dongBoQuickChips(filters.loai, filters.co_ban_ve, filters.dang_mo_cua);
    }

    // Helper đồng bộ trạng thái nút Quick Chips
    function dongBoQuickChips(loai, coBanVe, dangMoCua) {
        const chips = document.querySelectorAll('[data-filter-chip]');
        if (!chips.length) return;
        chips.forEach(c => {
            const val = c.getAttribute('data-filter-chip');
            let shouldActive = false;
            if (dangMoCua === '1') {
                shouldActive = (val === 'dang_mo_cua');
            } else if (coBanVe === '1') {
                shouldActive = (val === 'co_ban_ve');
            } else if (loai === 'du_lich') {
                shouldActive = (val === 'du_lich');
            } else if (loai === 'am_thuc') {
                shouldActive = (val === 'am_thuc');
            } else if (!loai && !coBanVe && !dangMoCua) {
                shouldActive = (val === 'all');
            }
            c.classList.toggle('active', shouldActive);
        });
    }

    // 5. Lọc dropdown Danh mục khi chọn Phân loại hình
    function locDanhMucTheoLoai() {
        const form = document.getElementById('live-filter-form');
        if (!form || !form.elements['loai'] || !form.elements['danh_muc_id']) return;

        const loaiDuocChon = form.elements['loai'].value;
        const selectDanhMuc = form.elements['danh_muc_id'];
        const options = selectDanhMuc.querySelectorAll('option');

        options.forEach(opt => {
            if (opt.value === '') {
                opt.hidden = false;
                return;
            }
            const optLoai = opt.getAttribute('data-loai');
            if (!loaiDuocChon || optLoai === loaiDuocChon) {
                opt.hidden = false;
            } else {
                opt.hidden = true;
                if (opt.selected) {
                    selectDanhMuc.value = '';
                }
            }
        });
    }

    // 6. Hàm tải dữ liệu địa điểm từ API
    function taiDuLieuBanDo(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        // Dùng helper getAppUrl để hỗ trợ cả domain root lẫn subfolder
        const apiUrl = getAppUrl('api/locations') + (query ? '?' + query : '');

        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    allLocations = res.data;
                    renderMarkers(allLocations);
                    capNhatDanhSachCards(allLocations);

                    if (allLocations.length > 0) {
                        fitMapToBounds(allLocations);
                    }
                }
            })
            .catch(err => {
                console.error('Lỗi tải dữ liệu địa điểm từ API:', err);
                // Fallback: nếu API lỗi mà có sẵn initial data, dùng lại
                if (window.INITIAL_LOCATIONS) {
                    allLocations = window.INITIAL_LOCATIONS;
                    renderMarkers(allLocations);
                }
            });
    }

    // Hàm căn chỉnh tầm nhìn bản đồ bao quát tất cả điểm
    function fitMapToBounds(locations) {
        if (!map || !locations || locations.length === 0) return;
        const bounds = [];
        locations.forEach(item => {
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

    // 7. Render Custom Markers trên Leaflet
    function renderMarkers(locations) {
        if (!markersLayer || !map) return;
        markersLayer.clearLayers();

        locations.forEach(item => {
            const lat = parseFloat(item.vi_do);
            const lng = parseFloat(item.kinh_do);

            if (isNaN(lat) || isNaN(lng)) return;

            const isFood = item.loai_danh_muc === 'am_thuc';
            const markerClass = isFood ? 'marker-food' : 'marker-travel';
            const iconHtml = isFood 
                ? '<i class="bi bi-egg-fried"></i>' 
                : '<i class="bi bi-compass"></i>';

            const customIcon = L.divIcon({
                className: 'custom-leaflet-marker-wrapper',
                html: `<div class="custom-marker ${markerClass}" id="marker-point-${item.id}">${iconHtml}</div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            // Tính khoảng cách nếu đã có vị trí người dùng
            let distanceHtml = '';
            if (userCoords) {
                const dist = tinhKhoangCachKm(userCoords.lat, userCoords.lng, lat, lng);
                distanceHtml = `<div class="distance-pill mt-1"><i class="bi bi-geo"></i> Cách bạn ${dist.toFixed(1)} km</div>`;
            }

            const giaFormatted = formatTienTe(item.gia_thap_nhat) + ' - ' + formatTienTe(item.gia_cao_nhat);
            const badgeTag = isFood ? 'badge-tag-food text-coral' : 'badge-tag-travel text-teal';
            const loaiLabel = isFood ? 'Ẩm thực' : 'Du lịch';
            const safeTitle = escapeHtml(item.ten_dia_diem);
            const safeAddress = escapeHtml(item.dia_chi);
            const safeCategory = escapeHtml(item.ten_danh_muc);
            const safeImage = escapeHtml(formatImageUrl(item.anh_dai_dien));
            const detailUrl = getAppUrl('dia-diem/' + encodeURIComponent(item.duong_dan));

            const popupContent = `
                <div class="popup-card">
                    <img src="${safeImage}" class="popup-img" alt="${safeTitle}">
                    <div class="popup-body">
                        <span class="badge-tag ${badgeTag} mb-1 d-inline-block">${loaiLabel} &bull; ${safeCategory}</span>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">${safeTitle}</h6>
                        <p class="text-muted small mb-1 text-truncate">${safeAddress}</p>
                        <div class="text-danger fw-bold small mb-2">${giaFormatted}</div>
                        ${distanceHtml}
                        <div class="mt-2 pt-2 border-top d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary py-2 px-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-1" onclick="window.startMapRouting(${lat}, ${lng}, '${safeTitle.replace(/'/g, "\\'")}')" title="Dẫn đường OSRM">
                                <i class="bi bi-sign-turn-right-fill"></i><span>Chỉ đường</span>
                            </button>
                            <a href="${detailUrl}" class="btn btn-sm btn-primary flex-grow-1 py-2 fw-semibold text-white d-flex align-items-center justify-content-center gap-1 shadow-sm" style="font-size: 13px; color: #ffffff !important; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border: none; text-decoration: none !important;">
                                <i class="bi bi-geo-alt-fill text-white"></i><span>Chi tiết</span>
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

        const mapPinCount = document.getElementById('map-pin-count');
        if (mapPinCount) {
            mapPinCount.innerText = locations.length;
        }
    }

    // 8. Cập nhật thẻ địa điểm (Cards) đồng bộ trên giao diện
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

            const safeTitle = escapeHtml(item.ten_dia_diem);
            const safeAddress = escapeHtml(item.dia_chi);
            const safeCategory = escapeHtml(item.ten_danh_muc);
            const safeImage = escapeHtml(formatImageUrl(item.anh_dai_dien));
            const detailUrl = getAppUrl('dia-diem/' + encodeURIComponent(item.duong_dan));

            html += `
                <div class="col-md-6 col-sm-12" id="card-location-${item.id}">
                    <div class="location-card-modern" onclick="focusOnMap(${item.id}, ${item.vi_do}, ${item.kinh_do})" style="cursor: pointer;">
                        <div class="location-card-img-wrap">
                            <img src="${safeImage}" class="location-card-img" alt="${safeTitle}" loading="lazy">
                            <span class="card-category-glass ${badgeTag}">
                                ${loaiLabel} &bull; ${safeCategory}
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
                            
                            <h3 class="location-card-title">${safeTitle}</h3>
                            
                            <div class="location-card-address">
                                <i class="bi bi-geo-alt text-danger me-1"></i>${safeAddress}
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
                                <span class="location-card-price">${giaFormatted}</span>
                                <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" onclick="focusOnMap(${item.id}, ${item.vi_do}, ${item.kinh_do})" title="Xem vị trí trên bản đồ">
                                        <i class="bi bi-geo-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" onclick="window.startMapRouting(${item.vi_do}, ${item.kinh_do}, '${safeTitle.replace(/'/g, "\\'")}')" title="Chỉ đường OSRM nội bộ">
                                        <i class="bi bi-sign-turn-right-fill"></i>
                                    </button>
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">
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

    // 9. Tập trung vào bản đồ khi click thẻ (Bidirectional Interaction)
    window.focusOnMap = function(id, lat, lng) {
        if (!map || !markersLayer) return;

        let targetMarker = null;

        // Tìm marker tương ứng
        if (typeof markersLayer.eachLayer === 'function') {
            markersLayer.eachLayer(marker => {
                if (marker.locationId === id) {
                    targetMarker = marker;
                }
            });
        }

        if (targetMarker) {
            if (typeof markersLayer.zoomToShowLayer === 'function') {
                markersLayer.zoomToShowLayer(targetMarker, function() {
                    targetMarker.openPopup();
                });
            } else {
                map.flyTo([lat, lng], 15, { duration: 0.8 });
                targetMarker.openPopup();
            }
        } else {
            map.flyTo([lat, lng], 15, { duration: 0.8 });
        }

        // Đánh dấu viền card
        document.querySelectorAll('.location-card-modern').forEach(el => el.classList.remove('highlighted-card'));
        const targetCard = document.getElementById(`card-location-${id}`);
        if (targetCard) {
            targetCard.querySelector('.location-card-modern')?.classList.add('highlighted-card');
            targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // 10. Hàm thực hiện lọc dữ liệu thống nhất (dùng chung cho cả Form và Quick Chips)
    window.thucHienLocDuLieu = function(overrideParams = null) {
        const filterForm = document.getElementById('live-filter-form');
        let params = {};

        if (overrideParams) {
            params = { ...overrideParams };
        } else if (filterForm) {
            const formData = new FormData(filterForm);
            formData.forEach((val, key) => {
                if (val !== '') params[key] = val;
            });
            // Kiểm tra checkbox co_ban_ve
            if (filterForm.elements['co_ban_ve']) {
                if (filterForm.elements['co_ban_ve'].checked) {
                    params['co_ban_ve'] = '1';
                } else {
                    delete params['co_ban_ve'];
                }
            }
            // Kiểm tra checkbox dang_mo_cua
            if (filterForm.elements['dang_mo_cua']) {
                if (filterForm.elements['dang_mo_cua'].checked) {
                    params['dang_mo_cua'] = '1';
                } else {
                    delete params['dang_mo_cua'];
                }
            }
        }

        // Cập nhật URL trình duyệt
        const newQuery = new URLSearchParams(params).toString();
        if (window.history.pushState) {
            const newUrl = window.location.pathname + (newQuery ? '?' + newQuery : '');
            window.history.pushState({}, '', newUrl);
        }

        // Đồng bộ visual chips
        dongBoQuickChips(params.loai, params.co_ban_ve, params.dang_mo_cua);

        // Gọi API lọc
        taiDuLieuBanDo(params);
    };

    // 11. Gắn sự kiện bộ lọc Form nâng cao
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

        // Xử lý khi submit form
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            window.thucHienLocDuLieu();
        });

        // Xử lý nút Đặt lại bộ lọc
        const btnReset = document.getElementById('btn-reset-filter');
        if (btnReset) {
            btnReset.addEventListener('click', function () {
                filterForm.reset();
                locDanhMucTheoLoai();

                // Đặt lại active chip về "Tất cả"
                document.querySelectorAll('[data-filter-chip]').forEach(c => {
                    c.classList.toggle('active', c.getAttribute('data-filter-chip') === 'all');
                });

                if (window.history.pushState) {
                    window.history.pushState({}, '', window.location.pathname);
                }
                taiDuLieuBanDo({});
            });
        }
    }

    // 12. Gắn sự kiện Quick Filter Chips 1 chạm
    function ganSuKienQuickChips() {
        const chips = document.querySelectorAll('[data-filter-chip]');
        const filterForm = document.getElementById('live-filter-form');
        if (!chips.length) return;

        chips.forEach(chip => {
            chip.addEventListener('click', function () {
                const type = chip.getAttribute('data-filter-chip');
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');

                if (filterForm) {
                    if (type === 'all') {
                        if (filterForm.elements['loai']) filterForm.elements['loai'].value = '';
                        if (filterForm.elements['danh_muc_id']) filterForm.elements['danh_muc_id'].value = '';
                        if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                        if (filterForm.elements['dang_mo_cua']) filterForm.elements['dang_mo_cua'].checked = false;
                    } else if (type === 'du_lich') {
                        if (filterForm.elements['loai']) filterForm.elements['loai'].value = 'du_lich';
                        if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                        if (filterForm.elements['dang_mo_cua']) filterForm.elements['dang_mo_cua'].checked = false;
                    } else if (type === 'am_thuc') {
                        if (filterForm.elements['loai']) filterForm.elements['loai'].value = 'am_thuc';
                        if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = false;
                        if (filterForm.elements['dang_mo_cua']) filterForm.elements['dang_mo_cua'].checked = false;
                    } else if (type === 'co_ban_ve') {
                        if (filterForm.elements['co_ban_ve']) filterForm.elements['co_ban_ve'].checked = true;
                    } else if (type === 'dang_mo_cua') {
                        if (filterForm.elements['dang_mo_cua']) filterForm.elements['dang_mo_cua'].checked = true;
                    }
                    locDanhMucTheoLoai();
                }

                // Thực hiện lọc trực tiếp
                window.thucHienLocDuLieu();
            });
        });
    }

    // 13. Định vị GPS người dùng & Bộ lọc bán kính (Radius Filter)
    function ganSuKienDinhVi() {
        const btnLocate = document.getElementById('btn-locate-me');
        const radiusBar = document.getElementById('radiusFilterBar');
        const radiusChips = document.querySelectorAll('.radius-chip');
        if (!btnLocate) return;

        function applyUserPosition(lat, lng, isDemo = false) {
            userCoords = { lat: lat, lng: lng };

            if (userMarker && map) {
                map.removeLayer(userMarker);
            }

            const userIcon = L.divIcon({
                className: 'custom-leaflet-marker-wrapper',
                html: '<div class="custom-marker marker-user"><i class="bi bi-person-fill"></i></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            userMarker = L.marker([userCoords.lat, userCoords.lng], { icon: userIcon })
                .addTo(map)
                .bindPopup('<strong>Vị trí của bạn</strong>' + (isDemo ? '<br><small class="text-muted">(Demo Quảng trường 2/4)</small>' : ''))
                .openPopup();

            if (radiusBar) {
                radiusBar.classList.remove('d-none');
                radiusBar.classList.add('d-flex');
            }

            updateRadiusCircleAndFilter();
        }

        function updateRadiusCircleAndFilter() {
            if (!userCoords || !map) return;

            if (userRadiusCircle) {
                map.removeLayer(userRadiusCircle);
                userRadiusCircle = null;
            }

            let filtered = [...allLocations];

            if (currentRadius !== 'all') {
                userRadiusCircle = L.circle([userCoords.lat, userCoords.lng], {
                    radius: currentRadius,
                    color: '#0284c7',
                    fillColor: '#38bdf8',
                    fillOpacity: 0.12,
                    weight: 2,
                    dashArray: '5, 5'
                }).addTo(map);

                const radiusKm = currentRadius / 1000;
                filtered = filtered.filter(item => {
                    const dist = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(item.vi_do), parseFloat(item.kinh_do));
                    return dist <= radiusKm;
                });

                filtered.sort((a, b) => {
                    const da = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(a.vi_do), parseFloat(a.kinh_do));
                    const db = tinhKhoangCachKm(userCoords.lat, userCoords.lng, parseFloat(b.vi_do), parseFloat(b.kinh_do));
                    return da - db;
                });

                map.fitBounds(userRadiusCircle.getBounds(), { padding: [30, 30] });
            } else {
                map.flyTo([userCoords.lat, userCoords.lng], 13, { duration: 0.8 });
            }

            const resText = document.getElementById('radiusResultText');
            if (resText) {
                if (currentRadius === 'all') {
                    resText.textContent = `Tất cả ${allLocations.length} địa điểm`;
                } else {
                    resText.textContent = `Tìm thấy ${filtered.length} địa điểm trong ${currentRadius / 1000} km`;
                }
            }

            capNhatDanhSachCards(filtered);
            renderMarkers(filtered);
        }

        btnLocate.addEventListener('click', function () {
            btnLocate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang định vị...';
            btnLocate.disabled = true;

            if (!navigator.geolocation) {
                btnLocate.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> Vị trí của bạn (Demo)';
                btnLocate.disabled = false;
                applyUserPosition(12.2388, 109.1961, true);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    btnLocate.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> Vị trí của bạn';
                    btnLocate.disabled = false;
                    applyUserPosition(position.coords.latitude, position.coords.longitude, false);
                },
                function (err) {
                    btnLocate.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> Vị trí của bạn (Demo)';
                    btnLocate.disabled = false;
                    applyUserPosition(12.2388, 109.1961, true);
                },
                { enableHighAccuracy: true, timeout: 6000 }
            );
        });

        radiusChips.forEach(chip => {
            chip.addEventListener('click', function () {
                radiusChips.forEach(c => {
                    c.classList.remove('btn-primary', 'active');
                    c.classList.add(c.getAttribute('data-radius') === 'all' ? 'btn-outline-secondary' : 'btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                this.classList.add('btn-primary', 'active');

                const val = this.getAttribute('data-radius');
                currentRadius = (val === 'all') ? 'all' : parseInt(val);
                updateRadiusCircleAndFilter();
            });
        });
    }

    // 14. Chuyển đổi chế độ xem (Split / Grid / Map)
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
                    fitMapToBounds(allLocations);
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

    // 15. Gắn sự kiện sắp xếp Client-side
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

    // 16. Bản đồ chi tiết địa điểm đơn lẻ
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
            window.detailMapInstance = detailMap;

            setTimeout(() => {
                detailMap.invalidateSize();
            }, 200);

            const detailIcon = L.divIcon({
                className: 'custom-leaflet-marker-wrapper',
                html: '<div class="custom-marker marker-travel"><i class="bi bi-geo-alt-fill"></i></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });

            L.marker([lat, lng], { icon: detailIcon }).addTo(detailMap)
                .bindPopup(`<strong>${title}</strong>`)
                .openPopup();
        }
    }

    // ========================================================
    // 17. OSRM DẪN ĐƯỜNG NỘI BỘ TRÊN BẢN ĐỒ CHÍNH (IN-APP ROUTING)
    // ========================================================
    let mapRouteLayer = null;
    let activeRoutingState = null;

    window.switchMapRoutingMode = function (mode) {
        if (!activeRoutingState) return;
        activeRoutingState.mode = mode;
        window.startMapRouting(activeRoutingState.destLat, activeRoutingState.destLng, activeRoutingState.destTitle, mode);
    };

    window.startMapRouting = function (destLat, destLng, destTitle, routeMode = 'driving') {
        if (!map) return;

        activeRoutingState = {
            destLat: destLat,
            destLng: destLng,
            destTitle: destTitle,
            mode: routeMode
        };

        let panel = document.getElementById('map-osrm-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'map-osrm-panel';
            panel.className = 'osrm-route-panel shadow-lg';
            const mapWrapper = document.querySelector('.sticky-map-wrapper') || mapElement.parentElement;
            mapWrapper.appendChild(panel);
        }

        const modeBadge = (routeMode === 'foot') ? 'Đi bộ ven biển' : 'Xe máy / Ô tô';

        panel.innerHTML = `
            <div class="osrm-panel-header">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-sign-turn-right-fill text-warning fs-5"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-white text-truncate" style="font-size: 13.5px; max-width: 200px;">${escapeHtml(destTitle)}</h6>
                            <small class="text-white-50" style="font-size: 11px;">Chỉ đường OSRM (${modeBadge})</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light py-0 px-2 rounded-pill fw-bold" onclick="window.closeMapRouting()" title="Đóng lộ trình">✕</button>
                </div>
                <div class="btn-group btn-group-sm w-100 rounded-pill overflow-hidden p-0.5" style="background: rgba(255,255,255,0.2);">
                    <button type="button" class="btn btn-sm rounded-pill py-0.5 fw-semibold ${routeMode === 'driving' ? 'btn-light text-primary shadow-xs' : 'text-white border-0'}" onclick="window.switchMapRoutingMode('driving')">
                        <i class="bi bi-bicycle me-1"></i>Xe máy / Ô tô
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill py-0.5 fw-semibold ${routeMode === 'foot' ? 'btn-light text-success shadow-xs' : 'text-white border-0'}" onclick="window.switchMapRoutingMode('foot')">
                        <i class="bi bi-person-walking me-1"></i>Đi bộ
                    </button>
                </div>
            </div>
            <div class="osrm-panel-body py-4 text-center">
                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                <span class="text-muted small">Đang tính toán lộ trình ${routeMode === 'foot' ? 'đi bộ' : 'đường bộ'}...</span>
            </div>
        `;

        function computeOSRM(startLat, startLng, isFallback = false) {
            const osrmProfile = (routeMode === 'foot') ? 'foot' : 'driving';
            const url = `https://router.project-osrm.org/route/v1/${osrmProfile}/${startLng},${startLat};${destLng},${destLat}?overview=full&geometries=geojson&steps=true`;

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (!data || data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        panel.innerHTML = `
                            <div class="osrm-panel-header d-flex align-items-center justify-content-between">
                                <span class="fw-bold">Lộ trình OSRM</span>
                                <button type="button" class="btn btn-sm btn-light py-0 px-2 rounded-pill fw-bold" onclick="window.closeMapRouting()">✕</button>
                            </div>
                            <div class="p-3 text-center text-danger small">Không tìm thấy lộ trình phù hợp. Vui lòng thử đổi phương tiện.</div>
                        `;
                        return;
                    }

                    const route = data.routes[0];
                    const km = (route.distance / 1000).toFixed(1);
                    let mins = Math.max(1, Math.round(route.duration / 60));
                    if (routeMode === 'foot') {
                        // Tính toán thời gian đi bộ trung bình 4.5km/h (~75m/phút)
                        mins = Math.max(1, Math.round(route.distance / 75));
                    }

                    if (mapRouteLayer) {
                        map.removeLayer(mapRouteLayer);
                    }

                    const latLngs = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    const lineColor = (routeMode === 'foot') ? '#10b981' : '#0284c7';
                    const casing = L.polyline(latLngs, { color: '#091e3a', weight: 7, opacity: 0.85 });
                    const line = L.polyline(latLngs, { color: lineColor, weight: 4.5, opacity: 0.95, dashArray: routeMode === 'foot' ? '6, 6' : null });

                    const startIcon = L.divIcon({
                        className: 'custom-leaflet-marker-wrapper',
                        html: '<div class="custom-marker" style="background:#16a34a; border-color:#ffffff; color:#fff; width:28px; height:28px; font-size:12px; display:flex; align-items:center; justify-content:center; border-radius:50%;"><i class="bi bi-geo-alt-fill"></i></div>',
                        iconSize: [28, 28]
                    });
                    const startMarker = L.marker([startLat, startLng], { icon: startIcon })
                        .bindPopup('<strong>Điểm xuất phát</strong>' + (isFallback ? '<br><small class="text-muted">(Quảng trường 2/4 Demo)</small>' : '<br><small class="text-muted">(Vị trí của bạn)</small>'));

                    mapRouteLayer = L.layerGroup([casing, line, startMarker]).addTo(map);
                    map.fitBounds(casing.getBounds(), { padding: [50, 50] });

                    let stepsHtml = '';
                    if (route.legs && route.legs[0] && route.legs[0].steps) {
                        route.legs[0].steps.forEach((st) => {
                            const m = st.maneuver || {};
                            let icon = '<i class="bi bi-arrow-up"></i>';
                            let desc = '';
                            const name = st.name ? `<strong>${escapeHtml(st.name)}</strong>` : 'đoạn đường';
                            const distStep = st.distance >= 1000 ? (st.distance / 1000).toFixed(1) + ' km' : Math.round(st.distance) + ' m';

                            if (m.type === 'depart') {
                                icon = '<i class="bi bi-play-circle-fill text-success"></i>';
                                desc = 'Bắt đầu di chuyển vào ' + name;
                            } else if (m.type === 'arrive') {
                                icon = '<i class="bi bi-geo-alt-fill text-danger"></i>';
                                desc = 'Đến điểm hẹn: ' + escapeHtml(destTitle);
                            } else if (m.modifier && m.modifier.includes('left')) {
                                icon = '<i class="bi bi-arrow-90deg-left text-primary"></i>';
                                desc = 'Rẽ trái vào ' + name;
                            } else if (m.modifier && m.modifier.includes('right')) {
                                icon = '<i class="bi bi-arrow-90deg-right text-primary"></i>';
                                desc = 'Rẽ phải vào ' + name;
                            } else {
                                icon = '<i class="bi bi-arrow-up text-secondary"></i>';
                                desc = 'Đi thẳng tiếp tục ' + name;
                            }

                            stepsHtml += `
                                <div class="osrm-step-item">
                                    <div class="osrm-step-icon">${icon}</div>
                                    <div class="flex-grow-1">
                                        <div class="text-dark">${desc}</div>
                                        <span class="text-muted" style="font-size: 11px;">(khoảng ${distStep})</span>
                                    </div>
                                </div>
                            `;
                        });
                    }

                    const timeLabel = (routeMode === 'foot') ? `~${mins} phút đi bộ` : `~${mins} phút di chuyển`;

                    panel.innerHTML = `
                        <div class="osrm-panel-header">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-sign-turn-right-fill text-warning fs-5"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-white text-truncate" style="font-size: 13.5px; max-width: 200px;">
                                            ${escapeHtml(destTitle)}
                                        </h6>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-white text-dark rounded-pill fw-bold" style="font-size: 11px;">${km} km</span>
                                            <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size: 11px;">${timeLabel}</span>
                                            ${isFallback ? '<span class="badge bg-light text-secondary rounded-pill" style="font-size: 10px;">Demo</span>' : ''}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light py-0 px-2 rounded-pill fw-bold" onclick="window.closeMapRouting()" title="Đóng lộ trình">✕</button>
                            </div>
                            <div class="btn-group btn-group-sm w-100 rounded-pill overflow-hidden p-0.5" style="background: rgba(255,255,255,0.2);">
                                <button type="button" class="btn btn-sm rounded-pill py-0.5 fw-semibold ${routeMode === 'driving' ? 'btn-light text-primary shadow-xs' : 'text-white border-0'}" onclick="window.switchMapRoutingMode('driving')">
                                    <i class="bi bi-bicycle me-1"></i>Xe máy / Ô tô
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill py-0.5 fw-semibold ${routeMode === 'foot' ? 'btn-light text-success shadow-xs' : 'text-white border-0'}" onclick="window.switchMapRoutingMode('foot')">
                                    <i class="bi bi-person-walking me-1"></i>Đi bộ
                                </button>
                            </div>
                        </div>
                        <div class="osrm-panel-body" id="osrmStepsBody">
                            ${stepsHtml}
                        </div>
                    `;
                })
                .catch(() => {
                    panel.innerHTML = `
                        <div class="osrm-panel-header d-flex align-items-center justify-content-between">
                            <span class="fw-bold">Lỗi OSRM</span>
                            <button type="button" class="btn btn-sm btn-light py-0 px-2 rounded-pill fw-bold" onclick="window.closeMapRouting()">✕</button>
                        </div>
                        <div class="p-3 text-center text-muted small">Không thể kết nối đến máy chủ dẫn đường OSRM.</div>
                    `;
                });
        }

        if (userCoords && userCoords.lat && userCoords.lng) {
            computeOSRM(userCoords.lat, userCoords.lng, false);
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    userCoords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    computeOSRM(pos.coords.latitude, pos.coords.longitude, false);
                },
                () => {
                    computeOSRM(12.2388, 109.1961, true);
                },
                { timeout: 4000 }
            );
        } else {
            computeOSRM(12.2388, 109.1961, true);
        }
    };

    window.closeMapRouting = function () {
        const panel = document.getElementById('map-osrm-panel');
        if (panel) panel.remove();
        activeRoutingState = null;

        if (map && mapRouteLayer) {
            map.removeLayer(mapRouteLayer);
            mapRouteLayer = null;
        }
    };

    // ========================================================
    // 18. LIVE AUTO-SUGGEST SEARCH TỨC THÌ
    // ========================================================
    function initLiveAutoSuggest() {
        const searchInputs = document.querySelectorAll('.live-search-input, input[name="tu_khoa"]');
        if (!searchInputs || searchInputs.length === 0) return;

        searchInputs.forEach(input => {
            let dropdown = null;
            let debounceTimer = null;
            let selectedIndex = -1;

            const wrapper = input.closest('.live-search-wrapper') || input.parentElement;
            if (wrapper && getComputedStyle(wrapper).position === 'static') {
                wrapper.style.position = 'relative';
            }

            function createDropdown() {
                if (!dropdown) {
                    dropdown = document.createElement('div');
                    dropdown.className = 'search-suggest-dropdown shadow-lg';
                    dropdown.style.display = 'none';
                    wrapper.appendChild(dropdown);
                }
                return dropdown;
            }

            function highlightMatch(text, query) {
                if (!query) return escapeHtml(text);
                const regex = new RegExp('(' + query.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + ')', 'gi');
                return escapeHtml(text).replace(regex, '<mark>$1</mark>');
            }

            function renderResults(results, query) {
                selectedIndex = -1;
                const dd = createDropdown();

                if (!results || results.length === 0) {
                    dd.innerHTML = `
                        <div class="p-3 text-center text-muted small">
                            <i class="bi bi-search me-1"></i>Không tìm thấy địa điểm nào khớp với "<strong>${escapeHtml(query)}</strong>"
                        </div>
                    `;
                    dd.style.display = 'block';
                    return;
                }

                let html = '';
                results.forEach((item, idx) => {
                    const openBadge = item.dang_mo_cua 
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0 px-2" style="font-size:10px;">Đang mở</span>'
                        : '<span class="badge bg-secondary-subtle text-secondary rounded-pill py-0 px-2" style="font-size:10px;">Đóng cửa</span>';
                    
                    const ticketBadge = item.co_ban_ve 
                        ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-0 px-2" style="font-size:10px;"><i class="bi bi-ticket-perforated"></i> Vé E-Voucher</span>'
                        : '';

                    const priceText = item.gia_thap_nhat > 0 
                        ? formatTienTe(item.gia_thap_nhat) 
                        : 'Miễn phí';

                    html += `
                        <a href="${item.url}" class="suggest-item" data-index="${idx}">
                            <img src="${item.anh_dai_dien}" class="suggest-thumb" alt="${escapeHtml(item.ten_dia_diem)}" loading="lazy">
                            <div class="suggest-info">
                                <div class="suggest-title">${highlightMatch(item.ten_dia_diem, query)}</div>
                                <div class="suggest-meta">
                                    <span class="badge bg-light text-secondary border py-0 px-1" style="font-size:10px;">${escapeHtml(item.ten_danh_muc)}</span>
                                    ${openBadge}
                                    ${ticketBadge}
                                    <span class="text-danger fw-semibold ms-auto" style="font-size:11.5px;">${priceText}</span>
                                </div>
                            </div>
                        </a>
                    `;
                });

                dd.innerHTML = html;
                dd.style.display = 'block';

                dd.querySelectorAll('.suggest-item').forEach(el => {
                    el.addEventListener('mouseenter', function() {
                        dd.querySelectorAll('.suggest-item').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedIndex = parseInt(this.dataset.index, 10);
                    });
                });
            }

            function hideDropdown() {
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
                selectedIndex = -1;
            }

            function performSearch() {
                const val = input.value.trim();
                if (val.length < 1) {
                    hideDropdown();
                    return;
                }

                const apiUrl = getAppUrl('api/search-suggest?q=' + encodeURIComponent(val));
                fetch(apiUrl)
                    .then(r => r.json())
                    .then(res => {
                        if (res && res.status === 'success') {
                            renderResults(res.data, val);
                        }
                    })
                    .catch(() => {});
            }

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, 220);
            });

            input.addEventListener('focus', function() {
                if (input.value.trim().length >= 1) {
                    performSearch();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (!dropdown || dropdown.style.display === 'none') return;
                const items = dropdown.querySelectorAll('.suggest-item');
                if (items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    items.forEach((item, idx) => {
                        item.classList.toggle('selected', idx === selectedIndex);
                    });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    items.forEach((item, idx) => {
                        item.classList.toggle('selected', idx === selectedIndex);
                    });
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        e.preventDefault();
                        window.location.href = items[selectedIndex].href;
                    }
                } else if (e.key === 'Escape') {
                    hideDropdown();
                }
            });

            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    hideDropdown();
                }
            });
        });
    }

    initLiveAutoSuggest();
});

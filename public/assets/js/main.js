document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');
    if (mapElement) {
        // Tọa độ trung tâm thành phố Nha Trang: [12.2475, 109.1967]
        const map = L.map('map').setView([12.2475, 109.1967], 13);

        // Nguồn bản đồ mở OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Lấy bộ lọc từ URL hiện tại
        const urlParams = new URLSearchParams(window.location.search);
        const apiUrl = (window.APP_URL || '') + '/api/locations?' + urlParams.toString();

        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    res.data.forEach(item => {
                        const lat = parseFloat(item.vi_do);
                        const lng = parseFloat(item.kinh_do);

                        if (!isNaN(lat) && !isNaN(lng)) {
                            const badgeClass = item.loai_danh_muc === 'am_thuc' ? 'bg-warning text-dark' : 'bg-primary text-white';
                            const badgeText = item.loai_danh_muc === 'am_thuc' ? 'Ẩm thực' : 'Du lịch';
                            
                            const popupContent = `
                                <div style="min-width: 180px; max-width: 220px;" class="custom-popup">
                                    <img src="${item.anh_dai_dien}" alt="${item.ten_dia_diem}">
                                    <span class="badge ${badgeClass} mb-1" style="font-size: 11px;">${badgeText}</span>
                                    <h6 class="fw-bold mb-1" style="font-size: 14px;">${item.ten_dia_diem}</h6>
                                    <p class="text-muted small mb-2 text-truncate">${item.dia_chi}</p>
                                    <a href="${(window.APP_URL || '')}/dia-diem/${item.duong_dan}" class="btn btn-sm btn-primary w-100 py-1" style="font-size: 12px;">Xem chi tiết</a>
                                </div>
                            `;

                            L.marker([lat, lng])
                                .addTo(map)
                                .bindPopup(popupContent);
                        }
                    });
                }
            })
            .catch(err => console.error('Lỗi tải dữ liệu bản đồ:', err));
    }

    // Bản đồ chi tiết địa điểm đơn lẻ
    const detailMapEl = document.getElementById('detail-map');
    if (detailMapEl) {
        const lat = parseFloat(detailMapEl.dataset.lat);
        const lng = parseFloat(detailMapEl.dataset.lng);
        const title = detailMapEl.dataset.title;

        if (!isNaN(lat) && !isNaN(lng)) {
            const detailMap = L.map('detail-map').setView([lat, lng], 15);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(detailMap);

            L.marker([lat, lng]).addTo(detailMap)
                .bindPopup(`<strong>${title}</strong>`)
                .openPopup();
        }
    }
});

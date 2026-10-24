<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Định dạng tiền tệ VND: ví dụ 500000 -> "500.000 đ"
     */
    public static function tienTe(float|int $soTien): string
    {
        if ($soTien <= 0) {
            return 'Miễn phí';
        }
        return number_format($soTien, 0, ',', '.') . ' đ';
    }

    /**
     * Định dạng ngày tháng: Y-m-d -> d/m/Y
     */
    public static function ngayThang(string $dateStr): string
    {
        if (empty($dateStr)) {
            return '';
        }
        $timestamp = strtotime($dateStr);
        return date('d/m/Y', $timestamp);
    }

    /**
     * Định dạng giờ: H:i:s -> H:i
     */
    public static function gio(string $timeStr): string
    {
        if (empty($timeStr)) {
            return '';
        }
        $timestamp = strtotime($timeStr);
        return date('H:i', $timestamp);
    }

    /**
     * Render HTML sao đánh giá (1 - 5 sao)
     */
    public static function saoHtml(int|float $soSao): string
    {
        $html = '<span class="text-warning">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $soSao) {
                $html .= '<i class="bi bi-star-fill"></i>';
            } elseif ($i - 0.5 <= $soSao) {
                $html .= '<i class="bi bi-star-half"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        $html .= '</span>';
        return $html;
    }

    /**
     * Render HTML trạng thái mở cửa / đóng cửa theo thời gian thực (Giờ địa phương Nha Trang)
     */
    public static function trangThaiHoatDongHtml(?string $gioMo, ?string $gioDong): string
    {
        if (empty($gioMo) || empty($gioDong)) {
            return '<span class="badge bg-light text-secondary border fw-medium px-2 py-1"><i class="bi bi-clock me-1"></i>Mở cửa đón khách</span>';
        }

        $tz = new \DateTimeZone('Asia/Ho_Chi_Minh');
        $now = new \DateTime('now', $tz);
        $nowMinutes = (int)$now->format('H') * 60 + (int)$now->format('i');

        $openTimeStr = date('H:i', strtotime($gioMo));
        $closeTimeStr = date('H:i', strtotime($gioDong));

        if (($openTimeStr === '00:00' && $closeTimeStr === '23:59') || ($openTimeStr === '00:00' && $closeTimeStr === '00:00')) {
            return '<span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Mở cửa 24/7</span>';
        }

        $openParts = explode(':', $openTimeStr);
        $closeParts = explode(':', $closeTimeStr);
        $openMinutes = (int)($openParts[0] ?? 0) * 60 + (int)($openParts[1] ?? 0);
        $closeMinutes = (int)($closeParts[0] ?? 0) * 60 + (int)($closeParts[1] ?? 0);

        if ($closeMinutes > $openMinutes) {
            $isOpen = ($nowMinutes >= $openMinutes && $nowMinutes <= $closeMinutes);
        } else {
            // Trường hợp mở qua đêm (ví dụ: 17:00 -> 02:00 sáng hôm sau)
            $isOpen = ($nowMinutes >= $openMinutes || $nowMinutes <= $closeMinutes);
        }

        if ($isOpen) {
            return '<span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1"><i class="bi bi-circle-fill me-1 text-success" style="font-size: 8px;"></i>Đang mở cửa • Đóng lúc ' . $closeTimeStr . '</span>';
        }

        return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2 py-1"><i class="bi bi-circle-fill me-1 text-danger" style="font-size: 8px;"></i>Đã đóng cửa • Mở lúc ' . $openTimeStr . '</span>';
    }

    /**
     * Chuẩn hóa đường dẫn hình ảnh (Hỗ trợ URL tuyệt đối, ảnh upload cục bộ và fallback placeholder)
     */
    public static function hinhAnh(?string $path): string
    {
        if (empty($path)) {
            return url('assets/images/placeholder.jpg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url(ltrim($path, '/'));
    }

    /**
     * Alias của hinhAnh(): Chuẩn hóa đường dẫn hình ảnh
     */
    public static function anhUrl(?string $path): string
    {
        return self::hinhAnh($path);
    }

    /**
     * Render HTML thanh phân trang chuẩn Bootstrap 5 với đầy đủ tham số tìm kiếm & lọc
     */
    public static function phanTrang(
        int $total,
        int $perPage,
        int $currentPage,
        string $baseUrl = '',
        array $queryParams = []
    ): string {
        if ($total <= 0) {
            return '';
        }

        $totalPages = (int)ceil($total / $perPage);
        if ($totalPages <= 1) {
            return '
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 px-3 border-top mt-3">
                    <span class="text-muted small">
                        Hiển thị <strong>1</strong> - <strong>' . $total . '</strong> trong tổng số <strong>' . number_format($total) . '</strong> bản ghi
                    </span>
                </div>
            ';
        }

        $currentPage = max(1, min($totalPages, $currentPage));
        $from = ($currentPage - 1) * $perPage + 1;
        $to = min($total, $currentPage * $perPage);

        // Chuẩn bị URL generator
        $buildUrl = function (int $page) use ($baseUrl, $queryParams): string {
            $params = $queryParams;
            $params['page'] = $page;
            // Loại bỏ các param rỗng
            $filteredParams = array_filter($params, fn($val) => ($val !== '' && $val !== null));
            $queryString = http_build_query($filteredParams);
            return url($baseUrl) . ($queryString !== '' ? '?' . $queryString : '');
        };

        // Tính toán các trang cần hiển thị (xử lý dấu ba chấm thông minh)
        $pages = [];
        if ($totalPages <= 7) {
            for ($i = 1; $i <= $totalPages; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($currentPage <= 4) {
                $pages = [1, 2, 3, 4, 5, '...', $totalPages];
            } elseif ($currentPage >= $totalPages - 3) {
                $pages = [1, '...', $totalPages - 4, $totalPages - 3, $totalPages - 2, $totalPages - 1, $totalPages];
            } else {
                $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $totalPages];
            }
        }

        $html = '<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 px-3 border-top mt-3">';
        
        // Đoạn text thông tin bản ghi
        $html .= '<div class="text-muted small">';
        $html .= 'Hiển thị <strong>' . $from . '</strong> - <strong>' . $to . '</strong> trong tổng số <strong>' . number_format($total) . '</strong> bản ghi';
        $html .= '</div>';

        // Danh sách nút phân trang
        $html .= '<nav aria-label="Phân trang dữ liệu">';
        $html .= '<ul class="pagination pagination-sm mb-0">';

        // Nút Trang trước (Previous)
        if ($currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($currentPage - 1)) . '" aria-label="Trước"><i class="bi bi-chevron-left me-1"></i>Trước</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left me-1"></i>Trước</span></li>';
        }

        // Các số trang
        foreach ($pages as $p) {
            if ($p === '...') {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            } elseif ($p === $currentPage) {
                $html .= '<li class="page-item active" aria-current="page"><span class="page-link fw-bold">' . $p . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl((int)$p)) . '">' . $p . '</a></li>';
            }
        }

        // Nút Trang sau (Next)
        if ($currentPage < $totalPages) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($currentPage + 1)) . '" aria-label="Sau">Sau<i class="bi bi-chevron-right ms-1"></i></a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Sau<i class="bi bi-chevron-right ms-1"></i></span></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Chuyển đổi cú pháp Markdown cơ bản của AI sang HTML an toàn
     */
    public static function markdownToHtml(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Chống XSS an toàn
        $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');

        // Tiêu đề Markdown
        $html = preg_replace('/^###\s+(.*$)/m', '<h6 class="fw-bold text-dark mt-2 mb-1">$1</h6>', $html);
        $html = preg_replace('/^####\s+(.*$)/m', '<strong class="d-block text-primary mt-1 mb-1">$1</strong>', $html);

        // Chữ đậm và nghiêng
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/(?<!\*)\*(?!\*)(.*?)\*/', '<em>$1</em>', $html);

        // Phân cách dòng
        $html = preg_replace('/^---$/m', '<hr class="my-2 border-secondary opacity-25">', $html);

        // Trích dẫn blockquote
        $html = preg_replace('/^&gt;\s+(.*$)/m', '<div class="p-2 my-1 rounded bg-light border-start border-3 border-primary text-secondary">$1</div>', $html);

        // Dấu đầu dòng danh sách
        $html = preg_replace('/^\-\s+(.*$)/m', '<div class="d-flex align-items-start gap-1 my-0.5"><span>•</span><span>$1</span></div>', $html);

        // Xuống dòng
        $html = nl2br($html);

        return $html;
    }
}

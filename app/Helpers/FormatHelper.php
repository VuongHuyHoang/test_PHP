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
}


<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\DanhMuc;
use App\Models\DiaDiem;
use App\Models\GoiVe;

class HomeController
{
    private DanhMuc $danhMucModel;
    private DiaDiem $diaDiemModel;
    private GoiVe $goiVeModel;

    public function __construct()
    {
        $this->danhMucModel = new DanhMuc();
        $this->diaDiemModel = new DiaDiem();
        $this->goiVeModel = new GoiVe();
    }

    public function index(): void
    {
        $db = Database::getConnection();

        // 1. Danh sách danh mục
        $danhMucList = $this->danhMucModel->tatCa();

        // 2. 4 Điểm đến Spotlight nổi bật nhất (Ảnh lớn Agoda-style)
        $spotlightSlugs = ['vinwonders-nha-trang', 'i-resort-nha-trang', 'nha-hat-do-nha-trang', 'khu-bao-ton-hon-mun'];
        $inQuery = implode("','", $spotlightSlugs);
        $spotlightList = $db->query("
            SELECT d.*, dm.ten_danh_muc, dm.loai_danh_muc,
                   COALESCE(AVG(dg.so_sao), 5.0) as diem_danh_gia,
                   COUNT(dg.id) as tong_danh_gia
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE d.duong_dan IN ('{$inQuery}') AND d.trang_thai = 'hien_thi'
            GROUP BY d.id
            ORDER BY FIELD(d.duong_dan, 'vinwonders-nha-trang', 'i-resort-nha-trang', 'nha-hat-do-nha-trang', 'khu-bao-ton-hon-mun')
        ")->fetchAll();

        // 3. Hoạt động & Vé tham quan trực tuyến (có bán vé)
        $veOnlineList = $db->query("
            SELECT d.*, dm.ten_danh_muc, dm.loai_danh_muc,
                   gv.id as goi_ve_id, gv.ten_goi_ve, gv.gia_nguoi_lon, gv.gia_tre_em,
                   COALESCE(AVG(dg.so_sao), 4.8) as diem_danh_gia,
                   COUNT(dg.id) as tong_danh_gia
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN goi_ve gv ON gv.id = (SELECT id FROM goi_ve WHERE dia_diem_id = d.id AND trang_thai = 'dang_ban' ORDER BY gia_nguoi_lon ASC LIMIT 1)
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE d.co_ban_ve = 1 AND d.trang_thai = 'hien_thi'
            GROUP BY d.id
            ORDER BY d.luot_xem DESC
            LIMIT 8
        ")->fetchAll();

        // 4. Địa điểm ẩm thực đặc sản tiêu biểu
        $amThucList = $this->diaDiemModel->danhSach(['loai' => 'am_thuc', 'limit' => 8]);

        // 5. Địa điểm du lịch danh lam tiêu biểu
        $duLichList = $this->diaDiemModel->danhSach(['loai' => 'du_lich', 'limit' => 8]);

        // 6. Toàn bộ tọa độ địa điểm cho bản đồ tương tác trang chủ
        $allMapLocations = $this->diaDiemModel->layToaDoMap();

        // 7. Thống kê nhanh toàn hệ thống
        $stats = [
            'totalLocations' => (int)$db->query("SELECT COUNT(*) FROM dia_diem WHERE trang_thai = 'hien_thi'")->fetchColumn(),
            'totalTickets'   => (int)$db->query("SELECT COUNT(*) FROM goi_ve WHERE trang_thai = 'dang_ban'")->fetchColumn(),
            'totalViews'     => (int)$db->query("SELECT COALESCE(SUM(luot_xem), 0) FROM dia_diem")->fetchColumn(),
        ];

        // 8. Widget Thời tiết biển & Thủy triều Nha Trang
        $weather = \App\Helpers\WeatherHelper::layThoiTietNhaTrang();

        view('client.home.index', [
            'title'           => 'Khám Phá Nha Trang - Ẩm Thực & Du Lịch Biển Đảo',
            'danhMucList'     => $danhMucList,
            'spotlightList'   => $spotlightList,
            'veOnlineList'    => $veOnlineList,
            'amThucList'      => $amThucList,
            'duLichList'      => $duLichList,
            'allMapLocations' => $allMapLocations,
            'stats'           => $stats,
            'weather'         => $weather,
        ]);
    }

    /**
     * Trang Giới thiệu tổng quan về Nha Trang (Lịch sử, Khí hậu, Điểm đến, Ẩm thực, Con người)
     */
    public function about(): void
    {
        $weather = \App\Helpers\WeatherHelper::layThoiTietNhaTrang();
        view('client.home.about', [
            'title'   => 'Giới Thiệu Nha Trang - Thiên Đường Biển Đảo & Xứ Trầm Hương',
            'weather' => $weather,
        ]);
    }

    /**
     * Trang Hướng dẫn sử dụng nền tảng Nha Trang Staycation
     */
    public function guide(): void
    {
        view('client.home.guide', [
            'title' => 'Hướng Dẫn Sử Dụng Nền Tảng - Nha Trang Staycation'
        ]);
    }
}

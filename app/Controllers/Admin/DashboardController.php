<?php

namespace App\Controllers\Admin;

use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class DashboardController
{
    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $db = Database::getConnection();

        // 1. Thống kê tổng quan KPI
        $totalLocations = (int)$db->query("SELECT COUNT(*) FROM dia_diem")->fetchColumn();
        $totalOrders    = (int)$db->query("SELECT COUNT(*) FROM don_hang WHERE ngay_xoa IS NULL")->fetchColumn();
        $totalRevenue   = (float)$db->query("SELECT COALESCE(SUM(tong_tien), 0) FROM don_hang WHERE trang_thai_thanh_toan = 'da_thanh_toan' AND ngay_xoa IS NULL")->fetchColumn();
        $totalUsers     = (int)$db->query("SELECT COUNT(*) FROM nguoi_dung")->fetchColumn();

        $todayRevenue = (float)$db->query("
            SELECT COALESCE(SUM(tong_tien), 0) 
            FROM don_hang 
            WHERE trang_thai_thanh_toan = 'da_thanh_toan' AND DATE(ngay_tao) = CURDATE() AND ngay_xoa IS NULL
        ")->fetchColumn();

        $totalTicketsSold = (int)$db->query("
            SELECT COALESCE(SUM(ct.so_luong_nguoi_lon + ct.so_luong_tre_em), 0) 
            FROM chi_tiet_don_hang ct 
            JOIN don_hang dh ON ct.don_hang_id = dh.id 
            WHERE dh.trang_thai_thanh_toan = 'da_thanh_toan' AND dh.ngay_xoa IS NULL
        ")->fetchColumn();

        // 2. Thống kê doanh thu & đơn hàng 7 ngày gần nhất (Dùng cho Chart.js)
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $displayDate = date('d/m', strtotime("-$i days"));
            $chartLabels[] = $displayDate;

            $stmtRev = $db->prepare("
                SELECT COALESCE(SUM(tong_tien), 0) as rev, COUNT(*) as cnt 
                FROM don_hang 
                WHERE DATE(ngay_tao) = :ngay AND trang_thai_thanh_toan = 'da_thanh_toan' AND ngay_xoa IS NULL
            ");
            $stmtRev->execute(['ngay' => $date]);
            $row = $stmtRev->fetch();

            $chartRevenue[] = (float)($row['rev'] ?? 0);
            $chartOrders[] = (int)($row['cnt'] ?? 0);
        }

        // 3. Phân bổ trạng thái đơn hàng (Doughnut Chart)
        $statusCounts = [
            'moi_tao'     => 0,
            'da_xac_nhan' => 0,
            'hoan_thanh'  => 0,
            'da_huy'      => 0,
        ];
        $statusRows = $db->query("
            SELECT trang_thai_don_hang, COUNT(*) as sl 
            FROM don_hang 
            WHERE ngay_xoa IS NULL 
            GROUP BY trang_thai_don_hang
        ")->fetchAll();

        foreach ($statusRows as $sr) {
            if (isset($statusCounts[$sr['trang_thai_don_hang']])) {
                $statusCounts[$sr['trang_thai_don_hang']] = (int)$sr['sl'];
            }
        }

        // 4. 5 đơn hàng mới nhất
        $recentOrders = $db->query("
            SELECT dh.*, nd.ho_ten as ten_tai_khoan 
            FROM don_hang dh 
            JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
            WHERE dh.ngay_xoa IS NULL 
            ORDER BY dh.id DESC 
            LIMIT 5
        ")->fetchAll();

        view('admin.dashboard.index', [
            'title'             => 'Admin Dashboard - Tổng Quan Hệ Thống',
            'totalLocations'    => $totalLocations,
            'totalOrders'       => $totalOrders,
            'totalRevenue'      => $totalRevenue,
            'todayRevenue'      => $todayRevenue,
            'totalTicketsSold'  => $totalTicketsSold,
            'totalUsers'        => $totalUsers,
            'recentOrders'      => $recentOrders,
            'chartLabels'       => json_encode($chartLabels),
            'chartRevenue'      => json_encode($chartRevenue),
            'chartOrders'       => json_encode($chartOrders),
            'chartStatusData'   => json_encode(array_values($statusCounts)),
        ]);
    }

    /**
     * Trang tài liệu hướng dẫn vận hành hệ thống Dashboard Admin
     */
    public function guide(): void
    {
        AuthMiddleware::checkAdmin();

        view('admin.dashboard.guide', [
            'title' => 'Hướng Dẫn Sử Dụng Admin Dashboard',
        ]);
    }
}

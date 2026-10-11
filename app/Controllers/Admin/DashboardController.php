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

        // Thống kê tổng số
        $totalLocations = (int)$db->query("SELECT COUNT(*) FROM dia_diem")->fetchColumn();
        $totalOrders    = (int)$db->query("SELECT COUNT(*) FROM don_hang WHERE ngay_xoa IS NULL")->fetchColumn();
        $totalRevenue   = (float)$db->query("SELECT COALESCE(SUM(tong_tien), 0) FROM don_hang WHERE trang_thai_thanh_toan = 'da_thanh_toan' AND ngay_xoa IS NULL")->fetchColumn();
        $totalUsers     = (int)$db->query("SELECT COUNT(*) FROM nguoi_dung")->fetchColumn();

        // 5 đơn hàng mới nhất
        $recentOrders = $db->query("
            SELECT dh.*, nd.ho_ten as ten_tai_khoan 
            FROM don_hang dh 
            JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
            WHERE dh.ngay_xoa IS NULL 
            ORDER BY dh.id DESC 
            LIMIT 5
        ")->fetchAll();

        view('admin.dashboard.index', [
            'title'          => 'Admin Dashboard - Tổng quan Hệ thống',
            'totalLocations' => $totalLocations,
            'totalOrders'    => $totalOrders,
            'totalRevenue'   => $totalRevenue,
            'totalUsers'     => $totalUsers,
            'recentOrders'   => $recentOrders,
        ]);
    }
}

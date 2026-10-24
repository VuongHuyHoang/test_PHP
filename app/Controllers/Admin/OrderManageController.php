<?php

namespace App\Controllers\Admin;

use App\Config\Database;
use App\Middlewares\AuthMiddleware;
use Exception;
use PDO;

class OrderManageController
{
    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $db = Database::getConnection();
        $tuKhoa = trim($_GET['q'] ?? '');
        $trangThaiDon = trim($_GET['trang_thai_don'] ?? '');
        $trangThaiTt = trim($_GET['trang_thai_tt'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $baseWhere = " WHERE dh.ngay_xoa IS NULL";
        $params = [];

        if (!empty($tuKhoa)) {
            $baseWhere .= " AND (dh.ma_don_hang LIKE :q_ma OR dh.sdt_khach_hang LIKE :q_sdt OR dh.ten_khach_hang LIKE :q_ten OR dh.email_khach_hang LIKE :q_email)";
            $params['q_ma']    = '%' . $tuKhoa . '%';
            $params['q_sdt']   = '%' . $tuKhoa . '%';
            $params['q_ten']   = '%' . $tuKhoa . '%';
            $params['q_email'] = '%' . $tuKhoa . '%';
        }

        if (!empty($trangThaiDon)) {
            $baseWhere .= " AND dh.trang_thai_don_hang = :tt_don";
            $params['tt_don'] = $trangThaiDon;
        }

        if (!empty($trangThaiTt)) {
            $baseWhere .= " AND dh.trang_thai_thanh_toan = :tt_tt";
            $params['tt_tt'] = $trangThaiTt;
        }

        // Đếm tổng số đơn hàng thỏa mãn điều kiện
        $countSql = "
            SELECT COUNT(*)
            FROM don_hang dh
            JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
            {$baseWhere}
        ";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $totalOrders = (int)$countStmt->fetchColumn();

        // Lấy danh sách có phân trang
        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT dh.*, nd.ho_ten as ten_tai_khoan
            FROM don_hang dh
            JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
            {$baseWhere}
            ORDER BY dh.id DESC
            LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        view('admin.orders.index', [
            'title'        => 'Quản Lý Đơn Hàng & Soát Vé E-Voucher',
            'orders'       => $orders,
            'tuKhoa'       => $tuKhoa,
            'trangThaiDon' => $trangThaiDon,
            'trangThaiTt'  => $trangThaiTt,
            'page'         => $page,
            'perPage'      => $perPage,
            'totalOrders'  => $totalOrders,
        ]);
    }

    public function capNhatTrangThai(int $donHangId, string $trangThaiMoi): bool
    {
        AuthMiddleware::checkAdmin();

        $danhSachHopLe = ['moi_tao', 'da_xac_nhan', 'hoan_thanh', 'da_huy'];
        if (!in_array($trangThaiMoi, $danhSachHopLe)) {
            throw new Exception("Trạng thái đơn hàng không hợp lệ.");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE don_hang 
            SET trang_thai_don_hang = :trang_thai 
            WHERE id = :id AND ngay_xoa IS NULL
        ");
        return $stmt->execute([
            'trang_thai' => $trangThaiMoi,
            'id'         => $donHangId
        ]);
    }

    public function updateStatusAction(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/orders'));
            return;
        }

        $donHangId = (int)($_POST['don_hang_id'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? '';

        try {
            $this->capNhatTrangThai($donHangId, $trangThai);
            flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
        } catch (Exception $e) {
            flash('error', 'Lỗi: ' . $e->getMessage());
        }

        redirect(url('admin/orders'));
    }
}


<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DanhGia
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy đánh giá đã duyệt theo địa điểm cho khách xem
     */
    public function layTheoDiaDiem(int $diaDiemId): array
    {
        $stmt = $this->db->prepare("
            SELECT dg.*, nd.ho_ten, nd.anh_dai_dien,
                   EXISTS (
                       SELECT 1 FROM chi_tiet_don_hang ct
                       JOIN don_hang dh ON ct.don_hang_id = dh.id
                       JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id
                       JOIN goi_ve gv ON tk.goi_ve_id = gv.id
                       WHERE dh.nguoi_dung_id = dg.nguoi_dung_id
                         AND gv.dia_diem_id = dg.dia_diem_id
                         AND dh.trang_thai_thanh_toan = 'da_thanh_toan'
                   ) AS da_mua_ve
            FROM danh_gia dg
            JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
            WHERE dg.dia_diem_id = :dia_diem_id AND dg.trang_thai = 'da_duyet'
            ORDER BY dg.id DESC
        ");
        $stmt->execute(['dia_diem_id' => $diaDiemId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách địa điểm và đánh giá của một người dùng cụ thể
     */
    public function layTheoNguoiDung(int $nguoiDungId): array
    {
        $stmt = $this->db->prepare("
            SELECT dg.*, 
                   dd.ten_dia_diem, dd.duong_dan, dd.anh_dai_dien, dd.dia_chi, dd.co_ban_ve,
                   dd.gia_thap_nhat, dd.gia_cao_nhat,
                   dm.ten_danh_muc, dm.loai_danh_muc,
                   EXISTS (
                       SELECT 1 FROM chi_tiet_don_hang ct
                       JOIN don_hang dh ON ct.don_hang_id = dh.id
                       JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id
                       JOIN goi_ve gv ON tk.goi_ve_id = gv.id
                       WHERE dh.nguoi_dung_id = dg.nguoi_dung_id
                         AND gv.dia_diem_id = dg.dia_diem_id
                         AND dh.trang_thai_thanh_toan = 'da_thanh_toan'
                   ) AS da_mua_ve
            FROM danh_gia dg
            JOIN dia_diem dd ON dg.dia_diem_id = dd.id
            JOIN danh_muc dm ON dd.danh_muc_id = dm.id
            WHERE dg.nguoi_dung_id = :nguoi_dung_id
            ORDER BY dg.id DESC
        ");
        $stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
        return $stmt->fetchAll();
    }

    /**
     * Khách hàng thêm mới hoặc cập nhật đánh giá (kèm ảnh thực tế)
     */
    public function themMoi(int $nguoiDungId, int $diaDiemId, int $soSao, string $noiDung, ?string $hinhAnh = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO danh_gia (nguoi_dung_id, dia_diem_id, so_sao, noi_dung, hinh_anh, trang_thai)
            VALUES (:nguoi_dung_id, :dia_diem_id, :so_sao, :noi_dung, :hinh_anh, 'da_duyet')
            ON DUPLICATE KEY UPDATE 
                so_sao = VALUES(so_sao), 
                noi_dung = VALUES(noi_dung), 
                hinh_anh = COALESCE(VALUES(hinh_anh), hinh_anh), 
                trang_thai = 'da_duyet'
        ");
        return $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'dia_diem_id'   => $diaDiemId,
            'so_sao'        => $soSao,
            'noi_dung'      => $noiDung,
            'hinh_anh'      => $hinhAnh
        ]);
    }

    /**
     * Đếm số lượng đánh giá phục vụ Quản trị viên
     */
    public function demAdmin(array $filters = []): int
    {
        $sql = "
            SELECT COUNT(dg.id)
            FROM danh_gia dg
            JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
            JOIN dia_diem dd ON dg.dia_diem_id = dd.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (nd.ho_ten LIKE :tk_user OR nd.email LIKE :tk_email OR dd.ten_dia_diem LIKE :tk_dd OR dg.noi_dung LIKE :tk_nd)";
            $pattern = '%' . $filters['tu_khoa'] . '%';
            $params['tk_user']  = $pattern;
            $params['tk_email'] = $pattern;
            $params['tk_dd']    = $pattern;
            $params['tk_nd']    = $pattern;
        }

        if (!empty($filters['trang_thai'])) {
            $sql .= " AND dg.trang_thai = :trang_thai";
            $params['trang_thai'] = $filters['trang_thai'];
        }

        if (!empty($filters['so_sao'])) {
            $sql .= " AND dg.so_sao = :so_sao";
            $params['so_sao'] = (int)$filters['so_sao'];
        }

        if (!empty($filters['dia_diem_id'])) {
            $sql .= " AND dg.dia_diem_id = :dia_diem_id";
            $params['dia_diem_id'] = (int)$filters['dia_diem_id'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy danh sách đánh giá phục vụ Quản trị viên (có phân trang)
     */
    public function danhSachAdmin(array $filters = [], ?int $page = null, ?int $perPage = null): array
    {
        $sql = "
            SELECT dg.*, 
                   nd.ho_ten, nd.email,
                   dd.ten_dia_diem, dd.duong_dan as dia_diem_slug
            FROM danh_gia dg
            JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
            JOIN dia_diem dd ON dg.dia_diem_id = dd.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (nd.ho_ten LIKE :tk_user OR nd.email LIKE :tk_email OR dd.ten_dia_diem LIKE :tk_dd OR dg.noi_dung LIKE :tk_nd)";
            $pattern = '%' . $filters['tu_khoa'] . '%';
            $params['tk_user']  = $pattern;
            $params['tk_email'] = $pattern;
            $params['tk_dd']    = $pattern;
            $params['tk_nd']    = $pattern;
        }

        if (!empty($filters['trang_thai'])) {
            $sql .= " AND dg.trang_thai = :trang_thai";
            $params['trang_thai'] = $filters['trang_thai'];
        }

        if (!empty($filters['so_sao'])) {
            $sql .= " AND dg.so_sao = :so_sao";
            $params['so_sao'] = (int)$filters['so_sao'];
        }

        if (!empty($filters['dia_diem_id'])) {
            $sql .= " AND dg.dia_diem_id = :dia_diem_id";
            $params['dia_diem_id'] = (int)$filters['dia_diem_id'];
        }

        $sql .= " ORDER BY dg.id DESC";

        if ($page !== null && $perPage !== null && $perPage > 0) {
            $page = max(1, $page);
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái duyệt/ẩn đánh giá
     */
    public function capNhatTrangThai(int $id, string $trangThai): bool
    {
        $stmt = $this->db->prepare("UPDATE danh_gia SET trang_thai = :trang_thai WHERE id = :id");
        return $stmt->execute([
            'trang_thai' => $trangThai,
            'id'         => $id
        ]);
    }

    /**
     * Xóa đánh giá vĩnh viễn
     */
    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM danh_gia WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Thống kê đánh giá
     */
    public function thongKe(): array
    {
        $total = (int)$this->db->query("SELECT COUNT(*) FROM danh_gia")->fetchColumn();
        $approved = (int)$this->db->query("SELECT COUNT(*) FROM danh_gia WHERE trang_thai = 'da_duyet'")->fetchColumn();
        $hidden = (int)$this->db->query("SELECT COUNT(*) FROM danh_gia WHERE trang_thai = 'da_an'")->fetchColumn();
        $avgRating = (float)$this->db->query("SELECT COALESCE(AVG(so_sao), 0) FROM danh_gia WHERE trang_thai = 'da_duyet'")->fetchColumn();

        return [
            'total'     => $total,
            'approved'  => $approved,
            'hidden'    => $hidden,
            'avgRating' => round($avgRating, 1),
        ];
    }
}

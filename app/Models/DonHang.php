<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DonHang
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function timTheoMa(string $maDon): ?array
    {
        $stmt = $this->db->prepare("
            SELECT dh.*, nd.ho_ten as ten_tai_khoan
            FROM don_hang dh
            JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
            WHERE dh.ma_don_hang = :ma_don AND dh.ngay_xoa IS NULL
            LIMIT 1
        ");
        $stmt->execute(['ma_don' => $maDon]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function chiTiet(int $donHangId): array
    {
        $stmt = $this->db->prepare("
            SELECT ct.*, gv.ten_goi_ve, dd.ten_dia_diem, dd.dia_chi
            FROM chi_tiet_don_hang ct
            JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE ct.don_hang_id = :don_hang_id
        ");
        $stmt->execute(['don_hang_id' => $donHangId]);
        return $stmt->fetchAll();
    }

    public function theoNguoiDung(int $nguoiDungId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM don_hang
            WHERE nguoi_dung_id = :nguoi_dung_id AND ngay_xoa IS NULL
            ORDER BY id DESC
        ");
        $stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
        return $stmt->fetchAll();
    }
}


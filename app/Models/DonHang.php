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

    /**
     * Lấy danh sách đơn vé của người dùng kèm tóm tắt địa điểm & ngày tham quan
     */
    public function theoNguoiDungKemChiTiet(int $nguoiDungId): array
    {
        $sql = "
            SELECT dh.*,
                   (SELECT dd.ten_dia_diem 
                    FROM chi_tiet_don_hang ct 
                    JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id 
                    JOIN goi_ve gv ON tk.goi_ve_id = gv.id 
                    JOIN dia_diem dd ON gv.dia_diem_id = dd.id 
                    WHERE ct.don_hang_id = dh.id LIMIT 1) as ten_dia_diem,
                   (SELECT dd.anh_dai_dien 
                    FROM chi_tiet_don_hang ct 
                    JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id 
                    JOIN goi_ve gv ON tk.goi_ve_id = gv.id 
                    JOIN dia_diem dd ON gv.dia_diem_id = dd.id 
                    WHERE ct.don_hang_id = dh.id LIMIT 1) as anh_dia_diem,
                   (SELECT dd.duong_dan 
                    FROM chi_tiet_don_hang ct 
                    JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id 
                    JOIN goi_ve gv ON tk.goi_ve_id = gv.id 
                    JOIN dia_diem dd ON gv.dia_diem_id = dd.id 
                    WHERE ct.don_hang_id = dh.id LIMIT 1) as dia_diem_slug,
                   (SELECT gv.ten_goi_ve 
                    FROM chi_tiet_don_hang ct 
                    JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id 
                    JOIN goi_ve gv ON tk.goi_ve_id = gv.id 
                    WHERE ct.don_hang_id = dh.id LIMIT 1) as ten_goi_ve,
                   (SELECT ct.ngay_tham_quan 
                    FROM chi_tiet_don_hang ct 
                    WHERE ct.don_hang_id = dh.id LIMIT 1) as ngay_tham_quan,
                   (SELECT SUM(ct.so_luong_nguoi_lon + ct.so_luong_tre_em) 
                    FROM chi_tiet_don_hang ct 
                    WHERE ct.don_hang_id = dh.id) as tong_so_ve
            FROM don_hang dh
            WHERE dh.nguoi_dung_id = :uid AND dh.ngay_xoa IS NULL
            ORDER BY dh.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $nguoiDungId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch sử giao dịch thanh toán của người dùng
     */
    public function layGiaoDichNguoiDung(int $nguoiDungId): array
    {
        $sql = "
            SELECT gd.*, dh.ma_don_hang, dh.tong_tien as tong_tien_don
            FROM giao_dich_thanh_toan gd
            JOIN don_hang dh ON gd.don_hang_id = dh.id
            WHERE dh.nguoi_dung_id = :uid AND dh.ngay_xoa IS NULL
            ORDER BY gd.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $nguoiDungId]);
        return $stmt->fetchAll();
    }
}


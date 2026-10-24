<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class YeuThich
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Kiểm tra người dùng đã yêu thích địa điểm chưa
     */
    public function kiemTra(int $nguoiDungId, int $diaDiemId): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM yeu_thich WHERE nguoi_dung_id = :nguoi_dung_id AND dia_diem_id = :dia_diem_id");
        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'dia_diem_id'   => $diaDiemId,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Bật/Tắt trạng thái yêu thích (Toggle)
     * @return bool True nếu vừa thêm vào yêu thích, False nếu vừa hủy yêu thích
     */
    public function toggle(int $nguoiDungId, int $diaDiemId): bool
    {
        if ($this->kiemTra($nguoiDungId, $diaDiemId)) {
            $stmt = $this->db->prepare("DELETE FROM yeu_thich WHERE nguoi_dung_id = :nguoi_dung_id AND dia_diem_id = :dia_diem_id");
            $stmt->execute([
                'nguoi_dung_id' => $nguoiDungId,
                'dia_diem_id'   => $diaDiemId,
            ]);
            return false;
        } else {
            $stmt = $this->db->prepare("INSERT INTO yeu_thich (nguoi_dung_id, dia_diem_id) VALUES (:nguoi_dung_id, :dia_diem_id)");
            $stmt->execute([
                'nguoi_dung_id' => $nguoiDungId,
                'dia_diem_id'   => $diaDiemId,
            ]);
            return true;
        }
    }

    /**
     * Lấy danh sách ID địa điểm yêu thích của người dùng
     */
    public function layDanhSachId(int $nguoiDungId): array
    {
        $stmt = $this->db->prepare("SELECT dia_diem_id FROM yeu_thich WHERE nguoi_dung_id = :nguoi_dung_id");
        $stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Lấy danh sách đầy đủ địa điểm người dùng đã yêu thích kèm thông tin danh mục, đánh giá
     */
    public function danhSachTheoNguoiDung(int $nguoiDungId): array
    {
        $sql = "
            SELECT d.*, dm.ten_danh_muc, dm.duong_dan as danh_muc_slug, dm.loai_danh_muc,
                   yt.ngay_tao as ngay_yeu_thich,
                   COALESCE(AVG(dg.so_sao), 0) as diem_danh_gia,
                   COUNT(dg.id) as tong_danh_gia
            FROM yeu_thich yt
            JOIN dia_diem d ON yt.dia_diem_id = d.id
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE yt.nguoi_dung_id = :nguoi_dung_id AND d.trang_thai = 'hien_thi'
            GROUP BY d.id
            ORDER BY yt.ngay_tao DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số lượng địa điểm yêu thích của người dùng
     */
    public function demTheoNguoiDung(int $nguoiDungId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM yeu_thich WHERE nguoi_dung_id = :nguoi_dung_id");
        $stmt->execute(['nguoi_dung_id' => $nguoiDungId]);
        return (int)$stmt->fetchColumn();
    }
}


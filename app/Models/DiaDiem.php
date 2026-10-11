<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DiaDiem
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy danh sách địa điểm kèm bộ lọc
     */
    public function danhSach(array $filters = []): array
    {
        $sql = "
            SELECT d.*, dm.ten_danh_muc, dm.duong_dan as danh_muc_slug, dm.loai_danh_muc,
                   COALESCE(AVG(dg.so_sao), 0) as diem_danh_gia,
                   COUNT(dg.id) as tong_danh_gia
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE d.trang_thai = 'hien_thi'
        ";
        $params = [];

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        if (!empty($filters['danh_muc_id'])) {
            $sql .= " AND d.danh_muc_id = :danh_muc_id";
            $params['danh_muc_id'] = $filters['danh_muc_id'];
        }

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (d.ten_dia_diem LIKE :tu_khoa OR d.dia_chi LIKE :tu_khoa)";
            $params['tu_khoa'] = '%' . $filters['tu_khoa'] . '%';
        }

        if (isset($filters['co_ban_ve']) && $filters['co_ban_ve'] !== '') {
            $sql .= " AND d.co_ban_ve = :co_ban_ve";
            $params['co_ban_ve'] = (int)$filters['co_ban_ve'];
        }

        $sql .= " GROUP BY d.id ORDER BY d.luot_xem DESC, d.id DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy dữ liệu nhẹ cho Leaflet Map Markers
     */
    public function layToaDoMap(array $filters = []): array
    {
        $sql = "
            SELECT d.id, d.ten_dia_diem, d.duong_dan, d.dia_chi, d.vi_do, d.kinh_do,
                   d.gia_thap_nhat, d.gia_cao_nhat, d.anh_dai_dien, d.co_ban_ve,
                   dm.ten_danh_muc, dm.loai_danh_muc
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            WHERE d.trang_thai = 'hien_thi'
        ";
        $params = [];

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        if (!empty($filters['danh_muc_id'])) {
            $sql .= " AND d.danh_muc_id = :danh_muc_id";
            $params['danh_muc_id'] = $filters['danh_muc_id'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin chi tiết địa điểm theo slug
     */
    public function chiTietTheoSlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, dm.ten_danh_muc, dm.duong_dan as danh_muc_slug, dm.loai_danh_muc,
                   COALESCE(AVG(dg.so_sao), 0) as diem_danh_gia,
                   COUNT(dg.id) as tong_danh_gia
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE d.duong_dan = :slug AND d.trang_thai = 'hien_thi'
            GROUP BY d.id
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Tăng số lượt xem của địa điểm
     */
    public function tangLuotXem(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE dia_diem SET luot_xem = luot_xem + 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}

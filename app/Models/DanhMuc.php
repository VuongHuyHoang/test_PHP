<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DanhMuc
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function tatCa(): array
    {
        $stmt = $this->db->query("SELECT * FROM danh_muc ORDER BY loai_danh_muc ASC, id ASC");
        return $stmt->fetchAll();
    }

    public function theoLoai(string $loai): array
    {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE loai_danh_muc = :loai ORDER BY id ASC");
        $stmt->execute(['loai' => $loai]);
        return $stmt->fetchAll();
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function timTheoSlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE duong_dan = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lấy danh sách danh mục phục vụ Quản trị viên (kèm đếm số địa điểm trực thuộc & phân trang)
     */
    public function danhSachAdmin(array $filters = [], ?int $page = null, ?int $perPage = null): array
    {
        $sql = "
            SELECT dm.*, 
                   COUNT(dd.id) as so_dia_diem,
                   COALESCE(SUM(dd.luot_xem), 0) as tong_luot_xem
            FROM danh_muc dm
            LEFT JOIN dia_diem dd ON dm.id = dd.danh_muc_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (dm.ten_danh_muc LIKE :tk OR dm.duong_dan LIKE :tk)";
            $params['tk'] = '%' . $filters['tu_khoa'] . '%';
        }

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        $sql .= " GROUP BY dm.id ORDER BY dm.loai_danh_muc ASC, dm.id DESC";

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
     * Đếm tổng số danh mục thỏa mãn bộ lọc
     */
    public function demAdmin(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM danh_muc dm WHERE 1=1";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (dm.ten_danh_muc LIKE :tk OR dm.duong_dan LIKE :tk)";
            $params['tk'] = '%' . $filters['tu_khoa'] . '%';
        }

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Đếm số lượng địa điểm gắn với danh mục
     */
    public function demDiaDiemThuocDanhMuc(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM dia_diem WHERE danh_muc_id = :id");
        $stmt->execute(['id' => $id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Kiểm tra trùng slug đường dẫn
     */
    public function kiemTraSlugTonTai(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM danh_muc WHERE duong_dan = :slug";
        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Thêm mới danh mục
     */
    public function themMoi(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO danh_muc (ten_danh_muc, duong_dan, loai_danh_muc, bieu_tuong)
            VALUES (:ten_danh_muc, :duong_dan, :loai_danh_muc, :bieu_tuong)
        ");
        $stmt->execute([
            'ten_danh_muc'  => $data['ten_danh_muc'],
            'duong_dan'     => $data['duong_dan'],
            'loai_danh_muc' => $data['loai_danh_muc'],
            'bieu_tuong'    => $data['bieu_tuong'] ?? 'bi-tag',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Cập nhật danh mục
     */
    public function capNhat(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE danh_muc 
            SET ten_danh_muc = :ten_danh_muc,
                duong_dan = :duong_dan,
                loai_danh_muc = :loai_danh_muc,
                bieu_tuong = :bieu_tuong
            WHERE id = :id
        ");
        return $stmt->execute([
            'ten_danh_muc'  => $data['ten_danh_muc'],
            'duong_dan'     => $data['duong_dan'],
            'loai_danh_muc' => $data['loai_danh_muc'],
            'bieu_tuong'    => $data['bieu_tuong'] ?? 'bi-tag',
            'id'            => $id,
        ]);
    }

    /**
     * Xóa danh mục
     */
    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM danh_muc WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Thống kê tóm tắt danh mục
     */
    public function thongKe(): array
    {
        $total = (int)$this->db->query("SELECT COUNT(*) FROM danh_muc")->fetchColumn();
        $foodCount = (int)$this->db->query("SELECT COUNT(*) FROM danh_muc WHERE loai_danh_muc = 'am_thuc'")->fetchColumn();
        $travelCount = (int)$this->db->query("SELECT COUNT(*) FROM danh_muc WHERE loai_danh_muc = 'du_lich'")->fetchColumn();

        return [
            'total'       => $total,
            'foodCount'   => $foodCount,
            'travelCount' => $travelCount,
        ];
    }
}

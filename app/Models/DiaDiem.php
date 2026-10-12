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

        if (!empty($filters['muc_gia'])) {
            if ($filters['muc_gia'] === 'duoi_50k') {
                $sql .= " AND d.gia_thap_nhat <= 50000";
            } elseif ($filters['muc_gia'] === '50k_200k') {
                $sql .= " AND (d.gia_cao_nhat >= 50000 AND d.gia_thap_nhat <= 200000)";
            } elseif ($filters['muc_gia'] === 'tren_200k') {
                $sql .= " AND d.gia_cao_nhat >= 200000";
            }
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
     * Lấy dữ liệu đầy đủ cho Leaflet Map Markers & Live Client Filter
     */
    public function layToaDoMap(array $filters = []): array
    {
        $sql = "
            SELECT d.id, d.ten_dia_diem, d.duong_dan, d.dia_chi, d.vi_do, d.kinh_do,
                   d.gia_thap_nhat, d.gia_cao_nhat, d.anh_dai_dien, d.co_ban_ve,
                   dm.ten_danh_muc, dm.loai_danh_muc,
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

        if (!empty($filters['muc_gia'])) {
            if ($filters['muc_gia'] === 'duoi_50k') {
                $sql .= " AND d.gia_thap_nhat <= 50000";
            } elseif ($filters['muc_gia'] === '50k_200k') {
                $sql .= " AND (d.gia_cao_nhat >= 50000 AND d.gia_thap_nhat <= 200000)";
            } elseif ($filters['muc_gia'] === 'tren_200k') {
                $sql .= " AND d.gia_cao_nhat >= 200000";
            }
        }

        $sql .= " GROUP BY d.id ORDER BY d.luot_xem DESC, d.id DESC";

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

    /**
     * Tìm địa điểm theo ID
     */
    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, dm.ten_danh_muc, dm.loai_danh_muc 
            FROM dia_diem d 
            JOIN danh_muc dm ON d.danh_muc_id = dm.id 
            WHERE d.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Danh sách toàn bộ địa điểm cho quản trị viên (có tìm kiếm & lọc)
     */
    public function danhSachAdmin(array $filters = []): array
    {
        $sql = "
            SELECT d.*, dm.ten_danh_muc, dm.loai_danh_muc,
                   (SELECT COUNT(*) FROM goi_ve gv WHERE gv.dia_diem_id = d.id) as so_goi_ve
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= " AND (d.ten_dia_diem LIKE :tu_khoa OR d.dia_chi LIKE :tu_khoa)";
            $params['tu_khoa'] = '%' . $filters['tu_khoa'] . '%';
        }

        if (!empty($filters['danh_muc_id'])) {
            $sql .= " AND d.danh_muc_id = :danh_muc_id";
            $params['danh_muc_id'] = (int)$filters['danh_muc_id'];
        }

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        $sql .= " ORDER BY d.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Thêm địa điểm mới
     */
    public function them(array $data): int
    {
        $sql = "
            INSERT INTO dia_diem (
                danh_muc_id, ten_dia_diem, duong_dan, dia_chi, 
                vi_do, kinh_do, gia_thap_nhat, gia_cao_nhat, 
                gio_mo_cua, gio_dong_cua, anh_dai_dien, 
                mo_ta_ngan, noi_dung_chi_tiet, co_ban_ve, trang_thai
            ) VALUES (
                :danh_muc_id, :ten_dia_diem, :duong_dan, :dia_chi, 
                :vi_do, :kinh_do, :gia_thap_nhat, :gia_cao_nhat, 
                :gio_mo_cua, :gio_dong_cua, :anh_dai_dien, 
                :mo_ta_ngan, :noi_dung_chi_tiet, :co_ban_ve, :trang_thai
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'danh_muc_id'       => $data['danh_muc_id'],
            'ten_dia_diem'      => $data['ten_dia_diem'],
            'duong_dan'         => $data['duong_dan'],
            'dia_chi'           => $data['dia_chi'],
            'vi_do'             => $data['vi_do'],
            'kinh_do'           => $data['kinh_do'],
            'gia_thap_nhat'     => $data['gia_thap_nhat'] ?? 0,
            'gia_cao_nhat'      => $data['gia_cao_nhat'] ?? 0,
            'gio_mo_cua'        => !empty($data['gio_mo_cua']) ? $data['gio_mo_cua'] : null,
            'gio_dong_cua'      => !empty($data['gio_dong_cua']) ? $data['gio_dong_cua'] : null,
            'anh_dai_dien'      => $data['anh_dai_dien'],
            'mo_ta_ngan'        => $data['mo_ta_ngan'] ?? null,
            'noi_dung_chi_tiet' => $data['noi_dung_chi_tiet'] ?? null,
            'co_ban_ve'         => !empty($data['co_ban_ve']) ? 1 : 0,
            'trang_thai'        => $data['trang_thai'] ?? 'hien_thi'
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Cập nhật thông tin địa điểm
     */
    public function capNhat(int $id, array $data): bool
    {
        $sql = "
            UPDATE dia_diem SET
                danh_muc_id       = :danh_muc_id,
                ten_dia_diem      = :ten_dia_diem,
                duong_dan         = :duong_dan,
                dia_chi           = :dia_chi,
                vi_do             = :vi_do,
                kinh_do           = :kinh_do,
                gia_thap_nhat     = :gia_thap_nhat,
                gia_cao_nhat      = :gia_cao_nhat,
                gio_mo_cua        = :gio_mo_cua,
                gio_dong_cua      = :gio_dong_cua,
                anh_dai_dien      = :anh_dai_dien,
                mo_ta_ngan        = :mo_ta_ngan,
                noi_dung_chi_tiet = :noi_dung_chi_tiet,
                co_ban_ve         = :co_ban_ve,
                trang_thai        = :trang_thai
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'                => $id,
            'danh_muc_id'       => $data['danh_muc_id'],
            'ten_dia_diem'      => $data['ten_dia_diem'],
            'duong_dan'         => $data['duong_dan'],
            'dia_chi'           => $data['dia_chi'],
            'vi_do'             => $data['vi_do'],
            'kinh_do'           => $data['kinh_do'],
            'gia_thap_nhat'     => $data['gia_thap_nhat'] ?? 0,
            'gia_cao_nhat'      => $data['gia_cao_nhat'] ?? 0,
            'gio_mo_cua'        => !empty($data['gio_mo_cua']) ? $data['gio_mo_cua'] : null,
            'gio_dong_cua'      => !empty($data['gio_dong_cua']) ? $data['gio_dong_cua'] : null,
            'anh_dai_dien'      => $data['anh_dai_dien'],
            'mo_ta_ngan'        => $data['mo_ta_ngan'] ?? null,
            'noi_dung_chi_tiet' => $data['noi_dung_chi_tiet'] ?? null,
            'co_ban_ve'         => !empty($data['co_ban_ve']) ? 1 : 0,
            'trang_thai'        => $data['trang_thai'] ?? 'hien_thi'
        ]);
    }

    /**
     * Xóa địa điểm (hoặc ẩn nếu có dữ liệu liên kết)
     */
    public function xoa(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM dia_diem WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            // Nếu có ràng buộc khóa ngoại (RESTRICT), chuyển sang ẩn
            $stmt = $this->db->prepare("UPDATE dia_diem SET trang_thai = 'an' WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        }
    }

    /**
     * Lấy danh sách ảnh gallery của địa điểm
     */
    public function layHinhAnh(int $diaDiemId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM hinh_anh_dia_diem WHERE dia_diem_id = :dia_diem_id ORDER BY id ASC");
        $stmt->execute(['dia_diem_id' => $diaDiemId]);
        return $stmt->fetchAll();
    }

    /**
     * Thêm ảnh vào gallery
     */
    public function themHinhAnh(int $diaDiemId, string $duongDanAnh): int
    {
        $stmt = $this->db->prepare("INSERT INTO hinh_anh_dia_diem (dia_diem_id, duong_dan_anh) VALUES (:dia_diem_id, :duong_dan_anh)");
        $stmt->execute([
            'dia_diem_id'   => $diaDiemId,
            'duong_dan_anh' => $duongDanAnh
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Xóa 1 ảnh trong gallery
     */
    public function xoaHinhAnh(int $hinhAnhId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM hinh_anh_dia_diem WHERE id = :id");
        return $stmt->execute(['id' => $hinhAnhId]);
    }
}


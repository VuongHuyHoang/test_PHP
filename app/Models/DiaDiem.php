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
     * Tối ưu hóa điều kiện tìm kiếm sử dụng MySQL FULLTEXT kết hợp Prefix Match
     */
    private function buildSearchClause(string $tuKhoa, array &$params): string
    {
        $tuKhoa = trim($tuKhoa);
        if (empty($tuKhoa)) return "";

        $searchPattern = '%' . $tuKhoa . '%';
        $startPattern = $tuKhoa . '%';
        $params['tu_khoa_name'] = $searchPattern;
        $params['tu_khoa_addr'] = $searchPattern;
        $params['tu_khoa_cat']  = $searchPattern;
        $params['tu_khoa_order_start'] = $startPattern;
        $params['tu_khoa_order_like']  = $searchPattern;

        // Chuẩn bị từ khóa cho Full-Text Search (loại bỏ ký tự đặc biệt)
        $cleanKw = preg_replace('/[+\-><()~*"@]+/', ' ', $tuKhoa);
        $words = array_filter(explode(' ', trim($cleanKw)));

        if (count($words) > 0 && mb_strlen($tuKhoa, 'UTF-8') >= 3) {
            $ftWords = [];
            foreach ($words as $w) {
                if (mb_strlen($w, 'UTF-8') >= 2) {
                    $ftWords[] = '+' . $w . '*';
                }
            }
            if (!empty($ftWords)) {
                $params['ft_query'] = implode(' ', $ftWords);
                return " AND (
                    MATCH(d.ten_dia_diem, d.dia_chi, d.mo_ta_ngan) AGAINST(:ft_query IN BOOLEAN MODE)
                    OR d.ten_dia_diem LIKE :tu_khoa_name
                    OR d.dia_chi LIKE :tu_khoa_addr
                    OR dm.ten_danh_muc LIKE :tu_khoa_cat
                )";
            }
        }

        return " AND (
            d.ten_dia_diem LIKE :tu_khoa_name
            OR d.dia_chi LIKE :tu_khoa_addr
            OR dm.ten_danh_muc LIKE :tu_khoa_cat
        )";
    }

    /**
     * Tìm kiếm gợi ý nhanh tức thì (Live Auto-Suggest)
     */
    public function timKiemGoiY(string $tuKhoa, int $limit = 6): array
    {
        $tuKhoa = trim($tuKhoa);
        if (empty($tuKhoa)) {
            return [];
        }

        $params = [];
        $searchClause = $this->buildSearchClause($tuKhoa, $params);
        $currentTime = date('H:i:s');
        $params['now_t1'] = $currentTime;
        $params['now_t2'] = $currentTime;
        $params['now_t3'] = $currentTime;
        $params['now_t4'] = $currentTime;

        $sql = "
            SELECT d.id, d.ten_dia_diem, d.duong_dan, d.dia_chi, d.anh_dai_dien,
                   d.gia_thap_nhat, d.gia_cao_nhat, d.co_ban_ve, d.gio_mo_cua, d.gio_dong_cua,
                   dm.ten_danh_muc, dm.loai_danh_muc,
                   COALESCE(AVG(dg.so_sao), 0) as diem_danh_gia,
                   (
                       (d.gio_mo_cua IS NULL OR d.gio_dong_cua IS NULL)
                       OR (d.gio_mo_cua <= d.gio_dong_cua AND :now_t1 >= d.gio_mo_cua AND :now_t2 <= d.gio_dong_cua)
                       OR (d.gio_mo_cua > d.gio_dong_cua AND (:now_t3 >= d.gio_mo_cua OR :now_t4 <= d.gio_dong_cua))
                   ) as dang_mo_cua
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON d.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE d.trang_thai = 'hien_thi'
            {$searchClause}
            GROUP BY d.id
            ORDER BY (CASE WHEN d.ten_dia_diem LIKE :tu_khoa_order_start THEN 1 WHEN d.ten_dia_diem LIKE :tu_khoa_order_like THEN 2 ELSE 3 END), d.luot_xem DESC
            LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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

        if (!empty($filters['exclude_id'])) {
            $sql .= " AND d.id != :exclude_id";
            $params['exclude_id'] = (int)$filters['exclude_id'];
        }

        if (!empty($filters['tu_khoa'])) {
            $sql .= $this->buildSearchClause($filters['tu_khoa'], $params);
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

        if (!empty($filters['dang_mo_cua'])) {
            $currentTime = date('H:i:s');
            $sql .= " AND (
                (d.gio_mo_cua IS NULL OR d.gio_dong_cua IS NULL)
                OR (d.gio_mo_cua <= d.gio_dong_cua AND :now_t1 >= d.gio_mo_cua AND :now_t2 <= d.gio_dong_cua)
                OR (d.gio_mo_cua > d.gio_dong_cua AND (:now_t3 >= d.gio_mo_cua OR :now_t4 <= d.gio_dong_cua))
            )";
            $params['now_t1'] = $currentTime;
            $params['now_t2'] = $currentTime;
            $params['now_t3'] = $currentTime;
            $params['now_t4'] = $currentTime;
        }

        if (!empty($filters['tu_khoa'])) {
            $sql .= " GROUP BY d.id ORDER BY (CASE WHEN d.ten_dia_diem LIKE :tu_khoa_order_start THEN 1 WHEN d.ten_dia_diem LIKE :tu_khoa_order_like THEN 2 ELSE 3 END), d.luot_xem DESC, d.id DESC";
        } else {
            $sql .= " GROUP BY d.id ORDER BY d.luot_xem DESC, d.id DESC";
        }

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
                   d.gia_thap_nhat, d.gia_cao_nhat, d.anh_dai_dien, d.co_ban_ve, d.luot_xem,
                   d.gio_mo_cua, d.gio_dong_cua,
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

        if (!empty($filters['exclude_id'])) {
            $sql .= " AND d.id != :exclude_id";
            $params['exclude_id'] = (int)$filters['exclude_id'];
        }

        if (!empty($filters['tu_khoa'])) {
            $sql .= $this->buildSearchClause($filters['tu_khoa'], $params);
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

        if (!empty($filters['dang_mo_cua'])) {
            $currentTime = date('H:i:s');
            $sql .= " AND (
                (d.gio_mo_cua IS NULL OR d.gio_dong_cua IS NULL)
                OR (d.gio_mo_cua <= d.gio_dong_cua AND :now_t1 >= d.gio_mo_cua AND :now_t2 <= d.gio_dong_cua)
                OR (d.gio_mo_cua > d.gio_dong_cua AND (:now_t3 >= d.gio_mo_cua OR :now_t4 <= d.gio_dong_cua))
            )";
            $params['now_t1'] = $currentTime;
            $params['now_t2'] = $currentTime;
            $params['now_t3'] = $currentTime;
            $params['now_t4'] = $currentTime;
        }

        if (!empty($filters['tu_khoa'])) {
            $sql .= " GROUP BY d.id ORDER BY (CASE WHEN d.ten_dia_diem LIKE :tu_khoa_order_start THEN 1 WHEN d.ten_dia_diem LIKE :tu_khoa_order_like THEN 2 ELSE 3 END), d.luot_xem DESC, d.id DESC";
        } else {
            $sql .= " GROUP BY d.id ORDER BY d.luot_xem DESC, d.id DESC";
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
     * Đếm tổng số địa điểm thỏa mãn bộ lọc cho quản trị viên
     */
    public function demAdmin(array $filters = []): int
    {
        $sql = "
            SELECT COUNT(DISTINCT d.id)
            FROM dia_diem d
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['tu_khoa'])) {
            $sql .= $this->buildSearchClause($filters['tu_khoa'], $params);
        }

        if (!empty($filters['danh_muc_id'])) {
            $sql .= " AND d.danh_muc_id = :danh_muc_id";
            $params['danh_muc_id'] = (int)$filters['danh_muc_id'];
        }

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        if (isset($filters['co_ban_ve']) && $filters['co_ban_ve'] !== '') {
            $sql .= " AND d.co_ban_ve = :co_ban_ve";
            $params['co_ban_ve'] = (int)$filters['co_ban_ve'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Danh sách địa điểm cho quản trị viên (có tìm kiếm, lọc & phân trang)
     */
    public function danhSachAdmin(array $filters = [], ?int $page = null, ?int $perPage = null): array
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
            $sql .= $this->buildSearchClause($filters['tu_khoa'], $params);
        }

        if (!empty($filters['danh_muc_id'])) {
            $sql .= " AND d.danh_muc_id = :danh_muc_id";
            $params['danh_muc_id'] = (int)$filters['danh_muc_id'];
        }

        if (!empty($filters['loai'])) {
            $sql .= " AND dm.loai_danh_muc = :loai";
            $params['loai'] = $filters['loai'];
        }

        if (isset($filters['co_ban_ve']) && $filters['co_ban_ve'] !== '') {
            $sql .= " AND d.co_ban_ve = :co_ban_ve";
            $params['co_ban_ve'] = (int)$filters['co_ban_ve'];
        }

        $sql .= " ORDER BY d.id DESC";

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


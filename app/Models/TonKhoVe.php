<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class TonKhoVe
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function layTheoGoiVeVaNgay(int $goiVeId, string $ngay): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM ton_kho_ve 
            WHERE goi_ve_id = :goi_ve_id AND ngay_su_dung = :ngay 
            LIMIT 1
        ");
        $stmt->execute([
            'goi_ve_id' => $goiVeId,
            'ngay'      => $ngay
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lấy tồn kho theo gói vé và ngày; nếu chưa có thì tự động khởi tạo theo hạn mức mặc định
     */
    public function layHoacTaoTheoGoiVeVaNgay(int $goiVeId, string $ngay, int $defaultQty = 500): ?array
    {
        $hienTai = $this->layTheoGoiVeVaNgay($goiVeId, $ngay);
        if ($hienTai) {
            return $hienTai;
        }

        // Chỉ tự sinh tồn kho cho ngày hôm nay hoặc tương lai
        if ($ngay < date('Y-m-d')) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO ton_kho_ve (goi_ve_id, ngay_su_dung, tong_so_ve, so_ve_con_lai)
                VALUES (:goi_ve_id, :ngay, :tong_so_ve, :so_ve_con_lai)
            ");
            $stmt->execute([
                'goi_ve_id'      => $goiVeId,
                'ngay'           => $ngay,
                'tong_so_ve'     => $defaultQty,
                'so_ve_con_lai'  => $defaultQty
            ]);
            return $this->layTheoGoiVeVaNgay($goiVeId, $ngay);
        } catch (\PDOException $e) {
            // Trường hợp race condition (đã được tạo bởi request song song)
            return $this->layTheoGoiVeVaNgay($goiVeId, $ngay);
        }
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT tk.*, gv.ten_goi_ve, gv.gia_nguoi_lon, gv.gia_tre_em, gv.dia_diem_id, dd.ten_dia_diem
            FROM ton_kho_ve tk
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE tk.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Đếm tổng số bản ghi tồn kho theo ngày
     */
    public function demDanhSach(?string $ngay = null): int
    {
        $sql = "
            SELECT COUNT(tk.id)
            FROM ton_kho_ve tk
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($ngay)) {
            $sql .= " AND tk.ngay_su_dung = :ngay";
            $params['ngay'] = $ngay;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy danh sách tồn kho vé theo ngày có phân trang cho Admin
     */
    public function layDanhSachPhanTrang(int $page = 1, int $perPage = 10, ?string $ngay = null): array
    {
        $sql = "
            SELECT tk.*, gv.ten_goi_ve, gv.gia_nguoi_lon, gv.gia_tre_em, dd.ten_dia_diem
            FROM ton_kho_ve tk
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($ngay)) {
            $sql .= " AND tk.ngay_su_dung = :ngay";
            $params['ngay'] = $ngay;
        }

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql .= " ORDER BY tk.ngay_su_dung DESC, dd.ten_dia_diem ASC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách tồn kho vé theo ngày cho Admin (Backward-compatible)
     */
    public function layDanhSach(int $limit = 50, ?string $ngay = null): array
    {
        $sql = "
            SELECT tk.*, gv.ten_goi_ve, gv.gia_nguoi_lon, gv.gia_tre_em, dd.ten_dia_diem
            FROM ton_kho_ve tk
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($ngay)) {
            $sql .= " AND tk.ngay_su_dung = :ngay";
            $params['ngay'] = $ngay;
        }

        $sql .= " ORDER BY tk.ngay_su_dung DESC, dd.ten_dia_diem ASC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Thiết lập hoặc điều chỉnh hạn mức vé cho 1 ngày
     */
    public function thietLapTonKho(int $goiVeId, string $ngaySuDung, int $tongSoVe): bool
    {
        $hienTai = $this->layTheoGoiVeVaNgay($goiVeId, $ngaySuDung);
        if ($hienTai) {
            // Đã có bản ghi: Cập nhật tổng vé và tính lại số vé còn lại
            $daBan = (int)$hienTai['tong_so_ve'] - (int)$hienTai['so_ve_con_lai'];
            $soVeConLai = max(0, $tongSoVe - $daBan);

            $stmt = $this->db->prepare("
                UPDATE ton_kho_ve 
                SET tong_so_ve = :tong_so_ve, so_ve_con_lai = :so_ve_con_lai 
                WHERE id = :id
            ");
            return $stmt->execute([
                'tong_so_ve'    => $tongSoVe,
                'so_ve_con_lai' => $soVeConLai,
                'id'            => $hienTai['id']
            ]);
        }

        // Tạo mới
        $stmt = $this->db->prepare("
            INSERT INTO ton_kho_ve (goi_ve_id, ngay_su_dung, tong_so_ve, so_ve_con_lai)
            VALUES (:goi_ve_id, :ngay_su_dung, :tong_so_ve, :so_ve_con_lai)
        ");
        return $stmt->execute([
            'goi_ve_id'     => $goiVeId,
            'ngay_su_dung'  => $ngaySuDung,
            'tong_so_ve'    => $tongSoVe,
            'so_ve_con_lai' => $tongSoVe
        ]);
    }
}


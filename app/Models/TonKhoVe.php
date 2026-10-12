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
}


<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class GoiVe
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function layTheoDiaDiem(int $diaDiemId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM goi_ve 
            WHERE dia_diem_id = :dia_diem_id AND trang_thai = 'dang_ban'
            ORDER BY gia_nguoi_lon ASC
        ");
        $stmt->execute(['dia_diem_id' => $diaDiemId]);
        return $stmt->fetchAll();
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM goi_ve WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

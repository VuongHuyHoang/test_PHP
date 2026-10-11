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

    public function timTheoSlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE duong_dan = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DanhGia
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function layTheoDiaDiem(int $diaDiemId): array
    {
        $stmt = $this->db->prepare("
            SELECT dg.*, nd.ho_ten, nd.anh_dai_dien
            FROM danh_gia dg
            JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id
            WHERE dg.dia_diem_id = :dia_diem_id AND dg.trang_thai = 'da_duyet'
            ORDER BY dg.id DESC
        ");
        $stmt->execute(['dia_diem_id' => $diaDiemId]);
        return $stmt->fetchAll();
    }

    public function themMoi(int $nguoiDungId, int $diaDiemId, int $soSao, string $noiDung): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO danh_gia (nguoi_dung_id, dia_diem_id, so_sao, noi_dung, trang_thai)
            VALUES (:nguoi_dung_id, :dia_diem_id, :so_sao, :noi_dung, 'da_duyet')
            ON DUPLICATE KEY UPDATE so_sao = VALUES(so_sao), noi_dung = VALUES(noi_dung)
        ");
        return $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'dia_diem_id'   => $diaDiemId,
            'so_sao'        => $soSao,
            'noi_dung'      => $noiDung
        ]);
    }
}

<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class NguoiDung
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function timTheoEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, ho_ten, email, so_dien_thoai, anh_dai_dien, vai_tro, trang_thai, ngay_tao FROM nguoi_dung WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function taoMoi(string $hoTen, string $email, string $matKhau, ?string $sdt = null, string $vaiTro = 'khach_hang'): int
    {
        $hash = password_hash($matKhau, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, vai_tro, trang_thai)
            VALUES (:ho_ten, :email, :mat_khau, :sdt, :vai_tro, 'hoat_dong')
        ");
        $stmt->execute([
            'ho_ten'   => $hoTen,
            'email'    => $email,
            'mat_khau' => $hash,
            'sdt'      => $sdt,
            'vai_tro'  => $vaiTro
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function capNhatThongTin(int $id, string $hoTen, ?string $sdt): bool
    {
        $stmt = $this->db->prepare("UPDATE nguoi_dung SET ho_ten = :ho_ten, so_dien_thoai = :sdt WHERE id = :id");
        return $stmt->execute([
            'id'     => $id,
            'ho_ten' => $hoTen,
            'sdt'    => $sdt
        ]);
    }

    public function layMatKhau(int $id): ?string
    {
        $stmt = $this->db->prepare("SELECT mat_khau FROM nguoi_dung WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $row['mat_khau'] : null;
    }

    public function doiMatKhau(int $id, string $matKhauMoi): bool
    {
        $hash = password_hash($matKhauMoi, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE nguoi_dung SET mat_khau = :mat_khau WHERE id = :id");
        return $stmt->execute([
            'id'       => $id,
            'mat_khau' => $hash
        ]);
    }
}


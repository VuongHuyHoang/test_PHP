<?php

namespace App\Services;

use App\Config\Database;
use PDO;
use Exception;

class BookingService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function datVe(
        int $nguoiDungId,
        int $tonKhoId,
        int $soLuongNguoiLon,
        int $soLuongTreEm,
        float $giaNguoiLon,
        float $giaTreEm,
        string $ngayThamQuan,
        array $thongTinKhach
    ): string {
        $tongSoLuong = $soLuongNguoiLon + $soLuongTreEm;
        if ($tongSoLuong <= 0) {
            throw new Exception("Số lượng vé phải lớn hơn 0.");
        }

        $this->db->beginTransaction();
        try {
            // 1. Trừ tồn kho an toàn (Atomic Update trực tiếp trên CSDL)
            $stmtStock = $this->db->prepare("
                UPDATE ton_kho_ve 
                SET so_ve_con_lai = so_ve_con_lai - :so_luong_tru 
                WHERE id = :ton_kho_id AND so_ve_con_lai >= :so_luong_check
            ");
            $stmtStock->execute([
                'so_luong_tru'   => $tongSoLuong,
                'so_luong_check' => $tongSoLuong,
                'ton_kho_id'     => $tonKhoId
            ]);

            if ($stmtStock->rowCount() === 0) {
                $this->db->rollBack();
                throw new Exception("Ngày được chọn đã hết vé hoặc không đủ số lượng vé yêu cầu.");
            }

            // 2. Tính tổng tiền phía máy chủ (Không tin tưởng dữ liệu client gửi)
            $tongTien = ($soLuongNguoiLon * $giaNguoiLon) + ($soLuongTreEm * $giaTreEm);
            $maDonHang = 'NT' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));

            // 3. Tạo bản ghi đơn hàng
            $stmtOrder = $this->db->prepare("
                INSERT INTO don_hang (
                    ma_don_hang, nguoi_dung_id, ten_khach_hang, email_khach_hang, 
                    sdt_khach_hang, tong_tien, phuong_thuc_thanh_toan, 
                    trang_thai_thanh_toan, trang_thai_don_hang
                ) VALUES (
                    :ma_don, :nguoi_dung_id, :ten, :email, 
                    :sdt, :tong_tien, :phuong_thuc, 
                    'cho_thanh_toan', 'moi_tao'
                )
            ");
            $stmtOrder->execute([
                'ma_don'        => $maDonHang,
                'nguoi_dung_id' => $nguoiDungId,
                'ten'           => $thongTinKhach['ten'],
                'email'         => $thongTinKhach['email'],
                'sdt'           => $thongTinKhach['sdt'],
                'tong_tien'     => $tongTien,
                'phuong_thuc'   => $thongTinKhach['phuong_thuc']
            ]);
            $donHangId = (int)$this->db->lastInsertId();

            // 4. Tạo chi tiết đơn hàng (thanh_tien tự sinh trong DB qua STORED GENERATED)
            $stmtItem = $this->db->prepare("
                INSERT INTO chi_tiet_don_hang (
                    don_hang_id, ton_kho_ve_id, ngay_tham_quan, 
                    so_luong_nguoi_lon, don_gia_nguoi_lon, 
                    so_luong_tre_em, don_gia_tre_em
                ) VALUES (
                    :don_hang_id, :ton_kho_id, :ngay_tham_quan, 
                    :sl_nl, :gia_nl, :sl_te, :gia_te
                )
            ");
            $stmtItem->execute([
                'don_hang_id'    => $donHangId,
                'ton_kho_id'     => $tonKhoId,
                'ngay_tham_quan' => $ngayThamQuan,
                'sl_nl'          => $soLuongNguoiLon,
                'gia_nl'         => $giaNguoiLon,
                'sl_te'          => $soLuongTreEm,
                'gia_te'         => $giaTreEm
            ]);

            $this->db->commit();
            return $maDonHang;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

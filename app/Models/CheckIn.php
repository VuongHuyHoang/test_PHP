<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use Exception;

class CheckIn
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Kiểm tra người dùng đã check-in địa điểm này hôm nay chưa
     */
    public function kiemTraDaCheckInHomNay(int $userId, int $diaDiemId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM check_in_dia_diem 
            WHERE nguoi_dung_id = :user_id 
              AND dia_diem_id = :dia_diem_id 
              AND DATE(ngay_check_in) = CURDATE()
        ");
        $stmt->execute([
            'user_id'     => $userId,
            'dia_diem_id' => $diaDiemId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Thực hiện check-in định vị GPS và cộng điểm tích lũy
     */
    public function thucHienCheckIn(
        int $userId,
        int $diaDiemId,
        ?float $viDo,
        ?float $kinhDo,
        ?int $khoangCachM,
        int $diemThuong = 50,
        ?string $ghiChu = null
    ): array {
        if ($this->kiemTraDaCheckInHomNay($userId, $diaDiemId)) {
            return [
                'success' => false,
                'message' => 'Bạn đã check-in địa điểm này hôm nay rồi! Mỗi ngày chỉ nhận điểm 1 lần cho từng địa điểm.'
            ];
        }

        try {
            $this->db->beginTransaction();

            // 1. Thêm bản ghi check-in
            $stmtInsert = $this->db->prepare("
                INSERT INTO check_in_dia_diem (
                    nguoi_dung_id, dia_diem_id, vi_do_checkin, kinh_do_checkin, 
                    khoang_cach_m, diem_thuong, ghi_chu
                ) VALUES (
                    :uid, :loc_id, :lat, :lng, 
                    :dist, :points, :note
                )
            ");
            $stmtInsert->execute([
                'uid'    => $userId,
                'loc_id' => $diaDiemId,
                'lat'    => $viDo,
                'lng'    => $kinhDo,
                'dist'   => $khoangCachM,
                'points' => $diemThuong,
                'note'   => $ghiChu
            ]);
            $checkinId = (int)$this->db->lastInsertId();

            // 2. Cập nhật điểm tích lũy vào tài khoản người dùng
            $stmtUser = $this->db->prepare("
                UPDATE nguoi_dung 
                SET diem_tich_luy = diem_tich_luy + :pts 
                WHERE id = :uid
            ");
            $stmtUser->execute([
                'pts' => $diemThuong,
                'uid' => $userId
            ]);

            // Lấy tổng điểm mới
            $stmtTotal = $this->db->prepare("SELECT diem_tich_luy FROM nguoi_dung WHERE id = :uid");
            $stmtTotal->execute(['uid' => $userId]);
            $totalPoints = (int)$stmtTotal->fetchColumn();

            $this->db->commit();

            return [
                'success'          => true,
                'checkin_id'       => $checkinId,
                'diem_nhan'        => $diemThuong,
                'tong_diem_moi'    => $totalPoints,
                'message'          => "Check-in thành công! Bạn nhận được +{$diemThuong} điểm thưởng vào Hộ Chiếu Du Lịch."
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return [
                'success' => false,
                'message' => 'Lỗi xử lý check-in: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách lịch sử check-in của người dùng
     */
    public function layLichSuUser(int $userId, int $limit = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, d.ten_dia_diem, d.duong_dan as dia_diem_slug, d.anh_dai_dien, d.dia_chi,
                   dm.ten_danh_muc, dm.loai_danh_muc
            FROM check_in_dia_diem c
            JOIN dia_diem d ON c.dia_diem_id = d.id
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            WHERE c.nguoi_dung_id = :uid
            ORDER BY c.ngay_check_in DESC
            LIMIT " . (int)$limit
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy số liệu thống kê Hộ Chiếu Du Lịch & Tính toán Huy Hiệu (Gamification)
     */
    public function layThongKePassport(int $userId): array
    {
        // 1. Lấy thông tin điểm và số lượt check-in
        $stmtUser = $this->db->prepare("SELECT diem_tich_luy FROM nguoi_dung WHERE id = :uid");
        $stmtUser->execute(['uid' => $userId]);
        $diemTichLuy = (int)$stmtUser->fetchColumn();

        $stmtStats = $this->db->prepare("
            SELECT COUNT(*) as tong_checkin,
                   COUNT(DISTINCT dia_diem_id) as so_diem_khac_nhau
            FROM check_in_dia_diem
            WHERE nguoi_dung_id = :uid
        ");
        $stmtStats->execute(['uid' => $userId]);
        $stats = $stmtStats->fetch() ?: ['tong_checkin' => 0, 'so_diem_khac_nhau' => 0];

        $tongCheckin = (int)$stats['tong_checkin'];
        $soDiem = (int)$stats['so_diem_khac_nhau'];

        // 2. Tính toán Hạng danh hiệu (Level / Title)
        if ($tongCheckin >= 10) {
            $danhHieu = 'Đại Sứ Du Lịch Nha Trang';
            $hang = 'Kim Cương';
            $badgeColor = '#06b6d4';
            $nextLevelText = 'Bạn đã đạt cấp độ Hộ chiếu cao nhất!';
            $progressPercent = 100;
        } elseif ($tongCheckin >= 5) {
            $danhHieu = 'Thổ Địa Phố Biển';
            $hang = 'Vàng';
            $badgeColor = '#eab308';
            $nextLevelText = 'Cần thêm ' . (10 - $tongCheckin) . ' lần check-in để lên Đại Sứ Kim Cương';
            $progressPercent = min(100, round(($tongCheckin / 10) * 100));
        } elseif ($tongCheckin >= 2) {
            $danhHieu = 'Du Khách Năng Động';
            $hang = 'Bạc';
            $badgeColor = '#94a3b8';
            $nextLevelText = 'Cần thêm ' . (5 - $tongCheckin) . ' lần check-in để lên Thổ Địa Vàng';
            $progressPercent = min(100, round(($tongCheckin / 5) * 100));
        } else {
            $danhHieu = 'Tân Binh Khám Phá';
            $hang = 'Đồng';
            $badgeColor = '#d97706';
            $nextLevelText = 'Cần thêm ' . (2 - $tongCheckin) . ' lần check-in để lên Du Khách Bạc';
            $progressPercent = min(100, round(($tongCheckin / 2) * 100));
        }

        // 3. Kiểm tra danh mục các địa điểm đã check-in để mở khóa Huy Hiệu
        $stmtCat = $this->db->prepare("
            SELECT DISTINCT dm.loai_danh_muc, dm.duong_dan, d.ten_dia_diem
            FROM check_in_dia_diem c
            JOIN dia_diem d ON c.dia_diem_id = d.id
            JOIN danh_muc dm ON d.danh_muc_id = dm.id
            WHERE c.nguoi_dung_id = :uid
        ");
        $stmtCat->execute(['uid' => $userId]);
        $checkedInPlaces = $stmtCat->fetchAll();

        $hasFood = false;
        $hasTravel = false;
        $hasHeritage = false;
        $hasIsland = false;

        foreach ($checkedInPlaces as $cp) {
            if ($cp['loai_danh_muc'] === 'am_thuc') $hasFood = true;
            if ($cp['loai_danh_muc'] === 'du_lich') $hasTravel = true;
            
            $placeName = mb_strtolower($cp['ten_dia_diem'], 'UTF-8');
            if (str_contains($placeName, 'tháp bà') || str_contains($placeName, 'chùa') || str_contains($placeName, 'nhà thờ') || str_contains($placeName, 'bảo tàng')) {
                $hasHeritage = true;
            }
            if (str_contains($placeName, 'đảo') || str_contains($placeName, 'vịnh') || str_contains($placeName, 'bãi') || str_contains($placeName, 'mun') || str_contains($placeName, 'hải dương')) {
                $hasIsland = true;
            }
        }

        $huyHieuList = [
            [
                'id'          => 'first_checkin',
                'ten'         => 'Dấu Chân Đầu Tiên',
                'mo_ta'       => 'Thực hiện chuyến check-in đầu tiên tại Nha Trang',
                'icon'        => 'bi-geo-alt-fill',
                'da_mo_khoa'  => $tongCheckin >= 1,
                'yeu_cau'     => '1 lượt check-in',
                'mau'         => '#0284c7'
            ],
            [
                'id'          => 'ocean_explorer',
                'ten'         => 'Nhà Thám Hiểm Biển Đảo',
                'mo_ta'       => 'Khám phá các vịnh đảo hoặc bãi biển thơ mộng của Nha Trang',
                'icon'        => 'bi-water',
                'da_mo_khoa'  => $hasIsland,
                'yeu_cau'     => 'Check-in bãi biển / đảo / viện hải dương',
                'mau'         => '#06b6d4'
            ],
            [
                'id'          => 'heritage_master',
                'ten'         => 'Chinh Phục Di Tích',
                'mo_ta'       => 'Ghé thăm các danh thắng văn hóa lịch sử lâu đời',
                'icon'        => 'bi-bank',
                'da_mo_khoa'  => $hasHeritage,
                'yeu_cau'     => 'Check-in di tích lịch sử / tâm linh',
                'mau'         => '#8b5cf6'
            ],
            [
                'id'          => 'food_lover',
                'ten'         => 'Vua Ẩm Thực Phố Biển',
                'mo_ta'       => 'Thưởng thức và check-in tại các quán ẩm thực đặc sản',
                'icon'        => 'bi-egg-fried',
                'da_mo_khoa'  => $hasFood,
                'yeu_cau'     => 'Check-in điểm ẩm thực / đặc sản',
                'mau'         => '#ef4444'
            ],
            [
                'id'          => 'travel_pro',
                'ten'         => 'Nhà Thám Hiểm Chuyên Nghiệp',
                'mo_ta'       => 'Check-in trên 5 địa điểm tham quan khác nhau',
                'icon'        => 'bi-award-fill',
                'da_mo_khoa'  => $soDiem >= 5,
                'yeu_cau'     => '5 địa điểm khác nhau',
                'mau'         => '#eab308'
            ]
        ];

        return [
            'diem_tich_luy'       => $diemTichLuy,
            'tong_checkin'        => $tongCheckin,
            'so_diem_khac_nhau'   => $soDiem,
            'danh_hieu'           => $danhHieu,
            'hang'                => $hang,
            'badge_color'         => $badgeColor,
            'next_level_text'     => $nextLevelText,
            'progress_percent'    => $progressPercent,
            'huy_hieu_list'       => $huyHieuList
        ];
    }
}


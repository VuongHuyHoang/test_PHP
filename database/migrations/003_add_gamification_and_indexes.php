<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

try {
    $pdo = Database::getConnection();
    echo "Connected to database successfully.\n";

    // 1. Thêm cột diem_tich_luy vào bảng nguoi_dung nếu chưa có
    $checkCol = $pdo->query("SHOW COLUMNS FROM `nguoi_dung` LIKE 'diem_tich_luy'")->fetch();
    if (!$checkCol) {
        $pdo->exec("ALTER TABLE `nguoi_dung` ADD COLUMN `diem_tich_luy` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `vai_tro`");
        echo "Added column diem_tich_luy to nguoi_dung.\n";
    } else {
        echo "Column diem_tich_luy already exists.\n";
    }

    // 2. Tạo bảng check_in_dia_diem (Gamification & Passport)
    $sqlCheckIn = "
    CREATE TABLE IF NOT EXISTS `check_in_dia_diem` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `nguoi_dung_id` BIGINT UNSIGNED NOT NULL,
        `dia_diem_id` BIGINT UNSIGNED NOT NULL,
        `vi_do_checkin` DECIMAL(10, 8) DEFAULT NULL,
        `kinh_do_checkin` DECIMAL(11, 8) DEFAULT NULL,
        `khoang_cach_m` INT UNSIGNED DEFAULT NULL,
        `diem_thuong` INT UNSIGNED NOT NULL DEFAULT 50,
        `ghi_chu` VARCHAR(255) DEFAULT NULL,
        `ngay_check_in` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_checkin_user` (`nguoi_dung_id`),
        KEY `idx_checkin_location` (`dia_diem_id`),
        KEY `idx_checkin_date` (`ngay_check_in`),
        CONSTRAINT `fk_checkin_user` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_checkin_location` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sqlCheckIn);
    echo "Table check_in_dia_diem created or already exists.\n";

    // 3. Thêm FULLTEXT index trên bảng dia_diem (ten_dia_diem, dia_chi, mo_ta_ngan)
    $checkIndex = $pdo->query("SHOW INDEX FROM `dia_diem` WHERE Key_name = 'ft_tim_kiem'")->fetch();
    if (!$checkIndex) {
        $pdo->exec("ALTER TABLE `dia_diem` ADD FULLTEXT INDEX `ft_tim_kiem` (`ten_dia_diem`, `dia_chi`, `mo_ta_ngan`)");
        echo "Added FULLTEXT index ft_tim_kiem to dia_diem.\n";
    } else {
        echo "FULLTEXT index ft_tim_kiem already exists.\n";
    }

    // 4. Thêm Index phụ trợ tối ưu hóa truy vấn lọc
    $checkIdxTrangThai = $pdo->query("SHOW INDEX FROM `dia_diem` WHERE Key_name = 'idx_dia_diem_trang_thai_luot_xem'")->fetch();
    if (!$checkIdxTrangThai) {
        $pdo->exec("ALTER TABLE `dia_diem` ADD INDEX `idx_dia_diem_trang_thai_luot_xem` (`trang_thai`, `luot_xem` DESC)");
        echo "Added index idx_dia_diem_trang_thai_luot_xem to dia_diem.\n";
    }

    echo "Migration 003 completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}


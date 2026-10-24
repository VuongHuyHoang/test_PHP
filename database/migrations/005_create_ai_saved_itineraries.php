<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

$pdo = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `ai_saved_itineraries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `tieu_de` VARCHAR(255) NOT NULL,
  `so_ngay` INT UNSIGNED NOT NULL DEFAULT 1,
  `phong_cach` VARCHAR(100) DEFAULT NULL,
  `ngan_sach` VARCHAR(100) DEFAULT NULL,
  `noi_dung_markdown` LONGTEXT NOT NULL,
  `dia_diem_ids` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_saved_tours` (`user_id`, `id` DESC),
  CONSTRAINT `fk_saved_tours_user` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
echo "Table ai_saved_itineraries created or already exists successfully.\n";


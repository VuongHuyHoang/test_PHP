<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

$pdo = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `yeu_thich` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` BIGINT UNSIGNED NOT NULL,
  `dia_diem_id` BIGINT UNSIGNED NOT NULL,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nguoidung_diadiem_yeuthich` (`nguoi_dung_id`, `dia_diem_id`),
  CONSTRAINT `fk_yeuthich_nguoidung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_yeuthich_dia_diem` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
echo "Table yeu_thich created or already exists.\n";


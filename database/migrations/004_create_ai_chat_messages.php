<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

$pdo = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `ai_chat_messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role` ENUM('user', 'model') NOT NULL,
  `message` LONGTEXT NOT NULL,
  `matched_locations` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_chat` (`user_id`, `id` DESC),
  CONSTRAINT `fk_aichat_user` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
echo "Table ai_chat_messages created or already exists successfully.\n";


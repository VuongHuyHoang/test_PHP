<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

$pdo = Database::getConnection();

// Kiểm tra cột hinh_anh đã tồn tại chưa
$stmt = $pdo->query("SHOW COLUMNS FROM `danh_gia` LIKE 'hinh_anh'");
$exists = $stmt->fetch();

if (!$exists) {
    $sql = "ALTER TABLE `danh_gia` ADD COLUMN `hinh_anh` TEXT NULL DEFAULT NULL AFTER `noi_dung`";
    $pdo->exec($sql);
    echo "Column 'hinh_anh' added to table 'danh_gia' successfully.\n";
} else {
    echo "Column 'hinh_anh' already exists in table 'danh_gia'.\n";
}


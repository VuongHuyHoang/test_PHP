<?php

// Script tự động khởi tạo cơ sở dữ liệu và nạp dữ liệu mẫu Nha Trang

require_once dirname(__DIR__) . '/app/bootstrap.php';

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$dbName = $_ENV['DB_DATABASE'] ?? 'nhatrang_tourism';

echo "=== KHỞI TẠO CƠ SỞ DỮ LIỆU NHA TRANG TOURISM ===\n";
echo "Đang kết nối tới MySQL server tại {$host}:{$port}...\n";

try {
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "[OK] Kết nối MySQL thành công.\n";

    // 1. Chạy Schema DDL
    $schemaFile = __DIR__ . '/schema_vi.sql';
    if (!file_exists($schemaFile)) {
        die("[LỖI] Không tìm thấy file schema_vi.sql\n");
    }
    echo "Đang khởi tạo 14 bảng dữ liệu từ schema_vi.sql...\n";
    $schemaSql = file_get_contents($schemaFile);
    if ($dbName !== 'nhatrang_tourism') {
        $schemaSql = str_replace('`nhatrang_tourism`', "`{$dbName}`", $schemaSql);
    }
    $pdo->exec($schemaSql);
    echo "[OK] Đã tạo thành công cơ sở dữ liệu '{$dbName}' và 14 bảng quan hệ chuẩn 3NF.\n";

    // 2. Chạy Seed Data
    $seedFile = __DIR__ . '/seeds/sample_data.sql';
    if (!file_exists($seedFile)) {
        die("[LỖI] Không tìm thấy file sample_data.sql\n");
    }
    echo "Đang nạp dữ liệu mẫu 15 địa điểm thực tế Nha Trang từ sample_data.sql...\n";
    $seedSql = file_get_contents($seedFile);
    $pdo->exec($seedSql);
    echo "[OK] Nạp dữ liệu mẫu thành công!\n";

    // 3. Chạy Gallery Seed (Thư viện ảnh địa điểm)
    $gallerySeedFile = __DIR__ . '/seeds/gallery_seed.php';
    if (file_exists($gallerySeedFile)) {
        echo "Đang nạp bộ sưu tập ảnh trải nghiệm (Gallery Seed)...\n";
        require $gallerySeedFile;
        echo "[OK] Nạp thư viện ảnh thành công!\n";
    }

    // 4. Chạy toàn bộ Migrations tự động
    $migrationFiles = glob(__DIR__ . '/migrations/*.php');
    if (!empty($migrationFiles)) {
        echo "Đang đồng bộ các migrations bổ sung...\n";
        sort($migrationFiles);
        foreach ($migrationFiles as $migFile) {
            require $migFile;
        }
        echo "[OK] Đồng bộ migrations thành công!\n";
    }

    echo "===============================================\n";
    echo "HỆ THỐNG ĐÃ SẴN SÀNG SỬ DỤNG!\n";
    echo "- Đã khởi tạo: 14 bảng dữ liệu, 15 địa điểm mẫu, 24 ảnh gallery, AI Tour & Đặt vé VietQR\n";
    echo "- Tài khoản Admin: admin@nhatrangtourism.vn / Admin@123\n";
    echo "- Tài khoản Khách: nguyenan@gmail.com / Khach@123\n";

} catch (PDOException $e) {
    echo "[THẤT BẠI] " . $e->getMessage() . "\n";
    echo "Gợi ý: Hãy đảm bảo MySQL / XAMPP Control Panel đã bật dịch vụ MySQL.\n";
    exit(1);
}


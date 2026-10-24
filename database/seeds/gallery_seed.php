<?php

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

use App\Config\Database;

$pdo = Database::getConnection();

$count = $pdo->query("SELECT COUNT(*) FROM hinh_anh_dia_diem")->fetchColumn();
if ($count > 0) {
    echo "Gallery already seeded ($count rows).\n";
    exit;
}

$sampleGalleries = [
    // 1: VinWonders Nha Trang
    1 => [
        'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80',
    ],
    // 2: Tháp Bà Ponagar
    2 => [
        'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
    ],
    // 3: Chùa Long Sơn
    3 => [
        'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
    ],
    // 4: Hòn Mun
    4 => [
        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?auto=format&fit=crop&w=800&q=80',
    ],
    // 6: Nem nướng Đặng Văn Quyên
    6 => [
        'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80',
    ],
    // 7: Bánh căn 51
    7 => [
        'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
    ],
    // 10: I-Resort
    10 => [
        'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?auto=format&fit=crop&w=800&q=80',
    ],
    // 11: Nhà Hát Đó
    11 => [
        'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80',
    ]
];

$stmt = $pdo->prepare("INSERT INTO hinh_anh_dia_diem (dia_diem_id, duong_dan_anh) VALUES (:dia_diem_id, :duong_dan_anh)");
$totalInserted = 0;
foreach ($sampleGalleries as $diaDiemId => $urls) {
    // Check if dia_diem exists
    $exists = $pdo->query("SELECT id FROM dia_diem WHERE id = $diaDiemId")->fetchColumn();
    if (!$exists) continue;
    foreach ($urls as $url) {
        $stmt->execute([
            'dia_diem_id'   => $diaDiemId,
            'duong_dan_anh' => $url
        ]);
        $totalInserted++;
    }
}

echo "Successfully seeded $totalInserted gallery images.\n";


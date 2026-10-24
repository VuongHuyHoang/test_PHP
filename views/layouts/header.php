<?php
/**
 * @var string|null $title
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Hệ thống Khám phá Ẩm thực & Du lịch Nha Trang') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Leaflet.js CSS (Bản đồ số OpenStreetMap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <!-- Google Fonts: Be Vietnam Pro (Chuẩn quốc tế cho tiếng Việt) & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <?php 
        $cssFile = dirname(__DIR__, 2) . '/public/assets/css/style.css';
        $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();
    ?>
    <link rel="stylesheet" href="<?= url('assets/css/style.css?v=' . $cssVersion) ?>">

    <!-- Cấu hình ứng dụng & Map API Key từ .env -->
    <script>
        window.APP_URL = "<?= rtrim(url(''), '/') ?>";
        window.MAP_PROVIDER = "<?= htmlspecialchars($_ENV['MAP_PROVIDER'] ?? 'cartodb') ?>";
        window.MAP_API_KEY = "<?= htmlspecialchars($_ENV['MAP_API_KEY'] ?? '') ?>";
    </script>
</head>
<body class="d-flex flex-column min-vh-100">


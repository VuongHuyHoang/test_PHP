<?php

// 0. Thiết lập múi giờ chuẩn Việt Nam (Nha Trang UTC+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 1. Khởi động Session an toàn
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 2. Tự động đọc file .env vào $_ENV
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Xử lý bỏ dấu nháy kép / nháy đơn bao ngoài
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

// 3. Tải Composer Autoload (PHPMailer, v.v.)
$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// 4. Fallback PSR-4 Autoloader cho namespace "App\"
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 4. Các hàm Helper toàn cục tiện ích
if (!function_exists('view')) {
    function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $fullPath = dirname(__DIR__) . '/views/' . str_replace('.', '/', $viewPath) . '.php';
        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            die("View không tồn tại: " . htmlspecialchars($viewPath));
        }
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        if ($baseUrl === '') {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . $host;
        }
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null): ?string
    {
        if ($message !== null) {
            $_SESSION['flash_' . $key] = $message;
            return null;
        }
        $val = $_SESSION['flash_' . $key] ?? null;
        unset($_SESSION['flash_' . $key]);
        return $val;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
    }
}

if (!function_exists('validate_csrf')) {
    function validate_csrf(): bool
    {
        $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($submittedToken) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $submittedToken);
    }
}

if (!function_exists('hinh_anh')) {
    function hinh_anh(?string $path): string
    {
        return \App\Helpers\FormatHelper::hinhAnh($path);
    }
}

if (!function_exists('phan_trang')) {
    function phan_trang(int $total, int $perPage, int $currentPage, string $baseUrl = '', array $queryParams = []): string
    {
        return \App\Helpers\FormatHelper::phanTrang($total, $perPage, $currentPage, $baseUrl, $queryParams);
    }
}

if (!function_exists('__')) {
    function __(string $key, ?string $default = null): string
    {
        return \App\Helpers\LangHelper::trans($key, $default);
    }
}

if (!function_exists('current_lang')) {
    function current_lang(): string
    {
        return \App\Helpers\LangHelper::getLang();
    }
}


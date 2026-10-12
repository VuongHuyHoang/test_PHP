<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function checkCustomer(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['flash_error'] = 'Bạn cần đăng nhập tài khoản để tiến hành đặt vé.';
            header('Location: ' . url('login'));
            exit;
        }
    }

    public static function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Truy cập bị từ chối: Yêu cầu quyền Quản trị viên.");
        }
    }
}


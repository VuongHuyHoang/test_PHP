<?php

// Điểm vào duy nhất của ứng dụng (Front Controller)

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Router;
use App\Controllers\HomeController;
use App\Controllers\LocationController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderManageController;

// ==========================================
// 1. ROUTE CLIENT (KHÁM PHÁ & BẢN ĐỒ)
// ==========================================
Router::get('/', [HomeController::class, 'index']);
Router::get('/locations', [LocationController::class, 'index']);
Router::get('/dia-diem/{slug}', [LocationController::class, 'detail']);
Router::post('/dia-diem/danh-gia', [LocationController::class, 'submitReview']);
Router::get('/api/locations', [LocationController::class, 'apiLocations']);

// ==========================================
// 2. ROUTE XÁC THỰC (AUTHENTICATION)
// ==========================================
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'showRegister']);
Router::post('/register', [AuthController::class, 'register']);
Router::get('/logout', [AuthController::class, 'logout']);

// ==========================================
// 3. ROUTE ĐẶT VÉ & E-VOUCHER
// ==========================================
Router::get('/booking/{id}', [BookingController::class, 'showForm']);
Router::post('/booking/submit', [BookingController::class, 'submitBooking']);
Router::get('/booking/payment/{code}', [BookingController::class, 'showPayment']);
Router::post('/booking/confirm-payment', [BookingController::class, 'confirmPayment']);
Router::get('/voucher/{code}', [BookingController::class, 'showVoucher']);

// ==========================================
// 4. ROUTE ADMIN PORTAL
// ==========================================
Router::get('/admin/dashboard', [DashboardController::class, 'index']);
Router::get('/admin/orders', [OrderManageController::class, 'index']);
Router::post('/admin/orders/update-status', [OrderManageController::class, 'updateStatusAction']);

// Điều phối yêu cầu HTTP
Router::dispatch();


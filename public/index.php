<?php

// Điểm vào duy nhất của ứng dụng (Front Controller)

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Router;
use App\Controllers\HomeController;
use App\Controllers\LocationController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\ProfileController;
use App\Controllers\AiTourController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderManageController;
use App\Controllers\Admin\LocationManageController;
use App\Controllers\Admin\TicketManageController;
use App\Controllers\Admin\ReviewManageController;
use App\Controllers\Admin\CategoryManageController;
use App\Controllers\Admin\ReportController;

// ==========================================
// 1. ROUTE CLIENT (KHÁM PHÁ, BẢN ĐỒ & GIỚI THIỆU)
// ==========================================
Router::get('/', [HomeController::class, 'index']);
Router::get('/gioi-thieu', [HomeController::class, 'about']);
Router::get('/about', [HomeController::class, 'about']);
Router::get('/huong-dan', [HomeController::class, 'guide']);
Router::get('/guide', [HomeController::class, 'guide']);
Router::get('/locations', [LocationController::class, 'index']);
Router::get('/ai-tour', [AiTourController::class, 'index']);
Router::post('/api/ai/chat', [AiTourController::class, 'apiChat']);
Router::get('/api/ai/messages', [AiTourController::class, 'apiGetMessages']);
Router::post('/api/ai/clear', [AiTourController::class, 'apiClearMessages']);
Router::post('/api/ai/plan', [AiTourController::class, 'apiPlan']);
Router::post('/api/ai/save-tour', [AiTourController::class, 'apiSaveTour']);
Router::post('/api/ai/delete-tour', [AiTourController::class, 'apiDeleteSavedTour']);
Router::get('/dia-diem/{slug}', [LocationController::class, 'detail']);
Router::post('/dia-diem/danh-gia', [LocationController::class, 'submitReview']);
Router::get('/api/locations', [LocationController::class, 'apiLocations']);
Router::get('/api/search-suggest', [LocationController::class, 'searchSuggest']);
Router::post('/api/favorites/toggle', [LocationController::class, 'toggleFavorite']);

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
Router::post('/booking/simulate-payment', [BookingController::class, 'simulatePayment']);
Router::post('/booking/cancel', [BookingController::class, 'cancelBooking']);
Router::get('/booking/check-status/{code}', [BookingController::class, 'checkOrderStatus']);
Router::get('/voucher/{code}', [BookingController::class, 'showVoucher']);

// ==========================================
// 4. ROUTE HỒ SƠ KHÁCH HÀNG & VÉ ĐÃ ĐẶT
// ==========================================
Router::get('/profile', [ProfileController::class, 'index']);
Router::post('/profile/update', [ProfileController::class, 'updateProfile']);
Router::post('/profile/change-password', [ProfileController::class, 'changePassword']);

// ==========================================
// 4. ROUTE ADMIN PORTAL
// ==========================================
Router::get('/admin/dashboard', [DashboardController::class, 'index']);
Router::get('/admin/guide', [DashboardController::class, 'guide']);
Router::get('/admin/orders', [OrderManageController::class, 'index']);
Router::post('/admin/orders/update-status', [OrderManageController::class, 'updateStatusAction']);

// Quản lý địa điểm
Router::get('/admin/locations', [LocationManageController::class, 'index']);
Router::get('/admin/locations/create', [LocationManageController::class, 'showCreate']);
Router::post('/admin/locations/create', [LocationManageController::class, 'create']);
Router::get('/admin/locations/edit/{id}', [LocationManageController::class, 'showEdit']);
Router::post('/admin/locations/edit/{id}', [LocationManageController::class, 'edit']);
Router::post('/admin/locations/delete/{id}', [LocationManageController::class, 'delete']);
Router::post('/admin/locations/delete-image', [LocationManageController::class, 'deleteImage']);

// Quản lý danh mục
Router::get('/admin/categories', [CategoryManageController::class, 'index']);
Router::get('/admin/categories/create', [CategoryManageController::class, 'showCreate']);
Router::post('/admin/categories/create', [CategoryManageController::class, 'create']);
Router::get('/admin/categories/edit/{id}', [CategoryManageController::class, 'showEdit']);
Router::post('/admin/categories/edit/{id}', [CategoryManageController::class, 'edit']);
Router::post('/admin/categories/delete/{id}', [CategoryManageController::class, 'delete']);

// Quản lý gói vé & tồn kho
Router::get('/admin/tickets', [TicketManageController::class, 'index']);
Router::post('/admin/tickets/create-package', [TicketManageController::class, 'createPackage']);
Router::post('/admin/tickets/update-package', [TicketManageController::class, 'updatePackage']);
Router::post('/admin/tickets/delete-package', [TicketManageController::class, 'deletePackage']);
Router::post('/admin/tickets/set-inventory', [TicketManageController::class, 'setInventory']);

// Quản lý đánh giá & phản hồi cộng đồng
Router::get('/admin/reviews', [ReviewManageController::class, 'index']);
Router::post('/admin/reviews/update-status', [ReviewManageController::class, 'updateStatus']);
Router::post('/admin/reviews/delete', [ReviewManageController::class, 'delete']);

// Báo cáo & Thống kê hệ thống
Router::get('/admin/reports', [ReportController::class, 'index']);
Router::get('/admin/reports/export-csv', [ReportController::class, 'exportCsv']);

// Điều phối yêu cầu HTTP
Router::dispatch();


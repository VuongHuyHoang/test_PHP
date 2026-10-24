<?php

namespace App\Controllers;

use App\Services\AiTourService;
use App\Models\DanhMuc;
use App\Models\DonHang;

class AiTourController
{
    private AiTourService $aiService;
    private DanhMuc $danhMucModel;
    private DonHang $donHangModel;

    public function __construct()
    {
        $this->aiService = new AiTourService();
        $this->danhMucModel = new DanhMuc();
        $this->donHangModel = new DonHang();
    }

    /**
     * Hiển thị trang Trợ lý dịch vụ AI Tour theo phong cách Trip.com
     */
    public function index(): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $isLoggedIn = ($userId > 0);
        $myBookings = [];
        $initialMessages = [];
        $savedTours = [];

        if ($isLoggedIn) {
            // Lấy danh sách đặt chỗ / vé tham quan của user
            $allOrders = $this->donHangModel->theoNguoiDungKemChiTiet($userId);
            // Lọc các vé đã thanh toán hoặc giữ chỗ
            $myBookings = array_filter($allOrders, fn($o) => in_array($o['trang_thai_thanh_toan'] ?? '', ['da_thanh_toan', 'cho_thanh_toan']));
            $myBookings = array_values($myBookings);

            // Tải 15 tin nhắn gần nhất
            $initialMessages = $this->aiService->getMessages($userId, 15);

            // Tải danh sách lịch trình đã lưu
            $savedTours = $this->aiService->getSavedItineraries($userId);
        }

        view('client.ai_tour.index', [
            'title'           => 'Trợ Lý Dịch Vụ AI Tour - Nha Trang Staycation',
            'isLoggedIn'      => $isLoggedIn,
            'myBookings'      => $myBookings,
            'initialMessages' => $initialMessages,
            'savedTours'      => $savedTours,
        ]);
    }

    /**
     * API Chatbot: Yêu cầu đăng nhập, lưu tin nhắn vào DB và nạp multi-turn context
     */
    public function apiChat(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode([
                'success'       => false,
                'require_login' => true,
                'message'       => 'Vui lòng đăng nhập tài khoản để trò chuyện với Trợ lý AI và lưu lại lịch sử chuyến đi!'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        $data = is_array($jsonData) ? array_merge($_POST, $jsonData) : $_POST;

        $message = trim($data['message'] ?? '');
        if (empty($message)) {
            echo json_encode([
                'success' => false,
                'message' => 'Nội dung câu hỏi không được để trống.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = $this->aiService->chatWithAssistant($message, $userId);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Tải thêm tin nhắn cũ (phục vụ nút "Xem Tin Nhắn Trước")
     */
    public function apiGetMessages(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode([
                'success'       => false,
                'require_login' => true,
                'messages'      => []
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $beforeId = (int)($_GET['before_id'] ?? 0);
        $limit = min(50, max(5, (int)($_GET['limit'] ?? 15)));

        $messages = $this->aiService->getMessages($userId, $limit, $beforeId);

        echo json_encode([
            'success'  => true,
            'messages' => $messages,
            'has_more' => count($messages) >= $limit
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Xóa toàn bộ lịch sử đoạn chat của người dùng
     */
    public function apiClearMessages(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode([
                'success'       => false,
                'require_login' => true,
                'message'       => 'Vui lòng đăng nhập.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $this->aiService->clearHistory($userId);

        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa toàn bộ lịch sử đoạn chat thành công.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Tạo lịch trình tour chuyên sâu (Dành cho chức năng Lập Tour)
     */
    public function apiPlan(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        $data = is_array($jsonData) ? array_merge($_POST, $jsonData) : $_POST;

        $songay    = (int)($data['songay'] ?? 1);
        $phongcach = trim($data['phongcach'] ?? 'tong_hop');
        $ngansach  = trim($data['ngansach'] ?? 'tieu_chuan');
        $doituong  = trim($data['doituong'] ?? 'cap_doi');
        $ghichu    = trim($data['ghichu'] ?? '');

        $result = $this->aiService->generateItinerary([
            'songay'    => $songay,
            'phongcach' => $phongcach,
            'ngansach'  => $ngansach,
            'doituong'  => $doituong,
            'ghichu'    => $ghichu,
        ]);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Lưu lịch trình vào hồ sơ cá nhân
     */
    public function apiSaveTour(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode([
                'success'       => false,
                'require_login' => true,
                'message'       => 'Vui lòng đăng nhập tài khoản để lưu lại lịch trình tour này!'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        $data = is_array($jsonData) ? array_merge($_POST, $jsonData) : $_POST;

        $title = trim($data['title'] ?? 'Lịch Trình Nha Trang Staycation');
        $days = max(1, (int)($data['days'] ?? 1));
        $style = trim($data['style'] ?? 'tong_hop');
        $budget = trim($data['budget'] ?? 'tieu_chuan');
        $markdown = trim($data['markdown'] ?? '');
        $locIds = $data['loc_ids'] ?? [];

        if (empty($markdown)) {
            echo json_encode(['success' => false, 'message' => 'Nội dung lịch trình không được để trống.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $savedId = $this->aiService->saveItinerary($userId, $title, $days, $style, $budget, $markdown, is_array($locIds) ? $locIds : null);

        echo json_encode([
            'success' => ($savedId > 0),
            'id'      => $savedId,
            'message' => ($savedId > 0) ? 'Đã lưu lịch trình thành công vào hồ sơ của bạn!' : 'Không thể lưu lịch trình lúc này.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Xóa lịch trình đã lưu
     */
    public function apiDeleteSavedTour(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'require_login' => true], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        $data = is_array($jsonData) ? array_merge($_POST, $jsonData) : $_POST;

        $id = (int)($data['id'] ?? 0);
        $deleted = $this->aiService->deleteSavedItinerary($id, $userId);

        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Đã xóa lịch trình thành công.' : 'Không thể xóa lịch trình này.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

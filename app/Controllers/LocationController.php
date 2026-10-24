<?php

namespace App\Controllers;

use App\Models\DanhMuc;
use App\Models\DiaDiem;
use App\Models\GoiVe;
use App\Models\DanhGia;
use App\Models\YeuThich;
use App\Middlewares\AuthMiddleware;

class LocationController
{
    private DiaDiem $diaDiemModel;
    private DanhMuc $danhMucModel;
    private GoiVe $goiVeModel;
    private DanhGia $danhGiaModel;
    private YeuThich $yeuThichModel;

    public function __construct()
    {
        $this->diaDiemModel = new DiaDiem();
        $this->danhMucModel = new DanhMuc();
        $this->goiVeModel = new GoiVe();
        $this->danhGiaModel = new DanhGia();
        $this->yeuThichModel = new YeuThich();
    }

    public function index(): void
    {
        $filters = [
            'loai'        => $_GET['loai'] ?? '',
            'danh_muc_id' => $_GET['danh_muc_id'] ?? '',
            'tu_khoa'     => $_GET['tu_khoa'] ?? '',
            'co_ban_ve'   => $_GET['co_ban_ve'] ?? '',
            'muc_gia'     => $_GET['muc_gia'] ?? '',
            'dang_mo_cua' => $_GET['dang_mo_cua'] ?? '',
        ];

        $diaDiemList = $this->diaDiemModel->danhSach($filters);
        $danhMucList = $this->danhMucModel->tatCa();

        view('client.locations.index', [
            'title'       => 'Khám Phá Địa Điểm Ẩm Thực & Du Lịch Nha Trang',
            'diaDiemList' => $diaDiemList,
            'danhMucList' => $danhMucList,
            'filters'     => $filters,
        ]);
    }

    public function detail(string $slug): void
    {
        $diaDiem = $this->diaDiemModel->chiTietTheoSlug($slug);
        if (!$diaDiem) {
            http_response_code(404);
            view('layouts.404', ['title' => 'Không tìm thấy địa điểm']);
            return;
        }

        // Tăng lượt xem
        $this->diaDiemModel->tangLuotXem((int)$diaDiem['id']);

        // Lấy danh sách gói vé nếu địa điểm có bán vé
        $goiVeList = [];
        if ((bool)$diaDiem['co_ban_ve']) {
            $goiVeList = $this->goiVeModel->layTheoDiaDiem((int)$diaDiem['id']);
        }

        // Lấy đánh giá cộng đồng
        $danhGiaList = $this->danhGiaModel->layTheoDiaDiem((int)$diaDiem['id']);

        // Lấy bộ sưu tập ảnh phụ (gallery)
        $gallery = $this->diaDiemModel->layHinhAnh((int)$diaDiem['id']);

        // Lấy 4 địa điểm liên quan cùng danh mục
        $diaDiemLienQuan = $this->diaDiemModel->danhSach([
            'danh_muc_id' => $diaDiem['danh_muc_id'],
            'exclude_id'  => (int)$diaDiem['id'],
            'limit'       => 4,
        ]);

        // Kiểm tra địa điểm đã được yêu thích chưa
        $isFavorite = false;
        if (!empty($_SESSION['user_id'])) {
            $isFavorite = $this->yeuThichModel->kiemTra((int)$_SESSION['user_id'], (int)$diaDiem['id']);
        }

        view('client.locations.detail', [
            'title'             => $diaDiem['ten_dia_diem'] . ' - Nha Trang',
            'diaDiem'           => $diaDiem,
            'goiVeList'         => $goiVeList,
            'danhGiaList'       => $danhGiaList,
            'gallery'           => $gallery,
            'diaDiemLienQuan'   => $diaDiemLienQuan,
            'isFavorite'        => $isFavorite,
        ]);
    }

    /**
     * Endpoint API JSON cung cấp danh sách tọa độ phục vụ Leaflet.js và AJAX filter
     */
    public function apiLocations(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [
            'loai'        => $_GET['loai'] ?? '',
            'danh_muc_id' => $_GET['danh_muc_id'] ?? '',
            'tu_khoa'     => $_GET['tu_khoa'] ?? '',
            'co_ban_ve'   => $_GET['co_ban_ve'] ?? '',
            'muc_gia'     => $_GET['muc_gia'] ?? '',
            'dang_mo_cua' => $_GET['dang_mo_cua'] ?? '',
        ];

        $markers = $this->diaDiemModel->layToaDoMap($filters);
        echo json_encode([
            'status' => 'success',
            'total'  => count($markers),
            'data'   => $markers
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Xử lý gửi đánh giá cộng đồng cho địa điểm
     */
    public function submitReview(): void
    {
        AuthMiddleware::checkCustomer();

        $slug = trim($_POST['slug'] ?? '');

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('dia-diem/' . $slug));
            return;
        }

        $diaDiemId = (int)($_POST['dia_diem_id'] ?? 0);
        $soSao = (int)($_POST['so_sao'] ?? 5);
        $noiDung = trim($_POST['noi_dung'] ?? '');

        if ($diaDiemId <= 0 || empty($noiDung)) {
            flash('error', 'Vui lòng nhập nội dung đánh giá.');
            redirect(url('dia-diem/' . $slug));
        }

        $soSao = max(1, min(5, $soSao));
        $nguoiDungId = (int)$_SESSION['user_id'];

        // Xử lý upload ảnh đánh giá đa phương tiện (tối đa 3 ảnh)
        $uploadedPhotos = [];
        if (!empty($_FILES['hinh_anh']['name'][0])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/reviews';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileCount = min(3, count($_FILES['hinh_anh']['name']));

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['hinh_anh']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['hinh_anh']['tmp_name'][$i];
                    $fileSize = $_FILES['hinh_anh']['size'][$i];
                    $mime = mime_content_type($tmpName);

                    if (in_array($mime, $allowedMimes) && $fileSize <= 5 * 1024 * 1024) {
                        $ext = pathinfo($_FILES['hinh_anh']['name'][$i], PATHINFO_EXTENSION);
                        $fileName = 'rev_' . $nguoiDungId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
                        $targetPath = $uploadDir . '/' . $fileName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $uploadedPhotos[] = 'uploads/reviews/' . $fileName;
                        }
                    }
                }
            }
        }

        $hinhAnhJson = !empty($uploadedPhotos) ? json_encode($uploadedPhotos, JSON_UNESCAPED_SLASHES) : null;

        $thanhCong = $this->danhGiaModel->themMoi($nguoiDungId, $diaDiemId, $soSao, $noiDung, $hinhAnhJson);
        if ($thanhCong) {
            flash('success', 'Cảm ơn bạn đã gửi đánh giá trải nghiệm kèm hình ảnh!');
        } else {
            flash('error', 'Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.');
        }

        redirect(url('dia-diem/' . $slug));
    }

    /**
     * API AJAX Bật/Tắt yêu thích địa điểm
     */
    public function toggleFavorite(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user_id'])) {
            echo json_encode([
                'success'       => false,
                'require_login' => true,
                'message'       => 'Vui lòng đăng nhập để lưu địa điểm yêu thích!'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Đọc dữ liệu từ JSON body hoặc POST
        $rawInput = file_get_contents('php://input');
        $jsonInput = json_decode($rawInput, true) ?? [];
        $diaDiemId = (int)($jsonInput['dia_diem_id'] ?? $_POST['dia_diem_id'] ?? 0);
        $token = $jsonInput['csrf_token'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            echo json_encode([
                'success' => false,
                'message' => 'Phiên bảo mật không hợp lệ (CSRF error).'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($diaDiemId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Địa điểm không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $isFavorited = $this->yeuThichModel->toggle($userId, $diaDiemId);
        $totalFav = $this->yeuThichModel->demTheoNguoiDung($userId);

        echo json_encode([
            'success'     => true,
            'is_favorite' => $isFavorited,
            'total_fav'   => $totalFav,
            'message'     => $isFavorited ? 'Đã thêm vào danh sách yêu thích!' : 'Đã bỏ khỏi danh sách yêu thích.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Endpoint API JSON gợi ý tức thì (Live Auto-Suggest Search)
     */
    public function searchSuggest(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $query = trim($_GET['q'] ?? '');

        if (mb_strlen($query, 'UTF-8') < 1) {
            echo json_encode(['status' => 'success', 'data' => []]);
            exit;
        }

        $results = $this->diaDiemModel->timKiemGoiY($query, 6);

        // Chuẩn hóa dữ liệu trả về cho frontend UI
        $formatted = array_map(function ($item) {
            return [
                'id'            => $item['id'],
                'ten_dia_diem'  => $item['ten_dia_diem'],
                'duong_dan'     => $item['duong_dan'],
                'url'           => url('dia-diem/' . $item['duong_dan']),
                'dia_chi'       => $item['dia_chi'],
                'anh_dai_dien'  => hinh_anh($item['anh_dai_dien']),
                'ten_danh_muc'  => $item['ten_danh_muc'],
                'loai_danh_muc' => $item['loai_danh_muc'],
                'gia_thap_nhat' => (float)$item['gia_thap_nhat'],
                'gia_cao_nhat'  => (float)$item['gia_cao_nhat'],
                'co_ban_ve'     => (bool)$item['co_ban_ve'],
                'diem_danh_gia' => (float)$item['diem_danh_gia'],
                'dang_mo_cua'   => (bool)$item['dang_mo_cua'],
            ];
        }, $results);

        echo json_encode([
            'status' => 'success',
            'query'  => $query,
            'total'  => count($formatted),
            'data'   => $formatted
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

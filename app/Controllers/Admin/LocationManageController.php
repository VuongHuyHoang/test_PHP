<?php

namespace App\Controllers\Admin;

use App\Middlewares\AuthMiddleware;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use Exception;

class LocationManageController
{
    private DiaDiem $diaDiemModel;
    private DanhMuc $danhMucModel;

    public function __construct()
    {
        $this->diaDiemModel = new DiaDiem();
        $this->danhMucModel = new DanhMuc();
    }

    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $tuKhoa = trim($_GET['q'] ?? '');
        $danhMucId = (int)($_GET['danh_muc_id'] ?? 0);
        $loai = trim($_GET['loai'] ?? '');

        $locations = $this->diaDiemModel->danhSachAdmin([
            'tu_khoa'     => $tuKhoa,
            'danh_muc_id' => $danhMucId ?: null,
            'loai'        => $loai ?: null,
        ]);

        $categories = $this->danhMucModel->tatCa();

        view('admin.locations.index', [
            'title'      => 'Quản Lý Địa Điểm Du Lịch & Ẩm Thực',
            'locations'  => $locations,
            'categories' => $categories,
            'tuKhoa'     => $tuKhoa,
            'danhMucId'  => $danhMucId,
            'loai'       => $loai,
        ]);
    }

    public function showCreate(): void
    {
        AuthMiddleware::checkAdmin();

        $categories = $this->danhMucModel->tatCa();

        view('admin.locations.form', [
            'title'      => 'Thêm Mới Địa Điểm',
            'mode'       => 'create',
            'location'   => null,
            'categories' => $categories,
            'gallery'    => [],
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::checkAdmin();

        $ten = trim($_POST['ten_dia_diem'] ?? '');
        $danhMucId = (int)($_POST['danh_muc_id'] ?? 0);
        $diaChi = trim($_POST['dia_chi'] ?? '');
        $viDo = (float)($_POST['vi_do'] ?? 0);
        $kinhDo = (float)($_POST['kinh_do'] ?? 0);
        $giaThap = (float)($_POST['gia_thap_nhat'] ?? 0);
        $giaCao = (float)($_POST['gia_cao_nhat'] ?? 0);
        $gioMo = trim($_POST['gio_mo_cua'] ?? '');
        $gioDong = trim($_POST['gio_dong_cua'] ?? '');
        $moTaNgan = trim($_POST['mo_ta_ngan'] ?? '');
        $noiDung = trim($_POST['noi_dung_chi_tiet'] ?? '');
        $coBanVe = isset($_POST['co_ban_ve']) ? 1 : 0;
        $trangThai = $_POST['trang_thai'] ?? 'hien_thi';

        if (empty($ten) || $danhMucId <= 0 || empty($diaChi) || $viDo == 0 || $kinhDo == 0) {
            flash('error', 'Vui lòng điền đầy đủ: Tên địa điểm, Danh mục, Địa chỉ và Tọa độ GPS hợp lệ.');
            redirect(url('admin/locations/create'));
            return;
        }

        // Sinh slug
        $slug = $this->slugify($ten);
        // Đảm bảo slug duy nhất
        $check = $this->diaDiemModel->chiTietTheoSlug($slug);
        if ($check) {
            $slug .= '-' . rand(100, 999);
        }

        // Xử lý ảnh đại diện
        $anhDaiDien = 'assets/images/placeholder.jpg';
        if (!empty($_FILES['anh_dai_dien']['name'])) {
            $uploaded = $this->uploadImage($_FILES['anh_dai_dien']);
            if ($uploaded) {
                $anhDaiDien = $uploaded;
            }
        } elseif (!empty($_POST['anh_dai_dien_url'])) {
            $anhDaiDien = trim($_POST['anh_dai_dien_url']);
        }

        try {
            $newId = $this->diaDiemModel->them([
                'danh_muc_id'       => $danhMucId,
                'ten_dia_diem'      => $ten,
                'duong_dan'         => $slug,
                'dia_chi'           => $diaChi,
                'vi_do'             => $viDo,
                'kinh_do'           => $kinhDo,
                'gia_thap_nhat'     => $giaThap,
                'gia_cao_nhat'      => $giaCao,
                'gio_mo_cua'        => $gioMo,
                'gio_dong_cua'      => $gioDong,
                'anh_dai_dien'      => $anhDaiDien,
                'mo_ta_ngan'        => $moTaNgan,
                'noi_dung_chi_tiet' => $noiDung,
                'co_ban_ve'         => $coBanVe,
                'trang_thai'        => $trangThai
            ]);

            // Xử lý nhiều ảnh thư viện
            if (!empty($_FILES['gallery_images']['name'][0])) {
                $this->uploadGallery($newId, $_FILES['gallery_images']);
            }

            flash('success', "Thêm mới địa điểm '{$ten}' thành công!");
            redirect(url('admin/locations'));
        } catch (Exception $e) {
            flash('error', 'Lỗi khi thêm địa điểm: ' . $e->getMessage());
            redirect(url('admin/locations/create'));
        }
    }

    public function showEdit(string|int $id): void
    {
        AuthMiddleware::checkAdmin();

        $location = $this->diaDiemModel->timTheoId((int)$id);
        if (!$location) {
            flash('error', 'Không tìm thấy địa điểm yêu cầu.');
            redirect(url('admin/locations'));
            return;
        }

        $categories = $this->danhMucModel->tatCa();
        $gallery = $this->diaDiemModel->layHinhAnh((int)$id);

        view('admin.locations.form', [
            'title'      => 'Chỉnh Sửa Địa Điểm - ' . $location['ten_dia_diem'],
            'mode'       => 'edit',
            'location'   => $location,
            'categories' => $categories,
            'gallery'    => $gallery,
        ]);
    }

    public function edit(string|int $id): void
    {
        AuthMiddleware::checkAdmin();
        $id = (int)$id;

        $location = $this->diaDiemModel->timTheoId($id);
        if (!$location) {
            flash('error', 'Địa điểm không tồn tại.');
            redirect(url('admin/locations'));
            return;
        }

        $ten = trim($_POST['ten_dia_diem'] ?? '');
        $danhMucId = (int)($_POST['danh_muc_id'] ?? 0);
        $diaChi = trim($_POST['dia_chi'] ?? '');
        $viDo = (float)($_POST['vi_do'] ?? 0);
        $kinhDo = (float)($_POST['kinh_do'] ?? 0);
        $giaThap = (float)($_POST['gia_thap_nhat'] ?? 0);
        $giaCao = (float)($_POST['gia_cao_nhat'] ?? 0);
        $gioMo = trim($_POST['gio_mo_cua'] ?? '');
        $gioDong = trim($_POST['gio_dong_cua'] ?? '');
        $moTaNgan = trim($_POST['mo_ta_ngan'] ?? '');
        $noiDung = trim($_POST['noi_dung_chi_tiet'] ?? '');
        $coBanVe = isset($_POST['co_ban_ve']) ? 1 : 0;
        $trangThai = $_POST['trang_thai'] ?? 'hien_thi';

        $slug = trim($_POST['duong_dan'] ?? '');
        if (empty($slug)) {
            $slug = $this->slugify($ten);
        }

        // Ảnh đại diện
        $anhDaiDien = $location['anh_dai_dien'];
        if (!empty($_FILES['anh_dai_dien']['name'])) {
            $uploaded = $this->uploadImage($_FILES['anh_dai_dien']);
            if ($uploaded) {
                $anhDaiDien = $uploaded;
            }
        } elseif (!empty($_POST['anh_dai_dien_url'])) {
            $anhDaiDien = trim($_POST['anh_dai_dien_url']);
        }

        try {
            $this->diaDiemModel->capNhat($id, [
                'danh_muc_id'       => $danhMucId,
                'ten_dia_diem'      => $ten,
                'duong_dan'         => $slug,
                'dia_chi'           => $diaChi,
                'vi_do'             => $viDo,
                'kinh_do'           => $kinhDo,
                'gia_thap_nhat'     => $giaThap,
                'gia_cao_nhat'      => $giaCao,
                'gio_mo_cua'        => $gioMo,
                'gio_dong_cua'      => $gioDong,
                'anh_dai_dien'      => $anhDaiDien,
                'mo_ta_ngan'        => $moTaNgan,
                'noi_dung_chi_tiet' => $noiDung,
                'co_ban_ve'         => $coBanVe,
                'trang_thai'        => $trangThai
            ]);

            // Upload thêm ảnh gallery nếu có
            if (!empty($_FILES['gallery_images']['name'][0])) {
                $this->uploadGallery($id, $_FILES['gallery_images']);
            }

            flash('success', "Cập nhật địa điểm '{$ten}' thành công!");
            redirect(url('admin/locations'));
        } catch (Exception $e) {
            flash('error', 'Lỗi khi cập nhật địa điểm: ' . $e->getMessage());
            redirect(url('admin/locations/edit/' . $id));
        }
    }

    public function delete(string|int $id): void
    {
        AuthMiddleware::checkAdmin();
        $id = (int)$id;

        try {
            $this->diaDiemModel->xoa($id);
            flash('success', 'Đã xóa / chuyển trạng thái ẩn địa điểm thành công!');
        } catch (Exception $e) {
            flash('error', 'Lỗi khi xóa địa điểm: ' . $e->getMessage());
        }

        redirect(url('admin/locations'));
    }

    public function deleteImage(): void
    {
        AuthMiddleware::checkAdmin();
        $imageId = (int)($_POST['image_id'] ?? 0);
        $locationId = (int)($_POST['location_id'] ?? 0);

        if ($imageId > 0) {
            $this->diaDiemModel->xoaHinhAnh($imageId);
            flash('success', 'Đã xóa ảnh thư viện!');
        }

        redirect(url('admin/locations/edit/' . $locationId));
    }

    private function uploadImage(array $file): ?string
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/locations';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = 'loc_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
        $dest = $uploadDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/locations/' . $newName;
        }

        return null;
    }

    private function uploadGallery(int $diaDiemId, array $files): void
    {
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $singleFile = [
                    'name'     => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $path = $this->uploadImage($singleFile);
                if ($path) {
                    $this->diaDiemModel->themHinhAnh($diaDiemId, $path);
                }
            }
        }
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $transliteration = [
            'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
            'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
            'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'đ'=>'d',
            'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
            'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
            'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
            'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
            'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y'
        ];
        $text = strtr($text, $transliteration);
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}

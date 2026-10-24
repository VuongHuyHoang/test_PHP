<?php

namespace App\Controllers\Admin;

use App\Middlewares\AuthMiddleware;
use App\Models\DanhMuc;
use Exception;

class CategoryManageController
{
    private DanhMuc $danhMucModel;

    public function __construct()
    {
        $this->danhMucModel = new DanhMuc();
    }

    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $tuKhoa = trim($_GET['q'] ?? '');
        $loai = trim($_GET['loai'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $filters = [
            'tu_khoa' => $tuKhoa,
            'loai'    => $loai ?: null,
        ];

        $totalCategories = $this->danhMucModel->demAdmin($filters);
        $categories = $this->danhMucModel->danhSachAdmin($filters, $page, $perPage);
        $stats = $this->danhMucModel->thongKe();

        view('admin.categories.index', [
            'title'           => 'Quản Lý Danh Mục Trải Nghiệm & Ẩm Thực',
            'categories'      => $categories,
            'stats'           => $stats,
            'tuKhoa'          => $tuKhoa,
            'loai'            => $loai,
            'page'            => $page,
            'perPage'         => $perPage,
            'totalCategories' => $totalCategories,
        ]);
    }

    public function showCreate(): void
    {
        AuthMiddleware::checkAdmin();

        view('admin.categories.form', [
            'title'    => 'Thêm Mới Danh Mục',
            'mode'     => 'create',
            'category' => null,
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc đã hết hạn hoặc không hợp lệ.');
            redirect(url('admin/categories/create'));
            return;
        }

        $ten = trim($_POST['ten_danh_muc'] ?? '');
        $loai = trim($_POST['loai_danh_muc'] ?? 'du_lich');
        $bieuTuong = trim($_POST['bieu_tuong'] ?? 'bi-tag');
        $customSlug = trim($_POST['duong_dan'] ?? '');

        if (empty($ten)) {
            flash('error', 'Vui lòng nhập tên danh mục.');
            redirect(url('admin/categories/create'));
            return;
        }

        if (!in_array($loai, ['am_thuc', 'du_lich'], true)) {
            $loai = 'du_lich';
        }

        $slug = !empty($customSlug) ? $this->slugify($customSlug) : $this->slugify($ten);

        // Đảm bảo slug là duy nhất
        if ($this->danhMucModel->kiemTraSlugTonTai($slug)) {
            $slug .= '-' . rand(10, 99);
        }

        try {
            $newId = $this->danhMucModel->themMoi([
                'ten_danh_muc'  => $ten,
                'duong_dan'     => $slug,
                'loai_danh_muc' => $loai,
                'bieu_tuong'    => $bieuTuong ?: 'bi-tag',
            ]);

            flash('success', "Thêm danh mục '{$ten}' thành công!");
            redirect(url('admin/categories'));
        } catch (Exception $e) {
            flash('error', 'Lỗi khi lưu danh mục: ' . $e->getMessage());
            redirect(url('admin/categories/create'));
        }
    }

    public function showEdit(int $id): void
    {
        AuthMiddleware::checkAdmin();

        $category = $this->danhMucModel->timTheoId($id);
        if (!$category) {
            flash('error', 'Danh mục không tồn tại.');
            redirect(url('admin/categories'));
            return;
        }

        $locationCount = $this->danhMucModel->demDiaDiemThuocDanhMuc($id);

        view('admin.categories.form', [
            'title'         => 'Chỉnh Sửa Danh Mục: ' . $category['ten_danh_muc'],
            'mode'          => 'edit',
            'category'      => $category,
            'locationCount' => $locationCount,
        ]);
    }

    public function edit(int $id): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc đã hết hạn hoặc không hợp lệ.');
            redirect(url('admin/categories/edit/' . $id));
            return;
        }

        $category = $this->danhMucModel->timTheoId($id);
        if (!$category) {
            flash('error', 'Danh mục không tồn tại.');
            redirect(url('admin/categories'));
            return;
        }

        $ten = trim($_POST['ten_danh_muc'] ?? '');
        $loai = trim($_POST['loai_danh_muc'] ?? 'du_lich');
        $bieuTuong = trim($_POST['bieu_tuong'] ?? 'bi-tag');
        $customSlug = trim($_POST['duong_dan'] ?? '');

        if (empty($ten)) {
            flash('error', 'Tên danh mục không được để trống.');
            redirect(url('admin/categories/edit/' . $id));
            return;
        }

        if (!in_array($loai, ['am_thuc', 'du_lich'], true)) {
            $loai = 'du_lich';
        }

        $slug = !empty($customSlug) ? $this->slugify($customSlug) : $this->slugify($ten);

        if ($this->danhMucModel->kiemTraSlugTonTai($slug, $id)) {
            $slug .= '-' . rand(10, 99);
        }

        try {
            $this->danhMucModel->capNhat($id, [
                'ten_danh_muc'  => $ten,
                'duong_dan'     => $slug,
                'loai_danh_muc' => $loai,
                'bieu_tuong'    => $bieuTuong ?: 'bi-tag',
            ]);

            flash('success', "Cập nhật danh mục '{$ten}' thành công!");
            redirect(url('admin/categories'));
        } catch (Exception $e) {
            flash('error', 'Lỗi khi cập nhật danh mục: ' . $e->getMessage());
            redirect(url('admin/categories/edit/' . $id));
        }
    }

    public function delete(int $id): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Yêu cầu không hợp lệ.');
            redirect(url('admin/categories'));
            return;
        }

        $category = $this->danhMucModel->timTheoId($id);
        if (!$category) {
            flash('error', 'Danh mục không tồn tại.');
            redirect(url('admin/categories'));
            return;
        }

        // Kiểm tra an toàn: nếu đang có địa điểm thuộc danh mục, KHÔNG cho xóa
        $locationCount = $this->danhMucModel->demDiaDiemThuocDanhMuc($id);
        if ($locationCount > 0) {
            flash('error', "Không thể xóa danh mục '{$category['ten_danh_muc']}' vì đang có {$locationCount} địa điểm trực thuộc. Vui lòng chuyển các địa điểm sang danh mục khác trước.");
            redirect(url('admin/categories'));
            return;
        }

        try {
            $this->danhMucModel->xoa($id);
            flash('success', "Đã xóa danh mục '{$category['ten_danh_muc']}' thành công!");
        } catch (Exception $e) {
            flash('error', 'Lỗi khi xóa danh mục: ' . $e->getMessage());
        }

        redirect(url('admin/categories'));
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
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}


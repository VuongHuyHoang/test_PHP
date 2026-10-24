<?php

namespace App\Controllers\Admin;

use App\Middlewares\AuthMiddleware;
use App\Models\DanhGia;
use Exception;

class ReviewManageController
{
    private DanhGia $danhGiaModel;

    public function __construct()
    {
        $this->danhGiaModel = new DanhGia();
    }

    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $tuKhoa = trim($_GET['q'] ?? '');
        $trangThai = trim($_GET['trang_thai'] ?? '');
        $soSao = trim($_GET['so_sao'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $filters = [
            'tu_khoa'    => $tuKhoa,
            'trang_thai' => $trangThai,
            'so_sao'     => $soSao,
        ];

        $totalReviews = $this->danhGiaModel->demAdmin($filters);
        $reviews = $this->danhGiaModel->danhSachAdmin($filters, $page, $perPage);
        $stats = $this->danhGiaModel->thongKe();

        view('admin.reviews.index', [
            'title'        => 'Quản Lý Đánh Giá & Nhận Xét Cộng Đồng',
            'reviews'      => $reviews,
            'stats'        => $stats,
            'tuKhoa'       => $tuKhoa,
            'trangThai'    => $trangThai,
            'soSao'        => $soSao,
            'page'         => $page,
            'perPage'      => $perPage,
            'totalReviews' => $totalReviews,
        ]);
    }

    public function updateStatus(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/reviews'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $trangThai = trim($_POST['trang_thai'] ?? '');

        if ($id <= 0 || !in_array($trangThai, ['da_duyet', 'da_an'])) {
            flash('error', 'Dữ liệu cập nhật trạng thái không hợp lệ.');
            redirect(url('admin/reviews'));
            return;
        }

        if ($this->danhGiaModel->capNhatTrangThai($id, $trangThai)) {
            $msg = ($trangThai === 'da_duyet') ? 'Đã duyệt hiển thị đánh giá thành công!' : 'Đã ẩn đánh giá thành công!';
            flash('success', $msg);
        } else {
            flash('error', 'Lỗi khi cập nhật trạng thái đánh giá.');
        }

        redirect(url('admin/reviews'));
    }

    public function delete(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/reviews'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'ID đánh giá không hợp lệ.');
            redirect(url('admin/reviews'));
            return;
        }

        if ($this->danhGiaModel->xoa($id)) {
            flash('success', 'Đã xóa đánh giá vĩnh viễn!');
        } else {
            flash('error', 'Không thể xóa đánh giá. Vui lòng thử lại.');
        }

        redirect(url('admin/reviews'));
    }
}


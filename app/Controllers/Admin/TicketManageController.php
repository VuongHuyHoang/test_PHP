<?php

namespace App\Controllers\Admin;

use App\Middlewares\AuthMiddleware;
use App\Models\GoiVe;
use App\Models\TonKhoVe;
use App\Models\DiaDiem;
use Exception;

class TicketManageController
{
    private GoiVe $goiVeModel;
    private TonKhoVe $tonKhoModel;
    private DiaDiem $diaDiemModel;

    public function __construct()
    {
        $this->goiVeModel = new GoiVe();
        $this->tonKhoModel = new TonKhoVe();
        $this->diaDiemModel = new DiaDiem();
    }

    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $filterNgay = trim($_GET['ngay'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        // Danh sách toàn bộ gói vé
        $packages = $this->goiVeModel->layTatCa();

        // Danh sách địa điểm có cờ bán vé để chọn khi thêm gói vé
        $ticketLocations = $this->diaDiemModel->danhSachAdmin(['co_ban_ve' => 1]);

        // Danh sách tồn kho theo ngày có phân trang
        $totalInventories = $this->tonKhoModel->demDanhSach($filterNgay ?: null);
        $inventories = $this->tonKhoModel->layDanhSachPhanTrang($page, $perPage, $filterNgay ?: null);

        view('admin.tickets.index', [
            'title'            => 'Quản Lý Gói Vé & Hạn Mức Tồn Kho',
            'packages'         => $packages,
            'ticketLocations'  => $ticketLocations,
            'inventories'      => $inventories,
            'filterNgay'       => $filterNgay,
            'page'             => $page,
            'perPage'          => $perPage,
            'totalInventories' => $totalInventories,
        ]);
    }

    public function createPackage(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        $diaDiemId = (int)($_POST['dia_diem_id'] ?? 0);
        $tenGoiVe = trim($_POST['ten_goi_ve'] ?? '');
        $moTa = trim($_POST['mo_ta'] ?? '');
        $giaNguoiLon = (float)($_POST['gia_nguoi_lon'] ?? 0);
        $giaTreEm = (float)($_POST['gia_tre_em'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? 'dang_ban';

        if ($diaDiemId <= 0 || empty($tenGoiVe) || $giaNguoiLon < 0) {
            flash('error', 'Vui lòng nhập đầy đủ địa điểm, tên gói vé và giá vé người lớn hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        try {
            $this->goiVeModel->them([
                'dia_diem_id'   => $diaDiemId,
                'ten_goi_ve'    => $tenGoiVe,
                'mo_ta'         => $moTa,
                'gia_nguoi_lon' => $giaNguoiLon,
                'gia_tre_em'    => $giaTreEm,
                'trang_thai'    => $trangThai
            ]);
            flash('success', "Thêm mới gói vé '{$tenGoiVe}' thành công!");
        } catch (Exception $e) {
            flash('error', 'Lỗi khi thêm gói vé: ' . $e->getMessage());
        }

        redirect(url('admin/tickets'));
    }

    public function updatePackage(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $diaDiemId = (int)($_POST['dia_diem_id'] ?? 0);
        $tenGoiVe = trim($_POST['ten_goi_ve'] ?? '');
        $moTa = trim($_POST['mo_ta'] ?? '');
        $giaNguoiLon = (float)($_POST['gia_nguoi_lon'] ?? 0);
        $giaTreEm = (float)($_POST['gia_tre_em'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? 'dang_ban';

        if ($id <= 0 || $diaDiemId <= 0 || empty($tenGoiVe)) {
            flash('error', 'Dữ liệu cập nhật gói vé không hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        try {
            $this->goiVeModel->capNhat($id, [
                'dia_diem_id'   => $diaDiemId,
                'ten_goi_ve'    => $tenGoiVe,
                'mo_ta'         => $moTa,
                'gia_nguoi_lon' => $giaNguoiLon,
                'gia_tre_em'    => $giaTreEm,
                'trang_thai'    => $trangThai
            ]);
            flash('success', "Cập nhật gói vé '{$tenGoiVe}' thành công!");
        } catch (Exception $e) {
            flash('error', 'Lỗi khi cập nhật gói vé: ' . $e->getMessage());
        }

        redirect(url('admin/tickets'));
    }

    public function deletePackage(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->goiVeModel->xoa($id);
            flash('success', 'Đã xóa / tạm dừng gói vé thành công!');
        }

        redirect(url('admin/tickets'));
    }

    public function setInventory(): void
    {
        AuthMiddleware::checkAdmin();

        if (!validate_csrf()) {
            flash('error', 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ.');
            redirect(url('admin/tickets'));
            return;
        }

        $goiVeId = (int)($_POST['goi_ve_id'] ?? 0);
        $ngaySuDung = trim($_POST['ngay_su_dung'] ?? '');
        $tongSoVe = (int)($_POST['tong_so_ve'] ?? 0);

        if ($goiVeId <= 0 || empty($ngaySuDung) || $tongSoVe <= 0) {
            flash('error', 'Vui lòng chọn gói vé, ngày sử dụng và số lượng vé lớn hơn 0.');
            redirect(url('admin/tickets'));
            return;
        }

        try {
            $this->tonKhoModel->thietLapTonKho($goiVeId, $ngaySuDung, $tongSoVe);
            flash('success', "Thiết lập hạn mức {$tongSoVe} vé cho ngày {$ngaySuDung} thành công!");
        } catch (Exception $e) {
            flash('error', 'Lỗi khi thiết lập tồn kho: ' . $e->getMessage());
        }

        redirect(url('admin/tickets'));
    }
}


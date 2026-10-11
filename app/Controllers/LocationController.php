<?php

namespace App\Controllers;

use App\Models\DanhMuc;
use App\Models\DiaDiem;
use App\Models\GoiVe;
use App\Models\DanhGia;

class LocationController
{
    private DiaDiem $diaDiemModel;
    private DanhMuc $danhMucModel;
    private GoiVe $goiVeModel;
    private DanhGia $danhGiaModel;

    public function __construct()
    {
        $this->diaDiemModel = new DiaDiem();
        $this->danhMucModel = new DanhMuc();
        $this->goiVeModel = new GoiVe();
        $this->danhGiaModel = new DanhGia();
    }

    public function index(): void
    {
        $filters = [
            'loai'        => $_GET['loai'] ?? '',
            'danh_muc_id' => $_GET['danh_muc_id'] ?? '',
            'tu_khoa'     => $_GET['tu_khoa'] ?? '',
            'co_ban_ve'   => $_GET['co_ban_ve'] ?? '',
        ];

        $diaDiemList = $this->diaDiemModel->danhSach($filters);
        $danhMucList = $this->danhMucModel->tatCa();

        view('client.locations.index', [
            'title'       => 'Danh Sách Địa Điểm Nha Trang',
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

        view('client.locations.detail', [
            'title'       => $diaDiem['ten_dia_diem'] . ' - Nha Trang',
            'diaDiem'     => $diaDiem,
            'goiVeList'   => $goiVeList,
            'danhGiaList' => $danhGiaList,
        ]);
    }

    /**
     * Endpoint API JSON cung cấp danh sách tọa độ phục vụ Leaflet.js
     */
    public function apiLocations(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [
            'loai'        => $_GET['loai'] ?? '',
            'danh_muc_id' => $_GET['danh_muc_id'] ?? '',
        ];

        $markers = $this->diaDiemModel->layToaDoMap($filters);
        echo json_encode([
            'status' => 'success',
            'total'  => count($markers),
            'data'   => $markers
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

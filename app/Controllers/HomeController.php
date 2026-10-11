<?php

namespace App\Controllers;

use App\Models\DanhMuc;
use App\Models\DiaDiem;

class HomeController
{
    private DanhMuc $danhMucModel;
    private DiaDiem $diaDiemModel;

    public function __construct()
    {
        $this->danhMucModel = new DanhMuc();
        $this->diaDiemModel = new DiaDiem();
    }

    public function index(): void
    {
        $danhMucList = $this->danhMucModel->tatCa();
        
        // 8 địa điểm nổi bật cho trang chủ
        $diaDiemNoiBat = $this->diaDiemModel->danhSach(['limit' => 8]);
        
        // Địa điểm ẩm thực tiêu biểu
        $amThucList = $this->diaDiemModel->danhSach(['loai' => 'am_thuc', 'limit' => 4]);

        // Địa điểm du lịch tiêu biểu
        $duLichList = $this->diaDiemModel->danhSach(['loai' => 'du_lich', 'limit' => 4]);

        view('client.home.index', [
            'title'          => 'Khám Phá Ẩm Thực & Du Lịch Nha Trang',
            'danhMucList'    => $danhMucList,
            'diaDiemNoiBat'  => $diaDiemNoiBat,
            'amThucList'     => $amThucList,
            'duLichList'     => $duLichList,
        ]);
    }
}

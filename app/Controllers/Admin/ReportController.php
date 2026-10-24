<?php

namespace App\Controllers\Admin;

use App\Config\Database;
use App\Middlewares\AuthMiddleware;
use App\Helpers\FormatHelper;
use PDO;

class ReportController
{
    public function index(): void
    {
        AuthMiddleware::checkAdmin();

        $db = Database::getConnection();

        // 1. Xác định khoảng thời gian báo cáo
        $ky = $_GET['ky'] ?? '30_ngay';
        $tuNgay = $_GET['tu_ngay'] ?? '';
        $denNgay = $_GET['den_ngay'] ?? '';

        $today = date('Y-m-d');

        switch ($ky) {
            case '7_ngay':
                $startDate = date('Y-m-d', strtotime('-6 days'));
                $endDate = $today;
                $labelPeriod = '7 ngày gần nhất (' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . ')';
                break;
            case 'thang_nay':
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
                $labelPeriod = 'Tháng này (' . date('m/Y') . ')';
                break;
            case 'quy_nay':
                $currentMonth = (int)date('m');
                $quarter = ceil($currentMonth / 3);
                $quarterStartMonth = ($quarter - 1) * 3 + 1;
                $startDate = date('Y-') . str_pad($quarterStartMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime(date('Y-') . str_pad($quarterStartMonth + 2, 2, '0', STR_PAD_LEFT) . '-01'));
                $labelPeriod = "Quý {$quarter}/" . date('Y');
                break;
            case 'nam_nay':
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                $labelPeriod = 'Năm ' . date('Y');
                break;
            case 'tuy_chon':
                $startDate = !empty($tuNgay) ? date('Y-m-d', strtotime($tuNgay)) : date('Y-m-01');
                $endDate = !empty($denNgay) ? date('Y-m-d', strtotime($denNgay)) : $today;
                $labelPeriod = 'Từ ' . date('d/m/Y', strtotime($startDate)) . ' đến ' . date('d/m/Y', strtotime($endDate));
                break;
            case '30_ngay':
            default:
                $ky = '30_ngay';
                $startDate = date('Y-m-d', strtotime('-29 days'));
                $endDate = $today;
                $labelPeriod = '30 ngày gần nhất (' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . ')';
                break;
        }

        // 2. Thống kê KPI trong kỳ
        $stmtKpi = $db->prepare("
            SELECT 
                COUNT(*) as tong_don,
                SUM(CASE WHEN trang_thai_thanh_toan = 'da_thanh_toan' THEN 1 ELSE 0 END) as don_thanh_cong,
                SUM(CASE WHEN trang_thai_don_hang = 'da_huy' THEN 1 ELSE 0 END) as don_huy,
                SUM(CASE WHEN trang_thai_thanh_toan = 'da_thanh_toan' THEN tong_tien ELSE 0 END) as doanh_thu
            FROM don_hang
            WHERE DATE(ngay_tao) BETWEEN :tu_ngay AND :den_ngay AND ngay_xoa IS NULL
        ");
        $stmtKpi->execute(['tu_ngay' => $startDate, 'den_ngay' => $endDate]);
        $kpi = $stmtKpi->fetch();

        $tongDon = (int)($kpi['tong_don'] ?? 0);
        $donThanhCong = (int)($kpi['don_thanh_cong'] ?? 0);
        $donHuy = (int)($kpi['don_huy'] ?? 0);
        $doanhThu = (float)($kpi['doanh_thu'] ?? 0);
        $giaTriDonTB = $donThanhCong > 0 ? round($doanhThu / $donThanhCong) : 0;
        $tyLeThanhCong = $tongDon > 0 ? round(($donThanhCong / $tongDon) * 100, 1) : 0;

        // Tổng số vé đã bán trong kỳ
        $stmtVe = $db->prepare("
            SELECT 
                COALESCE(SUM(ct.so_luong_nguoi_lon), 0) as ve_lon,
                COALESCE(SUM(ct.so_luong_tre_em), 0) as ve_tre_em,
                COALESCE(SUM(ct.so_luong_nguoi_lon + ct.so_luong_tre_em), 0) as tong_ve
            FROM chi_tiet_don_hang ct
            JOIN don_hang dh ON ct.don_hang_id = dh.id
            WHERE dh.trang_thai_thanh_toan = 'da_thanh_toan' 
              AND dh.ngay_xoa IS NULL
              AND DATE(dh.ngay_tao) BETWEEN :tu_ngay AND :den_ngay
        ");
        $stmtVe->execute(['tu_ngay' => $startDate, 'den_ngay' => $endDate]);
        $veData = $stmtVe->fetch();
        $tongVe = (int)($veData['tong_ve'] ?? 0);
        $veLon = (int)($veData['ve_lon'] ?? 0);
        $veTreEm = (int)($veData['ve_tre_em'] ?? 0);

        // 3. Biểu đồ đường Doanh thu & Số đơn theo ngày
        $diffDays = (strtotime($endDate) - strtotime($startDate)) / 86400;
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        // Nếu khoảng thời gian <= 45 ngày thì thống kê theo từng ngày, nếu > 45 ngày thì theo tuần/tháng
        if ($diffDays <= 45) {
            $current = strtotime($startDate);
            $endTimestamp = strtotime($endDate);

            while ($current <= $endTimestamp) {
                $curDate = date('Y-m-d', $current);
                $chartLabels[] = date('d/m', $current);

                $stmtDay = $db->prepare("
                    SELECT COALESCE(SUM(tong_tien), 0) as rev, COUNT(*) as cnt
                    FROM don_hang
                    WHERE DATE(ngay_tao) = :ngay 
                      AND trang_thai_thanh_toan = 'da_thanh_toan' 
                      AND ngay_xoa IS NULL
                ");
                $stmtDay->execute(['ngay' => $curDate]);
                $rowDay = $stmtDay->fetch();

                $chartRevenue[] = (float)($rowDay['rev'] ?? 0);
                $chartOrders[] = (int)($rowDay['cnt'] ?? 0);

                $current = strtotime('+1 day', $current);
            }
        } else {
            // Theo tháng
            $stmtMonth = $db->prepare("
                SELECT DATE_FORMAT(ngay_tao, '%m/%Y') as thang,
                       COALESCE(SUM(tong_tien), 0) as rev,
                       COUNT(*) as cnt
                FROM don_hang
                WHERE DATE(ngay_tao) BETWEEN :tu_ngay AND :den_ngay
                  AND trang_thai_thanh_toan = 'da_thanh_toan'
                  AND ngay_xoa IS NULL
                GROUP BY thang
                ORDER BY MIN(ngay_tao) ASC
            ");
            $stmtMonth->execute(['tu_ngay' => $startDate, 'den_ngay' => $endDate]);
            $rowsMonth = $stmtMonth->fetchAll();

            foreach ($rowsMonth as $rm) {
                $chartLabels[] = $rm['thang'];
                $chartRevenue[] = (float)$rm['rev'];
                $chartOrders[] = (int)$rm['cnt'];
            }
        }

        // 4. Top 5 Gói vé bán chạy nhất trong kỳ
        $stmtTopVe = $db->prepare("
            SELECT gv.ten_goi_ve, dd.ten_dia_diem, dd.duong_dan,
                   SUM(ct.so_luong_nguoi_lon + ct.so_luong_tre_em) as so_ve_ban,
                   SUM(ct.thanh_tien) as doanh_so
            FROM chi_tiet_don_hang ct
            JOIN don_hang dh ON ct.don_hang_id = dh.id
            JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id
            JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE dh.trang_thai_thanh_toan = 'da_thanh_toan'
              AND dh.ngay_xoa IS NULL
              AND DATE(dh.ngay_tao) BETWEEN :tu_ngay AND :den_ngay
            GROUP BY gv.id
            ORDER BY doanh_so DESC
            LIMIT 5
        ");
        $stmtTopVe->execute(['tu_ngay' => $startDate, 'den_ngay' => $endDate]);
        $topGoiVe = $stmtTopVe->fetchAll();

        // 5. Thống kê theo Danh mục (Số địa điểm, lượt xem, doanh thu vé)
        $categoryStats = $db->query("
            SELECT dm.ten_danh_muc, dm.loai_danh_muc, dm.bieu_tuong,
                   COUNT(DISTINCT dd.id) as so_dia_diem,
                   COALESCE(SUM(dd.luot_xem), 0) as tong_luot_xem,
                   COALESCE(SUM(CASE WHEN dh.trang_thai_thanh_toan = 'da_thanh_toan' THEN ct.thanh_tien ELSE 0 END), 0) as doanh_thu_ve
            FROM danh_muc dm
            LEFT JOIN dia_diem dd ON dm.id = dd.danh_muc_id
            LEFT JOIN goi_ve gv ON dd.id = gv.dia_diem_id
            LEFT JOIN ton_kho_ve tk ON gv.id = tk.goi_ve_id
            LEFT JOIN chi_tiet_don_hang ct ON tk.id = ct.ton_kho_ve_id
            LEFT JOIN don_hang dh ON ct.don_hang_id = dh.id AND dh.ngay_xoa IS NULL
            GROUP BY dm.id
            ORDER BY doanh_thu_ve DESC, tong_luot_xem DESC
        ")->fetchAll();

        // 6. Top 10 Địa điểm có nhiều lượt xem & quan tâm nhất
        $topLocations = $db->query("
            SELECT dd.id, dd.ten_dia_diem, dd.duong_dan, dd.luot_xem, dd.co_ban_ve,
                   dm.ten_danh_muc, dm.loai_danh_muc,
                   COALESCE(AVG(dg.so_sao), 5.0) as diem_sao,
                   COUNT(dg.id) as tong_danh_gia,
                   (SELECT COUNT(*) FROM yeu_thich WHERE dia_diem_id = dd.id) as tong_yeu_thich
            FROM dia_diem dd
            JOIN danh_muc dm ON dd.danh_muc_id = dm.id
            LEFT JOIN danh_gia dg ON dd.id = dg.dia_diem_id AND dg.trang_thai = 'da_duyet'
            WHERE dd.trang_thai = 'hien_thi'
            GROUP BY dd.id
            ORDER BY dd.luot_xem DESC
            LIMIT 10
        ")->fetchAll();

        // 7. Phân bổ đánh giá mức độ hài lòng (Review distribution 1 - 5 sao)
        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $ratingRows = $db->query("
            SELECT so_sao, COUNT(*) as sl 
            FROM danh_gia 
            WHERE trang_thai = 'da_duyet' 
            GROUP BY so_sao
        ")->fetchAll();
        foreach ($ratingRows as $rr) {
            $star = (int)$rr['so_sao'];
            if (isset($ratingCounts[$star])) {
                $ratingCounts[$star] = (int)$rr['sl'];
            }
        }

        // Tỷ lệ đánh giá tích cực (4-5 sao)
        $totalApprovedReviews = array_sum($ratingCounts);
        $positiveReviews = $ratingCounts[4] + $ratingCounts[5];
        $satisfactionRate = $totalApprovedReviews > 0 ? round(($positiveReviews / $totalApprovedReviews) * 100, 1) : 100;

        view('admin.reports.index', [
            'title'                => 'Báo Cáo & Phân Tích Thống Kê Hệ Thống',
            'ky'                   => $ky,
            'tuNgay'               => $startDate,
            'denNgay'              => $endDate,
            'labelPeriod'          => $labelPeriod,
            'tongDon'              => $tongDon,
            'donThanhCong'         => $donThanhCong,
            'donHuy'               => $donHuy,
            'doanhThu'             => $doanhThu,
            'giaTriDonTB'          => $giaTriDonTB,
            'tyLeThanhCong'        => $tyLeThanhCong,
            'tongVe'               => $tongVe,
            'veLon'                => $veLon,
            'veTreEm'              => $veTreEm,
            'chartLabels'          => json_encode($chartLabels),
            'chartRevenue'         => json_encode($chartRevenue),
            'chartOrders'          => json_encode($chartOrders),
            'topGoiVe'             => $topGoiVe,
            'categoryStats'        => $categoryStats,
            'topLocations'         => $topLocations,
            'ratingCounts'         => $ratingCounts,
            'satisfactionRate'     => $satisfactionRate,
            'totalApprovedReviews' => $totalApprovedReviews,
        ]);
    }

    /**
     * Xuất dữ liệu báo cáo đơn hàng sang CSV (Excel chuẩn tiếng Việt UTF-8 BOM)
     */
    public function exportCsv(): void
    {
        AuthMiddleware::checkAdmin();

        $db = Database::getConnection();

        $tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
        $denNgay = $_GET['den_ngay'] ?? date('Y-m-d');

        $stmt = $db->prepare("
            SELECT dh.ma_don_hang, dh.ngay_tao, dh.tong_tien,
                   dh.trang_thai_don_hang, dh.trang_thai_thanh_toan, dh.phuong_thuc_thanh_toan,
                   dh.ten_nguoi_dat, dh.email_nguoi_dat, dh.sdt_nguoi_dat,
                   gv.ten_goi_ve, dd.ten_dia_diem,
                   ct.so_luong_nguoi_lon, ct.so_luong_tre_em
            FROM don_hang dh
            LEFT JOIN chi_tiet_don_hang ct ON dh.id = ct.don_hang_id
            LEFT JOIN ton_kho_ve tk ON ct.ton_kho_ve_id = tk.id
            LEFT JOIN goi_ve gv ON tk.goi_ve_id = gv.id
            LEFT JOIN dia_diem dd ON gv.dia_diem_id = dd.id
            WHERE dh.ngay_xoa IS NULL 
              AND DATE(dh.ngay_tao) BETWEEN :tu_ngay AND :den_ngay
            ORDER BY dh.id DESC
        ");
        $stmt->execute(['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = 'Bao_Cao_Doanh_Thu_NhaTrang_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // Ghi UTF-8 BOM để Excel hiển thị tiếng Việt có dấu chuẩn xác 100%
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header các cột
        fputcsv($output, [
            'Mã Đơn Hàng',
            'Ngày Đặt',
            'Họ Tên Khách',
            'Email',
            'Số Điện Thoại',
            'Địa Điểm Tham Quan',
            'Gói Vé',
            'Vé Người Lớn',
            'Vé Trẻ Em',
            'Tổng Tiền (VNĐ)',
            'Trạng Thái Đơn',
            'Thanh Toán',
            'Phương Thức'
        ]);

        foreach ($orders as $o) {
            fputcsv($output, [
                $o['ma_don_hang'],
                $o['ngay_tao'],
                $o['ten_nguoi_dat'],
                $o['email_nguoi_dat'],
                $o['sdt_nguoi_dat'],
                $o['ten_dia_diem'] ?? 'Vé dịch vụ',
                $o['ten_goi_ve'] ?? 'Gói tiêu chuẩn',
                (int)($o['so_luong_nguoi_lon'] ?? 0),
                (int)($o['so_luong_tre_em'] ?? 0),
                (float)$o['tong_tien'],
                $o['trang_thai_don_hang'],
                $o['trang_thai_thanh_toan'],
                $o['phuong_thuc_thanh_toan']
            ]);
        }

        fclose($output);
        exit;
    }
}

# TÀI LIỆU KỸ THUẬT HỆ THỐNG QUẢNG BÁ ĐỊA ĐIỂM ẨM THỰC VÀ DU LỊCH NHA TRANG

---

## 1. Mục tiêu và Kiến trúc tổng thể của hệ thống

### 1.1. Mục tiêu dự án

* Xây dựng nền tảng web mã nguồn mở phục vụ du khách và người dân địa phương tại Nha Trang tra cứu, định vị các quán ăn đặc sản và địa điểm du lịch nổi tiếng.
* Cung cấp phân hệ đặt vé vui chơi, tour du lịch trực tuyến (mô hình tinh gọn lấy cảm hứng từ Agoda/Klook) kèm mã xác nhận đơn hàng (E-Voucher QR).
* Phục vụ môn học **Phát triển phần mềm mã nguồn mở** với nhóm 5 thành viên, áp dụng quy trình cộng tác phân tán chuyên nghiệp (**Forking Workflow**).

### 1.2. Phạm vi và Định vị nghiệp vụ

* **Ẩm thực:** Tập trung vào trải nghiệm khám phá, định vị tọa độ, xem khung giá, hình ảnh và đánh giá từ cộng đồng (không làm đặt bàn ăn).
* **Du lịch & Đặt vé:** Bán vé cho các khu vui chơi, tour đảo, dịch vụ có kiểm soát số lượng.
* **Quyết định phạm vi tinh giản:**
* **Bỏ đặt vé nhanh (Guest Checkout):** Bắt buộc người dùng phải đăng nhập tài khoản trước khi thanh toán nhằm loại trừ đơn ảo, tránh spam giữ chỗ kho vé và quản lý lịch sử đơn hàng tập trung.
* **Bỏ công cụ camera quét soát vé thời gian thực:** Chuyển sang mô hình **Phiếu xác nhận đặt vé (E-Voucher)**. Quản trị viên tại quầy tra cứu đơn theo `ma_don_hang` hoặc `sdt_khach_hang` để xác nhận hoàn tất dịch vụ.



### 1.3. Kiến trúc tổng thể

Hệ thống được thiết kế theo mô hình **MVC (Model - View - Controller)** kết hợp cơ sở dữ liệu quan hệ MySQL:

```
                  ┌────────────────────────────────────────────────────────┐
                  │                    CLIENT BROWSER                      │
                  └───────────▲────────────────────────────────▲───────────┘
                              │ HTTP Request                   │ Assets / CDN
                              ▼                                ▼
                  ┌──────────────────────┐        ┌────────────────────────┐
                  │    public/index.php  │        │   OpenStreetMap / Leaflet│
                  │   (Front Controller) │        │   Bootstrap 5 / CSS     │
                  └───────────┬──────────┘        └────────────────────────┘
                              │ Routing & Middleware (Auth, Role)
                              ▼
┌──────────────────────────────────────────────────────────────────────────────────┐
│                             BACKEND TIER (PHP MVC)                               │
│                                                                                  │
│   ┌───────────────────────────┐                ┌─────────────────────────────┐   │
│   │        Controllers        │                │            Views            │   │
│   │  - AuthController         │───────────────►│  - views/client/*           │   │
│   │  - LocationController     │ (Truyền Data)  │  - views/admin/*            │   │
│   │  - BookingController      │                │  - views/layouts/*          │   │
│   │  - AdminOrderController   │                └─────────────────────────────┘   │
│   └─────────────┬─────────────┘                                                  │
│                 │ Gọi xử lý nghiệp vụ & Transaction                              │
│                 ▼                                                                │
│   ┌───────────────────────────┐                ┌─────────────────────────────┐   │
│   │      Models / Services    │                │       Helpers / Utils       │   │
│   │  - Database (PDO Wrapper) │◄───────────────│  - SessionHelper            │   │
│   │  - BookingService         │                │  - QrGenerator / Mailer     │   │
│   └─────────────┬─────────────┘                └─────────────────────────────┘   │
└─────────────────┼────────────────────────────────────────────────────────────────┘
                  │ PDO / SQL Queries
                  ▼
┌──────────────────────────────────────────────────────────────────────────────────┐
│                          DATABASE TIER (MySQL 8.0+)                              │
│  - 10 Bảng quan hệ chuẩn 3NF                                                    │
│  - Ràng buộc toàn vẹn CHECK & STORED GENERATED COLUMN                            │
│  - Khóa ngoại CASCADE / RESTRICT theo chuẩn an toàn dữ liệu                     │
└──────────────────────────────────────────────────────────────────────────────────┘

```

---

## 2. Stack công nghệ, Cấu trúc thư mục và Quy ước đặt tên

### 2.1. Stack công nghệ (Toàn bộ mã nguồn mở)

* **Backend:** PHP 8.x (Mô hình MVC chuẩn, kết nối qua PDO).
* **Cơ sở dữ liệu:** MySQL 8.0+ / MariaDB 10.5+ (Hỗ trợ ràng buộc `CHECK` và `GENERATED ALWAYS AS`).
* **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5 hoặc Tailwind CSS.
* **Bản đồ tương tác:** **Leaflet.js** kết hợp dữ liệu bản đồ mở **OpenStreetMap** (thay thế Google Maps API).
* **Thư viện tiện ích mã nguồn mở:**
* **PHPMailer:** Gửi email xác nhận kèm phiếu E-Voucher.
* **Endroid QR Code** hoặc **BaconQrCode:** Tạo mã QR ảnh gắn vào E-Voucher.


* **Quản lý phiên bản & triển khai:** Git, GitHub, XAMPP (Apache + MariaDB).

### 2.2. Cấu trúc thư mục đề xuất cho dự án

```text
nhatrang-tourism-web/
├── .env.example                # Cấu hình môi trường mẫu
├── .env                        # Cấu hình môi trường thực tế (bị .gitignore bỏ qua)
├── .gitignore                  # Khai báo loại bỏ file rác, vendor, file bảo mật
├── README.md                   # Giới thiệu dự án và hướng dẫn cài đặt
├── composer.json               # Quản lý dependencies (PHPMailer, QR Code)
├── database/
│   ├── schema_vi.sql           # Script khởi tạo 10 bảng CSDL tiếng Việt
│   └── seeds/
│       └── sample_data.sql     # Dữ liệu mẫu địa điểm ẩm thực & du lịch Nha Trang
├── public/                     # Thư mục gốc web server trỏ tới (Document Root)
│   ├── index.php               # Điểm vào ứng dụng (Front Controller)
│   ├── .htaccess               # Cấu hình URL Rewrite sạch
│   ├── assets/
│   │   ├── css/                # File stylesheet
│   │   ├── js/                 # File javascript (xử lý Leaflet map, ajax)
│   │   └── images/             # Ảnh tĩnh giao diện
│   └── uploads/                # Ảnh do người dùng/admin upload (bọc quyền ghi)
│       ├── locations/
│       └── qrcodes/
├── app/
│   ├── Config/
│   │   └── Database.php        # Khởi tạo kết nối PDO an toàn qua .env
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── LocationController.php
│   │   ├── BookingController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── LocationManageController.php
│   │       └── OrderManageController.php
│   ├── Models/
│   │   ├── NguoiDung.php
│   │   ├── DanhMuc.php
│   │   ├── DiaDiem.php
│   │   ├── GoiVe.php
│   │   ├── TonKhoVe.php
│   │   ├── DonHang.php
│   │   └── DanhGia.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php  # Chặn bắt buộc đăng nhập
│   │   └── AdminMiddleware.php # Chặn quyền quản trị viên
│   └── Helpers/
│       ├── FormatHelper.php    # Format tiền VND, ngày giờ
│       └── QrHelper.php        # Hàm sinh QR code
└── views/
    ├── layouts/
    │   ├── header.php
    │   ├── footer.php
    │   └── admin_layout.php
    ├── client/
    │   ├── home/
    │   ├── locations/          # Danh sách, chi tiết địa điểm, bản đồ
    │   ├── booking/            # Form đặt vé, xác nhận E-Voucher
    │   └── auth/               # Đăng nhập, đăng ký
    └── admin/
        ├── dashboard/
        ├── locations/
        └── orders/             # Tra cứu đơn, duyệt E-Voucher

```

### 2.3. Quy ước đặt tên (Conventions)

* **Cơ sở dữ liệu:**
* Tên bảng và tên cột: Chữ thường, phân cách dấu gạch dưới (`snake_case`), hoàn toàn bằng **tiếng Việt không dấu** (ví dụ: `don_hang`, `ten_khach_hang`, `so_ve_con_lai`).
* Khóa chính: Luôn đặt tên là `id` (kiểu `BIGINT UNSIGNED`).
* Khóa ngoại: Tên bảng số ít + `_id` (ví dụ: `don_hang_id`, `goi_ve_id`).


* **Mã nguồn PHP:**
* Tên lớp (Class, Controller, Model): Dùng kiểu PascalCase (ví dụ: `BookingController`, `TonKhoVe`).
* Tên phương thức/hàm (Method, Function): Dùng kiểu camelCase (ví dụ: `datVe()`, `capNhatTrangThaiDonHang()`).
* Tên biến: Dùng camelCase (ví dụ: `$donHangId`, `$tongTien`).


* **Git Commit Messages:** Tuân thủ Conventional Commits:
* `feat:` Tính năng mới (ví dụ: `feat: Them bo loc quan an theo khu vuc`).
* `fix:` Sửa lỗi (ví dụ: `fix: Khac phuc race condition khi tru ton kho ve`).
* `db:` Thay đổi liên quan tới cơ sở dữ liệu.
* `style:` Sửa giao diện CSS/HTML không đổi logic.



---

## 3. Schema cơ sở dữ liệu đã chốt (10 Bảng)

### 3.1. Danh sách 10 bảng và mối quan hệ

1. `nguoi_dung`: Tài khoản hệ thống (Admin & Khách hàng).
2. `danh_muc`: Phân loại danh mục (Ẩm thực & Du lịch).
3. `dia_diem`: Địa điểm quán ăn/du lịch, kinh độ, vĩ độ, cờ `co_ban_ve`.
4. `hinh_anh_dia_diem`: Bộ sưu tập ảnh của địa điểm (quan hệ 1-N với `dia_diem`).
5. `goi_ve`: Các gói vé dịch vụ gắn với địa điểm (`co_ban_ve = 1`).
6. `ton_kho_ve`: Quản lý giới hạn số lượng vé theo từng ngày cụ thể.
7. `don_hang`: Đơn hàng đặt vé, thông tin khách, trạng thái thanh toán và mã QR voucher.
8. `chi_tiet_don_hang`: Chi tiết vé người lớn, trẻ em và cột `thanh_tien` tính tự động.
9. `giao_dich_thanh_toan`: Nhật ký đối soát thanh toán (chặn xóa cứng).
10. `danh_gia`: Đánh giá 1-5 sao và bình luận (chống đánh giá trùng lặp).

### 3.2. DDL Script SQL hoàn chỉnh (`database/schema_vi.sql`)

```sql
-- =============================================================================
-- HE THONG QUANG BA AM THUC VA DU LICH NHA TRANG (FINAL PRODUCTION SCHEMA)
-- MySQL 8.0.16+ / MariaDB 10.5+
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. BANG NGUOI_DUNG
DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE `nguoi_dung` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ho_ten` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `mat_khau` VARCHAR(255) NOT NULL,
  `so_dien_thoai` VARCHAR(15) DEFAULT NULL,
  `anh_dai_dien` VARCHAR(255) DEFAULT NULL,
  `vai_tro` ENUM('admin', 'khach_hang') NOT NULL DEFAULT 'khach_hang',
  `trang_thai` ENUM('hoat_dong', 'bi_khoa') NOT NULL DEFAULT 'hoat_dong',
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BANG DANH_MUC
DROP TABLE IF EXISTS `danh_muc`;
CREATE TABLE `danh_muc` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` VARCHAR(100) NOT NULL,
  `duong_dan` VARCHAR(120) NOT NULL UNIQUE,
  `loai_danh_muc` ENUM('am_thuc', 'du_lich') NOT NULL,
  `bieu_tuong` VARCHAR(100) DEFAULT NULL,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BANG DIA_DIEM
DROP TABLE IF EXISTS `dia_diem`;
CREATE TABLE `dia_diem` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `danh_muc_id` INT UNSIGNED NOT NULL,
  `ten_dia_diem` VARCHAR(200) NOT NULL,
  `duong_dan` VARCHAR(220) NOT NULL UNIQUE,
  `dia_chi` VARCHAR(255) NOT NULL,
  `vi_do` DECIMAL(10, 8) NOT NULL,
  `kinh_do` DECIMAL(11, 8) NOT NULL,
  `gia_thap_nhat` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `gia_cao_nhat` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `gio_mo_cua` TIME DEFAULT NULL,
  `gio_dong_cua` TIME DEFAULT NULL,
  `anh_dai_dien` VARCHAR(255) NOT NULL,
  `mo_ta_ngan` TEXT DEFAULT NULL,
  `noi_dung_chi_tiet` LONGTEXT DEFAULT NULL,
  `co_ban_ve` BOOLEAN NOT NULL DEFAULT FALSE,
  `trang_thai` ENUM('hien_thi', 'an') NOT NULL DEFAULT 'hien_thi',
  `luot_xem` INT UNSIGNED NOT NULL DEFAULT 0,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_dia_diem_gia` CHECK (`gia_thap_nhat` >= 0 AND `gia_cao_nhat` >= `gia_thap_nhat`),
  CONSTRAINT `chk_dia_diem_toa_do` CHECK (`vi_do` BETWEEN -90.00000000 AND 90.00000000 AND `kinh_do` BETWEEN -180.00000000 AND 180.00000000),
  CONSTRAINT `fk_dia_diem_danh_muc` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BANG HINH_ANH_DIA_DIEM
DROP TABLE IF EXISTS `hinh_anh_dia_diem`;
CREATE TABLE `hinh_anh_dia_diem` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dia_diem_id` BIGINT UNSIGNED NOT NULL,
  `duong_dan_anh` VARCHAR(255) NOT NULL,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_hinh_anh_dia_diem` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BANG GOI_VE
DROP TABLE IF EXISTS `goi_ve`;
CREATE TABLE `goi_ve` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dia_diem_id` BIGINT UNSIGNED NOT NULL,
  `ten_goi_ve` VARCHAR(150) NOT NULL,
  `mo_ta` TEXT DEFAULT NULL,
  `gia_nguoi_lon` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `gia_tre_em` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `trang_thai` ENUM('dang_ban', 'tam_dung') NOT NULL DEFAULT 'dang_ban',
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_goi_ve_gia` CHECK (`gia_nguoi_lon` >= 0 AND `gia_tre_em` >= 0),
  CONSTRAINT `fk_goi_ve_dia_diem` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BANG TON_KHO_VE
DROP TABLE IF EXISTS `ton_kho_ve`;
CREATE TABLE `ton_kho_ve` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `goi_ve_id` BIGINT UNSIGNED NOT NULL,
  `ngay_su_dung` DATE NOT NULL,
  `tong_so_ve` INT UNSIGNED NOT NULL DEFAULT 500,
  `so_ve_con_lai` INT UNSIGNED NOT NULL DEFAULT 500,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_goi_ve_ngay` (`goi_ve_id`, `ngay_su_dung`),
  CONSTRAINT `chk_ton_kho_hop_le` CHECK (`so_ve_con_lai` <= `tong_so_ve`),
  CONSTRAINT `fk_ton_kho_goi_ve` FOREIGN KEY (`goi_ve_id`) REFERENCES `goi_ve` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BANG DON_HANG (Tích hợp E-Voucher)
DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE `don_hang` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_don_hang` VARCHAR(30) NOT NULL UNIQUE,
  `ma_qr` VARCHAR(255) DEFAULT NULL,
  `nguoi_dung_id` BIGINT UNSIGNED NOT NULL,
  `ten_khach_hang` VARCHAR(100) NOT NULL,
  `email_khach_hang` VARCHAR(100) NOT NULL,
  `sdt_khach_hang` VARCHAR(15) NOT NULL,
  `tong_tien` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `phuong_thuc_thanh_toan` ENUM('vnpay', 'momo', 'chuyen_khoan', 'tai_quay') NOT NULL,
  `trang_thai_thanh_toan` ENUM('cho_thanh_toan', 'da_thanh_toan', 'that_bai', 'da_hoan_tien') NOT NULL DEFAULT 'cho_thanh_toan',
  `trang_thai_don_hang` ENUM('moi_tao', 'da_xac_nhan', 'hoan_thanh', 'da_huy') NOT NULL DEFAULT 'moi_tao',
  `ghi_chu` TEXT DEFAULT NULL,
  `ngay_xoa` TIMESTAMP NULL DEFAULT NULL,
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_don_hang_tong_tien` CHECK (`tong_tien` >= 0),
  CONSTRAINT `fk_don_hang_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BANG CHI_TIET_DON_HANG (Thành tiền tính tự động - Store Generated)
DROP TABLE IF EXISTS `chi_tiet_don_hang`;
CREATE TABLE `chi_tiet_don_hang` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `don_hang_id` BIGINT UNSIGNED NOT NULL,
  `ton_kho_ve_id` BIGINT UNSIGNED NOT NULL,
  `ngay_tham_quan` DATE NOT NULL,
  `so_luong_nguoi_lon` INT UNSIGNED NOT NULL DEFAULT 0,
  `so_luong_tre_em` INT UNSIGNED NOT NULL DEFAULT 0,
  `don_gia_nguoi_lon` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `don_gia_tre_em` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `thanh_tien` DECIMAL(12, 2) GENERATED ALWAYS AS ((`so_luong_nguoi_lon` * `don_gia_nguoi_lon`) + (`so_luong_tre_em` * `don_gia_tre_em`)) STORED,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_chi_tiet_so_luong` CHECK ((`so_luong_nguoi_lon` + `so_luong_tre_em`) > 0),
  CONSTRAINT `chk_chi_tiet_don_gia` CHECK (`don_gia_nguoi_lon` >= 0 AND `don_gia_tre_em` >= 0),
  CONSTRAINT `fk_chi_tiet_don_hang` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_chi_tiet_ton_kho` FOREIGN KEY (`ton_kho_ve_id`) REFERENCES `ton_kho_ve` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. BANG GIAO_DICH_THANH_TOAN
DROP TABLE IF EXISTS `giao_dich_thanh_toan`;
CREATE TABLE `giao_dich_thanh_toan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `don_hang_id` BIGINT UNSIGNED NOT NULL,
  `cong_thanh_toan` ENUM('vnpay', 'momo', 'chuyen_khoan', 'tai_quay') NOT NULL,
  `ma_giao_dich` VARCHAR(100) DEFAULT NULL,
  `so_tien` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `loai_tien_te` VARCHAR(10) NOT NULL DEFAULT 'VND',
  `du_lieu_phan_hoi` TEXT DEFAULT NULL,
  `trang_thai` ENUM('dang_cho', 'thanh_cong', 'that_bai', 'da_huy', 'da_hoan_tien') NOT NULL DEFAULT 'dang_cho',
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cong_ma_giao_dich` (`cong_thanh_toan`, `ma_giao_dich`),
  CONSTRAINT `chk_giao_dich_so_tien` CHECK (`so_tien` >= 0),
  CONSTRAINT `fk_giao_dich_don_hang` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. BANG DANH_GIA
DROP TABLE IF EXISTS `danh_gia`;
CREATE TABLE `danh_gia` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` BIGINT UNSIGNED NOT NULL,
  `dia_diem_id` BIGINT UNSIGNED NOT NULL,
  `so_sao` TINYINT UNSIGNED NOT NULL,
  `noi_dung` TEXT NOT NULL,
  `trang_thai` ENUM('da_duyet', 'da_an') NOT NULL DEFAULT 'da_duyet',
  `ngay_tao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_mot_danh_gia_mot_nguoi` (`nguoi_dung_id`, `dia_diem_id`),
  CONSTRAINT `chk_danh_gia_so_sao` CHECK (`so_sao` BETWEEN 1 AND 5),
  CONSTRAINT `fk_danh_gia_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_danh_gia_dia_diem` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

```

---

## 4. Các chức năng/Logic cốt lõi đã thiết kế hoàn chỉnh

### 4.1. Kết nối Database PDO chuẩn an toàn (`app/Config/Database.php`)

```php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $db   = $_ENV['DB_DATABASE'] ?? 'nhatrang_tourism';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

```

### 4.2. Logic đặt vé & Trừ tồn kho nguyên tử (`app/Services/BookingService.php`)

Chống bán vượt số vé cho phép (Overbooking) và xử lý đồng thời (Race condition) qua Transaction:

```php
namespace App\Services;

use App\Config\Database;
use PDO;
use Exception;

class BookingService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function datVe(
        int $nguoiDungId,
        int $tonKhoId,
        int $soLuongNguoiLon,
        int $soLuongTreEm,
        float $giaNguoiLon,
        float $giaTreEm,
        string $ngayThamQuan,
        array $thongTinKhach
    ): string {
        $tongSoLuong = $soLuongNguoiLon + $soLuongTreEm;
        if ($tongSoLuong <= 0) {
            throw new Exception("Số lượng vé phải lớn hơn 0.");
        }

        $this->db->beginTransaction();
        try {
            // 1. Trừ tồn kho an toàn (Atomic Update)
            $stmtStock = $this->db->prepare("
                UPDATE ton_kho_ve 
                SET so_ve_con_lai = so_ve_con_lai - :so_luong 
                WHERE id = :ton_kho_id AND so_ve_con_lai >= :so_luong
            ");
            $stmtStock->execute([
                'so_luong'    => $tongSoLuong,
                'ton_kho_id' => $tonKhoId
            ]);

            if ($stmtStock->rowCount() === 0) {
                $this->db->rollBack();
                throw new Exception("Ngày được chọn đã hết vé hoặc không đủ số lượng yêu cầu.");
            }

            // 2. Tính tổng tiền phía máy chủ (Không tin tưởng dữ liệu client gửi)
            $tongTien = ($soLuongNguoiLon * $giaNguoiLon) + ($soLuongTreEm * $giaTreEm);
            $maDonHang = 'NT' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));

            // 3. Tạo bản ghi đơn hàng
            $stmtOrder = $this->db->prepare("
                INSERT INTO don_hang (
                    ma_don_hang, nguoi_dung_id, ten_khach_hang, email_khach_hang, 
                    sdt_khach_hang, tong_tien, phuong_thuc_thanh_toan, 
                    trang_thai_thanh_toan, trang_thai_don_hang
                ) VALUES (
                    :ma_don, :nguoi_dung_id, :ten, :email, 
                    :sdt, :tong_tien, :phuong_thuc, 
                    'cho_thanh_toan', 'moi_tao'
                )
            ");
            $stmtOrder->execute([
                'ma_don'        => $maDonHang,
                'nguoi_dung_id' => $nguoiDungId,
                'ten'           => $thongTinKhach['ten'],
                'email'         => $thongTinKhach['email'],
                'sdt'           => $thongTinKhach['sdt'],
                'tong_tien'     => $tongTien,
                'phuong_thuc'   => $thongTinKhach['phuong_thuc']
            ]);
            $donHangId = (int)$this->db->lastInsertId();

            // 4. Tạo chi tiết đơn hàng (thanh_tien tự sinh trong DB)
            $stmtItem = $this->db->prepare("
                INSERT INTO chi_tiet_don_hang (
                    don_hang_id, ton_kho_ve_id, ngay_tham_quan, 
                    so_luong_nguoi_lon, don_gia_nguoi_lon, 
                    so_luong_tre_em, don_gia_tre_em
                ) VALUES (
                    :don_hang_id, :ton_kho_id, :ngay_tham_quan, 
                    :sl_nl, :gia_nl, :sl_te, :gia_te
                )
            ");
            $stmtItem->execute([
                'don_hang_id'    => $donHangId,
                'ton_kho_id'     => $tonKhoId,
                'ngay_tham_quan' => $ngayThamQuan,
                'sl_nl'          => $soLuongNguoiLon,
                'gia_nl'         => $giaNguoiLon,
                'sl_te'          => $soLuongTreEm,
                'gia_te'         => $giaTreEm
            ]);

            $this->db->commit();
            return $maDonHang;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

```

### 4.3. Logic kiểm tra đăng nhập trước khi Checkout (`app/Middlewares/AuthMiddleware.php`)

```php
namespace App\Middlewares;

class AuthMiddleware
{
    public static function checkCustomer(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['flash_error'] = 'Bạn cần đăng nhập tài khoản để tiến hành đặt vé.';
            header('Location: /login.php');
            exit;
        }
    }

    public static function checkAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Truy cập bị từ chối: Yêu cầu quyền Quản trị viên.");
        }
    }
}

```

### 4.4. Logic quản lý đơn & Duyệt E-Voucher của Admin (`app/Controllers/Admin/OrderManageController.php`)

```php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Middlewares\AuthMiddleware;
use Exception;

class OrderManageController
{
    public function capNhatTrangThai(int $donHangId, string $trangThaiMoi): bool
    {
        AuthMiddleware::checkAdmin();

        $danhSachHopLe = ['moi_tao', 'da_xac_nhan', 'hoan_thanh', 'da_huy'];
        if (!in_array($trangThaiMoi, $danhSachHopLe)) {
            throw new Exception("Trạng thái đơn hàng không hợp lệ.");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE don_hang 
            SET trang_thai_don_hang = :trang_thai 
            WHERE id = :id AND ngay_xoa IS NULL
        ");
        return $stmt->execute([
            'trang_thai' => $trangThaiMoi,
            'id'         => $donHangId
        ]);
    }
}

```

---

## 5. Các chức năng còn dang dở và Kế hoạch triển khai tiếp theo

Theo thứ tự ưu tiên phụ thuộc dữ liệu (Data Dependency), lộ trình 5 thành viên cần thực hiện:

### Nhóm việc 1: Khởi tạo dữ liệu mẫu và Master Layout (Giai đoạn nền)

* [ ] Nhập file SQL `database/schema_vi.sql` vào phpMyAdmin.
* [ ] Viết file seed `database/seeds/sample_data.sql` chèn ít nhất:
* 1 tài khoản Admin, 2 tài khoản Khách hàng.
* 6 danh mục (Hải sản, Món điểm tâm Nha Trang, Đồ ăn vặt; Tour biển đảo, Di tích tâm linh, Vui chơi giải trí).
* 15 địa điểm thực tế tại Nha Trang có tọa độ GPS chính xác (VinWonders, Tháp Bà Ponagar, Chùa Long Sơn, Hòn Mun, Hải sản Bờ Kè, Bánh căn Tháp Bà...).


* [ ] Dựng khung giao diện chung (Header, Footer, Navbar) có tích hợp CDN Leaflet.js.

### Nhóm việc 2: Hiển thị Bản đồ số Nha Trang & Bộ lọc địa điểm

* [ ] Viết AJAX/API lấy danh sách địa điểm theo định dạng JSON gồm: `id`, `ten_dia_diem`, `vi_do`, `kinh_do`, `anh_dai_dien`, `gia_thap_nhat`, `gia_cao_nhat`.
* [ ] Khởi tạo bản đồ `L.map('map').setView([12.2475, 109.1967], 13)` (tọa độ trung tâm thành phố Nha Trang).
* [ ] Duyệt danh sách tọa độ cắm Marker và gán Popup thông tin tóm tắt có nút dẫn đến trang chi tiết.
* [ ] Xây dựng bộ lọc kết hợp: Lọc theo loại danh mục (`am_thuc` hoặc `du_lich`) và theo khung giá.

### Nhóm việc 3: Tích hợp Cổng thanh toán & Xuất E-Voucher QR

* [ ] Viết hàm tạo ảnh QR code từ chuỗi `ma_don_hang` khi đơn chuyển sang `da_thanh_toan`.
* [ ] Xây dựng module thanh toán giả lập (VNPay Sandbox hoặc màn hình quét mã QR chuyển khoản có nút "Xác nhận đã chuyển khoản").
* [ ] Trang `views/client/booking/voucher.php`: Hiển thị phiếu E-Voucher gồm thông tin đơn hàng, số lượng vé, mã QR và nút "In phiếu / Lưu PDF".
* [ ] Tích hợp PHPMailer gửi thông báo phiếu đặt vé về email người nhận.

### Nhóm việc 4: Admin Portal & Báo cáo thống kê

* [ ] Màn hình CRUD danh mục, địa điểm, tải lên nhiều ảnh cho địa điểm.
* [ ] Màn hình cấu hình hạn mức vé theo ngày (`ton_kho_ve`).
* [ ] Giao diện tra cứu đơn hàng: Bộ lọc tìm kiếm nhanh theo `ma_don_hang` hoặc `sdt_khach_hang` để nhân viên lễ tân bấm nút "Hoàn thành" khi khách tới nhận vé.
* [ ] Dashboard thống kê doanh thu và lượt đặt vé bằng **Chart.js**.

---

## 6. Những quy tắc bất di bất dịch (Constraints) khi code tiếp

Mọi thành viên trong nhóm 5 người bắt buộc phải tuân thủ nghiêm ngặt 8 quy tắc sau để mã nguồn không bị xung đột, thất thoát dữ liệu hoặc vi phạm an toàn thông tin:

1. **Tuyệt đối không tin tưởng giá tiền từ phía Trình duyệt (Frontend):**
* Tham số gửi lên từ Client chỉ gồm: `ton_kho_id`, `so_luong_nguoi_lon`, `so_luong_tre_em`, `ngay_tham_quan`.
* Đơn giá và tổng tiền **bắt buộc** phải được Backend truy vấn từ bảng `goi_ve` và tính toán lại trước khi lưu vào CSDL. Cột `thanh_tien` trong CSDL là `STORED GENERATED COLUMN`, tuyệt đối không cố gắng chèn giá trị thủ công vào cột này.


2. **Bắt buộc dùng Database Transaction khi phát sinh Đơn hàng:**
* Mọi thao tác gồm: Trừ `so_ve_con_lai`, tạo bản ghi `don_hang` và tạo `chi_tiet_don_hang` phải nằm trong một khối `beginTransaction()` -> `commit()`. Nếu bất kỳ thao tác nào thất bại, bắt buộc phải `rollBack()`.


3. **Trừ tồn kho bằng câu lệnh Atomic Update:**
* Không dùng giải pháp đọc số lượng ra biến PHP rồi tính toán rồi mới Update. Bắt buộc viết câu lệnh SQL có điều kiện kiểm tra trực tiếp:
`UPDATE ton_kho_ve SET so_ve_con_lai = so_ve_con_lai - :qty WHERE id = :id AND so_ve_con_lai >= :qty;`
Sau đó kiểm tra `rowCount() === 0` để phát hiện hết vé.


4. **Không xóa cứng đơn hàng (Cấm Hard Delete):**
* Bảng `don_hang` có cột `ngay_xoa` dành riêng cho Xóa mềm (Soft Delete).
* Khóa ngoại của `giao_dich_thanh_toan` là `ON DELETE RESTRICT`, không được phép sửa thành `CASCADE` để bảo toàn lịch sử giao dịch và đối soát tài chính.


5. **Ràng buộc đăng nhập tại tầng Controller (Server-side Enforcement):**
* Không chỉ ẩn nút trên giao diện, tại hàm xử lý đặt vé của PHP phải có Middleware kiểm tra `isset($_SESSION['user_id'])`. Nếu chưa có, lập tức ngắt phiên và chuyển hướng.


6. **Bảo mật SQL Injection và Mật khẩu:**
* 100% câu truy vấn có tham số phải dùng PDO Prepared Statements (`$stmt->prepare()` và `$stmt->execute()`). Cấm tuyệt đối việc ghép chuỗi SQL trực tiếp (`"SELECT * WHERE id = " . $_GET['id']`).
* Mật khẩu người dùng phải được mã hóa qua hàm chuẩn `password_hash($matKhau, PASSWORD_BCRYPT)`.


7. **Quy tắc phân nhánh Git (Forking Workflow):**
* Thành viên **không bao giờ** commit hay push trực tiếp lên nhánh `main` hoặc `develop` trên kho gốc (Upstream).
* Mọi tính năng phải xuất phát từ nhánh riêng `feature/ten-tinh-nang`, push lên Fork cá nhân (`origin`) rồi tạo Pull Request vào nhánh `develop` của Trưởng nhóm để kiểm tra chéo trước khi merge.


8. **Độc lập cấu hình môi trường qua `.env`:**
* Không bao giờ commit file `.env` lên GitHub. Mọi thông số kết nối Database, cổng gửi mail phải đưa vào `.env.example` dạng mẫu để từng thành viên tự thiết lập trên máy cục bộ của mình.
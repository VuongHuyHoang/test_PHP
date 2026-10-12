# Hệ Thống Quảng Bá Địa Điểm Ẩm Thực và Du Lịch Nha Trang

> Dự án mã nguồn mở phục vụ du khách và người dân địa phương tại Nha Trang tra cứu, định vị các quán ăn đặc sản và địa điểm du lịch nổi tiếng trên bản đồ số tương tác (Leaflet.js & OpenStreetMap), kèm phân hệ đặt vé vui chơi, tour đảo trực tuyến với mã xác nhận E-Voucher QR.

---

## 1. Công nghệ sử dụng

* **Backend:** PHP 8.2 (Mô hình MVC chuẩn, kết nối PDO, chuẩn bảo mật CSRF & SQL Injection).
* **Cơ sở dữ liệu:** MariaDB 10.4 / MySQL 8.0+ (10 bảng chuẩn 3NF, ràng buộc CHECK, STORED GENERATED COLUMN, Atomic Update).
* **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5, Bootstrap Icons.
* **Bản đồ tương tác:** Leaflet.js + OpenStreetMap (Tọa độ trung tâm TP. Nha Trang: `12.2475, 109.1967`).
* **Hạ tầng cục bộ:** XAMPP (Apache + MariaDB).

---

## 2. Cấu trúc thư mục

```text
nhatrang-tourism-web/
├── .env.example                # Cấu hình môi trường mẫu
├── .env                        # Cấu hình môi trường cục bộ
├── .gitignore                  # Bỏ qua file rác và thông tin nhạy cảm
├── README.md                   # Hướng dẫn dự án
├── composer.json               # Cấu hình thông tin dự án & Autoload PSR-4
├── database/
│   ├── schema_vi.sql           # DDL 10 bảng CSDL tiếng Việt
│   ├── init_db.php             # Script tự động import schema & seed
│   └── seeds/
│       └── sample_data.sql     # 15 địa điểm Nha Trang, tài khoản & vé mẫu
├── public/                     # Thư mục gốc Document Root
│   ├── index.php               # Front Controller tiếp nhận mọi request
│   ├── .htaccess               # URL rewrite sạch
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── js/main.js          # Khởi tạo bản đồ Leaflet.js
│   │   └── images/
│   └── uploads/                # Ảnh upload
├── app/
│   ├── bootstrap.php           # Khởi động app, load .env & PSR-4 autoloader
│   ├── Router.php              # Bộ định tuyến Clean URL
│   ├── Config/
│   │   └── Database.php        # Kết nối PDO Singleton
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── LocationController.php
│   │   ├── AuthController.php
│   │   ├── BookingController.php
│   │   └── Admin/
│   │       ├── DashboardController.php      # Thống kê Chart.js
│   │       ├── LocationManageController.php # CRUD địa điểm & gallery
│   │       ├── TicketManageController.php   # Quản lý gói vé & tồn kho
│   │       └── OrderManageController.php    # Soát vé lễ tân quầy
│   ├── Models/
│   │   ├── NguoiDung.php
│   │   ├── DanhMuc.php
│   │   ├── DiaDiem.php
│   │   ├── GoiVe.php
│   │   ├── TonKhoVe.php
│   │   ├── DonHang.php
│   │   └── DanhGia.php
│   ├── Middlewares/
│   │   └── AuthMiddleware.php
│   ├── Services/
│   │   └── BookingService.php  # Atomic Update & Transaction đặt vé
│   └── Helpers/
│       ├── FormatHelper.php
│       ├── QrHelper.php        # Sinh & lưu mã QR E-Voucher cục bộ
│       └── MailHelper.php      # Gửi & ghi log email xác nhận
└── views/
    ├── layouts/
    │   ├── header.php, navbar.php, footer.php
    │   ├── admin_layout.php, admin_footer.php
    │   └── 404.php
    ├── client/
    │   ├── home/index.php
    │   ├── locations/index.php, detail.php
    │   ├── auth/login.php, register.php
    │   └── booking/form.php, payment.php, voucher.php
    └── admin/
        ├── dashboard/index.php # Chart.js charts
        ├── locations/index.php, form.php
        ├── tickets/index.php
        └── orders/index.php    # Soát vé tại quầy
```

---

## 3. Hướng dẫn cài đặt & Khởi chạy

### Bước 1: Khởi động MySQL trên XAMPP
Mở **XAMPP Control Panel** và nhấn **Start** tại mục **MySQL** (hoặc cả **Apache**).

### Bước 2: Tự động khởi tạo Database & Nạp dữ liệu mẫu
Mở terminal trong thư mục dự án và chạy lệnh sau (sử dụng PHP từ XAMPP):
```bash
C:\xampp\php\php.exe database/init_db.php
```
Script sẽ tự động tạo database `nhatrang_tourism`, import 10 bảng và nạp sẵn 15 địa điểm thực tế Nha Trang kèm tài khoản mẫu.

### Bước 3: Khởi chạy Web Server
Chạy PHP Built-in Server:
```bash
C:\xampp\php\php.exe -S localhost:8000 -t public
```
Sau đó mở trình duyệt truy cập: [http://localhost:8000](http://localhost:8000)

*(Hoặc nếu để trong thư mục `C:\xampp\htdocs\nhatrang-tourism-web`, truy cập: `http://localhost/nhatrang-tourism-web/public`)*

---

## 4. Tài khoản thử nghiệm có sẵn

| Vai trò | Email | Mật khẩu | Chức năng |
| :--- | :--- | :--- | :--- |
| **Quản Trị Viên (Admin)** | `admin@nhatrangtourism.vn` | `Admin@123` | Dashboard thống kê, Tra cứu & Duyệt đơn E-Voucher tại quầy |
| **Khách Hàng** | `nguyenan@gmail.com` | `Khach@123` | Khám phá bản đồ, xem chi tiết quán ăn, đặt vé tour/vui chơi |
| **Khách Hàng 2** | `tranmai@gmail.com` | `Khach@123` | Đánh giá địa điểm, xem vé đã đặt |

---

## 5. Quy tắc Kỹ thuật Bất Di Bất Dịch (Constraints)

1. **Atomic Update:** Trừ tồn kho vé bằng lệnh SQL trực tiếp `UPDATE ton_kho_ve SET so_ve_con_lai = so_ve_con_lai - :qty WHERE id = :id AND so_ve_con_lai >= :qty`.
2. **Database Transaction:** Bắt buộc bọc `beginTransaction()` -> `commit()` khi phát sinh đơn hàng.
3. **Giá vé tính tại Server:** Không tin tưởng giá từ Frontend gửi lên. Cột `thanh_tien` là `STORED GENERATED COLUMN`.
4. **Không xóa cứng (Soft Delete):** Đơn hàng chỉ cập nhật cột `ngay_xoa`. Khóa ngoại giao dịch là `RESTRICT`.
5. **Server-side Auth:** Chặn quyền tại Controller/Middleware qua `AuthMiddleware`.
6. **Mật khẩu an toàn:** Dùng `password_hash($pass, PASSWORD_BCRYPT)`.


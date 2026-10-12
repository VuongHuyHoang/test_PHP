-- =============================================================================
-- SAMPLE DATA FOR HE THONG QUANG BA AM THUC VA DU LICH NHA TRANG
-- =============================================================================

USE `nhatrang_tourism`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. SEED NGUOI_DUNG
-- Mat khau admin: Admin@123 | Mat khau khach hang: Khach@123
INSERT INTO `nguoi_dung` (`id`, `ho_ten`, `email`, `mat_khau`, `so_dien_thoai`, `vai_tro`, `trang_thai`) VALUES
(1, 'Quản Trị Viên Hệ Thống', 'admin@nhatrangtourism.vn', '$2y$10$.vphq0wtc6n.xwBo.8xNt.hl8Ez7FbBCpVyYrFgeQBQK5qutl5PIu', '0901234567', 'admin', 'hoat_dong'),
(2, 'Nguyễn Văn An', 'nguyenan@gmail.com', '$2y$10$x7zpDZ8OLZbstS97oCB85ei5vhqb78/aPGDpu0AINjBTaBRWSveaq', '0912345678', 'khach_hang', 'hoat_dong'),
(3, 'Trần Thị Mai', 'tranmai@gmail.com', '$2y$10$x7zpDZ8OLZbstS97oCB85ei5vhqb78/aPGDpu0AINjBTaBRWSveaq', '0987654321', 'khach_hang', 'hoat_dong');

-- 2. SEED DANH_MUC (6 danh muc am thuc & du lich)
INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `duong_dan`, `loai_danh_muc`, `bieu_tuong`) VALUES
(1, 'Hải sản tươi sống Nha Trang', 'hai-san-tuoi-song', 'am_thuc', 'bi-water'),
(2, 'Món điểm tâm & Đặc sản truyền thống', 'mon-diem-tam-dac-san', 'am_thuc', 'bi-cup-hot'),
(3, 'Đồ ăn vặt & Tráng miệng phố biển', 'do-an-vat-trang-mieng', 'am_thuc', 'bi-egg-fried'),
(4, 'Tour vịnh đảo & Lặn ngắm san hô', 'tour-vinh-dao-lan-bien', 'du_lich', 'bi-compass'),
(5, 'Di tích lịch sử & Văn hóa tâm linh', 'di-tich-lich-su-tam-linh', 'du_lich', 'bi-bank'),
(6, 'Khu vui chơi giải trí & Công viên', 'khu-vui-choi-cong-vien', 'du_lich', 'bi-sun');

-- 3. SEED DIA_DIEM (15 dia diem thuc te toa do GPS chinh xac tai Nha Trang)
INSERT INTO `dia_diem` (`id`, `danh_muc_id`, `ten_dia_diem`, `duong_dan`, `dia_chi`, `vi_do`, `kinh_do`, `gia_thap_nhat`, `gia_cao_nhat`, `gio_mo_cua`, `gio_dong_cua`, `anh_dai_dien`, `mo_ta_ngan`, `noi_dung_chi_tiet`, `co_ban_ve`, `trang_thai`, `luot_xem`) VALUES
(1, 6, 'VinWonders Nha Trang', 'vinwonders-nha-trang', 'Đảo Hòn Tre, Phường Vĩnh Nguyên, TP. Nha Trang', 12.21805600, 109.24305600, 560000.00, 950000.00, '08:00:00', '20:00:00', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80', 'Công viên giải trí của những kỷ lục trên đảo Hòn Tre với hàng trăm trò chơi đỉnh cao.', 'VinWonders Nha Trang là thiên đường giải trí đẳng cấp quốc tế, sở hữu cáp treo vượt biển kỷ lục, công viên nước trên bờ biển và show diễn thực cảnh Tata Show tráng lệ.', 1, 'hien_thi', 1520),

(2, 5, 'Tháp Bà Ponagar', 'thap-ba-ponagar', 'Đường 2 Tháng 4, Phường Vĩnh Phước, TP. Nha Trang', 12.26527800, 109.19583300, 30000.00, 30000.00, '06:00:00', '18:00:00', 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=800&q=80', 'Quần thể đền tháp Chăm Pa cổ kính bậc nhất miền Trung với kiến trúc gạch nung kỳ vĩ.', 'Tháp Bà Ponagar là di tích lịch sử văn hóa quốc gia đặc biệt, được xây dựng từ thế kỷ 8 đến thế kỷ 13, thờ nữ thần Ponagar - Mẹ xứ sở của người Chăm.', 1, 'hien_thi', 980),

(3, 5, 'Chùa Long Sơn (Chùa Phật Trắng)', 'chua-long-son', '20 Đường 23 Tháng 10, Phường Phương Sơn, TP. Nha Trang', 12.25055600, 109.18027800, 0.00, 0.00, '06:00:00', '21:00:00', 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=800&q=80', 'Ngôi chùa cổ kính với tượng Kim Thân Phật Tổ khổng lồ ngự trên đỉnh đồi Trại Thủy.', 'Chùa Long Sơn được xây dựng từ năm 1886, nổi tiếng với bức tượng Phật trắng ngồi thuyết pháp uy nghi cao 24 mét, từ đây có thể ngắm toàn cảnh thành phố Nha Trang.', 0, 'hien_thi', 820),

(4, 4, 'Khu bảo tồn Biển Hòn Mun', 'khu-bao-ton-hon-mun', 'Vịnh Nha Trang, TP. Nha Trang', 12.16722200, 109.30333300, 250000.00, 650000.00, '07:30:00', '16:30:00', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80', 'Thiên đường lặn biển với rạn san hô phong phú và hệ sinh thái đa dạng nhất Đông Nam Á.', 'Hòn Mun là khu bảo tồn sinh vật biển đầu tiên tại Việt Nam, sở hữu hơn 1.500 loài san hô và sinh vật biển quý hiếm. Địa điểm lý tưởng để tham gia tour lặn scuba diving và đi bộ dưới biển.', 1, 'hien_thi', 1140),

(5, 5, 'Viện Hải Dương Học Nha Trang', 'vien-hai-duong-hoc', 'Số 1 Cầu Đá, Phường Vĩnh Nguyên, TP. Nha Trang', 12.20722200, 109.21555600, 40000.00, 40000.00, '06:00:00', '18:00:00', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'Cơ sở nghiên cứu biển lâu đời nhất Đông Dương với bộ xương cá voi lưng gù khổng lồ.', 'Thành lập từ năm 1922, Viện Hải dương học lưu giữ hơn 20.000 mẫu sinh vật biển của hơn 4.000 loài cùng bể nuôi thủy cung sống động.', 1, 'hien_thi', 750),

(6, 4, 'Bãi Tranh Hòn Miễu', 'bai-tranh-nha-trang', 'Đảo Trí Nguyên, Vịnh Nha Trang', 12.19555600, 109.23194400, 50000.00, 350000.00, '08:00:00', '17:00:00', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80', 'Bãi biển độc đáo với một bên là bờ cát trắng mịn, một bên là bãi sỏi tự nhiên.', 'Bãi Tranh mang nét đẹp hoang sơ, cung cấp nhiều trò chơi mạo hiểm như dù bay, mô tô nước, chèo thuyền kayak và phao chuối.', 1, 'hien_thi', 620),

(7, 1, 'Hải Sản Bờ Kè Nha Trang', 'hai-san-bo-ke', '42-52 Cù Huân, Phường Vĩnh Thọ, TP. Nha Trang', 12.25750000, 109.19833300, 150000.00, 600000.00, '10:00:00', '23:00:00', 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=800&q=80', 'Hải sản tươi sống bên bờ sông Cái, thưởng thức tôm hùm, cua huỳnh đế ngắm cầu Trần Phú.', 'Khu hải sản Bờ Kè nổi tiếng với nguồn hải sản đánh bắt trong ngày tươi roi rói, chế biến theo khẩu vị miền Trung đậm đà với mức giá bình dân đến cao cấp.', 0, 'hien_thi', 1310),

(8, 2, 'Bánh Căn Tháp Bà 47', 'banh-can-thap-ba-47', '47 Tháp Bà, Phường Vĩnh Thọ, TP. Nha Trang', 12.26416700, 109.19694400, 30000.00, 80000.00, '15:00:00', '22:00:00', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=800&q=80', 'Bánh căn giòn rụm với nhân tôm mực tươi ngon kèm nước mắm nêm xíu mại béo ngậy.', 'Quán bánh căn đượm lửa than hồng, vỏ bánh đổ khuôn đất nung giòn đáy mềm xốp phía trên, ăn kèm hành lá và mỡ hành thơm nức.', 0, 'hien_thi', 890),

(9, 2, 'Nem Nướng Đặng Văn Quyên', 'nem-nuong-dang-van-quyen', '16A Lãn Ông, Phường Xương Huân, TP. Nha Trang', 12.25305600, 109.19305600, 45000.00, 120000.00, '07:30:00', '21:30:00', 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80', 'Thương hiệu nem nướng Ninh Hòa trứ danh với nước chấm tương đậu bí truyền sánh mịn.', 'Mẹt nem nướng gồm những que nem thịt heo thơm lừng, bánh tráng chiên giòn, xoài xanh, dưa leo chua và rổ rau thơm xanh mát cuốn chặt tay.', 0, 'hien_thi', 1450),

(10, 2, 'Bún Cá Sứa Năm Beo', 'bun-ca-sua-nam-beo', 'B2 Chung cư Chợ Đầm, Phan Bội Châu, Phường Xương Huân, TP. Nha Trang', 12.25527800, 109.19138900, 35000.00, 60000.00, '06:00:00', '21:00:00', 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=800&q=80', 'Tô bún chả cá thu dai ngọt cùng thịt sứa giòn sần sật trong nước dùng ngọt thanh vị cá cờ.', 'Quán Năm Beo là điểm ăn sáng quen thuộc của người địa phương gần chợ Đầm, mang trọn hương vị mộc mạc và tươi lành của biển Nha Trang.', 0, 'hien_thi', 920),

(11, 1, 'Quán Ốc Xuân Anh', 'quan-oc-xuan-anh', '9C Tháp Bà, Phường Vĩnh Thọ, TP. Nha Trang', 12.26388900, 109.19777800, 50000.00, 200000.00, '16:00:00', '23:30:00', 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80', 'Thiên đường các loại ốc hương, sò huyết, ghẹ rang me nức tiếng trên phố ẩm thực Tháp Bà.', 'Quán ốc bình dân tấp nập với phong cách chế biến phong phú từ hấp xả, nướng mỡ hành tới rang muối ớt đậm đà.', 0, 'hien_thi', 770),

(12, 2, 'Cơm Gà Trâm Anh', 'com-ga-tram-anh', '10 Bà Triệu, Phường Phương Sài, TP. Nha Trang', 12.24972200, 109.19194400, 45000.00, 90000.00, '07:00:00', '20:30:00', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80', 'Hạt cơm nấu nước luộc gà óng vàng dẻo bùi, thịt gà ta xé phay ngọt mềm ăn cùng sốt bơ trứng đặc trưng.', 'Cơm gà Trâm Anh là một trong những quán cơm gà lâu đời và nổi tiếng nhất thành phố, làm say lòng du khách bởi chén canh gà đậm vị và đĩa hành ngâm giòn chua.', 0, 'hien_thi', 1050),

(13, 3, 'Chợ Đầm Nha Trang', 'cho-dam-nha-trang', 'Bến Chợ, Phường Vạn Thạnh, TP. Nha Trang', 12.25416700, 109.19083300, 15000.00, 300000.00, '05:00:00', '18:30:00', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80', 'Biểu tượng thương mại của Nha Trang với khu chợ tròn độc đáo, bán đủ loại hải sản khô và đồ ăn vặt.', 'Chợ Đầm là trung tâm mua sắm quà lưu niệm, hải sản khô (mực một nắng, cá chỉ vàng, rong biển) và trải nghiệm ẩm thực vỉa hè đa dạng.', 0, 'hien_thi', 1600),

(14, 5, 'Nhà Thờ Đá (Nhà Thờ Núi)', 'nha-tho-nui-nha-trang', '01 Thái Nguyên, Phường Phước Tân, TP. Nha Trang', 12.24666700, 109.18861100, 0.00, 0.00, '05:30:00', '17:30:00', 'https://images.unsplash.com/photo-1548625361-195fe57871b6?auto=format&fit=crop&w=800&q=80', 'Công trình kiến trúc Gothic Pháp cổ kính tọa lạc trên ngọn đồi nhỏ ngay ngã sáu trung tâm.', 'Nhà thờ Chánh tòa Kitô Vua được xây dựng từ năm 1928 bằng đá chẻ, nổi bật với tháp chuông uy nghiêm và những ô kính màu nghệ thuật tuyệt đẹp.', 0, 'hien_thi', 1180),

(15, 5, 'Danh Thắng Hòn Chồng', 'danh-thang-hon-chong', 'Đường Phạm Văn Đồng, Phường Vĩnh Phước, TP. Nha Trang', 12.27250000, 109.20527800, 30000.00, 30000.00, '07:00:00', '19:00:00', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'Quần thể khối đá hoa cương tự nhiên xếp chồng lên nhau kỳ ảo vươn ra biển Đông.', 'Hòn Chồng lưu giữ dấu vết bàn tay khổng lồ kỳ bí trên phiến đá lớn, nơi du khách có thể nghe biểu diễn nhạc cụ dân tộc truyền thống bên bờ sóng vỗ.', 1, 'hien_thi', 840);

-- 4. SEED GOI_VE (Cac goi ve cho dia diem co_ban_ve = 1)
INSERT INTO `goi_ve` (`id`, `dia_diem_id`, `ten_goi_ve`, `mo_ta`, `gia_nguoi_lon`, `gia_tre_em`, `trang_thai`) VALUES
(1, 1, 'Vé Tiêu Chuẩn VinWonders Nha Trang (Gồm Cáp Treo 2 Chiều)', 'Vé vào cổng không giới hạn tất cả các khu trò chơi và cáp treo vượt biển 2 chiều.', 950000.00, 710000.00, 'dang_ban'),
(2, 1, 'Vé Sau 16h00 VinWonders Nha Trang', 'Áp dụng vào cổng sau 16h00 ngắm hoàng hôn và xem Tata Show trọn vẹn.', 560000.00, 420000.00, 'dang_ban'),
(3, 2, 'Vé Tham Quan Tháp Bà Ponagar', 'Vé vào cổng tham quan di tích và thưởng thức múa Chăm Pa truyền thống.', 30000.00, 15000.00, 'dang_ban'),
(4, 4, 'Tour Cano Lặn Ngắm San Hô Hòn Mun', 'Bao gồm cano khứ hồi, áo phao, kính lặn và hướng dẫn viên bảo tồn biển.', 450000.00, 320000.00, 'dang_ban'),
(5, 5, 'Vé Tham Quan Viện Hải Dương Học', 'Vé vào cổng toàn bộ khu thủy cung, bảo tàng mẫu vật và khu xương cá voi.', 40000.00, 20000.00, 'dang_ban'),
(6, 6, 'Vé Lên Đảo Bãi Tranh & Tắm Biển', 'Bao gồm ghế nằm, dù che bãi biển và tắm nước ngọt.', 50000.00, 30000.00, 'dang_ban'),
(7, 15, 'Vé Thăm Quan Thắng Cảnh Hòn Chồng', 'Vé vào cổng ngắm toàn cảnh vịnh biển và nghe biểu diễn đàn đá truyền thống.', 30000.00, 15000.00, 'dang_ban');

-- 5. SEED TON_KHO_VE (Du lieu cho 7 ngay tiep theo cho tung goi ve)
INSERT INTO `ton_kho_ve` (`goi_ve_id`, `ngay_su_dung`, `tong_so_ve`, `so_ve_con_lai`) VALUES
(1, CURRENT_DATE(), 500, 480),
(1, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 500, 495),
(1, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY), 500, 500),
(2, CURRENT_DATE(), 300, 290),
(2, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 300, 300),
(3, CURRENT_DATE(), 1000, 950),
(3, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 1000, 990),
(4, CURRENT_DATE(), 100, 85),
(4, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 100, 98),
(5, CURRENT_DATE(), 800, 780),
(5, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 800, 800),
(6, CURRENT_DATE(), 400, 390),
(6, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 400, 400),
(7, CURRENT_DATE(), 600, 590),
(7, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 600, 600);

-- 6. SEED DANH_GIA
INSERT INTO `danh_gia` (`nguoi_dung_id`, `dia_diem_id`, `so_sao`, `noi_dung`, `trang_thai`) VALUES
(2, 1, 5, 'VinWonders cực kỳ rộng và đẹp! Tata Show buổi tối đỉnh cao xứng đáng từng đồng tiền bát gạo.', 'da_duyet'),
(3, 2, 5, 'Tháp Bà cổ kính uy nghiêm, vũ điệu Chăm rất cuốn hút, không khí thanh tịnh.', 'da_duyet'),
(2, 7, 4, 'Hải sản Bờ Kè tôm mực rất tươi, ngồi sát sông Cái đón gió lộng ăn ngon miệng.', 'da_duyet');

SET FOREIGN_KEY_CHECKS = 1;


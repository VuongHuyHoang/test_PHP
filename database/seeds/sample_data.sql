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

(15, 5, 'Danh Thắng Hòn Chồng', 'danh-thang-hon-chong', 'Đường Phạm Văn Đồng, Phường Vĩnh Phước, TP. Nha Trang', 12.27250000, 109.20527800, 30000.00, 30000.00, '07:00:00', '19:00:00', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'Quần thể khối đá hoa cương tự nhiên xếp chồng lên nhau kỳ ảo vươn ra biển Đông.', 'Hòn Chồng lưu giữ dấu vết bàn tay khổng lồ kỳ bí trên phiến đá lớn, nơi du khách có thể nghe biểu diễn nhạc cụ dân tộc truyền thống bên bờ sóng vỗ.', 1, 'hien_thi', 840),
(16, 6, 'Khu Suối Khoáng Nóng I-Resort Nha Trang', 'i-resort-nha-trang', 'Tổ 19, Thôn Xuân Ngọc, Xã Vĩnh Ngọc, TP. Nha Trang', 12.28583300, 109.17638900, 170000.00, 450000.00, '08:00:00', '18:00:00', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80', 'Ốc đảo nghỉ dưỡng suối khoáng nóng và tắm bùn thảo dược đạt chuẩn quốc tế giữa thiên nhiên xanh mát.', 'I-Resort Nha Trang là khu du lịch suối khoáng nóng nổi tiếng bậc nhất Nha Trang với công viên nước khoáng và các gói tắm bùn cao cấp.', 1, 'hien_thi', 1890),
(17, 5, 'Nhà Hát Đó - Vega City Nha Trang', 'nha-hat-do-nha-trang', 'Khu đô thị Vega City, Đại lộ Phạm Văn Đồng, Bãi Tiên, Phường Vĩnh Hòa, TP. Nha Trang', 12.30250000, 109.21083300, 300000.00, 750000.00, '09:00:00', '21:30:00', 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?auto=format&fit=crop&w=800&q=80', 'Kiến trúc mang hình dáng chiếc Đó bắt cá khổng lồ, điểm biểu diễn show nghệ thuật rối mơ Rối Mơ độc bản.', 'Nhà Hát Đó sở hữu sân khấu cơ khí hiện đại bậc nhất thế giới cùng show diễn Rối Mơ (Life Puppets) độc đáo.', 1, 'hien_thi', 2100),
(18, 4, 'Khu Du Lịch Đảo Hoa Lan (Suối Hoa Lan)', 'dao-hoa-lan-nha-trang', 'Bán đảo Hòn Hèo, Vịnh Nha Phu, Thị xã Ninh Hòa, Tỉnh Khánh Hòa', 12.37805600, 109.20861100, 140000.00, 250000.00, '07:30:00', '16:30:00', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80', 'Thung lũng hoa lan rực rỡ bên bờ vịnh Nha Phu cùng đàn hươu sao thân thiện và bãi tắm cát vàng An Bình.', 'Đảo Hoa Lan sở hữu con đường hoa lan tuyệt đẹp với hàng trăm giống lan quý hiếm bung nở quanh năm.', 1, 'hien_thi', 890),
(19, 6, 'Khu Du Lịch Đảo Khỉ (Hòn Lao)', 'dao-khi-hon-lao', 'Đảo Hòn Lao, Vịnh Nha Phu, Thị xã Ninh Hòa, Tỉnh Khánh Hòa', 12.35555600, 109.21388900, 180000.00, 300000.00, '07:30:00', '16:30:00', 'https://images.unsplash.com/photo-1540573133985-87b6da6d54a9?auto=format&fit=crop&w=800&q=80', 'Vương quốc của hơn 1.200 chú khỉ sống tự nhiên trên đảo hoang sơ rợp bóng cây xanh mát.', 'Hòn Lao là khu bảo tồn khỉ lớn nhất miền Trung với đàn khỉ sống bầy đàn tự do trong rừng nguyên sinh.', 1, 'hien_thi', 940),
(20, 6, 'Khu Du Lịch Tắm Bùn Trăm Trứng', 'tam-bun-tram-trung', 'Đại lộ Nguyễn Tất Thành, Xã Phước Đồng, TP. Nha Trang', 12.18888900, 109.15555600, 120000.00, 280000.00, '08:00:00', '18:00:00', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80', 'Khu du lịch sinh thái nổi bật với hơn 100 bồn tắm bùn khoáng tạo hình quả trứng khổng lồ giữa sườn đồi.', 'Tắm Bùn Trăm Trứng trải rộng trên diện tích hơn 20 hecta dưới chân núi xanh ngắt, kết hợp làng văn hóa Chăm Pa.', 1, 'hien_thi', 760),
(21, 4, 'Đảo Điệp Sơn (Con Đường Giữa Biển)', 'dao-diep-son-nha-trang', 'Vịnh Vân Phong, Huyện Vạn Ninh, Tỉnh Khánh Hòa', 12.60833300, 109.22222200, 250000.00, 650000.00, '06:00:00', '17:00:00', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'Kỳ quan thủy đạo uốn lượn dưới làn nước trong vắt nối liền 3 hòn đảo nhỏ giữa vịnh Vân Phong.', 'Điệp Sơn nổi tiếng khắp cả nước nhờ con đường cát trắng tự nhiên dài gần 800m chìm dưới biển.', 1, 'hien_thi', 1670),
(22, 5, 'Bảo Tàng Bác Sĩ Alexandre Yersin', 'bao-tang-alexandre-yersin', 'Số 10 Trần Phú, Phường Xương Huân, TP. Nha Trang', 12.25333300, 109.19777800, 26000.00, 50000.00, '07:30:00', '17:00:00', 'https://images.unsplash.com/photo-1565008447742-97f6f38c985c?auto=format&fit=crop&w=800&q=80', 'Nơi lưu giữ kỷ vật, sách vở và kính thiên văn của nhà khoa học vĩ đại đã gắn bó cả cuộc đời với Nha Trang.', 'Bảo tàng nằm trong khuôn viên Viện Pasteur Nha Trang, trưng bày nguyên vẹn phòng làm việc của bác sĩ Yersin.', 1, 'hien_thi', 540),
(23, 4, 'Bãi Biển Dốc Lết', 'bai-bien-doc-let', 'Phường Ninh Hải, Thị xã Ninh Hòa, Tỉnh Khánh Hòa', 12.53611100, 109.23194400, 50000.00, 200000.00, '06:00:00', '18:30:00', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'Bãi biển cát trắng mịn màng thoai thoải nước cạn, làn nước trong xanh phẳng lặng lý tưởng cho gia đình.', 'Dốc Lết thu hút du khách nhờ dải cát trắng tinh trải dài hơn 2,5km cùng những cồn cát nhấp nhô rợp bóng phi lao.', 0, 'hien_thi', 1150),
(24, 1, 'Hải Sản Thanh Sương Nha Trang', 'hai-san-thanh-suong', '21 Đường Trần Phú, Phường Vĩnh Nguyên, TP. Nha Trang', 12.20388900, 109.21194400, 60000.00, 500000.00, '11:00:00', '23:00:00', 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=800&q=80', 'Quán hải sản bình dân nổi tiếng bậc nhất khu Cầu Đá với thực đơn tôm hùm, ốc hương, cua gạch tươi bơi bể.', 'Thanh Sương có bể hải sản sống tự chọn cân ký tại chỗ, chế biến theo yêu cầu với mức giá niêm yết rõ ràng.', 0, 'hien_thi', 2350),
(25, 2, 'Bò Nướng Lạc Cảnh (Hơn 40 Năm)', 'bo-nuong-lac-canh', '44 Nguyễn Bỉnh Khiêm, Phường Xương Huân, TP. Nha Trang', 12.25472200, 109.19361100, 90000.00, 250000.00, '10:00:00', '22:00:00', 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80', 'Món bò ướp mật ong nướng than hoa trứ danh với câu truyền khẩu: "Đến Nha Trang chưa ăn bò Lạc Cảnh là chưa biết Nha Trang".', 'Bò Lạc Cảnh tồn tại từ những năm 1970 với bí quyết thịt bò tươi thái con cờ ướp mật ong nướng xèo xèo bếp than.', 0, 'hien_thi', 1980),
(26, 2, 'Bánh Canh Chả Cá & Cua Biển Cô Hà', 'banh-canh-co-ha', '14 Phan Chu Trinh, Phường Vạn Thạnh, TP. Nha Trang', 12.24833300, 109.19305600, 35000.00, 80000.00, '06:30:00', '21:00:00', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=800&q=80', 'Tô bánh canh bột lọc dai dẻo ngập tràn thịt cua biển tươi roi rói cùng chả cá thu hấp ngọt thanh.', 'Nước dùng ninh từ xương cá cờ và ghẹ biển ngọt lịm tự nhiên, chả cá chiên vàng ươm dai sần sật.', 0, 'hien_thi', 1240),
(27, 2, 'Bánh Xèo Chảo Mực Sữa Tô Hiến Thành', 'banh-xeo-to-hien-thanh', '85 Tô Hiến Thành, Phường Tân Lập, TP. Nha Trang', 12.24111100, 109.19250000, 40000.00, 90000.00, '15:00:00', '22:00:00', 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80', 'Bánh xèo miền biển giòn rụm với nguyên con mực sữa còn giữ túi mật thơm lừng và tôm biển tươi ngọt.', 'Bánh xèo mực Nha Trang đặc trưng bởi con mực cơm nguyên con tươi ngọt ngào khi cắn vỡ túi mật ngọt bùi.', 0, 'hien_thi', 1420),
(28, 1, 'Hải Sản Hạnh Xuân (Chân Cầu Trần Phú)', 'hai-san-hanh-xuan', '36 Cù Huân, Phường Vĩnh Thọ, TP. Nha Trang', 12.25833300, 109.19722200, 100000.00, 550000.00, '10:00:00', '23:30:00', 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80', 'Không gian mở đón gió cửa sông Cái với view ngắm tháp Trầm Hương và cầu Trần Phú rực rỡ về đêm.', 'Quán chuyên các món cá mú hấp xì dầu, cua gạch rang me, sò dương nướng mỡ hành sát mép nước bờ kè.', 0, 'hien_thi', 1780),
(29, 3, 'Bánh Bèo Mâm & Bánh Đập Huỳnh Thúc Kháng', 'banh-beo-huynh-thuc-khang', '16 Huỳnh Thúc Kháng, Phường Phước Tiến, TP. Nha Trang', 12.24361100, 109.19027800, 25000.00, 60000.00, '14:00:00', '21:00:00', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=800&q=80', 'Mâm bánh bèo chén tôm cháy thơm phưng phức cùng bánh tráng nướng đập dập kẹp bánh ướt chấm mắm nêm đậm đà.', 'Mỗi mâm gồm 10 chén bánh bèo nhỏ nhắn rắc bột tôm khô, da heo giòn rụm và mỡ hành xanh mướt.', 0, 'hien_thi', 880),
(30, 3, 'Chè Trái Cây & Tráng Miệng Cô Ba', 'che-co-ba-nha-trang', '105 Thống Nhất, Phường Vạn Thắng, TP. Nha Trang', 12.24722200, 109.18972200, 15000.00, 45000.00, '13:00:00', '22:30:00', 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=800&q=80', 'Quán chè giải nhiệt thanh mát với dừa dầm thốt nốt, chè sầu riêng béo ngậy và sâm bổ lượng hạt sen bổ dưỡng.', 'Hơn 20 loại chè được nấu từ nước cốt dừa tươi nguyên chất, trái cây nhiệt đới tươi ngon giúp xua tan nắng nóng.', 0, 'hien_thi', 1120);

-- 4. SEED GOI_VE (Cac goi ve cho dia diem co_ban_ve = 1)
INSERT INTO `goi_ve` (`id`, `dia_diem_id`, `ten_goi_ve`, `mo_ta`, `gia_nguoi_lon`, `gia_tre_em`, `trang_thai`) VALUES
(1, 1, 'Vé Tiêu Chuẩn VinWonders Nha Trang (Gồm Cáp Treo 2 Chiều)', 'Vé vào cổng không giới hạn tất cả các khu trò chơi và cáp treo vượt biển 2 chiều.', 950000.00, 710000.00, 'dang_ban'),
(2, 1, 'Vé Sau 16h00 VinWonders Nha Trang', 'Áp dụng vào cổng sau 16h00 ngắm hoàng hôn và xem Tata Show trọn vẹn.', 560000.00, 420000.00, 'dang_ban'),
(3, 2, 'Vé Tham Quan Tháp Bà Ponagar', 'Vé vào cổng tham quan di tích và thưởng thức múa Chăm Pa truyền thống.', 30000.00, 15000.00, 'dang_ban'),
(4, 4, 'Tour Cano Lặn Ngắm San Hô Hòn Mun', 'Bao gồm cano khứ hồi, áo phao, kính lặn và hướng dẫn viên bảo tồn biển.', 450000.00, 320000.00, 'dang_ban'),
(5, 5, 'Vé Tham Quan Viện Hải Dương Học', 'Vé vào cổng toàn bộ khu thủy cung, bảo tàng mẫu vật và khu xương cá voi.', 40000.00, 20000.00, 'dang_ban'),
(6, 6, 'Vé Lên Đảo Bãi Tranh & Tắm Biển', 'Bao gồm ghế nằm, dù che bãi biển và tắm nước ngọt.', 50000.00, 30000.00, 'dang_ban'),
(7, 15, 'Vé Thăm Quan Thắng Cảnh Hòn Chồng', 'Vé vào cổng ngắm toàn cảnh vịnh biển và nghe biểu diễn đàn đá truyền thống.', 30000.00, 15000.00, 'dang_ban'),
(8, 16, 'Vé Suối Khoáng Nóng & Công Viên Nước I-Resort', 'Ngâm hồ khoáng nóng vô cực, massage tia nước thủy lực, công viên nước khoáng.', 170000.00, 80000.00, 'dang_ban'),
(9, 16, 'Gói Tắm Bùn Khoáng Nóng Cao Cấp I-Resort', 'Gồm ngâm bùn khoáng nóng 20 phút, ngâm nước khoáng nóng thảo dược và toàn bộ dịch vụ công viên nước.', 260000.00, 130000.00, 'dang_ban'),
(10, 17, 'Vé Xem Show Rối Mơ - Hạng Tiêu Chuẩn (Silver)', 'Thưởng thức 60 phút trình diễn nghệ thuật đa phương tiện đỉnh cao.', 350000.00, 250000.00, 'dang_ban'),
(11, 18, 'Vé Tàu Khứ Hồi & Vé Cổng Đảo Hoa Lan', 'Gồm tàu cano đưa đón 2 chiều bến Đá Chồng, tham quan vườn lan, vườn bướm, xem xiếc chim.', 140000.00, 100000.00, 'dang_ban'),
(12, 19, 'Vé Tàu & Trọn Gói Tham Quan Đảo Khỉ', 'Tàu cano 2 chiều, xem xiếc thú, tiếp xúc đàn khỉ tự nhiên và tắm biển.', 180000.00, 110000.00, 'dang_ban'),
(13, 20, 'Vé Tắm Bùn Trứng & Hồ Bơi Khoáng Nóng', 'Ngâm bùn khoáng nóng trong bồn trứng 20 phút, hồ bơi khoáng nóng và tắm ôn tuyền.', 200000.00, 120000.00, 'dang_ban'),
(14, 21, 'Tour Cano Khám Phá Thủy Đạo Điệp Sơn', 'Cano cao tốc đưa đón khứ hồi từ bến Vạn Giã, đi bộ trên cát biển và ăn trưa hải sản.', 350000.00, 200000.00, 'dang_ban'),
(15, 22, 'Vé Thăm Quan Không Gian Kỷ Niệm Yersin', 'Bao gồm hướng dẫn viên thuyết minh tư liệu lịch sử viện Pasteur.', 26000.00, 15000.00, 'dang_ban');

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
(7, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 600, 600),
(8, CURRENT_DATE(), 500, 480),
(8, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 500, 500),
(9, CURRENT_DATE(), 300, 290),
(9, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 300, 300),
(10, CURRENT_DATE(), 400, 380),
(10, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 400, 400),
(11, CURRENT_DATE(), 300, 295),
(11, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 300, 300),
(12, CURRENT_DATE(), 300, 290),
(12, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 300, 300),
(13, CURRENT_DATE(), 300, 285),
(13, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 300, 300),
(14, CURRENT_DATE(), 200, 190),
(14, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 200, 200),
(15, CURRENT_DATE(), 500, 490),
(15, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), 500, 500);

-- 6. SEED DANH_GIA
INSERT INTO `danh_gia` (`nguoi_dung_id`, `dia_diem_id`, `so_sao`, `noi_dung`, `trang_thai`) VALUES
(2, 1, 5, 'VinWonders cực kỳ rộng và đẹp! Tata Show buổi tối đỉnh cao xứng đáng từng đồng tiền bát gạo.', 'da_duyet'),
(3, 2, 5, 'Tháp Bà cổ kính uy nghiêm, vũ điệu Chăm rất cuốn hút, không khí thanh tịnh.', 'da_duyet'),
(2, 7, 4, 'Hải sản Bờ Kè tôm mực rất tươi, ngồi sát sông Cái đón gió lộng ăn ngon miệng.', 'da_duyet');

SET FOREIGN_KEY_CHECKS = 1;


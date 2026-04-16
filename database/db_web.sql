-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 16, 2026 lúc 08:35 PM
-- Phiên bản máy phục vụ: 8.0.44
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_web`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DH_TaoDonHang` (IN `p_nguoi_dung_id` INT, IN `p_dia_chi_id` INT, IN `p_ma_giam_gia_id` INT, IN `p_phi_van_chuyen` DECIMAL(15,2), OUT `p_don_hang_id` INT)   BEGIN
    DECLARE v_gio_hang_id INT;
    DECLARE v_tong_tien DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tien_giam DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tong_thanh_toan DECIMAL(15,2);
    DECLARE v_so_sp INT DEFAULT 0;

    -- Nếu lỗi → rollback
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi khi tạo đơn hàng';
    END;

    --  Kiểm tra địa chỉ thuộc user
    IF NOT EXISTS (
        SELECT 1 FROM dia_chi
        WHERE id = p_dia_chi_id
        AND nguoi_dung_id = p_nguoi_dung_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Địa chỉ không hợp lệ';
    END IF;

    --  Lấy giỏ hàng
    SELECT id INTO v_gio_hang_id
    FROM gio_hang
    WHERE nguoi_dung_id = p_nguoi_dung_id
    LIMIT 1;

    IF v_gio_hang_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Không tìm thấy giỏ hàng';
    END IF;

    --  Kiểm tra giỏ có sản phẩm
    SELECT COUNT(*) INTO v_so_sp
    FROM chi_tiet_gio
    WHERE gio_hang_id = v_gio_hang_id;

    IF v_so_sp = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Giỏ hàng trống';
    END IF;

    --  Lock tồn kho
    SELECT pb.id
    FROM phien_ban_san_pham pb
    JOIN chi_tiet_gio ct ON pb.id = ct.phien_ban_id
    WHERE ct.gio_hang_id = v_gio_hang_id
    FOR UPDATE;

    --  Kiểm tra đủ tồn kho
    IF EXISTS (
        SELECT 1
        FROM phien_ban_san_pham pb
        JOIN chi_tiet_gio ct ON pb.id = ct.phien_ban_id
        WHERE ct.gio_hang_id = v_gio_hang_id
        AND pb.so_luong_ton < ct.so_luong
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Không đủ tồn kho';
    END IF;

    --  Tạo đơn hàng
    INSERT INTO don_hang (
        ma_don_hang,
        nguoi_dung_id,
        dia_chi_id,
        ma_giam_gia_id,
        phi_van_chuyen,
        trang_thai
    )
    VALUES (
        CONCAT('DH', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
        p_nguoi_dung_id,
        p_dia_chi_id,
        p_ma_giam_gia_id,
        p_phi_van_chuyen,
        'CHO_DUYET'
    );

    SET p_don_hang_id = LAST_INSERT_ID();

    --  Thêm chi tiết đơn
    INSERT INTO chi_tiet_don (
        don_hang_id,
        phien_ban_id,
        so_luong,
        gia_tai_thoi_diem_mua
    )
    SELECT
        p_don_hang_id,
        ct.phien_ban_id,
        ct.so_luong,
        pb.gia_ban
    FROM chi_tiet_gio ct
    JOIN phien_ban_san_pham pb ON ct.phien_ban_id = pb.id
    WHERE ct.gio_hang_id = v_gio_hang_id;

    --  Tính tổng tiền
    SELECT IFNULL(SUM(so_luong * gia_tai_thoi_diem_mua),0)
    INTO v_tong_tien
    FROM chi_tiet_don
    WHERE don_hang_id = p_don_hang_id;

    --  Xử lý voucher
    IF p_ma_giam_gia_id IS NOT NULL THEN

        IF NOT EXISTS (
            SELECT 1 FROM ma_giam_gia
            WHERE id = p_ma_giam_gia_id
            AND trang_thai = 'HOAT_DONG'
            AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
            AND (gioi_han_su_dung IS NULL 
                 OR so_luot_da_dung < gioi_han_su_dung)
            AND v_tong_tien >= don_toi_thieu
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voucher không hợp lệ';
        END IF;

        SELECT
            CASE
                WHEN loai_giam = 'PHAN_TRAM'
                THEN LEAST(
                    v_tong_tien * gia_tri_giam / 100,
                    IFNULL(giam_toi_da, v_tong_tien)
                )
                ELSE gia_tri_giam
            END
        INTO v_tien_giam
        FROM ma_giam_gia
        WHERE id = p_ma_giam_gia_id;

        UPDATE ma_giam_gia
        SET so_luot_da_dung = so_luot_da_dung + 1
        WHERE id = p_ma_giam_gia_id;

    END IF;

    --  Tổng thanh toán
    SET v_tong_thanh_toan =
        v_tong_tien + p_phi_van_chuyen - v_tien_giam;

    IF v_tong_thanh_toan < 0 THEN
        SET v_tong_thanh_toan = 0;
    END IF;

    UPDATE don_hang
    SET tong_tien = v_tong_tien,
        tien_giam_gia = v_tien_giam,
        tong_thanh_toan = v_tong_thanh_toan
    WHERE id = p_don_hang_id;

    --  Trừ tồn kho
    UPDATE phien_ban_san_pham pb
    JOIN chi_tiet_don ct ON pb.id = ct.phien_ban_id
    SET pb.so_luong_ton = pb.so_luong_ton - ct.so_luong
    WHERE ct.don_hang_id = p_don_hang_id;

    --  Xóa giỏ
    DELETE FROM chi_tiet_gio
    WHERE gio_hang_id = v_gio_hang_id;

    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DH_XemDonHang` (IN `p_don_hang_id` INT, IN `p_nguoi_dung_id` INT)   BEGIN

    -- Kiểm tra đơn có tồn tại và thuộc về user
    IF NOT EXISTS (
        SELECT 1 FROM don_hang
        WHERE id = p_don_hang_id
        AND nguoi_dung_id = p_nguoi_dung_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Đơn hàng không tồn tại hoặc không thuộc về bạn';
    END IF;

    --  Thông tin đơn hàng
    SELECT 
        dh.id,
        dh.ma_don_hang,
        dh.trang_thai,
        dh.tong_tien,
        dh.tien_giam_gia,
        dh.phi_van_chuyen,
        dh.tong_thanh_toan,
        dh.ghi_chu,
        dh.ngay_giao_du_kien,
        dh.ngay_tao,
        dc.ten_nguoi_nhan,
        dc.sdt_nhan,
        dc.so_nha_duong,
        dc.phuong_xa,
        dc.quan_huyen,
        dc.tinh_thanh
    FROM don_hang dh
    LEFT JOIN dia_chi dc ON dh.dia_chi_id = dc.id
    WHERE dh.id = p_don_hang_id;

    --  Chi tiết sản phẩm trong đơn
    SELECT 
        ct.id,
        sp.ten_san_pham,
        pb.ten_phien_ban,
        pb.mau_sac,
        pb.dung_luong,
        pb.ram,
        ct.so_luong,
        ct.gia_tai_thoi_diem_mua,
        (ct.so_luong * ct.gia_tai_thoi_diem_mua) AS thanh_tien
    FROM chi_tiet_don ct
    JOIN phien_ban_san_pham pb ON ct.phien_ban_id = pb.id
    JOIN san_pham sp ON pb.san_pham_id = sp.id
    WHERE ct.don_hang_id = p_don_hang_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GH_CapNhatSoLuongGio` (IN `p_chi_tiet_id` INT, IN `p_so_luong_moi` INT)   BEGIN
    -- Nếu số lượng mới <= 0 thì tự động xóa khỏi giỏ
    IF p_so_luong_moi <= 0 THEN
        DELETE FROM chi_tiet_gio WHERE id = p_chi_tiet_id;
    ELSE
        UPDATE chi_tiet_gio 
        SET so_luong = p_so_luong_moi
        WHERE id = p_chi_tiet_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GH_LayChiTietGioHang` (IN `p_gio_hang_id` INT)   BEGIN
    SELECT 
        ctg.id AS chi_tiet_id,
        sp.ten_san_pham,
        pb.ten_phien_ban,
        pb.mau_sac,
        pb.dung_luong,
        pb.gia_ban,
        ctg.so_luong,
        (pb.gia_ban * ctg.so_luong) AS thanh_tien,
        -- Lấy ảnh chính của sản phẩm (nếu có ảnh phiên bản thì lấy, ko thì lấy ảnh chung)
        COALESCE(ha_pb.url_anh, ha_sp.url_anh) AS hinh_anh
    FROM chi_tiet_gio ctg
    JOIN phien_ban_san_pham pb ON ctg.phien_ban_id = pb.id
    JOIN san_pham sp ON pb.san_pham_id = sp.id
    -- Join lấy ảnh chính của sản phẩm
    LEFT JOIN hinh_anh_san_pham ha_sp ON sp.id = ha_sp.san_pham_id AND ha_sp.la_anh_chinh = 1 AND ha_sp.phien_ban_id IS NULL
    -- Join lấy ảnh của riêng phiên bản (nếu có)
    LEFT JOIN hinh_anh_san_pham ha_pb ON pb.id = ha_pb.phien_ban_id AND ha_pb.la_anh_chinh = 1
    WHERE ctg.gio_hang_id = p_gio_hang_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GH_ThemVaoGioHang` (IN `p_gio_hang_id` INT, IN `p_phien_ban_id` INT, IN `p_so_luong` INT)   BEGIN
    -- Kiểm tra xem phiên bản sản phẩm này đã có trong giỏ hàng chưa
    IF EXISTS (SELECT 1 FROM chi_tiet_gio WHERE gio_hang_id = p_gio_hang_id AND phien_ban_id = p_phien_ban_id) THEN
        -- Nếu có rồi thì cộng dồn số lượng
        UPDATE chi_tiet_gio 
        SET so_luong = so_luong + p_so_luong
        WHERE gio_hang_id = p_gio_hang_id AND phien_ban_id = p_phien_ban_id;
    ELSE
        -- Nếu chưa có thì chèn mới
        INSERT INTO chi_tiet_gio (gio_hang_id, phien_ban_id, so_luong)
        VALUES (p_gio_hang_id, p_phien_ban_id, p_so_luong);
    END IF;
    
    -- Cập nhật thời gian thay đổi của giỏ hàng mẹ
    UPDATE gio_hang SET ngay_cap_nhat = CURRENT_TIMESTAMP WHERE id = p_gio_hang_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GH_XoaKhoiGioHang` (IN `p_chi_tiet_id` INT)   BEGIN
    DELETE FROM chi_tiet_gio WHERE id = p_chi_tiet_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LayChiTietPhienBanSanPham` (IN `p_san_pham_id` INT)   BEGIN
    SELECT 
        sp.ten_san_pham, 
        sp.hang_san_xuat,
        pb.sku, 
        pb.ten_phien_ban, 
        pb.mau_sac, 
        pb.dung_luong,    
        pb.ram,          
        pb.cau_hinh, 
        pb.gia_goc,      
        pb.gia_ban, 
        pb.so_luong_ton,
        pb.trang_thai     
    FROM san_pham sp
    JOIN phien_ban_san_pham pb ON sp.id = pb.san_pham_id
    WHERE sp.id = p_san_pham_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tao_hoa_don` (IN `p_nguoi_dung_id` INT, IN `p_dia_chi_id` INT, IN `p_phien_ban_id` INT, IN `p_so_luong` INT, IN `p_phuong_thuc` VARCHAR(50))   BEGIN
    DECLARE v_gia DECIMAL(15,2);
    DECLARE v_tong DECIMAL(15,2);
    DECLARE v_don_hang_id INT;
    DECLARE v_ma_don VARCHAR(20);
    
    -- Biến tạm để lưu snapshot địa chỉ
    DECLARE v_ten_nhan VARCHAR(255);
    DECLARE v_sdt_nhan VARCHAR(20);
    DECLARE v_dia_chi_full TEXT;

    -- Bắt đầu Transaction để đảm bảo tính nguyên tử
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Lấy thông tin địa chỉ để Snapshot
    SELECT 
        ten_nguoi_nhan, 
        sdt_nhan, 
        CONCAT(so_nha_duong, ', ', phuong_xa, ', ', quan_huyen, ', ', tinh_thanh)
    INTO v_ten_nhan, v_sdt_nhan, v_dia_chi_full
    FROM dia_chi
    WHERE id = p_dia_chi_id AND nguoi_dung_id = p_nguoi_dung_id;

    -- 2. Lấy giá sản phẩm và kiểm tra tồn kho
    SELECT gia_ban INTO v_gia
    FROM phien_ban_san_pham
    WHERE id = p_phien_ban_id;

    SET v_tong = v_gia * p_so_luong;
    
    -- 3. Tạo Mã đơn hàng tự động: DH + NămThángNgày + 4 số ngẫu nhiên
    SET v_ma_don = CONCAT('DH', DATE_FORMAT(NOW(), '%Y%m%d'), FLOOR(RAND() * 10000));

    -- 4. Tạo đơn hàng với THÔNG TIN SNAPSHOT
    INSERT INTO don_hang (
        ma_don_hang,
        nguoi_dung_id,
        ten_nguoi_nhan,
        sdt_nguoi_nhan,
        dia_chi_giao_hang,
        trang_thai,
        tam_tinh,
        phi_van_chuyen,
        tong_thanh_toan
    )
    VALUES (
        v_ma_don,
        p_nguoi_dung_id,
        v_ten_nhan,
        v_sdt_nhan,
        v_dia_chi_full,
        'CHO_XAC_NHAN',
        v_tong,
        0, -- Giả định phí ship mặc định là 0
        v_tong
    );

    SET v_don_hang_id = LAST_INSERT_ID();

    -- 5. Tạo chi tiết đơn
    INSERT INTO chi_tiet_don (
        don_hang_id,
        phien_ban_id,
        so_luong,
        gia_tai_thoi_diem_mua
    )
    VALUES (
        v_don_hang_id,
        p_phien_ban_id,
        p_so_luong,
        v_gia
    );

    -- 6. Tạo bản ghi thanh toán
    INSERT INTO thanh_toan (
        don_hang_id,
        phuong_thuc,
        so_tien,
        trang_thai_duyet
    )
    VALUES (
        v_don_hang_id,
        p_phuong_thuc,
        v_tong,
        'CHO_DUYET'
    );

    COMMIT;
    
    -- Trả về ID đơn vừa tạo để Backend xử lý tiếp
    SELECT v_don_hang_id AS id_moi_tao, v_ma_don AS ma_don_hang;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_TimKiemVaThongKeSanPham` (IN `p_tu_khoa` VARCHAR(255))   BEGIN
    SELECT 
        sp.id AS ma_san_pham,
        sp.ten_san_pham,
        sp.slug,                  -- ví dụ: /iphone-16-pro-max
        sp.hang_san_xuat,        
        dm.ten AS ten_danh_muc,
        sp.diem_danh_gia,
        sp.trang_thai,           
        MAX(ha.url_anh) AS anh_dai_dien, 
        MIN(pb.gia_ban) AS gia_thap_nhat,
        MAX(pb.gia_ban) AS gia_cao_nhat,
        SUM(pb.so_luong_ton) AS tong_ton_kho
    FROM san_pham sp
    LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
    LEFT JOIN phien_ban_san_pham pb ON sp.id = pb.san_pham_id 
        AND pb.trang_thai != 'NGUNG_BAN' 
    LEFT JOIN hinh_anh_san_pham ha ON sp.id = ha.san_pham_id 
        AND ha.la_anh_chinh = 1 
    WHERE 
        sp.ten_san_pham LIKE CONCAT('%', p_tu_khoa, '%')
        OR sp.hang_san_xuat LIKE CONCAT('%', p_tu_khoa, '%') 
    GROUP BY
        sp.id,
        sp.ten_san_pham,
        sp.slug,
        sp.hang_san_xuat,
        dm.ten,
        sp.diem_danh_gia,
        sp.trang_thai
    ORDER BY 
        sp.diem_danh_gia DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_xem_hoa_don` (IN `p_don_hang_id` INT)   BEGIN
    SELECT 
        -- Thông tin tổng quát đơn hàng
        dh.id AS id_he_thong,
        dh.ma_don_hang,
        dh.ngay_tao,
        dh.trang_thai AS trang_thai_don_hang,
        dh.tong_thanh_toan,
        
        -- Thông tin khách hàng (Dữ liệu Profile)
        nd.ho_ten AS khach_hang,
        nd.email,
        
        -- Thông tin nhận hàng (Dữ liệu Snapshot - Cực kỳ quan trọng)
        dh.ten_nguoi_nhan,
        dh.sdt_nguoi_nhan,
        dh.dia_chi_giao_hang AS dia_chi_chi_tiet,

        -- Thông tin sản phẩm
        sp.ten_san_pham,
        pb.ten_phien_ban,
        ctd.so_luong,
        ctd.gia_tai_thoi_diem_mua,

        -- Thông tin thanh toán
        tt.phuong_thuc,
        tt.trang_thai_duyet AS trang_thai_thanh_toan,
        tt.ngay_thanh_toan

    FROM don_hang dh
    LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
    INNER JOIN chi_tiet_don ctd ON dh.id = ctd.don_hang_id
    INNER JOIN phien_ban_san_pham pb ON ctd.phien_ban_id = pb.id
    INNER JOIN san_pham sp ON pb.san_pham_id = sp.id
    LEFT JOIN thanh_toan tt ON dh.id = tt.don_hang_id

    WHERE dh.id = p_don_hang_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banner_quang_cao`
--

CREATE TABLE `banner_quang_cao` (
  `id` int NOT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên banner để admin dễ quản lý',
  `hinh_anh_desktop` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link ảnh cho màn hình máy tính',
  `hinh_anh_mobile` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link ảnh cho màn hình điện thoại',
  `link_dich` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL khi user click vào banner',
  `vi_tri` enum('HOME_HERO','HOME_SIDE','HOME_MID','FLOATING_BOTTOM_LEFT','POPUP','CATEGORY_TOP') COLLATE utf8mb4_unicode_ci NOT NULL,
  `thu_tu` int DEFAULT '0' COMMENT 'Sắp xếp thứ tự nếu có nhiều banner cùng vị trí',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT '1' COMMENT '1 = Hiển thị, 0 = Ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banner_quang_cao`
--

INSERT INTO `banner_quang_cao` (`id`, `tieu_de`, `hinh_anh_desktop`, `hinh_anh_mobile`, `link_dich`, `vi_tri`, `thu_tu`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(5, 'Laptop giá sốc - Giảm đến 30%', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://www.thegioididong.com/laptop', 'HOME_HERO', 2, '2026-04-04 20:26:09', '2026-04-19 20:26:09', 1),
(6, 'Khuyến mãi siêu sale điện thoại', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://shopee.vn', 'HOME_HERO', 1, '2026-04-04 20:51:46', '2026-05-04 20:51:46', 1),
(7, 'Flash Sale phụ kiện - Giá từ 9K', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://tiki.vn/phu-kien-dien-thoai/c1789', 'HOME_HERO', 1, '2026-04-04 20:52:18', '2026-04-14 20:52:18', 1),
(17, 'test sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776000404/banners/banner_desktop_1776000399.webp', NULL, '/san-pham/lenovo-thinkpad-x1-carbon-gen-13-u7-258v-21ns010jvn', 'HOME_HERO', 0, '2026-04-12 20:26:00', '2026-04-24 20:26:00', 1),
(18, 'ok', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776001646/banners/banner_desktop_1776001638.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_HERO', 0, '2026-04-12 20:47:00', '2026-04-25 20:47:00', 1),
(19, 'Siêu sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776081915/banners/banner_desktop_1776081905.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:01:00', '2026-04-23 19:01:00', 1),
(20, 'Sale giữa tháng', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776081974/banners/banner_desktop_1776081965.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:05:00', '2026-04-30 19:06:00', 1),
(21, 'Sale chào lương về', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776082226/banners/banner_desktop_1776082199.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:09:00', '2026-04-30 19:09:00', 1),
(22, 'Săn sale hết cỡ', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776082270/banners/banner_desktop_1776082251.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:10:00', '2026-04-24 19:10:00', 1),
(23, 'Deal tới', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776132457/banners/banner_desktop_1776132436.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:07:00', '2026-04-30 09:07:00', 1),
(24, 'Săn ngay voucher', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776132644/banners/banner_desktop_1776132608.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:10:00', '2026-04-30 09:10:00', 1),
(25, 'Deal nửa giá', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776134749/banners/banner_desktop_1776134718.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:45:00', '2026-04-30 09:45:00', 1),
(28, 'Deal nửa giá - PopUP', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776216305/banners/banner_desktop_1776216278.png', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776216322/banners/banner_mobile_1776216278.png', '/san-pham/iphone-15', 'POPUP', 0, '2026-04-15 08:24:00', '2026-04-30 08:24:00', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don`
--

CREATE TABLE `chi_tiet_don` (
  `id` int NOT NULL,
  `don_hang_id` int NOT NULL,
  `phien_ban_id` int NOT NULL,
  `so_luong` int DEFAULT '1',
  `gia_tai_thoi_diem_mua` decimal(15,2) DEFAULT NULL COMMENT 'Snapshot giá lúc đặt hàng'
) ;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don`
--

INSERT INTO `chi_tiet_don` (`id`, `don_hang_id`, `phien_ban_id`, `so_luong`, `gia_tai_thoi_diem_mua`) VALUES
(1, 1, 11, 1, 34.99),
(2, 2, 11, 1, 34.99),
(3, 3, 11, 1, 34.99),
(4, 4, 10, 1, 34990000.00),
(5, 5, 11, 1, 34.99),
(6, 6, 10, 1, 34990000.00),
(7, 7, 10, 1, 34990000.00),
(8, 8, 10, 1, 34990000.00),
(9, 9, 11, 1, 34.99),
(10, 10, 10, 1, 34990000.00),
(11, 11, 10, 1, 34990000.00),
(12, 12, 10, 1, 34990000.00),
(13, 13, 10, 1, 34990000.00),
(14, 14, 10, 1, 34990000.00),
(15, 15, 11, 1, 34.99),
(16, 16, 11, 2, 34.99),
(17, 17, 11, 1, 34.99),
(18, 18, 11, 1, 34.99),
(19, 19, 11, 1, 34.99),
(20, 20, 11, 1, 34.99),
(21, 21, 11, 1, 34.99),
(22, 22, 11, 1, 34.99),
(23, 23, 11, 1, 34.99),
(24, 24, 11, 1, 34.99),
(25, 25, 11, 1, 34.99),
(26, 26, 11, 1, 34.99),
(27, 27, 11, 1, 34.99),
(28, 28, 11, 1, 34.99),
(29, 29, 11, 1, 34.99),
(30, 30, 11, 1, 34.99),
(31, 31, 14, 1, 17890000.00),
(32, 32, 10, 1, 34990000.00),
(33, 33, 10, 1, 34990000.00),
(34, 34, 12, 1, 20490000.00),
(35, 35, 12, 1, 20490000.00),
(36, 36, 12, 1, 20490000.00),
(37, 37, 11, 1, 34.99),
(38, 38, 12, 1, 20490000.00),
(39, 39, 12, 1, 20490000.00),
(40, 40, 12, 1, 20490000.00),
(41, 41, 19, 1, 440000.00),
(42, 42, 19, 1, 440000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_gio`
--

CREATE TABLE `chi_tiet_gio` (
  `id` int NOT NULL,
  `gio_hang_id` int NOT NULL,
  `phien_ban_id` int NOT NULL,
  `so_luong` int DEFAULT '1'
) ;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_gio`
--

INSERT INTO `chi_tiet_gio` (`id`, `gio_hang_id`, `phien_ban_id`, `so_luong`) VALUES
(5, 8, 11, 1),
(53, 2, 17, 1),
(55, 25, 17, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int NOT NULL,
  `nguoi_dung_id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `so_sao` int DEFAULT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngay_viet` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id`, `nguoi_dung_id`, `san_pham_id`, `so_sao`, `noi_dung`, `ngay_viet`) VALUES
(1, 3, 7, 5, 'ok', '2026-04-13 13:01:08'),
(2, 3, 2, 5, 'chất lượng rất tốt', '2026-04-16 20:51:50'),
(3, 3, 13, 5, 'tuyệt vời', '2026-04-16 21:15:15'),
(4, 3, 10, 5, 'quá mát', '2026-04-16 22:45:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int NOT NULL,
  `ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL thân thiện: dien-thoai, laptop',
  `icon_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon hiển thị trên menu',
  `danh_muc_cha_id` int DEFAULT NULL,
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị trên menu',
  `trang_thai` tinyint(1) DEFAULT '1' COMMENT '1=hiện, 0=ẩn',
  `is_noi_bat` tinyint(1) DEFAULT '0' COMMENT '1 = Hiện ở danh mục nổi bật, 0 = Không',
  `is_goi_y` tinyint(1) DEFAULT '0' COMMENT '1 = Hiện ở gợi ý cho bạn, 0 = Không'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten`, `slug`, `icon_url`, `danh_muc_cha_id`, `thu_tu`, `trang_thai`, `is_noi_bat`, `is_goi_y`) VALUES
(1, 'Điện Thoại', 'dien-thoai', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775381967/categories/category_icon_1.webp', NULL, 1, 1, 1, 0),
(2, 'Máy tính bảng', 'may-tinh-bang', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775382190/categories/category_icon_2.webp', NULL, 2, 1, 1, 0),
(3, 'Laptop', 'may-tinh-xach-tay', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383217/categories/category_icon_1775383216.webp', NULL, 3, 1, 1, 0),
(4, 'Màn hình', 'man-hinh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383299/categories/category_icon_1775383298.webp', NULL, 4, 1, 1, 0),
(5, 'PC - Máy tính để bàn', 'may-tinh-de-ban', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383413/categories/category_icon_1775383412.webp', NULL, 5, 1, 1, 0),
(6, 'Phụ kiện', 'phu-kien', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383502/categories/category_icon_1775383501.webp', NULL, 6, 1, 1, 0),
(7, 'Sim FPT', 'sim-fpt', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383575/categories/category_icon_1775383574.webp', NULL, 7, 1, 1, 0),
(8, 'Đồng hồ thông minh', 'smartwatch', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383648/categories/category_icon_1775383647.webp', NULL, 8, 1, 1, 0),
(9, 'Tivi', 'tivi', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383713/categories/category_icon_1775383711.gif', NULL, 9, 1, 1, 0),
(10, 'Máy lạnh - Điều hòa', 'may-lanh-dieu-hoa', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383793/categories/category_icon_1775383791.gif', NULL, 10, 1, 1, 0),
(11, 'Robot hút bụi', 'robot-hut-bui', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383856/categories/category_icon_1775383853.gif', NULL, 11, 1, 1, 0),
(12, 'Quạt điều hòa', 'quat-dieu-hoa', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383922/categories/category_icon_1775383921.webp', NULL, 12, 1, 1, 0),
(13, 'Máy giặt', 'may-giat', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383997/categories/category_icon_1775383995.gif', NULL, 13, 1, 1, 0),
(14, 'Tủ lạnh', 'tu-lanh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384149/categories/category_icon_1775384147.gif', NULL, 14, 1, 1, 0),
(15, 'Máy lọc nước', 'may-loc-nuoc', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384461/categories/category_icon_1775384459.gif', NULL, 15, 1, 1, 0),
(16, 'Máy cũ giá rẻ', 'may-doi-tra', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384521/categories/category_icon_1775384520.webp', NULL, 16, 1, 1, 0),
(26, 'Máy sấy quần áo', 'may-say-quan-ao', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775395070/categories/category_icon_1775395068.gif', NULL, 2, 1, 0, 1),
(27, 'Camera an ninh', 'camera-an-ninh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407123/categories/category_icon_1775407122.webp', NULL, 1, 1, 0, 1),
(28, 'Điện gia dụng', 'dien-gia-dung', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407612/categories/category_icon_1775407610.webp', NULL, 3, 1, 0, 1),
(29, 'Quạt máy', 'quat', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407680/categories/category_icon_1775407679.webp', NULL, 4, 1, 0, 1),
(30, 'Máy lọc không khí', 'may-loc-khong-khi', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407771/categories/category_icon_1775407768.gif', NULL, 5, 1, 0, 1),
(31, 'Thiết bị bếp', 'thiet-bi-bep', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407878/categories/category_icon_1775407877.webp', NULL, 6, 1, 0, 1),
(32, 'Nồi cơm điện', 'noi-com-dien', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775407972/categories/category_icon_1775407970.webp', NULL, 7, 1, 0, 1),
(33, 'Sinh tố - Xay vắt ép', 'sinh-to-xay-ep', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408049/categories/category_icon_1775408048.webp', NULL, 8, 1, 0, 1),
(34, 'Nồi chiên không dầu', 'noi-chien', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408144/categories/category_icon_1775408142.webp', 28, 9, 1, 0, 1),
(35, 'Máy in', 'may-in', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408216/categories/category_icon_1775408215.webp', NULL, 10, 1, 0, 1),
(36, 'Cây nước nóng lạnh', 'cay-nuoc-nong-lanh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408296/categories/category_icon_1775408295.webp', NULL, 11, 1, 0, 1),
(37, 'Chăm sóc sức khỏe', 'cham-soc-suc-khoe', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408584/categories/category_icon_1775408583.webp', NULL, 12, 1, 0, 1),
(38, 'Máy massage', 'may-massage', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408681/categories/category_icon_1775408680.webp', NULL, 13, 1, 0, 1),
(39, 'Máy nước nóng', 'may-nuoc-nong', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408744/categories/category_icon_1775408743.webp', NULL, 14, 1, 0, 1),
(40, 'Máy hút ẩm', 'may-hut-am', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408802/categories/category_icon_1775408801.webp', NULL, 15, 1, 0, 1),
(41, 'Xe đạp', 'xe-dap', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775408851/categories/category_icon_1775408850.webp', NULL, 16, 1, 0, 1),
(42, 'Loa', 'loa', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775409105/categories/category_icon_1775409102.gif', 6, 17, 1, 0, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dia_chi`
--

CREATE TABLE `dia_chi` (
  `id` int NOT NULL,
  `nguoi_dung_id` int NOT NULL,
  `ten_nguoi_nhan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt_nhan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_nha_duong` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phuong_xa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quan_huyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tinh_thanh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_dinh` tinyint(1) DEFAULT '0' COMMENT '1 = địa chỉ mặc định'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dia_chi`
--

INSERT INTO `dia_chi` (`id`, `nguoi_dung_id`, `ten_nguoi_nhan`, `sdt_nhan`, `so_nha_duong`, `phuong_xa`, `quan_huyen`, `tinh_thanh`, `mac_dinh`) VALUES
(2, 3, 'Trương Thành Đạt', '0399746618', 'Lê Duẩn', 'Phường Tân Định', 'Quận 1', 'Thành phố Hồ Chí Minh', 1),
(4, 3, 'Trương Thành Đạt', '0399746618', '49 Hồ Thị Kỷ', 'Phường 1', 'Quận 3', 'Thành phố Hồ Chí Minh', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id` int NOT NULL,
  `ma_don_hang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hiển thị: DH20260224001',
  `nguoi_dung_id` int DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `dia_chi_id` int DEFAULT NULL COMMENT 'NULL nếu guest (dùng thong_tin_guest)',
  `ma_giam_gia_id` int DEFAULT NULL COMMENT 'Voucher áp dụng',
  `trang_thai` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `tong_tien` decimal(15,2) DEFAULT NULL COMMENT 'Tổng tiền sản phẩm',
  `phi_van_chuyen` decimal(15,2) DEFAULT '0.00',
  `tien_giam_gia` decimal(15,2) DEFAULT '0.00' COMMENT 'Số tiền được giảm',
  `tong_thanh_toan` decimal(15,2) DEFAULT NULL COMMENT 'tong_tien + phi_van_chuyen - tien_giam_gia',
  `thong_tin_guest` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON: {ten, sdt, dia_chi} cho khách vãng lai',
  `ghi_chu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của khách hàng',
  `ngay_giao_du_kien` datetime DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id`, `ma_don_hang`, `nguoi_dung_id`, `dia_chi_id`, `ma_giam_gia_id`, `trang_thai`, `tong_tien`, `phi_van_chuyen`, `tien_giam_gia`, `tong_thanh_toan`, `thong_tin_guest`, `ghi_chu`, `ngay_giao_du_kien`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'DH20260410052803', 3, 2, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-10 10:28:03', '2026-04-10 10:28:03'),
(2, 'DH20260410052949', 3, 2, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-10 10:29:49', '2026-04-10 10:29:49'),
(3, 'DH20260410053230', 3, 2, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-10 10:32:30', '2026-04-10 10:32:30'),
(4, 'DH20260410084543', 3, 2, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 13:45:43', '2026-04-10 13:45:43'),
(5, 'DH20260410084859', 3, 2, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-10 13:48:59', '2026-04-10 13:48:59'),
(6, 'DH20260410085349', 3, 2, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 13:53:49', '2026-04-10 13:53:49'),
(7, 'DH20260410085737', 3, 2, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 13:57:37', '2026-04-10 13:57:37'),
(8, 'DH20260410085821', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 13:58:21', '2026-04-10 13:58:21'),
(9, 'DH20260410090114', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-10 14:01:14', '2026-04-10 14:01:14'),
(10, 'DH20260410090407', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 14:04:07', '2026-04-10 14:04:07'),
(11, 'DH20260410090934', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 14:09:34', '2026-04-10 14:09:34'),
(12, 'DH20260410091205', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 14:12:05', '2026-04-10 14:12:05'),
(13, 'DH20260410091530', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 14:15:30', '2026-04-10 14:15:30'),
(14, 'DH20260410091633', 3, 4, NULL, 'DA_HUY', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-10 14:16:33', '2026-04-11 10:42:05'),
(15, 'DH20260411062040', 3, 4, NULL, 'HOAN_THANH', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 11:20:40', '2026-04-11 11:25:44'),
(16, 'DH20260411160413', 3, 4, NULL, 'CHO_DUYET', 69.98, 30000.00, 0.00, 30069.98, NULL, '', NULL, '2026-04-11 21:04:13', '2026-04-11 21:04:13'),
(17, 'DH20260411161022', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:10:22', '2026-04-11 21:10:22'),
(18, 'DH20260411161554', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:15:54', '2026-04-11 21:15:54'),
(19, 'DH20260411161959', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:19:59', '2026-04-11 21:19:59'),
(20, 'DH20260411162138', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:21:38', '2026-04-11 21:21:38'),
(21, 'DH20260411163442', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:34:42', '2026-04-11 21:34:42'),
(22, 'DH20260411163835', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:38:35', '2026-04-11 21:38:35'),
(23, 'DH20260411164152', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:41:52', '2026-04-11 21:41:52'),
(24, 'DH20260411164422', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:44:22', '2026-04-11 21:44:22'),
(25, 'DH20260411165448', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-11 21:54:48', '2026-04-11 21:54:48'),
(26, 'DH20260412134506', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 18:45:06', '2026-04-12 18:45:06'),
(27, 'DH20260412140034', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 19:00:34', '2026-04-12 19:00:34'),
(28, 'DH20260412140421', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 19:04:21', '2026-04-12 19:04:21'),
(29, 'DH20260412140635', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 19:06:35', '2026-04-12 19:06:35'),
(30, 'DH20260412140909', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 19:09:09', '2026-04-12 19:09:09'),
(31, 'DH20260414163032', 3, 2, NULL, 'CHO_DUYET', 17890000.00, 30000.00, 0.00, 17920000.00, NULL, '', NULL, '2026-04-14 21:30:32', '2026-04-14 21:30:32'),
(32, 'DH20260414170202', 3, 2, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-14 22:02:02', '2026-04-14 22:02:02'),
(33, 'DH20260415021846', 3, 4, NULL, 'CHO_DUYET', 34990000.00, 30000.00, 0.00, 35020000.00, NULL, '', NULL, '2026-04-15 07:18:46', '2026-04-15 07:18:46'),
(34, 'DH20260415022916', 3, 4, NULL, 'CHO_DUYET', 20490000.00, 30000.00, 0.00, 20520000.00, NULL, '', NULL, '2026-04-15 07:29:16', '2026-04-15 07:29:16'),
(35, 'DH20260415023158', 3, 4, NULL, 'CHO_DUYET', 20490000.00, 30000.00, 0.00, 20520000.00, NULL, '', NULL, '2026-04-15 07:31:58', '2026-04-15 07:31:58'),
(36, 'DH20260415023532', NULL, NULL, NULL, 'CHO_DUYET', 20490000.00, 30000.00, 0.00, 20520000.00, '{\"ten\":\"Tru01b0u01a1ng Thu00e0nh u0110u1ea1t\",\"sdt\":\"0399746618\",\"dia_chi\":\"VN\"}', '', NULL, '2026-04-15 07:35:32', '2026-04-15 07:35:32'),
(37, 'DH20260415132912', 3, 4, 1, 'DA_HUY', 34.99, 30000.00, 17.50, 30017.50, NULL, '', NULL, '2026-04-15 18:29:12', '2026-04-15 18:30:58'),
(38, 'DH20260415133136', 3, 2, 1, 'CHO_DUYET', 20490000.00, 30000.00, 200000.00, 20320000.00, NULL, '', NULL, '2026-04-15 18:31:36', '2026-04-15 18:31:36'),
(39, 'DH20260415134256', 3, 2, NULL, 'CHO_DUYET', 20490000.00, 30000.00, 0.00, 20520000.00, NULL, '', NULL, '2026-04-15 18:42:56', '2026-04-15 18:42:56'),
(40, 'DH20260415172446', 3, 2, NULL, 'DA_XAC_NHAN', 20490000.00, 30000.00, 0.00, 20520000.00, NULL, '', NULL, '2026-04-15 22:24:46', '2026-04-15 23:03:25'),
(41, 'DH20260416161638', 3, 4, NULL, 'CHO_DUYET', 440000.00, 30000.00, 0.00, 470000.00, NULL, '', NULL, '2026-04-16 21:16:38', '2026-04-16 21:16:38'),
(42, 'DH20260416195647', 3, 2, 1, 'HOAN_THANH', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 00:56:47', '2026-04-17 01:00:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gateway_health`
--

CREATE TABLE `gateway_health` (
  `id` int NOT NULL,
  `gateway_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `success_count` int NOT NULL DEFAULT '0',
  `failure_count` int NOT NULL DEFAULT '0',
  `last_success_at` datetime DEFAULT NULL,
  `last_failure_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gateway_health`
--

INSERT INTO `gateway_health` (`id`, `gateway_name`, `success_count`, `failure_count`, `last_success_at`, `last_failure_at`, `updated_at`) VALUES
(1, 'VNPay', 24, 0, '2026-04-17 00:56:47', NULL, '2026-04-17 00:56:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id` int NOT NULL,
  `nguoi_dung_id` int DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Session cho khách vãng lai',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id`, `nguoi_dung_id`, `session_id`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 3, NULL, '2026-04-04 08:53:00', '2026-04-04 08:53:00'),
(2, 4, NULL, '2026-04-04 09:51:23', '2026-04-04 09:51:23'),
(3, NULL, 'udkgpuid92f2fuj4j6ufdevqt9', '2026-04-04 16:45:37', '2026-04-04 16:45:37'),
(4, NULL, '26pt12vji07i85sfca2l3a14bv', '2026-04-05 08:18:09', '2026-04-05 08:18:09'),
(5, NULL, 'm1sko248l5sva015empeco22oj', '2026-04-06 08:59:03', '2026-04-06 08:59:03'),
(6, NULL, 'cvoeblef6iadvm3o44brsuqmc1', '2026-04-07 07:30:37', '2026-04-07 07:30:37'),
(7, NULL, 'd25d4748ortier2eh5k3t0otum', '2026-04-07 18:27:11', '2026-04-07 18:27:11'),
(8, 162, NULL, '2026-04-07 19:38:46', '2026-04-07 19:38:46'),
(9, NULL, 'jm2rdr0c9h6qrlj9m08h8i4e93', '2026-04-08 10:37:35', '2026-04-08 10:37:35'),
(10, NULL, 'oe91h09lidoviiesk8okdpm6vk', '2026-04-08 17:37:23', '2026-04-08 17:37:23'),
(11, NULL, 'p2bqtl637dhuc1f51diafigkad', '2026-04-09 15:55:40', '2026-04-09 15:55:40'),
(12, NULL, 'fq6g3h38cdco37p4u19ldu6c4v', '2026-04-10 07:14:27', '2026-04-10 07:14:27'),
(13, NULL, 'lt7um51mk9qi9fgpakfk6k59cg', '2026-04-10 13:45:01', '2026-04-10 13:45:01'),
(14, NULL, '3mgp07e26iheh58nn2n8ij07ho', '2026-04-11 10:35:41', '2026-04-11 10:35:41'),
(15, NULL, '1a2b9bd5lrqlkedfksh5gq80dp', '2026-04-12 15:43:39', '2026-04-12 15:43:39'),
(16, NULL, '7k73i8ilkrp6trnc14vgt1mlf0', '2026-04-12 19:08:46', '2026-04-12 19:08:46'),
(17, NULL, 'dkf2irbmv1uuu57luq2ef24nk5', '2026-04-12 20:58:15', '2026-04-12 20:58:15'),
(18, NULL, 'atjhmgaqrkoth9n0q8eipbaf39', '2026-04-13 08:37:49', '2026-04-13 08:37:49'),
(19, NULL, 'co40svetio12or9hduikn3vver', '2026-04-14 08:41:25', '2026-04-14 08:41:25'),
(20, NULL, 'qltro9v9955a8qlcgm28m5iuk1', '2026-04-14 18:05:50', '2026-04-14 18:05:50'),
(21, NULL, 'r9q77de5pr5k1f3936c06ocark', '2026-04-15 07:18:06', '2026-04-15 07:18:06'),
(22, NULL, 'k39hc70gqe9cap234il992qr8c', '2026-04-15 08:15:18', '2026-04-15 08:15:18'),
(23, NULL, 'ob5ai00v6p8ssdhennnb3h2bbj', '2026-04-15 13:22:03', '2026-04-15 13:22:03'),
(24, NULL, 'e28o6u2ev418e6eq0hb73n0f68', '2026-04-15 17:51:08', '2026-04-15 17:51:08'),
(25, NULL, '1re26n8cos7e2pcb9sindsh4oh', '2026-04-16 07:11:12', '2026-04-16 07:11:12'),
(26, NULL, 'o8b3sa9sqcggmukk8r380nkd6d', '2026-04-16 10:57:43', '2026-04-16 10:57:43'),
(27, NULL, 'r137e70tder7cei10vsb52puhf', '2026-04-16 19:19:07', '2026-04-16 19:19:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_san_pham`
--

CREATE TABLE `hinh_anh_san_pham` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `phien_ban_id` int DEFAULT NULL COMMENT 'NULL = ảnh chung, có giá trị = ảnh theo phiên bản/màu',
  `url_anh` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả ảnh cho SEO/accessibility',
  `la_anh_chinh` tinyint(1) DEFAULT '0' COMMENT '1 = ảnh đại diện hiển thị ở listing',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự trong gallery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinh_anh_san_pham`
--

INSERT INTO `hinh_anh_san_pham` (`id`, `san_pham_id`, `phien_ban_id`, `url_anh`, `alt_text`, `la_anh_chinh`, `thu_tu`) VALUES
(7, 7, 10, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775442699/products/product_7_1775442698_397.webp', '', 1, 1),
(12, 7, 11, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775444279/products/product_7_1775444278_970.webp', '', 0, 0),
(13, 7, 11, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775444863/products/product_7_1775444862_105.webp', '', 0, 0),
(14, 7, 10, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775444919/products/product_7_1775444918_527.webp', '', 0, 0),
(15, 2, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775478491/products/product_2_1775478490_847.webp', '', 1, 0),
(16, 8, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775480874/products/product_8_1775480872_547.webp', '', 1, 0),
(17, 9, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775481059/products/product_9_1775481057_950.webp', '', 1, 0),
(18, 10, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775736264/products/product_10_1775736261_565.webp', 'Ảnh chính', 1, 0),
(19, 11, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775736448/products/product_11_1775736446_341.webp', 'Ảnh chính', 1, 0),
(20, 7, 14, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172113/products/product_7_1776172105_239.webp', '', 0, 0),
(21, 7, 14, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172135/products/product_7_1776172129_901.webp', '', 0, 0),
(22, 7, 15, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172275/products/product_7_1776172269_621.webp', '', 0, 0),
(23, 7, 15, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172294/products/product_7_1776172288_262.webp', '', 0, 0),
(24, 7, 16, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172533/products/product_7_1776172528_108.webp', '', 0, 0),
(25, 7, 16, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776172545/products/product_7_1776172540_425.webp', '', 0, 0),
(26, 12, 17, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776302753/products/product_12_1776302749_310.webp', '32 inch', 1, 0),
(27, 12, 17, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776302775/products/product_12_1776302774_793.webp', '32 inch', 0, 0),
(29, 11, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776305858/products/product_11_1776305856_215.webp', '', 0, 0),
(30, 13, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776310120/products/product_13_1776310118_154.webp', '', 1, 0),
(31, 13, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776310137/products/product_13_1776310136_553.webp', '', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id` int NOT NULL,
  `ten_chuong_trinh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'PHAN_TRAM',
  `gia_tri_giam` decimal(15,2) DEFAULT NULL COMMENT '10 = 10% hoặc 500000 = 500k VND',
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Giảm tối đa (áp dụng nếu loại %)',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','TAM_DUNG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id`, `ten_chuong_trinh`, `loai_giam`, `gia_tri_giam`, `giam_toi_da`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(2, 'Khuyến mãi test 1', 'PHAN_TRAM', 50.00, 90.00, '2026-04-04 22:02:03', '2026-04-24 22:02:03', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_tim_kiem`
--

CREATE TABLE `lich_su_tim_kiem` (
  `id` int NOT NULL,
  `nguoi_dung_id` int NOT NULL,
  `tu_khoa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thoi_gian_tim` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_tim_kiem`
--

INSERT INTO `lich_su_tim_kiem` (`id`, `nguoi_dung_id`, `tu_khoa`, `thoi_gian_tim`) VALUES
(1, 4, 'computer', '2026-04-08 21:56:14'),
(2, 3, 'ok', '2026-04-08 23:14:58'),
(3, 3, 'ok', '2026-04-09 20:15:05'),
(4, 3, 'ok', '2026-04-09 23:33:09'),
(5, 3, 'Apple', '2026-04-15 18:03:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_giam_gia`
--

CREATE TABLE `ma_giam_gia` (
  `id` int NOT NULL,
  `ma_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: FPTSHOP50K, SALE10',
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Áp dụng nếu loại PHAN_TRAM',
  `don_toi_thieu` decimal(15,2) DEFAULT '0.00' COMMENT 'Giá trị đơn hàng tối thiểu',
  `so_luot_da_dung` int DEFAULT '0',
  `gioi_han_su_dung` int DEFAULT NULL COMMENT 'NULL = không giới hạn',
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','HET_LUOT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`id`, `ma_code`, `mo_ta`, `loai_giam`, `gia_tri_giam`, `giam_toi_da`, `don_toi_thieu`, `so_luot_da_dung`, `gioi_han_su_dung`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(1, 'SUMMER2026', 'Mã giảm giá mùa hè 2026', 'PHAN_TRAM', 50.00, 200000.00, 0.00, 3, NULL, '2026-04-14 21:27:00', '2026-04-30 21:28:00', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int NOT NULL,
  `supabase_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã định danh duy nhất từ Supabase',
  `auth_provider` enum('LOCAL','GOOGLE','FACEBOOK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'LOCAL' COMMENT 'Nguồn tạo tài khoản',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cho phép NULL nếu đăng nhập bằng nền tảng khác',
  `ho_ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện',
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('NAM','NU','KHAC') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_tai_khoan` enum('ADMIN','MEMBER') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MEMBER',
  `trang_thai` enum('ACTIVE','BLOCKED','UNVERIFIED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `verification_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `forget_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token đặt lại mật khẩu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `supabase_id`, `auth_provider`, `email`, `mat_khau`, `ho_ten`, `sdt`, `avatar_url`, `ngay_sinh`, `gioi_tinh`, `loai_tai_khoan`, `trang_thai`, `verification_token`, `ngay_tao`, `ngay_cap_nhat`, `forget_token`) VALUES
(1, NULL, 'LOCAL', 'test_1773155576@example.com', '$2y$10$IcOj9mDvjFD1jdTaRVVY0eoywjosOpf80oNvDP3KWZqxl6TMUDTW6', 'Nguyễn Văn Test', '0901234567', NULL, NULL, 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(2, NULL, 'LOCAL', 'admin_1773155576@example.com', '$2y$10$dyvFZGKucag4pZ.RXkSQN.XTO.0tgpfouhBOIg7PyKocR2N7.uCqO', 'Admin Test', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(3, NULL, 'LOCAL', 'dat82770@gmail.com', 'cbd5140549732304f6590c5d13afb4fabd68c357', 'Trương Thành Đạt', '0399746612', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775357973/avatars/avatar_user_3.jpg', '2006-10-15', 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-28 17:19:23', '2026-04-16 14:38:33', NULL),
(4, NULL, 'LOCAL', 'admin@fptshop.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-29 19:08:00', '2026-04-06 20:48:32', NULL),
(5, NULL, 'LOCAL', 'datweb07@gmail.com', 'e13187e9e3517e5c3c4c6cecc580d8d9880910a0', 'loc', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-30 08:47:17', '2026-03-30 08:50:42', NULL),
(6, NULL, 'LOCAL', 'dattruong.31241024873@st.ueh.edu.vn', 'e42c0141250d02dad20c86609d5d19d155f12717', 'ok', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-30 08:59:17', '2026-03-30 09:07:36', NULL),
(7, NULL, 'LOCAL', 'test_reset_1774858467@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '771016c4c17b6d983a360510b62d0747388599bc0281821c4675e4b38436dc6b', '2026-03-30 10:14:27', '2026-03-30 10:14:27', NULL),
(8, NULL, 'LOCAL', 'test_reset_1774858550@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '8b88305a96792e1eccbd100868d5e316abd18d501b3d3a4cbaf0ca6b9c3b7029', '2026-03-30 10:15:50', '2026-03-30 15:15:50', '397e96fac4d873ea58648f341799fda30935651e3fae792a694d2755b84aaa4e'),
(42, NULL, 'LOCAL', 'test_reset_1774859235@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '92efe7c252315d4fcd464e9a591c80628cc15b3f296d9b503cb1cc67e1cd3edb', '2026-03-30 10:27:15', '2026-03-30 15:27:15', 'cff85a50b371038fb5bc4f50064d4da55b655fbb83955c03c600317972b181b3'),
(161, NULL, 'LOCAL', 'hsntk1610@gmail.com', '0bf7e28d9ad8eb2c7afa624bcbc7afe8eeadbae0', 'nguyentankhiem', NULL, '/public/uploads/avatars/avatar_161_1774929615.jpg', NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-31 05:58:29', '2026-03-31 06:00:15', NULL),
(162, 'a8f80858-0337-43c4-b903-39fb3186cafb', 'GOOGLE', 'dat158623@gmail.com', NULL, 'Đạt Trương', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocI6h_MaEuzfcRIyqnN2FGUDrsUfwyPpEN_QJFd7FcbHEpg6YA=s96-c', NULL, NULL, 'MEMBER', 'ACTIVE', NULL, '2026-04-07 14:38:45', '2026-04-07 14:38:45', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phien_ban_san_pham`
--

CREATE TABLE `phien_ban_san_pham` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã kho duy nhất',
  `ten_phien_ban` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'iPhone 16 Pro Max 256GB',
  `mau_sac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đen Titan, Trắng, Xanh...',
  `thuoc_tinh_bien_the` json DEFAULT NULL COMMENT 'Lưu chuỗi JSON: {"RAM": "8GB", "Dung lượng": "256GB"} hoặc {"Công suất": "1 HP"}',
  `cau_hinh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả cấu hình khác (nếu có)',
  `gia_ban` decimal(15,2) DEFAULT NULL COMMENT 'Giá bán hiện tại',
  `gia_goc` decimal(15,2) DEFAULT NULL COMMENT 'Giá gốc (giá gạch ngang)',
  `so_luong_ton` int DEFAULT '0',
  `trang_thai` enum('CON_HANG','HET_HANG','NGUNG_BAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CON_HANG'
) ;

--
-- Đang đổ dữ liệu cho bảng `phien_ban_san_pham`
--

INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `thuoc_tinh_bien_the`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(10, 7, 'IP15PM-256-TITAN', 'iPhone 15 Pro Max 256GB Titan Tự Nhiên', 'Titan Tự Nhiên', '{\"RAM\": \"128GB\"}', NULL, 34990000.00, 34990000.00, 40, 'CON_HANG'),
(11, 7, 'IP15PM-256-TIM', 'iPhone 15 Pro Max 256GB Tím', 'Tím', '{\"RAM\": \"128GB\"}', NULL, 34.99, 34.99, 44, 'CON_HANG'),
(12, 2, 'samsung-galaxy-s26-12gb-256gb', 'Samsung Galaxy S26 5G 12GB 256GB', 'Đen', NULL, NULL, 20490000.00, 25990000.00, 93, 'CON_HANG'),
(13, 10, 'CI1-TRANG', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP Trắng', 'Trắng', '{\"Cong_suat_Dung_tich\": \"1 HP - 9.350 BTU\"}', NULL, 9999999.00, 999999999.00, 100, 'CON_HANG'),
(14, 7, 'iphone-15-128gb', 'iPhone 15 128GB', 'Hồng', '{\"RAM\": \"128GB\"}', NULL, 17890000.00, 19590000.00, 99, 'CON_HANG'),
(15, 7, 'iphone-15-256gb', 'iPhone 15 256GB', 'Xanh dương', '{\"RAM\": \"256GB\"}', NULL, 20890000.00, 22490000.00, 100, 'CON_HANG'),
(16, 7, 'iphone-15-128gb-xanh-duong', 'iPhone 15 128GB', 'Xanh dương', '{\"RAM\": \"128GB\"}', NULL, 17890000.00, 19590000.00, 100, 'CON_HANG'),
(17, 12, '00920820', 'Xiaomi Google TV QLED 32 inch HD A Pro 2026 L32MB-APSEA', 'Đen', '{\"Màn hình\": \"32 inch\"}', NULL, 4190000.00, 4490000.00, 100, 'CON_HANG'),
(18, 11, '00907960', 'Máy lọc nước nóng lạnh RO Hydrogen Kangaroo KG11A6 11 lõi', 'Đen', NULL, NULL, 8990000.00, 11590000.00, 100, 'CON_HANG'),
(19, 13, '00922445', 'Củ sạc nhanh 1 cổng 25W USB-C PPS Wall Charger Belkin', 'Trắng', NULL, NULL, 440000.00, 490000.00, 97, 'CON_HANG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `refund`
--

CREATE TABLE `refund` (
  `id` int NOT NULL,
  `thanh_toan_id` int NOT NULL COMMENT 'ID giao dịch thanh toán gốc',
  `gateway_refund_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID hoàn tiền từ cổng thanh toán',
  `amount` decimal(15,2) NOT NULL COMMENT 'Số tiền hoàn',
  `status` enum('PENDING','COMPLETED','FAILED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do hoàn tiền',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `admin_id` int DEFAULT NULL COMMENT 'ID admin thực hiện hoàn tiền'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `refund`
--

INSERT INTO `refund` (`id`, `thanh_toan_id`, `gateway_refund_id`, `amount`, `status`, `reason`, `created_at`, `completed_at`, `admin_id`) VALUES
(1, 41, NULL, 270000.00, 'PENDING', 'test', '2026-04-17 01:03:32', NULL, 4),
(2, 41, NULL, 270000.00, 'FAILED', 'test', '2026-04-17 01:05:55', NULL, 4),
(3, 41, NULL, 270000.00, 'FAILED', 'test', '2026-04-17 01:07:59', NULL, 4),
(4, 41, 'REFUND_41_1776362964', 270000.00, 'COMPLETED', 'test', '2026-04-17 01:09:24', '2026-04-17 01:09:24', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id` int NOT NULL,
  `danh_muc_id` int DEFAULT NULL,
  `ten_san_pham` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL: iphone-16-pro-max',
  `hang_san_xuat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Apple, Samsung, Xiaomi...',
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gia_hien_thi` decimal(15,2) DEFAULT NULL COMMENT 'Giá "từ" hiển thị (giá thấp nhất phiên bản)',
  `diem_danh_gia` float DEFAULT '0',
  `trang_thai` enum('CON_BAN','NGUNG_BAN','SAP_RA_MAT','HET_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CON_BAN',
  `noi_bat` tinyint(1) DEFAULT '0' COMMENT '1 = hiện trên banner/trang chủ',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten_san_pham`, `slug`, `hang_san_xuat`, `mo_ta`, `gia_hien_thi`, `diem_danh_gia`, `trang_thai`, `noi_bat`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'IPhone', 'iphone', 'Apple', 'Smartphone', 999.00, 0, 'CON_BAN', 0, '2026-03-30 20:51:28', '2026-04-14 20:54:52'),
(2, 1, 'Điện thoại Samsung', 'sam-sung', 'Samsung', 'Điện thoại thông minh', NULL, 5, 'CON_BAN', 0, '2026-04-02 14:47:33', '2026-04-16 20:51:50'),
(7, 1, 'iPhone 15', 'iphone-15', 'Apple', 'Với iPhone 15, bạn sẽ được tận hưởng những trải nghiệm cao cấp trên một thiết bị bền bỉ và thanh lịch. Sản phẩm gây ấn tượng với màn hình Dynamic Island, camera độ phân giải siêu cao cùng nhiều chế độ quay chụp xuất sắc. Nhờ cổng USB-C, trải nghiệm kết nối của iPhone 15 thực sự khác biệt so với các thế hệ trước.', 34990000.00, 5, 'CON_BAN', 1, '2026-04-04 09:59:16', '2026-04-14 20:02:56'),
(8, 3, 'Laptop Lenovo ThinkPad X1 Carbon Gen 13 U7 258V/AI/32GB/1TB/14\"OLED 2.8K/W11PRO (21NS010JVN)', 'lenovo-thinkpad-x1-carbon-gen-13-u7-258v-21ns010jvn', 'Lenovo', 'Lenovo ThinkPad X1 Carbon Gen 13 là chiếc laptop doanh nhân cao cấp dành cho những người cần một thiết bị vừa mạnh mẽ, lại vừa siêu nhẹ để dễ dàng mang theo. Sở hữu bộ vi xử lý AI Intel Core Ultra 7 258V đầu bảng, màn hình OLED 14 inch sắc nét nhưng với thiết kế sợi Carbon, ThinkPad X1 Carbon Gen 13 chỉ có trọng lượng vỏn vẹn 1 kg, cho khả năng di động bậc nhất hiện nay.', NULL, 0, 'NGUNG_BAN', 1, '2026-04-06 20:04:49', '2026-04-07 09:42:55'),
(9, 6, 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka', 'op-lung-magsafe-samsung-s26-ultra-ultra-slim-with-pitatap-moonrise-pitaka', 'Samsung', 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka là sự kết hợp giữa nghệ thuật chế tác và công nghệ tối ưu trải nghiệm. Thiết kế Moonrise nổi bật với hiệu ứng chuyển sắc độc đáo trên nền sợi Aramid cao cấp. Sản phẩm ôm sát thân máy, duy trì vẻ nguyên bản của Galaxy S26 Ultra. Đồng thời, PitaTap cùng Aaron Button mở ra cách tương tác hoàn toàn mới, nhanh gọn và chính xác.', NULL, 0, 'NGUNG_BAN', 0, '2026-04-06 20:10:35', '2026-04-07 09:42:57'),
(10, 10, 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP', 'comfee-inverter-1-hp-cfs-10vgpf', 'Inverter', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGPF hỗ trợ làm lạnh hiệu quả và mang lại sự tiện lợi cho người dùng. Với công suất 1 HP, thiết bị này phù hợp với các căn phòng có diện tích dưới 15m². Ngoài thiết kế tinh tế, sang trọng, máy còn tích hợp nhiều tính năng thông minh như kết nối với hệ sinh thái nhà thông minh, điều khiển bằng giọng nói và các chế độ tiết kiệm điện hiệu quả', NULL, 5, 'CON_BAN', 0, '2026-04-08 19:10:33', '2026-04-16 22:45:23'),
(11, 15, 'Máy lọc nước nóng lạnh RO Hydrogen Kangaroo KG11A6 11 lõi', 'may-loc-nuoc-nong-lanh-ro-hydrogen-kangaroo-11-loi-kg11a6', 'Kangaroo', 'Máy lọc nước Kangaroo Hydrogen nóng lạnh KG11A6 là dòng máy lọc nước vừa ra mắt trong năm 2024 thuộc thương hiệu Kangaroo. Do đó, những tinh hoa công nghệ trong việc đầu tư và thiết kế hệ thống siêu lõi lọc làm tăng hiệu năng lọc nước hơn bao giờ hết, không chỉ loại bỏ chất bẩn mà còn bù khoáng cho cơ thể', NULL, 0, 'CON_BAN', 0, '2026-04-08 21:30:56', '2026-04-08 21:30:56'),
(12, 9, 'Xiaomi Google TV QLED 32 inch HD A Pro 2026 L32MB-APSEA', 'xiaomi-google-tivi-l-mb-apsea', 'Xiaomi', 'Xiaomi Google Tivi L MB-APSEA là mẫu tivi nhỏ gọn, phù hợp với nhiều không gian sống. Thiết bị này được trang bị công nghệ QLED tiên tiến, tái hiện màu sắc sống động và trung thực. Bên cạnh đó, thiết kế kim loại tinh tế cùng giao diện Google TV thân thiện hứa hẹn mang lại trải nghiệm giải trí thuận tiện cho mọi thành viên trong gia đình.\r\n\r\nCông nghệ QLED tái hiện màu sắc sống động\r\nXiaomi Google Tivi L MB-APSEA sử dụng màn hình QLED với dải màu rộng và khả năng điều chỉnh chính xác, nhờ vậy mỗi khung hình hiện lên đều rực rỡ và chân thật. Công nghệ này còn đáp ứng chuẩn màu DCI-P3 vốn được ứng dụng trong ngành điện ảnh Hollywood, đem đến những gam màu sống động và cuốn hút. Với khả năng hiển thị 16,7 triệu màu cùng độ phủ màu DCI-P3 đạt 90%, từng chi tiết nhỏ đều được thể hiện sắc nét và ấn tượng.', NULL, 0, 'CON_BAN', 1, '2026-04-16 08:12:58', '2026-04-16 08:12:58'),
(13, 6, 'Củ sạc nhanh 1 cổng 25W USB-C PPS Wall Charger Belkin', 'cu-sac-nhanh-1-cong-25w-usb-c-pps-wall-charger-belkin', 'Belkin', 'Củ sạc nhanh 1 cổng 25 W USB-C PPS Wall Charger Belkin là một giải pháp sạc nhanh đáng tin cậy, tối ưu cho các thiết bị như iPhone, Samsung và những thiết bị hỗ trợ USB-C PD. Công suất 25W rút ngắn đáng kể thời gian nạp pin, mang lại trải nghiệm sử dụng liền mạch và ổn định. Thiết kế nhỏ gọn và tính tương thích cao giúp củ sạc này trở thành một người bạn đồng hành lý tưởng trong các chuyến đi xa.\r\nCủ sạc Belkin 25 W USB-C PPS Wall Charger được thiết kế để tương thích với iPhone, Samsung và nhiều thiết bị hỗ trợ chuẩn USB-C PD, giúp bạn sử dụng một củ sạc duy nhất cho tất cả các thiết bị này. Với khả năng sạc nhanh, bạn sẽ không phải lo lắng về việc tìm kiếm các bộ sạc khác nhau nữa. Dù là iPhone hay Samsung, sản phẩm này đều mang đến một giải pháp sạc đơn giản và hiệu quả cho mọi nhu cầu.', NULL, 5, 'CON_BAN', 1, '2026-04-16 10:23:11', '2026-04-16 21:15:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham_khuyen_mai`
--

CREATE TABLE `san_pham_khuyen_mai` (
  `san_pham_id` int NOT NULL,
  `khuyen_mai_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham_khuyen_mai`
--

INSERT INTO `san_pham_khuyen_mai` (`san_pham_id`, `khuyen_mai_id`) VALUES
(2, 2),
(7, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` int NOT NULL,
  `don_hang_id` int NOT NULL,
  `nguoi_duyet_id` int DEFAULT NULL COMMENT 'Admin duyệt thanh toán',
  `gateway_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo) for idempotency (Req 8.5)',
  `gateway_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment gateway identifier (VNPAY, MOMO, COD)',
  `expiration_time` datetime DEFAULT NULL COMMENT 'Transaction expiration timestamp (15 minutes from creation) (Req 6.1)',
  `payment_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Payment URL generated by gateway for customer redirect',
  `error_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Error code from payment gateway (Req 7.6)',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'User-friendly error message in Vietnamese (Req 7.6)',
  `phuong_thuc` enum('COD','CHUYEN_KHOAN','QR','TRA_GOP','VI_DIEN_TU','ZALOPAY') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `anh_bien_lai` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL ảnh biên lai chuyển khoản',
  `trang_thai_duyet` enum('CHO_DUYET','THANH_CONG','THAT_BAI','HOAN_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `ghi_chu_duyet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Admin ghi chú khi duyệt',
  `ngay_thanh_toan` datetime DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thanh_toan`
--

INSERT INTO `thanh_toan` (`id`, `don_hang_id`, `nguoi_duyet_id`, `gateway_transaction_id`, `gateway_name`, `expiration_time`, `payment_url`, `error_code`, `error_message`, `phuong_thuc`, `so_tien`, `anh_bien_lai`, `trang_thai_duyet`, `ghi_chu_duyet`, `ngay_thanh_toan`, `ngay_duyet`) VALUES
(1, 1, NULL, NULL, 'VNPAY', '2026-04-10 05:43:03', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260410052803&vnp_CurrCode=VND&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%231&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=%C4%90I%E1%BB%80N_TMN_CODE_C%E1%BB%A6A_B%E1%BA%A0N_V%C3%80O_%C4%90%C3%82Y&vnp_TxnRef=1&vnp_Version=2.1.0&vnp_SecureHash=744beafb456ec2ede483a5ec12647da5a19ada3dd80a91669816fea7d728d22dc02a7e3fabc0c5196c40cf1681ba502cb0e6b8e12e4f600cd64142e417ba021d', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-10 05:28:03', NULL),
(2, 2, NULL, NULL, 'VNPAY', '2026-04-10 05:44:49', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260410102949&vnp_CurrCode=VND&vnp_ExpireDate=20260410104449&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%232&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=%C4%90I%E1%BB%80N_TMN_CODE_C%E1%BB%A6A_B%E1%BA%A0N_V%C3%80O_%C4%90%C3%82Y&vnp_TxnRef=2&vnp_Version=2.1.0&vnp_SecureHash=417052449c9262e095ad5c0434f966afc5d45587cbe396a09dbdc901df1455a382c2b3168e4604eecd736000da72dab933c2294c0f815601c8d505ba3ac7d469', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-10 05:29:49', NULL),
(3, 3, NULL, NULL, 'VNPAY', '2026-04-10 05:47:30', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260410103230&vnp_CurrCode=VND&vnp_ExpireDate=20260410104730&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%233&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=3&vnp_Version=2.1.0&vnp_SecureHash=aad64a02b9d4559545a8109e7859bfef92da9fb1a955318f9ec91d7da8b0d5a49b8b9456f91d9e3b6167ea35ad5b1eac209c85df42131cbcc045b06fbca1a71f', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-10 05:32:30', NULL),
(4, 4, NULL, NULL, 'VNPAY', '2026-04-10 09:00:43', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410134543&vnp_CurrCode=VND&vnp_ExpireDate=20260410140043&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%234&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=4&vnp_Version=2.1.0&vnp_SecureHash=2fdb13b45e681970117512f9ab1c596aae47f5887828818a5bfedaf6c82335d3382e1d7364cafdbb7e44e552b99f40fc82b90c6cb4feda4954c6c74187a3d223', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 08:45:43', NULL),
(5, 5, NULL, NULL, 'VNPAY', '2026-04-10 09:03:59', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260410134859&vnp_CurrCode=VND&vnp_ExpireDate=20260410140359&vnp_IpAddr=%3A%3A1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%235&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=5&vnp_Version=2.1.0&vnp_SecureHash=d3562b0ce081c13bc1b158d5fa8106fe5a6bae29d635424e415ea0de9f88ee4568446e167fce29c283eed5b3cf7303a8bf2d674b96a036bf62a6f22aae59a9b7', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-10 08:48:59', NULL),
(6, 6, NULL, NULL, 'VNPAY', '2026-04-10 09:08:49', NULL, NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 08:53:49', NULL),
(7, 7, NULL, NULL, 'VNPAY', '2026-04-10 09:12:37', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410135737&vnp_CurrCode=VND&vnp_ExpireDate=20260410141237&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh_toan_don_hang_7&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=7&vnp_Version=2.1.0&vnp_SecureHash=36a696c0f2619479cf23f00a376a83e09a0440c3d2b510ffa2fba875e0f10238e444dc3549b4cc77a896bb615b8ebd7e8bb3301bc2abe42fc8e7f515d4b01380', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 08:57:37', NULL),
(8, 8, NULL, NULL, 'VNPAY', '2026-04-10 09:13:21', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410135821&vnp_CurrCode=VND&vnp_ExpireDate=20260410141321&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh_toan_don_hang_8&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=8&vnp_Version=2.1.0&vnp_SecureHash=77d683c41f96f78278b1f760e49978e8fab35455e20cfb9f88d0e3c5e2e78809936f6afff6e8f5d27930e82f7788b49d2b6746141b00384d4504749726228c31', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 08:58:21', NULL),
(9, 9, NULL, NULL, 'VNPAY', '2026-04-10 09:16:14', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260410140114&vnp_CurrCode=VND&vnp_ExpireDate=20260410141614&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh_toan_don_hang_9&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=9&vnp_Version=2.1.0&vnp_SecureHash=6888f4d93a2849db717d11a16e4f12e3680332c13c7cf1eeccba75647d612173da7ccf08ece750cdf9067426a8753446e3e8df7d35181084285d153e41b020a9', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:01:14', NULL),
(10, 10, NULL, NULL, 'VNPAY', '2026-04-10 09:19:07', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410140407&vnp_CurrCode=VND&vnp_ExpireDate=20260410141907&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=ThanhToanDonHang&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=1775804647&vnp_Version=2.1.0&vnp_SecureHash=57d210a640ebcbb96442bc7cc45e4b05506c7e492015ea100ad574d3aa5edc0d1d174a6b5fff4d0a3289b0a757cac6076c816b21bf642e30ce231a6b0d695d77', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:04:07', NULL),
(11, 11, NULL, NULL, 'VNPAY', '2026-04-10 09:24:35', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410140935&vnp_CurrCode=VND&vnp_ExpireDate=20260410142435&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh_toan_don_hang_11&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=11_1775804975&vnp_Version=2.1.0&vnp_SecureHash=8e1459f2f3462dbd93f9b5b5533f85b48ee2e310dba9ea6630e41a34f78e20c6a0c502f4559664835e67226545636c9587cb73d0cb364d0ef72212199d2b87c0', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:09:35', NULL),
(12, 12, NULL, NULL, 'VNPAY', '2026-04-10 09:27:05', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410141205&vnp_CurrCode=VND&vnp_ExpireDate=20260410142705&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+12&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=12&vnp_Version=2.1.0&vnp_SecureHash=6b459e42de41a26b76161aa56791e46e18c02fb6546f2de401328954a50ccf5c3dd7bbd378ec6e064154e107a9e9ce309a17a3d3df5543a1ef366be95e66e173', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:12:05', NULL),
(13, 13, NULL, NULL, 'VNPAY', '2026-04-10 09:30:30', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410141530&vnp_CurrCode=VND&vnp_ExpireDate=20260410143030&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+13&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=13&vnp_Version=2.1.0&vnp_SecureHash=ca367cdd4bf604db81af08b3d7fad4e51fe05fbca19af65e2e05574ca939319044c7144ed8989987e6dccb9c36499727b2d824f97aeb2482263ac1ca155bce88', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:15:30', NULL),
(14, 14, NULL, NULL, 'VNPAY', '2026-04-10 09:31:33', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3502000000&vnp_Command=pay&vnp_CreateDate=20260410141633&vnp_CurrCode=VND&vnp_ExpireDate=20260410143133&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+14&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=14&vnp_Version=2.1.0&vnp_SecureHash=9f48fe9fdf580bbd7a5cf48cfcb01fc5be6986d64d35745299352e935656ca90b6119649ea8e34ef7eb9de6eb16742ed8fda89fbf63dc59c9d59845fc8757158', NULL, NULL, 'CHUYEN_KHOAN', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-10 09:16:33', NULL),
(15, 15, 3, NULL, 'VNPAY', '2026-04-11 06:35:40', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260411112040&vnp_CurrCode=VND&vnp_ExpireDate=20260411113540&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+15&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=15&vnp_Version=2.1.0&vnp_SecureHash=a1728563e4945c763878cd43b45e486631fd30203b18a1598065f276bb5341b00e53b973f04e9b6cf8ddbe393aabf485e66a21f9c1b8e855cab8f9b353813152', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'THANH_CONG', NULL, '2026-04-11 06:20:40', '2026-04-11 06:25:07'),
(16, 16, NULL, NULL, 'VNPAY', '2026-04-11 16:19:13', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3006900&vnp_Command=pay&vnp_CreateDate=20260411210413&vnp_CurrCode=VND&vnp_ExpireDate=20260411211913&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+16&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=16&vnp_Version=2.1.0&vnp_SecureHash=d722c9c2fb52120163447b3539346316a0b82bff611edf24678cb0d50add5eb360368f739f27b9198c5c0eb03d2f3fbb84adb06f6434a180d24d6d6767ff94f2', NULL, NULL, 'CHUYEN_KHOAN', 30069.98, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:04:13', NULL),
(17, 17, NULL, NULL, 'VNPAY', '2026-04-11 16:25:22', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260411211022&vnp_CurrCode=VND&vnp_ExpireDate=20260411212522&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+17&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=17&vnp_Version=2.1.0&vnp_SecureHash=2c70d209d8899f9b5bbc7a7221f447f93219f4c4781acaf1d0e5f6e17d7e5e65b8a42cf3ca4047266447ca921c2662b94d6c27bc0a37b5676d082a7c90e68dcd', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:10:22', NULL),
(18, 18, NULL, NULL, 'VNPAY', '2026-04-11 16:30:54', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260411211554&vnp_CurrCode=VND&vnp_ExpireDate=20260411213054&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=ThanhToanDH&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=1775916954&vnp_Version=2.1.0&vnp_SecureHash=5cbc0c5f016f836e0d46223326e5b85ad8de2dbf7c2fcae1a61311ac47de064e17afd7ea4a0b677af0cc5edd486984ecb85d62448a9e254df5a73f712bd931ca', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:15:54', NULL),
(19, 19, NULL, NULL, 'VNPAY', '2026-04-11 16:34:59', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260411211959&vnp_CurrCode=VND&vnp_ExpireDate=20260411213459&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+19&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=19&vnp_Version=2.1.0&vnp_SecureHash=03d2179fab13d2b4780d6e31f47458086c5b6122103b6f0ee3f309d45e91bdebc1422d897efc8cca5f9f0cce5503f8837ffc5215e9b99693037ce79252121f89', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:19:59', NULL),
(20, 20, NULL, NULL, 'VNPAY', '2026-04-11 16:36:38', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3003400&vnp_Command=pay&vnp_CreateDate=20260411212138&vnp_CurrCode=VND&vnp_ExpireDate=20260411213638&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+20&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=20&vnp_Version=2.1.0&vnp_SecureHash=7e7b4e63406aec459de6afddfad66d8b188c37cd8f3b83a51f49d07705a26ad884b4f5650a2c9052c8e3dae731ed1067edb351cc15350f4b11343724e8d7fc8e', NULL, NULL, 'CHUYEN_KHOAN', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:21:38', NULL),
(21, 22, NULL, NULL, 'ZALOPAY', '2026-04-11 16:53:35', NULL, NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:38:35', NULL),
(22, 23, NULL, NULL, 'ZALOPAY', '2026-04-11 16:56:52', NULL, NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:41:52', NULL),
(23, 24, NULL, NULL, 'ZALOPAY', '2026-04-11 16:59:22', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ3Faam9aNHViUGJGSUtrMDluaEVxdmciLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:44:22', NULL),
(24, 25, NULL, NULL, 'ZALOPAY', '2026-04-11 17:09:48', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0pSMUdXeW1VUGNhU1hpc2pwNlNXMlEiLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:54:48', NULL),
(25, 26, NULL, NULL, 'ZALOPAY', '2026-04-12 14:00:06', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0FYZFJNbnBncFhUanhQN0w4akx4dmciLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 13:45:06', NULL),
(26, 27, NULL, NULL, 'ZALOPAY', '2026-04-12 14:15:34', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0VWdHRSVS1iZExwZnRkaEZNZ1Fyb3ciLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:00:34', NULL),
(27, 28, NULL, NULL, 'ZALOPAY', '2026-04-12 14:19:21', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQzlrTW04UWR6a3hLRXNHWC00SkUxV1EiLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:04:21', NULL),
(28, 29, NULL, NULL, 'ZALOPAY', '2026-04-12 14:21:35', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQzA3Nk5mSFpXcEt4eDFIdDlURmNDd2ciLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:06:35', NULL),
(29, 30, NULL, NULL, 'ZALOPAY', '2026-04-12 14:24:09', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ202NlZNRG5tb1FocWZQTGZYRzgyS3ciLCJhcHBpZCI6MjU1M30=', NULL, ' [Migrated from COD - Gateway removed]', 'COD', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:09:09', NULL),
(30, 31, NULL, NULL, 'COD', '2026-04-14 16:45:32', NULL, NULL, NULL, 'COD', 17920000.00, NULL, 'CHO_DUYET', NULL, '2026-04-14 16:30:32', NULL),
(31, 32, NULL, NULL, 'COD', '2026-04-14 17:17:02', NULL, NULL, NULL, 'COD', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-14 17:02:02', NULL),
(32, 33, NULL, NULL, 'COD', '2026-04-15 02:33:46', NULL, NULL, NULL, 'COD', 35020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 02:18:46', NULL),
(33, 34, NULL, NULL, 'COD', '2026-04-15 02:44:16', NULL, NULL, NULL, 'COD', 20520000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 02:29:16', NULL),
(34, 35, NULL, NULL, 'COD', '2026-04-15 02:46:58', NULL, NULL, NULL, 'COD', 20520000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 02:31:58', NULL),
(35, 36, NULL, NULL, 'COD', '2026-04-15 02:50:32', NULL, NULL, NULL, 'COD', 20520000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 02:35:32', NULL),
(36, 37, NULL, NULL, 'VNPAY', '2026-04-15 13:44:12', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3001700&vnp_Command=pay&vnp_CreateDate=20260415182912&vnp_CurrCode=VND&vnp_ExpireDate=20260415184412&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+37&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=36&vnp_Version=2.1.0&vnp_SecureHash=e7591753b87cbf8756dc6f51b4217007db196ec5b7a15d43a6e2abcdc557d1452f7c3dd57b017002911e48b18e9ec60f54ccca804d30b6c8fdca8ca2e239b12d', NULL, NULL, 'CHUYEN_KHOAN', 30017.50, NULL, 'CHO_DUYET', NULL, '2026-04-15 13:29:12', NULL),
(37, 38, NULL, NULL, 'VNPAY', '2026-04-15 13:46:36', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=2032000000&vnp_Command=pay&vnp_CreateDate=20260415183136&vnp_CurrCode=VND&vnp_ExpireDate=20260415184636&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+38&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=37&vnp_Version=2.1.0&vnp_SecureHash=e7cedb3ee6b262f6dfd89128e3addce784da96eed5ef72f4f1b107e84db3ca3ab6829457347903eae5c19eb99a8335183a5ee2644ab7f15ae0658c8d776c7722', NULL, NULL, 'CHUYEN_KHOAN', 20320000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 13:31:36', NULL),
(38, 39, NULL, NULL, 'VNPAY', '2026-04-15 13:57:56', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=2052000000&vnp_Command=pay&vnp_CreateDate=20260415184256&vnp_CurrCode=VND&vnp_ExpireDate=20260415185756&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+39&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=38&vnp_Version=2.1.0&vnp_SecureHash=68127297e8bad89f35d74c31d053807d627751092fc40fa0adecc6caa941f6e79cd6130955d689f3f796f23b0b0e1576b3de6f6b3bf663824dbb71bea3ae732b', NULL, NULL, 'CHUYEN_KHOAN', 20520000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 13:42:56', NULL),
(39, 40, NULL, NULL, 'VNPAY', '2026-04-15 17:39:46', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=2052000000&vnp_Command=pay&vnp_CreateDate=20260415222446&vnp_CurrCode=VND&vnp_ExpireDate=20260415223946&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+40&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=39&vnp_Version=2.1.0&vnp_SecureHash=d7ab0ef8776e7b2f76654b71c17b9ec7c3cdf21de0bb86ab70082dc09199400c0782a941e070863388542a2a903538b8b4d407d906aa6fe56aad1c3ed51967d3', NULL, NULL, 'CHUYEN_KHOAN', 20520000.00, NULL, 'CHO_DUYET', NULL, '2026-04-15 17:24:46', NULL),
(40, 41, NULL, NULL, 'COD', '2026-04-16 16:31:38', NULL, NULL, NULL, 'COD', 470000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 16:16:38', NULL),
(41, 42, 4, NULL, 'VNPAY', '2026-04-16 20:11:47', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=27000000&vnp_Command=pay&vnp_CreateDate=20260417005647&vnp_CurrCode=VND&vnp_ExpireDate=20260417011147&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+42&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=41&vnp_Version=2.1.0&vnp_SecureHash=f317405f8a83c603ca2d0398e0557ea4ca00b06e569c0e616a31e5c3d22c52598965d3e559d03943285497f408cc8eecf448cb3a11c82d2dc077dbd11f58c67d', NULL, NULL, 'CHUYEN_KHOAN', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 19:56:47', '2026-04-16 20:00:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_so_ky_thuat`
--

CREATE TABLE `thong_so_ky_thuat` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `ten_thong_so` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ram, Chip, Pin, Màn hình...',
  `gia_tri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '8GB, A18 Pro, 5000mAh...',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_so_ky_thuat`
--

INSERT INTO `thong_so_ky_thuat` (`id`, `san_pham_id`, `ten_thong_so`, `gia_tri`, `thu_tu`) VALUES
(13, 7, 'Màn hình', '6.7 inch', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh_danh_muc`
--

CREATE TABLE `thuoc_tinh_danh_muc` (
  `id` int NOT NULL,
  `danh_muc_id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên key JSON (VD: RAM, Kich_thuoc)',
  `label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nhãn hiển thị cho Admin (VD: Dung lượng RAM)',
  `placeholder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gợi ý nhập liệu',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT 'Loại thẻ input: text, number...',
  `col` int DEFAULT '6' COMMENT 'Kích thước cột Bootstrap (6 = nửa dòng, 12 = cả dòng)',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thuoc_tinh_danh_muc`
--

INSERT INTO `thuoc_tinh_danh_muc` (`id`, `danh_muc_id`, `name`, `label`, `placeholder`, `type`, `col`, `thu_tu`) VALUES
(1, 1, 'RAM', 'Dung lượng RAM', 'VD: 8GB', 'text', 6, 1),
(2, 9, 'Màn hình', 'Kích thước màn hình', 'Nhập kích thước màn hình', 'text', 6, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction_log`
--

CREATE TABLE `transaction_log` (
  `id` int NOT NULL,
  `thanh_toan_id` int NOT NULL COMMENT 'Foreign key to thanh_toan table',
  `gateway_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo)',
  `gateway_name` enum('VNPAY','COD','REFUND') COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Payment request sent to gateway',
  `response_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Response received from gateway',
  `callback_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Callback/IPN data received from gateway',
  `status` enum('PENDING','SUCCESS','FAILED','EXPIRED','AMOUNT_MISMATCH') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT 'Transaction status',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when log entry was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment gateway transaction logs for audit and idempotency (Req 13.1, 13.2, 13.3)';

--
-- Đang đổ dữ liệu cho bảng `transaction_log`
--

INSERT INTO `transaction_log` (`id`, `thanh_toan_id`, `gateway_transaction_id`, `gateway_name`, `request_data`, `response_data`, `callback_data`, `status`, `created_at`) VALUES
(1, 1, NULL, 'VNPAY', '{\"don_hang_id\":1,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 05:43:03\"}', NULL, NULL, 'PENDING', '2026-04-10 10:28:03'),
(2, 2, NULL, 'VNPAY', '{\"don_hang_id\":2,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 05:44:49\"}', NULL, NULL, 'PENDING', '2026-04-10 10:29:49'),
(3, 3, NULL, 'VNPAY', '{\"don_hang_id\":3,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 05:47:30\"}', NULL, NULL, 'PENDING', '2026-04-10 10:32:30'),
(4, 4, NULL, 'VNPAY', '{\"don_hang_id\":4,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:00:43\"}', NULL, NULL, 'PENDING', '2026-04-10 13:45:43'),
(5, 5, NULL, 'VNPAY', '{\"don_hang_id\":5,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:03:59\"}', NULL, NULL, 'PENDING', '2026-04-10 13:48:59'),
(6, 6, NULL, 'VNPAY', '{\"don_hang_id\":6,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:08:49\"}', NULL, NULL, 'PENDING', '2026-04-10 13:53:49'),
(7, 7, NULL, 'VNPAY', '{\"don_hang_id\":7,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:12:37\"}', NULL, NULL, 'PENDING', '2026-04-10 13:57:37'),
(8, 8, NULL, 'VNPAY', '{\"don_hang_id\":8,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:13:21\"}', NULL, NULL, 'PENDING', '2026-04-10 13:58:21'),
(9, 9, NULL, 'VNPAY', '{\"don_hang_id\":9,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:16:14\"}', NULL, NULL, 'PENDING', '2026-04-10 14:01:14'),
(10, 10, NULL, 'VNPAY', '{\"don_hang_id\":10,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:19:07\"}', NULL, NULL, 'PENDING', '2026-04-10 14:04:07'),
(11, 11, NULL, 'VNPAY', '{\"don_hang_id\":11,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:24:35\"}', NULL, NULL, 'PENDING', '2026-04-10 14:09:35'),
(12, 12, NULL, 'VNPAY', '{\"don_hang_id\":12,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:27:05\"}', NULL, NULL, 'PENDING', '2026-04-10 14:12:05'),
(13, 13, NULL, 'VNPAY', '{\"don_hang_id\":13,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:30:30\"}', NULL, NULL, 'PENDING', '2026-04-10 14:15:30'),
(14, 14, NULL, 'VNPAY', '{\"don_hang_id\":14,\"amount\":35020000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-10 09:31:33\"}', NULL, NULL, 'PENDING', '2026-04-10 14:16:33'),
(15, 15, NULL, 'VNPAY', '{\"don_hang_id\":15,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 06:35:40\"}', NULL, NULL, 'PENDING', '2026-04-11 11:20:40'),
(16, 16, NULL, 'VNPAY', '{\"don_hang_id\":16,\"amount\":30069.98,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 16:19:13\"}', NULL, NULL, 'PENDING', '2026-04-11 21:04:13'),
(17, 17, NULL, 'VNPAY', '{\"don_hang_id\":17,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 16:25:22\"}', NULL, NULL, 'PENDING', '2026-04-11 21:10:22'),
(18, 18, NULL, 'VNPAY', '{\"don_hang_id\":18,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 16:30:54\"}', NULL, NULL, 'PENDING', '2026-04-11 21:15:54'),
(19, 19, NULL, 'VNPAY', '{\"don_hang_id\":19,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 16:34:59\"}', NULL, NULL, 'PENDING', '2026-04-11 21:19:59'),
(20, 20, NULL, 'VNPAY', '{\"don_hang_id\":20,\"amount\":30034.99,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-11 16:36:38\"}', NULL, NULL, 'PENDING', '2026-04-11 21:21:38'),
(29, 30, NULL, 'COD', '{\"don_hang_id\":31,\"amount\":17920000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-14 16:45:32\"}', NULL, NULL, 'PENDING', '2026-04-14 21:30:32'),
(30, 31, NULL, 'COD', '{\"don_hang_id\":32,\"amount\":35020000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-14 17:17:02\"}', NULL, NULL, 'PENDING', '2026-04-14 22:02:02'),
(31, 32, NULL, 'COD', '{\"don_hang_id\":33,\"amount\":35020000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-15 02:33:46\"}', NULL, NULL, 'PENDING', '2026-04-15 07:18:46'),
(32, 33, NULL, 'COD', '{\"don_hang_id\":34,\"amount\":20520000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-15 02:44:16\"}', NULL, NULL, 'PENDING', '2026-04-15 07:29:16'),
(33, 34, NULL, 'COD', '{\"don_hang_id\":35,\"amount\":20520000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-15 02:46:58\"}', NULL, NULL, 'PENDING', '2026-04-15 07:31:58'),
(34, 35, NULL, 'COD', '{\"don_hang_id\":36,\"amount\":20520000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-15 02:50:32\"}', NULL, NULL, 'PENDING', '2026-04-15 07:35:32'),
(35, 36, NULL, 'VNPAY', '{\"don_hang_id\":37,\"amount\":30017.495000000003,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-15 13:44:12\"}', NULL, NULL, 'PENDING', '2026-04-15 18:29:12'),
(36, 37, NULL, 'VNPAY', '{\"don_hang_id\":38,\"amount\":20320000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-15 13:46:36\"}', NULL, NULL, 'PENDING', '2026-04-15 18:31:36'),
(37, 38, NULL, 'VNPAY', '{\"don_hang_id\":39,\"amount\":20520000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-15 13:57:56\"}', NULL, NULL, 'PENDING', '2026-04-15 18:42:56'),
(38, 39, NULL, 'VNPAY', '{\"don_hang_id\":40,\"amount\":20520000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-15 17:39:46\"}', NULL, NULL, 'PENDING', '2026-04-15 22:24:46'),
(39, 40, NULL, 'COD', '{\"don_hang_id\":41,\"amount\":470000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-16 16:31:38\"}', NULL, NULL, 'PENDING', '2026-04-16 21:16:38'),
(40, 41, NULL, 'VNPAY', '{\"don_hang_id\":42,\"amount\":270000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-16 20:11:47\"}', NULL, NULL, 'PENDING', '2026-04-17 00:56:47'),
(41, 41, NULL, 'REFUND', '{\"action\":\"REFUND_INITIATED\",\"refund_id\":2,\"amount\":270000,\"reason\":\"test\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:05:55\"}', NULL, NULL, 'PENDING', '2026-04-17 01:05:55'),
(42, 41, NULL, 'REFUND', '{\"action\":\"REFUND_FAILED\",\"refund_id\":2,\"amount\":270000,\"reason\":\"test\",\"error\":\"Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê).\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:05:56\"}', NULL, NULL, 'PENDING', '2026-04-17 01:05:56'),
(43, 41, NULL, 'REFUND', '{\"action\":\"REFUND_INITIATED\",\"refund_id\":3,\"amount\":270000,\"reason\":\"test\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:07:59\"}', NULL, NULL, 'PENDING', '2026-04-17 01:07:59'),
(44, 41, NULL, 'REFUND', '{\"action\":\"REFUND_FAILED\",\"refund_id\":3,\"amount\":270000,\"reason\":\"test\",\"error\":\"Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê).\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:08:00\"}', NULL, NULL, 'PENDING', '2026-04-17 01:08:00'),
(45, 41, NULL, 'REFUND', '{\"action\":\"REFUND_INITIATED\",\"refund_id\":4,\"amount\":270000,\"reason\":\"test\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:09:24\"}', NULL, NULL, 'PENDING', '2026-04-17 01:09:24'),
(46, 41, NULL, 'REFUND', '{\"action\":\"REFUND_COMPLETED\",\"refund_id\":4,\"amount\":270000,\"reason\":\"test\",\"gateway_refund_id\":\"REFUND_41_1776362964\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:09:24\"}', NULL, NULL, 'PENDING', '2026-04-17 01:09:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_thich`
--

CREATE TABLE `yeu_thich` (
  `nguoi_dung_id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `ngay_them` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `banner_quang_cao`
--
ALTER TABLE `banner_quang_cao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vi_tri_trang_thai` (`vi_tri`,`trang_thai`);

--
-- Chỉ mục cho bảng `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_don_phienban` (`don_hang_id`,`phien_ban_id`) COMMENT 'Tránh trùng SP trong đơn',
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Chỉ mục cho bảng `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_gio_phienban` (`gio_hang_id`,`phien_ban_id`) COMMENT 'Tránh trùng SP trong giỏ',
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `san_pham_id` (`san_pham_id`),
  ADD KEY `idx_danh_gia_ngay_viet` (`ngay_viet`),
  ADD KEY `idx_danh_gia_so_sao_ngay_viet` (`so_sao`,`ngay_viet`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_dm_slug` (`slug`),
  ADD KEY `danh_muc_cha_id` (`danh_muc_cha_id`);

--
-- Chỉ mục cho bảng `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ma_don` (`ma_don_hang`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `dia_chi_id` (`dia_chi_id`),
  ADD KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`),
  ADD KEY `idx_ngay_tao` (`ngay_tao`),
  ADD KEY `idx_don_hang_trang_thai_ngay_tao` (`trang_thai`,`ngay_tao`),
  ADD KEY `idx_don_hang_ngay_cap_nhat` (`ngay_cap_nhat`),
  ADD KEY `idx_don_hang_revenue` (`trang_thai`,`ngay_tao`);

--
-- Chỉ mục cho bảng `gateway_health`
--
ALTER TABLE `gateway_health`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_gateway_name` (`gateway_name`),
  ADD KEY `idx_gateway_name` (`gateway_name`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Chỉ mục cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`),
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Chỉ mục cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Chỉ mục cho bảng `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ma_code` (`ma_code`),
  ADD KEY `idx_ma_giam_gia_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_email` (`email`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD UNIQUE KEY `idx_supabase_id` (`supabase_id`),
  ADD KEY `idx_forget_token` (`forget_token`);

--
-- Chỉ mục cho bảng `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sku` (`sku`),
  ADD KEY `san_pham_id` (`san_pham_id`),
  ADD KEY `idx_phien_ban_so_luong` (`so_luong_ton`),
  ADD KEY `idx_phien_ban_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `refund`
--
ALTER TABLE `refund`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thanh_toan_id` (`thanh_toan_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_refund_status_created_at` (`status`,`created_at`),
  ADD KEY `idx_refund_admin` (`admin_id`),
  ADD KEY `idx_refund_thanh_toan_status` (`thanh_toan_id`,`status`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sp_slug` (`slug`),
  ADD KEY `danh_muc_id` (`danh_muc_id`),
  ADD KEY `idx_hang_sx` (`hang_san_xuat`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `san_pham_khuyen_mai`
--
ALTER TABLE `san_pham_khuyen_mai`
  ADD PRIMARY KEY (`san_pham_id`,`khuyen_mai_id`),
  ADD KEY `khuyen_mai_id` (`khuyen_mai_id`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `don_hang_id` (`don_hang_id`),
  ADD KEY `nguoi_duyet_id` (`nguoi_duyet_id`),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`) COMMENT 'Index for idempotency checks and transaction lookups (Req 8.5)',
  ADD KEY `idx_expiration_time` (`expiration_time`) COMMENT 'Index for timeout checking queries (Req 6.1)',
  ADD KEY `idx_gateway_name` (`gateway_name`) COMMENT 'Index for filtering transactions by gateway',
  ADD KEY `idx_thanh_toan_trang_thai_duyet_ngay` (`trang_thai_duyet`,`ngay_thanh_toan`),
  ADD KEY `idx_thanh_toan_duyet` (`trang_thai_duyet`);

--
-- Chỉ mục cho bảng `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Chỉ mục cho bảng `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `danh_muc_id` (`danh_muc_id`);

--
-- Chỉ mục cho bảng `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thanh_toan_id` (`thanh_toan_id`),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`) COMMENT 'Index for idempotency checks (Req 8.1, 8.5)',
  ADD KEY `idx_gateway_name` (`gateway_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_transaction_log_status_created` (`status`,`created_at`);

--
-- Chỉ mục cho bảng `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD PRIMARY KEY (`nguoi_dung_id`,`san_pham_id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `banner_quang_cao`
--
ALTER TABLE `banner_quang_cao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `dia_chi`
--
ALTER TABLE `dia_chi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `gateway_health`
--
ALTER TABLE `gateway_health`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT cho bảng `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `refund`
--
ALTER TABLE `refund`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  ADD CONSTRAINT `chi_tiet_don_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  ADD CONSTRAINT `chi_tiet_gio_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD CONSTRAINT `danh_muc_ibfk_1` FOREIGN KEY (`danh_muc_cha_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD CONSTRAINT `dia_chi_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`dia_chi_id`) REFERENCES `dia_chi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_ibfk_3` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD CONSTRAINT `hinh_anh_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hinh_anh_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD CONSTRAINT `lich_su_tim_kiem_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  ADD CONSTRAINT `phien_ban_sp_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `fk_refund_admin` FOREIGN KEY (`admin_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `san_pham_khuyen_mai`
--
ALTER TABLE `san_pham_khuyen_mai`
  ADD CONSTRAINT `sp_km_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sp_km_ibfk_2` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  ADD CONSTRAINT `thong_so_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  ADD CONSTRAINT `ttdm_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD CONSTRAINT `yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

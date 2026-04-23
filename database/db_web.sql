-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 23, 2026 lúc 02:32 AM
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
-- Cơ sở dữ liệu: `db_web_fpt`
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
  `tieu_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên banner để admin dễ quản lý',
  `hinh_anh_desktop` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link ảnh cho màn hình máy tính',
  `hinh_anh_mobile` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link ảnh cho màn hình điện thoại',
  `link_dich` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL khi user click vào banner',
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
(5, 'Laptop giá sốc - Giảm đến 30%', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://www.thegioididong.com/laptop', 'HOME_HERO', 2, '2026-04-04 20:26:00', '2026-04-19 20:26:00', 0),
(6, 'Khuyến mãi siêu sale điện thoại', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://shopee.vn', 'HOME_HERO', 1, '2026-04-04 20:51:00', '2026-05-04 20:51:00', 0),
(7, 'Flash Sale phụ kiện - Giá từ 9K', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://tiki.vn/phu-kien-dien-thoai/c1789', 'HOME_HERO', 1, '2026-04-04 20:52:00', '2026-04-14 20:52:00', 0),
(17, 'test sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776000404/banners/banner_desktop_1776000399.webp', NULL, '/san-pham/iphone-16-pro-max-256gb', 'HOME_HERO', 0, '2026-04-12 20:26:00', '2026-04-24 20:26:00', 1),
(18, 'ok', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776001646/banners/banner_desktop_1776001638.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_HERO', 0, '2026-04-12 20:47:00', '2026-04-25 20:47:00', 1),
(19, 'Siêu sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776081915/banners/banner_desktop_1776081905.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:01:00', '2026-04-23 19:01:00', 1),
(20, 'Sale giữa tháng', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776081974/banners/banner_desktop_1776081965.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:05:00', '2026-04-30 19:06:00', 1),
(21, 'Sale chào lương về', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776082226/banners/banner_desktop_1776082199.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:09:00', '2026-04-30 19:09:00', 1),
(22, 'Săn sale hết cỡ', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776082270/banners/banner_desktop_1776082251.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-13 19:10:00', '2026-04-24 19:10:00', 1),
(23, 'Deal tới', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776132457/banners/banner_desktop_1776132436.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:07:00', '2026-04-30 09:07:00', 1),
(24, 'Săn ngay voucher', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776132644/banners/banner_desktop_1776132608.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:10:00', '2026-04-30 09:10:00', 1),
(25, 'Deal nửa giá', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776134749/banners/banner_desktop_1776134718.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_MID', 0, '2026-04-14 09:45:00', '2026-04-30 09:45:00', 1),
(28, 'Deal nửa giá - PopUP', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776216305/banners/banner_desktop_1776216278.png', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776216322/banners/banner_mobile_1776216278.png', '/san-pham/iphone-15', 'POPUP', 0, '2026-04-15 08:24:00', '2026-04-30 08:24:00', 1),
(29, 'Deal nửa giá - Top', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776420980/banners/banner_desktop_1776420971.png', NULL, '/san-pham/iphone-16-pro-max-256gb', 'HOME_HERO', 0, '2026-04-17 17:16:00', '2026-04-30 17:16:00', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(42, 42, 19, 1, 440000.00),
(43, 43, 19, 1, 440000.00),
(44, 44, 19, 1, 440000.00),
(45, 45, 19, 1, 440000.00),
(46, 46, 18, 1, 8990000.00),
(47, 47, 13, 1, 9999999.00),
(48, 48, 17, 1, 4190000.00),
(49, 49, 17, 1, 4190000.00),
(50, 50, 17, 1, 4190000.00),
(51, 51, 18, 1, 8990000.00),
(52, 52, 19, 1, 440000.00),
(53, 53, 19, 1, 440000.00),
(54, 54, 19, 1, 440000.00),
(55, 55, 19, 1, 440000.00),
(56, 56, 19, 1, 440000.00),
(57, 57, 17, 1, 4190000.00),
(58, 58, 19, 1, 440000.00),
(59, 60, 14, 1, 17890000.00),
(60, 61, 11, 1, 34.99),
(61, 62, 11, 1, 34.99),
(68, 69, 459, 1, 3090000.00),
(69, 70, 11, 1, 17890000.00),
(70, 71, 459, 1, 3090000.00),
(71, 72, 459, 1, 3090000.00),
(72, 73, 459, 1, 3090000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_gio`
--

CREATE TABLE `chi_tiet_gio` (
  `id` int NOT NULL,
  `gio_hang_id` int NOT NULL,
  `phien_ban_id` int NOT NULL,
  `so_luong` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_gio`
--

INSERT INTO `chi_tiet_gio` (`id`, `gio_hang_id`, `phien_ban_id`, `so_luong`) VALUES
(56, 33, 19, 10),
(65, 8, 150, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int NOT NULL,
  `nguoi_dung_id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `so_sao` int DEFAULT NULL,
  `noi_dung` text COLLATE utf8mb4_unicode_ci,
  `ngay_viet` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `ten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL thân thiện: dien-thoai, laptop',
  `icon_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon hiển thị trên menu',
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
  `ten_nguoi_nhan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt_nhan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_nha_duong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phuong_xa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quan_huyen` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tinh_thanh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_dinh` tinyint(1) DEFAULT '0' COMMENT '1 = địa chỉ mặc định'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dia_chi`
--

INSERT INTO `dia_chi` (`id`, `nguoi_dung_id`, `ten_nguoi_nhan`, `sdt_nhan`, `so_nha_duong`, `phuong_xa`, `quan_huyen`, `tinh_thanh`, `mac_dinh`) VALUES
(2, 3, 'Trương Thành Đạt', '0399746618', 'Lê Duẩn', 'Phường Tân Định', 'Quận 1', 'Thành phố Hồ Chí Minh', 1),
(4, 3, 'Trương Thành Đạt', '0399746618', '49 Hồ Thị Kỷ', 'Phường 1', 'Quận 3', 'Thành phố Hồ Chí Minh', 0),
(7, 167, 'Nguyễn Tấn Khiêm', '0399746618', '49 Hồ Thị Kỷ', 'Phường 1', 'Quận 3', 'Thành phố Hồ Chí Minh', 1),
(8, 162, 'Trương Thành Đạt', '0399746618', '49 Hồ Thị Kỷ', 'Phường 1', 'Quận 3', 'Thành phố Hồ Chí Minh', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id` int NOT NULL,
  `ma_don_hang` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hiển thị: DH20260224001',
  `nguoi_dung_id` int DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `dia_chi_id` int DEFAULT NULL COMMENT 'NULL nếu guest (dùng thong_tin_guest)',
  `ma_giam_gia_id` int DEFAULT NULL COMMENT 'Voucher áp dụng',
  `trang_thai` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `tong_tien` decimal(15,2) DEFAULT NULL COMMENT 'Tổng tiền sản phẩm',
  `phi_van_chuyen` decimal(15,2) DEFAULT '0.00',
  `tien_giam_gia` decimal(15,2) DEFAULT '0.00' COMMENT 'Số tiền được giảm',
  `tong_thanh_toan` decimal(15,2) DEFAULT NULL COMMENT 'tong_tien + phi_van_chuyen - tien_giam_gia',
  `thong_tin_guest` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON: {ten, sdt, dia_chi} cho khách vãng lai',
  `ghi_chu` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của khách hàng',
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
(42, 'DH20260416195647', 3, 2, 1, 'HOAN_THANH', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 00:56:47', '2026-04-17 01:00:22'),
(43, 'DH20260416205309', 3, 2, 1, 'DA_HUY', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 01:53:09', '2026-04-17 01:54:01'),
(44, 'DH20260416205424', 3, 4, 1, 'DA_HUY', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 01:54:24', '2026-04-17 01:56:20'),
(45, 'DH20260416210038', 3, 4, 1, 'DANG_GIAO', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 02:00:38', '2026-04-17 02:08:19'),
(46, 'DH20260416212557', 3, 2, 1, 'CHO_DUYET', 8990000.00, 30000.00, 200000.00, 8820000.00, NULL, '', NULL, '2026-04-17 02:25:57', '2026-04-17 02:25:57'),
(47, 'DH20260416212751', 3, 4, 1, 'CHO_DUYET', 9999999.00, 30000.00, 200000.00, 9829999.00, NULL, '', NULL, '2026-04-17 02:27:51', '2026-04-17 02:27:51'),
(48, 'DH20260416212921', 3, 4, 1, 'CHO_DUYET', 4190000.00, 30000.00, 200000.00, 4020000.00, NULL, '', NULL, '2026-04-17 02:29:21', '2026-04-17 02:29:21'),
(49, 'DH20260416213326', 3, 4, 1, 'CHO_DUYET', 4190000.00, 30000.00, 200000.00, 4020000.00, NULL, '', NULL, '2026-04-17 02:33:26', '2026-04-17 02:33:26'),
(50, 'DH20260416213427', 3, 4, NULL, 'CHO_DUYET', 4190000.00, 30000.00, 0.00, 4220000.00, NULL, '', NULL, '2026-04-17 02:34:27', '2026-04-17 02:34:27'),
(51, 'DH20260416213554', 3, 4, NULL, 'CHO_DUYET', 8990000.00, 30000.00, 0.00, 9020000.00, NULL, '', NULL, '2026-04-17 02:35:54', '2026-04-17 02:35:54'),
(52, 'DH20260416213654', 3, 4, NULL, 'CHO_DUYET', 440000.00, 30000.00, 0.00, 470000.00, NULL, '', NULL, '2026-04-17 02:36:54', '2026-04-17 02:36:54'),
(53, 'DH20260416213951', 3, 4, 1, 'CHO_DUYET', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 02:39:51', '2026-04-17 02:39:51'),
(54, 'DH20260416214300', 3, 4, 1, 'CHO_DUYET', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 02:43:00', '2026-04-17 02:43:00'),
(55, 'DH20260416214548', 3, 4, 1, 'CHO_DUYET', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 02:45:48', '2026-04-17 02:45:48'),
(56, 'DH20260416215151', 3, 4, 1, 'DA_XAC_NHAN', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 02:51:51', '2026-04-17 02:52:06'),
(57, 'DH20260416215457', 3, 4, 1, 'DA_XAC_NHAN', 4190000.00, 30000.00, 200000.00, 4020000.00, NULL, '', NULL, '2026-04-17 02:54:57', '2026-04-17 02:55:06'),
(58, 'DH20260416221135', 3, 4, 1, 'DA_XAC_NHAN', 440000.00, 30000.00, 200000.00, 270000.00, NULL, '', NULL, '2026-04-17 03:11:35', '2026-04-17 03:12:37'),
(59, 'DH20260417053409', 3, 4, 1, 'CHO_DUYET', 17890000.00, 30000.00, 200000.00, 17720000.00, NULL, '', NULL, '2026-04-17 10:34:09', '2026-04-17 10:34:09'),
(60, 'DH20260417053703', 3, 4, 1, 'CHO_DUYET', 17890000.00, 30000.00, 200000.00, 17720000.00, NULL, '', NULL, '2026-04-17 10:37:03', '2026-04-17 10:37:03'),
(61, 'DH20260417053909', 3, 4, 1, 'DA_XAC_NHAN', 34.99, 30000.00, 17.50, 30017.50, NULL, '', NULL, '2026-04-17 10:39:09', '2026-04-17 10:39:49'),
(62, 'DH20260417054242', 3, 4, 1, 'DA_XAC_NHAN', 34.99, 30000.00, 17.50, 30017.50, NULL, '', NULL, '2026-04-17 10:42:42', '2026-04-17 10:43:29'),
(63, 'DH20260417054405', 3, 4, 1, 'HOAN_THANH', 34.99, 30000.00, 17.50, 30017.50, NULL, '', NULL, '2026-04-17 10:44:05', '2026-04-17 14:46:02'),
(64, 'DH20260417070418', 3, 4, 1, 'CHO_DUYET', 20490000.00, 30000.00, 200000.00, 20320000.00, NULL, '', NULL, '2026-04-17 12:04:18', '2026-04-17 12:04:18'),
(69, 'DH20260417150534', 167, 7, 1, 'CHO_DUYET', 3090000.00, 30000.00, 200000.00, 2920000.00, NULL, '', NULL, '2026-04-17 20:05:34', '2026-04-17 20:05:34'),
(70, 'DH20260419020647', 3, 4, 1, 'HOAN_THANH', 17890000.00, 30000.00, 200000.00, 17720000.00, NULL, '', NULL, '2026-04-19 07:06:47', '2026-04-19 07:11:52'),
(71, 'DH20260419020935', 3, 2, 1, 'HOAN_THANH', 3090000.00, 30000.00, 200000.00, 2920000.00, NULL, '', NULL, '2026-04-19 07:09:35', '2026-04-19 07:11:37'),
(72, 'DH20260419054622', 3, 2, 1, 'HOAN_THANH', 3090000.00, 30000.00, 200000.00, 2920000.00, NULL, '', NULL, '2026-04-19 10:46:22', '2026-04-19 10:50:07'),
(73, 'DH20260419054748', 3, 2, 1, 'HOAN_THANH', 3090000.00, 30000.00, 200000.00, 2920000.00, NULL, '', NULL, '2026-04-19 10:47:48', '2026-04-19 10:49:52');

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
(1, 'VNPay', 31, 0, '2026-04-19 10:46:30', NULL, '2026-04-19 10:46:30'),
(28, 'VietQR', 19, 0, '2026-04-17 10:44:07', NULL, '2026-04-17 10:44:07'),
(44, 'PayPal', 9, 7, '2026-04-19 10:47:58', '2026-04-17 02:39:53', '2026-04-19 10:47:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id` int NOT NULL,
  `nguoi_dung_id` int DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Session cho khách vãng lai',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id`, `nguoi_dung_id`, `session_id`, `ngay_tao`, `ngay_cap_nhat`) VALUES
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
(26, NULL, 'o8b3sa9sqcggmukk8r380nkd6d', '2026-04-16 10:57:43', '2026-04-16 10:57:43'),
(27, NULL, 'r137e70tder7cei10vsb52puhf', '2026-04-16 19:19:07', '2026-04-16 19:19:07'),
(28, NULL, 'dujbm9f9gsdvksra2u9388uvtg', '2026-04-17 04:09:59', '2026-04-17 04:09:59'),
(29, NULL, '2p4q20mmavmf4fl07qrsukhp9k', '2026-04-17 04:11:35', '2026-04-17 04:11:35'),
(30, NULL, '69iqbmi93l4vpe8nncpiot6gpr', '2026-04-17 10:32:36', '2026-04-17 10:32:36'),
(31, NULL, 'cgpuo2he0s0rtjgrcfsspcvvof', '2026-04-17 12:00:55', '2026-04-17 12:00:55'),
(32, NULL, 'c0tq04oplj8t1ho542famr5t1i', '2026-04-17 14:37:44', '2026-04-17 14:37:44'),
(33, NULL, '8gg43ljlf5am3e57gpkfmqaa7a', '2026-04-17 14:40:54', '2026-04-17 14:40:54'),
(35, NULL, 'd6ggd9u4ssqjrg4ns1adeptrch', '2026-04-17 18:37:30', '2026-04-17 18:37:30'),
(46, 4, NULL, '2026-04-17 19:13:27', '2026-04-17 19:13:27'),
(52, 167, NULL, '2026-04-17 20:04:19', '2026-04-17 20:04:19'),
(53, NULL, 'bnl6a7uc8c194hqfmhldlpp9sn', '2026-04-19 07:02:35', '2026-04-19 07:02:35'),
(54, NULL, 'jgr5uvd0s41th4ihf4gpap8r5q', '2026-04-19 07:03:56', '2026-04-19 07:03:56'),
(55, 3, NULL, '2026-04-19 07:05:53', '2026-04-19 07:05:53'),
(56, NULL, 'pokqmakqq4brq3spr7iv1c9527', '2026-04-23 07:26:50', '2026-04-23 07:26:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_san_pham`
--

CREATE TABLE `hinh_anh_san_pham` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `phien_ban_id` int DEFAULT NULL COMMENT 'NULL = ảnh chung, có giá trị = ảnh theo phiên bản/màu',
  `url_anh` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả ảnh cho SEO/accessibility',
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
(31, 13, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776310137/products/product_13_1776310136_553.webp', '', 0, 0),
(32, 29, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414593/products/product_29_1776414588_440.webp', '', 1, 0),
(33, 30, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414662/products/product_30_1776414660_967.webp', '', 0, 0),
(35, 32, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414755/products/product_32_1776414753_972.webp', '', 1, 0),
(36, 33, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414788/products/product_33_1776414786_884.webp', '', 1, 0),
(37, 34, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414843/products/product_34_1776414840_846.jpg', '', 0, 0),
(38, 35, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414879/products/product_35_1776414877_185.webp', '', 1, 0),
(39, 36, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414917/products/product_36_1776414915_465.webp', '', 1, 0),
(40, 37, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414957/products/product_37_1776414955_952.webp', '', 1, 0),
(41, 38, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776414991/products/product_38_1776414989_869.webp', '', 1, 0),
(42, 39, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415033/products/product_39_1776415031_563.webp', '', 1, 0),
(43, 40, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415126/products/product_40_1776415124_680.webp', '', 1, 0),
(44, 41, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415158/products/product_41_1776415156_233.webp', '', 1, 0),
(45, 42, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415205/products/product_42_1776415203_590.webp', '', 1, 0),
(46, 43, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415239/products/product_43_1776415237_816.webp', '', 1, 0),
(47, 14, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415353/products/product_14_1776415351_681.webp', '', 1, 0),
(48, 15, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415393/products/product_15_1776415391_126.webp', '', 1, 0),
(49, 16, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415427/products/product_16_1776415425_557.webp', '', 1, 0),
(50, 17, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415491/products/product_17_1776415489_228.webp', '', 1, 0),
(51, 18, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415522/products/product_18_1776415520_794.webp', '', 1, 0),
(52, 19, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415559/products/product_19_1776415557_480.webp', '', 1, 0),
(53, 20, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415590/products/product_20_1776415588_132.webp', '', 1, 0),
(54, 21, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415638/products/product_21_1776415636_740.webp', '', 1, 0),
(55, 22, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415678/products/product_22_1776415675_744.webp', '', 1, 0),
(56, 25, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415718/products/product_25_1776415716_286.webp', '', 1, 0),
(57, 26, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776415751/products/product_26_1776415748_203.webp', '', 1, 0),
(59, 31, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776416069/products/product_31_1776416066_131.webp', '', 1, 0),
(60, 49, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419442/products/product_49_1776419440_993.webp', '', 1, 0),
(61, 50, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419482/products/product_50_1776419480_319.webp', '', 1, 0),
(62, 47, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419522/products/product_47_1776419520_464.webp', '', 1, 0),
(63, 48, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419554/products/product_48_1776419552_429.webp', '', 1, 0),
(64, 51, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419607/products/product_51_1776419605_630.webp', '', 1, 0),
(65, 52, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419636/products/product_52_1776419635_277.webp', '', 1, 0),
(66, 53, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419667/products/product_53_1776419665_359.webp', '', 1, 0),
(67, 54, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419698/products/product_54_1776419696_705.webp', '', 1, 0),
(68, 55, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419751/products/product_55_1776419749_585.webp', '', 1, 0),
(69, 56, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419781/products/product_56_1776419779_606.webp', '', 1, 0),
(70, 23, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419816/products/product_23_1776419814_459.webp', '', 1, 0),
(71, 24, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776419854/products/product_24_1776419852_696.webp', '', 1, 0),
(77, 89, 459, 'https://res.cloudinary.com/dmahghpku/image/upload/v1776430977/products/product_89_1776430973_287.webp', '', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id` int NOT NULL,
  `ten_chuong_trinh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') COLLATE utf8mb4_unicode_ci DEFAULT 'PHAN_TRAM',
  `gia_tri_giam` decimal(15,2) DEFAULT NULL COMMENT '10 = 10% hoặc 500000 = 500k VND',
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Giảm tối đa (áp dụng nếu loại %)',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','TAM_DUNG') COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG'
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
  `tu_khoa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `ma_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: FPTSHOP50K, SALE10',
  `mo_ta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Áp dụng nếu loại PHAN_TRAM',
  `don_toi_thieu` decimal(15,2) DEFAULT '0.00' COMMENT 'Giá trị đơn hàng tối thiểu',
  `so_luot_da_dung` int DEFAULT '0',
  `gioi_han_su_dung` int DEFAULT NULL COMMENT 'NULL = không giới hạn',
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','HET_LUOT') COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`id`, `ma_code`, `mo_ta`, `loai_giam`, `gia_tri_giam`, `giam_toi_da`, `don_toi_thieu`, `so_luot_da_dung`, `gioi_han_su_dung`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(1, 'SUMMER2026', 'Mã giảm giá mùa hè 2026', 'PHAN_TRAM', 50.00, 200000.00, 0.00, 30, NULL, '2026-04-14 21:27:00', '2026-04-30 21:28:00', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int NOT NULL,
  `supabase_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã định danh duy nhất từ Supabase',
  `auth_provider` enum('LOCAL','GOOGLE','FACEBOOK') COLLATE utf8mb4_unicode_ci DEFAULT 'LOCAL' COMMENT 'Nguồn tạo tài khoản',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cho phép NULL nếu đăng nhập bằng nền tảng khác',
  `ho_ten` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện',
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('NAM','NU','KHAC') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_tai_khoan` enum('ADMIN','MEMBER') COLLATE utf8mb4_unicode_ci DEFAULT 'MEMBER',
  `trang_thai` enum('ACTIVE','BLOCKED','UNVERIFIED') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `verification_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `forget_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token đặt lại mật khẩu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `supabase_id`, `auth_provider`, `email`, `mat_khau`, `ho_ten`, `sdt`, `avatar_url`, `ngay_sinh`, `gioi_tinh`, `loai_tai_khoan`, `trang_thai`, `verification_token`, `ngay_tao`, `ngay_cap_nhat`, `forget_token`) VALUES
(1, NULL, 'LOCAL', 'test_1773155576@example.com', '$2y$10$IcOj9mDvjFD1jdTaRVVY0eoywjosOpf80oNvDP3KWZqxl6TMUDTW6', 'Nguyễn Văn Test', '0901234567', NULL, NULL, 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(2, NULL, 'LOCAL', 'admin_1773155576@example.com', '$2y$10$dyvFZGKucag4pZ.RXkSQN.XTO.0tgpfouhBOIg7PyKocR2N7.uCqO', 'Admin Test', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(3, '94b3ad2b-35ec-4548-8ef4-a50f7d6241bc', 'GOOGLE', 'dat82770@gmail.com', 'cbd5140549732304f6590c5d13afb4fabd68c357', 'Trương Thành Đạt', '0399746612', 'https://lh3.googleusercontent.com/a/ACg8ocK8HeIX9iTinGa-DKPsCjmRr_v5ZwKYOdRefn83Pi_o2t0QbA=s96-c', '2006-10-15', 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-28 17:19:23', '2026-04-19 02:05:48', NULL),
(4, NULL, 'LOCAL', 'admin@fptshop.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-29 19:08:00', '2026-04-06 20:48:32', NULL),
(6, NULL, 'LOCAL', 'dattruong.31241024873@st.ueh.edu.vn', 'e42c0141250d02dad20c86609d5d19d155f12717', 'ok', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-30 08:59:17', '2026-03-30 09:07:36', NULL),
(7, NULL, 'LOCAL', 'test_reset_1774858467@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '771016c4c17b6d983a360510b62d0747388599bc0281821c4675e4b38436dc6b', '2026-03-30 10:14:27', '2026-03-30 10:14:27', NULL),
(8, NULL, 'LOCAL', 'test_reset_1774858550@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '8b88305a96792e1eccbd100868d5e316abd18d501b3d3a4cbaf0ca6b9c3b7029', '2026-03-30 10:15:50', '2026-03-30 15:15:50', '397e96fac4d873ea58648f341799fda30935651e3fae792a694d2755b84aaa4e'),
(42, NULL, 'LOCAL', 'test_reset_1774859235@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '92efe7c252315d4fcd464e9a591c80628cc15b3f296d9b503cb1cc67e1cd3edb', '2026-03-30 10:27:15', '2026-03-30 15:27:15', 'cff85a50b371038fb5bc4f50064d4da55b655fbb83955c03c600317972b181b3'),
(161, NULL, 'LOCAL', 'hsntk1610@gmail.com', '0bf7e28d9ad8eb2c7afa624bcbc7afe8eeadbae0', 'nguyentankhiem', NULL, '/public/uploads/avatars/avatar_161_1774929615.jpg', NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-31 05:58:29', '2026-03-31 06:00:15', NULL),
(162, 'ea78e311-5348-49d5-b308-9f804f279caf', 'GOOGLE', 'dat158623@gmail.com', NULL, 'Đạt Trương', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocI6h_MaEuzfcRIyqnN2FGUDrsUfwyPpEN_QJFd7FcbHEpg6YA=s96-c', NULL, NULL, 'MEMBER', 'ACTIVE', NULL, '2026-04-07 14:38:45', '2026-04-17 15:31:09', NULL),
(167, NULL, 'LOCAL', 'datweb07@gmail.com', '0bf7e28d9ad8eb2c7afa624bcbc7afe8eeadbae0', 'Nguyễn Tấn Khiêm', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-04-17 15:03:55', '2026-04-17 15:04:15', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phien_ban_san_pham`
--

CREATE TABLE `phien_ban_san_pham` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã kho duy nhất',
  `ten_phien_ban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'iPhone 16 Pro Max 256GB',
  `mau_sac` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đen Titan, Trắng, Xanh...',
  `thuoc_tinh_bien_the` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Lưu chuỗi JSON: {"RAM": "8GB", "Dung lượng": "256GB"} hoặc {"Công suất": "1 HP"}',
  `cau_hinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả cấu hình khác (nếu có)',
  `gia_ban` decimal(15,2) DEFAULT NULL COMMENT 'Giá bán hiện tại',
  `gia_goc` decimal(15,2) DEFAULT NULL COMMENT 'Giá gốc (giá gạch ngang)',
  `so_luong_ton` int DEFAULT '0',
  `trang_thai` enum('CON_HANG','HET_HANG','NGUNG_BAN') COLLATE utf8mb4_unicode_ci DEFAULT 'CON_HANG'
) ;

--
-- Đang đổ dữ liệu cho bảng `phien_ban_san_pham`
--

INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `thuoc_tinh_bien_the`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(10, 7, 'IP15-256-TITAN', 'iPhone 15 256GB Titan Tự Nhiên', 'Titan Tự Nhiên', '{\"Bộ nhớ\": \"256GB\"}', NULL, 34990000.00, 36990000.00, 40, 'CON_HANG'),
(11, 7, 'IP15-128-TIM', 'iPhone 15 128GB Tím', 'Tím', '{\"Bộ nhớ\": \"128GB\"}', NULL, 17890000.00, 19590000.00, 39, 'CON_HANG'),
(12, 2, 'samsung-galaxy-s26-12gb-256gb', 'Samsung Galaxy S26 5G 12GB 256GB', 'Đen', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 20490000.00, 25990000.00, 92, 'CON_HANG'),
(13, 10, 'CI1-TRANG', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP Trắng', 'Trắng', '{\"Công suất\": \"1 HP - 9.350 BTU\"}', NULL, 9999999.00, 12500000.00, 99, 'CON_HANG'),
(14, 7, 'iphone-15-128gb', 'iPhone 15 128GB', 'Hồng', '{\"Bộ nhớ\": \"128GB\"}', NULL, 17890000.00, 19590000.00, 98, 'CON_HANG'),
(15, 7, 'iphone-15-256gb', 'iPhone 15 256GB', 'Xanh dương', '{\"Bộ nhớ\": \"256GB\"}', NULL, 20890000.00, 22490000.00, 100, 'CON_HANG'),
(16, 7, 'iphone-15-128gb-xanh-duong', 'iPhone 15 128GB', 'Xanh dương', '{\"Bộ nhớ\": \"128GB\"}', NULL, 17890000.00, 19590000.00, 100, 'CON_HANG'),
(17, 12, '00920820', 'Xiaomi Google TV QLED 32 inch HD A Pro 2026 L32MB-APSEA', 'Đen', '{\"Màn hình\": \"32 inch\"}', NULL, 4190000.00, 4490000.00, 96, 'CON_HANG'),
(18, 11, '00907960', 'Máy lọc nước nóng lạnh RO Hydrogen Kangaroo KG11A6 11 lõi', 'Đen', '{\"Số lõi\": \"11\", \"Công nghệ\": \"RO Hydrogen\"}', NULL, 8990000.00, 11590000.00, 98, 'CON_HANG'),
(19, 13, '00922445', 'Củ sạc nhanh 1 cổng 25W USB-C PPS Wall Charger Belkin', 'Trắng', '{\"Công suất\": \"25W\", \"Cổng\": \"1x USB-C\"}', NULL, 440000.00, 490000.00, 89, 'CON_HANG'),
(147, 1, 'SP1-V2', 'iPhone 256GB', 'Xanh', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 1199.00, 1299.00, 120, 'NGUNG_BAN'),
(148, 1, 'SP1-V3', 'iPhone 512GB', 'Vàng', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 1399.00, 1499.00, 80, 'NGUNG_BAN'),
(149, 2, 'SP2-V2', 'Samsung Galaxy A55 256GB', 'Đen', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 7490000.00, 8490000.00, 180, 'CON_HANG'),
(150, 2, 'SP2-V3', 'Samsung Galaxy A55 128GB', 'Tím', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"128GB\"}', NULL, 6790000.00, 7790000.00, 220, 'CON_HANG'),
(151, 7, 'SP7-V4', 'iPhone 15 256GB Tím', 'Tím', '{\"Bộ nhớ\": \"256GB\"}', NULL, 36990000.00, 38990000.00, 90, 'CON_HANG'),
(152, 7, 'SP7-V5', 'iPhone 15 512GB Đen', 'Đen', '{\"Bộ nhớ\": \"512GB\"}', NULL, 39990000.00, 41990000.00, 60, 'CON_HANG'),
(153, 8, 'SP8-V2', 'ThinkPad X1 Carbon 16GB/512GB', 'Bạc', '{\"CPU\": \"Intel Core Ultra 5\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', 'Màn hình IPS 2.2K', 35990000.00, 39990000.00, 30, 'CON_HANG'),
(154, 8, 'SP8-V3', 'ThinkPad X1 Carbon 32GB/1TB', 'Xám', '{\"CPU\": \"Intel Core Ultra 7\", \"RAM\": \"32GB\", \"SSD\": \"1TB\"}', 'Màn hình OLED 2.8K', 49990000.00, 54990000.00, 20, 'CON_HANG'),
(155, 9, 'SP9-V2', 'Ốp lưng Magsafe Sunset', 'Cam', NULL, 'Tương thích S26 Ultra', 450000.00, 590000.00, 280, 'CON_HANG'),
(156, 9, 'SP9-V3', 'Ốp lưng Magsafe Forest', 'Xanh rêu', NULL, 'Tương thích S26 Ultra', 450000.00, 590000.00, 260, 'CON_HANG'),
(157, 9, 'SP9-V4', 'Ốp lưng Magsafe Midnight', 'Đen bóng', NULL, 'Tương thích S26 Ultra', 490000.00, 620000.00, 310, 'CON_HANG'),
(158, 10, 'SP10-V3', 'Comfee Inverter 2 HP', 'Trắng', '{\"Công suất\": \"2 HP - 18.000 BTU\"}', NULL, 14990000.00, 16500000.00, 55, 'CON_HANG'),
(159, 10, 'SP10-V4', 'Comfee Inverter 1 HP', 'Bạc', '{\"Công suất\": \"1 HP - 9.000 BTU\"}', NULL, 10990000.00, 12500000.00, 70, 'CON_HANG'),
(160, 11, 'SP11-V3', 'Kangaroo RO 7 lõi', 'Trắng', '{\"Số lõi\": \"7\", \"Công nghệ\": \"RO\"}', NULL, 6990000.00, 8500000.00, 80, 'CON_HANG'),
(161, 11, 'SP11-V4', 'Kangaroo Hydrogen 5 lõi', 'Xám', '{\"Số lõi\": \"5\", \"Công nghệ\": \"Hydrogen\"}', NULL, 5990000.00, 7200000.00, 100, 'CON_HANG'),
(162, 12, 'SP12-V3', 'Xiaomi TV A Pro 50 inch', 'Đen', '{\"Kích thước\": \"50 inch\", \"Độ phân giải\": \"4K\"}', NULL, 7490000.00, 8490000.00, 80, 'CON_HANG'),
(163, 12, 'SP12-V4', 'Xiaomi TV A Pro 32 inch', 'Đen', '{\"Kích thước\": \"32 inch\", \"Độ phân giải\": \"HD\"}', NULL, 3990000.00, 4990000.00, 120, 'CON_HANG'),
(164, 13, 'SP13-V3', 'Belkin 30W GaN', 'Trắng', '{\"Công suất\": \"30W\", \"Công nghệ\": \"GaN\"}', NULL, 490000.00, 650000.00, 350, 'CON_HANG'),
(165, 13, 'SP13-V4', 'Belkin 65W GaN 2 cổng', 'Đen', '{\"Công suất\": \"65W\", \"Cổng\": \"2x USB-C\"}', NULL, 890000.00, 1090000.00, 200, 'CON_HANG'),
(166, 14, 'SP14-V3', 'iPhone 16 Pro Max 512GB', 'Trắng', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 39990000.00, 42990000.00, 130, 'CON_HANG'),
(167, 14, 'SP14-V4', 'iPhone 16 Pro Max 1TB', 'Đen', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"1TB\"}', NULL, 44990000.00, 47990000.00, 80, 'CON_HANG'),
(168, 15, 'SP15-V2', 'Galaxy S25 Ultra 512GB', 'Xám', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 32990000.00, 35990000.00, 140, 'CON_HANG'),
(169, 15, 'SP15-V3', 'Galaxy S25 Ultra 1TB', 'Xanh', '{\"RAM\": \"16GB\", \"Bộ nhớ\": \"1TB\"}', NULL, 37990000.00, 40990000.00, 90, 'CON_HANG'),
(170, 16, 'SP16-V2', 'Xiaomi 15 Pro 512GB', 'Xanh', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 20990000.00, 22990000.00, 180, 'CON_HANG'),
(171, 16, 'SP16-V3', 'Xiaomi 15 Pro 1TB', 'Vàng', '{\"RAM\": \"16GB\", \"Bộ nhớ\": \"1TB\"}', NULL, 23990000.00, 25990000.00, 100, 'CON_HANG'),
(172, 17, 'SP17-V2', 'iPad Pro 13 inch 512GB', 'Bạc', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 32990000.00, 34990000.00, 70, 'CON_HANG'),
(173, 17, 'SP17-V3', 'iPad Pro 13 inch 1TB', 'Xám', '{\"RAM\": \"16GB\", \"Bộ nhớ\": \"1TB\"}', NULL, 39990000.00, 42990000.00, 40, 'CON_HANG'),
(174, 18, 'SP18-V2', 'Galaxy Tab S10 Ultra 256GB', 'Bạc', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 21990000.00, 24990000.00, 80, 'CON_HANG'),
(175, 18, 'SP18-V3', 'Galaxy Tab S10 Ultra 1TB', 'Xanh', '{\"RAM\": \"16GB\", \"Bộ nhớ\": \"1TB\"}', NULL, 29990000.00, 32990000.00, 50, 'CON_HANG'),
(176, 19, 'SP19-V2', 'MacBook Pro M3 Pro 36GB/1TB', 'Xám', '{\"CPU\": \"M3 Pro 12 nhân\", \"RAM\": \"36GB\", \"SSD\": \"1TB\"}', NULL, 47990000.00, 52990000.00, 40, 'CON_HANG'),
(177, 19, 'SP19-V3', 'MacBook Pro M3 Max 48GB/1TB', 'Bạc', '{\"CPU\": \"M3 Max 16 nhân\", \"RAM\": \"48GB\", \"SSD\": \"1TB\"}', NULL, 69990000.00, 74990000.00, 25, 'CON_HANG'),
(178, 20, 'SP20-V2', 'Dell XPS 15 i7/16GB/512GB', 'Bạc', '{\"CPU\": \"i7-13700H\", \"RAM\": \"16GB\", \"SSD\": \"512GB\", \"GPU\": \"RTX 4050\"}', NULL, 39990000.00, 43990000.00, 35, 'CON_HANG'),
(179, 20, 'SP20-V3', 'Dell XPS 15 i9/64GB/2TB', 'Đen', '{\"CPU\": \"i9-13900H\", \"RAM\": \"64GB\", \"SSD\": \"2TB\", \"GPU\": \"RTX 4070\"}', NULL, 59990000.00, 64990000.00, 15, 'CON_HANG'),
(180, 21, 'SP21-V2', 'LG 27 inch 5K', 'Đen', '{\"Độ phân giải\": \"5120x2880\", \"Tần số\": \"60Hz\"}', NULL, 22990000.00, 24990000.00, 30, 'CON_HANG'),
(181, 21, 'SP21-V3', 'LG 24 inch 4K', 'Trắng', '{\"Độ phân giải\": \"3840x2160\", \"Kích thước\": \"24 inch\"}', NULL, 9990000.00, 11990000.00, 60, 'CON_HANG'),
(182, 22, 'SP22-V2', 'Odyssey G7 27 inch 240Hz', 'Đen', '{\"Độ phân giải\": \"2560x1440\", \"Kích thước\": \"27 inch\"}', NULL, 12990000.00, 14990000.00, 50, 'CON_HANG'),
(183, 22, 'SP22-V3', 'Odyssey G7 32 inch 165Hz', 'Trắng', '{\"Độ phân giải\": \"3840x2160\", \"Tần số\": \"165Hz\"}', NULL, 17990000.00, 19990000.00, 35, 'CON_HANG'),
(184, 23, 'SP23-V2', 'OptiPlex 7010 i5/8GB/256GB', 'Đen', '{\"CPU\": \"i5-13500\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 13990000.00, 15990000.00, 40, 'CON_HANG'),
(185, 23, 'SP23-V3', 'OptiPlex 7010 i9/32GB/1TB', 'Đen', '{\"CPU\": \"i9-13900\", \"RAM\": \"32GB\", \"SSD\": \"1TB\"}', NULL, 25990000.00, 28990000.00, 20, 'CON_HANG'),
(186, 24, 'SP24-V2', 'EliteDesk 800 G9 i7/16GB/512GB', 'Đen', '{\"CPU\": \"i7-13700\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', NULL, 23990000.00, 26990000.00, 30, 'CON_HANG'),
(187, 24, 'SP24-V3', 'EliteDesk 800 G9 i5/8GB/256GB', 'Đen', '{\"CPU\": \"i5-13500\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 18990000.00, 20990000.00, 35, 'CON_HANG'),
(188, 25, 'SP25-V2', 'MX Master 3S', 'Xám', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth/2.4GHz\"}', NULL, 1999000.00, 2299000.00, 280, 'CON_HANG'),
(189, 25, 'SP25-V3', 'MX Master 3S for Mac', 'Bạc', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth\"}', NULL, 1999000.00, 2299000.00, 150, 'CON_HANG'),
(190, 26, 'SP26-V2', 'BlackWidow V4 Pro Yellow', 'Đen', '{\"Switch\": \"Yellow\", \"Đèn\": \"RGB\"}', NULL, 3999000.00, 4599000.00, 100, 'CON_HANG'),
(191, 26, 'SP26-V3', 'BlackWidow V4 Pro Orange', 'Đen', '{\"Switch\": \"Orange\", \"Đèn\": \"RGB\"}', NULL, 3999000.00, 4599000.00, 90, 'CON_HANG'),
(192, 27, 'SP27-V2', 'Sim Data 5GB/ngày eSIM', 'eSIM', NULL, NULL, 99000.00, 150000.00, 600, 'CON_HANG'),
(193, 27, 'SP27-V3', 'Sim Data 2GB/ngày 30 ngày', 'Vật lý', NULL, NULL, 69000.00, 99000.00, 800, 'CON_HANG'),
(194, 28, 'SP28-V2', 'Sim trọn gói 6 tháng', 'Vật lý', NULL, NULL, 499000.00, 699000.00, 200, 'CON_HANG'),
(195, 28, 'SP28-V3', 'Sim trọn gói 12 tháng', 'Vật lý', NULL, NULL, 899000.00, 1199000.00, 150, 'CON_HANG'),
(196, 29, 'SP29-V2', 'Apple Watch S9 45mm', 'Hồng', '{\"GPS\": \"Có\", \"Viền\": \"Nhôm\"}', NULL, 10990000.00, 11990000.00, 90, 'CON_HANG'),
(197, 29, 'SP29-V3', 'Apple Watch S9 41mm', 'Đen', '{\"GPS\": \"Có\", \"Viền\": \"Nhôm\"}', NULL, 9990000.00, 10990000.00, 120, 'CON_HANG'),
(198, 30, 'SP30-V2', 'Galaxy Watch 7 40mm', 'Hồng', '{\"Pin\": \"300mAh\", \"Chống nước\": \"5ATM\"}', NULL, 5990000.00, 6990000.00, 140, 'CON_HANG'),
(199, 30, 'SP30-V3', 'Galaxy Watch 7 44mm LTE', 'Đen', '{\"Pin\": \"425mAh\", \"LTE\": \"Có\"}', NULL, 7990000.00, 8990000.00, 80, 'CON_HANG'),
(200, 31, 'SP31-V2', 'QLED Q80D 65 inch', 'Đen', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"65 inch\"}', NULL, 19990000.00, 22990000.00, 45, 'CON_HANG'),
(201, 31, 'SP31-V3', 'QLED Q80D 50 inch', 'Đen', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"50 inch\"}', NULL, 12990000.00, 14990000.00, 60, 'CON_HANG'),
(202, 32, 'SP32-V2', 'OLED C3 77 inch', 'Đen', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"77 inch\"}', NULL, 49990000.00, 54990000.00, 25, 'CON_HANG'),
(203, 32, 'SP32-V3', 'OLED C3 55 inch', 'Đen', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"55 inch\"}', NULL, 24990000.00, 27990000.00, 50, 'CON_HANG'),
(204, 33, 'SP33-V2', 'Daikin Inverter 1.0HP', 'Trắng', '{\"BTU\": \"9000\", \"Inverter\": \"Có\"}', NULL, 10990000.00, 12990000.00, 100, 'CON_HANG'),
(205, 33, 'SP33-V3', 'Daikin Inverter 2.0HP', 'Trắng', '{\"BTU\": \"18000\", \"Inverter\": \"Có\"}', NULL, 15990000.00, 17990000.00, 60, 'CON_HANG'),
(206, 34, 'SP34-V2', 'Panasonic Inverter 1.5HP', 'Trắng', '{\"BTU\": \"12000\", \"Inverter\": \"Có\"}', NULL, 14990000.00, 16990000.00, 80, 'CON_HANG'),
(207, 34, 'SP34-V3', 'Panasonic Inverter 2.5HP', 'Trắng', '{\"BTU\": \"22000\", \"Inverter\": \"Có\"}', NULL, 19990000.00, 22990000.00, 40, 'CON_HANG'),
(208, 35, 'SP35-V2', 'Xiaomi Robot S10', 'Trắng', '{\"Lực hút\": \"3000Pa\", \"Chức năng\": \"Hút\"}', NULL, 6990000.00, 8490000.00, 120, 'CON_HANG'),
(209, 35, 'SP35-V3', 'Xiaomi Robot X20+', 'Đen', '{\"Lực hút\": \"5000Pa\", \"Tự làm sạch\": \"Có\"}', NULL, 12990000.00, 14990000.00, 70, 'CON_HANG'),
(210, 36, 'SP36-V2', 'Deebot T10 Omni', 'Trắng', '{\"Lực hút\": \"4000Pa\", \"Lau nước nóng\": \"Không\"}', NULL, 14990000.00, 16990000.00, 55, 'CON_HANG'),
(211, 36, 'SP36-V3', 'Deebot X2 Omni', 'Đen', '{\"Lực hút\": \"6000Pa\", \"Hình dáng\": \"Vuông\"}', NULL, 22990000.00, 25990000.00, 30, 'CON_HANG'),
(212, 37, 'SP37-V2', 'Sunhouse Quạt điều hòa SHD7711', 'Xanh', '{\"Dung tích nước\": \"7L\", \"Công suất\": \"120W\"}', NULL, 2290000.00, 2790000.00, 90, 'CON_HANG'),
(213, 37, 'SP37-V3', 'Sunhouse Quạt điều hòa mini', 'Hồng', '{\"Dung tích nước\": \"3L\", \"Công suất\": \"60W\"}', NULL, 1290000.00, 1590000.00, 150, 'CON_HANG'),
(214, 38, 'SP38-V2', 'Kangaroo Quạt hơi nước KG829', 'Trắng', '{\"Dung tích\": \"6L\", \"Hẹn giờ\": \"8h\"}', NULL, 1890000.00, 2290000.00, 110, 'CON_HANG'),
(215, 38, 'SP38-V3', 'Kangaroo Quạt hơi nước KG825', 'Xám', '{\"Dung tích\": \"4L\", \"Công suất\": \"75W\"}', NULL, 1390000.00, 1690000.00, 140, 'CON_HANG'),
(216, 39, 'SP39-V2', 'LG AI DD 10.5kg', 'Trắng', '{\"Công nghệ\": \"Inverter\", \"Khối lượng\": \"10.5kg\"}', NULL, 9990000.00, 11490000.00, 65, 'CON_HANG'),
(217, 39, 'SP39-V3', 'LG AI DD 8kg', 'Bạc', '{\"Công nghệ\": \"Inverter\", \"Khối lượng\": \"8kg\"}', NULL, 7490000.00, 8990000.00, 85, 'CON_HANG'),
(218, 40, 'SP40-V2', 'Electrolux UltraMix 9kg', 'Trắng', '{\"Công nghệ\": \"UltraMix\", \"Inverter\": \"Có\", \"Khối lượng\": \"9kg\"}', NULL, 9990000.00, 11990000.00, 70, 'CON_HANG'),
(219, 40, 'SP40-V3', 'Electrolux UltraMix 11kg', 'Bạc', '{\"Công nghệ\": \"UltraMix\", \"Inverter\": \"Có\", \"Khối lượng\": \"11kg\"}', NULL, 12990000.00, 14990000.00, 50, 'CON_HANG'),
(220, 41, 'SP41-V2', 'Samsung SpaceMax 500L', 'Bạc', '{\"Dung tích\": \"500L\", \"Inverter\": \"Có\"}', NULL, 16990000.00, 18990000.00, 45, 'CON_HANG'),
(221, 41, 'SP41-V3', 'Samsung SpaceMax 350L', 'Đen', '{\"Dung tích\": \"350L\", \"Inverter\": \"Có\"}', NULL, 11990000.00, 13990000.00, 70, 'CON_HANG'),
(222, 42, 'SP42-V2', 'Hitachi 550L Inverter', 'Bạc', '{\"Dung tích\": \"550L\", \"Ngăn đá mềm\": \"Có\"}', NULL, 19990000.00, 22990000.00, 35, 'CON_HANG'),
(223, 42, 'SP42-V3', 'Hitachi 380L Inverter', 'Trắng', '{\"Dung tích\": \"380L\", \"Ngăn đá mềm\": \"Không\"}', NULL, 12990000.00, 14990000.00, 60, 'CON_HANG'),
(224, 43, 'SP43-V2', 'Kangaroo RO 9 lõi', 'Trắng', '{\"Số lõi\": \"9\", \"Công nghệ\": \"RO\", \"Nóng lạnh\": \"Không\"}', NULL, 6990000.00, 8990000.00, 100, 'CON_HANG'),
(225, 43, 'SP43-V3', 'Kangaroo RO 13 lõi', 'Xám', '{\"Số lõi\": \"13\", \"Công nghệ\": \"RO Hydrogen\", \"Nóng lạnh\": \"Có\"}', NULL, 10990000.00, 12990000.00, 60, 'CON_HANG'),
(226, 44, 'SP44-V2', 'Mutosi RO 7 lõi', 'Trắng', '{\"Số lõi\": \"7\", \"Dung tích\": \"3L\"}', NULL, 5990000.00, 7990000.00, 110, 'CON_HANG'),
(227, 44, 'SP44-V3', 'Mutosi RO 11 lõi nóng lạnh', 'Vàng', '{\"Số lõi\": \"11\", \"Nóng lạnh\": \"Có\"}', NULL, 8490000.00, 10490000.00, 80, 'CON_HANG'),
(228, 45, 'SP45-V2', 'iPhone 12 128GB Like new', 'Trắng', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"128GB\"}', 'Like new, pin 87%', 8990000.00, 9990000.00, 25, 'CON_HANG'),
(229, 45, 'SP45-V3', 'iPhone 12 Pro 256GB Like new', 'Vàng', '{\"RAM\": \"6GB\", \"Bộ nhớ\": \"256GB\"}', 'Like new, pin 90%', 10990000.00, 11990000.00, 15, 'CON_HANG'),
(230, 46, 'SP46-V2', 'MacBook Air 2017 i7/8GB/256GB', 'Bạc', '{\"CPU\": \"i7\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 8490000.00, 9490000.00, 20, 'CON_HANG'),
(231, 46, 'SP46-V3', 'MacBook Air 2017 i5/8GB/128GB', 'Vàng', '{\"CPU\": \"i5\", \"RAM\": \"8GB\", \"SSD\": \"128GB\"}', NULL, 6490000.00, 7490000.00, 30, 'CON_HANG'),
(232, 47, 'SP47-V2', 'Electrolux bơm nhiệt 9kg', 'Trắng', '{\"Công nghệ\": \"Bơm nhiệt\", \"Khối lượng\": \"9kg\"}', NULL, 14990000.00, 16990000.00, 35, 'CON_HANG'),
(233, 47, 'SP47-V3', 'Electrolux bơm nhiệt 7kg', 'Bạc', '{\"Công nghệ\": \"Bơm nhiệt\", \"Khối lượng\": \"7kg\"}', NULL, 11990000.00, 13990000.00, 45, 'CON_HANG'),
(234, 48, 'SP48-V2', 'Beko thông hơi 8kg', 'Trắng', '{\"Loại\": \"Thông hơi\", \"Khối lượng\": \"8kg\"}', NULL, 8990000.00, 10990000.00, 60, 'CON_HANG'),
(235, 48, 'SP48-V3', 'Beko bơm nhiệt 9kg', 'Bạc', '{\"Loại\": \"Bơm nhiệt\", \"Khối lượng\": \"9kg\"}', NULL, 12990000.00, 14990000.00, 40, 'CON_HANG'),
(236, 49, 'SP49-V2', 'Xiaomi Camera 2K Pro', 'Trắng', '{\"Độ phân giải\": \"2K\", \"Góc rộng\": \"140°\", \"AI\": \"Có\"}', NULL, 899000.00, 1099000.00, 200, 'CON_HANG'),
(237, 49, 'SP49-V3', 'Xiaomi Camera 1080p', 'Trắng', '{\"Độ phân giải\": \"1080p\", \"Góc rộng\": \"120°\"}', NULL, 499000.00, 699000.00, 350, 'CON_HANG'),
(238, 50, 'SP50-V2', 'TP-Link Tapo C325WB', 'Trắng', '{\"Độ phân giải\": \"2K\", \"Chống nước\": \"IP66\", \"Màu đêm\": \"Starlight\"}', NULL, 1299000.00, 1599000.00, 150, 'CON_HANG'),
(239, 50, 'SP50-V3', 'TP-Link Tapo C310', 'Trắng', '{\"Độ phân giải\": \"3MP\", \"Chống nước\": \"IP66\"}', NULL, 699000.00, 899000.00, 220, 'CON_HANG'),
(240, 51, 'SP51-V2', 'Philips Bàn ủi đứng 2000W', 'Xanh', '{\"Công suất\": \"2000W\", \"Bình nước\": \"2L\"}', NULL, 2490000.00, 2990000.00, 60, 'CON_HANG'),
(241, 51, 'SP51-V3', 'Philips Bàn ủi cầm tay', 'Trắng', '{\"Công suất\": \"1200W\", \"Bình nước\": \"0.3L\"}', NULL, 890000.00, 1190000.00, 120, 'CON_HANG'),
(242, 52, 'SP52-V2', 'Panasonic Lò vi sóng 25L', 'Đen', '{\"Dung tích\": \"25L\", \"Công suất\": \"1000W\"}', NULL, 2990000.00, 3490000.00, 70, 'CON_HANG'),
(243, 52, 'SP52-V3', 'Panasonic Lò vi sóng 20L Inverter', 'Bạc', '{\"Dung tích\": \"20L\", \"Công nghệ\": \"Inverter\"}', NULL, 2690000.00, 3190000.00, 80, 'CON_HANG'),
(244, 53, 'SP53-V2', 'Senko Quạt sạc mini 5000mAh', 'Hồng', '{\"Pin\": \"5000mAh\", \"Tốc độ\": \"4 cấp\"}', NULL, 249000.00, 349000.00, 350, 'CON_HANG'),
(245, 53, 'SP53-V3', 'Senko Quạt sạc để bàn', 'Trắng', '{\"Pin\": \"2000mAh\", \"Tốc độ\": \"3 cấp\"}', NULL, 149000.00, 199000.00, 500, 'CON_HANG'),
(246, 54, 'SP54-V2', 'Mitsubishi Quạt đứng 3 cánh', 'Trắng', '{\"Công suất\": \"45W\", \"Điều khiển\": \"Remote\"}', NULL, 699000.00, 899000.00, 180, 'CON_HANG'),
(247, 54, 'SP54-V3', 'Mitsubishi Quạt treo tường', 'Trắng', '{\"Công suất\": \"50W\", \"Loại\": \"Treo tường\"}', NULL, 899000.00, 1099000.00, 120, 'CON_HANG'),
(248, 55, 'SP55-V2', 'Xiaomi Air Purifier 4 Lite', 'Trắng', '{\"CADR\": \"360m³/h\", \"Diện tích\": \"45m²\"}', NULL, 2990000.00, 3990000.00, 80, 'CON_HANG'),
(249, 55, 'SP55-V3', 'Xiaomi Air Purifier 4 Compact', 'Trắng', '{\"CADR\": \"250m³/h\", \"Diện tích\": \"30m²\"}', NULL, 1990000.00, 2590000.00, 110, 'CON_HANG'),
(250, 56, 'SP56-V2', 'Coway AP-1019C', 'Trắng', '{\"CADR\": \"200m³/h\", \"Diện tích\": \"25m²\"}', NULL, 3990000.00, 4990000.00, 60, 'CON_HANG'),
(251, 56, 'SP56-V3', 'Coway AP-2015F', 'Xám', '{\"CADR\": \"400m³/h\", \"Diện tích\": \"55m²\"}', NULL, 7990000.00, 8990000.00, 40, 'CON_HANG'),
(252, 57, 'SP57-V2', 'Sunhouse Bếp từ 3 vùng', 'Đen', '{\"Số vùng nấu\": \"3\", \"Công suất\": \"2500W\"}', NULL, 2290000.00, 2690000.00, 60, 'CON_HANG'),
(253, 57, 'SP57-V3', 'Sunhouse Bếp từ đơn', 'Đen', '{\"Số vùng nấu\": \"1\", \"Công suất\": \"2000W\"}', NULL, 990000.00, 1290000.00, 100, 'CON_HANG'),
(254, 58, 'SP58-V2', 'Tefal Bếp hồng ngoại đôi', 'Đen', '{\"Số vùng nấu\": \"2\", \"Công suất\": \"2200W\"}', NULL, 1990000.00, 2390000.00, 80, 'CON_HANG'),
(255, 58, 'SP58-V3', 'Tefal Bếp hồng ngoại mini', 'Trắng', '{\"Công suất\": \"1500W\", \"Kích thước\": \"Nhỏ\"}', NULL, 990000.00, 1290000.00, 120, 'CON_HANG'),
(256, 59, 'SP59-V2', 'Sharp Nồi cơm điện 1.0L', 'Trắng', '{\"Dung tích\": \"1.0L\", \"Chức năng\": \"Nấu cơm, cháo\"}', NULL, 499000.00, 699000.00, 250, 'CON_HANG'),
(257, 59, 'SP59-V3', 'Sharp Nồi cơm điện 2.0L', 'Đen', '{\"Dung tích\": \"2.0L\", \"Chức năng\": \"Nấu cơm, cháo, hầm\"}', NULL, 999000.00, 1199000.00, 150, 'CON_HANG'),
(258, 60, 'SP60-V2', 'Panasonic Nồi cơm điện 1.5L', 'Trắng', '{\"Dung tích\": \"1.5L\", \"Công nghệ\": \"Cao tần\"}', NULL, 2990000.00, 3490000.00, 60, 'CON_HANG'),
(259, 60, 'SP60-V3', 'Panasonic Nồi cơm điện 0.8L', 'Hồng', '{\"Dung tích\": \"0.8L\", \"Công nghệ\": \"Cao tần\"}', NULL, 1990000.00, 2390000.00, 80, 'CON_HANG'),
(260, 61, 'SP61-V2', 'Philips Máy xay 2.0L', 'Trắng', '{\"Công suất\": \"1000W\", \"Cối\": \"Thủy tinh\"}', NULL, 1590000.00, 1890000.00, 100, 'CON_HANG'),
(261, 61, 'SP61-V3', 'Philips Máy xay mini 0.6L', 'Xanh', '{\"Công suất\": \"350W\", \"Cối\": \"Nhựa\"}', NULL, 690000.00, 890000.00, 180, 'CON_HANG'),
(262, 62, 'SP62-V2', 'Kangaroo Máy ép chậm cao cấp', 'Bạc', '{\"Công nghệ\": \"Ép chậm\", \"Công suất\": \"200W\"}', NULL, 2990000.00, 3490000.00, 70, 'CON_HANG'),
(263, 62, 'SP62-V3', 'Kangaroo Máy ép đa năng', 'Trắng', '{\"Công nghệ\": \"Ép ly tâm\", \"Công suất\": \"400W\"}', NULL, 1690000.00, 1990000.00, 100, 'CON_HANG'),
(264, 63, 'SP63-V2', 'Philips Airfryer 6.2L', 'Đen', '{\"Dung tích\": \"6.2L\", \"Công suất\": \"1800W\"}', NULL, 3990000.00, 4690000.00, 90, 'CON_HANG'),
(265, 63, 'SP63-V3', 'Philips Airfryer 2.5L', 'Trắng', '{\"Dung tích\": \"2.5L\", \"Công suất\": \"1200W\"}', NULL, 2490000.00, 2990000.00, 130, 'CON_HANG'),
(266, 64, 'SP64-V2', 'Lock&Lock Nồi chiên 4L', 'Đỏ', '{\"Dung tích\": \"4L\", \"Công suất\": \"1400W\"}', NULL, 1690000.00, 2190000.00, 150, 'CON_HANG'),
(267, 64, 'SP64-V3', 'Lock&Lock Nồi chiên 6L', 'Đen', '{\"Dung tích\": \"6L\", \"Công suất\": \"1700W\"}', NULL, 2390000.00, 2890000.00, 100, 'CON_HANG'),
(268, 65, 'SP65-V2', 'HP LaserJet M140w', 'Trắng', '{\"Loại\": \"Laser đen trắng\", \"Tốc độ\": \"18 trang/phút\"}', NULL, 3490000.00, 3990000.00, 40, 'CON_HANG'),
(269, 65, 'SP65-V3', 'HP LaserJet M236dw', 'Đen', '{\"Loại\": \"Laser đen trắng\", \"Tốc độ\": \"30 trang/phút\"}', NULL, 4990000.00, 5990000.00, 30, 'CON_HANG'),
(270, 66, 'SP66-V2', 'Canon Pixma G670', 'Đen', '{\"Loại\": \"Ink tank\", \"Chức năng\": \"In ảnh, scan\"}', NULL, 6990000.00, 7990000.00, 35, 'CON_HANG'),
(271, 66, 'SP66-V3', 'Canon Pixma G470', 'Đen', '{\"Loại\": \"Ink tank\", \"Chức năng\": \"In, scan, copy\"}', NULL, 4990000.00, 5990000.00, 50, 'CON_HANG'),
(272, 67, 'SP67-V2', 'Kangaroo KG512', 'Đen', '{\"Dung tích bình\": \"6L\", \"Chức năng\": \"Nóng, lạnh, thường\"}', NULL, 3690000.00, 4390000.00, 50, 'CON_HANG'),
(273, 67, 'SP67-V3', 'Kangaroo KG510', 'Trắng', '{\"Dung tích bình\": \"4L\", \"Chức năng\": \"Nóng, thường\"}', NULL, 2590000.00, 3190000.00, 70, 'CON_HANG'),
(274, 68, 'SP68-V2', 'Sunhouse SHD535', 'Trắng', '{\"Làm lạnh\": \"8°C\", \"Làm nóng\": \"95°C\"}', NULL, 2990000.00, 3490000.00, 60, 'CON_HANG'),
(275, 68, 'SP68-V3', 'Sunhouse SHD530', 'Đen', '{\"Làm lạnh\": \"12°C\", \"Làm nóng\": \"85°C\"}', NULL, 2190000.00, 2690000.00, 80, 'CON_HANG'),
(276, 69, 'SP69-V2', 'Omron JPN2', 'Trắng', '{\"Loại\": \"Bắp tay\", \"Bộ nhớ\": \"90 lần\"}', NULL, 1590000.00, 1890000.00, 80, 'CON_HANG'),
(277, 69, 'SP69-V3', 'Omron HEM-7120', 'Trắng', '{\"Loại\": \"Bắp tay\", \"Bộ nhớ\": \"60 lần\"}', NULL, 1190000.00, 1490000.00, 100, 'CON_HANG'),
(278, 70, 'SP70-V2', 'Xiaomi Cân thông minh 2 Pro', 'Trắng', '{\"Chỉ số\": \"15 chỉ số\", \"Kết nối\": \"Bluetooth\"}', NULL, 699000.00, 899000.00, 180, 'CON_HANG'),
(279, 70, 'SP70-V3', 'Xiaomi Cân điện tử cơ bản', 'Trắng', '{\"Chỉ số\": \"Cân nặng\", \"Kết nối\": \"Không\"}', NULL, 299000.00, 399000.00, 300, 'CON_HANG'),
(280, 71, 'SP71-V2', 'Panasonic Massage cầm tay EW-RA38', 'Hồng', '{\"Công suất\": \"15W\", \"Đầu đấm\": \"4\"}', NULL, 1490000.00, 1790000.00, 80, 'CON_HANG'),
(281, 71, 'SP71-V3', 'Panasonic Massage mắt', 'Xanh', '{\"Chức năng\": \"Massage mắt\", \"Pin\": \"Sạc USB\"}', NULL, 890000.00, 1090000.00, 120, 'CON_HANG'),
(282, 72, 'SP72-V2', 'Cuckoo Gối massage toàn thân', 'Xám', '{\"Chức năng\": \"Rung nhiệt 8 điểm\", \"Điều khiển\": \"Remote\"}', NULL, 1299000.00, 1599000.00, 90, 'CON_HANG'),
(283, 72, 'SP72-V3', 'Cuckoo Gối massage lưng', 'Đen', '{\"Chức năng\": \"Rung nhiệt 4 điểm\"}', NULL, 699000.00, 899000.00, 140, 'CON_HANG'),
(284, 73, 'SP73-V2', 'Ariston 20L trực tiếp', 'Trắng', '{\"Dung tích\": \"20L\", \"Công suất\": \"2500W\"}', NULL, 2390000.00, 2890000.00, 40, 'CON_HANG'),
(285, 73, 'SP73-V3', 'Ariston 10L trực tiếp', 'Trắng', '{\"Dung tích\": \"10L\", \"Công suất\": \"2000W\"}', NULL, 1590000.00, 1990000.00, 60, 'CON_HANG'),
(286, 74, 'SP74-V2', 'Ferroli 40L gián tiếp', 'Trắng', '{\"Dung tích\": \"40L\", \"Công suất\": \"2500W\"}', NULL, 3490000.00, 3990000.00, 30, 'CON_HANG'),
(287, 74, 'SP74-V3', 'Ferroli 25L gián tiếp', 'Trắng', '{\"Dung tích\": \"25L\", \"Công suất\": \"2000W\"}', NULL, 2590000.00, 3090000.00, 45, 'CON_HANG'),
(288, 75, 'SP75-V2', 'Sharp 12L/ngày', 'Trắng', '{\"Lượng hút\": \"12L/ngày\", \"Diện tích\": \"35m²\"}', NULL, 4490000.00, 4990000.00, 45, 'CON_HANG'),
(289, 75, 'SP75-V3', 'Sharp 8L/ngày', 'Trắng', '{\"Lượng hút\": \"8L/ngày\", \"Diện tích\": \"25m²\"}', NULL, 3490000.00, 3990000.00, 65, 'CON_HANG'),
(290, 76, 'SP76-V2', 'Panasonic 20L/ngày', 'Trắng', '{\"Lượng hút\": \"20L/ngày\", \"Màn hình\": \"LCD\"}', NULL, 6990000.00, 7990000.00, 35, 'CON_HANG'),
(291, 76, 'SP76-V3', 'Panasonic 12L/ngày', 'Trắng', '{\"Lượng hút\": \"12L/ngày\", \"Điều khiển\": \"Cơ\"}', NULL, 4990000.00, 5990000.00, 55, 'CON_HANG'),
(292, 77, 'SP77-V2', 'Giant Escape 3', 'Đen', '{\"Khung\": \"Nhôm\", \"Phanh\": \"Vành\", \"Tốc độ\": \"18\"}', NULL, 7490000.00, 8990000.00, 30, 'CON_HANG'),
(293, 77, 'SP77-V3', 'Giant Escape 1', 'Xanh', '{\"Khung\": \"Nhôm\", \"Phanh\": \"Đĩa cơ\", \"Tốc độ\": \"24\"}', NULL, 9990000.00, 11490000.00, 20, 'CON_HANG'),
(294, 78, 'SP78-V2', 'Trek FX 2', 'Xám', '{\"Khung\": \"Nhôm Alpha\", \"Phanh\": \"Đĩa cơ\", \"Tốc độ\": \"18\"}', NULL, 10990000.00, 12990000.00, 25, 'CON_HANG'),
(295, 78, 'SP78-V3', 'Trek FX 4', 'Đen', '{\"Khung\": \"Nhôm Alpha\", \"Phanh\": \"Đĩa thủy lực\", \"Tốc độ\": \"30\"}', NULL, 15990000.00, 17990000.00, 15, 'CON_HANG'),
(296, 79, 'SP79-V2', 'JBL Flip 6', 'Xanh', '{\"Công suất\": \"30W\", \"Chống nước\": \"IP67\", \"Pin\": \"12h\"}', NULL, 2590000.00, 2990000.00, 140, 'CON_HANG'),
(297, 79, 'SP79-V3', 'JBL Flip 6', 'Đỏ', '{\"Công suất\": \"30W\", \"Chống nước\": \"IP67\", \"Pin\": \"12h\"}', NULL, 2590000.00, 2990000.00, 130, 'CON_HANG'),
(298, 80, 'SP80-V2', 'Sony SRS-XE300', 'Đen', '{\"Công suất\": \"20W\", \"Pin\": \"24h\", \"Chống nước\": \"IP67\"}', NULL, 2990000.00, 3490000.00, 110, 'CON_HANG'),
(299, 80, 'SP80-V3', 'Sony SRS-XE200', 'Xanh', '{\"Công suất\": \"15W\", \"Pin\": \"16h\", \"Chống nước\": \"IP67\"}', NULL, 1990000.00, 2490000.00, 150, 'CON_HANG'),
(300, 2, 'SP2-V4', 'Samsung Galaxy S26 5G 12GB 512GB', 'Bạc', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 23990000.00, 27990000.00, 75, 'CON_HANG'),
(301, 10, 'SP10-V5', 'Comfee Inverter 1.5 HP', 'Trắng', '{\"Công suất\": \"1.5 HP - 12.000 BTU\"}', NULL, 12990000.00, 14990000.00, 48, 'CON_HANG'),
(302, 11, 'SP11-V5', 'Kangaroo RO Hydrogen 13 lõi', 'Đen', '{\"Số lõi\": \"13\", \"Công nghệ\": \"RO Hydrogen\"}', NULL, 9990000.00, 12590000.00, 64, 'CON_HANG'),
(303, 13, 'SP13-V5', 'Belkin 45W GaN', 'Trắng', '{\"Công suất\": \"45W\", \"Cổng\": \"1x USB-C\"}', NULL, 690000.00, 890000.00, 170, 'CON_HANG'),
(304, 7, 'SP7-V6', 'iPhone 15 256GB Hồng', 'Hồng', '{\"Bộ nhớ\": \"256GB\"}', NULL, 34990000.00, 36990000.00, 55, 'CON_HANG'),
(305, 7, 'SP7-V7', 'iPhone 15 512GB Xanh dương', 'Xanh dương', '{\"Bộ nhớ\": \"512GB\"}', NULL, 38990000.00, 40990000.00, 45, 'CON_HANG'),
(306, 2, 'SP2-V5', 'Samsung Galaxy S26 5G 8GB 256GB', 'Xanh navy', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 18990000.00, 22990000.00, 80, 'CON_HANG'),
(307, 2, 'SP2-V6', 'Samsung Galaxy S26 5G 8GB 512GB', 'Tím', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 21990000.00, 24990000.00, 72, 'CON_HANG'),
(308, 10, 'SP10-V6', 'Comfee Inverter 1.5 HP Bạc', 'Bạc', '{\"Công suất\": \"1.5 HP - 12.000 BTU\"}', NULL, 13290000.00, 15290000.00, 36, 'CON_HANG'),
(309, 10, 'SP10-V7', 'Comfee Inverter 2.0 HP Trắng', 'Trắng', '{\"Công suất\": \"2 HP - 18.000 BTU\"}', NULL, 15290000.00, 17290000.00, 28, 'CON_HANG'),
(310, 11, 'SP11-V6', 'Kangaroo RO 9 lõi Nóng Lạnh', 'Trắng', '{\"Số lõi\": \"9\", \"Công nghệ\": \"RO\"}', NULL, 7990000.00, 9890000.00, 58, 'CON_HANG'),
(311, 13, 'SP13-V6', 'Belkin 65W GaN 1 cổng', 'Trắng', '{\"Công suất\": \"65W\", \"Cổng\": \"1x USB-C\"}', NULL, 790000.00, 990000.00, 150, 'CON_HANG'),
(312, 13, 'SP13-V7', 'Belkin 65W GaN 2 cổng Trắng', 'Trắng', '{\"Công suất\": \"65W\", \"Cổng\": \"2x USB-C\"}', NULL, 920000.00, 1120000.00, 120, 'CON_HANG'),
(313, 1, 'AUTO-P1-V3', 'iPhone 256GB - Ban 3', 'Xam', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 31199.00, 231199.00, 117, 'CON_HANG'),
(314, 1, 'AUTO-P1-V4', 'iPhone 256GB - Ban 4', 'Tim', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 41199.00, 241199.00, 116, 'CON_HANG'),
(315, 8, 'AUTO-P8-V3', 'ThinkPad X1 Carbon 16GB/512GB - Ban 3', 'Vang', '{\"CPU\": \"Intel Core Ultra 5\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', 'Màn hình IPS 2.2K', 36020000.00, 39990000.00, 27, 'CON_HANG'),
(316, 8, 'AUTO-P8-V4', 'ThinkPad X1 Carbon 16GB/512GB - Ban 4', 'Den', '{\"CPU\": \"Intel Core Ultra 5\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', 'Màn hình IPS 2.2K', 36030000.00, 39990000.00, 26, 'CON_HANG'),
(317, 9, 'AUTO-P9-V4', 'Ốp lưng Magsafe Sunset - Ban 4', 'Xanh duong', NULL, 'Tương thích S26 Ultra', 490000.00, 690000.00, 276, 'CON_HANG'),
(318, 12, 'AUTO-P12-V4', 'Xiaomi Google TV QLED 32 inch HD A Pro 2026 L32MB-APSEA - Ban 4', 'Vang', '{\"Màn hình\": \"32 inch\"}', NULL, 4230000.00, 4490000.00, 92, 'CON_HANG'),
(319, 14, 'AUTO-P14-V3', 'iPhone 16 Pro Max 512GB - Ban 3', 'Den', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 40020000.00, 42990000.00, 127, 'CON_HANG'),
(320, 14, 'AUTO-P14-V4', 'iPhone 16 Pro Max 512GB - Ban 4', 'Xanh duong', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 40030000.00, 42990000.00, 126, 'CON_HANG'),
(321, 15, 'AUTO-P15-V3', 'Galaxy S25 Ultra 512GB - Ban 3', 'Bac', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 33020000.00, 35990000.00, 137, 'CON_HANG'),
(322, 15, 'AUTO-P15-V4', 'Galaxy S25 Ultra 512GB - Ban 4', 'Xam', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 33030000.00, 35990000.00, 136, 'CON_HANG'),
(323, 16, 'AUTO-P16-V3', 'Xiaomi 15 Pro 512GB - Ban 3', 'Vang', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 21020000.00, 22990000.00, 177, 'CON_HANG'),
(324, 16, 'AUTO-P16-V4', 'Xiaomi 15 Pro 512GB - Ban 4', 'Den', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 21030000.00, 22990000.00, 176, 'CON_HANG'),
(325, 17, 'AUTO-P17-V3', 'iPad Pro 13 inch 512GB - Ban 3', 'Trang', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 33020000.00, 34990000.00, 67, 'CON_HANG'),
(326, 17, 'AUTO-P17-V4', 'iPad Pro 13 inch 512GB - Ban 4', 'Bac', '{\"RAM\": \"8GB\", \"Bộ nhớ\": \"512GB\"}', NULL, 33030000.00, 34990000.00, 66, 'CON_HANG'),
(327, 18, 'AUTO-P18-V3', 'Galaxy Tab S10 Ultra 256GB - Ban 3', 'Hong', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 22020000.00, 24990000.00, 77, 'CON_HANG'),
(328, 18, 'AUTO-P18-V4', 'Galaxy Tab S10 Ultra 256GB - Ban 4', 'Vang', '{\"RAM\": \"12GB\", \"Bộ nhớ\": \"256GB\"}', NULL, 22030000.00, 24990000.00, 76, 'CON_HANG'),
(329, 19, 'AUTO-P19-V3', 'MacBook Pro M3 Pro 36GB/1TB - Ban 3', 'Tim', '{\"CPU\": \"M3 Pro 12 nhân\", \"RAM\": \"36GB\", \"SSD\": \"1TB\"}', NULL, 48020000.00, 52990000.00, 37, 'CON_HANG'),
(330, 19, 'AUTO-P19-V4', 'MacBook Pro M3 Pro 36GB/1TB - Ban 4', 'Trang', '{\"CPU\": \"M3 Pro 12 nhân\", \"RAM\": \"36GB\", \"SSD\": \"1TB\"}', NULL, 48030000.00, 52990000.00, 36, 'CON_HANG'),
(331, 20, 'AUTO-P20-V3', 'Dell XPS 15 i7/16GB/512GB - Ban 3', 'Xanh duong', '{\"CPU\": \"i7-13700H\", \"RAM\": \"16GB\", \"SSD\": \"512GB\", \"GPU\": \"RTX 4050\"}', NULL, 40020000.00, 43990000.00, 32, 'CON_HANG'),
(332, 20, 'AUTO-P20-V4', 'Dell XPS 15 i7/16GB/512GB - Ban 4', 'Hong', '{\"CPU\": \"i7-13700H\", \"RAM\": \"16GB\", \"SSD\": \"512GB\", \"GPU\": \"RTX 4050\"}', NULL, 40030000.00, 43990000.00, 31, 'CON_HANG'),
(333, 21, 'AUTO-P21-V3', 'LG 27 inch 5K - Ban 3', 'Xam', '{\"Độ phân giải\": \"5120x2880\", \"Tần số\": \"60Hz\"}', NULL, 23020000.00, 24990000.00, 27, 'CON_HANG'),
(334, 21, 'AUTO-P21-V4', 'LG 27 inch 5K - Ban 4', 'Tim', '{\"Độ phân giải\": \"5120x2880\", \"Tần số\": \"60Hz\"}', NULL, 23030000.00, 24990000.00, 26, 'CON_HANG'),
(335, 22, 'AUTO-P22-V3', 'Odyssey G7 27 inch 240Hz - Ban 3', 'Den', '{\"Độ phân giải\": \"2560x1440\", \"Kích thước\": \"27 inch\"}', NULL, 13020000.00, 14990000.00, 47, 'CON_HANG'),
(336, 22, 'AUTO-P22-V4', 'Odyssey G7 27 inch 240Hz - Ban 4', 'Xanh duong', '{\"Độ phân giải\": \"2560x1440\", \"Kích thước\": \"27 inch\"}', NULL, 13030000.00, 14990000.00, 46, 'CON_HANG'),
(337, 23, 'AUTO-P23-V3', 'OptiPlex 7010 i5/8GB/256GB - Ban 3', 'Bac', '{\"CPU\": \"i5-13500\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 14020000.00, 15990000.00, 37, 'CON_HANG'),
(338, 23, 'AUTO-P23-V4', 'OptiPlex 7010 i5/8GB/256GB - Ban 4', 'Xam', '{\"CPU\": \"i5-13500\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 14030000.00, 15990000.00, 36, 'CON_HANG'),
(339, 24, 'AUTO-P24-V3', 'EliteDesk 800 G9 i7/16GB/512GB - Ban 3', 'Vang', '{\"CPU\": \"i7-13700\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', NULL, 24020000.00, 26990000.00, 27, 'CON_HANG'),
(340, 24, 'AUTO-P24-V4', 'EliteDesk 800 G9 i7/16GB/512GB - Ban 4', 'Den', '{\"CPU\": \"i7-13700\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', NULL, 24030000.00, 26990000.00, 26, 'CON_HANG'),
(341, 25, 'AUTO-P25-V3', 'MX Master 3S - Ban 3', 'Trang', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth/2.4GHz\"}', NULL, 2029000.00, 2299000.00, 277, 'CON_HANG'),
(342, 25, 'AUTO-P25-V4', 'MX Master 3S - Ban 4', 'Bac', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth/2.4GHz\"}', NULL, 2039000.00, 2299000.00, 276, 'CON_HANG'),
(343, 26, 'AUTO-P26-V3', 'BlackWidow V4 Pro Yellow - Ban 3', 'Hong', '{\"Switch\": \"Yellow\", \"Đèn\": \"RGB\"}', NULL, 4029000.00, 4599000.00, 97, 'CON_HANG'),
(344, 26, 'AUTO-P26-V4', 'BlackWidow V4 Pro Yellow - Ban 4', 'Vang', '{\"Switch\": \"Yellow\", \"Đèn\": \"RGB\"}', NULL, 4039000.00, 4599000.00, 96, 'CON_HANG'),
(345, 27, 'AUTO-P27-V3', 'Sim Data 5GB/ngày eSIM - Ban 3', 'Tim', NULL, NULL, 129000.00, 329000.00, 597, 'CON_HANG'),
(346, 27, 'AUTO-P27-V4', 'Sim Data 5GB/ngày eSIM - Ban 4', 'Trang', NULL, NULL, 139000.00, 339000.00, 596, 'CON_HANG'),
(347, 28, 'AUTO-P28-V3', 'Sim trọn gói 6 tháng - Ban 3', 'Xanh duong', NULL, NULL, 529000.00, 729000.00, 197, 'CON_HANG'),
(348, 28, 'AUTO-P28-V4', 'Sim trọn gói 6 tháng - Ban 4', 'Hong', NULL, NULL, 539000.00, 739000.00, 196, 'CON_HANG'),
(349, 29, 'AUTO-P29-V3', 'Apple Watch S9 45mm - Ban 3', 'Xam', '{\"GPS\": \"Có\", \"Viền\": \"Nhôm\"}', NULL, 11020000.00, 11990000.00, 87, 'CON_HANG'),
(350, 29, 'AUTO-P29-V4', 'Apple Watch S9 45mm - Ban 4', 'Tim', '{\"GPS\": \"Có\", \"Viền\": \"Nhôm\"}', NULL, 11030000.00, 11990000.00, 86, 'CON_HANG'),
(351, 30, 'AUTO-P30-V3', 'Galaxy Watch 7 40mm - Ban 3', 'Den', '{\"Pin\": \"300mAh\", \"Chống nước\": \"5ATM\"}', NULL, 6020000.00, 6990000.00, 137, 'CON_HANG'),
(352, 30, 'AUTO-P30-V4', 'Galaxy Watch 7 40mm - Ban 4', 'Xanh duong', '{\"Pin\": \"300mAh\", \"Chống nước\": \"5ATM\"}', NULL, 6030000.00, 6990000.00, 136, 'CON_HANG'),
(353, 31, 'AUTO-P31-V3', 'QLED Q80D 65 inch - Ban 3', 'Bac', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"65 inch\"}', NULL, 20020000.00, 22990000.00, 42, 'CON_HANG'),
(354, 31, 'AUTO-P31-V4', 'QLED Q80D 65 inch - Ban 4', 'Xam', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"65 inch\"}', NULL, 20030000.00, 22990000.00, 41, 'CON_HANG'),
(355, 32, 'AUTO-P32-V3', 'OLED C3 77 inch - Ban 3', 'Vang', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"77 inch\"}', NULL, 50020000.00, 54990000.00, 22, 'CON_HANG'),
(356, 32, 'AUTO-P32-V4', 'OLED C3 77 inch - Ban 4', 'Den', '{\"Độ phân giải\": \"4K\", \"Tần số\": \"120Hz\", \"Kích thước\": \"77 inch\"}', NULL, 50030000.00, 54990000.00, 21, 'CON_HANG'),
(357, 33, 'AUTO-P33-V3', 'Daikin Inverter 1.0HP - Ban 3', 'Trang', '{\"BTU\": \"9000\", \"Inverter\": \"Có\"}', NULL, 11020000.00, 12990000.00, 97, 'CON_HANG'),
(358, 33, 'AUTO-P33-V4', 'Daikin Inverter 1.0HP - Ban 4', 'Bac', '{\"BTU\": \"9000\", \"Inverter\": \"Có\"}', NULL, 11030000.00, 12990000.00, 96, 'CON_HANG'),
(359, 34, 'AUTO-P34-V3', 'Panasonic Inverter 1.5HP - Ban 3', 'Hong', '{\"BTU\": \"12000\", \"Inverter\": \"Có\"}', NULL, 15020000.00, 16990000.00, 77, 'CON_HANG'),
(360, 34, 'AUTO-P34-V4', 'Panasonic Inverter 1.5HP - Ban 4', 'Vang', '{\"BTU\": \"12000\", \"Inverter\": \"Có\"}', NULL, 15030000.00, 16990000.00, 76, 'CON_HANG'),
(361, 35, 'AUTO-P35-V3', 'Xiaomi Robot S10 - Ban 3', 'Tim', '{\"Lực hút\": \"3000Pa\", \"Chức năng\": \"Hút\"}', NULL, 7020000.00, 8490000.00, 117, 'CON_HANG'),
(362, 35, 'AUTO-P35-V4', 'Xiaomi Robot S10 - Ban 4', 'Trang', '{\"Lực hút\": \"3000Pa\", \"Chức năng\": \"Hút\"}', NULL, 7030000.00, 8490000.00, 116, 'CON_HANG'),
(363, 36, 'AUTO-P36-V3', 'Deebot T10 Omni - Ban 3', 'Xanh duong', '{\"Lực hút\": \"4000Pa\", \"Lau nước nóng\": \"Không\"}', NULL, 15020000.00, 16990000.00, 52, 'CON_HANG'),
(364, 36, 'AUTO-P36-V4', 'Deebot T10 Omni - Ban 4', 'Hong', '{\"Lực hút\": \"4000Pa\", \"Lau nước nóng\": \"Không\"}', NULL, 15030000.00, 16990000.00, 51, 'CON_HANG'),
(365, 37, 'AUTO-P37-V3', 'Sunhouse Quạt điều hòa SHD7711 - Ban 3', 'Xam', '{\"Dung tích nước\": \"7L\", \"Công suất\": \"120W\"}', NULL, 2320000.00, 2790000.00, 87, 'CON_HANG'),
(366, 37, 'AUTO-P37-V4', 'Sunhouse Quạt điều hòa SHD7711 - Ban 4', 'Tim', '{\"Dung tích nước\": \"7L\", \"Công suất\": \"120W\"}', NULL, 2330000.00, 2790000.00, 86, 'CON_HANG'),
(367, 38, 'AUTO-P38-V3', 'Kangaroo Quạt hơi nước KG829 - Ban 3', 'Den', '{\"Dung tích\": \"6L\", \"Hẹn giờ\": \"8h\"}', NULL, 1920000.00, 2290000.00, 107, 'CON_HANG'),
(368, 38, 'AUTO-P38-V4', 'Kangaroo Quạt hơi nước KG829 - Ban 4', 'Xanh duong', '{\"Dung tích\": \"6L\", \"Hẹn giờ\": \"8h\"}', NULL, 1930000.00, 2290000.00, 106, 'CON_HANG'),
(369, 39, 'AUTO-P39-V3', 'LG AI DD 10.5kg - Ban 3', 'Bac', '{\"Công nghệ\": \"Inverter\", \"Khối lượng\": \"10.5kg\"}', NULL, 10020000.00, 11490000.00, 62, 'CON_HANG'),
(370, 39, 'AUTO-P39-V4', 'LG AI DD 10.5kg - Ban 4', 'Xam', '{\"Công nghệ\": \"Inverter\", \"Khối lượng\": \"10.5kg\"}', NULL, 10030000.00, 11490000.00, 61, 'CON_HANG'),
(371, 40, 'AUTO-P40-V3', 'Electrolux UltraMix 9kg - Ban 3', 'Vang', '{\"Công nghệ\": \"UltraMix\", \"Inverter\": \"Có\", \"Khối lượng\": \"9kg\"}', NULL, 10020000.00, 11990000.00, 67, 'CON_HANG'),
(372, 40, 'AUTO-P40-V4', 'Electrolux UltraMix 9kg - Ban 4', 'Den', '{\"Công nghệ\": \"UltraMix\", \"Inverter\": \"Có\", \"Khối lượng\": \"9kg\"}', NULL, 10030000.00, 11990000.00, 66, 'CON_HANG'),
(373, 41, 'AUTO-P41-V3', 'Samsung SpaceMax 500L - Ban 3', 'Trang', '{\"Dung tích\": \"500L\", \"Inverter\": \"Có\"}', NULL, 17020000.00, 18990000.00, 42, 'CON_HANG'),
(374, 41, 'AUTO-P41-V4', 'Samsung SpaceMax 500L - Ban 4', 'Bac', '{\"Dung tích\": \"500L\", \"Inverter\": \"Có\"}', NULL, 17030000.00, 18990000.00, 41, 'CON_HANG'),
(375, 42, 'AUTO-P42-V3', 'Hitachi 550L Inverter - Ban 3', 'Hong', '{\"Dung tích\": \"550L\", \"Ngăn đá mềm\": \"Có\"}', NULL, 20020000.00, 22990000.00, 32, 'CON_HANG'),
(376, 42, 'AUTO-P42-V4', 'Hitachi 550L Inverter - Ban 4', 'Vang', '{\"Dung tích\": \"550L\", \"Ngăn đá mềm\": \"Có\"}', NULL, 20030000.00, 22990000.00, 31, 'CON_HANG'),
(377, 43, 'AUTO-P43-V3', 'Kangaroo RO 9 lõi - Ban 3', 'Tim', '{\"Số lõi\": \"9\", \"Công nghệ\": \"RO\", \"Nóng lạnh\": \"Không\"}', NULL, 7020000.00, 8990000.00, 97, 'CON_HANG'),
(378, 43, 'AUTO-P43-V4', 'Kangaroo RO 9 lõi - Ban 4', 'Trang', '{\"Số lõi\": \"9\", \"Công nghệ\": \"RO\", \"Nóng lạnh\": \"Không\"}', NULL, 7030000.00, 8990000.00, 96, 'CON_HANG'),
(379, 44, 'AUTO-P44-V3', 'Mutosi RO 7 lõi - Ban 3', 'Xanh duong', '{\"Số lõi\": \"7\", \"Dung tích\": \"3L\"}', NULL, 6020000.00, 7990000.00, 107, 'CON_HANG'),
(380, 44, 'AUTO-P44-V4', 'Mutosi RO 7 lõi - Ban 4', 'Hong', '{\"Số lõi\": \"7\", \"Dung tích\": \"3L\"}', NULL, 6030000.00, 7990000.00, 106, 'CON_HANG'),
(381, 45, 'AUTO-P45-V3', 'iPhone 12 128GB Like new - Ban 3', 'Xam', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"128GB\"}', 'Like new, pin 87%', 9020000.00, 9990000.00, 22, 'CON_HANG'),
(382, 45, 'AUTO-P45-V4', 'iPhone 12 128GB Like new - Ban 4', 'Tim', '{\"RAM\": \"4GB\", \"Bộ nhớ\": \"128GB\"}', 'Like new, pin 87%', 9030000.00, 9990000.00, 21, 'CON_HANG'),
(383, 46, 'AUTO-P46-V3', 'MacBook Air 2017 i7/8GB/256GB - Ban 3', 'Den', '{\"CPU\": \"i7\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 8520000.00, 9490000.00, 17, 'CON_HANG'),
(384, 46, 'AUTO-P46-V4', 'MacBook Air 2017 i7/8GB/256GB - Ban 4', 'Xanh duong', '{\"CPU\": \"i7\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 8530000.00, 9490000.00, 16, 'CON_HANG'),
(385, 47, 'AUTO-P47-V3', 'Electrolux bơm nhiệt 9kg - Ban 3', 'Bac', '{\"Công nghệ\": \"Bơm nhiệt\", \"Khối lượng\": \"9kg\"}', NULL, 15020000.00, 16990000.00, 32, 'CON_HANG'),
(386, 47, 'AUTO-P47-V4', 'Electrolux bơm nhiệt 9kg - Ban 4', 'Xam', '{\"Công nghệ\": \"Bơm nhiệt\", \"Khối lượng\": \"9kg\"}', NULL, 15030000.00, 16990000.00, 31, 'CON_HANG'),
(387, 48, 'AUTO-P48-V3', 'Beko thông hơi 8kg - Ban 3', 'Vang', '{\"Loại\": \"Thông hơi\", \"Khối lượng\": \"8kg\"}', NULL, 9020000.00, 10990000.00, 57, 'CON_HANG'),
(388, 48, 'AUTO-P48-V4', 'Beko thông hơi 8kg - Ban 4', 'Den', '{\"Loại\": \"Thông hơi\", \"Khối lượng\": \"8kg\"}', NULL, 9030000.00, 10990000.00, 56, 'CON_HANG'),
(389, 49, 'AUTO-P49-V3', 'Xiaomi Camera 2K Pro - Ban 3', 'Trang', '{\"Độ phân giải\": \"2K\", \"Góc rộng\": \"140°\", \"AI\": \"Có\"}', NULL, 929000.00, 1129000.00, 197, 'CON_HANG'),
(390, 49, 'AUTO-P49-V4', 'Xiaomi Camera 2K Pro - Ban 4', 'Bac', '{\"Độ phân giải\": \"2K\", \"Góc rộng\": \"140°\", \"AI\": \"Có\"}', NULL, 939000.00, 1139000.00, 196, 'CON_HANG'),
(391, 50, 'AUTO-P50-V3', 'TP-Link Tapo C325WB - Ban 3', 'Hong', '{\"Độ phân giải\": \"2K\", \"Chống nước\": \"IP66\", \"Màu đêm\": \"Starlight\"}', NULL, 1329000.00, 1599000.00, 147, 'CON_HANG'),
(392, 50, 'AUTO-P50-V4', 'TP-Link Tapo C325WB - Ban 4', 'Vang', '{\"Độ phân giải\": \"2K\", \"Chống nước\": \"IP66\", \"Màu đêm\": \"Starlight\"}', NULL, 1339000.00, 1599000.00, 146, 'CON_HANG'),
(393, 51, 'AUTO-P51-V3', 'Philips Bàn ủi đứng 2000W - Ban 3', 'Tim', '{\"Công suất\": \"2000W\", \"Bình nước\": \"2L\"}', NULL, 2520000.00, 2990000.00, 57, 'CON_HANG'),
(394, 51, 'AUTO-P51-V4', 'Philips Bàn ủi đứng 2000W - Ban 4', 'Trang', '{\"Công suất\": \"2000W\", \"Bình nước\": \"2L\"}', NULL, 2530000.00, 2990000.00, 56, 'CON_HANG'),
(395, 52, 'AUTO-P52-V3', 'Panasonic Lò vi sóng 25L - Ban 3', 'Xanh duong', '{\"Dung tích\": \"25L\", \"Công suất\": \"1000W\"}', NULL, 3020000.00, 3490000.00, 67, 'CON_HANG'),
(396, 52, 'AUTO-P52-V4', 'Panasonic Lò vi sóng 25L - Ban 4', 'Hong', '{\"Dung tích\": \"25L\", \"Công suất\": \"1000W\"}', NULL, 3030000.00, 3490000.00, 66, 'CON_HANG'),
(397, 53, 'AUTO-P53-V3', 'Senko Quạt sạc mini 5000mAh - Ban 3', 'Xam', '{\"Pin\": \"5000mAh\", \"Tốc độ\": \"4 cấp\"}', NULL, 279000.00, 479000.00, 347, 'CON_HANG'),
(398, 53, 'AUTO-P53-V4', 'Senko Quạt sạc mini 5000mAh - Ban 4', 'Tim', '{\"Pin\": \"5000mAh\", \"Tốc độ\": \"4 cấp\"}', NULL, 289000.00, 489000.00, 346, 'CON_HANG'),
(399, 54, 'AUTO-P54-V3', 'Mitsubishi Quạt đứng 3 cánh - Ban 3', 'Den', '{\"Công suất\": \"45W\", \"Điều khiển\": \"Remote\"}', NULL, 729000.00, 929000.00, 177, 'CON_HANG'),
(400, 54, 'AUTO-P54-V4', 'Mitsubishi Quạt đứng 3 cánh - Ban 4', 'Xanh duong', '{\"Công suất\": \"45W\", \"Điều khiển\": \"Remote\"}', NULL, 739000.00, 939000.00, 176, 'CON_HANG'),
(401, 55, 'AUTO-P55-V3', 'Xiaomi Air Purifier 4 Lite - Ban 3', 'Bac', '{\"CADR\": \"360m³/h\", \"Diện tích\": \"45m²\"}', NULL, 3020000.00, 3990000.00, 77, 'CON_HANG'),
(402, 55, 'AUTO-P55-V4', 'Xiaomi Air Purifier 4 Lite - Ban 4', 'Xam', '{\"CADR\": \"360m³/h\", \"Diện tích\": \"45m²\"}', NULL, 3030000.00, 3990000.00, 76, 'CON_HANG'),
(403, 56, 'AUTO-P56-V3', 'Coway AP-1019C - Ban 3', 'Vang', '{\"CADR\": \"200m³/h\", \"Diện tích\": \"25m²\"}', NULL, 4020000.00, 4990000.00, 57, 'CON_HANG'),
(404, 56, 'AUTO-P56-V4', 'Coway AP-1019C - Ban 4', 'Den', '{\"CADR\": \"200m³/h\", \"Diện tích\": \"25m²\"}', NULL, 4030000.00, 4990000.00, 56, 'CON_HANG'),
(405, 57, 'AUTO-P57-V3', 'Sunhouse Bếp từ 3 vùng - Ban 3', 'Trang', '{\"Số vùng nấu\": \"3\", \"Công suất\": \"2500W\"}', NULL, 2320000.00, 2690000.00, 57, 'CON_HANG'),
(406, 57, 'AUTO-P57-V4', 'Sunhouse Bếp từ 3 vùng - Ban 4', 'Bac', '{\"Số vùng nấu\": \"3\", \"Công suất\": \"2500W\"}', NULL, 2330000.00, 2690000.00, 56, 'CON_HANG'),
(407, 58, 'AUTO-P58-V3', 'Tefal Bếp hồng ngoại đôi - Ban 3', 'Hong', '{\"Số vùng nấu\": \"2\", \"Công suất\": \"2200W\"}', NULL, 2020000.00, 2390000.00, 77, 'CON_HANG'),
(408, 58, 'AUTO-P58-V4', 'Tefal Bếp hồng ngoại đôi - Ban 4', 'Vang', '{\"Số vùng nấu\": \"2\", \"Công suất\": \"2200W\"}', NULL, 2030000.00, 2390000.00, 76, 'CON_HANG'),
(409, 59, 'AUTO-P59-V3', 'Sharp Nồi cơm điện 1.0L - Ban 3', 'Tim', '{\"Dung tích\": \"1.0L\", \"Chức năng\": \"Nấu cơm, cháo\"}', NULL, 529000.00, 729000.00, 247, 'CON_HANG'),
(410, 59, 'AUTO-P59-V4', 'Sharp Nồi cơm điện 1.0L - Ban 4', 'Trang', '{\"Dung tích\": \"1.0L\", \"Chức năng\": \"Nấu cơm, cháo\"}', NULL, 539000.00, 739000.00, 246, 'CON_HANG'),
(411, 60, 'AUTO-P60-V3', 'Panasonic Nồi cơm điện 1.5L - Ban 3', 'Xanh duong', '{\"Dung tích\": \"1.5L\", \"Công nghệ\": \"Cao tần\"}', NULL, 3020000.00, 3490000.00, 57, 'CON_HANG'),
(412, 60, 'AUTO-P60-V4', 'Panasonic Nồi cơm điện 1.5L - Ban 4', 'Hong', '{\"Dung tích\": \"1.5L\", \"Công nghệ\": \"Cao tần\"}', NULL, 3030000.00, 3490000.00, 56, 'CON_HANG'),
(413, 61, 'AUTO-P61-V3', 'Philips Máy xay 2.0L - Ban 3', 'Xam', '{\"Công suất\": \"1000W\", \"Cối\": \"Thủy tinh\"}', NULL, 1620000.00, 1890000.00, 97, 'CON_HANG'),
(414, 61, 'AUTO-P61-V4', 'Philips Máy xay 2.0L - Ban 4', 'Tim', '{\"Công suất\": \"1000W\", \"Cối\": \"Thủy tinh\"}', NULL, 1630000.00, 1890000.00, 96, 'CON_HANG'),
(415, 62, 'AUTO-P62-V3', 'Kangaroo Máy ép chậm cao cấp - Ban 3', 'Den', '{\"Công nghệ\": \"Ép chậm\", \"Công suất\": \"200W\"}', NULL, 3020000.00, 3490000.00, 67, 'CON_HANG'),
(416, 62, 'AUTO-P62-V4', 'Kangaroo Máy ép chậm cao cấp - Ban 4', 'Xanh duong', '{\"Công nghệ\": \"Ép chậm\", \"Công suất\": \"200W\"}', NULL, 3030000.00, 3490000.00, 66, 'CON_HANG'),
(417, 63, 'AUTO-P63-V3', 'Philips Airfryer 6.2L - Ban 3', 'Bac', '{\"Dung tích\": \"6.2L\", \"Công suất\": \"1800W\"}', NULL, 4020000.00, 4690000.00, 87, 'CON_HANG'),
(418, 63, 'AUTO-P63-V4', 'Philips Airfryer 6.2L - Ban 4', 'Xam', '{\"Dung tích\": \"6.2L\", \"Công suất\": \"1800W\"}', NULL, 4030000.00, 4690000.00, 86, 'CON_HANG'),
(419, 64, 'AUTO-P64-V3', 'Lock&Lock Nồi chiên 4L - Ban 3', 'Vang', '{\"Dung tích\": \"4L\", \"Công suất\": \"1400W\"}', NULL, 1720000.00, 2190000.00, 147, 'CON_HANG'),
(420, 64, 'AUTO-P64-V4', 'Lock&Lock Nồi chiên 4L - Ban 4', 'Den', '{\"Dung tích\": \"4L\", \"Công suất\": \"1400W\"}', NULL, 1730000.00, 2190000.00, 146, 'CON_HANG'),
(421, 65, 'AUTO-P65-V3', 'HP LaserJet M140w - Ban 3', 'Trang', '{\"Loại\": \"Laser đen trắng\", \"Tốc độ\": \"18 trang/phút\"}', NULL, 3520000.00, 3990000.00, 37, 'CON_HANG'),
(422, 65, 'AUTO-P65-V4', 'HP LaserJet M140w - Ban 4', 'Bac', '{\"Loại\": \"Laser đen trắng\", \"Tốc độ\": \"18 trang/phút\"}', NULL, 3530000.00, 3990000.00, 36, 'CON_HANG'),
(423, 66, 'AUTO-P66-V3', 'Canon Pixma G670 - Ban 3', 'Hong', '{\"Loại\": \"Ink tank\", \"Chức năng\": \"In ảnh, scan\"}', NULL, 7020000.00, 7990000.00, 32, 'CON_HANG'),
(424, 66, 'AUTO-P66-V4', 'Canon Pixma G670 - Ban 4', 'Vang', '{\"Loại\": \"Ink tank\", \"Chức năng\": \"In ảnh, scan\"}', NULL, 7030000.00, 7990000.00, 31, 'CON_HANG'),
(425, 67, 'AUTO-P67-V3', 'Kangaroo KG512 - Ban 3', 'Tim', '{\"Dung tích bình\": \"6L\", \"Chức năng\": \"Nóng, lạnh, thường\"}', NULL, 3720000.00, 4390000.00, 47, 'CON_HANG'),
(426, 67, 'AUTO-P67-V4', 'Kangaroo KG512 - Ban 4', 'Trang', '{\"Dung tích bình\": \"6L\", \"Chức năng\": \"Nóng, lạnh, thường\"}', NULL, 3730000.00, 4390000.00, 46, 'CON_HANG'),
(427, 68, 'AUTO-P68-V3', 'Sunhouse SHD535 - Ban 3', 'Xanh duong', '{\"Làm lạnh\": \"8°C\", \"Làm nóng\": \"95°C\"}', NULL, 3020000.00, 3490000.00, 57, 'CON_HANG'),
(428, 68, 'AUTO-P68-V4', 'Sunhouse SHD535 - Ban 4', 'Hong', '{\"Làm lạnh\": \"8°C\", \"Làm nóng\": \"95°C\"}', NULL, 3030000.00, 3490000.00, 56, 'CON_HANG'),
(429, 69, 'AUTO-P69-V3', 'Omron JPN2 - Ban 3', 'Xam', '{\"Loại\": \"Bắp tay\", \"Bộ nhớ\": \"90 lần\"}', NULL, 1620000.00, 1890000.00, 77, 'CON_HANG'),
(430, 69, 'AUTO-P69-V4', 'Omron JPN2 - Ban 4', 'Tim', '{\"Loại\": \"Bắp tay\", \"Bộ nhớ\": \"90 lần\"}', NULL, 1630000.00, 1890000.00, 76, 'CON_HANG'),
(431, 70, 'AUTO-P70-V3', 'Xiaomi Cân thông minh 2 Pro - Ban 3', 'Den', '{\"Chỉ số\": \"15 chỉ số\", \"Kết nối\": \"Bluetooth\"}', NULL, 729000.00, 929000.00, 177, 'CON_HANG'),
(432, 70, 'AUTO-P70-V4', 'Xiaomi Cân thông minh 2 Pro - Ban 4', 'Xanh duong', '{\"Chỉ số\": \"15 chỉ số\", \"Kết nối\": \"Bluetooth\"}', NULL, 739000.00, 939000.00, 176, 'CON_HANG'),
(433, 71, 'AUTO-P71-V3', 'Panasonic Massage cầm tay EW-RA38 - Ban 3', 'Bac', '{\"Công suất\": \"15W\", \"Đầu đấm\": \"4\"}', NULL, 1520000.00, 1790000.00, 77, 'CON_HANG'),
(434, 71, 'AUTO-P71-V4', 'Panasonic Massage cầm tay EW-RA38 - Ban 4', 'Xam', '{\"Công suất\": \"15W\", \"Đầu đấm\": \"4\"}', NULL, 1530000.00, 1790000.00, 76, 'CON_HANG'),
(435, 72, 'AUTO-P72-V3', 'Cuckoo Gối massage toàn thân - Ban 3', 'Vang', '{\"Chức năng\": \"Rung nhiệt 8 điểm\", \"Điều khiển\": \"Remote\"}', NULL, 1329000.00, 1599000.00, 87, 'CON_HANG'),
(436, 72, 'AUTO-P72-V4', 'Cuckoo Gối massage toàn thân - Ban 4', 'Den', '{\"Chức năng\": \"Rung nhiệt 8 điểm\", \"Điều khiển\": \"Remote\"}', NULL, 1339000.00, 1599000.00, 86, 'CON_HANG'),
(437, 73, 'AUTO-P73-V3', 'Ariston 20L trực tiếp - Ban 3', 'Trang', '{\"Dung tích\": \"20L\", \"Công suất\": \"2500W\"}', NULL, 2420000.00, 2890000.00, 37, 'CON_HANG'),
(438, 73, 'AUTO-P73-V4', 'Ariston 20L trực tiếp - Ban 4', 'Bac', '{\"Dung tích\": \"20L\", \"Công suất\": \"2500W\"}', NULL, 2430000.00, 2890000.00, 36, 'CON_HANG'),
(439, 74, 'AUTO-P74-V3', 'Ferroli 40L gián tiếp - Ban 3', 'Hong', '{\"Dung tích\": \"40L\", \"Công suất\": \"2500W\"}', NULL, 3520000.00, 3990000.00, 27, 'CON_HANG'),
(440, 74, 'AUTO-P74-V4', 'Ferroli 40L gián tiếp - Ban 4', 'Vang', '{\"Dung tích\": \"40L\", \"Công suất\": \"2500W\"}', NULL, 3530000.00, 3990000.00, 26, 'CON_HANG'),
(441, 75, 'AUTO-P75-V3', 'Sharp 12L/ngày - Ban 3', 'Tim', '{\"Lượng hút\": \"12L/ngày\", \"Diện tích\": \"35m²\"}', NULL, 4520000.00, 4990000.00, 42, 'CON_HANG'),
(442, 75, 'AUTO-P75-V4', 'Sharp 12L/ngày - Ban 4', 'Trang', '{\"Lượng hút\": \"12L/ngày\", \"Diện tích\": \"35m²\"}', NULL, 4530000.00, 4990000.00, 41, 'CON_HANG'),
(443, 76, 'AUTO-P76-V3', 'Panasonic 20L/ngày - Ban 3', 'Xanh duong', '{\"Lượng hút\": \"20L/ngày\", \"Màn hình\": \"LCD\"}', NULL, 7020000.00, 7990000.00, 32, 'CON_HANG'),
(444, 76, 'AUTO-P76-V4', 'Panasonic 20L/ngày - Ban 4', 'Hong', '{\"Lượng hút\": \"20L/ngày\", \"Màn hình\": \"LCD\"}', NULL, 7030000.00, 7990000.00, 31, 'CON_HANG'),
(445, 77, 'AUTO-P77-V3', 'Giant Escape 3 - Ban 3', 'Xam', '{\"Khung\": \"Nhôm\", \"Phanh\": \"Vành\", \"Tốc độ\": \"18\"}', NULL, 7520000.00, 8990000.00, 27, 'CON_HANG'),
(446, 77, 'AUTO-P77-V4', 'Giant Escape 3 - Ban 4', 'Tim', '{\"Khung\": \"Nhôm\", \"Phanh\": \"Vành\", \"Tốc độ\": \"18\"}', NULL, 7530000.00, 8990000.00, 26, 'CON_HANG');
INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `thuoc_tinh_bien_the`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(447, 78, 'AUTO-P78-V3', 'Trek FX 2 - Ban 3', 'Den', '{\"Khung\": \"Nhôm Alpha\", \"Phanh\": \"Đĩa cơ\", \"Tốc độ\": \"18\"}', NULL, 11020000.00, 12990000.00, 22, 'CON_HANG'),
(448, 78, 'AUTO-P78-V4', 'Trek FX 2 - Ban 4', 'Xanh duong', '{\"Khung\": \"Nhôm Alpha\", \"Phanh\": \"Đĩa cơ\", \"Tốc độ\": \"18\"}', NULL, 11030000.00, 12990000.00, 21, 'CON_HANG'),
(449, 79, 'AUTO-P79-V3', 'JBL Flip 6 - Ban 3', 'Bac', '{\"Công suất\": \"30W\", \"Chống nước\": \"IP67\", \"Pin\": \"12h\"}', NULL, 2620000.00, 2990000.00, 137, 'CON_HANG'),
(450, 79, 'AUTO-P79-V4', 'JBL Flip 6 - Ban 4', 'Xam', '{\"Công suất\": \"30W\", \"Chống nước\": \"IP67\", \"Pin\": \"12h\"}', NULL, 2630000.00, 2990000.00, 136, 'CON_HANG'),
(451, 80, 'AUTO-P80-V3', 'Sony SRS-XE300 - Ban 3', 'Vang', '{\"Công suất\": \"20W\", \"Pin\": \"24h\", \"Chống nước\": \"IP67\"}', NULL, 3020000.00, 3490000.00, 107, 'CON_HANG'),
(452, 80, 'AUTO-P80-V4', 'Sony SRS-XE300 - Ban 4', 'Den', '{\"Công suất\": \"20W\", \"Pin\": \"24h\", \"Chống nước\": \"IP67\"}', NULL, 3030000.00, 3490000.00, 106, 'CON_HANG'),
(459, 89, '00924620', 'Honor X5c Plus 4GB 64GB', 'Xanh', NULL, NULL, 3090000.00, 3190000.00, 96, 'CON_HANG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `refund`
--

CREATE TABLE `refund` (
  `id` int NOT NULL,
  `thanh_toan_id` int NOT NULL COMMENT 'ID giao dịch thanh toán gốc',
  `gateway_refund_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID hoàn tiền từ cổng thanh toán',
  `amount` decimal(15,2) NOT NULL COMMENT 'Số tiền hoàn',
  `status` enum('PENDING','COMPLETED','FAILED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Lý do hoàn tiền',
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
  `ten_san_pham` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL: iphone-16-pro-max',
  `hang_san_xuat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Apple, Samsung, Xiaomi...',
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `gia_hien_thi` decimal(15,2) DEFAULT NULL COMMENT 'Giá "từ" hiển thị (giá thấp nhất phiên bản)',
  `diem_danh_gia` float DEFAULT '0',
  `trang_thai` enum('CON_BAN','NGUNG_BAN','SAP_RA_MAT','HET_HANG') COLLATE utf8mb4_unicode_ci DEFAULT 'CON_BAN',
  `noi_bat` tinyint(1) DEFAULT '0' COMMENT '1 = hiện trên banner/trang chủ',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten_san_pham`, `slug`, `hang_san_xuat`, `mo_ta`, `gia_hien_thi`, `diem_danh_gia`, `trang_thai`, `noi_bat`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'IPhone', 'iphone', 'Apple', 'Smartphone', 999.00, 0, 'NGUNG_BAN', 0, '2026-03-30 20:51:28', '2026-04-17 15:50:49'),
(2, 1, 'Điện thoại Samsung', 'sam-sung', 'Samsung', 'Điện thoại thông minh', NULL, 5, 'CON_BAN', 0, '2026-04-02 14:47:33', '2026-04-16 20:51:50'),
(7, 1, 'iPhone 15', 'iphone-15', 'Apple', 'Với iPhone 15, bạn sẽ được tận hưởng những trải nghiệm cao cấp trên một thiết bị bền bỉ và thanh lịch. Sản phẩm gây ấn tượng với màn hình Dynamic Island, camera độ phân giải siêu cao cùng nhiều chế độ quay chụp xuất sắc. Nhờ cổng USB-C, trải nghiệm kết nối của iPhone 15 thực sự khác biệt so với các thế hệ trước.', 34990000.00, 5, 'CON_BAN', 1, '2026-04-04 09:59:16', '2026-04-14 20:02:56'),
(8, 3, 'Laptop Lenovo ThinkPad X1 Carbon Gen 13 U7 258V/AI/32GB/1TB/14\"OLED 2.8K/W11PRO (21NS010JVN)', 'lenovo-thinkpad-x1-carbon-gen-13-u7-258v-21ns010jvn', 'Lenovo', 'Lenovo ThinkPad X1 Carbon Gen 13 là chiếc laptop doanh nhân cao cấp dành cho những người cần một thiết bị vừa mạnh mẽ, lại vừa siêu nhẹ để dễ dàng mang theo. Sở hữu bộ vi xử lý AI Intel Core Ultra 7 258V đầu bảng, màn hình OLED 14 inch sắc nét nhưng với thiết kế sợi Carbon, ThinkPad X1 Carbon Gen 13 chỉ có trọng lượng vỏn vẹn 1 kg, cho khả năng di động bậc nhất hiện nay.', NULL, 0, 'NGUNG_BAN', 1, '2026-04-06 20:04:49', '2026-04-07 09:42:55'),
(9, 6, 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka', 'op-lung-magsafe-samsung-s26-ultra-ultra-slim-with-pitatap-moonrise-pitaka', 'Samsung', 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka là sự kết hợp giữa nghệ thuật chế tác và công nghệ tối ưu trải nghiệm. Thiết kế Moonrise nổi bật với hiệu ứng chuyển sắc độc đáo trên nền sợi Aramid cao cấp. Sản phẩm ôm sát thân máy, duy trì vẻ nguyên bản của Galaxy S26 Ultra. Đồng thời, PitaTap cùng Aaron Button mở ra cách tương tác hoàn toàn mới, nhanh gọn và chính xác.', NULL, 0, 'NGUNG_BAN', 0, '2026-04-06 20:10:35', '2026-04-07 09:42:57'),
(10, 10, 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP', 'comfee-inverter-1-hp-cfs-10vgpf', 'Inverter', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGPF hỗ trợ làm lạnh hiệu quả và mang lại sự tiện lợi cho người dùng. Với công suất 1 HP, thiết bị này phù hợp với các căn phòng có diện tích dưới 15m². Ngoài thiết kế tinh tế, sang trọng, máy còn tích hợp nhiều tính năng thông minh như kết nối với hệ sinh thái nhà thông minh, điều khiển bằng giọng nói và các chế độ tiết kiệm điện hiệu quả', NULL, 5, 'CON_BAN', 0, '2026-04-08 19:10:33', '2026-04-16 22:45:23'),
(11, 15, 'Máy lọc nước nóng lạnh RO Hydrogen Kangaroo KG11A6 11 lõi', 'may-loc-nuoc-nong-lanh-ro-hydrogen-kangaroo-11-loi-kg11a6', 'Kangaroo', 'Máy lọc nước Kangaroo Hydrogen nóng lạnh KG11A6 là dòng máy lọc nước vừa ra mắt trong năm 2024 thuộc thương hiệu Kangaroo. Do đó, những tinh hoa công nghệ trong việc đầu tư và thiết kế hệ thống siêu lõi lọc làm tăng hiệu năng lọc nước hơn bao giờ hết, không chỉ loại bỏ chất bẩn mà còn bù khoáng cho cơ thể', NULL, 0, 'CON_BAN', 0, '2026-04-08 21:30:56', '2026-04-08 21:30:56'),
(12, 9, 'Xiaomi Google TV QLED 32 inch HD A Pro 2026 L32MB-APSEA', 'xiaomi-google-tivi-l-mb-apsea', 'Xiaomi', 'Xiaomi Google Tivi L MB-APSEA là mẫu tivi nhỏ gọn, phù hợp với nhiều không gian sống. Thiết bị này được trang bị công nghệ QLED tiên tiến, tái hiện màu sắc sống động và trung thực. Bên cạnh đó, thiết kế kim loại tinh tế cùng giao diện Google TV thân thiện hứa hẹn mang lại trải nghiệm giải trí thuận tiện cho mọi thành viên trong gia đình.\r\n\r\nCông nghệ QLED tái hiện màu sắc sống động\r\nXiaomi Google Tivi L MB-APSEA sử dụng màn hình QLED với dải màu rộng và khả năng điều chỉnh chính xác, nhờ vậy mỗi khung hình hiện lên đều rực rỡ và chân thật. Công nghệ này còn đáp ứng chuẩn màu DCI-P3 vốn được ứng dụng trong ngành điện ảnh Hollywood, đem đến những gam màu sống động và cuốn hút. Với khả năng hiển thị 16,7 triệu màu cùng độ phủ màu DCI-P3 đạt 90%, từng chi tiết nhỏ đều được thể hiện sắc nét và ấn tượng.', NULL, 0, 'CON_BAN', 1, '2026-04-16 08:12:58', '2026-04-16 08:12:58'),
(13, 6, 'Củ sạc nhanh 1 cổng 25W USB-C PPS Wall Charger Belkin', 'cu-sac-nhanh-1-cong-25w-usb-c-pps-wall-charger-belkin', 'Belkin', 'Củ sạc nhanh 1 cổng 25 W USB-C PPS Wall Charger Belkin là một giải pháp sạc nhanh đáng tin cậy, tối ưu cho các thiết bị như iPhone, Samsung và những thiết bị hỗ trợ USB-C PD. Công suất 25W rút ngắn đáng kể thời gian nạp pin, mang lại trải nghiệm sử dụng liền mạch và ổn định. Thiết kế nhỏ gọn và tính tương thích cao giúp củ sạc này trở thành một người bạn đồng hành lý tưởng trong các chuyến đi xa.\r\nCủ sạc Belkin 25 W USB-C PPS Wall Charger được thiết kế để tương thích với iPhone, Samsung và nhiều thiết bị hỗ trợ chuẩn USB-C PD, giúp bạn sử dụng một củ sạc duy nhất cho tất cả các thiết bị này. Với khả năng sạc nhanh, bạn sẽ không phải lo lắng về việc tìm kiếm các bộ sạc khác nhau nữa. Dù là iPhone hay Samsung, sản phẩm này đều mang đến một giải pháp sạc đơn giản và hiệu quả cho mọi nhu cầu.', NULL, 5, 'CON_BAN', 1, '2026-04-16 10:23:11', '2026-04-16 21:15:15'),
(14, 1, 'iPhone 16 Pro Max 256GB', 'iphone-16-pro-max-256gb', 'Apple', 'Màn hình 6.9 inch Super Retina XDR, chip A18 Pro, camera 48MP, pin trâu.', 34990000.00, 5, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(15, 1, 'Samsung Galaxy S25 Ultra', 'samsung-galaxy-s25-ultra', 'Samsung', 'Màn hình 6.8 inch Dynamic AMOLED 2X, chip Snapdragon 8 Gen 4, camera 200MP, S Pen tích hợp.', 29990000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(16, 1, 'Xiaomi 15 Pro', 'xiaomi-15-pro', 'Xiaomi', 'Màn hình 6.73 inch AMOLED, chip Snapdragon 8 Gen 4, pin 6000mAh, sạc nhanh 120W.', 18990000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(17, 2, 'iPad Pro 13 inch M4', 'ipad-pro-13-inch-m4', 'Apple', 'Màn hình Ultra Retina XDR, chip M4, hỗ trợ Apple Pencil Pro, Face ID.', 29990000.00, 5, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(18, 2, 'Samsung Galaxy Tab S10 Ultra', 'samsung-galaxy-tab-s10-ultra', 'Samsung', 'Màn hình 14.6 inch Dynamic AMOLED, chip Snapdragon 8 Gen 3, S Pen đi kèm.', 24990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(19, 3, 'MacBook Pro 14 inch M3 Pro', 'macbook-pro-14-inch-m3-pro', 'Apple', 'Chip M3 Pro 12 nhân, RAM 18GB, SSD 512GB, màn hình Liquid Retina XDR.', 39990000.00, 4.9, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(20, 3, 'Dell XPS 15', 'dell-xps-15', 'Dell', 'Intel Core i9-13900H, RAM 32GB, SSD 1TB, RTX 4060, màn hình 15.6 inch 4K OLED.', 45990000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(21, 4, 'LG UltraFine 27 inch 4K', 'lg-ultrafine-27-inch-4k', 'LG', 'Màn hình 27 inch 4K IPS, độ phủ màu 99% DCI-P3, kết nối Thunderbolt 3.', 12990000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(22, 4, 'Samsung Odyssey G7 32 inch 240Hz', 'samsung-odyssey-g7-32-inch-240hz', 'Samsung', 'Màn hình cong 32 inch, QLED, 240Hz, 1ms, G-Sync compatible.', 15990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(23, 5, 'Dell OptiPlex 7010 Plus', 'dell-optiplex-7010-plus', 'Dell', 'Intel Core i7-13700, RAM 16GB, SSD 512GB, Windows 11 Pro.', 18990000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(24, 5, 'HP EliteDesk 800 G9', 'hp-elitedesk-800-g9', 'HP', 'Intel Core i9-13900, RAM 32GB, SSD 1TB, đồ họa Intel UHD.', 27990000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(25, 6, 'Chuột không dây Logitech MX Master 3S', 'chuot-khong-day-logitech-mx-master-3s', 'Logitech', 'Chuột cảm ứng, 8000 DPI, kết nối Bluetooth và USB, pin 70 ngày.', 1999000.00, 4.9, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(26, 6, 'Bàn phím cơ Razer BlackWidow V4 Pro', 'ban-phim-co-razer-blackwidow-v4-pro', 'Razer', 'Switch xanh, đèn RGB Chroma, phím điều khiển đa phương tiện.', 3999000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(27, 7, 'Sim FPT Data 4G 5GB/ngày', 'sim-fpt-data-4g-5gb-ngay', 'FPT', 'Sim data 4G tốc độ cao, 5GB/ngày, 30 ngày sử dụng, không gọi SMS.', 99000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(28, 7, 'Sim FPT trọn gói 3 tháng', 'sim-fpt-tron-goi-3-thang', 'FPT', 'Gọi nội mạng 3000 phút, data 30GB/tháng, 3 tháng.', 299000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:04', '2026-04-17 15:02:04'),
(29, 8, 'Apple Watch Series 9 45mm', 'apple-watch-series-9-45mm', 'Apple', 'Màn hình Always-On, chip S9, đo SPO2, ECG, GPS.', 10990000.00, 4.9, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(30, 8, 'Samsung Galaxy Watch 7', 'samsung-galaxy-watch-7', 'Samsung', 'Màn hình 1.5 inch Super AMOLED, pin 425mAh, theo dõi sức khỏe toàn diện.', 6990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(31, 9, 'Samsung QLED 4K 55 inch Q80D', 'samsung-qled-4k-55-inch-q80d', 'Samsung', 'QLED 4K, 120Hz, hỗ trợ HDR10+, Tizen OS.', 14990000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(32, 9, 'LG OLED C3 65 inch', 'lg-oled-c3-65-inch', 'LG', 'OLED evo, 4K, 120Hz, Dolby Vision, webOS 23.', 29990000.00, 4.9, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(33, 10, 'Daikin Inverter 1.5 HP FTKM35', 'daikin-inverter-1-5hp-ftkm35', 'Daikin', 'Tiết kiệm điện, lọc bụi, làm lạnh nhanh, gas R32.', 12990000.00, 4.7, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(34, 10, 'Panasonic Inverter 2.0 HP', 'panasonic-inverter-2hp', 'Panasonic', 'Công nghệ nanoe X, làm sạch không khí, vận hành êm.', 16990000.00, 4.8, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(35, 11, 'Robot hút bụi lau nhà Xiaomi X10+', 'robot-xiaomi-x10-plus', 'Xiaomi', 'Công suất hút 4000Pa, tự động lau, bản đồ AI, trạm tự làm sạch.', 9990000.00, 4.6, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(36, 11, 'Ecovacs Deebot T20 Omni', 'ecovacs-deebot-t20-omni', 'Ecovacs', 'Hút lau 5000Pa, lau nước nóng, tự giặt hơi nóng, tránh chướng ngại vật thông minh.', 18990000.00, 4.8, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(37, 12, 'Sunhouse Quạt điều hòa SHD7710', 'sunhouse-quat-dieu-hoa-shd7710', 'Sunhouse', 'Bình nước 6 lít, công suất 100W, gió mát tự nhiên, remote.', 1990000.00, 4.3, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(38, 12, 'Kangaroo Quạt hơi nước KG828', 'kangaroo-quat-hoi-nuoc-kg828', 'Kangaroo', 'Dung tích 5 lít, 3 chế độ gió, hẹn giờ 7.5h, an toàn cho trẻ em.', 1690000.00, 4.2, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(39, 13, 'LG Máy giặt Inverter 9kg', 'lg-may-giat-inverter-9kg', 'LG', 'Công nghệ AI DD, giặt hơi nước, Inverter, 9kg.', 8490000.00, 4.7, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(40, 13, 'Electrolux Máy giặt cửa trước 10kg', 'electrolux-may-giat-cua-truoc-10kg', 'Electrolux', 'Công nghệ UltraMix, Inverter, 10kg, tiết kiệm điện.', 10990000.00, 4.8, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(41, 14, 'Samsung Tủ lạnh Inverter 400L', 'samsung-tu-lanh-inverter-400l', 'Samsung', 'Công nghệ SpaceMax, Digital Inverter, 400 lít, tiết kiệm điện.', 13990000.00, 4.6, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(42, 14, 'Hitachi Tủ lạnh Inverter 450L', 'hitachi-tu-lanh-inverter-450l', 'Hitachi', 'Ngăn đá mềm, Inverter, 450 lít, kháng khuẩn.', 16990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(43, 15, 'Kangaroo Máy lọc nước RO 11 lõi', 'kangaroo-may-loc-nuoc-ro-11-loi', 'Kangaroo', 'Công nghệ RO Hydrogen, 11 lõi, nước nóng lạnh, bình 4 lít.', 8990000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(44, 15, 'Mutosi Máy lọc nước RO 9 lõi', 'mutosi-may-loc-nuoc-ro-9-loi', 'Mutosi', 'Lọc 9 cấp, bổ sung khoáng, vỏ inox, dung tích 3.5 lít.', 6990000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(45, 16, 'iPhone 12 64GB Like new', 'iphone-12-64gb-like-new', 'Apple', 'Máy chính hãng, pin 85%, bảo hành 6 tháng, full box.', 7990000.00, 4.3, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(46, 16, 'MacBook Air 2017 13 inch', 'macbook-air-2017-13-inch', 'Apple', 'Core i5, RAM 8GB, SSD 128GB, ngoại hình đẹp, pin tốt.', 6990000.00, 4, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(47, 26, 'Electrolux Máy sấy bơm nhiệt 8kg', 'electrolux-may-say-bom-nhiet-8kg', 'Electrolux', 'Công nghệ bơm nhiệt, 8kg, chống nhăn, kháng khuẩn.', 13990000.00, 4.7, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(48, 26, 'Beko Máy sấy thông hơi 9kg', 'beko-may-say-thong-hoi-9kg', 'Beko', 'Sấy nhanh, 9kg, 3 chế độ sấy, thiết kế sang trọng.', 9990000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(49, 27, 'Xiaomi Camera an ninh 2K', 'xiaomi-camera-an-ninh-2k', 'Xiaomi', 'Độ phân giải 2K, góc rộng 130°, hồng ngoại, nghe nói hai chiều.', 699000.00, 4.6, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(50, 27, 'TP-Link Tapo C320WS', 'tp-link-tapo-c320ws', 'TP-Link', '4MP, ngoài trời, IP66, đèn LED màu, lưu trữ tối đa 256GB.', 999000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(51, 28, 'Philips Bàn ủi hơi nước đứng', 'philips-ban-ui-hoi-nuoc-dung', 'Philips', 'Công suất 1500W, bình chứa 1.5 lít, chống nhỏ giọt.', 1990000.00, 4.4, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(52, 28, 'Panasonic Lò vi sóng 20L', 'panasonic-lo-vi-song-20l', 'Panasonic', '20 lít, 800W, nấu rã đông, phím bấm dễ dùng.', 2390000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(53, 29, 'Senko Quạt sạc mini', 'senko-quat-sac-mini', 'Senko', 'Quạt cầm tay, pin 4000mAh, 3 tốc độ, nhỏ gọn.', 199000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(54, 29, 'Mitsubishi Quạt đứng 5 cánh', 'mitsubishi-quat-dung-5-canh', 'Mitsubishi', 'Công suất 55W, 5 cánh, 3 cấp độ, điều khiển từ xa.', 799000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(55, 30, 'Xiaomi Máy lọc không khí 4 Pro', 'xiaomi-may-loc-khong-khi-4-pro', 'Xiaomi', 'CADR 500m³/h, lọc HEPA, kháng khuẩn, diện tích 60m².', 3990000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(56, 30, 'Coway Máy lọc không khí AP-1512HH', 'coway-may-loc-khong-khi-ap-1512hh', 'Coway', 'Lọc 4 cấp, cảm biến chất lượng không khí, chế độ ngủ êm.', 5990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(57, 31, 'Sunhouse Bếp từ đôi', 'sunhouse-bep-tu-doi', 'Sunhouse', '2 vùng nấu, công suất 2000W, cảm ứng, an toàn.', 1690000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(58, 31, 'Tefal Bếp hồng ngoại', 'tefal-bep-hong-ngoai', 'Tefal', 'Công suất 2000W, mặt kính chịu nhiệt, dễ vệ sinh.', 1490000.00, 4.4, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(59, 32, 'Sharp Nồi cơm điện 1.8 lít', 'sharp-noi-com-dien-1-8-lit', 'Sharp', 'Lòng chống dính, nấu cơm, nấu cháo, hẹn giờ, giữ ấm.', 799000.00, 4.3, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(60, 32, 'Panasonic Nồi cơm điện cao tần', 'panasonic-noi-com-dien-cao-tan', 'Panasonic', 'Công nghệ cao tần, 1.0 lít, lòng nồi kim loại, nhiều chế độ.', 2590000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(61, 33, 'Philips Máy xay sinh tố 1.5L', 'philips-may-xay-sinh-to-1-5l', 'Philips', 'Công suất 800W, cối thủy tinh, 2 tốc độ + turbo.', 1290000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(62, 33, 'Kangaroo Máy ép chậm', 'kangaroo-may-ep-cham', 'Kangaroo', 'Ép trái cây chậm, giữ nguyên vitamin, dễ vệ sinh.', 2590000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(63, 34, 'Philips Nồi chiên không dầu 4.5L', 'philips-noi-chien-khong-dau-4-5l', 'Philips', 'Công nghệ Rapid Air, 4.5 lít, 1400W, màn hình cảm ứng.', 3290000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(64, 34, 'Lock&Lock Nồi chiên không dầu 5L', 'locklock-noi-chien-khong-dau-5l', 'Lock&Lock', '1500W, 5 lít, hẹn giờ 30 phút, dễ lau chùi.', 1990000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(65, 35, 'HP LaserJet M141w', 'hp-laserjet-m141w', 'HP', 'In đen trắng, đa năng in-scan-copy, kết nối Wi-Fi, tốc độ 20 trang/phút.', 3990000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(66, 35, 'Canon Pixma G570', 'canon-pixma-g570', 'Canon', 'In màu ảnh, hệ thống bình mực rời, in không viền.', 5990000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(67, 36, 'Kangaroo Cây nước nóng lạnh KG511', 'kangaroo-cay-nuoc-nong-lanh-kg511', 'Kangaroo', 'Cấp nước tự động, nóng lạnh thường, an toàn trẻ em.', 3290000.00, 4.4, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(68, 36, 'Sunhouse Cây nước nóng lạnh SHD532', 'sunhouse-cay-nuoc-nong-lanh-shd532', 'Sunhouse', 'Thiết kế nhỏ gọn, làm lạnh 10°C, nóng 90°C.', 2590000.00, 4.3, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(69, 37, 'Omron Máy đo huyết áp JPN1', 'omron-may-do-huyet-ap-jpn1', 'Omron', 'Đo cổ tay, lưu 60 lần nhớ, phát hiện rối loạn nhịp tim.', 1390000.00, 4.7, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(70, 37, 'Xiaomi Cân điện tử thông minh 2', 'xiaomi-can-dien-tu-thong-minh-2', 'Xiaomi', 'Đo 13 chỉ số, kết nối Mi Fit, thiết kế kính cường lực.', 499000.00, 4.6, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(71, 38, 'Panasonic Máy massage cầm tay', 'panasonic-may-massage-cam-tay', 'Panasonic', 'Massage toàn thân, 4 đầu đấm, 3 tốc độ, nhỏ gọn.', 1290000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(72, 38, 'Cuckoo Gối massage cổ vai gáy', 'cuckoo-goi-massage-co-vai-gay', 'Cuckoo', 'Rung nhiệt, 8 mô phỏng, điều khiển từ xa.', 899000.00, 4.4, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(73, 39, 'Ariston Máy nước nóng trực tiếp 15L', 'ariston-may-nuoc-nong-truc-tiep-15l', 'Ariston', 'Thanh đốt Titan, chống giật, 15 lít, bảo hành 2 năm.', 1990000.00, 4.6, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(74, 39, 'Ferroli Máy nước nóng gián tiếp 30L', 'ferroli-may-nuoc-nong-gian-tiep-30l', 'Ferroli', 'Công suất 2500W, 30 lít, điều khiển cơ, an toàn.', 2890000.00, 4.5, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(75, 40, 'Sharp Máy hút ẩm 10L', 'sharp-may-hut-am-10l', 'Sharp', 'Hút ẩm 10L/ngày, diện tích 30m², lọc bụi, chế độ thông minh.', 3990000.00, 4.5, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(76, 40, 'Panasonic Máy hút ẩm 16L', 'panasonic-may-hut-am-16l', 'Panasonic', 'Hút ẩm 16L/ngày, cảm biến độ ẩm, màn hình LCD.', 5990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(77, 41, 'Giant Xe đạp địa hình Escape 2', 'giant-xe-dap-dia-hinh-escape-2', 'Giant', 'Khung nhôm, phanh đĩa cơ, 21 tốc độ, lốp 700x38c.', 8490000.00, 4.6, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(78, 41, 'Trek Xe đạp thể thao FX 3', 'trek-xe-dap-the-thao-fx-3', 'Trek', 'Khung nhôm Alpha, phanh đĩa thủy lực, 24 tốc độ, trọng lượng nhẹ.', 12990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(79, 42, 'JBL Flip 6', 'jbl-flip-6', 'JBL', 'Loa Bluetooth 30W, IP67, pin 12 giờ, âm thanh mạnh mẽ.', 2590000.00, 4.8, 'CON_BAN', 1, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(80, 42, 'Sony SRS-XE300', 'sony-srs-xe300', 'Sony', 'Loa chống bụi nước IP67, pin 24 giờ, sạc nhanh, Line-out.', 2990000.00, 4.7, 'CON_BAN', 0, '2026-04-17 15:02:05', '2026-04-17 15:02:05'),
(89, 1, 'Honor X5c Plus 4GB 64GB', 'honor-x5c-plus', 'HONOR', 'HONOR X5c Plus là mẫu smartphone giá tốt dành cho người dùng cần một thiết bị ổn định. Sản phẩm ghi điểm nhờ thời lượng pin bền bỉ, màn hình lớn với tần số quét cao. Máy sở hữu viên pin dung lượng cao 5.260mAh, 4GB RAM, bộ nhớ trong 64GB, màn hình lớn 6.74 inch cùng camera chính 50MP, đáp ứng trọn vẹn các tác vụ học tập, giải trí và liên lạc hằng ngày.', NULL, 0, 'CON_BAN', 1, '2026-04-17 20:02:02', '2026-04-17 20:02:02');

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
  `gateway_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo) for idempotency (Req 8.5)',
  `gateway_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment gateway identifier (VNPAY, MOMO, COD)',
  `expiration_time` datetime DEFAULT NULL COMMENT 'Transaction expiration timestamp (15 minutes from creation) (Req 6.1)',
  `payment_url` text COLLATE utf8mb4_unicode_ci COMMENT 'Payment URL generated by gateway for customer redirect',
  `error_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Error code from payment gateway (Req 7.6)',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'User-friendly error message in Vietnamese (Req 7.6)',
  `phuong_thuc` enum('COD','CHUYEN_KHOAN','QR','TRA_GOP','VI_DIEN_TU','ZALOPAY','VIETQR','PAYPAL') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `anh_bien_lai` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL ảnh biên lai chuyển khoản',
  `trang_thai_duyet` enum('CHO_DUYET','THANH_CONG','THAT_BAI','HOAN_TIEN') COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `ghi_chu_duyet` text COLLATE utf8mb4_unicode_ci COMMENT 'Admin ghi chú khi duyệt',
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
(41, 42, 4, NULL, 'VNPAY', '2026-04-16 20:11:47', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=27000000&vnp_Command=pay&vnp_CreateDate=20260417005647&vnp_CurrCode=VND&vnp_ExpireDate=20260417011147&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+42&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=41&vnp_Version=2.1.0&vnp_SecureHash=f317405f8a83c603ca2d0398e0557ea4ca00b06e569c0e616a31e5c3d22c52598965d3e559d03943285497f408cc8eecf448cb3a11c82d2dc077dbd11f58c67d', NULL, NULL, 'CHUYEN_KHOAN', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 19:56:47', '2026-04-16 20:00:45'),
(42, 43, 4, NULL, 'VIETQR', '2026-04-16 21:08:09', 'https://img.vietqr.io/image/VTB-104882641761-compact2.png?amount=270000&addInfo=DH43&accountName=TRUONG+THANH+DAT', NULL, NULL, 'VIETQR', 270000.00, NULL, 'THAT_BAI', NULL, '2026-04-16 20:53:09', '2026-04-16 20:56:55'),
(43, 44, 4, NULL, 'VIETQR', '2026-04-16 21:09:24', 'https://img.vietqr.io/image/VTB-104882641761-compact2.png?amount=270000&addInfo=DH44&accountName=TRUONG+THANH+DAT', NULL, NULL, 'VIETQR', 270000.00, NULL, 'THAT_BAI', NULL, '2026-04-16 20:54:24', '2026-04-16 20:56:47'),
(44, 45, 4, NULL, 'VIETQR', '2026-04-16 21:15:38', 'https://img.vietqr.io/image/ICB-104882641761-compact2.png?amount=270000&addInfo=DH45&accountName=TRUONG+THANH+DAT', NULL, NULL, 'VIETQR', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 21:00:38', '2026-04-16 21:08:07'),
(45, 46, NULL, NULL, 'PAYPAL', '2026-04-16 21:40:57', NULL, NULL, NULL, 'PAYPAL', 8820000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:25:57', NULL),
(46, 47, NULL, NULL, 'PAYPAL', '2026-04-16 21:42:51', NULL, NULL, NULL, 'PAYPAL', 9829999.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:27:51', NULL),
(47, 48, NULL, NULL, 'VIETQR', '2026-04-16 21:44:21', 'https://img.vietqr.io/image/ICB-104882641761-compact2.png?amount=4020000&addInfo=DH48&accountName=TRUONG+THANH+DAT', NULL, NULL, 'VIETQR', 4020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:29:21', NULL),
(48, 49, NULL, NULL, 'PAYPAL', '2026-04-16 21:48:26', NULL, NULL, NULL, 'PAYPAL', 4020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:33:26', NULL),
(49, 50, NULL, NULL, 'PAYPAL', '2026-04-16 21:49:27', NULL, NULL, NULL, 'PAYPAL', 4220000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:34:27', NULL),
(50, 51, NULL, NULL, 'PAYPAL', '2026-04-16 21:50:54', NULL, NULL, NULL, 'PAYPAL', 9020000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:35:54', NULL),
(51, 52, NULL, NULL, 'PAYPAL', '2026-04-16 21:51:54', NULL, NULL, NULL, 'PAYPAL', 470000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:36:54', NULL),
(52, 53, NULL, NULL, 'PAYPAL', '2026-04-16 21:54:51', NULL, NULL, NULL, 'PAYPAL', 270000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:39:51', NULL),
(53, 54, NULL, '1GA732028T6890613', 'PAYPAL', '2026-04-16 21:58:00', 'https://www.sandbox.paypal.com/checkoutnow?token=1GA732028T6890613', NULL, NULL, 'PAYPAL', 270000.00, NULL, 'CHO_DUYET', NULL, '2026-04-16 21:43:00', NULL),
(54, 55, NULL, '6SE79415EK2134249', 'PAYPAL', '2026-04-16 22:00:48', 'https://www.sandbox.paypal.com/checkoutnow?token=1BJ15454YS758752J', NULL, NULL, 'PAYPAL', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 21:49:49', NULL),
(55, 56, NULL, '18U72217FV8315940', 'PAYPAL', '2026-04-16 22:06:51', 'https://www.sandbox.paypal.com/checkoutnow?token=7LH31957U49675322', NULL, NULL, 'PAYPAL', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 21:52:06', NULL),
(56, 57, NULL, '2TR17181J83990820', 'PAYPAL', '2026-04-16 22:09:57', 'https://www.sandbox.paypal.com/checkoutnow?token=4N800447EJ768920X', NULL, NULL, 'PAYPAL', 4020000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 21:55:06', NULL),
(57, 58, NULL, '15500362', 'VNPAY', '2026-04-16 22:26:35', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=27000000&vnp_Command=pay&vnp_CreateDate=20260417031135&vnp_CurrCode=VND&vnp_ExpireDate=20260417032635&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+58&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=57&vnp_Version=2.1.0&vnp_SecureHash=97e78b15ffff75999deeccc55c16d76d6eebe0dfae7d0165ffe28d8d52bb27629bb8a96b38c7ff55cc5e4a240ba5f85f510a3b6f769056f8e2e57e0990809daf', NULL, NULL, 'CHUYEN_KHOAN', 270000.00, NULL, 'THANH_CONG', NULL, '2026-04-16 22:12:37', NULL),
(58, 60, NULL, NULL, 'COD', '2026-04-17 05:52:05', NULL, NULL, NULL, 'COD', 17720000.00, NULL, 'CHO_DUYET', NULL, '2026-04-17 05:37:05', NULL),
(59, 61, NULL, '15500646', 'VNPAY', '2026-04-17 05:54:11', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=3001700&vnp_Command=pay&vnp_CreateDate=20260417103911&vnp_CurrCode=VND&vnp_ExpireDate=20260417105411&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+61&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=59&vnp_Version=2.1.0&vnp_SecureHash=bc1d53b3e9fcf3148c171877f9058a1983dc6bc6dcb1e5bc39d72bb3969dae815790e1f7a1e9e654cc27b8f8fb417eb19f5500f2e6dbe096d3d85783f943c85c', NULL, NULL, 'CHUYEN_KHOAN', 30017.50, NULL, 'THANH_CONG', NULL, '2026-04-17 05:39:49', NULL),
(60, 62, NULL, '1V119822H2607164C', 'PAYPAL', '2026-04-17 05:57:44', 'https://www.sandbox.paypal.com/checkoutnow?token=5CJ39166NK2909350', NULL, NULL, 'PAYPAL', 30017.50, NULL, 'THANH_CONG', NULL, '2026-04-17 05:43:29', NULL),
(61, 63, NULL, NULL, 'VIETQR', '2026-04-17 05:59:07', 'https://img.vietqr.io/image/ICB-104882641761-compact2.png?amount=30017&addInfo=DH63&accountName=TRUONG+THANH+DAT', NULL, NULL, 'VIETQR', 30017.50, NULL, 'CHO_DUYET', NULL, '2026-04-17 05:44:07', NULL),
(62, 64, NULL, NULL, 'COD', '2026-04-17 07:19:20', NULL, NULL, NULL, 'COD', 20320000.00, NULL, 'CHO_DUYET', NULL, '2026-04-17 07:04:20', NULL),
(67, 69, NULL, NULL, 'VNPAY', '2026-04-17 15:20:41', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=292000000&vnp_Command=pay&vnp_CreateDate=20260417200541&vnp_CurrCode=VND&vnp_ExpireDate=20260417202041&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+69&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=67&vnp_Version=2.1.0&vnp_SecureHash=8724b1ab729d0bdbc64d3898f8e2bcb9343cfa484125ccb8a902a22d3798c60ed22a406b61c94f28763f5c91aaffd5d6c132fa432ad2738d52e5d39cfc95e81d', NULL, NULL, 'CHUYEN_KHOAN', 2920000.00, NULL, 'CHO_DUYET', NULL, '2026-04-17 15:05:41', NULL),
(68, 70, NULL, '15502855', 'VNPAY', '2026-04-19 02:21:55', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=1772000000&vnp_Command=pay&vnp_CreateDate=20260419070655&vnp_CurrCode=VND&vnp_ExpireDate=20260419072155&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+70&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=68&vnp_Version=2.1.0&vnp_SecureHash=1602b2fc21c336fe47ff18cf83516dee9b6e176590b6df086f226a961d4b841eff969d4ff91b7866ec3e4bf4ab49372d31a0cc9ade929f9928857ca5637f7fd5', NULL, NULL, 'CHUYEN_KHOAN', 17720000.00, NULL, 'THANH_CONG', NULL, '2026-04-19 02:09:01', NULL),
(69, 71, NULL, '9BC466995D0237906', 'PAYPAL', '2026-04-19 02:24:44', 'https://www.sandbox.paypal.com/checkoutnow?token=0040565310829082R', NULL, NULL, 'PAYPAL', 2920000.00, NULL, 'THANH_CONG', NULL, '2026-04-19 02:10:25', NULL),
(70, 72, NULL, '15502915', 'VNPAY', '2026-04-19 06:01:30', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=292000000&vnp_Command=pay&vnp_CreateDate=20260419104630&vnp_CurrCode=VND&vnp_ExpireDate=20260419110130&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+72&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fthanh-toan%2Freturn%2Fvnpay&vnp_TmnCode=NUIPDZDI&vnp_TxnRef=70&vnp_Version=2.1.0&vnp_SecureHash=60880b0627f4623b11590c84bb50be93b80dd60dd8bb6208a607420f717b88a2203d5d293d7f096f17fcaa85fb9952e422abde76d6b910777508fdbce80d6e1d', NULL, NULL, 'CHUYEN_KHOAN', 2920000.00, NULL, 'THANH_CONG', NULL, '2026-04-19 05:47:16', NULL),
(71, 73, NULL, '9BG16660ML468761A', 'PAYPAL', '2026-04-19 06:02:56', 'https://www.sandbox.paypal.com/checkoutnow?token=23X27472M1562170C', NULL, NULL, 'PAYPAL', 2920000.00, NULL, 'THANH_CONG', NULL, '2026-04-19 05:48:57', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_so_ky_thuat`
--

CREATE TABLE `thong_so_ky_thuat` (
  `id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `ten_thong_so` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ram, Chip, Pin, Màn hình...',
  `gia_tri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '8GB, A18 Pro, 5000mAh...',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_so_ky_thuat`
--

INSERT INTO `thong_so_ky_thuat` (`id`, `san_pham_id`, `ten_thong_so`, `gia_tri`, `thu_tu`) VALUES
(13, 7, 'Màn hình', '6.7 inch', 0),
(14, 1, 'Màn hình', '6.1 inch Retina HD', 1),
(15, 1, 'CPU', 'Apple A13 Bionic', 2),
(16, 1, 'RAM', '4GB', 3),
(17, 1, 'Camera sau', '12MP', 4),
(18, 2, 'Màn hình', '6.5 inch Super AMOLED', 1),
(19, 2, 'CPU', 'Exynos 1380', 2),
(20, 2, 'RAM', '8GB', 3),
(21, 2, 'Pin', '5000mAh', 4),
(22, 2, 'Camera chính', '50MP', 5),
(23, 7, 'CPU', 'A16 Bionic', 2),
(24, 7, 'RAM', '6GB', 3),
(25, 7, 'Pin', '3877mAh', 4),
(26, 7, 'Camera chính', '48MP', 5),
(27, 7, 'Cổng sạc', 'USB-C', 6),
(28, 8, 'CPU', 'Intel Core Ultra 7 258V', 1),
(29, 8, 'RAM', '32GB LPDDR5x', 2),
(30, 8, 'SSD', '1TB PCIe Gen4', 3),
(31, 8, 'Màn hình', '14 inch OLED 2.8K', 4),
(32, 8, 'Trọng lượng', '1.0 kg', 5),
(33, 9, 'Chất liệu', 'Sợi Aramid', 1),
(34, 9, 'Tương thích', 'Samsung S26 Ultra', 2),
(35, 9, 'Tính năng', 'Magsafe, PitaTap', 3),
(36, 10, 'Công suất', '1.5 HP - 12.000 BTU', 1),
(37, 10, 'Inverter', 'Có', 2),
(38, 10, 'Gas', 'R32', 3),
(39, 10, 'Độ ồn', '22 dB(A)', 4),
(40, 11, 'Số lõi lọc', '9 lõi', 1),
(41, 11, 'Công nghệ', 'RO Hydrogen', 2),
(42, 11, 'Dung tích bình', '4 lít', 3),
(43, 11, 'Tính năng', 'Nóng lạnh', 4),
(44, 12, 'Kích thước', '32 inch', 1),
(45, 12, 'Độ phân giải', 'HD (1366x768)', 2),
(46, 12, 'Tấm nền', 'QLED', 3),
(47, 12, 'Hệ điều hành', 'Google TV', 4),
(48, 13, 'Công suất', '25W', 1),
(49, 13, 'Cổng', '1x USB-C', 2),
(50, 13, 'Hỗ trợ', 'PD 3.0, PPS', 3),
(51, 14, 'Màn hình', '6.9 inch Super Retina XDR', 1),
(52, 14, 'CPU', 'A18 Pro', 2),
(53, 14, 'RAM', '8GB', 3),
(54, 14, 'Camera chính', '48MP + 12MP tele + 12MP ultrawide', 4),
(55, 14, 'Pin', '4676mAh', 5),
(56, 15, 'Màn hình', '6.8 inch Dynamic AMOLED 2X 120Hz', 1),
(57, 15, 'CPU', 'Snapdragon 8 Gen 4', 2),
(58, 15, 'RAM', '12GB', 3),
(59, 15, 'Camera chính', '200MP', 4),
(60, 15, 'Pin', '5000mAh', 5),
(61, 16, 'Màn hình', '6.73 inch AMOLED 120Hz', 1),
(62, 16, 'CPU', 'Snapdragon 8 Gen 4', 2),
(63, 16, 'RAM', '12GB', 3),
(64, 16, 'Camera chính', '50MP Leica', 4),
(65, 16, 'Pin', '6000mAh, sạc 120W', 5),
(66, 17, 'Màn hình', '13 inch Ultra Retina XDR', 1),
(67, 17, 'CPU', 'Apple M4', 2),
(68, 17, 'RAM', '8GB', 3),
(69, 17, 'Dung lượng', '256GB', 4),
(70, 17, 'Face ID', 'Có', 5),
(71, 18, 'Màn hình', '14.6 inch Dynamic AMOLED 2X', 1),
(72, 18, 'CPU', 'Snapdragon 8 Gen 3', 2),
(73, 18, 'RAM', '12GB', 3),
(74, 18, 'Dung lượng', '512GB', 4),
(75, 18, 'S Pen', 'Đi kèm', 5),
(76, 19, 'CPU', 'Apple M3 Pro (12 nhân)', 1),
(77, 19, 'RAM', '18GB', 2),
(78, 19, 'SSD', '512GB', 3),
(79, 19, 'Màn hình', '14.2 inch Liquid Retina XDR', 4),
(80, 19, 'Pin', 'Lên đến 18 giờ', 5),
(81, 20, 'CPU', 'Intel Core i9-13900H', 1),
(82, 20, 'RAM', '32GB DDR5', 2),
(83, 20, 'SSD', '1TB', 3),
(84, 20, 'GPU', 'NVIDIA RTX 4060', 4),
(85, 20, 'Màn hình', '15.6 inch OLED 4K', 5),
(86, 21, 'Kích thước', '27 inch', 1),
(87, 21, 'Độ phân giải', '4K UHD (3840x2160)', 2),
(88, 21, 'Tấm nền', 'IPS', 3),
(89, 21, 'Độ phủ màu', '99% DCI-P3', 4),
(90, 21, 'Cổng kết nối', 'Thunderbolt 3, USB-C', 5),
(91, 22, 'Kích thước', '32 inch', 1),
(92, 22, 'Độ phân giải', '2K (2560x1440)', 2),
(93, 22, 'Tần số quét', '240Hz', 3),
(94, 22, 'Thời gian đáp ứng', '1ms', 4),
(95, 22, 'Curve', '1000R', 5),
(96, 23, 'CPU', 'Intel Core i7-13700', 1),
(97, 23, 'RAM', '16GB DDR4', 2),
(98, 23, 'SSD', '512GB', 3),
(99, 23, 'Hệ điều hành', 'Windows 11 Pro', 4),
(100, 24, 'CPU', 'Intel Core i9-13900', 1),
(101, 24, 'RAM', '32GB DDR5', 2),
(102, 24, 'SSD', '1TB', 3),
(103, 24, 'Đồ họa', 'Intel UHD 770', 4),
(104, 25, 'DPI', '8000', 1),
(105, 25, 'Kết nối', 'Bluetooth, 2.4GHz', 2),
(106, 25, 'Pin', '70 ngày (sạc USB-C)', 3),
(107, 25, 'Nút bấm', '6 nút có thể lập trình', 4),
(108, 26, 'Switch', 'Xanh (clicky)', 1),
(109, 26, 'Đèn', 'RGB Chroma', 2),
(110, 26, 'Kết nối', 'USB có dây', 3),
(111, 26, 'Phím điều khiển', 'Đa phương tiện', 4),
(112, 27, 'Loại sim', 'Vật lý + eSIM', 1),
(113, 27, 'Data', '5GB/ngày', 2),
(114, 27, 'Thời hạn', '30 ngày', 3),
(115, 28, 'Thời hạn', '3 tháng', 1),
(116, 28, 'Data', '30GB/tháng', 2),
(117, 28, 'Gọi nội mạng', '3000 phút', 3),
(118, 29, 'Kích thước', '45mm', 1),
(119, 29, 'Màn hình', 'Always-On Retina LTPO', 2),
(120, 29, 'Chip', 'S9', 3),
(121, 29, 'Tính năng', 'ECG, SPO2, GPS', 4),
(122, 30, 'Kích thước', '44mm', 1),
(123, 30, 'Màn hình', '1.5 inch Super AMOLED', 2),
(124, 30, 'Pin', '425mAh', 3),
(125, 30, 'Chống nước', '5ATM', 4),
(126, 31, 'Kích thước', '55 inch', 1),
(127, 31, 'Độ phân giải', '4K UHD', 2),
(128, 31, 'Tần số quét', '120Hz', 3),
(129, 31, 'Hệ điều hành', 'Tizen OS', 4),
(130, 31, 'HDR', 'HDR10+', 5),
(131, 32, 'Kích thước', '65 inch', 1),
(132, 32, 'Độ phân giải', '4K UHD', 2),
(133, 32, 'Tần số quét', '120Hz', 3),
(134, 32, 'Công nghệ', 'OLED evo', 4),
(135, 32, 'Hệ điều hành', 'webOS 23', 5),
(136, 33, 'Công suất', '1.5 HP - 12.000 BTU', 1),
(137, 33, 'Inverter', 'Có', 2),
(138, 33, 'Gas', 'R32', 3),
(139, 33, 'Lọc bụi', 'Có', 4),
(140, 34, 'Công suất', '2 HP - 18.000 BTU', 1),
(141, 34, 'Inverter', 'Có', 2),
(142, 34, 'Công nghệ', 'nanoe X', 3),
(143, 34, 'Độ ồn', '21 dB', 4),
(144, 35, 'Lực hút', '4000Pa', 1),
(145, 35, 'Chức năng', 'Hút + lau', 2),
(146, 35, 'Bản đồ', 'AI 3D', 3),
(147, 35, 'Tự làm sạch', 'Trạm đa năng', 4),
(148, 36, 'Lực hút', '5000Pa', 1),
(149, 36, 'Lau nước nóng', 'Có', 2),
(150, 36, 'Tránh chướng ngại', 'AI thông minh', 3),
(151, 36, 'Tự giặt hơi nóng', 'Có', 4),
(152, 37, 'Dung tích nước', '6 lít', 1),
(153, 37, 'Công suất', '100W', 2),
(154, 37, 'Chế độ gió', '3 chế độ', 3),
(155, 37, 'Hẹn giờ', 'Có (8h)', 4),
(156, 38, 'Dung tích', '5 lít', 1),
(157, 38, 'Công suất', '75W', 2),
(158, 38, 'Hẹn giờ', '7.5 giờ', 3),
(159, 38, 'An toàn', 'Lưới bảo vệ', 4),
(160, 39, 'Khối lượng giặt', '9kg', 1),
(161, 39, 'Công nghệ', 'AI DD', 2),
(162, 39, 'Inverter', 'Có', 3),
(163, 39, 'Giặt hơi nước', 'Có', 4),
(164, 40, 'Khối lượng giặt', '10kg', 1),
(165, 40, 'Công nghệ', 'UltraMix', 2),
(166, 40, 'Inverter', 'Có', 3),
(167, 40, 'Tiết kiệm điện', 'A+++', 4),
(168, 41, 'Dung tích', '400 lít', 1),
(169, 41, 'Inverter', 'Có', 2),
(170, 41, 'Công nghệ', 'SpaceMax', 3),
(171, 41, 'Tiêu thụ điện', '0.9 kW/ngày', 4),
(172, 42, 'Dung tích', '450 lít', 1),
(173, 42, 'Inverter', 'Có', 2),
(174, 42, 'Ngăn đá mềm', 'Có', 3),
(175, 42, 'Kháng khuẩn', 'Có', 4),
(176, 43, 'Số lõi', '11 lõi', 1),
(177, 43, 'Công nghệ', 'RO Hydrogen', 2),
(178, 43, 'Nóng lạnh', 'Có', 3),
(179, 43, 'Bình chứa', '4 lít', 4),
(180, 44, 'Số lõi', '9 lõi', 1),
(181, 44, 'Công nghệ', 'RO', 2),
(182, 44, 'Dung tích', '3.5 lít', 3),
(183, 44, 'Vỏ', 'Inox', 4),
(184, 45, 'Màn hình', '6.1 inch Super Retina XDR', 1),
(185, 45, 'CPU', 'A14 Bionic', 2),
(186, 45, 'RAM', '4GB', 3),
(187, 45, 'Camera', '12MP kép', 4),
(188, 45, 'Pin', '87% (like new)', 5),
(189, 46, 'CPU', 'Intel Core i5', 1),
(190, 46, 'RAM', '8GB', 2),
(191, 46, 'SSD', '128GB', 3),
(192, 46, 'Màn hình', '13.3 inch Retina', 4),
(193, 47, 'Khối lượng sấy', '8kg', 1),
(194, 47, 'Công nghệ', 'Bơm nhiệt', 2),
(195, 47, 'Chống nhăn', 'Có', 3),
(196, 47, 'Kháng khuẩn', 'Có', 4),
(197, 48, 'Khối lượng sấy', '9kg', 1),
(198, 48, 'Loại', 'Thông hơi', 2),
(199, 48, 'Chế độ sấy', '3 chế độ', 3),
(200, 49, 'Độ phân giải', '2K (2304x1296)', 1),
(201, 49, 'Góc nhìn', '130°', 2),
(202, 49, 'Nghe nói 2 chiều', 'Có', 3),
(203, 49, 'Hồng ngoại', 'Có (10m)', 4),
(204, 50, 'Độ phân giải', '4MP (2560x1440)', 1),
(205, 50, 'Chống nước', 'IP66', 2),
(206, 50, 'Đèn LED màu', 'Có', 3),
(207, 50, 'Lưu trữ', 'Thẻ nhớ 256GB', 4),
(208, 51, 'Công suất', '1500W', 1),
(209, 51, 'Bình nước', '1.5 lít', 2),
(210, 51, 'Chống nhỏ giọt', 'Có', 3),
(211, 52, 'Dung tích', '20 lít', 1),
(212, 52, 'Công suất', '800W', 2),
(213, 52, 'Chức năng', 'Nấu, rã đông', 3),
(214, 53, 'Pin', '4000mAh', 1),
(215, 53, 'Tốc độ', '3 cấp', 2),
(216, 53, 'Thời gian sử dụng', 'Lên đến 8 giờ', 3),
(217, 54, 'Công suất', '55W', 1),
(218, 54, 'Số cánh', '5', 2),
(219, 54, 'Điều khiển', 'Remote', 3),
(220, 55, 'CADR', '500 m³/h', 1),
(221, 55, 'Diện tích', '60 m²', 2),
(222, 55, 'Lọc HEPA', 'Có', 3),
(223, 55, 'Cảm biến', 'PM2.5, nhiệt độ, độ ẩm', 4),
(224, 56, 'CADR', '300 m³/h', 1),
(225, 56, 'Lọc 4 cấp', 'HEPA + than hoạt tính', 2),
(226, 56, 'Chế độ ngủ', 'Êm ái', 3),
(227, 57, 'Số vùng nấu', '2', 1),
(228, 57, 'Công suất', '2000W', 2),
(229, 57, 'Mặt kính', 'Ceramic', 3),
(230, 58, 'Công suất', '2000W', 1),
(231, 58, 'Mặt kính', 'Chịu nhiệt', 2),
(232, 58, 'Điều khiển', 'Cảm ứng', 3),
(233, 59, 'Dung tích', '1.8 lít', 1),
(234, 59, 'Lòng nồi', 'Chống dính', 2),
(235, 59, 'Chức năng', 'Nấu cơm, cháo', 3),
(236, 60, 'Dung tích', '1.0 lít', 1),
(237, 60, 'Công nghệ', 'Cao tần', 2),
(238, 60, 'Lòng nồi', 'Kim loại', 3),
(239, 61, 'Công suất', '800W', 1),
(240, 61, 'Cối', 'Thủy tinh 1.5L', 2),
(241, 61, 'Tốc độ', '2 cấp + turbo', 3),
(242, 62, 'Công nghệ', 'Ép chậm', 1),
(243, 62, 'Công suất', '150W', 2),
(244, 62, 'Dễ vệ sinh', 'Có', 3),
(245, 63, 'Dung tích', '4.5 lít', 1),
(246, 63, 'Công suất', '1400W', 2),
(247, 63, 'Công nghệ', 'Rapid Air', 3),
(248, 64, 'Dung tích', '5 lít', 1),
(249, 64, 'Công suất', '1500W', 2),
(250, 64, 'Hẹn giờ', '30 phút', 3),
(251, 65, 'Loại', 'Laser đen trắng', 1),
(252, 65, 'Tốc độ', '20 trang/phút', 2),
(253, 65, 'Kết nối', 'Wi-Fi, USB', 3),
(254, 66, 'Loại', 'Ink tank', 1),
(255, 66, 'Chức năng', 'In ảnh không viền', 2),
(256, 66, 'Kết nối', 'USB, Wi-Fi', 3),
(257, 67, 'Dung tích bình', '5 lít', 1),
(258, 67, 'Nhiệt độ nóng', '85-95°C', 2),
(259, 67, 'Nhiệt độ lạnh', '5-10°C', 3),
(260, 68, 'Dung tích', '5 lít', 1),
(261, 68, 'Làm lạnh', '10°C', 2),
(262, 68, 'Làm nóng', '90°C', 3),
(263, 69, 'Loại', 'Cổ tay', 1),
(264, 69, 'Bộ nhớ', '60 lần', 2),
(265, 69, 'Phát hiện rối loạn nhịp', 'Có', 3),
(266, 70, 'Chỉ số', '13 chỉ số', 1),
(267, 70, 'Kết nối', 'Bluetooth 5.0', 2),
(268, 70, 'Mặt kính', 'Cường lực', 3),
(269, 71, 'Công suất', '10W', 1),
(270, 71, 'Đầu đấm', '4 đầu', 2),
(271, 71, 'Tốc độ', '3 cấp', 3),
(272, 72, 'Chức năng', 'Rung nhiệt', 1),
(273, 72, 'Mô phỏng', '8 mô phỏng', 2),
(274, 72, 'Điều khiển', 'Remote', 3),
(275, 73, 'Dung tích', '15 lít', 1),
(276, 73, 'Công suất', '2500W', 2),
(277, 73, 'Chống giật', 'Có', 3),
(278, 74, 'Dung tích', '30 lít', 1),
(279, 74, 'Công suất', '2500W', 2),
(280, 74, 'Điều khiển', 'Cơ', 3),
(281, 75, 'Lượng hút', '10L/ngày', 1),
(282, 75, 'Diện tích', '30m²', 2),
(283, 75, 'Lọc bụi', 'Có', 3),
(284, 76, 'Lượng hút', '16L/ngày', 1),
(285, 76, 'Cảm biến', 'Độ ẩm', 2),
(286, 76, 'Màn hình', 'LCD', 3),
(287, 77, 'Khung', 'Nhôm', 1),
(288, 77, 'Phanh', 'Đĩa cơ', 2),
(289, 77, 'Tốc độ', '21', 3),
(290, 77, 'Lốp', '700x38c', 4),
(291, 78, 'Khung', 'Nhôm Alpha', 1),
(292, 78, 'Phanh', 'Đĩa thủy lực', 2),
(293, 78, 'Tốc độ', '24', 3),
(294, 78, 'Trọng lượng', '~10 kg', 4),
(295, 79, 'Công suất', '30W', 1),
(296, 79, 'Chống nước', 'IP67', 2),
(297, 79, 'Pin', '12 giờ', 3),
(298, 79, 'Kết nối', 'Bluetooth 5.1', 4),
(299, 80, 'Công suất', '20W', 1),
(300, 80, 'Pin', '24 giờ', 2),
(301, 80, 'Chống nước', 'IP67', 3),
(302, 80, 'Kết nối', 'Bluetooth, Line-out', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh_danh_muc`
--

CREATE TABLE `thuoc_tinh_danh_muc` (
  `id` int NOT NULL,
  `danh_muc_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên key JSON (VD: RAM, Kich_thuoc)',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nhãn hiển thị cho Admin (VD: Dung lượng RAM)',
  `placeholder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gợi ý nhập liệu',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT 'Loại thẻ input: text, number...',
  `col` int DEFAULT '6' COMMENT 'Kích thước cột Bootstrap (6 = nửa dòng, 12 = cả dòng)',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thuoc_tinh_danh_muc`
--

INSERT INTO `thuoc_tinh_danh_muc` (`id`, `danh_muc_id`, `name`, `label`, `placeholder`, `type`, `col`, `thu_tu`) VALUES
(1, 1, 'RAM', 'Dung lượng RAM', 'VD: 8GB', 'text', 6, 1),
(2, 9, 'Màn hình', 'Kích thước màn hình', 'Nhập kích thước màn hình', 'text', 6, 0),
(3, 1, 'bo_nho_trong', 'Bộ nhớ trong', 'VD: 128GB, 256GB', 'text', 6, 1),
(4, 1, 'ram', 'RAM', 'VD: 8GB, 12GB', 'text', 6, 2),
(5, 1, 'chip', 'Chip xử lý', 'VD: A18 Pro, Snapdragon 8 Gen 3', 'text', 6, 3),
(6, 1, 'dung_luong_pin', 'Dung lượng pin', 'VD: 5000mAh', 'text', 6, 4),
(7, 1, 'man_hinh', 'Màn hình', 'VD: 6.7 inch Super Retina XDR', 'text', 6, 5),
(8, 1, 'camera_sau', 'Camera sau', 'VD: 48MP chính + 12MP góc siêu rộng', 'text', 12, 6),
(9, 1, 'camera_truoc', 'Camera trước', 'VD: 12MP', 'text', 6, 7),
(10, 1, 'he_dieu_hanh', 'Hệ điều hành', 'VD: iOS 18, Android 15', 'text', 6, 8),
(11, 1, 'khe_sim', 'Khe SIM', 'VD: 2 nano SIM, eSIM', 'text', 6, 9),
(12, 1, 'cong_sac', 'Cổng sạc', 'VD: USB-C, Lightning', 'text', 6, 10),
(13, 2, 'bo_nho_trong', 'Bộ nhớ trong', 'VD: 64GB, 128GB', 'text', 6, 1),
(14, 2, 'ram', 'RAM', 'VD: 8GB, 12GB', 'text', 6, 2),
(15, 2, 'chip', 'Chip xử lý', 'VD: Apple M2, Snapdragon', 'text', 6, 3),
(16, 2, 'dung_luong_pin', 'Dung lượng pin', 'VD: 8000mAh', 'text', 6, 4),
(17, 2, 'man_hinh', 'Kích thước màn hình', 'VD: 11 inch, 12.9 inch', 'text', 6, 5),
(18, 2, 'he_dieu_hanh', 'Hệ điều hành', 'VD: iPadOS, Android', 'text', 6, 6),
(19, 2, 'ho_tro_ban_phim', 'Hỗ trợ bàn phím', 'VD: Có, không', 'text', 6, 7),
(20, 2, 'cong_ket_noi', 'Cổng kết nối', 'VD: USB-C, Lightning', 'text', 6, 8),
(21, 3, 'cpu', 'CPU', 'VD: Intel Core i7-1360P, AMD Ryzen 7', 'text', 6, 1),
(22, 3, 'ram', 'RAM', 'VD: 16GB DDR5', 'text', 6, 2),
(23, 3, 'o_cung', 'Ổ cứng', 'VD: 512GB SSD PCIe', 'text', 6, 3),
(24, 3, 'man_hinh', 'Màn hình', 'VD: 14 inch OLED 2.8K', 'text', 6, 4),
(25, 3, 'card_do_hoa', 'Card đồ họa', 'VD: NVIDIA RTX 4060', 'text', 6, 5),
(26, 3, 'pin', 'Pin', 'VD: 75Wh, lên đến 12 giờ', 'text', 6, 6),
(27, 3, 'trong_luong', 'Trọng lượng', 'VD: 1.2 kg', 'text', 6, 7),
(28, 3, 'he_dieu_hanh', 'Hệ điều hành', 'VD: Windows 11, macOS', 'text', 6, 8),
(29, 3, 'cong_ket_noi', 'Cổng kết nối', 'VD: 2x USB-C, HDMI, jack 3.5mm', 'text', 12, 9),
(30, 4, 'kich_thuoc', 'Kích thước màn hình', 'VD: 24 inch, 27 inch', 'text', 6, 1),
(31, 4, 'do_phan_giai', 'Độ phân giải', 'VD: 1920x1080, 4K UHD', 'text', 6, 2),
(32, 4, 'tan_so_quet', 'Tần số quét', 'VD: 60Hz, 144Hz, 240Hz', 'text', 6, 3),
(33, 4, 'tam_nen', 'Tấm nền', 'VD: IPS, VA, OLED', 'text', 6, 4),
(34, 4, 'do_sang', 'Độ sáng', 'VD: 300 cd/m², 400 cd/m²', 'text', 6, 5),
(35, 4, 'cong_ket_noi', 'Cổng kết nối', 'VD: HDMI, DisplayPort, USB-C', 'text', 12, 6),
(36, 5, 'cpu', 'CPU', 'VD: Intel Core i9-13900K', 'text', 6, 1),
(37, 5, 'ram', 'RAM', 'VD: 32GB DDR4', 'text', 6, 2),
(38, 5, 'o_cung', 'Ổ cứng', 'VD: 1TB SSD + 2TB HDD', 'text', 6, 3),
(39, 5, 'card_do_hoa', 'Card đồ họa', 'VD: NVIDIA RTX 4070 Ti', 'text', 6, 4),
(40, 5, 'nguon', 'Nguồn', 'VD: 750W 80 Plus Gold', 'text', 6, 5),
(41, 5, 'he_dieu_hanh', 'Hệ điều hành', 'VD: Windows 11 Pro', 'text', 6, 6),
(42, 6, 'loai_ket_noi', 'Loại kết nối', 'VD: Bluetooth, USB, Lightning', 'text', 6, 1),
(43, 6, 'tuong_thich', 'Tương thích', 'VD: iPhone, Samsung, đa năng', 'text', 12, 2),
(44, 6, 'chat_lieu', 'Chất liệu', 'VD: Silicon, da, nhựa ABS', 'text', 6, 3),
(45, 7, 'loai_sim', 'Loại sim', 'VD: Vật lý, eSIM', 'text', 6, 1),
(46, 7, 'dung_luong', 'Dung lượng data', 'VD: 1GB/ngày, 5GB/tháng', 'text', 6, 2),
(47, 7, 'thoi_han', 'Thời hạn sử dụng', 'VD: 30 ngày, 365 ngày', 'text', 6, 3),
(48, 8, 'man_hinh', 'Kích thước màn hình', 'VD: 1.9 inch AMOLED', 'text', 6, 1),
(49, 8, 'he_dieu_hanh', 'Hệ điều hành', 'VD: Wear OS, watchOS, RTOS', 'text', 6, 2),
(50, 8, 'dung_luong_pin', 'Dung lượng pin', 'VD: 300mAh, lên đến 5 ngày', 'text', 6, 3),
(51, 8, 'chong_nuoc', 'Chống nước', 'VD: 5ATM, IP68', 'text', 6, 4),
(52, 8, 'gps', 'GPS', 'VD: Có, không', 'text', 6, 5),
(53, 8, 'cam_bien', 'Cảm biến', 'VD: nhịp tim, SPO2, nhiệt độ', 'text', 12, 6),
(54, 9, 'kich_thuoc', 'Kích thước màn hình', 'VD: 43 inch, 55 inch', 'text', 6, 1),
(55, 9, 'do_phan_giai', 'Độ phân giải', 'VD: 4K UHD, 8K', 'text', 6, 2),
(56, 9, 'tan_so_quet', 'Tần số quét', 'VD: 60Hz, 120Hz', 'text', 6, 3),
(57, 9, 'he_dieu_hanh', 'Hệ điều hành thông minh', 'VD: Google TV, webOS, Tizen', 'text', 6, 4),
(58, 9, 'cong_ket_noi', 'Cổng kết nối', 'VD: HDMI 2.1, USB, Ethernet', 'text', 12, 5),
(59, 9, 'cong_suat_loa', 'Công suất loa', 'VD: 20W, 40W Dolby Atmos', 'text', 6, 6),
(60, 10, 'cong_suat', 'Công suất (BTU/h)', 'VD: 9000 BTU, 12000 BTU', 'text', 6, 1),
(61, 10, 'loai_may', 'Loại máy', 'VD: Treo tường, âm trần, tủ đứng', 'text', 6, 2),
(62, 10, 'inverter', 'Inverter', 'VD: Có, không', 'text', 6, 3),
(63, 10, 'tieu_thu_dien', 'Tiêu thụ điện', 'VD: ~0.9 kW/h', 'text', 6, 4),
(64, 10, 'chat_lam_lanh', 'Môi chất lạnh', 'VD: R32, R410A', 'text', 6, 5),
(65, 10, 'do_on', 'Độ ồn', 'VD: 22 dB(A)', 'text', 6, 6),
(66, 11, 'cong_suat_hut', 'Công suất hút', 'VD: 3000Pa, 5000Pa', 'text', 6, 1),
(67, 11, 'dung_luong_pin', 'Dung lượng pin', 'VD: 5200mAh', 'text', 6, 2),
(68, 11, 'thoi_gian_hoat_dong', 'Thời gian hoạt động', 'VD: 150 phút', 'text', 6, 3),
(69, 11, 'dung_tich_hop_bui', 'Dung tích hộp bụi', 'VD: 0.5 lít', 'text', 6, 4),
(70, 11, 'chuc_nang_lau', 'Chức năng lau nhà', 'VD: Có, rung ướt', 'text', 6, 5),
(71, 11, 'dieu_khien', 'Điều khiển', 'VD: APP, giọng nói, remote', 'text', 12, 6),
(72, 12, 'loai_quat', 'Loại quạt', 'VD: Quạt hơi nước, quạt điều hòa', 'text', 6, 1),
(73, 12, 'dung_tich_binh_nuoc', 'Dung tích bình nước', 'VD: 5 lít, 7 lít', 'text', 6, 2),
(74, 12, 'cong_suat', 'Công suất', 'VD: 100W, 150W', 'text', 6, 3),
(75, 12, 'che_do_gio', 'Chế độ gió', 'VD: Tự nhiên, ngủ, thường', 'text', 6, 4),
(76, 12, 'hen_gio', 'Hẹn giờ', 'VD: Có (8h), không', 'text', 6, 5),
(77, 13, 'loai_may', 'Loại máy', 'VD: Cửa trên, cửa trước', 'text', 6, 1),
(78, 13, 'khoi_luong_giat', 'Khối lượng giặt', 'VD: 8kg, 9.5kg', 'text', 6, 2),
(79, 13, 'hieu_suat', 'Hiệu suất (Wh/kg)', 'VD: 12.5 Wh/kg', 'text', 6, 3),
(80, 13, 'inverter', 'Inverter', 'VD: Có, không', 'text', 6, 4),
(81, 13, 'cong_nghe_say', 'Công nghệ sấy', 'VD: Sấy khô, sấy thông hơi', 'text', 12, 5),
(82, 14, 'loai_tu', 'Loại tủ', 'VD: Ngăn đá trên, ngăn đá dưới, side-by-side', 'text', 6, 1),
(83, 14, 'dung_tich', 'Dung tích tổng', 'VD: 350 lít', 'text', 6, 2),
(84, 14, 'inverter', 'Inverter', 'VD: Có, không', 'text', 6, 3),
(85, 14, 'tieu_thu_dien', 'Tiêu thụ điện', 'VD: 0.9 kW/ngày', 'text', 6, 4),
(86, 14, 'ngan_dong_mem', 'Ngăn đông mềm', 'VD: Có, không', 'text', 6, 5),
(87, 15, 'so_loi_loc', 'Số lõi lọc', 'VD: 7 lõi, 11 lõi', 'text', 6, 1),
(88, 15, 'cong_nghe_loc', 'Công nghệ lọc', 'VD: RO, Nano, Hydrogen', 'text', 6, 2),
(89, 15, 'dung_tich_binh_chua', 'Dung tích bình chứa', 'VD: 4 lít', 'text', 6, 3),
(90, 15, 'cong_suat', 'Công suất', 'VD: 45W', 'text', 6, 4),
(91, 15, 'nuoc_nong_lanh', 'Nước nóng/lạnh', 'VD: Nóng lạnh thường, không', 'text', 6, 5),
(92, 16, 'loai_may', 'Loại máy', 'VD: Điện thoại, Laptop, Máy tính bảng', 'text', 6, 1),
(93, 16, 'tinh_trang', 'Tình trạng', 'VD: Like new, đã qua sử dụng', 'text', 6, 2),
(94, 16, 'bao_hanh', 'Bảo hành', 'VD: 3 tháng, 6 tháng', 'text', 6, 3),
(95, 16, 'cau_hinh', 'Cấu hình chi tiết', 'VD: iPhone 12 64GB, RAM 4GB', 'text', 12, 4),
(96, 26, 'loai_may', 'Loại máy', 'VD: Bơm nhiệt, thông hơi, ngưng tụ', 'text', 6, 1),
(97, 26, 'khoi_luong_say', 'Khối lượng sấy', 'VD: 8kg, 9kg', 'text', 6, 2),
(98, 26, 'chat_lieu', 'Chất liệu lồng', 'VD: Thép không gỉ, nhựa', 'text', 6, 3),
(99, 26, 'inverter', 'Inverter', 'VD: Có, không', 'text', 6, 4),
(100, 27, 'do_phan_giai', 'Độ phân giải', 'VD: 2MP, 5MP, 4K', 'text', 6, 1),
(101, 27, 'goc_nhin', 'Góc nhìn', 'VD: 110°, 360°', 'text', 6, 2),
(102, 27, 'ho_tro_audio', 'Hỗ trợ âm thanh', 'VD: 2 chiều, chỉ nghe', 'text', 6, 3),
(103, 27, 'luu_tru', 'Lưu trữ', 'VD: Thẻ nhớ tối đa 128GB, Cloud', 'text', 6, 4),
(104, 27, 'chong_nuoc', 'Chống nước', 'VD: IP66, IP67', 'text', 6, 5),
(105, 28, 'cong_suat', 'Công suất', 'VD: 2000W', 'text', 6, 1),
(106, 28, 'dien_ap', 'Điện áp', 'VD: 220V', 'text', 6, 2),
(107, 28, 'chat_lieu', 'Chất liệu', 'VD: Nhựa cao cấp, Inox', 'text', 6, 3),
(108, 29, 'loai_quat', 'Loại quạt', 'VD: Đứng, bàn, treo tường, cây', 'text', 6, 1),
(109, 29, 'so_canh', 'Số cánh quạt', 'VD: 3, 5', 'text', 6, 2),
(110, 29, 'che_do_gio', 'Chế độ gió', 'VD: 3 cấp độ', 'text', 6, 3),
(111, 29, 'hen_gio', 'Hẹn giờ', 'VD: Có (8h), không', 'text', 6, 4),
(112, 30, 'dien_tich_phong', 'Diện tích phù hợp', 'VD: 30m², 50m²', 'text', 6, 1),
(113, 30, 'luu_luong_khi', 'Lưu lượng khí (CADR)', 'VD: 200 m³/h', 'text', 6, 2),
(114, 30, 'so_tang_loc', 'Số tầng lọc', 'VD: 3, 4', 'text', 6, 3),
(115, 30, 'cam_bien', 'Cảm biến', 'VD: Bụi, mùi, nhiệt độ', 'text', 6, 4),
(116, 30, 'do_on', 'Độ ồn', 'VD: 25-50 dB', 'text', 6, 5),
(117, 31, 'loai_thiet_bi', 'Loại thiết bị', 'VD: Bếp từ, bếp hồng ngoại, lò vi sóng', 'text', 6, 1),
(118, 31, 'cong_suat', 'Công suất', 'VD: 2000W', 'text', 6, 2),
(119, 31, 'so_vung_nau', 'Số vùng nấu', 'VD: 2, 3, 4', 'text', 6, 3),
(120, 31, 'chat_lieu_mat', 'Chất liệu mặt bếp', 'VD: Kính cường lực, Ceramic', 'text', 6, 4),
(121, 32, 'dung_tich', 'Dung tích', 'VD: 1.0 lít, 1.8 lít', 'text', 6, 1),
(122, 32, 'cong_suat', 'Công suất', 'VD: 500W, 700W', 'text', 6, 2),
(123, 32, 'chat_lieu_long', 'Chất liệu lòng nồi', 'VD: Chống dính, gang phủ', 'text', 6, 3),
(124, 32, 'chuc_nang', 'Chức năng', 'VD: Nấu cơm, hầm, làm bánh', 'text', 12, 4),
(125, 33, 'loai_may', 'Loại máy', 'VD: Máy xay sinh tố, máy ép chậm', 'text', 6, 1),
(126, 33, 'cong_suat', 'Công suất', 'VD: 400W, 1000W', 'text', 6, 2),
(127, 33, 'dung_tich_coc', 'Dung tích cối xay', 'VD: 1.2 lít', 'text', 6, 3),
(128, 33, 'toc_do', 'Tốc độ', 'VD: 2 cấp + turbo', 'text', 6, 4),
(129, 33, 'chat_lieu_coc', 'Chất liệu cối', 'VD: Thủy tinh, nhựa Tritan', 'text', 6, 5),
(130, 34, 'dung_tich', 'Dung tích', 'VD: 3.5 lít, 5.5 lít', 'text', 6, 1),
(131, 34, 'cong_suat', 'Công suất', 'VD: 1500W', 'text', 6, 2),
(132, 34, 'nhiet_do', 'Dải nhiệt độ', 'VD: 80-200°C', 'text', 6, 3),
(133, 34, 'hen_gio', 'Hẹn giờ', 'VD: 60 phút', 'text', 6, 4),
(134, 34, 'chat_lieu_long', 'Chất liệu lòng', 'VD: Chống dính, tháo rời', 'text', 6, 5),
(135, 35, 'loai_may_in', 'Loại máy in', 'VD: Laser, phun mực, đa năng', 'text', 6, 1),
(136, 35, 'toc_do_in', 'Tốc độ in', 'VD: 20 trang/phút', 'text', 6, 2),
(137, 35, 'do_phan_giai', 'Độ phân giải', 'VD: 1200x1200 dpi', 'text', 6, 3),
(138, 35, 'ket_noi', 'Kết nối', 'VD: USB, Wi-Fi, Ethernet', 'text', 6, 4),
(139, 35, 'chuc_nang', 'Chức năng', 'VD: In, scan, copy, fax', 'text', 12, 5),
(140, 36, 'loai_cay', 'Loại cây', 'VD: Đứng, để bàn', 'text', 6, 1),
(141, 36, 'dung_tich_binh', 'Dung tích bình chứa', 'VD: 5 lít, 7 lít', 'text', 6, 2),
(142, 36, 'nhiet_do', 'Nhiệt độ', 'VD: Nóng (85-95°C), Lạnh (5-10°C)', 'text', 6, 3),
(143, 36, 'cong_suat', 'Công suất', 'VD: Làm nóng 500W, làm lạnh 100W', 'text', 6, 4),
(144, 36, 'tinh_nang_dac_biet', 'Tính năng đặc biệt', 'VD: Khử trùng UV, touch screen', 'text', 12, 5),
(145, 37, 'loai_san_pham', 'Loại sản phẩm', 'VD: Cân điện tử, máy đo huyết áp', 'text', 6, 1),
(146, 37, 'cong_nghe', 'Công nghệ', 'VD: Cảm biến điện tử, Bluetooth', 'text', 6, 2),
(147, 37, 'pin', 'Pin', 'VD: 2xAAA, sạc USB', 'text', 6, 3),
(148, 37, 'do_chinh_xac', 'Độ chính xác', 'VD: ±0.1 kg', 'text', 6, 4),
(149, 38, 'loai_may', 'Loại máy', 'VD: Massage cầm tay, gối massage, ghế massage', 'text', 6, 1),
(150, 38, 'cong_suat', 'Công suất', 'VD: 40W, 100W', 'text', 6, 2),
(151, 38, 'che_do', 'Chế độ massage', 'VD: Rung, xoa bóp, nhiệt', 'text', 6, 3),
(152, 38, 'nguon_dien', 'Nguồn điện', 'VD: Sạc USB, pin, cắm điện', 'text', 6, 4),
(153, 39, 'loai_may', 'Loại máy', 'VD: Trực tiếp, gián tiếp', 'text', 6, 1),
(154, 39, 'dung_tich', 'Dung tích bình chứa', 'VD: 15 lít, 30 lít', 'text', 6, 2),
(155, 39, 'cong_suat', 'Công suất', 'VD: 2500W', 'text', 6, 3),
(156, 39, 'che_do_bao_ve', 'Chế độ bảo vệ', 'VD: Chống giật, chống quá nhiệt', 'text', 6, 4),
(157, 40, 'dien_tich', 'Diện tích phù hợp', 'VD: 30m², 50m²', 'text', 6, 1),
(158, 40, 'luong_hut_am', 'Lượng hút ẩm', 'VD: 10L/ngày, 20L/ngày', 'text', 6, 2),
(159, 40, 'dung_tich_binh', 'Dung tích bình chứa', 'VD: 2.5 lít', 'text', 6, 3),
(160, 40, 'do_on', 'Độ ồn', 'VD: 40 dB', 'text', 6, 4),
(161, 41, 'loai_xe', 'Loại xe', 'VD: Xe đạp địa hình, xe đạp thể thao, xe đạp gấp', 'text', 6, 1),
(162, 41, 'khung_xe', 'Chất liệu khung', 'VD: Thép, nhôm, carbon', 'text', 6, 2),
(163, 41, 'so_tang', 'Số tầng', 'VD: 21, 27, 30', 'text', 6, 3),
(164, 41, 'phanh', 'Loại phanh', 'VD: Đĩa cơ, đĩa dầu', 'text', 6, 4),
(165, 41, 'kich_thuoc_bánh', 'Kích thước bánh xe', 'VD: 26 inch, 27.5 inch', 'text', 6, 5),
(166, 42, 'cong_suat', 'Công suất', 'VD: 20W RMS, 60W Peak', 'text', 6, 1),
(167, 42, 'ket_noi', 'Kết nối', 'VD: Bluetooth 5.0, AUX, USB', 'text', 12, 2),
(168, 42, 'thoi_luong_pin', 'Thời lượng pin', 'VD: 10 giờ', 'text', 6, 3),
(169, 42, 'chong_nuoc', 'Chống nước', 'VD: IPX7, IP67', 'text', 6, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction_log`
--

CREATE TABLE `transaction_log` (
  `id` int NOT NULL,
  `thanh_toan_id` int NOT NULL COMMENT 'Foreign key to thanh_toan table',
  `gateway_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo)',
  `gateway_name` enum('VNPAY','COD','REFUND','VIETQR','PAYPAL') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_data` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Payment request sent to gateway',
  `response_data` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Response received from gateway',
  `callback_data` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON: Callback/IPN data received from gateway',
  `status` enum('PENDING','SUCCESS','FAILED','EXPIRED','AMOUNT_MISMATCH') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT 'Transaction status',
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
(46, 41, NULL, 'REFUND', '{\"action\":\"REFUND_COMPLETED\",\"refund_id\":4,\"amount\":270000,\"reason\":\"test\",\"gateway_refund_id\":\"REFUND_41_1776362964\",\"admin_id\":4,\"timestamp\":\"2026-04-16 20:09:24\"}', NULL, NULL, 'PENDING', '2026-04-17 01:09:24'),
(47, 42, NULL, 'VIETQR', '{\"don_hang_id\":43,\"amount\":270000,\"payment_method\":\"VIETQR\",\"expiration_time\":\"2026-04-16 21:08:09\"}', NULL, NULL, 'PENDING', '2026-04-17 01:53:09'),
(48, 43, NULL, 'VIETQR', '{\"don_hang_id\":44,\"amount\":270000,\"payment_method\":\"VIETQR\",\"expiration_time\":\"2026-04-16 21:09:24\"}', NULL, NULL, 'PENDING', '2026-04-17 01:54:24'),
(49, 44, NULL, 'VIETQR', '{\"don_hang_id\":45,\"amount\":270000,\"payment_method\":\"VIETQR\",\"expiration_time\":\"2026-04-16 21:15:38\"}', NULL, NULL, 'PENDING', '2026-04-17 02:00:38'),
(50, 45, NULL, 'PAYPAL', '{\"don_hang_id\":46,\"amount\":8820000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:40:57\"}', NULL, NULL, 'PENDING', '2026-04-17 02:25:57'),
(51, 46, NULL, 'PAYPAL', '{\"don_hang_id\":47,\"amount\":9829999,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:42:51\"}', NULL, NULL, 'PENDING', '2026-04-17 02:27:51'),
(52, 47, NULL, 'VIETQR', '{\"don_hang_id\":48,\"amount\":4020000,\"payment_method\":\"VIETQR\",\"expiration_time\":\"2026-04-16 21:44:21\"}', NULL, NULL, 'PENDING', '2026-04-17 02:29:21'),
(53, 48, NULL, 'PAYPAL', '{\"don_hang_id\":49,\"amount\":4020000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:48:26\"}', NULL, NULL, 'PENDING', '2026-04-17 02:33:26'),
(54, 49, NULL, 'PAYPAL', '{\"don_hang_id\":50,\"amount\":4220000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:49:27\"}', NULL, NULL, 'PENDING', '2026-04-17 02:34:27'),
(55, 50, NULL, 'PAYPAL', '{\"don_hang_id\":51,\"amount\":9020000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:50:54\"}', NULL, NULL, 'PENDING', '2026-04-17 02:35:54'),
(56, 51, NULL, 'PAYPAL', '{\"don_hang_id\":52,\"amount\":470000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:51:54\"}', NULL, NULL, 'PENDING', '2026-04-17 02:36:54'),
(57, 52, NULL, 'PAYPAL', '{\"don_hang_id\":53,\"amount\":270000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:54:51\"}', NULL, NULL, 'PENDING', '2026-04-17 02:39:51'),
(58, 53, NULL, 'PAYPAL', '{\"don_hang_id\":54,\"amount\":270000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 21:58:00\"}', NULL, NULL, 'PENDING', '2026-04-17 02:43:00'),
(59, 54, NULL, 'PAYPAL', '{\"don_hang_id\":55,\"amount\":270000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 22:00:48\"}', NULL, NULL, 'PENDING', '2026-04-17 02:45:48'),
(60, 55, NULL, 'PAYPAL', '{\"don_hang_id\":56,\"amount\":270000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 22:06:51\"}', NULL, NULL, 'PENDING', '2026-04-17 02:51:51'),
(61, 56, NULL, 'PAYPAL', '{\"don_hang_id\":57,\"amount\":4020000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-16 22:09:57\"}', NULL, NULL, 'PENDING', '2026-04-17 02:54:57'),
(62, 56, '2TR17181J83990820', 'PAYPAL', '{\"token\":\"4N800447EJ768920X\"}', '{\"id\":\"4N800447EJ768920X\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"account_id\":\"KT3L4Q2ZDJGZ6\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"address\":{\"country_code\":\"VN\"}}},\"purchase_units\":[{\"reference_id\":\"DH_57\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Vietnam Main Street\",\"admin_area_2\":\"Hanoi\",\"admin_area_1\":\"VIETNAM\",\"postal_code\":\"100000\",\"country_code\":\"VN\"}},\"payments\":{\"captures\":[{\"id\":\"2TR17181J83990820\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"160.80\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"160.80\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"5.77\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"155.03\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/2TR17181J83990820\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/2TR17181J83990820/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/4N800447EJ768920X\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2026-04-16T19:55:06Z\",\"update_time\":\"2026-04-16T19:55:06Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"payer_id\":\"KT3L4Q2ZDJGZ6\",\"address\":{\"country_code\":\"VN\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/4N800447EJ768920X\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL, 'SUCCESS', '2026-04-17 02:55:06'),
(63, 57, NULL, 'VNPAY', '{\"don_hang_id\":58,\"amount\":270000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-16 22:26:35\"}', NULL, NULL, 'PENDING', '2026-04-17 03:11:35'),
(64, 57, '15500362', 'VNPAY', '{\"vnp_TxnRef\":\"57\"}', '{\"vnp_Amount\":\"27000000\",\"vnp_BankCode\":\"NCB\",\"vnp_BankTranNo\":\"VNP15500362\",\"vnp_CardType\":\"ATM\",\"vnp_OrderInfo\":\"Thanh toan don hang 58\",\"vnp_PayDate\":\"20260417031233\",\"vnp_ResponseCode\":\"00\",\"vnp_TmnCode\":\"NUIPDZDI\",\"vnp_TransactionNo\":\"15500362\",\"vnp_TransactionStatus\":\"00\",\"vnp_TxnRef\":\"57\",\"vnp_SecureHash\":\"7514d2355fbf9654d746e724bb777c4f1a9cd65554454cd36d0feb87208e34f0a7ca29c84492975a19e7dc54c084c87724e414832d8ac5054dd8c7ce7edb52ce\"}', NULL, 'SUCCESS', '2026-04-17 03:12:37'),
(65, 58, NULL, 'COD', '{\"don_hang_id\":60,\"amount\":17720000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-17 05:52:05\"}', NULL, NULL, 'PENDING', '2026-04-17 10:37:05'),
(66, 59, NULL, 'VNPAY', '{\"don_hang_id\":61,\"amount\":30017.495000000003,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-17 05:54:11\"}', NULL, NULL, 'PENDING', '2026-04-17 10:39:11'),
(67, 59, '15500646', 'VNPAY', '{\"vnp_TxnRef\":\"59\"}', '{\"vnp_Amount\":\"3001700\",\"vnp_BankCode\":\"NCB\",\"vnp_BankTranNo\":\"VNP15500646\",\"vnp_CardType\":\"ATM\",\"vnp_OrderInfo\":\"Thanh toan don hang 61\",\"vnp_PayDate\":\"20260417103946\",\"vnp_ResponseCode\":\"00\",\"vnp_TmnCode\":\"NUIPDZDI\",\"vnp_TransactionNo\":\"15500646\",\"vnp_TransactionStatus\":\"00\",\"vnp_TxnRef\":\"59\",\"vnp_SecureHash\":\"6822dab9cd639204e0e8efdad3bfa5b4db1836abda541d2fd67f47a0b53c0797264090a217738349e5914f83a9e7bdfe5c48da8b7d393356a3643d265475ee89\"}', NULL, 'SUCCESS', '2026-04-17 10:39:50'),
(68, 60, NULL, 'PAYPAL', '{\"don_hang_id\":62,\"amount\":30017.495000000003,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-17 05:57:44\"}', NULL, NULL, 'PENDING', '2026-04-17 10:42:44'),
(69, 60, '1V119822H2607164C', 'PAYPAL', '{\"token\":\"5CJ39166NK2909350\"}', '{\"id\":\"5CJ39166NK2909350\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"account_id\":\"KT3L4Q2ZDJGZ6\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"address\":{\"country_code\":\"VN\"}}},\"purchase_units\":[{\"reference_id\":\"DH_62\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Vietnam Main Street\",\"admin_area_2\":\"Hanoi\",\"admin_area_1\":\"VIETNAM\",\"postal_code\":\"100000\",\"country_code\":\"VN\"}},\"payments\":{\"captures\":[{\"id\":\"1V119822H2607164C\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"1.20\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"1.20\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.34\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.86\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/1V119822H2607164C\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/1V119822H2607164C/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/5CJ39166NK2909350\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2026-04-17T03:43:30Z\",\"update_time\":\"2026-04-17T03:43:30Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"payer_id\":\"KT3L4Q2ZDJGZ6\",\"address\":{\"country_code\":\"VN\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/5CJ39166NK2909350\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL, 'SUCCESS', '2026-04-17 10:43:29'),
(70, 61, NULL, 'VIETQR', '{\"don_hang_id\":63,\"amount\":30017.495000000003,\"payment_method\":\"VIETQR\",\"expiration_time\":\"2026-04-17 05:59:07\"}', NULL, NULL, 'PENDING', '2026-04-17 10:44:07'),
(71, 62, NULL, 'COD', '{\"don_hang_id\":64,\"amount\":20320000,\"payment_method\":\"COD\",\"expiration_time\":\"2026-04-17 07:19:20\"}', NULL, NULL, 'PENDING', '2026-04-17 12:04:20'),
(80, 67, NULL, 'VNPAY', '{\"don_hang_id\":69,\"amount\":2920000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-17 15:20:41\"}', NULL, NULL, 'PENDING', '2026-04-17 20:05:41'),
(81, 68, NULL, 'VNPAY', '{\"don_hang_id\":70,\"amount\":17720000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-19 02:21:55\"}', NULL, NULL, 'PENDING', '2026-04-19 07:06:55'),
(82, 68, '15502855', 'VNPAY', '{\"vnp_TxnRef\":\"68\"}', '{\"vnp_Amount\":\"1772000000\",\"vnp_BankCode\":\"NCB\",\"vnp_BankTranNo\":\"VNP15502855\",\"vnp_CardType\":\"ATM\",\"vnp_OrderInfo\":\"Thanh toan don hang 70\",\"vnp_PayDate\":\"20260419070726\",\"vnp_ResponseCode\":\"00\",\"vnp_TmnCode\":\"NUIPDZDI\",\"vnp_TransactionNo\":\"15502855\",\"vnp_TransactionStatus\":\"00\",\"vnp_TxnRef\":\"68\",\"vnp_SecureHash\":\"8128b20018855c8927c442c04964781e0466ebbca09b2324e8756c4cee76f0e73b127af45c0bffede85e45819cf915b60ed480e7ef8f03ae45e001bdac703700\"}', NULL, 'SUCCESS', '2026-04-19 07:09:01'),
(83, 69, NULL, 'PAYPAL', '{\"don_hang_id\":71,\"amount\":2920000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-19 02:24:44\"}', NULL, NULL, 'PENDING', '2026-04-19 07:09:44'),
(84, 69, '9BC466995D0237906', 'PAYPAL', '{\"token\":\"0040565310829082R\"}', '{\"id\":\"0040565310829082R\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"account_id\":\"KT3L4Q2ZDJGZ6\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"address\":{\"country_code\":\"VN\"}}},\"purchase_units\":[{\"reference_id\":\"DH_71\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Vietnam Main Street\",\"admin_area_2\":\"Hanoi\",\"admin_area_1\":\"VIETNAM\",\"postal_code\":\"100000\",\"country_code\":\"VN\"}},\"payments\":{\"captures\":[{\"id\":\"9BC466995D0237906\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"116.80\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"116.80\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"4.27\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"112.53\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/9BC466995D0237906\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/9BC466995D0237906/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/0040565310829082R\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2026-04-19T00:10:25Z\",\"update_time\":\"2026-04-19T00:10:25Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"payer_id\":\"KT3L4Q2ZDJGZ6\",\"address\":{\"country_code\":\"VN\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/0040565310829082R\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL, 'SUCCESS', '2026-04-19 07:10:26'),
(85, 70, NULL, 'VNPAY', '{\"don_hang_id\":72,\"amount\":2920000,\"payment_method\":\"CHUYEN_KHOAN\",\"expiration_time\":\"2026-04-19 06:01:30\"}', NULL, NULL, 'PENDING', '2026-04-19 10:46:30'),
(86, 70, '15502915', 'VNPAY', '{\"vnp_TxnRef\":\"70\"}', '{\"vnp_Amount\":\"292000000\",\"vnp_BankCode\":\"NCB\",\"vnp_BankTranNo\":\"VNP15502915\",\"vnp_CardType\":\"ATM\",\"vnp_OrderInfo\":\"Thanh toan don hang 72\",\"vnp_PayDate\":\"20260419104710\",\"vnp_ResponseCode\":\"00\",\"vnp_TmnCode\":\"NUIPDZDI\",\"vnp_TransactionNo\":\"15502915\",\"vnp_TransactionStatus\":\"00\",\"vnp_TxnRef\":\"70\",\"vnp_SecureHash\":\"d2c9e09ff3708fde5ee8014e5e5582ca4da974ee7f5edf9a0987ecbb2c4b962ea59488a3b835a471e4bc9fc866a7e1246b912c488766bc24fac96f1ed209c7b3\"}', NULL, 'SUCCESS', '2026-04-19 10:47:16'),
(87, 71, NULL, 'PAYPAL', '{\"don_hang_id\":73,\"amount\":2920000,\"payment_method\":\"PAYPAL\",\"expiration_time\":\"2026-04-19 06:02:56\"}', NULL, NULL, 'PENDING', '2026-04-19 10:47:56'),
(88, 71, '9BG16660ML468761A', 'PAYPAL', '{\"token\":\"23X27472M1562170C\"}', '{\"id\":\"23X27472M1562170C\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"account_id\":\"KT3L4Q2ZDJGZ6\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"address\":{\"country_code\":\"VN\"}}},\"purchase_units\":[{\"reference_id\":\"DH_73\",\"shipping\":{\"name\":{\"full_name\":\"John Doe\"},\"address\":{\"address_line_1\":\"Vietnam Main Street\",\"admin_area_2\":\"Hanoi\",\"admin_area_1\":\"VIETNAM\",\"postal_code\":\"100000\",\"country_code\":\"VN\"}},\"payments\":{\"captures\":[{\"id\":\"9BG16660ML468761A\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"116.80\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"116.80\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"4.27\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"112.53\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/9BG16660ML468761A\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https://api.sandbox.paypal.com/v2/payments/captures/9BG16660ML468761A/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/23X27472M1562170C\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2026-04-19T03:48:56Z\",\"update_time\":\"2026-04-19T03:48:56Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"John\",\"surname\":\"Doe\"},\"email_address\":\"sb-lgm8g50631707@personal.example.com\",\"payer_id\":\"KT3L4Q2ZDJGZ6\",\"address\":{\"country_code\":\"VN\"}},\"links\":[{\"href\":\"https://api.sandbox.paypal.com/v2/checkout/orders/23X27472M1562170C\",\"rel\":\"self\",\"method\":\"GET\"}]}', NULL, 'SUCCESS', '2026-04-19 10:48:57');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `dia_chi`
--
ALTER TABLE `dia_chi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT cho bảng `gateway_health`
--
ALTER TABLE `gateway_health`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT cho bảng `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT cho bảng `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT cho bảng `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_web`
--

DELIMITER $$
--
-- Procedures
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
-- Table structure for table `banner_quang_cao`
--

CREATE TABLE `banner_quang_cao` (
  `id` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL COMMENT 'Tên banner để admin dễ quản lý',
  `hinh_anh_desktop` varchar(500) NOT NULL COMMENT 'Link ảnh cho màn hình máy tính',
  `hinh_anh_mobile` varchar(500) DEFAULT NULL COMMENT 'Link ảnh cho màn hình điện thoại',
  `link_dich` varchar(500) NOT NULL COMMENT 'URL khi user click vào banner',
  `vi_tri` enum('HOME_HERO','HOME_SIDE','FLOATING_BOTTOM_LEFT','POPUP','CATEGORY_TOP') NOT NULL COMMENT 'Vị trí hiển thị trên web',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Sắp xếp thứ tự nếu có nhiều banner cùng vị trí',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1 = Hiển thị, 0 = Ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banner_quang_cao`
--

INSERT INTO `banner_quang_cao` (`id`, `tieu_de`, `hinh_anh_desktop`, `hinh_anh_mobile`, `link_dich`, `vi_tri`, `thu_tu`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(5, 'Laptop giá sốc - Giảm đến 30%', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'https://www.thegioididong.com/laptop', 'HOME_HERO', 2, '2026-04-04 20:26:09', '2026-04-19 20:26:09', 1),
(6, 'Khuyến mãi siêu sale điện thoại', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9', 'https://shopee.vn', 'HOME_HERO', 1, '2026-04-04 20:51:46', '2026-05-04 20:51:46', 1),
(7, 'Flash Sale phụ kiện - Giá từ 9K', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad', 'https://tiki.vn/phu-kien-dien-thoai/c1789', 'HOME_HERO', 1, '2026-04-04 20:52:18', '2026-04-14 20:52:18', 1),
(14, 'test', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776001382/banners/banner_desktop_14.webp', NULL, '/san-pham/sam-sung', 'HOME_HERO', 0, '2026-04-05 17:29:00', '2026-04-30 17:29:00', 1),
(15, 'săn sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775385139/banners/banner_desktop_1775385136.webp', NULL, '/san-pham/iphone', 'HOME_HERO', 0, '2026-04-05 17:31:00', '2026-04-24 17:31:00', 1),
(17, 'test sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776000404/banners/banner_desktop_1776000399.webp', NULL, '/san-pham/lenovo-thinkpad-x1-carbon-gen-13-u7-258v-21ns010jvn', 'HOME_HERO', 0, '2026-04-12 20:26:00', '2026-04-24 20:26:00', 1),
(18, 'ok', 'https://res.cloudinary.com/dmahghpku/image/upload/v1776001646/banners/banner_desktop_1776001638.png', NULL, '/san-pham/iphone-15-pro-max', 'HOME_HERO', 0, '2026-04-12 20:47:00', '2026-04-25 20:47:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don`
--

CREATE TABLE `chi_tiet_don` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) NOT NULL,
  `phien_ban_id` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 1,
  `gia_tai_thoi_diem_mua` decimal(15,2) DEFAULT NULL COMMENT 'Snapshot giá lúc đặt hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_don`
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
(30, 30, 11, 1, 34.99);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_gio`
--

CREATE TABLE `chi_tiet_gio` (
  `id` int(11) NOT NULL,
  `gio_hang_id` int(11) NOT NULL,
  `phien_ban_id` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_gio`
--

INSERT INTO `chi_tiet_gio` (`id`, `gio_hang_id`, `phien_ban_id`, `so_luong`) VALUES
(5, 8, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_sao` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_viet` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL COMMENT 'URL thân thiện: dien-thoai, laptop',
  `icon_url` varchar(500) DEFAULT NULL COMMENT 'Icon hiển thị trên menu',
  `danh_muc_cha_id` int(11) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị trên menu',
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1=hiện, 0=ẩn',
  `is_noi_bat` tinyint(1) DEFAULT 0 COMMENT '1 = Hiện ở danh mục nổi bật, 0 = Không',
  `is_goi_y` tinyint(1) DEFAULT 0 COMMENT '1 = Hiện ở gợi ý cho bạn, 0 = Không'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danh_muc`
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
-- Table structure for table `dia_chi`
--

CREATE TABLE `dia_chi` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `ten_nguoi_nhan` varchar(255) DEFAULT NULL,
  `sdt_nhan` varchar(20) DEFAULT NULL,
  `so_nha_duong` varchar(255) DEFAULT NULL,
  `phuong_xa` varchar(100) DEFAULT NULL,
  `quan_huyen` varchar(100) DEFAULT NULL,
  `tinh_thanh` varchar(100) DEFAULT NULL,
  `mac_dinh` tinyint(1) DEFAULT 0 COMMENT '1 = địa chỉ mặc định'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dia_chi`
--

INSERT INTO `dia_chi` (`id`, `nguoi_dung_id`, `ten_nguoi_nhan`, `sdt_nhan`, `so_nha_duong`, `phuong_xa`, `quan_huyen`, `tinh_thanh`, `mac_dinh`) VALUES
(2, 3, 'Trương Thành Đạt', '0399746618', 'Lê Duẩn', 'Phường Tân Định', 'Quận 1', 'Thành phố Hồ Chí Minh', 1),
(4, 3, 'Trương Thành Đạt', '0399746618', '49 Hồ Thị Kỷ', 'Phường 1', 'Quận 3', 'Thành phố Hồ Chí Minh', 0);

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `id` int(11) NOT NULL,
  `ma_don_hang` varchar(20) DEFAULT NULL COMMENT 'Mã hiển thị: DH20260224001',
  `nguoi_dung_id` int(11) DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `dia_chi_id` int(11) DEFAULT NULL COMMENT 'NULL nếu guest (dùng thong_tin_guest)',
  `ma_giam_gia_id` int(11) DEFAULT NULL COMMENT 'Voucher áp dụng',
  `trang_thai` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') DEFAULT 'CHO_DUYET',
  `tong_tien` decimal(15,2) DEFAULT NULL COMMENT 'Tổng tiền sản phẩm',
  `phi_van_chuyen` decimal(15,2) DEFAULT 0.00,
  `tien_giam_gia` decimal(15,2) DEFAULT 0.00 COMMENT 'Số tiền được giảm',
  `tong_thanh_toan` decimal(15,2) DEFAULT NULL COMMENT 'tong_tien + phi_van_chuyen - tien_giam_gia',
  `thong_tin_guest` text DEFAULT NULL COMMENT 'JSON: {ten, sdt, dia_chi} cho khách vãng lai',
  `ghi_chu` text DEFAULT NULL COMMENT 'Ghi chú của khách hàng',
  `ngay_giao_du_kien` datetime DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `don_hang`
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
(30, 'DH20260412140909', 3, 4, NULL, 'CHO_DUYET', 34.99, 30000.00, 0.00, 30034.99, NULL, '', NULL, '2026-04-12 19:09:09', '2026-04-12 19:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_health`
--

CREATE TABLE `gateway_health` (
  `id` int(11) NOT NULL,
  `gateway_name` varchar(50) NOT NULL,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `failure_count` int(11) NOT NULL DEFAULT 0,
  `last_success_at` datetime DEFAULT NULL,
  `last_failure_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateway_health`
--

INSERT INTO `gateway_health` (`id`, `gateway_name`, `success_count`, `failure_count`, `last_success_at`, `last_failure_at`, `updated_at`) VALUES
(1, 'VNPay', 19, 0, '2026-04-11 21:21:38', NULL, '2026-04-11 21:21:38'),
(2, 'Momo', 0, 0, NULL, NULL, '2026-04-10 09:43:47'),
(22, 'ZaloPay', 0, 0, NULL, NULL, '2026-04-11 21:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Session cho khách vãng lai',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gio_hang`
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
(18, NULL, 'atjhmgaqrkoth9n0q8eipbaf39', '2026-04-13 08:37:49', '2026-04-13 08:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `hinh_anh_san_pham`
--

CREATE TABLE `hinh_anh_san_pham` (
  `id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `phien_ban_id` int(11) DEFAULT NULL COMMENT 'NULL = ảnh chung, có giá trị = ảnh theo phiên bản/màu',
  `url_anh` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL COMMENT 'Mô tả ảnh cho SEO/accessibility',
  `la_anh_chinh` tinyint(1) DEFAULT 0 COMMENT '1 = ảnh đại diện hiển thị ở listing',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự trong gallery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hinh_anh_san_pham`
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
(19, 11, NULL, 'https://res.cloudinary.com/dmahghpku/image/upload/v1775736448/products/product_11_1775736446_341.webp', 'Ảnh chính', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id` int(11) NOT NULL,
  `ten_chuong_trinh` varchar(255) NOT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') DEFAULT 'PHAN_TRAM',
  `gia_tri_giam` decimal(15,2) DEFAULT NULL COMMENT '10 = 10% hoặc 500000 = 500k VND',
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Giảm tối đa (áp dụng nếu loại %)',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','TAM_DUNG') DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id`, `ten_chuong_trinh`, `loai_giam`, `gia_tri_giam`, `giam_toi_da`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(2, 'Khuyến mãi test 1', 'PHAN_TRAM', 50.00, 90.00, '2026-04-04 22:02:03', '2026-04-24 22:02:03', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Table structure for table `lich_su_tim_kiem`
--

CREATE TABLE `lich_su_tim_kiem` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `tu_khoa` varchar(255) DEFAULT NULL,
  `thoi_gian_tim` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_su_tim_kiem`
--

INSERT INTO `lich_su_tim_kiem` (`id`, `nguoi_dung_id`, `tu_khoa`, `thoi_gian_tim`) VALUES
(1, 4, 'computer', '2026-04-08 21:56:14'),
(2, 3, 'ok', '2026-04-08 23:14:58'),
(3, 3, 'ok', '2026-04-09 20:15:05'),
(4, 3, 'ok', '2026-04-09 23:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `ma_giam_gia`
--

CREATE TABLE `ma_giam_gia` (
  `id` int(11) NOT NULL,
  `ma_code` varchar(50) NOT NULL COMMENT 'VD: FPTSHOP50K, SALE10',
  `mo_ta` varchar(255) DEFAULT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Áp dụng nếu loại PHAN_TRAM',
  `don_toi_thieu` decimal(15,2) DEFAULT 0.00 COMMENT 'Giá trị đơn hàng tối thiểu',
  `so_luot_da_dung` int(11) DEFAULT 0,
  `gioi_han_su_dung` int(11) DEFAULT NULL COMMENT 'NULL = không giới hạn',
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','HET_LUOT') DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int(11) NOT NULL,
  `supabase_id` char(36) DEFAULT NULL COMMENT 'Mã định danh duy nhất từ Supabase',
  `auth_provider` enum('LOCAL','GOOGLE','FACEBOOK') DEFAULT 'LOCAL' COMMENT 'Nguồn tạo tài khoản',
  `email` varchar(255) NOT NULL,
  `mat_khau` varchar(255) DEFAULT NULL COMMENT 'Cho phép NULL nếu đăng nhập bằng nền tảng khác',
  `ho_ten` varchar(255) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL COMMENT 'Ảnh đại diện',
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('NAM','NU','KHAC') DEFAULT NULL,
  `loai_tai_khoan` enum('ADMIN','MEMBER') DEFAULT 'MEMBER',
  `trang_thai` enum('ACTIVE','BLOCKED','UNVERIFIED') DEFAULT 'ACTIVE',
  `verification_token` varchar(64) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `forget_token` varchar(64) DEFAULT NULL COMMENT 'Token đặt lại mật khẩu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `supabase_id`, `auth_provider`, `email`, `mat_khau`, `ho_ten`, `sdt`, `avatar_url`, `ngay_sinh`, `gioi_tinh`, `loai_tai_khoan`, `trang_thai`, `verification_token`, `ngay_tao`, `ngay_cap_nhat`, `forget_token`) VALUES
(1, NULL, 'LOCAL', 'test_1773155576@example.com', '$2y$10$IcOj9mDvjFD1jdTaRVVY0eoywjosOpf80oNvDP3KWZqxl6TMUDTW6', 'Nguyễn Văn Test', '0901234567', NULL, NULL, 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(2, NULL, 'LOCAL', 'admin_1773155576@example.com', '$2y$10$dyvFZGKucag4pZ.RXkSQN.XTO.0tgpfouhBOIg7PyKocR2N7.uCqO', 'Admin Test', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(3, NULL, 'LOCAL', 'dat82770@gmail.com', 'cbd5140549732304f6590c5d13afb4fabd68c357', 'Trương Thành Đạt', '0399746612', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775357973/avatars/avatar_user_3.jpg', '2006-10-15', 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-28 17:19:23', '2026-04-05 04:59:34', NULL),
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
-- Table structure for table `phien_ban_san_pham`
--

CREATE TABLE `phien_ban_san_pham` (
  `id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL COMMENT 'Mã kho duy nhất',
  `ten_phien_ban` varchar(255) DEFAULT NULL COMMENT 'iPhone 16 Pro Max 256GB',
  `mau_sac` varchar(50) DEFAULT NULL COMMENT 'Đen Titan, Trắng, Xanh...',
  `thuoc_tinh_bien_the` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Lưu chuỗi JSON: {"RAM": "8GB", "Dung lượng": "256GB"} hoặc {"Công suất": "1 HP"}' CHECK (json_valid(`thuoc_tinh_bien_the`)),
  `cau_hinh` varchar(255) DEFAULT NULL COMMENT 'Mô tả cấu hình khác (nếu có)',
  `gia_ban` decimal(15,2) DEFAULT NULL COMMENT 'Giá bán hiện tại',
  `gia_goc` decimal(15,2) DEFAULT NULL COMMENT 'Giá gốc (giá gạch ngang)',
  `so_luong_ton` int(11) DEFAULT 0,
  `trang_thai` enum('CON_HANG','HET_HANG','NGUNG_BAN') DEFAULT 'CON_HANG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phien_ban_san_pham`
--

INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `thuoc_tinh_bien_the`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(10, 7, 'IP15PM-256-TITAN', 'iPhone 15 Pro Max 256GB Titan Tự Nhiên', 'Titan Tự Nhiên', NULL, NULL, 34990000.00, 34990000.00, 42, 'CON_HANG'),
(11, 7, 'IP15PM-256-TIM', 'iPhone 15 Pro Max 256GB Tím', 'Tím', NULL, NULL, 34.99, 34.99, 44, 'CON_HANG'),
(12, 2, 'samsung-galaxy-s26-12gb-256gb', 'Samsung Galaxy S26 5G 12GB 256GB', 'Đen', NULL, NULL, 20490000.00, 25990000.00, 100, 'NGUNG_BAN'),
(13, 10, 'CI1-TRANG', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP Trắng', 'Trắng', '{\"Cong_suat_Dung_tich\": \"1 HP - 9.350 BTU\"}', NULL, 9999999.00, 999999999.00, 100, 'CON_HANG'),
(14, 12, 'IP16PM-512-NAT', 'iPhone 16 Pro Max 512GB Titan Tự Nhiên', 'Titan Tự Nhiên', '{\"RAM\": \"8GB\", \"Dung lượng\": \"512GB\"}', NULL, 39990000.00, 42990000.00, 30, 'CON_HANG'),
(15, 12, 'IP16PM-512-BLK', 'iPhone 16 Pro Max 512GB Đen', 'Đen', '{\"RAM\": \"8GB\", \"Dung lượng\": \"512GB\"}', NULL, 39990000.00, 42990000.00, 25, 'CON_HANG'),
(16, 12, 'IP16PM-512-WHT', 'iPhone 16 Pro Max 512GB Trắng', 'Trắng', '{\"RAM\": \"8GB\", \"Dung lượng\": \"512GB\"}', NULL, 39990000.00, 42990000.00, 20, 'CON_HANG'),
(17, 13, 'S25U-256-BLK', 'Samsung Galaxy S25 Ultra 256GB Đen', 'Đen', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 28990000.00, 30990000.00, 40, 'CON_HANG'),
(18, 13, 'S25U-256-SLV', 'Samsung Galaxy S25 Ultra 256GB Bạc', 'Bạc', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 28990000.00, 30990000.00, 35, 'CON_HANG'),
(19, 13, 'S25U-512-BLK', 'Samsung Galaxy S25 Ultra 512GB Đen', 'Đen', '{\"RAM\": \"12GB\", \"Dung lượng\": \"512GB\"}', NULL, 31990000.00, 34990000.00, 20, 'CON_HANG'),
(20, 14, 'XM15P-256-BLK', 'Xiaomi 15 Pro 256GB Đen', 'Đen', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 16990000.00, 18990000.00, 50, 'CON_HANG'),
(21, 14, 'XM15P-256-SLV', 'Xiaomi 15 Pro 256GB Bạc', 'Bạc', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 16990000.00, 18990000.00, 45, 'CON_HANG'),
(23, 15, 'IPAD13M4-256-SLV', 'iPad Pro 13\" M4 256GB Bạc', 'Bạc', '{\"RAM\": \"8GB\", \"Dung lượng\": \"256GB\"}', NULL, 29990000.00, 32990000.00, 25, 'CON_HANG'),
(24, 15, 'IPAD13M4-512-SPC', 'iPad Pro 13\" M4 512GB Xám', 'Xám không gian', '{\"RAM\": \"8GB\", \"Dung lượng\": \"512GB\"}', NULL, 35990000.00, 38990000.00, 15, 'CON_HANG'),
(26, 16, 'TABS10U-256-GRY', 'Galaxy Tab S10 Ultra 256GB Xám', 'Xám', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 22990000.00, 25990000.00, 30, 'CON_HANG'),
(27, 16, 'TABS10U-512-SLV', 'Galaxy Tab S10 Ultra 512GB Bạc', 'Bạc', '{\"RAM\": \"12GB\", \"Dung lượng\": \"512GB\"}', NULL, 27990000.00, 30990000.00, 20, 'CON_HANG'),
(29, 17, 'PAD7P-128-BLU', 'Xiaomi Pad 7 Pro 128GB Xanh', 'Xanh', '{\"RAM\": \"8GB\", \"Dung lượng\": \"128GB\"}', NULL, 11990000.00, 13990000.00, 40, 'CON_HANG'),
(30, 17, 'PAD7P-256-SLV', 'Xiaomi Pad 7 Pro 256GB Bạc', 'Bạc', '{\"RAM\": \"12GB\", \"Dung lượng\": \"256GB\"}', NULL, 14990000.00, 16990000.00, 25, 'CON_HANG'),
(32, 18, 'MBP14M3-512-SPC', 'MacBook Pro 14\" M3 Pro 512GB Xám', 'Xám không gian', '{\"RAM\": \"18GB\", \"SSD\": \"512GB\"}', NULL, 46990000.00, 49990000.00, 20, 'CON_HANG'),
(33, 18, 'MBP14M3-1TB-SLV', 'MacBook Pro 14\" M3 Pro 1TB Bạc', 'Bạc', '{\"RAM\": \"18GB\", \"SSD\": \"1TB\"}', NULL, 52990000.00, 55990000.00, 15, 'CON_HANG'),
(35, 19, 'XPS16-32-1TB', 'Dell XPS 16 Plus (32GB/1TB)', 'Bạc', '{\"RAM\": \"32GB\", \"SSD\": \"1TB\", \"CPU\": \"Ultra 7\"}', NULL, 45990000.00, 49990000.00, 10, 'CON_HANG'),
(36, 20, 'LEG9i-32-1TB', 'Lenovo Legion 9i (32GB/1TB/RTX4090)', 'Đen', '{\"RAM\": \"32GB\", \"SSD\": \"1TB\", \"GPU\": \"RTX 4090\"}', NULL, 67990000.00, 74990000.00, 8, 'CON_HANG'),
(37, 21, 'TUF15-16-512', 'Asus TUF A15 (16GB/512GB/RTX4060)', 'Đen', '{\"RAM\": \"16GB\", \"SSD\": \"512GB\", \"GPU\": \"RTX 4060\"}', NULL, 22990000.00, 25990000.00, 25, 'CON_HANG'),
(38, 21, 'TUF15-32-1TB', 'Asus TUF A15 (32GB/1TB/RTX4060)', 'Đen', '{\"RAM\": \"32GB\", \"SSD\": \"1TB\", \"GPU\": \"RTX 4060\"}', NULL, 25990000.00, 28990000.00, 12, 'CON_HANG'),
(40, 22, 'G9-OLED-49', 'Samsung Odyssey OLED G9 49\"', 'Đen', '{\"Độ phân giải\": \"DQHD\", \"Tần số\": \"240Hz\"}', NULL, 39990000.00, 44990000.00, 15, 'CON_HANG'),
(41, 23, 'LG-5K-27', 'LG UltraFine 5K 27\"', 'Bạc', '{\"Độ phân giải\": \"5K\", \"Kết nối\": \"Thunderbolt 3\"}', NULL, 27990000.00, 30990000.00, 10, 'CON_HANG'),
(42, 24, 'VS-VX2479', 'ViewSonic VX2479 24\" 165Hz', 'Đen', '{\"Độ phân giải\": \"FHD\", \"Tần số\": \"165Hz\"}', NULL, 3990000.00, 4990000.00, 30, 'CON_HANG'),
(43, 25, 'PREC3680-64-2TB', 'Dell Precision 3680 (Xeon/64GB/2TB/A5000)', 'Đen', '{\"CPU\": \"Xeon W\", \"RAM\": \"64GB\", \"SSD\": \"2TB\"}', NULL, 78990000.00, 84990000.00, 5, 'CON_HANG'),
(44, 26, 'HP800-16-512', 'HP Elite SFF 800 G9 (i7/16GB/512GB)', 'Đen', '{\"CPU\": \"i7-13700\", \"RAM\": \"16GB\", \"SSD\": \"512GB\"}', NULL, 18990000.00, 21990000.00, 20, 'CON_HANG'),
(45, 27, 'M75s-8-256', 'Lenovo ThinkCentre M75s (Ryzen5/8GB/256GB)', 'Đen', '{\"CPU\": \"Ryzen 5 PRO\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 11990000.00, 13990000.00, 25, 'CON_HANG'),
(46, 28, 'MX3S-BLK', 'Logitech MX Master 3S Đen', 'Đen', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth\"}', NULL, 1890000.00, 2290000.00, 50, 'CON_HANG'),
(47, 28, 'MX3S-WHT', 'Logitech MX Master 3S Trắng', 'Trắng', '{\"DPI\": \"8000\", \"Kết nối\": \"Bluetooth\"}', NULL, 1890000.00, 2290000.00, 30, 'CON_HANG'),
(49, 29, 'K3P-RGB', 'Keychron K3 Pro RGB (Red switch)', 'Xám', '{\"Switch\": \"Red\", \"Layout\": \"75%\"}', NULL, 2490000.00, 2890000.00, 40, 'CON_HANG'),
(50, 30, 'XM5-BLK', 'Sony WH-1000XM5 Đen', 'Đen', '{\"ANC\": \"Có\", \"Pin\": \"30h\"}', NULL, 6990000.00, 7990000.00, 25, 'CON_HANG'),
(51, 30, 'XM5-SLV', 'Sony WH-1000XM5 Bạc', 'Bạc', '{\"ANC\": \"Có\", \"Pin\": \"30h\"}', NULL, 6990000.00, 7990000.00, 20, 'CON_HANG'),
(53, 31, 'C920-HD', 'Logitech C920 HD Pro', 'Đen', '{\"Độ phân giải\": \"1080p\", \"Mic\": \"Stereo\"}', NULL, 1690000.00, 1990000.00, 60, 'CON_HANG'),
(54, 32, 'CM-NP200', 'Cooler Master Notepal U2 Plus', 'Đen', '{\"Quạt\": \"200mm\", \"USB\": \"3 cổng\"}', NULL, 890000.00, 1090000.00, 70, 'CON_HANG'),
(55, 33, 'FPT5G-60', 'Sim FPT 5G 60GB (tháng)', NULL, '{\"Data\": \"60GB\", \"Gọi\": \"100 phút\"}', NULL, 200000.00, 250000.00, 500, 'CON_HANG'),
(56, 34, 'FPT4G-UNL', 'Sim FPT 4G không giới hạn', NULL, '{\"Data\": \"Unlimited (2Mbps)\", \"Gọi\": \"Nội mạng free\"}', NULL, 350000.00, 450000.00, 300, 'CON_HANG'),
(58, 35, 'AWU3-TI', 'Apple Watch Ultra 3 Titan (49mm)', 'Titan', '{\"Pin\": \"3 ngày\", \"Chống nước\": \"100m\"}', NULL, 18990000.00, 20990000.00, 20, 'CON_HANG'),
(59, 36, 'GW7C-43', 'Samsung Galaxy Watch7 Classic 43mm', 'Đen', '{\"Mặt xoay\": \"Có\", \"Pin\": \"425mAh\"}', NULL, 8990000.00, 9990000.00, 35, 'CON_HANG'),
(60, 37, 'FENIX8-51', 'Garmin Fenix 8 51mm Solar', 'Đen', '{\"GPS\": \"Đa tần\", \"Pin\": \"28 ngày\"}', NULL, 14990000.00, 16990000.00, 15, 'CON_HANG'),
(61, 38, '85QN900D', 'Samsung Neo QLED 8K 85 inch', 'Đen', '{\"Độ phân giải\": \"8K\", \"Tần số\": \"120Hz\"}', NULL, 129990000.00, 149990000.00, 10, 'CON_HANG'),
(62, 39, 'OLED65C4', 'LG OLED C4 65 inch', 'Đen', '{\"Công nghệ\": \"OLED evo\", \"Tần số\": \"144Hz\"}', NULL, 39990000.00, 44990000.00, 20, 'CON_HANG'),
(63, 40, 'XR55A95L', 'Sony Bravia XR A95L 55 inch', 'Đen', '{\"Công nghệ\": \"QD-OLED\", \"HDR\": \"Dolby Vision\"}', NULL, 48990000.00, 54990000.00, 12, 'CON_HANG'),
(64, 41, '55C745', 'TCL C745 55 inch Mini LED', 'Đen', '{\"Công nghệ\": \"Mini LED\", \"Tần số\": \"144Hz\"}', NULL, 14990000.00, 17990000.00, 30, 'CON_HANG'),
(65, 42, 'DAI-1.5', 'Daikin Inverter 1.5 HP', 'Trắng', '{\"Công suất\": \"1.5 HP\", \"CSPF\": \"5.0\"}', NULL, 12990000.00, 14990000.00, 25, 'CON_HANG'),
(66, 43, 'PANA-1', 'Panasonic Inverter 1 HP', 'Trắng', '{\"Công suất\": \"1 HP\", \"Nanoe X\": \"Có\"}', NULL, 8990000.00, 10990000.00, 30, 'CON_HANG'),
(67, 44, 'MID-2', 'Midea Inverter 2 HP', 'Trắng', '{\"Công suất\": \"2 HP\", \"Tiết kiệm\": \"Inverter\"}', NULL, 11990000.00, 13990000.00, 20, 'CON_HANG'),
(68, 45, 'XM-S20', 'Xiaomi Robot Hút Lau S20', 'Trắng', '{\"Lực hút\": \"4000Pa\", \"Pin\": \"5200mAh\"}', NULL, 6990000.00, 7990000.00, 40, 'CON_HANG'),
(69, 46, 'T30PRO', 'Ecovacs Deebot T30 Pro', 'Đen', '{\"AI\": \"Có\", \"Tự giặt giẻ\": \"Có\"}', NULL, 15990000.00, 18990000.00, 15, 'CON_HANG'),
(70, 47, 'J9P', 'iRobot Roomba j9+', 'Đen', '{\"Tự đổ rác\": \"Có\", \"Chải cao su\": \"Kép\"}', NULL, 18990000.00, 21990000.00, 10, 'CON_HANG'),
(71, 48, 'SHD7720', 'Quạt điều hòa Sunhouse SHD-7720', 'Trắng', '{\"Bình nước\": \"7L\", \"Công suất\": \"100W\"}', NULL, 2890000.00, 3290000.00, 50, 'CON_HANG'),
(72, 49, 'KG10A12', 'Kangaroo KG10A12', 'Trắng', '{\"Bình nước\": \"10L\", \"Remote\": \"Có\"}', NULL, 3490000.00, 3990000.00, 35, 'CON_HANG'),
(73, 50, 'F7', 'Quạt điều hòa Daikiosan F7', 'Trắng', '{\"Công suất\": \"70W\", \"Ion âm\": \"Có\"}', NULL, 2190000.00, 2590000.00, 40, 'CON_HANG'),
(74, 51, 'LG-9-AIDD', 'Máy giặt LG 9kg Inverter AI DD', 'Trắng', '{\"Công nghệ\": \"AI DD, Steam\", \"Hiệu suất\": \"A+++\"}', NULL, 8490000.00, 9990000.00, 30, 'CON_HANG'),
(75, 52, 'PANA-8', 'Panasonic Inverter 8kg', 'Trắng', '{\"Công nghệ\": \"Active Foam, Nanoe\"}', NULL, 7290000.00, 8490000.00, 25, 'CON_HANG'),
(76, 53, 'ELUX-7.5', 'Electrolux 7.5kg', 'Trắng', '{\"Công nghệ\": \"Hơi nước\"}', NULL, 5990000.00, 6990000.00, 30, 'CON_HANG'),
(77, 54, 'SAM-310', 'Tủ lạnh Samsung Inverter 310L', 'Bạc', '{\"Dung tích\": \"310L\", \"Công nghệ\": \"Metal Cooling\"}', NULL, 8990000.00, 10490000.00, 25, 'CON_HANG'),
(78, 55, 'HIT-450', 'Hitachi Inverter 450L', 'Bạc', '{\"Dung tích\": \"450L\", \"BioActive\": \"Có\"}', NULL, 15990000.00, 17990000.00, 15, 'CON_HANG'),
(79, 56, 'PANA-220', 'Panasonic Inverter 220L', 'Trắng', '{\"Dung tích\": \"220L\", \"Ngăn đá\": \"Trên\"}', NULL, 6490000.00, 7490000.00, 20, 'CON_HANG'),
(80, 57, 'KG11A6', 'Kangaroo 11 lõi Hydrogen nóng lạnh', 'Trắng', '{\"Số lõi\": \"11\", \"Dung tích bình\": \"11L\"}', NULL, 12990000.00, 15990000.00, 20, 'CON_HANG'),
(81, 58, 'KAF-IX6', 'Karofi KAF-IX6', 'Bạc', '{\"Công nghệ\": \"IX6\", \"Lõi\": \"6\"}', NULL, 7990000.00, 9990000.00, 30, 'CON_HANG'),
(82, 59, 'SHR6900', 'Sunhouse SHR-6900', 'Trắng', '{\"Lõi lọc\": \"5\", \"Bình chứa\": \"8L\"}', NULL, 4590000.00, 5590000.00, 40, 'CON_HANG'),
(83, 60, '12PM-LN', 'iPhone 12 Pro Max 256GB Like New', 'Xanh', '{\"Tình trạng\": \"Like new\", \"Pin\": \"90%\"}', NULL, 11990000.00, 13990000.00, 15, 'CON_HANG'),
(84, 61, 'LAT5410', 'Dell Latitude 5410 cũ (i5/8GB/256GB)', 'Đen', '{\"CPU\": \"i5\", \"RAM\": \"8GB\", \"SSD\": \"256GB\"}', NULL, 6990000.00, 8990000.00, 20, 'CON_HANG'),
(85, 62, 'ELUX-DRY8', 'Máy sấy Electrolux 8kg bơm nhiệt', 'Trắng', '{\"Công nghệ\": \"Bơm nhiệt\", \"Cảm biến\": \"Có\"}', NULL, 11990000.00, 13990000.00, 20, 'CON_HANG'),
(86, 63, 'PANA-DRY7', 'Panasonic 7kg bơm nhiệt', 'Trắng', '{\"Công nghệ\": \"Nanoe X\", \"Bơm nhiệt\": \"Có\"}', NULL, 9990000.00, 11990000.00, 15, 'CON_HANG'),
(87, 64, 'IMOU-C2', 'Camera Imou Cue 2 (2MP)', 'Trắng', '{\"Độ phân giải\": \"1080p\", \"Xoay\": \"355°\"}', NULL, 890000.00, 1090000.00, 60, 'CON_HANG'),
(88, 65, 'XM-AW300', 'Xiaomi Outdoor AW300', 'Trắng', '{\"Độ phân giải\": \"2K\", \"IP67\": \"Có\"}', NULL, 1290000.00, 1590000.00, 40, 'CON_HANG'),
(89, 66, 'C320WS', 'TP-Link Tapo C320WS', 'Trắng', '{\"Độ phân giải\": \"2K\", \"Hồng ngoại\": \"30m\"}', NULL, 990000.00, 1290000.00, 50, 'CON_HANG'),
(90, 67, 'GC3672', 'Bàn ủi hơi nước Philips GC3672', 'Xanh', '{\"Công suất\": \"2400W\", \"Đế\": \"Chống dính\"}', NULL, 1490000.00, 1790000.00, 50, 'CON_HANG'),
(91, 68, 'V12-DETECT', 'Dyson V12 Detect Slim', 'Vàng', '{\"Pin\": \"60 phút\", \"Lọc\": \"HEPA\"}', NULL, 15990000.00, 18990000.00, 20, 'CON_HANG'),
(92, 69, 'EC221', 'Delonghi EC221', 'Đen', '{\"Áp suất\": \"15 bar\", \"Tạo bọt sữa\": \"Có\"}', NULL, 3490000.00, 3990000.00, 30, 'CON_HANG'),
(93, 70, 'SHARP20L', 'Lò vi sóng Sharp 20L cơ', 'Trắng', '{\"Dung tích\": \"20L\", \"Công suất\": \"700W\"}', NULL, 1890000.00, 2190000.00, 40, 'CON_HANG'),
(94, 71, 'SF6600', 'Quạt cây Senko SF-6600', 'Xanh', '{\"Cánh\": \"3\", \"Tốc độ\": \"3\"}', NULL, 790000.00, 990000.00, 80, 'CON_HANG'),
(95, 72, 'SHD7708', 'Quạt tích điện Sunhouse SHD7708', 'Trắng', '{\"Pin\": \"6 giờ\", \"Cánh\": \"4\"}', NULL, 890000.00, 1090000.00, 60, 'CON_HANG'),
(96, 73, 'XM-SF', 'Xiaomi Smart Fan', 'Trắng', '{\"Điều khiển\": \"App\", \"Cánh\": \"7\"}', NULL, 1290000.00, 1590000.00, 45, 'CON_HANG'),
(97, 74, 'XM-4PRO', 'Xiaomi Air Purifier 4 Pro', 'Trắng', '{\"CADR\": \"500\", \"HEPA\": \"Có\"}', NULL, 3990000.00, 4590000.00, 35, 'CON_HANG'),
(98, 75, 'COWAY1512', 'Coway AP-1512HH', 'Trắng', '{\"Diện tích\": \"30m²\", \"HEPA\": \"Có\"}', NULL, 5990000.00, 6990000.00, 20, 'CON_HANG'),
(99, 76, 'SHARP-FM40', 'Sharp FP-FM40E', 'Trắng', '{\"Plasma Cluster\": \"Có\", \"Cảm biến\": \"Bụi\"}', NULL, 4490000.00, 5290000.00, 25, 'CON_HANG'),
(100, 77, 'BOSCH-PUE', 'Bếp từ đôi Bosch PUE611BB1E', 'Đen', '{\"Vùng nấu\": \"2\", \"Công suất\": \"4600W\"}', NULL, 8990000.00, 10490000.00, 15, 'CON_HANG'),
(101, 78, 'SANAKY38', 'Lò nướng thùng thủy tinh Sanaky 38L', 'Đen', '{\"Dung tích\": \"38L\", \"Công suất\": \"1500W\"}', NULL, 1990000.00, 2490000.00, 30, 'CON_HANG'),
(102, 79, 'TEKA650', 'Máy rửa bát Teka DBI 650', 'Bạc', '{\"Bộ\": \"13\", \"Sấy khô\": \"Có\"}', NULL, 13990000.00, 16990000.00, 12, 'CON_HANG'),
(103, 80, 'RK6021', 'Nồi cơm điện Tefal RK6021', 'Đen', '{\"Dung tích\": \"1.5L\", \"Công suất\": \"650W\"}', NULL, 1490000.00, 1790000.00, 40, 'CON_HANG'),
(104, 81, 'CUCKOO-P06', 'Cuckoo CRP-P0609F (áp suất)', 'Đỏ', '{\"Dung tích\": \"1.5L\", \"Áp suất\": \"Có\"}', NULL, 3890000.00, 4590000.00, 20, 'CON_HANG'),
(105, 82, 'SR-DF181', 'Panasonic SR-DF181', 'Trắng', '{\"Dung tích\": \"1.8L\", \"Chế độ\": \"5\"}', NULL, 1790000.00, 2090000.00, 35, 'CON_HANG'),
(106, 83, 'HR2041', 'Máy xay sinh tố Philips HR2041', 'Trắng', '{\"Cối\": \"2L\", \"Công suất\": \"800W\"}', NULL, 1490000.00, 1790000.00, 30, 'CON_HANG'),
(107, 84, 'C7000', 'Máy ép chậm Kuvings C7000', 'Bạc', '{\"Công nghệ\": \"Ép chậm\", \"Tốc độ\": \"60 vòng/phút\"}', NULL, 7990000.00, 9990000.00, 15, 'CON_HANG'),
(108, 85, 'MQ5235', 'Máy xay cầm tay Braun MQ5235', 'Đen', '{\"Công suất\": \"750W\", \"Tốc độ\": \"12\"}', NULL, 1690000.00, 1990000.00, 40, 'CON_HANG'),
(109, 86, 'HD9867', 'Nồi chiên không dầu Philips HD9867', 'Đen', '{\"Dung tích\": \"7.3L\", \"Công suất\": \"2225W\"}', NULL, 6990000.00, 8490000.00, 25, 'CON_HANG'),
(110, 87, 'EJD919', 'Lock&Lock EJD-919 5L', 'Đen', '{\"Dung tích\": \"5L\", \"Công suất\": \"1500W\"}', NULL, 1890000.00, 2290000.00, 50, 'CON_HANG'),
(111, 88, 'SHD1831', 'Sunhouse SHD1831 4.5L', 'Đen', '{\"Dung tích\": \"4.5L\", \"Màn hình\": \"Cảm ứng\"}', NULL, 1590000.00, 1890000.00, 45, 'CON_HANG');

-- --------------------------------------------------------

--
-- Table structure for table `refund`
--

CREATE TABLE `refund` (
  `id` int(11) NOT NULL,
  `thanh_toan_id` int(11) NOT NULL COMMENT 'ID giao dịch thanh toán gốc',
  `gateway_refund_id` varchar(255) DEFAULT NULL COMMENT 'ID hoàn tiền từ cổng thanh toán',
  `amount` decimal(15,2) NOT NULL COMMENT 'Số tiền hoàn',
  `status` enum('PENDING','COMPLETED','FAILED') DEFAULT 'PENDING',
  `reason` text DEFAULT NULL COMMENT 'Lý do hoàn tiền',
  `created_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `san_pham`
--

CREATE TABLE `san_pham` (
  `id` int(11) NOT NULL,
  `danh_muc_id` int(11) DEFAULT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL COMMENT 'URL: iphone-16-pro-max',
  `hang_san_xuat` varchar(100) DEFAULT NULL COMMENT 'Apple, Samsung, Xiaomi...',
  `mo_ta` text DEFAULT NULL,
  `gia_hien_thi` decimal(15,2) DEFAULT NULL COMMENT 'Giá "từ" hiển thị (giá thấp nhất phiên bản)',
  `diem_danh_gia` float DEFAULT 0,
  `trang_thai` enum('CON_BAN','NGUNG_BAN','SAP_RA_MAT','HET_HANG') DEFAULT 'CON_BAN',
  `noi_bat` tinyint(1) DEFAULT 0 COMMENT '1 = hiện trên banner/trang chủ',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten_san_pham`, `slug`, `hang_san_xuat`, `mo_ta`, `gia_hien_thi`, `diem_danh_gia`, `trang_thai`, `noi_bat`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'IPhone', 'iphone', 'Apple', 'Smartphone', 999.00, 0, 'NGUNG_BAN', 0, '2026-03-30 20:51:28', '2026-04-06 19:28:28'),
(2, 1, 'Điện thoại Samsung', 'sam-sung', 'Samsung', 'Điện thoại thông minh', NULL, 0, 'NGUNG_BAN', 0, '2026-04-02 14:47:33', '2026-04-07 09:42:51'),
(7, 1, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'Apple', 'Siêu phẩm Apple với khung viền Titan cao cấp, chip A17 Pro mạnh mẽ.', 34990000.00, 5, 'CON_BAN', 1, '2026-04-04 09:59:16', '2026-04-04 09:59:16'),
(8, 3, 'Laptop Lenovo ThinkPad X1 Carbon Gen 13 U7 258V/AI/32GB/1TB/14\"OLED 2.8K/W11PRO (21NS010JVN)', 'lenovo-thinkpad-x1-carbon-gen-13-u7-258v-21ns010jvn', 'Lenovo', 'Lenovo ThinkPad X1 Carbon Gen 13 là chiếc laptop doanh nhân cao cấp dành cho những người cần một thiết bị vừa mạnh mẽ, lại vừa siêu nhẹ để dễ dàng mang theo. Sở hữu bộ vi xử lý AI Intel Core Ultra 7 258V đầu bảng, màn hình OLED 14 inch sắc nét nhưng với thiết kế sợi Carbon, ThinkPad X1 Carbon Gen 13 chỉ có trọng lượng vỏn vẹn 1 kg, cho khả năng di động bậc nhất hiện nay.', NULL, 0, 'NGUNG_BAN', 1, '2026-04-06 20:04:49', '2026-04-07 09:42:55'),
(9, 6, 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka', 'op-lung-magsafe-samsung-s26-ultra-ultra-slim-with-pitatap-moonrise-pitaka', 'Samsung', 'Ốp lưng Magsafe Samsung S26 Ultra Ultra-Slim with PitaTap Moonrise Pitaka là sự kết hợp giữa nghệ thuật chế tác và công nghệ tối ưu trải nghiệm. Thiết kế Moonrise nổi bật với hiệu ứng chuyển sắc độc đáo trên nền sợi Aramid cao cấp. Sản phẩm ôm sát thân máy, duy trì vẻ nguyên bản của Galaxy S26 Ultra. Đồng thời, PitaTap cùng Aaron Button mở ra cách tương tác hoàn toàn mới, nhanh gọn và chính xác.', NULL, 0, 'NGUNG_BAN', 0, '2026-04-06 20:10:35', '2026-04-07 09:42:57'),
(10, 10, 'Máy lạnh Comfee Inverter 1 HP CFS-10VGP', 'comfee-inverter-1-hp-cfs-10vgpf', 'Inverter', 'Máy lạnh Comfee Inverter 1 HP CFS-10VGPF hỗ trợ làm lạnh hiệu quả và mang lại sự tiện lợi cho người dùng. Với công suất 1 HP, thiết bị này phù hợp với các căn phòng có diện tích dưới 15m². Ngoài thiết kế tinh tế, sang trọng, máy còn tích hợp nhiều tính năng thông minh như kết nối với hệ sinh thái nhà thông minh, điều khiển bằng giọng nói và các chế độ tiết kiệm điện hiệu quả', NULL, 0, 'CON_BAN', 0, '2026-04-08 19:10:33', '2026-04-08 19:10:33'),
(11, 15, 'Máy lọc nước nóng lạnh RO Hydrogen Kangaroo KG11A6 11 lõi', 'may-loc-nuoc-nong-lanh-ro-hydrogen-kangaroo-11-loi-kg11a6', 'Kangaroo', 'Máy lọc nước Kangaroo Hydrogen nóng lạnh KG11A6 là dòng máy lọc nước vừa ra mắt trong năm 2024 thuộc thương hiệu Kangaroo. Do đó, những tinh hoa công nghệ trong việc đầu tư và thiết kế hệ thống siêu lõi lọc làm tăng hiệu năng lọc nước hơn bao giờ hết, không chỉ loại bỏ chất bẩn mà còn bù khoáng cho cơ thể', NULL, 0, 'CON_BAN', 0, '2026-04-08 21:30:56', '2026-04-08 21:30:56'),
(12, 1, 'iPhone 16 Pro Max 512GB', 'iphone-16-pro-max-512gb', 'Apple', 'Chip A18 Pro, màn hình 6.9 inch, camera 48MP', 39990000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(13, 1, 'Samsung Galaxy S25 Ultra 5G', 'samsung-galaxy-s25-ultra', 'Samsung', 'Snapdragon 8 Gen 4, pin 5000mAh, S Pen tích hợp', 28990000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(14, 1, 'Xiaomi 15 Pro', 'xiaomi-15-pro', 'Xiaomi', 'Chip Snapdragon 8 Gen 4, camera Leica 50MP', 16990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(15, 2, 'iPad Pro 13 inch M4', 'ipad-pro-13-m4', 'Apple', 'Chip M4, màn hình Ultra Retina XDR, hỗ trợ Apple Pencil Pro', 29990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(16, 2, 'Samsung Galaxy Tab S10 Ultra', 'samsung-tab-s10-ultra', 'Samsung', 'Màn hình 14.6 inch Dynamic AMOLED, S Pen đi kèm', 22990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(17, 2, 'Xiaomi Pad 7 Pro', 'xiaomi-pad-7-pro', 'Xiaomi', 'Màn hình 12.4 inch 144Hz, Snapdragon 8+ Gen 2', 11990000.00, 4.4, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(18, 3, 'MacBook Pro 14 inch M3 Pro', 'macbook-pro-14-m3-pro', 'Apple', 'Chip M3 Pro (11 core CPU, 14 core GPU), 18GB RAM, 512GB SSD', 46990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(19, 3, 'Dell XPS 16 Plus', 'dell-xps-16-plus', 'Dell', 'Intel Core Ultra 7, 32GB RAM, 1TB SSD, RTX 4060', 45990000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(20, 3, 'Lenovo Legion 9i', 'lenovo-legion-9i', 'Lenovo', 'Intel Core i9-14900HX, 32GB RAM, 1TB SSD, RTX 4090', 67990000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(21, 3, 'Asus TUF Gaming A15', 'asus-tuf-a15', 'Asus', 'Ryzen 7 7840HS, 16GB RAM, 512GB SSD, RTX 4060', 22990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(22, 4, 'Samsung Odyssey OLED G9', 'samsung-odyssey-g9', 'Samsung', 'Màn hình cong 49 inch, 240Hz, 0.03ms, HDR True Black 400', 39990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(23, 4, 'LG UltraFine 5K 27 inch', 'lg-ultrafine-5k', 'LG', '27 inch 5K, P3 màu 99%, cổng Thunderbolt 3', 27990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(24, 4, 'ViewSonic VX2479', 'viewsonic-vx2479', 'ViewSonic', '24 inch IPS, 165Hz, 1ms, độ phủ 99% sRGB', 3990000.00, 4.3, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(25, 5, 'Dell Precision 3680', 'dell-precision-3680', 'Dell', 'Intel Xeon W, 64GB ECC RAM, 2TB SSD, RTX A5000', 78990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(26, 5, 'HP Elite SFF 800 G9', 'hp-elite-sff-800-g9', 'HP', 'Intel Core i7-13700, 16GB RAM, 512GB SSD, Win 11 Pro', 18990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(27, 5, 'Lenovo ThinkCentre M75s', 'lenovo-thinkcentre-m75s', 'Lenovo', 'AMD Ryzen 5 PRO, 8GB RAM, 256GB SSD, TPM 2.0', 11990000.00, 4.4, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(28, 6, 'Logitech MX Master 3S', 'logitech-mx-master-3s', 'Logitech', 'Chuột không dây, cảm biến 8K DPI, pin 70 ngày', 1890000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(29, 6, 'Bàn phím cơ Keychron K3 Pro', 'keychron-k3-pro', 'Keychron', '75% layout, switch Low Profile, RGB, Bluetooth', 2490000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(30, 6, 'Tai nghe Sony WH-1000XM5', 'sony-wh-1000xm5', 'Sony', 'Chống ồn chủ động, pin 30 giờ, codec LDAC', 6990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(31, 6, 'Webcam Logitech C920 HD Pro', 'logitech-c920', 'Logitech', '1080p 30fps, 2 mic stereo, kẹp chắc chắn', 1690000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(32, 6, 'Đế tản nhiệt laptop Cooler Master', 'cooler-master-notepal', 'Cooler Master', 'Quạt 200mm, 3 cổng USB, kích thước lên tới 17 inch', 890000.00, 4.4, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(33, 7, 'Sim FPT 5G Data 60GB', 'sim-fpt-5g-60gb', 'FPT', '60GB data tốc độ cao, gọi nội mạng 100 phút, 1 tháng', 200000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(34, 7, 'Sim FPT 4G không giới hạn', 'sim-fpt-unlimited', 'FPT', 'Data không giới hạn (tốc độ 2Mbps), gọi nội mạng miễn phí', 350000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(35, 8, 'Apple Watch Ultra 3', 'apple-watch-ultra-3', 'Apple', '49mm titan, màn hình luôn bật, pin lên tới 3 ngày', 18990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(36, 8, 'Samsung Galaxy Watch7 Classic', 'samsung-galaxy-watch7', 'Samsung', 'Mặt số xoay, theo dõi sức khỏe toàn diện, pin 425mAh', 8990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(37, 8, 'Garmin Fenix 8', 'garmin-fenix-8', 'Garmin', 'GPS đa tần, chống nước 10ATM, pin 28 ngày', 14990000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(38, 9, 'Samsung Neo QLED 8K 85 inch', 'samsung-neo-qled-8k-85', 'Samsung', '85 inch, 8K, AI upscaling, 120Hz, Object Tracking Sound Pro', 129990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(39, 9, 'LG OLED C4 65 inch', 'lg-oled-c4-65', 'LG', '65 inch OLED evo, 144Hz, hỗ trợ G-Sync, webOS 24', 39990000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(40, 9, 'Sony Bravia XR A95L 55 inch', 'sony-bravia-a95l', 'Sony', '55 inch QD-OLED, 4K HDR, Cognitive Processor XR', 48990000.00, 4.9, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(41, 9, 'TCL C745 55 inch Mini LED', 'tcl-c745', 'TCL', '55 inch Mini LED, 4K, 144Hz, Google TV', 14990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(42, 10, 'Daikin Inverter 1.5 HP', 'daikin-inverter-1-5hp', 'Daikin', 'Tiết kiệm điện, lọc không khí, vận hành êm', 12990000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(43, 10, 'Panasonic Inverter 1 HP', 'panasonic-inverter-1hp', 'Panasonic', 'Công nghệ nanoe X, làm lạnh nhanh, tiêu chuẩn Nhật', 8990000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(44, 10, 'Midea Inverter 2 HP', 'midea-inverter-2hp', 'Midea', 'Tiết kiệm điện, dễ lắp đặt, điều khiển từ xa', 11990000.00, 4.4, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(45, 11, 'Robot hút bụi lau nhà Xiaomi S20', 'xiaomi-robot-s20', 'Xiaomi', 'Hút và lau, định vị laser, tự động trạm', 6990000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(46, 11, 'Ecovacs Deebot T30 Pro', 'ecovacs-t30-pro', 'Ecovacs', 'AI object detection, tự giặt giẻ, lau rung 4000 lần/phút', 15990000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(47, 11, 'Roomba j9+', 'roomba-j9-plus', 'iRobot', 'Tự động đổ rác, lông thú cưng, chải kép cao su', 18990000.00, 4.9, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(48, 12, 'Quạt điều hòa Sunhouse SHD-7720', 'sunhouse-shd7720', 'Sunhouse', 'Làm mát bằng hơi nước, bình 7L, công suất 100W', 2890000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(49, 12, 'Kangaroo KG10A12', 'kangaroo-kg10a12', 'Kangaroo', '3 chế độ gió, bình 10L, điều khiển từ xa', 3490000.00, 4.6, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(50, 12, 'Quạt điều hòa Daikiosan F7', 'daikiosan-f7', 'Daikiosan', 'Công suất 70W, lọc bụi, phun sương ion âm', 2190000.00, 4.4, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(51, 13, 'Máy giặt LG Inverter 9kg', 'lg-inverter-9kg', 'LG', 'Công nghệ AI DD, Inverter, khử khuẩn Steam', 8490000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(52, 13, 'Panasonic Inverter 8kg', 'panasonic-inverter-8kg', 'Panasonic', 'Active Foam, Inverter, kháng khuẩn Nanoe', 7290000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(53, 13, 'Electrolux 7.5kg', 'electrolux-7-5kg', 'Electrolux', 'Công nghệ giặt hơi nước, tiết kiệm điện', 5990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(54, 14, 'Tủ lạnh Samsung Inverter 310L', 'samsung-inverter-310l', 'Samsung', 'Ngăn đá trên, Digital Inverter, Metal Cooling', 8990000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(55, 14, 'Hitachi Inverter 450L', 'hitachi-inverter-450l', 'Hitachi', 'Ngăn đá dưới, công nghệ BioActive, tiết kiệm A+++', 15990000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(56, 14, 'Panasonic Inverter 220L', 'panasonic-inverter-220l', 'Panasonic', 'Nhỏ gọn, ngăn đá trên, Inverter', 6490000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(57, 15, 'Máy lọc nước RO Kangaroo 11 lõi', 'kangaroo-ro-11loi', 'Kangaroo', 'Lọc RO, Hydrogen, nóng lạnh, dung tích 11L', 12990000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(58, 15, 'Karofi KAF-IX6', 'karofi-kaf-ix6', 'Karofi', 'Công nghệ IX6, 6 lõi lọc, vỏ inox', 7990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(59, 15, 'Sunhouse SHR-6900', 'sunhouse-shr6900', 'Sunhouse', 'Lọc RO, 5 lõi, bình chứa 8L', 4590000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(60, 16, 'iPhone 12 Pro Max 256GB Like New', 'iphone-12-pro-max-like-new', 'Apple', 'Máy chính hãng VN/A, pin 90%, bảo hành 6 tháng', 11990000.00, 4.4, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(61, 16, 'Laptop Dell Latitude 5410 cũ', 'dell-latitude-5410-old', 'Dell', 'Core i5, 8GB RAM, 256GB SSD, màn 14 inch, mới đẹp', 6990000.00, 4.3, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(62, 26, 'Máy sấy quần áo Electrolux 8kg', 'electrolux-dryer-8kg', 'Electrolux', 'Công nghệ sấy bơm nhiệt, khử khuẩn, cảm biến độ ẩm', 11990000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(63, 26, 'Panasonic 7kg bơm nhiệt', 'panasonic-dryer-7kg', 'Panasonic', 'Sấy êm, tiết kiệm điện, công nghệ Nanoe X', 9990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(64, 27, 'Camera WiFi Imou Cue 2', 'imou-cue-2', 'Imou', '2MP, xoay 360°, hồng ngoại, phát hiện chuyển động', 890000.00, 4.6, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(65, 27, 'Xiaomi Outdoor Camera AW300', 'xiaomi-aw300', 'Xiaomi', 'Chống nước IP67, 2K, AI nhận diện người', 1290000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(66, 27, 'TP-Link Tapo C320WS', 'tapo-c320ws', 'TP-Link', '2K QHD, hồng ngoại 30m, âm thanh 2 chiều', 990000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(67, 28, 'Bàn ủi hơi nước Philips GC3672', 'philips-gc3672', 'Philips', 'Công suất 2400W, đế chống dính, tự ngắt an toàn', 1490000.00, 4.6, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(68, 28, 'Máy hút bụi cầm tay Dyson V12', 'dyson-v12', 'Dyson', 'Hút bụi mạnh mẽ, pin 60 phút, lọc HEPA', 15990000.00, 4.9, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(69, 28, 'Máy pha cà phê Delonghi EC221', 'delonghi-ec221', 'Delonghi', 'Pha cà phê Ý, 15 bar, tạo bọi sữa', 3490000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(70, 28, 'Lò vi sóng Sharp 20L', 'sharp-microwave-20l', 'Sharp', '20 lít, 5 mức công suất, nút xoay cơ', 1890000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(71, 29, 'Quạt cây Senko SF-6600', 'senko-sf6600', 'Senko', 'Quạt cây 3 cánh, 3 tốc độ, điều khiển từ xa', 790000.00, 4.6, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(72, 29, 'Quạt tích điện Sunhouse SHD7708', 'sunhouse-shd7708', 'Sunhouse', 'Quạt sạc dự phòng, 4 cánh, chạy 6 giờ', 890000.00, 4.5, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(73, 29, 'Quạt bàn Xiaomi Smart', 'xiaomi-smart-fan', 'Xiaomi', 'Điều khiển app, 7 cánh, siêu êm', 1290000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(74, 30, 'Máy lọc không khí Xiaomi 4 Pro', 'xiaomi-air-purifier-4pro', 'Xiaomi', 'Lọc HEPA, CADR 500m³/h, màn hình OLED', 3990000.00, 4.8, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(75, 30, 'Coway AP-1512HH', 'coway-ap1512hh', 'Coway', 'Màng lọc HEPA, kháng khuẩn, phù hợp 30m²', 5990000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(76, 30, 'Sharp FP-FM40E', 'sharp-fpfm40e', 'Sharp', 'Công nghệ Plasma Cluster, cảm biến bụi', 4490000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(77, 31, 'Bếp từ đôi Bosch PUE611BB1E', 'bosch-pue611bb1e', 'Bosch', '2 vùng nấu, công suất 4600W, mặt kính Schott Ceran', 8990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(78, 31, 'Lò nướng thùng thủy tinh Sanaky 38L', 'sanaky-38l', 'Sanaky', '38 lít, 1500W, hẹn giờ, đèn bên trong', 1990000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(79, 31, 'Máy rửa bát Teka DBI 650', 'teka-dbi650', 'Teka', '13 bộ, sấy khô, rửa bằng muối, tiết kiệm nước', 13990000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(80, 32, 'Nồi cơm điện Tefal RK6021', 'tefal-rk6021', 'Tefal', 'Dung tích 1.5L, công suất 650W, lòng chống dính', 1490000.00, 4.7, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(81, 32, 'Cuckoo CRP-P0609F', 'cuckoo-crp-p0609f', 'Cuckoo', 'Áp suất cao, nấu gạo tẻ/đen, màn hình LED', 3890000.00, 4.8, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(82, 32, 'Panasonic SR-DF181', 'panasonic-sr-df181', 'Panasonic', 'Dung tích 1.8L, 5 chế độ nấu, giữ ấm 24h', 1790000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(83, 33, 'Máy xay sinh tố Philips HR2041', 'philips-hr2041', 'Philips', 'Cối 2L, 800W, lưỡi dao thép không gỉ', 1490000.00, 4.6, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(84, 33, 'Máy ép chậm Kuvings C7000', 'kuvings-c7000', 'Kuvings', 'Công nghệ ép chậm 60 vòng/phút, lắp ráp nhanh', 7990000.00, 4.9, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(85, 33, 'Máy xay cầm tay Braun MQ5235', 'braun-mq5235', 'Braun', 'Công suất 750W, 12 tốc độ, kèm cối xay', 1690000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(86, 34, 'Nồi chiên không dầu Philips HD9867', 'philips-hd9867', 'Philips', 'Dung tích 7.3L, công nghệ Rapid Air, giảm 90% dầu mỡ', 6990000.00, 4.9, 'CON_BAN', 1, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(87, 34, 'Lock&Lock EJD-919', 'locknlock-ejd919', 'Lock&Lock', 'Dung tích 5L, 1500W, 8 chế độ nấu', 1890000.00, 4.7, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45'),
(88, 34, 'Sunhouse SHD1831', 'sunhouse-shd1831', 'Sunhouse', 'Dung tích 4.5L, màn hình cảm ứng, hẹn giờ', 1590000.00, 4.6, 'CON_BAN', 0, '2026-04-13 15:31:45', '2026-04-13 15:31:45');

-- --------------------------------------------------------

--
-- Table structure for table `san_pham_khuyen_mai`
--

CREATE TABLE `san_pham_khuyen_mai` (
  `san_pham_id` int(11) NOT NULL,
  `khuyen_mai_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `san_pham_khuyen_mai`
--

INSERT INTO `san_pham_khuyen_mai` (`san_pham_id`, `khuyen_mai_id`) VALUES
(2, 2),
(7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) NOT NULL,
  `nguoi_duyet_id` int(11) DEFAULT NULL COMMENT 'Admin duyệt thanh toán',
  `gateway_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo) for idempotency (Req 8.5)',
  `gateway_name` varchar(50) DEFAULT NULL COMMENT 'Payment gateway identifier (VNPAY, MOMO, COD)',
  `expiration_time` datetime DEFAULT NULL COMMENT 'Transaction expiration timestamp (15 minutes from creation) (Req 6.1)',
  `payment_url` text DEFAULT NULL COMMENT 'Payment URL generated by gateway for customer redirect',
  `error_code` varchar(50) DEFAULT NULL COMMENT 'Error code from payment gateway (Req 7.6)',
  `error_message` text DEFAULT NULL COMMENT 'User-friendly error message in Vietnamese (Req 7.6)',
  `phuong_thuc` enum('COD','CHUYEN_KHOAN','QR','TRA_GOP','VI_DIEN_TU','ZALOPAY') DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `anh_bien_lai` varchar(500) DEFAULT NULL COMMENT 'URL ảnh biên lai chuyển khoản',
  `trang_thai_duyet` enum('CHO_DUYET','THANH_CONG','THAT_BAI','HOAN_TIEN') DEFAULT 'CHO_DUYET',
  `ghi_chu_duyet` text DEFAULT NULL COMMENT 'Admin ghi chú khi duyệt',
  `ngay_thanh_toan` datetime DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanh_toan`
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
(21, 22, NULL, NULL, 'ZALOPAY', '2026-04-11 16:53:35', NULL, NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:38:35', NULL),
(22, 23, NULL, NULL, 'ZALOPAY', '2026-04-11 16:56:52', NULL, NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:41:52', NULL),
(23, 24, NULL, NULL, 'ZALOPAY', '2026-04-11 16:59:22', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ3Faam9aNHViUGJGSUtrMDluaEVxdmciLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:44:22', NULL),
(24, 25, NULL, NULL, 'ZALOPAY', '2026-04-11 17:09:48', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0pSMUdXeW1VUGNhU1hpc2pwNlNXMlEiLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-11 16:54:48', NULL),
(25, 26, NULL, NULL, 'ZALOPAY', '2026-04-12 14:00:06', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0FYZFJNbnBncFhUanhQN0w4akx4dmciLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 13:45:06', NULL),
(26, 27, NULL, NULL, 'ZALOPAY', '2026-04-12 14:15:34', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ0VWdHRSVS1iZExwZnRkaEZNZ1Fyb3ciLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:00:34', NULL),
(27, 28, NULL, NULL, 'ZALOPAY', '2026-04-12 14:19:21', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQzlrTW04UWR6a3hLRXNHWC00SkUxV1EiLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:04:21', NULL),
(28, 29, NULL, NULL, 'ZALOPAY', '2026-04-12 14:21:35', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQzA3Nk5mSFpXcEt4eDFIdDlURmNDd2ciLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:06:35', NULL),
(29, 30, NULL, NULL, 'ZALOPAY', '2026-04-12 14:24:09', 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ202NlZNRG5tb1FocWZQTGZYRzgyS3ciLCJhcHBpZCI6MjU1M30=', NULL, NULL, 'ZALOPAY', 30034.99, NULL, 'CHO_DUYET', NULL, '2026-04-12 14:09:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thong_so_ky_thuat`
--

CREATE TABLE `thong_so_ky_thuat` (
  `id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `ten_thong_so` varchar(100) DEFAULT NULL COMMENT 'Ram, Chip, Pin, Màn hình...',
  `gia_tri` varchar(255) DEFAULT NULL COMMENT '8GB, A18 Pro, 5000mAh...',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thong_so_ky_thuat`
--

INSERT INTO `thong_so_ky_thuat` (`id`, `san_pham_id`, `ten_thong_so`, `gia_tri`, `thu_tu`) VALUES
(13, 7, 'Màn hình', '6.7 inch', 0),
(14, 12, 'Màn hình', '6.9 inch Super Retina XDR (2796x1290)', 1),
(15, 12, 'Chip', 'Apple A18 Pro (3nm)', 2),
(16, 12, 'RAM', '8GB', 3),
(17, 12, 'Dung lượng lưu trữ', '512GB', 4),
(18, 12, 'Camera sau', '48MP chính + 12MP góc siêu rộng + 12MP tele', 5),
(19, 12, 'Camera trước', '12MP TrueDepth', 6),
(20, 12, 'Pin', '4676 mAh', 7),
(21, 12, 'Hệ điều hành', 'iOS 18', 8),
(29, 13, 'Màn hình', '6.9 inch Dynamic AMOLED 2X, 120Hz', 1),
(30, 13, 'Chip', 'Snapdragon 8 Gen 4 for Galaxy', 2),
(31, 13, 'RAM', '12GB', 3),
(32, 13, 'Dung lượng lưu trữ', '256GB/512GB/1TB', 4),
(33, 13, 'Camera sau', '200MP chính + 50MP tele + 50MP siêu rộng', 5),
(34, 13, 'Camera trước', '12MP', 6),
(35, 13, 'Pin', '5000 mAh, sạc nhanh 45W', 7),
(36, 13, 'Hệ điều hành', 'Android 15 (One UI 7)', 8),
(44, 14, 'Màn hình', '6.73 inch AMOLED 120Hz, 3200x1440', 1),
(45, 14, 'Chip', 'Snapdragon 8 Gen 4', 2),
(46, 14, 'RAM', '12GB/16GB', 3),
(47, 14, 'Dung lượng lưu trữ', '256GB/512GB', 4),
(48, 14, 'Camera sau', '50MP Leica + 50MP tele + 50MP siêu rộng', 5),
(49, 14, 'Camera trước', '32MP', 6),
(50, 14, 'Pin', '5200 mAh, sạc nhanh 120W', 7),
(51, 14, 'Hệ điều hành', 'HyperOS 2.0 (Android 15)', 8),
(59, 15, 'Màn hình', '13 inch Ultra Retina XDR (2752x2064)', 1),
(60, 15, 'Chip', 'Apple M4 (10 core CPU, 10 core GPU)', 2),
(61, 15, 'RAM', '8GB/16GB', 3),
(62, 15, 'Dung lượng lưu trữ', '256GB/512GB/1TB/2TB', 4),
(63, 15, 'Camera sau', '12MP + LiDAR', 5),
(64, 15, 'Camera trước', '12MP Ultra Wide', 6),
(65, 15, 'Pin', 'Lên đến 10 giờ', 7),
(66, 15, 'Hệ điều hành', 'iPadOS 18', 8),
(74, 16, 'Màn hình', '14.6 inch Dynamic AMOLED 2X, 120Hz', 1),
(75, 16, 'Chip', 'MediaTek Dimensity 9300+', 2),
(76, 16, 'RAM', '12GB/16GB', 3),
(77, 16, 'Dung lượng lưu trữ', '256GB/512GB/1TB', 4),
(78, 16, 'Camera sau', '13MP + 8MP', 5),
(79, 16, 'Camera trước', '12MP + 12MP ultra-wide', 6),
(80, 16, 'Pin', '11200 mAh, sạc nhanh 45W', 7),
(81, 16, 'Hệ điều hành', 'Android 14 (One UI 6.1.1)', 8),
(89, 17, 'Màn hình', '12.4 inch LCD, 144Hz, 2.8K', 1),
(90, 17, 'Chip', 'Snapdragon 8+ Gen 2', 2),
(91, 17, 'RAM', '8GB/12GB', 3),
(92, 17, 'Dung lượng lưu trữ', '128GB/256GB/512GB', 4),
(93, 17, 'Camera sau', '50MP', 5),
(94, 17, 'Camera trước', '20MP', 6),
(95, 17, 'Pin', '10000 mAh, sạc nhanh 67W', 7),
(96, 17, 'Hệ điều hành', 'HyperOS (Android 14)', 8),
(104, 18, 'Màn hình', '14.2 inch Liquid Retina XDR (3024x1964)', 1),
(105, 18, 'CPU', 'Apple M3 Pro (11 core CPU, 14 core GPU)', 2),
(106, 18, 'RAM', '18GB', 3),
(107, 18, 'SSD', '512GB', 4),
(108, 18, 'Card đồ họa', 'Tích hợp 14 core GPU', 5),
(109, 18, 'Pin', 'Lên đến 22 giờ', 6),
(110, 18, 'Hệ điều hành', 'macOS Sonoma', 7),
(111, 19, 'Màn hình', '16.3 inch OLED 4K+, 120Hz', 1),
(112, 19, 'CPU', 'Intel Core Ultra 7 155H', 2),
(113, 19, 'RAM', '32GB LPDDR5x', 3),
(114, 19, 'SSD', '1TB NVMe', 4),
(115, 19, 'Card đồ họa', 'NVIDIA GeForce RTX 4060 8GB', 5),
(116, 19, 'Pin', '99.5Wh', 6),
(117, 19, 'Hệ điều hành', 'Windows 11 Pro', 7),
(118, 20, 'Màn hình', '16 inch Mini-LED, 3.2K, 165Hz', 1),
(119, 20, 'CPU', 'Intel Core i9-14900HX', 2),
(120, 20, 'RAM', '32GB DDR5', 3),
(121, 20, 'SSD', '1TB PCIe Gen4', 4),
(122, 20, 'Card đồ họa', 'NVIDIA RTX 4090 16GB', 5),
(123, 20, 'Pin', '99.9Wh', 6),
(124, 20, 'Hệ điều hành', 'Windows 11 Home', 7),
(125, 21, 'Màn hình', '15.6 inch FHD, 144Hz', 1),
(126, 21, 'CPU', 'AMD Ryzen 7 7840HS', 2),
(127, 21, 'RAM', '16GB DDR5', 3),
(128, 21, 'SSD', '512GB NVMe', 4),
(129, 21, 'Card đồ họa', 'NVIDIA RTX 4060 8GB', 5),
(130, 21, 'Pin', '90Wh', 6),
(131, 21, 'Hệ điều hành', 'Windows 11 Home', 7),
(132, 22, 'Kích thước', '49 inch cong', 1),
(133, 22, 'Độ phân giải', 'DQHD (5120x1440)', 2),
(134, 22, 'Tần số quét', '240Hz', 3),
(135, 22, 'Thời gian đáp ứng', '0.03ms', 4),
(136, 22, 'Công nghệ', 'OLED, HDR True Black 400', 5),
(139, 23, 'Kích thước', '27 inch', 1),
(140, 23, 'Độ phân giải', '5K (5120x2880)', 2),
(141, 23, 'Cổng kết nối', 'Thunderbolt 3, 3x USB-C', 3),
(142, 23, 'Màu sắc', '99% P3', 4),
(143, 23, 'Ứng dụng', 'Dành cho Mac', 5),
(146, 24, 'Kích thước', '24 inch', 1),
(147, 24, 'Độ phân giải', 'FHD (1920x1080)', 2),
(148, 24, 'Tần số quét', '165Hz', 3),
(149, 24, 'Thời gian đáp ứng', '1ms', 4),
(150, 24, 'Công nghệ', 'IPS, 99% sRGB', 5),
(153, 25, 'CPU', 'Intel Xeon W-2400 series', 1),
(154, 25, 'RAM', '64GB ECC', 2),
(155, 25, 'SSD', '2TB NVMe', 3),
(156, 25, 'Card đồ họa', 'NVIDIA RTX A5000 24GB', 4),
(157, 25, 'Hệ điều hành', 'Windows 11 Pro for Workstations', 5),
(160, 26, 'CPU', 'Intel Core i7-13700', 1),
(161, 26, 'RAM', '16GB DDR5', 2),
(162, 26, 'SSD', '512GB NVMe', 3),
(163, 26, 'Kích thước', 'Small Form Factor', 4),
(164, 26, 'Hệ điều hành', 'Windows 11 Pro', 5),
(167, 27, 'CPU', 'AMD Ryzen 5 PRO 5650GE', 1),
(168, 27, 'RAM', '8GB DDR4', 2),
(169, 27, 'SSD', '256GB NVMe', 3),
(170, 27, 'Bảo mật', 'TPM 2.0', 4),
(171, 27, 'Hệ điều hành', 'Windows 11 Pro', 5),
(174, 28, 'Loại', 'Chuột không dây', 1),
(175, 28, 'DPI', 'Lên đến 8000', 2),
(176, 28, 'Kết nối', 'Bluetooth, USB receiver', 3),
(177, 28, 'Pin', '70 ngày (sạc USB-C)', 4),
(181, 30, 'Loại', 'Tai nghe over-ear', 1),
(182, 30, 'Chống ồn', 'Chủ động (ANC)', 2),
(183, 30, 'Thời lượng pin', '30 giờ (có ANC)', 3),
(184, 30, 'Codec', 'LDAC, AAC, SBC', 4),
(188, 33, 'Công nghệ', '5G', 1),
(189, 33, 'Data tốc độ cao', '60GB', 2),
(190, 33, 'Gọi nội mạng', '100 phút', 3),
(191, 33, 'Thời hạn', '30 ngày', 4),
(195, 35, 'Kích thước mặt', '49mm', 1),
(196, 35, 'Chất liệu', 'Titan', 2),
(197, 35, 'Pin', 'Lên đến 3 ngày', 3),
(198, 35, 'Tính năng', 'GPS, Lặn sâu 40m, Loa kép', 4),
(202, 39, 'Kích thước', '65 inch', 1),
(203, 39, 'Công nghệ', 'OLED evo', 2),
(204, 39, 'Tần số quét', '144Hz', 3),
(205, 39, 'Hệ điều hành', 'webOS 24', 4),
(206, 39, 'HDR', 'Dolby Vision, HDR10, HLG', 5),
(209, 42, 'Công suất', '1.5 HP (12.000 BTU)', 1),
(210, 42, 'Công nghệ', 'Inverter', 2),
(211, 42, 'Tiết kiệm điện', 'Hiệu suất CSPF 5.0', 3),
(212, 42, 'Lọc không khí', 'Màng lọc PM 2.5', 4),
(216, 45, 'Loại', 'Hút và lau', 1),
(217, 45, 'Định vị', 'Laser LDS', 2),
(218, 45, 'Dung tích hút', '4000 Pa', 3),
(219, 45, 'Pin', '5200 mAh, hoạt động 180 phút', 4),
(223, 49, 'Bình nước', '10 lít', 1),
(224, 49, 'Công suất', '100W', 2),
(225, 49, 'Chế độ gió', '3 chế độ', 3),
(226, 49, 'Điều khiển', 'Remote', 4),
(230, 51, 'Khối lượng giặt', '9 kg', 1),
(231, 51, 'Công nghệ', 'AI DD, Inverter', 2),
(232, 51, 'Khử khuẩn', 'Hơi nước Steam', 3),
(233, 51, 'Hiệu suất', 'Tiết kiệm điện A+++', 4),
(237, 54, 'Dung tích', '310 lít', 1),
(238, 54, 'Công nghệ', 'Digital Inverter, Metal Cooling', 2),
(239, 54, 'Ngăn đá', 'Trên', 3),
(240, 54, 'Màu sắc', 'Bạc', 4),
(244, 57, 'Công nghệ lọc', 'RO + Hydrogen', 1),
(245, 57, 'Số lõi', '11 lõi', 2),
(246, 57, 'Bình chứa', '11 lít', 3),
(247, 57, 'Tính năng', 'Nóng lạnh', 4),
(251, 60, 'Màn hình', '6.7 inch Super Retina XDR', 1),
(252, 60, 'Chip', 'Apple A14 Bionic', 2),
(253, 60, 'Dung lượng', '256GB', 3),
(254, 60, 'Pin', '90%', 4),
(255, 60, 'Bảo hành', '6 tháng', 5),
(258, 62, 'Khối lượng sấy', '8 kg', 1),
(259, 62, 'Công nghệ', 'Bơm nhiệt', 2),
(260, 62, 'Tính năng', 'Khử khuẩn, cảm biến độ ẩm', 3),
(261, 64, 'Độ phân giải', '2MP (1080p)', 1),
(262, 64, 'Góc quay', 'Xoay 355°, nghiêng 90°', 2),
(263, 64, 'Tầm nhìn đêm', 'Hồng ngoại 10m', 3),
(264, 67, 'Công suất', '2400W', 1),
(265, 67, 'Đế', 'Chống dính', 2),
(266, 67, 'An toàn', 'Tự ngắt', 3),
(267, 71, 'Loại', 'Quạt cây 3 cánh', 1),
(268, 71, 'Tốc độ', '3 cấp', 2),
(269, 71, 'Điều khiển', 'Remote', 3),
(270, 74, 'Công nghệ lọc', 'HEPA', 1),
(271, 74, 'CADR', '500 m³/h', 2),
(272, 74, 'Diện tích phù hợp', '60 m²', 3),
(273, 77, 'Số vùng nấu', '2', 1),
(274, 77, 'Công suất tối đa', '4600W', 2),
(275, 77, 'Mặt kính', 'Schott Ceran', 3),
(276, 80, 'Dung tích', '1.5 lít', 1),
(277, 80, 'Công suất', '650W', 2),
(278, 80, 'Lòng nồi', 'Chống dính', 3),
(279, 83, 'Dung tích cối', '2 lít', 1),
(280, 83, 'Công suất', '800W', 2),
(281, 83, 'Lưỡi dao', 'Thép không gỉ', 3),
(282, 86, 'Dung tích', '7.3 lít', 1),
(283, 86, 'Công nghệ', 'Rapid Air', 2),
(284, 86, 'Công suất', '2225W', 3);

-- --------------------------------------------------------

--
-- Table structure for table `thuoc_tinh_danh_muc`
--

CREATE TABLE `thuoc_tinh_danh_muc` (
  `id` int(11) NOT NULL,
  `danh_muc_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Tên key JSON (VD: RAM, Kich_thuoc)',
  `label` varchar(100) NOT NULL COMMENT 'Nhãn hiển thị cho Admin (VD: Dung lượng RAM)',
  `placeholder` varchar(255) DEFAULT NULL COMMENT 'Gợi ý nhập liệu',
  `type` varchar(20) DEFAULT 'text' COMMENT 'Loại thẻ input: text, number...',
  `col` int(11) DEFAULT 6 COMMENT 'Kích thước cột Bootstrap (6 = nửa dòng, 12 = cả dòng)',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thuoc_tinh_danh_muc`
--

INSERT INTO `thuoc_tinh_danh_muc` (`id`, `danh_muc_id`, `name`, `label`, `placeholder`, `type`, `col`, `thu_tu`) VALUES
(1, 1, 'RAM', 'Dung lượng RAM', 'VD: 8GB', 'text', 6, 1),
(2, 1, 'dung_luong_luu_tru', 'Dung lượng lưu trữ', 'VD: 128GB, 256GB', 'text', 6, 2),
(3, 1, 'mau_sac', 'Màu sắc', 'VD: Đen, Trắng, Xanh', 'text', 6, 3),
(4, 1, 'chip', 'Chip xử lý', 'VD: A17 Pro, Snapdragon 8 Gen 3', 'text', 12, 4),
(5, 2, 'ram', 'Dung lượng RAM', 'VD: 6GB, 8GB', 'text', 6, 1),
(6, 2, 'dung_luong_luu_tru', 'Dung lượng lưu trữ', 'VD: 128GB, 256GB', 'text', 6, 2),
(7, 2, 'mau_sac', 'Màu sắc', 'VD: Xám, Bạc, Xanh', 'text', 6, 3),
(8, 2, 'chip', 'Chip xử lý', 'VD: Apple M2, Snapdragon', 'text', 12, 4),
(9, 2, 'kich_thuoc_man_hinh', 'Kích thước màn hình', 'VD: 10.9 inch, 12.4 inch', 'text', 6, 5),
(10, 3, 'ram', 'Dung lượng RAM', 'VD: 8GB, 16GB, 32GB', 'text', 6, 1),
(11, 3, 'dung_luong_luu_tru', 'Dung lượng lưu trữ', 'VD: 512GB SSD, 1TB SSD', 'text', 6, 2),
(12, 3, 'cpu', 'CPU', 'VD: Intel Core i7-1360P, AMD Ryzen 7', 'text', 12, 3),
(13, 3, 'kich_thuoc_man_hinh', 'Kích thước màn hình', 'VD: 14 inch, 15.6 inch', 'text', 6, 4),
(14, 3, 'card_do_hoa', 'Card đồ họa', 'VD: NVIDIA RTX 4060, Intel Iris Xe', 'text', 6, 5),
(15, 3, 'mau_sac', 'Màu sắc', 'VD: Đen, Bạc, Xám', 'text', 6, 6),
(16, 4, 'kich_thuoc_man_hinh', 'Kích thước màn hình', 'VD: 24 inch, 27 inch, 32 inch', 'text', 6, 1),
(17, 4, 'do_phan_giai', 'Độ phân giải', 'VD: Full HD (1920x1080), 4K (3840x2160)', 'text', 6, 2),
(18, 4, 'tan_so_quet', 'Tần số quét', 'VD: 60Hz, 144Hz, 240Hz', 'text', 6, 3),
(19, 4, 'loai_man_hinh', 'Loại màn hình', 'VD: IPS, VA, OLED', 'text', 6, 4),
(20, 5, 'ram', 'Dung lượng RAM', 'VD: 16GB, 32GB, 64GB', 'text', 6, 1),
(21, 5, 'dung_luong_luu_tru', 'Dung lượng lưu trữ', 'VD: 1TB HDD + 512GB SSD', 'text', 6, 2),
(22, 5, 'cpu', 'CPU', 'VD: Intel Core i9-13900K, AMD Ryzen 9', 'text', 12, 3),
(23, 5, 'card_do_hoa', 'Card đồ họa', 'VD: NVIDIA RTX 4070, AMD Radeon RX 7800 XT', 'text', 12, 4),
(24, 5, 'nguon', 'Công suất nguồn', 'VD: 650W, 850W', 'text', 6, 5),
(25, 6, 'loai_phu_kien', 'Loại phụ kiện', 'VD: Chuột, Bàn phím, Tai nghe', 'text', 6, 1),
(26, 6, 'mau_sac', 'Màu sắc', 'VD: Đen, Trắng, Hồng', 'text', 6, 2),
(27, 6, 'chat_lieu', 'Chất liệu', 'VD: Nhựa, Kim loại, Silicon', 'text', 6, 3),
(28, 7, 'loai_sim', 'Loại sim', 'VD: 4G, 5G', 'text', 6, 1),
(29, 7, 'dung_luong_data', 'Dung lượng data', 'VD: 30GB/tháng, 60GB/tháng', 'text', 6, 2),
(30, 7, 'thoi_han', 'Thời hạn sử dụng', 'VD: 1 tháng, 12 tháng', 'text', 6, 3),
(31, 8, 'kich_thuoc_man_hinh', 'Kích thước màn hình', 'VD: 1.5 inch, 1.9 inch', 'text', 6, 1),
(32, 8, 'loai_day', 'Loại dây', 'VD: Silicon, Thép không gỉ, Da', 'text', 6, 2),
(33, 8, 'chuc_nang', 'Chức năng đặc biệt', 'VD: GPS, Nhịp tim, SpO2', 'text', 12, 3),
(34, 8, 'mau_sac', 'Màu sắc', 'VD: Đen, Bạc, Hồng', 'text', 6, 4),
(35, 9, 'kich_thuoc_man_hinh', 'Kích thước màn hình', 'VD: 55 inch, 65 inch, 75 inch', 'text', 6, 1),
(36, 9, 'do_phan_giai', 'Độ phân giải', 'VD: 4K, 8K', 'text', 6, 2),
(37, 9, 'tan_so_quet', 'Tần số quét', 'VD: 60Hz, 120Hz', 'text', 6, 3),
(38, 9, 'he_dieu_hanh', 'Hệ điều hành', 'VD: webOS, Tizen, Google TV', 'text', 6, 4),
(39, 9, 'cong_nghe_man_hinh', 'Công nghệ màn hình', 'VD: QLED, OLED, Neo QLED', 'text', 12, 5),
(40, 10, 'cong_suat', 'Công suất', 'VD: 1 HP (9.000 BTU), 1.5 HP (12.000 BTU)', 'text', 6, 1),
(41, 10, 'loai_may_lanh', 'Loại máy lạnh', 'VD: 1 chiều, 2 chiều (Inverter)', 'text', 6, 2),
(42, 10, 'tieu_thu_dien', 'Tiêu thụ điện', 'VD: 0.8 kW/h, 1.2 kW/h', 'text', 6, 3),
(43, 11, 'dung_luong_binh_chua', 'Dung lượng bình chứa', 'VD: 0.5 L, 0.8 L', 'text', 6, 1),
(44, 11, 'thoi_luong_pin', 'Thời lượng pin', 'VD: 120 phút, 180 phút', 'text', 6, 2),
(45, 11, 'tinh_nang', 'Tính năng đặc biệt', 'VD: Lau nhà, Tự động đổ rác, Hút ẩm', 'text', 12, 3),
(46, 12, 'dung_tich_binh_nuoc', 'Dung tích bình nước', 'VD: 5 L, 8 L', 'text', 6, 1),
(47, 12, 'cong_suat_lam_mat', 'Công suất làm mát', 'VD: 60W, 100W', 'text', 6, 2),
(48, 12, 'che_do_gio', 'Chế độ gió', 'VD: Thường, Ngủ, Tự nhiên', 'text', 6, 3),
(49, 13, 'loai_may_giat', 'Loại máy giặt', 'VD: Cửa trên, Cửa trước', 'text', 6, 1),
(50, 13, 'khoi_luong_giat', 'Khối lượng giặt', 'VD: 7 kg, 9 kg, 12 kg', 'text', 6, 2),
(51, 13, 'hieu_suat_nang_luong', 'Hiệu suất năng lượng', 'VD: Inverter, Tiết kiệm điện A+++', 'text', 12, 3),
(52, 14, 'dung_tich', 'Dung tích', 'VD: 250 L, 450 L, 650 L', 'text', 6, 1),
(53, 14, 'loai_tu', 'Loại tủ', 'VD: Ngăn đá trên, Ngăn đá dưới, Side-by-Side', 'text', 6, 2),
(54, 14, 'cong_nghe_tiet_kiem_dien', 'Công nghệ tiết kiệm điện', 'VD: Inverter, Dual Inverter', 'text', 12, 3),
(55, 15, 'so_loc_loc', 'Số lõi lọc', 'VD: 5 lõi, 7 lõi, 11 lõi', 'text', 6, 1),
(56, 15, 'dung_tich_binh_chua', 'Dung tích bình chứa', 'VD: 7 L, 10 L', 'text', 6, 2),
(57, 15, 'cong_nghe_loc', 'Công nghệ lọc', 'VD: RO, Nano, Hydrogen', 'text', 12, 3),
(58, 16, 'tinh_trang', 'Tình trạng', 'VD: Like new, Đã qua sử dụng, Trưng bày', 'text', 6, 1),
(59, 16, 'bao_hanh', 'Thời gian bảo hành', 'VD: 3 tháng, 6 tháng, 12 tháng', 'text', 6, 2),
(60, 16, 'hang_san_xuat', 'Hãng sản xuất', 'VD: Apple, Samsung, Xiaomi', 'text', 12, 3),
(61, 26, 'loai_may_say', 'Loại máy sấy', 'VD: Bơm nhiệt, Ngưng tụ, Thông hơi', 'text', 6, 1),
(62, 26, 'khoi_luong_say', 'Khối lượng sấy', 'VD: 8 kg, 9 kg, 10 kg', 'text', 6, 2),
(63, 26, 'tieu_thu_dien', 'Tiêu thụ điện', 'VD: 0.9 kW/h, 1.2 kW/h', 'text', 6, 3),
(64, 27, 'do_phan_giai', 'Độ phân giải', 'VD: 2MP (1080p), 4MP, 8MP (4K)', 'text', 6, 1),
(65, 27, 'goc_quay', 'Góc quay', 'VD: 90°, 120°, Xoay 360°', 'text', 6, 2),
(66, 27, 'tinh_nang', 'Tính năng', 'VD: Hồng ngoại, Phát hiện chuyển động, AI', 'text', 12, 3),
(67, 28, 'thuong_hieu', 'Thương hiệu', 'VD: Philips, Panasonic, Sharp', 'text', 6, 1),
(68, 28, 'mau_sac', 'Màu sắc', 'VD: Trắng, Đen, Bạc', 'text', 6, 2),
(69, 28, 'cong_suat', 'Công suất', 'VD: 1000W, 1500W', 'text', 6, 3),
(70, 29, 'loai_quat', 'Loại quạt', 'VD: Quạt cây, Quạt bàn, Quạt treo tường', 'text', 6, 1),
(71, 29, 'cong_suat', 'Công suất', 'VD: 45W, 55W, 65W', 'text', 6, 2),
(72, 29, 'che_do_gio', 'Chế độ gió', 'VD: Thường, Ngủ, Tự nhiên', 'text', 6, 3),
(73, 30, 'dien_tich_phong', 'Diện tích phòng', 'VD: 20 m², 30 m², 50 m²', 'text', 6, 1),
(74, 30, 'luu_luong_loc_khi', 'Lưu lượng lọc khí', 'VD: 150 m³/h, 300 m³/h', 'text', 6, 2),
(75, 30, 'bo_loc', 'Bộ lọc', 'VD: HEPA, Than hoạt tính, UV', 'text', 12, 3),
(76, 31, 'loai_thiet_bi', 'Loại thiết bị', 'VD: Bếp từ, Lò vi sóng, Lò nướng', 'text', 6, 1),
(77, 31, 'cong_suat', 'Công suất', 'VD: 2000W, 2500W', 'text', 6, 2),
(78, 31, 'kich_thuoc', 'Kích thước', 'VD: Rộng 60 cm, Cao 45 cm', 'text', 6, 3),
(79, 32, 'dung_tich', 'Dung tích', 'VD: 1.0 L, 1.5 L, 1.8 L', 'text', 6, 1),
(80, 32, 'cong_suat', 'Công suất', 'VD: 500W, 700W', 'text', 6, 2),
(81, 32, 'chat_lieu_long', 'Chất liệu lòng nồi', 'VD: Chống dính, Gang, Inox', 'text', 12, 3),
(82, 33, 'dung_tich_coc', 'Dung tích cốc', 'VD: 1.2 L, 1.5 L', 'text', 6, 1),
(83, 33, 'cong_suat', 'Công suất', 'VD: 350W, 500W', 'text', 6, 2),
(84, 33, 'toc_do', 'Tốc độ', 'VD: 2 tốc độ, 3 tốc độ + Turbo', 'text', 6, 3),
(85, 34, 'dung_tich', 'Dung tích', 'VD: 3 L, 5 L, 6 L', 'text', 6, 1),
(86, 34, 'cong_suat', 'Công suất', 'VD: 1400W, 1700W', 'text', 6, 2),
(87, 34, 'tinh_nang', 'Tính năng', 'VD: Điều chỉnh nhiệt độ, Hẹn giờ, Chế độ tự động', 'text', 12, 3);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE `transaction_log` (
  `id` int(11) NOT NULL,
  `thanh_toan_id` int(11) NOT NULL COMMENT 'Foreign key to thanh_toan table',
  `gateway_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Unique transaction ID from payment gateway (VNPay/Momo)',
  `gateway_name` enum('COD','VNPAY','MOMO','ZALOPAY') DEFAULT NULL,
  `request_data` text DEFAULT NULL COMMENT 'JSON: Payment request sent to gateway',
  `response_data` text DEFAULT NULL COMMENT 'JSON: Response received from gateway',
  `callback_data` text DEFAULT NULL COMMENT 'JSON: Callback/IPN data received from gateway',
  `status` enum('PENDING','SUCCESS','FAILED','EXPIRED','AMOUNT_MISMATCH') DEFAULT 'PENDING' COMMENT 'Transaction status',
  `created_at` datetime DEFAULT current_timestamp() COMMENT 'Timestamp when log entry was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment gateway transaction logs for audit and idempotency (Req 13.1, 13.2, 13.3)';

--
-- Dumping data for table `transaction_log`
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
(21, 22, NULL, 'ZALOPAY', '{\"don_hang_id\":23,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-11 16:56:52\"}', NULL, NULL, 'PENDING', '2026-04-11 21:41:52'),
(22, 23, NULL, 'ZALOPAY', '{\"don_hang_id\":24,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-11 16:59:22\"}', NULL, NULL, 'PENDING', '2026-04-11 21:44:22'),
(23, 24, NULL, 'ZALOPAY', '{\"don_hang_id\":25,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-11 17:09:48\"}', NULL, NULL, 'PENDING', '2026-04-11 21:54:48'),
(24, 25, NULL, 'ZALOPAY', '{\"don_hang_id\":26,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-12 14:00:06\"}', NULL, NULL, 'PENDING', '2026-04-12 18:45:06'),
(25, 26, NULL, 'ZALOPAY', '{\"don_hang_id\":27,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-12 14:15:34\"}', NULL, NULL, 'PENDING', '2026-04-12 19:00:34'),
(26, 27, NULL, 'ZALOPAY', '{\"don_hang_id\":28,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-12 14:19:21\"}', NULL, NULL, 'PENDING', '2026-04-12 19:04:21'),
(27, 28, NULL, 'ZALOPAY', '{\"don_hang_id\":29,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-12 14:21:35\"}', NULL, NULL, 'PENDING', '2026-04-12 19:06:35'),
(28, 29, NULL, 'ZALOPAY', '{\"don_hang_id\":30,\"amount\":30034.99,\"payment_method\":\"ZALOPAY\",\"expiration_time\":\"2026-04-12 14:24:09\"}', NULL, NULL, 'PENDING', '2026-04-12 19:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `yeu_thich`
--

CREATE TABLE `yeu_thich` (
  `nguoi_dung_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `ngay_them` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner_quang_cao`
--
ALTER TABLE `banner_quang_cao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vi_tri_trang_thai` (`vi_tri`,`trang_thai`);

--
-- Indexes for table `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_don_phienban` (`don_hang_id`,`phien_ban_id`) COMMENT 'Tránh trùng SP trong đơn',
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Indexes for table `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_gio_phienban` (`gio_hang_id`,`phien_ban_id`) COMMENT 'Tránh trùng SP trong giỏ',
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Indexes for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Indexes for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_dm_slug` (`slug`),
  ADD KEY `danh_muc_cha_id` (`danh_muc_cha_id`);

--
-- Indexes for table `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ma_don` (`ma_don_hang`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `dia_chi_id` (`dia_chi_id`),
  ADD KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`),
  ADD KEY `idx_ngay_tao` (`ngay_tao`);

--
-- Indexes for table `gateway_health`
--
ALTER TABLE `gateway_health`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_gateway_name` (`gateway_name`),
  ADD KEY `idx_gateway_name` (`gateway_name`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`),
  ADD KEY `phien_ban_id` (`phien_ban_id`);

--
-- Indexes for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ma_code` (`ma_code`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_email` (`email`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD UNIQUE KEY `idx_supabase_id` (`supabase_id`),
  ADD KEY `idx_forget_token` (`forget_token`);

--
-- Indexes for table `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sku` (`sku`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Indexes for table `refund`
--
ALTER TABLE `refund`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thanh_toan_id` (`thanh_toan_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sp_slug` (`slug`),
  ADD KEY `danh_muc_id` (`danh_muc_id`),
  ADD KEY `idx_hang_sx` (`hang_san_xuat`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Indexes for table `san_pham_khuyen_mai`
--
ALTER TABLE `san_pham_khuyen_mai`
  ADD PRIMARY KEY (`san_pham_id`,`khuyen_mai_id`),
  ADD KEY `khuyen_mai_id` (`khuyen_mai_id`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `don_hang_id` (`don_hang_id`),
  ADD KEY `nguoi_duyet_id` (`nguoi_duyet_id`),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`) COMMENT 'Index for idempotency checks and transaction lookups (Req 8.5)',
  ADD KEY `idx_expiration_time` (`expiration_time`) COMMENT 'Index for timeout checking queries (Req 6.1)',
  ADD KEY `idx_gateway_name` (`gateway_name`) COMMENT 'Index for filtering transactions by gateway';

--
-- Indexes for table `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Indexes for table `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `danh_muc_id` (`danh_muc_id`);

--
-- Indexes for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thanh_toan_id` (`thanh_toan_id`),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`) COMMENT 'Index for idempotency checks (Req 8.1, 8.5)',
  ADD KEY `idx_gateway_name` (`gateway_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD PRIMARY KEY (`nguoi_dung_id`,`san_pham_id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner_quang_cao`
--
ALTER TABLE `banner_quang_cao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `dia_chi`
--
ALTER TABLE `dia_chi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `gateway_health`
--
ALTER TABLE `gateway_health`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `refund`
--
ALTER TABLE `refund`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT for table `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  ADD CONSTRAINT `chi_tiet_don_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  ADD CONSTRAINT `chi_tiet_gio_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD CONSTRAINT `danh_muc_ibfk_1` FOREIGN KEY (`danh_muc_cha_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD CONSTRAINT `dia_chi_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`dia_chi_id`) REFERENCES `dia_chi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_ibfk_3` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD CONSTRAINT `hinh_anh_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hinh_anh_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD CONSTRAINT `lich_su_tim_kiem_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  ADD CONSTRAINT `phien_ban_sp_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `san_pham_khuyen_mai`
--
ALTER TABLE `san_pham_khuyen_mai`
  ADD CONSTRAINT `sp_km_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sp_km_ibfk_2` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  ADD CONSTRAINT `thong_so_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thuoc_tinh_danh_muc`
--
ALTER TABLE `thuoc_tinh_danh_muc`
  ADD CONSTRAINT `ttdm_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD CONSTRAINT `yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

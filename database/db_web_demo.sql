-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 01:23 PM
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
(13, 'test', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775364494/banners/banner_desktop_1775364493.webp', NULL, '/san-pham/sam-sung', 'HOME_HERO', 3, '2026-04-05 11:48:00', '2026-04-29 11:48:00', 1),
(14, 'test', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775385003/banners/banner_desktop_1775385001.webp', NULL, '/san-pham/sam-sung', 'HOME_HERO', 0, '2026-04-05 17:29:00', '2026-04-30 17:29:00', 1),
(15, 'săn sale', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775385139/banners/banner_desktop_1775385136.webp', NULL, '/san-pham/iphone', 'HOME_HERO', 0, '2026-04-05 17:31:00', '2026-04-24 17:31:00', 1);

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
(1, 5, 186, 1);

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
(1, 'Điện Thoại', 'dien-thoai', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775381967/categories/category_icon_1.webp', NULL, 1, 1, 0, 0),
(2, 'Máy tính bảng', 'may-tinh-bang', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775382190/categories/category_icon_2.webp', NULL, 2, 1, 0, 0),
(3, 'Laptop', 'may-tinh-xach-tay', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383217/categories/category_icon_1775383216.webp', NULL, 3, 1, 0, 0),
(4, 'Màn hình', 'man-hinh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383299/categories/category_icon_1775383298.webp', NULL, 4, 1, 0, 0),
(5, 'PC - Máy tính để bàn', 'may-tinh-de-ban', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383413/categories/category_icon_1775383412.webp', NULL, 5, 1, 0, 0),
(6, 'Phụ kiện', 'phu-kien', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383502/categories/category_icon_1775383501.webp', NULL, 6, 1, 0, 0),
(7, 'Sim FPT', 'sim-fpt', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383575/categories/category_icon_1775383574.webp', NULL, 7, 1, 0, 0),
(8, 'Đồng hồ thông minh', 'smartwatch', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383648/categories/category_icon_1775383647.webp', NULL, 8, 1, 0, 0),
(9, 'Tivi', 'tivi', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383713/categories/category_icon_1775383711.gif', NULL, 9, 1, 0, 0),
(10, 'Máy lạnh - Điều hòa', 'may-lanh-dieu-hoa', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383793/categories/category_icon_1775383791.gif', NULL, 10, 1, 0, 0),
(11, 'Robot hút bụi', 'robot-hut-bui', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383856/categories/category_icon_1775383853.gif', NULL, 11, 1, 0, 0),
(12, 'Quạt điều hòa', 'quat-dieu-hoa', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383922/categories/category_icon_1775383921.webp', NULL, 12, 1, 0, 0),
(13, 'Máy giặt', 'may-giat', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775383997/categories/category_icon_1775383995.gif', NULL, 13, 1, 0, 0),
(14, 'Tủ lạnh', 'tu-lanh', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384149/categories/category_icon_1775384147.gif', NULL, 14, 1, 0, 0),
(15, 'Máy lọc nước', 'may-loc-nuoc', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384461/categories/category_icon_1775384459.gif', NULL, 15, 1, 0, 0),
(16, 'Máy cũ giá rẻ', 'may-doi-tra', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775384521/categories/category_icon_1775384520.webp', NULL, 16, 1, 0, 0),
(26, 'Máy sấy quần áo', 'may-say-quan-ao', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775395070/categories/category_icon_1775395068.gif', NULL, 17, 1, 0, 0);

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
(5, NULL, 'ob5ha377qv6sb2nri5515vh3rq', '2026-04-06 17:55:14', '2026-04-06 17:55:14');

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
(5, 7, NULL, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'iPhone 15 Pro Max', 1, 1),
(6, 7, 10, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'iPhone 15 Pro Max Titan Tự Nhiên', 0, 2);

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
(1, 'Khuyến mãi tết', 'PHAN_TRAM', 12.00, 50.00, '2026-04-04 21:57:32', '2026-04-24 21:57:32', 'HOAT_DONG'),
(2, 'Khuyến mãi test 1', 'PHAN_TRAM', 50.00, 90.00, '2026-04-04 22:02:03', '2026-04-24 22:02:03', 'HOAT_DONG'),
(3, 'Khuyến mãi test 2', 'PHAN_TRAM', 56.00, 79.00, '2026-04-04 22:02:31', '2026-04-24 22:02:31', 'HOAT_DONG'),
(4, 'Khuyến mãi test 3', 'PHAN_TRAM', 67.00, 80.00, '2026-04-04 22:02:54', '2026-04-23 22:02:54', 'HOAT_DONG'),
(5, 'Khuyến mãi test 4', 'PHAN_TRAM', 67.00, 80.00, '2026-04-04 22:03:21', '2026-04-23 22:03:21', 'HOAT_DONG');

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
  `email` varchar(255) NOT NULL,
  `mat_khau` varchar(255) NOT NULL COMMENT 'Lưu hash bằng password_hash()',
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

INSERT INTO `nguoi_dung` (`id`, `email`, `mat_khau`, `ho_ten`, `sdt`, `avatar_url`, `ngay_sinh`, `gioi_tinh`, `loai_tai_khoan`, `trang_thai`, `verification_token`, `ngay_tao`, `ngay_cap_nhat`, `forget_token`) VALUES
(1, 'test_1773155576@example.com', '$2y$10$IcOj9mDvjFD1jdTaRVVY0eoywjosOpf80oNvDP3KWZqxl6TMUDTW6', 'Nguyễn Văn Test', '0901234567', NULL, NULL, 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(2, 'admin_1773155576@example.com', '$2y$10$dyvFZGKucag4pZ.RXkSQN.XTO.0tgpfouhBOIg7PyKocR2N7.uCqO', 'Admin Test', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-10 22:12:56', '2026-03-10 22:12:56', NULL),
(3, 'dat82770@gmail.com', 'cbd5140549732304f6590c5d13afb4fabd68c357', 'Trương Thành Đạt', '0399746612', 'https://res.cloudinary.com/dmahghpku/image/upload/v1775357973/avatars/avatar_user_3.jpg', '2006-10-15', 'NAM', 'MEMBER', 'ACTIVE', NULL, '2026-03-28 17:19:23', '2026-04-05 04:59:34', NULL),
(4, 'admin@gmail.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', NULL, NULL, NULL, NULL, 'ADMIN', 'ACTIVE', NULL, '2026-03-29 19:08:00', '2026-03-29 19:15:03', NULL),
(5, 'datweb07@gmail.com', 'e13187e9e3517e5c3c4c6cecc580d8d9880910a0', 'loc', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-30 08:47:17', '2026-03-30 08:50:42', NULL),
(6, 'dattruong.31241024873@st.ueh.edu.vn', 'e42c0141250d02dad20c86609d5d19d155f12717', 'ok', NULL, NULL, NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-30 08:59:17', '2026-03-30 09:07:36', NULL),
(7, 'test_reset_1774858467@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '771016c4c17b6d983a360510b62d0747388599bc0281821c4675e4b38436dc6b', '2026-03-30 10:14:27', '2026-03-30 10:14:27', NULL),
(8, 'test_reset_1774858550@example.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '8b88305a96792e1eccbd100868d5e316abd18d501b3d3a4cbaf0ca6b9c3b7029', '2026-03-30 10:15:50', '2026-03-30 15:15:50', '397e96fac4d873ea58648f341799fda30935651e3fae792a694d2755b84aaa4e'),
(42, 'test_reset_1774859235@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 'Test User', NULL, NULL, NULL, NULL, 'MEMBER', 'UNVERIFIED', '92efe7c252315d4fcd464e9a591c80628cc15b3f296d9b503cb1cc67e1cd3edb', '2026-03-30 10:27:15', '2026-03-30 15:27:15', 'cff85a50b371038fb5bc4f50064d4da55b655fbb83955c03c600317972b181b3'),
(161, 'hsntk1610@gmail.com', '0bf7e28d9ad8eb2c7afa624bcbc7afe8eeadbae0', 'nguyentankhiem', NULL, '/public/uploads/avatars/avatar_161_1774929615.jpg', NULL, NULL, 'MEMBER', 'ACTIVE', '', '2026-03-31 05:58:29', '2026-03-31 06:00:15', NULL);

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
  `dung_luong` varchar(50) DEFAULT NULL COMMENT '128GB, 256GB, 512GB, 1TB',
  `ram` varchar(50) DEFAULT NULL COMMENT '8GB, 12GB, 16GB',
  `cau_hinh` varchar(255) DEFAULT NULL COMMENT 'Mô tả cấu hình khác (nếu có)',
  `gia_ban` decimal(15,2) DEFAULT NULL COMMENT 'Giá bán hiện tại',
  `gia_goc` decimal(15,2) DEFAULT NULL COMMENT 'Giá gốc (giá gạch ngang)',
  `so_luong_ton` int(11) DEFAULT 0,
  `trang_thai` enum('CON_HANG','HET_HANG','NGUNG_BAN') DEFAULT 'CON_HANG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phien_ban_san_pham`
--

INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `dung_luong`, `ram`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(10, 7, 'IP15PM-256-TITAN', 'iPhone 15 Pro Max 256GB Titan Tự Nhiên', 'Titan Tự Nhiên', '256GB', '8GB', NULL, 34990000.00, 34990000.00, 50, 'CON_HANG'),
(101, 100, 'IP16PM-256-TITAN', 'iPhone 16 Pro Max 256GB', 'Titan', '256GB', '8GB', NULL, 29990000.00, 31990000.00, 10, 'CON_HANG'),
(102, 100, 'IP16PM-512-TITAN', 'iPhone 16 Pro Max 512GB', 'Titan', '512GB', '8GB', NULL, 33990000.00, 35990000.00, 8, 'CON_HANG'),
(103, 100, 'IP16PM-1TB-TITAN', 'iPhone 16 Pro Max 1TB', 'Titan', '1TB', '8GB', NULL, 37990000.00, 39990000.00, 5, 'CON_HANG'),
(104, 100, 'IP16PM-256-WHITE', 'iPhone 16 Pro Max 256GB', 'Trắng', '256GB', '8GB', NULL, 29990000.00, 31990000.00, 7, 'CON_HANG'),
(105, 100, 'IP16PM-512-BLUE', 'iPhone 16 Pro Max 512GB', 'Xanh', '512GB', '8GB', NULL, 33990000.00, 35990000.00, 6, 'CON_HANG'),
(106, 101, 'S24U-256-BLACK', 'S24 Ultra 256GB', 'Đen', '256GB', '12GB', NULL, 26990000.00, 28990000.00, 10, 'CON_HANG'),
(107, 101, 'S24U-512-BLACK', 'S24 Ultra 512GB', 'Đen', '512GB', '12GB', NULL, 29990000.00, 31990000.00, 8, 'CON_HANG'),
(108, 101, 'S24U-1TB-BLACK', 'S24 Ultra 1TB', 'Đen', '1TB', '12GB', NULL, 32990000.00, 34990000.00, 5, 'CON_HANG'),
(109, 101, 'S24U-256-GREEN', 'S24 Ultra 256GB', 'Xanh', '256GB', '12GB', NULL, 26990000.00, 28990000.00, 6, 'CON_HANG'),
(110, 101, 'S24U-512-WHITE', 'S24 Ultra 512GB', 'Trắng', '512GB', '12GB', NULL, 29990000.00, 31990000.00, 4, 'CON_HANG'),
(111, 102, 'XM14U-256-BLACK', 'Xiaomi 14 Ultra 256GB', 'Đen', '256GB', '12GB', NULL, 22000000.00, 24000000.00, 10, 'CON_HANG'),
(112, 102, 'XM14U-512-BLACK', 'Xiaomi 14 Ultra 512GB', 'Đen', '512GB', '12GB', NULL, 25000000.00, 27000000.00, 7, 'CON_HANG'),
(113, 102, 'XM14U-1TB-BLACK', 'Xiaomi 14 Ultra 1TB', 'Đen', '1TB', '16GB', NULL, 28000000.00, 30000000.00, 5, 'CON_HANG'),
(114, 102, 'XM14U-256-WHITE', 'Xiaomi 14 Ultra 256GB', 'Trắng', '256GB', '12GB', NULL, 22000000.00, 24000000.00, 6, 'CON_HANG'),
(115, 102, 'XM14U-512-GREEN', 'Xiaomi 14 Ultra 512GB', 'Xanh', '512GB', '12GB', NULL, 25000000.00, 27000000.00, 5, 'CON_HANG'),
(116, 103, 'OPN3-256-BLACK', 'Oppo Find N3 256GB', 'Đen', '256GB', '12GB', NULL, 41990000.00, 43990000.00, 5, 'CON_HANG'),
(117, 103, 'OPN3-512-BLACK', 'Oppo Find N3 512GB', 'Đen', '512GB', '16GB', NULL, 44990000.00, 46990000.00, 4, 'CON_HANG'),
(118, 103, 'OPN3-256-GOLD', 'Oppo Find N3 256GB', 'Vàng', '256GB', '12GB', NULL, 41990000.00, 43990000.00, 3, 'CON_HANG'),
(119, 103, 'OPN3-512-GREEN', 'Oppo Find N3 512GB', 'Xanh', '512GB', '16GB', NULL, 44990000.00, 46990000.00, 3, 'CON_HANG'),
(120, 103, 'OPN3-1TB-BLACK', 'Oppo Find N3 1TB', 'Đen', '1TB', '16GB', NULL, 47990000.00, 49990000.00, 2, 'CON_HANG'),
(121, 104, 'PIX8P-128-BLACK', 'Pixel 8 Pro 128GB', 'Đen', '128GB', '12GB', NULL, 19500000.00, 21500000.00, 10, 'CON_HANG'),
(122, 104, 'PIX8P-256-BLACK', 'Pixel 8 Pro 256GB', 'Đen', '256GB', '12GB', NULL, 21500000.00, 23500000.00, 8, 'CON_HANG'),
(123, 104, 'PIX8P-512-BLACK', 'Pixel 8 Pro 512GB', 'Đen', '512GB', '12GB', NULL, 24500000.00, 26500000.00, 5, 'CON_HANG'),
(124, 104, 'PIX8P-128-BLUE', 'Pixel 8 Pro 128GB', 'Xanh', '128GB', '12GB', NULL, 19500000.00, 21500000.00, 6, 'CON_HANG'),
(125, 104, 'PIX8P-256-WHITE', 'Pixel 8 Pro 256GB', 'Trắng', '256GB', '12GB', NULL, 21500000.00, 23500000.00, 5, 'CON_HANG'),
(126, 105, 'XP1V-256-BLACK', 'Xperia 1 V 256GB', 'Đen', '256GB', '12GB', NULL, 24000000.00, 26000000.00, 6, 'CON_HANG'),
(127, 105, 'XP1V-512-BLACK', 'Xperia 1 V 512GB', 'Đen', '512GB', '12GB', NULL, 27000000.00, 29000000.00, 4, 'CON_HANG'),
(128, 105, 'XP1V-256-GREEN', 'Xperia 1 V 256GB', 'Xanh', '256GB', '12GB', NULL, 24000000.00, 26000000.00, 5, 'CON_HANG'),
(129, 105, 'XP1V-512-GREEN', 'Xperia 1 V 512GB', 'Xanh', '512GB', '12GB', NULL, 27000000.00, 29000000.00, 3, 'CON_HANG'),
(130, 105, 'XP1V-1TB-BLACK', 'Xperia 1 V 1TB', 'Đen', '1TB', '16GB', NULL, 30000000.00, 32000000.00, 2, 'CON_HANG'),
(131, 106, 'ROG8-256-BLACK', 'ROG Phone 8 256GB', 'Đen', '256GB', '12GB', NULL, 23500000.00, 25500000.00, 8, 'CON_HANG'),
(132, 106, 'ROG8-512-BLACK', 'ROG Phone 8 512GB', 'Đen', '512GB', '16GB', NULL, 26500000.00, 28500000.00, 6, 'CON_HANG'),
(133, 106, 'ROG8-1TB-BLACK', 'ROG Phone 8 1TB', 'Đen', '1TB', '16GB', NULL, 29500000.00, 31500000.00, 4, 'CON_HANG'),
(134, 106, 'ROG8-256-WHITE', 'ROG Phone 8 256GB', 'Trắng', '256GB', '12GB', NULL, 23500000.00, 25500000.00, 5, 'CON_HANG'),
(135, 106, 'ROG8-512-WHITE', 'ROG Phone 8 512GB', 'Trắng', '512GB', '16GB', NULL, 26500000.00, 28500000.00, 3, 'CON_HANG'),
(136, 107, 'VIVOX100-256-BLUE', 'Vivo X100 Pro 256GB', 'Xanh', '256GB', '12GB', NULL, 18000000.00, 20000000.00, 10, 'CON_HANG'),
(137, 107, 'VIVOX100-512-BLUE', 'Vivo X100 Pro 512GB', 'Xanh', '512GB', '12GB', NULL, 20000000.00, 22000000.00, 7, 'CON_HANG'),
(138, 107, 'VIVOX100-256-BLACK', 'Vivo X100 Pro 256GB', 'Đen', '256GB', '12GB', NULL, 18000000.00, 20000000.00, 6, 'CON_HANG'),
(139, 107, 'VIVOX100-512-BLACK', 'Vivo X100 Pro 512GB', 'Đen', '512GB', '12GB', NULL, 20000000.00, 22000000.00, 5, 'CON_HANG'),
(140, 107, 'VIVOX100-1TB-BLACK', 'Vivo X100 Pro 1TB', 'Đen', '1TB', '16GB', NULL, 23000000.00, 25000000.00, 3, 'CON_HANG'),
(141, 108, 'IP13-128-BLACK', 'iPhone 13 128GB', 'Đen', '128GB', '4GB', NULL, 13500000.00, 15500000.00, 12, 'CON_HANG'),
(142, 108, 'IP13-256-BLACK', 'iPhone 13 256GB', 'Đen', '256GB', '4GB', NULL, 15500000.00, 17500000.00, 8, 'CON_HANG'),
(143, 108, 'IP13-128-WHITE', 'iPhone 13 128GB', 'Trắng', '128GB', '4GB', NULL, 13500000.00, 15500000.00, 7, 'CON_HANG'),
(144, 108, 'IP13-256-BLUE', 'iPhone 13 256GB', 'Xanh', '256GB', '4GB', NULL, 15500000.00, 17500000.00, 5, 'CON_HANG'),
(145, 108, 'IP13-512-BLACK', 'iPhone 13 512GB', 'Đen', '512GB', '4GB', NULL, 18500000.00, 20500000.00, 3, 'CON_HANG'),
(146, 109, 'A55-128-BLACK', 'Galaxy A55 128GB', 'Đen', '128GB', '8GB', NULL, 9500000.00, 10500000.00, 10, 'CON_HANG'),
(147, 109, 'A55-256-BLACK', 'Galaxy A55 256GB', 'Đen', '256GB', '8GB', NULL, 10500000.00, 11500000.00, 8, 'CON_HANG'),
(148, 109, 'A55-128-BLUE', 'Galaxy A55 128GB', 'Xanh', '128GB', '8GB', NULL, 9500000.00, 10500000.00, 7, 'CON_HANG'),
(149, 109, 'A55-256-BLUE', 'Galaxy A55 256GB', 'Xanh', '256GB', '8GB', NULL, 10500000.00, 11500000.00, 6, 'CON_HANG'),
(150, 109, 'A55-256-WHITE', 'Galaxy A55 256GB', 'Trắng', '256GB', '8GB', NULL, 10500000.00, 11500000.00, 5, 'CON_HANG'),
(151, 110, 'IPADPRO11-128-SILVER', 'iPad Pro M2 11 128GB', 'Bạc', '128GB', '8GB', NULL, 20500000.00, 22500000.00, 10, 'CON_HANG'),
(152, 110, 'IPADPRO11-256-SILVER', 'iPad Pro M2 11 256GB', 'Bạc', '256GB', '8GB', NULL, 22500000.00, 24500000.00, 8, 'CON_HANG'),
(153, 110, 'IPADPRO11-512-SILVER', 'iPad Pro M2 11 512GB', 'Bạc', '512GB', '8GB', NULL, 25500000.00, 27500000.00, 6, 'CON_HANG'),
(154, 110, 'IPADPRO11-128-SPACE', 'iPad Pro M2 11 128GB', 'Xám', '128GB', '8GB', NULL, 20500000.00, 22500000.00, 7, 'CON_HANG'),
(155, 110, 'IPADPRO11-256-SPACE', 'iPad Pro M2 11 256GB', 'Xám', '256GB', '8GB', NULL, 22500000.00, 24500000.00, 5, 'CON_HANG'),
(156, 111, 'TABS9U-256-BLACK', 'Tab S9 Ultra 256GB', 'Đen', '256GB', '12GB', NULL, 22000000.00, 24000000.00, 10, 'CON_HANG'),
(157, 111, 'TABS9U-512-BLACK', 'Tab S9 Ultra 512GB', 'Đen', '512GB', '12GB', NULL, 25000000.00, 27000000.00, 7, 'CON_HANG'),
(158, 111, 'TABS9U-1TB-BLACK', 'Tab S9 Ultra 1TB', 'Đen', '1TB', '16GB', NULL, 28000000.00, 30000000.00, 5, 'CON_HANG'),
(159, 111, 'TABS9U-256-GREEN', 'Tab S9 Ultra 256GB', 'Xanh', '256GB', '12GB', NULL, 22000000.00, 24000000.00, 6, 'CON_HANG'),
(160, 111, 'TABS9U-512-GREEN', 'Tab S9 Ultra 512GB', 'Xanh', '512GB', '12GB', NULL, 25000000.00, 27000000.00, 4, 'CON_HANG'),
(161, 112, 'XPAD6-128-BLUE', 'Xiaomi Pad 6 128GB', 'Xanh', '128GB', '8GB', NULL, 7500000.00, 8500000.00, 12, 'CON_HANG'),
(162, 112, 'XPAD6-256-BLUE', 'Xiaomi Pad 6 256GB', 'Xanh', '256GB', '8GB', NULL, 8500000.00, 9500000.00, 10, 'CON_HANG'),
(163, 112, 'XPAD6-128-GRAY', 'Xiaomi Pad 6 128GB', 'Xám', '128GB', '8GB', NULL, 7500000.00, 8500000.00, 8, 'CON_HANG'),
(164, 112, 'XPAD6-256-GRAY', 'Xiaomi Pad 6 256GB', 'Xám', '256GB', '8GB', NULL, 8500000.00, 9500000.00, 7, 'CON_HANG'),
(165, 112, 'XPAD6-256-GOLD', 'Xiaomi Pad 6 256GB', 'Vàng', '256GB', '8GB', NULL, 8500000.00, 9500000.00, 5, 'CON_HANG'),
(166, 113, 'IPADAIR5-64-BLUE', 'iPad Air 5 64GB', 'Xanh', '64GB', '8GB', NULL, 14500000.00, 16500000.00, 10, 'CON_HANG'),
(167, 113, 'IPADAIR5-256-BLUE', 'iPad Air 5 256GB', 'Xanh', '256GB', '8GB', NULL, 17500000.00, 19500000.00, 8, 'CON_HANG'),
(168, 113, 'IPADAIR5-64-PINK', 'iPad Air 5 64GB', 'Hồng', '64GB', '8GB', NULL, 14500000.00, 16500000.00, 7, 'CON_HANG'),
(169, 113, 'IPADAIR5-256-PINK', 'iPad Air 5 256GB', 'Hồng', '256GB', '8GB', NULL, 17500000.00, 19500000.00, 5, 'CON_HANG'),
(170, 113, 'IPADAIR5-256-SPACE', 'iPad Air 5 256GB', 'Xám', '256GB', '8GB', NULL, 17500000.00, 19500000.00, 4, 'CON_HANG'),
(171, 114, 'LENOVOP11-128-GRAY', 'Lenovo P11 Pro 128GB', 'Xám', '128GB', '6GB', NULL, 9000000.00, 10500000.00, 10, 'CON_HANG'),
(172, 114, 'LENOVOP11-256-GRAY', 'Lenovo P11 Pro 256GB', 'Xám', '256GB', '8GB', NULL, 10500000.00, 12000000.00, 8, 'CON_HANG'),
(173, 114, 'LENOVOP11-128-SILVER', 'Lenovo P11 Pro 128GB', 'Bạc', '128GB', '6GB', NULL, 9000000.00, 10500000.00, 7, 'CON_HANG'),
(174, 114, 'LENOVOP11-256-SILVER', 'Lenovo P11 Pro 256GB', 'Bạc', '256GB', '8GB', NULL, 10500000.00, 12000000.00, 6, 'CON_HANG'),
(175, 114, 'LENOVOP11-256-GOLD', 'Lenovo P11 Pro 256GB', 'Vàng', '256GB', '8GB', NULL, 10500000.00, 12000000.00, 5, 'CON_HANG'),
(176, 115, 'MBP14M3-512-SILVER', 'MacBook Pro 14 M3 512GB', 'Bạc', '512GB', '16GB', NULL, 39990000.00, 41990000.00, 8, 'CON_HANG'),
(177, 115, 'MBP14M3-1TB-SILVER', 'MacBook Pro 14 M3 1TB', 'Bạc', '1TB', '16GB', NULL, 44990000.00, 46990000.00, 6, 'CON_HANG'),
(178, 115, 'MBP14M3-512-SPACE', 'MacBook Pro 14 M3 512GB', 'Xám', '512GB', '16GB', NULL, 39990000.00, 41990000.00, 7, 'CON_HANG'),
(179, 115, 'MBP14M3-1TB-SPACE', 'MacBook Pro 14 M3 1TB', 'Xám', '1TB', '16GB', NULL, 44990000.00, 46990000.00, 5, 'CON_HANG'),
(180, 115, 'MBP14M3-2TB-SPACE', 'MacBook Pro 14 M3 2TB', 'Xám', '2TB', '32GB', NULL, 49990000.00, 51990000.00, 3, 'CON_HANG'),
(181, 116, 'XPS15-512-SILVER', 'Dell XPS 15 512GB', 'Bạc', '512GB', '16GB', NULL, 45000000.00, 47000000.00, 6, 'CON_HANG'),
(182, 116, 'XPS15-1TB-SILVER', 'Dell XPS 15 1TB', 'Bạc', '1TB', '16GB', NULL, 48000000.00, 50000000.00, 5, 'CON_HANG'),
(183, 116, 'XPS15-512-BLACK', 'Dell XPS 15 512GB', 'Đen', '512GB', '16GB', NULL, 45000000.00, 47000000.00, 6, 'CON_HANG'),
(184, 116, 'XPS15-1TB-BLACK', 'Dell XPS 15 1TB', 'Đen', '1TB', '32GB', NULL, 52000000.00, 54000000.00, 4, 'CON_HANG'),
(185, 116, 'XPS15-2TB-BLACK', 'Dell XPS 15 2TB', 'Đen', '2TB', '32GB', NULL, 58000000.00, 60000000.00, 2, 'CON_HANG'),
(186, 117, 'TUFF15-512-BLACK', 'TUF F15 512GB', 'Đen', '512GB', '16GB', NULL, 18500000.00, 20500000.00, 10, 'CON_HANG'),
(187, 117, 'TUFF15-1TB-BLACK', 'TUF F15 1TB', 'Đen', '1TB', '16GB', NULL, 20500000.00, 22500000.00, 8, 'CON_HANG'),
(188, 117, 'TUFF15-512-GRAY', 'TUF F15 512GB', 'Xám', '512GB', '16GB', NULL, 18500000.00, 20500000.00, 7, 'CON_HANG'),
(189, 117, 'TUFF15-1TB-GRAY', 'TUF F15 1TB', 'Xám', '1TB', '16GB', NULL, 20500000.00, 22500000.00, 6, 'CON_HANG'),
(190, 117, 'TUFF15-1TB-RGB', 'TUF F15 1TB RGB', 'Đen RGB', '1TB', '32GB', NULL, 23500000.00, 25500000.00, 4, 'CON_HANG'),
(191, 118, 'HP15-256-SILVER', 'HP Pavilion 256GB', 'Bạc', '256GB', '8GB', NULL, 14000000.00, 15500000.00, 10, 'CON_HANG'),
(192, 118, 'HP15-512-SILVER', 'HP Pavilion 512GB', 'Bạc', '512GB', '8GB', NULL, 15500000.00, 17000000.00, 8, 'CON_HANG'),
(193, 118, 'HP15-256-GOLD', 'HP Pavilion 256GB', 'Vàng', '256GB', '8GB', NULL, 14000000.00, 15500000.00, 7, 'CON_HANG'),
(194, 118, 'HP15-512-GOLD', 'HP Pavilion 512GB', 'Vàng', '512GB', '8GB', NULL, 15500000.00, 17000000.00, 6, 'CON_HANG'),
(195, 118, 'HP15-1TB-SILVER', 'HP Pavilion 1TB', 'Bạc', '1TB', '16GB', NULL, 17500000.00, 19000000.00, 4, 'CON_HANG'),
(196, 119, 'LEGION5-512-BLACK', 'Legion 5 512GB', 'Đen', '512GB', '16GB', NULL, 26000000.00, 28000000.00, 8, 'CON_HANG'),
(197, 119, 'LEGION5-1TB-BLACK', 'Legion 5 1TB', 'Đen', '1TB', '16GB', NULL, 28000000.00, 30000000.00, 6, 'CON_HANG'),
(198, 119, 'LEGION5-512-GRAY', 'Legion 5 512GB', 'Xám', '512GB', '16GB', NULL, 26000000.00, 28000000.00, 6, 'CON_HANG'),
(199, 119, 'LEGION5-1TB-GRAY', 'Legion 5 1TB', 'Xám', '1TB', '32GB', NULL, 31000000.00, 33000000.00, 4, 'CON_HANG'),
(200, 119, 'LEGION5-2TB-BLACK', 'Legion 5 2TB', 'Đen', '2TB', '32GB', NULL, 35000000.00, 37000000.00, 2, 'CON_HANG'),
(201, 120, 'SWIFT14-512-SILVER', 'Swift Go 14 512GB', 'Bạc', '512GB', '16GB', NULL, 16500000.00, 18500000.00, 9, 'CON_HANG'),
(202, 120, 'SWIFT14-1TB-SILVER', 'Swift Go 14 1TB', 'Bạc', '1TB', '16GB', NULL, 18500000.00, 20500000.00, 7, 'CON_HANG'),
(203, 120, 'SWIFT14-512-GREEN', 'Swift Go 14 512GB', 'Xanh', '512GB', '16GB', NULL, 16500000.00, 18500000.00, 6, 'CON_HANG'),
(204, 120, 'SWIFT14-1TB-GREEN', 'Swift Go 14 1TB', 'Xanh', '1TB', '16GB', NULL, 18500000.00, 20500000.00, 5, 'CON_HANG'),
(205, 120, 'SWIFT14-1TB-GOLD', 'Swift Go 14 1TB', 'Vàng', '1TB', '16GB', NULL, 18500000.00, 20500000.00, 4, 'CON_HANG'),
(206, 121, 'KATANA15-512-BLACK', 'Katana 15 512GB', 'Đen', '512GB', '16GB', NULL, 21000000.00, 23000000.00, 8, 'CON_HANG'),
(207, 121, 'KATANA15-1TB-BLACK', 'Katana 15 1TB', 'Đen', '1TB', '16GB', NULL, 23000000.00, 25000000.00, 6, 'CON_HANG'),
(208, 121, 'KATANA15-512-RGB', 'Katana 15 RGB', 'RGB', '512GB', '16GB', NULL, 22000000.00, 24000000.00, 5, 'CON_HANG'),
(209, 121, 'KATANA15-1TB-RGB', 'Katana 15 RGB', 'RGB', '1TB', '32GB', NULL, 26000000.00, 28000000.00, 4, 'CON_HANG'),
(210, 121, 'KATANA15-2TB-BLACK', 'Katana 15 2TB', 'Đen', '2TB', '32GB', NULL, 30000000.00, 32000000.00, 2, 'CON_HANG'),
(211, 122, 'GIGA5-512-BLACK', 'Gigabyte G5 512GB', 'Đen', '512GB', '16GB', NULL, 17500000.00, 19500000.00, 10, 'CON_HANG'),
(212, 122, 'GIGA5-1TB-BLACK', 'Gigabyte G5 1TB', 'Đen', '1TB', '16GB', NULL, 19500000.00, 21500000.00, 8, 'CON_HANG'),
(213, 122, 'GIGA5-512-GRAY', 'Gigabyte G5 512GB', 'Xám', '512GB', '16GB', NULL, 17500000.00, 19500000.00, 7, 'CON_HANG'),
(214, 122, 'GIGA5-1TB-GRAY', 'Gigabyte G5 1TB', 'Xám', '1TB', '16GB', NULL, 19500000.00, 21500000.00, 6, 'CON_HANG'),
(215, 122, 'GIGA5-2TB-BLACK', 'Gigabyte G5 2TB', 'Đen', '2TB', '32GB', NULL, 22500000.00, 24500000.00, 4, 'CON_HANG'),
(216, 123, 'LGGRAM16-512-WHITE', 'LG Gram 16 512GB', 'Trắng', '512GB', '16GB', NULL, 28000000.00, 30000000.00, 7, 'CON_HANG'),
(217, 123, 'LGGRAM16-1TB-WHITE', 'LG Gram 16 1TB', 'Trắng', '1TB', '16GB', NULL, 30000000.00, 32000000.00, 5, 'CON_HANG'),
(218, 123, 'LGGRAM16-512-BLACK', 'LG Gram 16 512GB', 'Đen', '512GB', '16GB', NULL, 28000000.00, 30000000.00, 6, 'CON_HANG'),
(219, 123, 'LGGRAM16-1TB-BLACK', 'LG Gram 16 1TB', 'Đen', '1TB', '16GB', NULL, 30000000.00, 32000000.00, 4, 'CON_HANG'),
(220, 123, 'LGGRAM16-2TB-BLACK', 'LG Gram 16 2TB', 'Đen', '2TB', '32GB', NULL, 34000000.00, 36000000.00, 3, 'CON_HANG'),
(221, 124, 'SURFACE5-256-SILVER', 'Surface Laptop 5 256GB', 'Bạc', '256GB', '8GB', NULL, 23000000.00, 25000000.00, 9, 'CON_HANG'),
(222, 124, 'SURFACE5-512-SILVER', 'Surface Laptop 5 512GB', 'Bạc', '512GB', '16GB', NULL, 26000000.00, 28000000.00, 7, 'CON_HANG'),
(223, 124, 'SURFACE5-256-BLACK', 'Surface Laptop 5 256GB', 'Đen', '256GB', '8GB', NULL, 23000000.00, 25000000.00, 6, 'CON_HANG'),
(224, 124, 'SURFACE5-512-BLACK', 'Surface Laptop 5 512GB', 'Đen', '512GB', '16GB', NULL, 26000000.00, 28000000.00, 5, 'CON_HANG'),
(225, 124, 'SURFACE5-1TB-BLACK', 'Surface Laptop 5 1TB', 'Đen', '1TB', '16GB', NULL, 30000000.00, 32000000.00, 3, 'CON_HANG'),
(226, 125, 'U2723QE-4K-BLACK', 'Dell U2723QE 4K', 'Đen', '4K', 'N/A', NULL, 15500000.00, 17000000.00, 6, 'CON_HANG'),
(227, 125, 'U2723QE-4K-HUB', 'Dell U2723QE USB-C Hub', 'Đen', '4K', 'N/A', NULL, 16500000.00, 18000000.00, 5, 'CON_HANG'),
(228, 125, 'U2723QE-IPS', 'Dell U2723QE IPS Black', 'Đen', '4K', 'N/A', NULL, 17500000.00, 19000000.00, 4, 'CON_HANG'),
(229, 125, 'U2723QE-ADJUST', 'Dell U2723QE Adjustable', 'Đen', '4K', 'N/A', NULL, 16000000.00, 17500000.00, 5, 'CON_HANG'),
(230, 125, 'U2723QE-PRO', 'Dell U2723QE Pro', 'Đen', '4K', 'N/A', NULL, 18500000.00, 20000000.00, 3, 'CON_HANG'),
(231, 126, 'LG27UP-4K-WHITE', 'LG 27UP850N 4K', 'Trắng', '4K', 'N/A', NULL, 9500000.00, 11000000.00, 7, 'CON_HANG'),
(232, 126, 'LG27UP-USB-C', 'LG 27UP850N USB-C', 'Trắng', '4K', 'N/A', NULL, 10500000.00, 12000000.00, 6, 'CON_HANG'),
(233, 126, 'LG27UP-HDR', 'LG 27UP850N HDR', 'Trắng', '4K', 'N/A', NULL, 9900000.00, 11500000.00, 6, 'CON_HANG'),
(234, 126, 'LG27UP-ADJUST', 'LG 27UP850N Adjustable', 'Trắng', '4K', 'N/A', NULL, 10000000.00, 11500000.00, 5, 'CON_HANG'),
(235, 126, 'LG27UP-PRO', 'LG 27UP850N Pro', 'Trắng', '4K', 'N/A', NULL, 11500000.00, 13000000.00, 4, 'CON_HANG'),
(236, 127, 'ODYG5-144-BLACK', 'Odyssey G5 144Hz', 'Đen', '2K', 'N/A', NULL, 6500000.00, 7500000.00, 10, 'CON_HANG'),
(237, 127, 'ODYG5-165-BLACK', 'Odyssey G5 165Hz', 'Đen', '2K', 'N/A', NULL, 7000000.00, 8000000.00, 8, 'CON_HANG'),
(238, 127, 'ODYG5-CURVE', 'Odyssey G5 Cong', 'Đen', '2K', 'N/A', NULL, 6800000.00, 7800000.00, 7, 'CON_HANG'),
(239, 127, 'ODYG5-GAMING', 'Odyssey G5 Gaming', 'Đen', '2K', 'N/A', NULL, 7200000.00, 8200000.00, 6, 'CON_HANG'),
(240, 127, 'ODYG5-RGB', 'Odyssey G5 RGB', 'Đen RGB', '2K', 'N/A', NULL, 7500000.00, 8500000.00, 5, 'CON_HANG'),
(241, 128, 'PA248QV-IPS', 'ProArt PA248QV IPS', 'Đen', 'FHD+', 'N/A', NULL, 5200000.00, 6000000.00, 9, 'CON_HANG'),
(242, 128, 'PA248QV-CALIB', 'ProArt PA248QV Calibrated', 'Đen', 'FHD+', 'N/A', NULL, 5500000.00, 6300000.00, 7, 'CON_HANG'),
(243, 128, 'PA248QV-ADJUST', 'ProArt PA248QV Adjustable', 'Đen', 'FHD+', 'N/A', NULL, 5300000.00, 6100000.00, 6, 'CON_HANG'),
(244, 128, 'PA248QV-DESIGN', 'ProArt PA248QV Design', 'Đen', 'FHD+', 'N/A', NULL, 5600000.00, 6400000.00, 5, 'CON_HANG'),
(245, 128, 'PA248QV-PRO', 'ProArt PA248QV Pro', 'Đen', 'FHD+', 'N/A', NULL, 5900000.00, 6700000.00, 4, 'CON_HANG'),
(246, 129, 'G24F2-165', 'Gigabyte G24F2 165Hz', 'Đen', 'FHD', 'N/A', NULL, 3500000.00, 4200000.00, 12, 'CON_HANG'),
(247, 129, 'G24F2-180', 'Gigabyte G24F2 180Hz', 'Đen', 'FHD', 'N/A', NULL, 3800000.00, 4500000.00, 10, 'CON_HANG'),
(248, 129, 'G24F2-IPS', 'Gigabyte G24F2 IPS', 'Đen', 'FHD', 'N/A', NULL, 3600000.00, 4300000.00, 9, 'CON_HANG'),
(249, 129, 'G24F2-GAMING', 'Gigabyte G24F2 Gaming', 'Đen', 'FHD', 'N/A', NULL, 3900000.00, 4600000.00, 8, 'CON_HANG'),
(250, 129, 'G24F2-RGB', 'Gigabyte G24F2 RGB', 'Đen RGB', 'FHD', 'N/A', NULL, 4100000.00, 4800000.00, 6, 'CON_HANG'),
(251, 130, 'OPTIX271-144', 'MSI G271 144Hz', 'Đen', 'FHD', 'N/A', NULL, 4800000.00, 5500000.00, 10, 'CON_HANG'),
(252, 130, 'OPTIX271-165', 'MSI G271 165Hz', 'Đen', 'FHD', 'N/A', NULL, 5100000.00, 5800000.00, 8, 'CON_HANG'),
(253, 130, 'OPTIX271-IPS', 'MSI G271 IPS', 'Đen', 'FHD', 'N/A', NULL, 4900000.00, 5600000.00, 7, 'CON_HANG'),
(254, 130, 'OPTIX271-GAMING', 'MSI G271 Gaming', 'Đen', 'FHD', 'N/A', NULL, 5200000.00, 5900000.00, 6, 'CON_HANG'),
(255, 130, 'OPTIX271-RGB', 'MSI G271 RGB', 'Đen RGB', 'FHD', 'N/A', NULL, 5500000.00, 6200000.00, 5, 'CON_HANG'),
(256, 131, 'MACSTUDIO-M2-512', 'Mac Studio M2 512GB', 'Bạc', '512GB', '32GB', NULL, 52000000.00, 54000000.00, 5, 'CON_HANG'),
(257, 131, 'MACSTUDIO-M2-1TB', 'Mac Studio M2 1TB', 'Bạc', '1TB', '32GB', NULL, 58000000.00, 60000000.00, 4, 'CON_HANG'),
(258, 131, 'MACSTUDIO-M2-2TB', 'Mac Studio M2 2TB', 'Bạc', '2TB', '64GB', NULL, 65000000.00, 67000000.00, 3, 'CON_HANG'),
(259, 131, 'MACSTUDIO-M2ULTRA', 'Mac Studio M2 Ultra', 'Bạc', '1TB', '64GB', NULL, 75000000.00, 78000000.00, 2, 'CON_HANG'),
(260, 131, 'MACSTUDIO-M2ULTRA-2TB', 'Mac Studio M2 Ultra 2TB', 'Bạc', '2TB', '128GB', NULL, 90000000.00, 93000000.00, 1, 'CON_HANG'),
(261, 132, 'SENTINEL-I7-4070', 'Sentinel i7 RTX 4070', 'Đen', '1TB', '16GB', NULL, 35000000.00, 37000000.00, 6, 'CON_HANG'),
(262, 132, 'SENTINEL-I7-4070TI', 'Sentinel i7 RTX 4070Ti', 'Đen', '1TB', '32GB', NULL, 42000000.00, 44000000.00, 5, 'CON_HANG'),
(263, 132, 'SENTINEL-I9-4080', 'Sentinel i9 RTX 4080', 'Đen', '2TB', '32GB', NULL, 52000000.00, 55000000.00, 4, 'CON_HANG'),
(264, 132, 'SENTINEL-I9-4090', 'Sentinel i9 RTX 4090', 'Đen', '2TB', '64GB', NULL, 65000000.00, 68000000.00, 3, 'CON_HANG'),
(265, 132, 'SENTINEL-RGB', 'Sentinel Full RGB', 'Đen RGB', '1TB', '32GB', NULL, 38000000.00, 40000000.00, 5, 'CON_HANG'),
(266, 133, 'HPDESK-I3-256', 'HP Desktop i3', 'Đen', '256GB', '8GB', NULL, 12000000.00, 13500000.00, 10, 'CON_HANG'),
(267, 133, 'HPDESK-I5-512', 'HP Desktop i5', 'Đen', '512GB', '8GB', NULL, 14000000.00, 15500000.00, 8, 'CON_HANG'),
(268, 133, 'HPDESK-I5-1TB', 'HP Desktop i5', 'Đen', '1TB', '16GB', NULL, 16000000.00, 17500000.00, 7, 'CON_HANG'),
(269, 133, 'HPDESK-I7-512', 'HP Desktop i7', 'Đen', '512GB', '16GB', NULL, 18000000.00, 19500000.00, 6, 'CON_HANG'),
(270, 133, 'HPDESK-I7-1TB', 'HP Desktop i7', 'Đen', '1TB', '16GB', NULL, 20000000.00, 21500000.00, 5, 'CON_HANG'),
(271, 134, 'ROG-I7-4060', 'ROG Strix i7 RTX 4060', 'Đen', '1TB', '16GB', NULL, 42000000.00, 44000000.00, 6, 'CON_HANG'),
(272, 134, 'ROG-I7-4070', 'ROG Strix i7 RTX 4070', 'Đen', '1TB', '32GB', NULL, 48000000.00, 50000000.00, 5, 'CON_HANG'),
(273, 134, 'ROG-I9-4080', 'ROG Strix i9 RTX 4080', 'Đen', '2TB', '32GB', NULL, 58000000.00, 61000000.00, 4, 'CON_HANG'),
(274, 134, 'ROG-I9-4090', 'ROG Strix i9 RTX 4090', 'Đen', '2TB', '64GB', NULL, 70000000.00, 73000000.00, 3, 'CON_HANG'),
(275, 134, 'ROG-RGB', 'ROG Strix Full RGB', 'Đen RGB', '1TB', '32GB', NULL, 46000000.00, 48000000.00, 5, 'CON_HANG'),
(276, 135, 'G502X-BLACK', 'G502 X Plus Black', 'Đen', 'N/A', 'N/A', NULL, 3200000.00, 3500000.00, 10, 'CON_HANG'),
(277, 135, 'G502X-WHITE', 'G502 X Plus White', 'Trắng', 'N/A', 'N/A', NULL, 3200000.00, 3500000.00, 8, 'CON_HANG'),
(278, 135, 'G502X-RGB', 'G502 X Plus RGB', 'RGB', 'N/A', 'N/A', NULL, 3400000.00, 3700000.00, 7, 'CON_HANG'),
(279, 135, 'G502X-WIRELESS', 'G502 X Wireless', 'Đen', 'N/A', 'N/A', NULL, 3000000.00, 3300000.00, 9, 'CON_HANG'),
(280, 135, 'G502X-LIGHT', 'G502 X Lightweight', 'Đen', 'N/A', 'N/A', NULL, 3100000.00, 3400000.00, 6, 'CON_HANG'),
(281, 136, 'AKKO3087-BLACK', 'Akko 3087 Black', 'Đen', 'N/A', 'N/A', NULL, 1200000.00, 1400000.00, 12, 'CON_HANG'),
(282, 136, 'AKKO3087-WHITE', 'Akko 3087 White', 'Trắng', 'N/A', 'N/A', NULL, 1200000.00, 1400000.00, 10, 'CON_HANG'),
(283, 136, 'AKKO3087-RGB', 'Akko 3087 RGB', 'RGB', 'N/A', 'N/A', NULL, 1400000.00, 1600000.00, 8, 'CON_HANG'),
(284, 136, 'AKKO3087-BLUE', 'Akko 3087 Blue Switch', 'Xanh', 'N/A', 'N/A', NULL, 1300000.00, 1500000.00, 9, 'CON_HANG'),
(285, 136, 'AKKO3087-RED', 'Akko 3087 Red Switch', 'Đỏ', 'N/A', 'N/A', NULL, 1300000.00, 1500000.00, 9, 'CON_HANG'),
(286, 137, 'XM5-BLACK', 'Sony XM5 Black', 'Đen', 'N/A', 'N/A', NULL, 7500000.00, 8000000.00, 10, 'CON_HANG'),
(287, 137, 'XM5-SILVER', 'Sony XM5 Silver', 'Bạc', 'N/A', 'N/A', NULL, 7500000.00, 8000000.00, 8, 'CON_HANG'),
(288, 137, 'XM5-LIMITED', 'Sony XM5 Limited', 'Đen', 'N/A', 'N/A', NULL, 7800000.00, 8300000.00, 6, 'CON_HANG'),
(289, 137, 'XM5-WIRELESS', 'Sony XM5 Wireless', 'Đen', 'N/A', 'N/A', NULL, 7500000.00, 8000000.00, 7, 'CON_HANG'),
(290, 137, 'XM5-TRAVEL', 'Sony XM5 Travel Set', 'Đen', 'N/A', 'N/A', NULL, 8000000.00, 8500000.00, 5, 'CON_HANG'),
(291, 138, 'EMBER2-BLACK', 'Emberton II Black', 'Đen', 'N/A', 'N/A', NULL, 3800000.00, 4200000.00, 10, 'CON_HANG'),
(292, 138, 'EMBER2-CREAM', 'Emberton II Cream', 'Kem', 'N/A', 'N/A', NULL, 3800000.00, 4200000.00, 8, 'CON_HANG'),
(293, 138, 'EMBER2-GREEN', 'Emberton II Green', 'Xanh', 'N/A', 'N/A', NULL, 3900000.00, 4300000.00, 7, 'CON_HANG'),
(294, 138, 'EMBER2-BLUETOOTH', 'Emberton II Bluetooth', 'Đen', 'N/A', 'N/A', NULL, 3800000.00, 4200000.00, 9, 'CON_HANG'),
(295, 138, 'EMBER2-LIMITED', 'Emberton II Limited', 'Đen', 'N/A', 'N/A', NULL, 4200000.00, 4600000.00, 5, 'CON_HANG'),
(296, 139, 'ANKER737-24K', 'Anker 737 24000mAh', 'Đen', '24000mAh', 'N/A', NULL, 2500000.00, 2800000.00, 10, 'CON_HANG'),
(297, 139, 'ANKER737-20K', 'Anker 737 20000mAh', 'Đen', '20000mAh', 'N/A', NULL, 2300000.00, 2600000.00, 8, 'CON_HANG'),
(298, 139, 'ANKER737-GRAY', 'Anker 737 Gray', 'Xám', '24000mAh', 'N/A', NULL, 2500000.00, 2800000.00, 7, 'CON_HANG'),
(299, 139, 'ANKER737-PD', 'Anker 737 PD 140W', 'Đen', '24000mAh', 'N/A', NULL, 2700000.00, 3000000.00, 6, 'CON_HANG'),
(300, 139, 'ANKER737-MINI', 'Anker 737 Mini', 'Đen', '10000mAh', 'N/A', NULL, 1800000.00, 2100000.00, 9, 'CON_HANG'),
(301, 140, 'C922-1080', 'Logitech C922 1080p', 'Đen', 'FullHD', 'N/A', NULL, 1900000.00, 2200000.00, 10, 'CON_HANG'),
(302, 140, 'C922-STREAM', 'Logitech C922 Stream', 'Đen', 'FullHD', 'N/A', NULL, 2000000.00, 2300000.00, 8, 'CON_HANG'),
(303, 140, 'C922-TRIPOD', 'Logitech C922 Tripod', 'Đen', 'FullHD', 'N/A', NULL, 2100000.00, 2400000.00, 7, 'CON_HANG'),
(304, 140, 'C922-PRO', 'Logitech C922 Pro', 'Đen', 'FullHD', 'N/A', NULL, 2200000.00, 2500000.00, 6, 'CON_HANG'),
(305, 140, 'C922-BASIC', 'Logitech C922 Basic', 'Đen', 'FullHD', 'N/A', NULL, 1800000.00, 2100000.00, 9, 'CON_HANG'),
(306, 141, 'FPT-G99-1M', 'G99 - 1 tháng', 'N/A', '4GB/ngày', 'N/A', NULL, 150000.00, 180000.00, 50, 'CON_HANG'),
(307, 141, 'FPT-G99-3M', 'G99 - 3 tháng', 'N/A', '4GB/ngày', 'N/A', NULL, 420000.00, 500000.00, 40, 'CON_HANG'),
(308, 141, 'FPT-G99-6M', 'G99 - 6 tháng', 'N/A', '4GB/ngày', 'N/A', NULL, 800000.00, 900000.00, 30, 'CON_HANG'),
(309, 141, 'FPT-G99-12M', 'G99 - 12 tháng', 'N/A', '4GB/ngày', 'N/A', NULL, 1500000.00, 1700000.00, 20, 'CON_HANG'),
(310, 142, 'FPT-G120-1M', 'G120 - 1 tháng', 'N/A', '6GB/ngày', 'N/A', NULL, 250000.00, 280000.00, 50, 'CON_HANG'),
(311, 142, 'FPT-G120-3M', 'G120 - 3 tháng', 'N/A', '6GB/ngày', 'N/A', NULL, 700000.00, 800000.00, 40, 'CON_HANG'),
(312, 142, 'FPT-G120-6M', 'G120 - 6 tháng', 'N/A', '6GB/ngày', 'N/A', NULL, 1300000.00, 1500000.00, 30, 'CON_HANG'),
(313, 142, 'FPT-G120-12M', 'G120 - 12 tháng', 'N/A', '6GB/ngày', 'N/A', NULL, 2400000.00, 2700000.00, 20, 'CON_HANG'),
(314, 143, 'FPT-V60-1M', 'V60 - 1 tháng', 'N/A', '2GB/ngày', 'N/A', NULL, 90000.00, 110000.00, 60, 'CON_HANG'),
(315, 143, 'FPT-V60-3M', 'V60 - 3 tháng', 'N/A', '2GB/ngày', 'N/A', NULL, 250000.00, 300000.00, 50, 'CON_HANG'),
(316, 143, 'FPT-V60-6M', 'V60 - 6 tháng', 'N/A', '2GB/ngày', 'N/A', NULL, 480000.00, 550000.00, 40, 'CON_HANG'),
(317, 143, 'FPT-V60-12M', 'V60 - 12 tháng', 'N/A', '2GB/ngày', 'N/A', NULL, 900000.00, 1000000.00, 30, 'CON_HANG'),
(318, 144, 'FPT-12T-STD', '12T - Gói chuẩn', 'N/A', 'Không giới hạn', 'N/A', NULL, 1200000.00, 1400000.00, 25, 'CON_HANG'),
(319, 144, 'FPT-12T-PRO', '12T - Gói Pro', 'N/A', 'Không giới hạn', 'N/A', NULL, 1400000.00, 1600000.00, 20, 'CON_HANG'),
(320, 144, 'FPT-12T-VIP', '12T - Gói VIP', 'N/A', 'Không giới hạn', 'N/A', NULL, 1600000.00, 1800000.00, 15, 'CON_HANG'),
(321, 144, 'FPT-12T-STUDENT', '12T - Sinh viên', 'N/A', 'Không giới hạn', 'N/A', NULL, 1000000.00, 1200000.00, 30, 'CON_HANG'),
(322, 145, 'AWU2-49-GPS', 'Ultra 2 49mm GPS', 'Titan', '36h', 'GPS', NULL, 21000000.00, 23000000.00, 10, 'CON_HANG'),
(323, 145, 'AWU2-49-LTE', 'Ultra 2 49mm LTE', 'Titan', '36h', 'LTE', NULL, 23000000.00, 25000000.00, 8, 'CON_HANG'),
(324, 145, 'AWU2-OCEAN', 'Ultra 2 Ocean Band', 'Xanh', '36h', 'LTE', NULL, 23500000.00, 25500000.00, 6, 'CON_HANG'),
(325, 145, 'AWU2-TRAIL', 'Ultra 2 Trail Loop', 'Đen', '36h', 'GPS', NULL, 21500000.00, 23500000.00, 7, 'CON_HANG'),
(326, 146, 'GW6-43-GPS', 'Watch 6 43mm GPS', 'Đen', '40h', 'GPS', NULL, 7500000.00, 8500000.00, 10, 'CON_HANG'),
(327, 146, 'GW6-43-LTE', 'Watch 6 43mm LTE', 'Đen', '40h', 'LTE', NULL, 8500000.00, 9500000.00, 8, 'CON_HANG'),
(328, 146, 'GW6-47-GPS', 'Watch 6 47mm GPS', 'Bạc', '40h', 'GPS', NULL, 7800000.00, 8800000.00, 9, 'CON_HANG'),
(329, 146, 'GW6-47-LTE', 'Watch 6 47mm LTE', 'Bạc', '40h', 'LTE', NULL, 8800000.00, 9800000.00, 7, 'CON_HANG'),
(330, 147, 'GR255-STD', 'Forerunner 255 Standard', 'Đen', '14 ngày', 'GPS', NULL, 8500000.00, 9500000.00, 10, 'CON_HANG'),
(331, 147, 'GR255-MUSIC', 'Forerunner 255 Music', 'Đen', '14 ngày', 'GPS', NULL, 9500000.00, 10500000.00, 8, 'CON_HANG'),
(332, 147, 'GR255-WHITE', 'Forerunner 255 White', 'Trắng', '14 ngày', 'GPS', NULL, 8600000.00, 9600000.00, 7, 'CON_HANG'),
(333, 147, 'GR255-BLUE', 'Forerunner 255 Blue', 'Xanh', '14 ngày', 'GPS', NULL, 8600000.00, 9600000.00, 6, 'CON_HANG'),
(334, 148, 'XWS3-STD', 'Watch S3 Standard', 'Đen', '15 ngày', 'GPS', NULL, 3500000.00, 4000000.00, 12, 'CON_HANG'),
(335, 148, 'XWS3-SILVER', 'Watch S3 Silver', 'Bạc', '15 ngày', 'GPS', NULL, 3600000.00, 4100000.00, 10, 'CON_HANG'),
(336, 148, 'XWS3-LEATHER', 'Watch S3 Leather', 'Nâu', '15 ngày', 'GPS', NULL, 3800000.00, 4300000.00, 8, 'CON_HANG'),
(337, 148, 'XWS3-LIMITED', 'Watch S3 Limited', 'Đen', '15 ngày', 'GPS', NULL, 4000000.00, 4500000.00, 6, 'CON_HANG'),
(338, 149, 'HWGT4-41', 'GT 4 41mm', 'Trắng', '14 ngày', 'GPS', NULL, 4500000.00, 5000000.00, 10, 'CON_HANG'),
(339, 149, 'HWGT4-46', 'GT 4 46mm', 'Đen', '14 ngày', 'GPS', NULL, 4700000.00, 5200000.00, 9, 'CON_HANG'),
(340, 149, 'HWGT4-LEATHER', 'GT 4 Leather', 'Nâu', '14 ngày', 'GPS', NULL, 4900000.00, 5400000.00, 7, 'CON_HANG'),
(341, 149, 'HWGT4-ELITE', 'GT 4 Elite', 'Bạc', '14 ngày', 'GPS', NULL, 5200000.00, 5800000.00, 6, 'CON_HANG'),
(342, 150, 'GTR4-STD', 'GTR 4 Standard', 'Đen', '14 ngày', 'GPS', NULL, 4000000.00, 4500000.00, 12, 'CON_HANG'),
(343, 150, 'GTR4-BROWN', 'GTR 4 Brown', 'Nâu', '14 ngày', 'GPS', NULL, 4100000.00, 4600000.00, 10, 'CON_HANG'),
(344, 150, 'GTR4-SILVER', 'GTR 4 Silver', 'Bạc', '14 ngày', 'GPS', NULL, 4200000.00, 4700000.00, 8, 'CON_HANG'),
(345, 150, 'GTR4-LIMITED', 'GTR 4 Limited', 'Đen', '14 ngày', 'GPS', NULL, 4500000.00, 5000000.00, 6, 'CON_HANG'),
(346, 151, 'SONY-A80L-55', 'A80L 55 inch', 'Đen', '55 inch', '120Hz', NULL, 35000000.00, 38000000.00, 8, 'CON_HANG'),
(347, 151, 'SONY-A80L-65', 'A80L 65 inch', 'Đen', '65 inch', '120Hz', NULL, 42000000.00, 46000000.00, 6, 'CON_HANG'),
(348, 151, 'SONY-A80L-77', 'A80L 77 inch', 'Đen', '77 inch', '120Hz', NULL, 65000000.00, 70000000.00, 4, 'CON_HANG'),
(349, 151, 'SONY-A80L-55-STD', 'A80L 55 inch bản chuẩn', 'Đen', '55 inch', '60Hz', NULL, 33000000.00, 36000000.00, 7, 'CON_HANG'),
(350, 152, 'SS-QN90C-55', 'QN90C 55 inch', 'Đen', '55 inch', '120Hz', NULL, 28000000.00, 31000000.00, 10, 'CON_HANG'),
(351, 152, 'SS-QN90C-65', 'QN90C 65 inch', 'Đen', '65 inch', '120Hz', NULL, 35000000.00, 39000000.00, 8, 'CON_HANG'),
(352, 152, 'SS-QN90C-75', 'QN90C 75 inch', 'Đen', '75 inch', '120Hz', NULL, 50000000.00, 55000000.00, 5, 'CON_HANG'),
(353, 152, 'SS-QN90C-55-144', 'QN90C 55 inch Gaming', 'Đen', '55 inch', '144Hz', NULL, 30000000.00, 33000000.00, 6, 'CON_HANG'),
(354, 153, 'LG-C3-55', 'LG C3 55 inch', 'Đen', '55 inch', '120Hz', NULL, 30000000.00, 33000000.00, 9, 'CON_HANG'),
(355, 153, 'LG-C3-65', 'LG C3 65 inch', 'Đen', '65 inch', '120Hz', NULL, 38000000.00, 42000000.00, 7, 'CON_HANG'),
(356, 153, 'LG-C3-77', 'LG C3 77 inch', 'Đen', '77 inch', '120Hz', NULL, 60000000.00, 65000000.00, 4, 'CON_HANG'),
(357, 153, 'LG-C3-42', 'LG C3 42 inch', 'Đen', '42 inch', '120Hz', NULL, 25000000.00, 28000000.00, 6, 'CON_HANG'),
(358, 154, 'TCL-C845-55', 'C845 55 inch', 'Đen', '55 inch', '144Hz', NULL, 18000000.00, 20000000.00, 12, 'CON_HANG'),
(359, 154, 'TCL-C845-65', 'C845 65 inch', 'Đen', '65 inch', '144Hz', NULL, 23000000.00, 26000000.00, 10, 'CON_HANG'),
(360, 154, 'TCL-C845-75', 'C845 75 inch', 'Đen', '75 inch', '144Hz', NULL, 32000000.00, 35000000.00, 6, 'CON_HANG'),
(361, 154, 'TCL-C845-85', 'C845 85 inch', 'Đen', '85 inch', '144Hz', NULL, 45000000.00, 50000000.00, 3, 'CON_HANG'),
(362, 155, 'MI-A65-STD', 'Xiaomi A Pro 65', 'Đen', '65 inch', '60Hz', NULL, 11000000.00, 13000000.00, 15, 'CON_HANG'),
(363, 155, 'MI-A55', 'Xiaomi A Pro 55', 'Đen', '55 inch', '60Hz', NULL, 9000000.00, 11000000.00, 12, 'CON_HANG'),
(364, 155, 'MI-A75', 'Xiaomi A Pro 75', 'Đen', '75 inch', '60Hz', NULL, 15000000.00, 17000000.00, 8, 'CON_HANG'),
(365, 155, 'MI-A65-GG', 'Xiaomi A Pro 65 Google TV', 'Đen', '65 inch', '60Hz', NULL, 12000000.00, 14000000.00, 10, 'CON_HANG'),
(366, 156, 'CASPER-55', 'Casper 55 inch', 'Đen', '55 inch', '60Hz', NULL, 7500000.00, 9000000.00, 15, 'CON_HANG'),
(367, 156, 'CASPER-50', 'Casper 50 inch', 'Đen', '50 inch', '60Hz', NULL, 6500000.00, 8000000.00, 12, 'CON_HANG'),
(368, 156, 'CASPER-65', 'Casper 65 inch', 'Đen', '65 inch', '60Hz', NULL, 10000000.00, 12000000.00, 10, 'CON_HANG'),
(369, 156, 'CASPER-43', 'Casper 43 inch', 'Đen', '43 inch', '60Hz', NULL, 5500000.00, 7000000.00, 14, 'CON_HANG'),
(370, 157, 'DAIKIN-1HP-STD', 'Daikin 1HP Standard', 'Trắng', '1 HP', 'Inverter', NULL, 10500000.00, 12000000.00, 10, 'CON_HANG'),
(371, 157, 'DAIKIN-1HP-PRO', 'Daikin 1HP Pro', 'Trắng', '1 HP', 'Inverter', NULL, 11500000.00, 13000000.00, 8, 'CON_HANG'),
(372, 157, 'DAIKIN-1HP-GAS', 'Daikin 1HP Gas R32', 'Trắng', '1 HP', 'Inverter', NULL, 11000000.00, 12500000.00, 9, 'CON_HANG'),
(373, 157, 'DAIKIN-1HP-SMART', 'Daikin 1HP Smart', 'Trắng', '1 HP', 'Inverter WiFi', NULL, 12500000.00, 14000000.00, 6, 'CON_HANG'),
(374, 158, 'PANA-15HP-STD', 'Panasonic 1.5HP Standard', 'Trắng', '1.5 HP', 'Inverter', NULL, 13000000.00, 14500000.00, 10, 'CON_HANG'),
(375, 158, 'PANA-15HP-NANO', 'Panasonic 1.5HP Nanoe-G', 'Trắng', '1.5 HP', 'Inverter', NULL, 14000000.00, 15500000.00, 8, 'CON_HANG'),
(376, 158, 'PANA-15HP-WIFI', 'Panasonic 1.5HP WiFi', 'Trắng', '1.5 HP', 'Inverter WiFi', NULL, 15000000.00, 16500000.00, 6, 'CON_HANG'),
(377, 158, 'PANA-15HP-PRO', 'Panasonic 1.5HP Pro', 'Trắng', '1.5 HP', 'Inverter', NULL, 14500000.00, 16000000.00, 7, 'CON_HANG'),
(378, 159, 'LG-1HP-STD', 'LG 1HP Standard', 'Trắng', '1 HP', 'Dual Inverter', NULL, 8500000.00, 10000000.00, 12, 'CON_HANG'),
(379, 159, 'LG-1HP-WIFI', 'LG 1HP WiFi', 'Trắng', '1 HP', 'Dual Inverter', NULL, 9500000.00, 11000000.00, 10, 'CON_HANG'),
(380, 159, 'LG-1HP-ION', 'LG 1HP Ionizer', 'Trắng', '1 HP', 'Dual Inverter', NULL, 9000000.00, 10500000.00, 9, 'CON_HANG'),
(381, 159, 'LG-1HP-PRO', 'LG 1HP Pro', 'Trắng', '1 HP', 'Dual Inverter', NULL, 9800000.00, 11500000.00, 8, 'CON_HANG'),
(382, 160, 'SS-1HP-WF', 'Samsung Wind-Free', 'Trắng', '1 HP', 'Wind-Free', NULL, 9800000.00, 11500000.00, 10, 'CON_HANG'),
(383, 160, 'SS-1HP-WIFI', 'Samsung Wind-Free WiFi', 'Trắng', '1 HP', 'Wind-Free', NULL, 11000000.00, 12500000.00, 8, 'CON_HANG'),
(384, 160, 'SS-1HP-AI', 'Samsung Wind-Free AI', 'Trắng', '1 HP', 'Wind-Free AI', NULL, 12000000.00, 13500000.00, 6, 'CON_HANG'),
(385, 160, 'SS-1HP-PRO', 'Samsung Wind-Free Pro', 'Trắng', '1 HP', 'Wind-Free', NULL, 11500000.00, 13000000.00, 7, 'CON_HANG'),
(386, 161, 'CASPER-1HP-STD', 'Casper 1HP Standard', 'Trắng', '1 HP', 'Inverter', NULL, 6000000.00, 7500000.00, 15, 'CON_HANG'),
(387, 161, 'CASPER-1HP-ECO', 'Casper 1HP Eco', 'Trắng', '1 HP', 'Inverter', NULL, 6500000.00, 8000000.00, 12, 'CON_HANG'),
(388, 161, 'CASPER-1HP-WIFI', 'Casper 1HP WiFi', 'Trắng', '1 HP', 'Inverter WiFi', NULL, 7000000.00, 8500000.00, 10, 'CON_HANG'),
(389, 161, 'CASPER-1HP-PRO', 'Casper 1HP Pro', 'Trắng', '1 HP', 'Inverter', NULL, 6800000.00, 8200000.00, 11, 'CON_HANG'),
(390, 162, 'S8PU-STD', 'S8 Pro Ultra Standard', 'Đen', '180 phút', '6000Pa', NULL, 25000000.00, 27000000.00, 8, 'CON_HANG'),
(391, 162, 'S8PU-PLUS', 'S8 Pro Ultra Plus', 'Đen', '180 phút', '6000Pa', NULL, 26000000.00, 28000000.00, 6, 'CON_HANG'),
(392, 162, 'S8PU-DOCK', 'S8 Pro Ultra Full Dock', 'Đen', '180 phút', '6000Pa', NULL, 28000000.00, 30000000.00, 5, 'CON_HANG'),
(393, 162, 'S8PU-WHITE', 'S8 Pro Ultra White', 'Trắng', '180 phút', '6000Pa', NULL, 25500000.00, 27500000.00, 7, 'CON_HANG'),
(394, 163, 'L20-STD', 'L20 Ultra Standard', 'Đen', '210 phút', '7000Pa', NULL, 22000000.00, 24000000.00, 10, 'CON_HANG'),
(395, 163, 'L20-DOCK', 'L20 Ultra Auto Dock', 'Đen', '210 phút', '7000Pa', NULL, 24000000.00, 26000000.00, 8, 'CON_HANG'),
(396, 163, 'L20-WASH', 'L20 Ultra Hot Wash', 'Đen', '210 phút', '7000Pa', NULL, 25000000.00, 27000000.00, 6, 'CON_HANG'),
(397, 163, 'L20-WHITE', 'L20 Ultra White', 'Trắng', '210 phút', '7000Pa', NULL, 22500000.00, 24500000.00, 7, 'CON_HANG'),
(398, 164, 'T20-STD', 'T20 Omni Standard', 'Đen', '180 phút', '6000Pa', NULL, 16000000.00, 18000000.00, 10, 'CON_HANG'),
(399, 164, 'T20-HOT', 'T20 Omni Hot Water', 'Đen', '180 phút', '6000Pa', NULL, 17500000.00, 19500000.00, 8, 'CON_HANG'),
(400, 164, 'T20-DOCK', 'T20 Omni Full Dock', 'Đen', '180 phút', '6000Pa', NULL, 18500000.00, 20500000.00, 6, 'CON_HANG'),
(401, 164, 'T20-WHITE', 'T20 Omni White', 'Trắng', '180 phút', '6000Pa', NULL, 16500000.00, 18500000.00, 7, 'CON_HANG'),
(402, 165, 'MI-VAC3-STD', 'Mi Vacuum 3 Standard', 'Trắng', '150 phút', '4000Pa', NULL, 5500000.00, 6500000.00, 15, 'CON_HANG'),
(403, 165, 'MI-VAC3-MOP', 'Mi Vacuum 3 Mop', 'Trắng', '150 phút', '4000Pa', NULL, 6000000.00, 7000000.00, 12, 'CON_HANG'),
(404, 165, 'MI-VAC3-PRO', 'Mi Vacuum 3 Pro', 'Trắng', '150 phút', '4000Pa', NULL, 6500000.00, 7500000.00, 10, 'CON_HANG'),
(405, 165, 'MI-VAC3-BLACK', 'Mi Vacuum 3 Black', 'Đen', '150 phút', '4000Pa', NULL, 5700000.00, 6700000.00, 13, 'CON_HANG'),
(406, 166, 'KG50F62-STD', 'KG50F62 Standard', 'Trắng', '50L', '5000 m3/h', NULL, 3500000.00, 4000000.00, 10, 'CON_HANG'),
(407, 166, 'KG50F62-REMOTE', 'KG50F62 Remote', 'Trắng', '50L', '5000 m3/h', NULL, 3700000.00, 4200000.00, 8, 'CON_HANG'),
(408, 166, 'KG50F62-ION', 'KG50F62 Ionizer', 'Trắng', '50L', '5000 m3/h', NULL, 3900000.00, 4400000.00, 7, 'CON_HANG'),
(409, 166, 'KG50F62-PRO', 'KG50F62 Pro', 'Trắng', '50L', '5500 m3/h', NULL, 4100000.00, 4600000.00, 6, 'CON_HANG'),
(410, 167, 'SHD7727-STD', 'SHD7727 Standard', 'Trắng', '40L', '4000 m3/h', NULL, 2800000.00, 3200000.00, 12, 'CON_HANG'),
(411, 167, 'SHD7727-REMOTE', 'SHD7727 Remote', 'Trắng', '40L', '4000 m3/h', NULL, 3000000.00, 3400000.00, 10, 'CON_HANG'),
(412, 167, 'SHD7727-ION', 'SHD7727 Ionizer', 'Trắng', '40L', '4000 m3/h', NULL, 3200000.00, 3600000.00, 8, 'CON_HANG'),
(413, 167, 'SHD7727-PRO', 'SHD7727 Pro', 'Trắng', '40L', '4500 m3/h', NULL, 3400000.00, 3800000.00, 7, 'CON_HANG'),
(414, 168, 'BOSS-S102-STD', 'Boss S102 Standard', 'Trắng', '60L', '6000 m3/h', NULL, 4200000.00, 4700000.00, 9, 'CON_HANG'),
(415, 168, 'BOSS-S102-REMOTE', 'Boss S102 Remote', 'Trắng', '60L', '6000 m3/h', NULL, 4400000.00, 4900000.00, 8, 'CON_HANG'),
(416, 168, 'BOSS-S102-ION', 'Boss S102 Ionizer', 'Trắng', '60L', '6000 m3/h', NULL, 4600000.00, 5100000.00, 7, 'CON_HANG'),
(417, 168, 'BOSS-S102-PRO', 'Boss S102 Pro', 'Trắng', '60L', '6500 m3/h', NULL, 4800000.00, 5300000.00, 6, 'CON_HANG'),
(418, 169, 'CLIMAX-M12-STD', 'Climax M12 Standard', 'Trắng', '30L', '3000 m3/h', NULL, 2100000.00, 2500000.00, 15, 'CON_HANG'),
(419, 169, 'CLIMAX-M12-REMOTE', 'Climax M12 Remote', 'Trắng', '30L', '3000 m3/h', NULL, 2300000.00, 2700000.00, 12, 'CON_HANG'),
(420, 169, 'CLIMAX-M12-ION', 'Climax M12 Ionizer', 'Trắng', '30L', '3000 m3/h', NULL, 2500000.00, 2900000.00, 10, 'CON_HANG'),
(421, 169, 'CLIMAX-M12-PRO', 'Climax M12 Pro', 'Trắng', '30L', '3500 m3/h', NULL, 2700000.00, 3100000.00, 9, 'CON_HANG'),
(422, 170, 'LG-10KG-STD', 'LG 10kg Standard', 'Trắng', '10kg', 'AI DD', NULL, 10500000.00, 12000000.00, 10, 'CON_HANG'),
(423, 170, 'LG-10KG-STEAM', 'LG 10kg Steam', 'Trắng', '10kg', 'AI DD + Steam', NULL, 11500000.00, 13000000.00, 8, 'CON_HANG'),
(424, 170, 'LG-10KG-INVERTER', 'LG 10kg Inverter', 'Trắng', '10kg', 'Inverter', NULL, 11000000.00, 12500000.00, 9, 'CON_HANG'),
(425, 170, 'LG-10KG-BLACK', 'LG 10kg Black', 'Đen', '10kg', 'AI DD', NULL, 10800000.00, 12300000.00, 7, 'CON_HANG'),
(426, 171, 'SS-9KG-STD', 'Samsung 9kg Standard', 'Trắng', '9kg', 'EcoBubble', NULL, 9000000.00, 10500000.00, 10, 'CON_HANG'),
(427, 171, 'SS-9KG-INVERTER', 'Samsung 9kg Inverter', 'Trắng', '9kg', 'EcoBubble + Inverter', NULL, 9500000.00, 11000000.00, 8, 'CON_HANG'),
(428, 171, 'SS-9KG-BLACK', 'Samsung 9kg Black', 'Đen', '9kg', 'EcoBubble', NULL, 9200000.00, 10700000.00, 9, 'CON_HANG'),
(429, 171, 'SS-9KG-STEAM', 'Samsung 9kg Steam', 'Trắng', '9kg', 'EcoBubble + Steam', NULL, 9800000.00, 11300000.00, 7, 'CON_HANG'),
(430, 172, 'TOSH-85KG-STD', 'Toshiba 8.5kg Standard', 'Trắng', '8.5kg', 'Inverter', NULL, 8200000.00, 9500000.00, 10, 'CON_HANG'),
(431, 172, 'TOSH-85KG-DD', 'Toshiba 8.5kg Direct Drive', 'Trắng', '8.5kg', 'Inverter DD', NULL, 8700000.00, 10000000.00, 8, 'CON_HANG'),
(432, 172, 'TOSH-85KG-BLACK', 'Toshiba 8.5kg Black', 'Đen', '8.5kg', 'Inverter', NULL, 8400000.00, 9700000.00, 9, 'CON_HANG'),
(433, 172, 'TOSH-85KG-PRO', 'Toshiba 8.5kg Pro', 'Trắng', '8.5kg', 'Inverter', NULL, 8800000.00, 10200000.00, 7, 'CON_HANG'),
(434, 173, 'ELX-10KG-STD', 'Electrolux 10kg Standard', 'Trắng', '10kg', 'Inverter', NULL, 12000000.00, 13500000.00, 10, 'CON_HANG'),
(435, 173, 'ELX-10KG-STEAM', 'Electrolux 10kg Steam', 'Trắng', '10kg', 'Inverter + Steam', NULL, 13000000.00, 14500000.00, 8, 'CON_HANG'),
(436, 173, 'ELX-10KG-BLACK', 'Electrolux 10kg Black', 'Đen', '10kg', 'Inverter', NULL, 12200000.00, 13700000.00, 9, 'CON_HANG'),
(437, 173, 'ELX-10KG-PRO', 'Electrolux 10kg Pro', 'Trắng', '10kg', 'Inverter', NULL, 13500000.00, 15000000.00, 7, 'CON_HANG'),
(438, 174, 'PANA-9KG-TOP', 'Panasonic 9kg Top', 'Trắng', '9kg', 'Cửa trên', NULL, 6500000.00, 8000000.00, 12, 'CON_HANG'),
(439, 174, 'PANA-9KG-INVERTER', 'Panasonic 9kg Inverter', 'Trắng', '9kg', 'Cửa trên + Inverter', NULL, 7000000.00, 8500000.00, 10, 'CON_HANG'),
(440, 174, 'PANA-9KG-BLACK', 'Panasonic 9kg Black', 'Đen', '9kg', 'Cửa trên', NULL, 6700000.00, 8200000.00, 11, 'CON_HANG'),
(441, 174, 'PANA-9KG-PRO', 'Panasonic 9kg Pro', 'Trắng', '9kg', 'Cửa trên', NULL, 7200000.00, 8700000.00, 9, 'CON_HANG'),
(442, 175, 'SS-400L-WHITE', 'Samsung 400L White', 'Trắng', '400L', 'Bespoke Inverter', NULL, 18000000.00, 20000000.00, 10, 'CON_HANG'),
(443, 175, 'SS-400L-BLACK', 'Samsung 400L Black', 'Đen', '400L', 'Bespoke Inverter', NULL, 18500000.00, 20500000.00, 9, 'CON_HANG'),
(444, 175, 'SS-400L-GLASS', 'Samsung 400L Glass', 'Gương', '400L', 'Bespoke Inverter', NULL, 19000000.00, 21000000.00, 8, 'CON_HANG'),
(445, 175, 'SS-400L-PRO', 'Samsung 400L Pro', 'Đen', '400L', 'Bespoke Inverter', NULL, 19500000.00, 21500000.00, 7, 'CON_HANG'),
(446, 176, 'PANA-550L-STD', 'Panasonic 550L Standard', 'Bạc', '550L', 'Multi Door Inverter', NULL, 32000000.00, 35000000.00, 8, 'CON_HANG'),
(447, 176, 'PANA-550L-PRIME', 'Panasonic 550L Prime Fresh', 'Bạc', '550L', 'Multi Door + Prime Fresh', NULL, 33000000.00, 36000000.00, 7, 'CON_HANG'),
(448, 176, 'PANA-550L-BLACK', 'Panasonic 550L Black', 'Đen', '550L', 'Multi Door Inverter', NULL, 32500000.00, 35500000.00, 6, 'CON_HANG'),
(449, 176, 'PANA-550L-PRO', 'Panasonic 550L Pro', 'Đen', '550L', 'Multi Door Inverter', NULL, 34000000.00, 37000000.00, 5, 'CON_HANG'),
(450, 177, 'LG-635L-STD', 'LG 635L Standard', 'Bạc', '635L', 'Side by Side Inverter', NULL, 21000000.00, 24000000.00, 9, 'CON_HANG'),
(451, 177, 'LG-635L-UV', 'LG 635L UV', 'Bạc', '635L', 'Side by Side + UV', NULL, 22000000.00, 25000000.00, 8, 'CON_HANG'),
(452, 177, 'LG-635L-BLACK', 'LG 635L Black', 'Đen', '635L', 'Side by Side Inverter', NULL, 21500000.00, 24500000.00, 7, 'CON_HANG'),
(453, 177, 'LG-635L-PRO', 'LG 635L Pro', 'Đen', '635L', 'Side by Side + UV', NULL, 23000000.00, 26000000.00, 6, 'CON_HANG'),
(454, 178, 'TOSH-180L-STD', 'Toshiba 180L Standard', 'Trắng', '180L', 'Ngăn đá trên', NULL, 4500000.00, 5500000.00, 15, 'CON_HANG'),
(455, 178, 'TOSH-180L-INVERTER', 'Toshiba 180L Inverter', 'Trắng', '180L', 'Ngăn đá trên + Inverter', NULL, 5000000.00, 6000000.00, 12, 'CON_HANG'),
(456, 178, 'TOSH-180L-BLACK', 'Toshiba 180L Black', 'Đen', '180L', 'Ngăn đá trên', NULL, 4700000.00, 5700000.00, 13, 'CON_HANG'),
(457, 178, 'TOSH-180L-PRO', 'Toshiba 180L Pro', 'Trắng', '180L', 'Ngăn đá trên', NULL, 5200000.00, 6200000.00, 10, 'CON_HANG'),
(458, 179, 'SHARP-322L-STD', 'Sharp 322L Standard', 'Bạc', '322L', 'Inverter', NULL, 8500000.00, 10000000.00, 12, 'CON_HANG'),
(459, 179, 'SHARP-322L-ION', 'Sharp 322L Ion', 'Bạc', '322L', 'Inverter + Khử mùi', NULL, 9000000.00, 10500000.00, 10, 'CON_HANG'),
(460, 179, 'SHARP-322L-BLACK', 'Sharp 322L Black', 'Đen', '322L', 'Inverter', NULL, 8700000.00, 10200000.00, 11, 'CON_HANG'),
(461, 179, 'SHARP-322L-PRO', 'Sharp 322L Pro', 'Đen', '322L', 'Inverter', NULL, 9200000.00, 10700000.00, 9, 'CON_HANG'),
(462, 180, 'KG100HK-STD', 'KG100HK Standard', 'Đen', '10 lõi', 'Hydrogen', NULL, 8500000.00, 10000000.00, 10, 'CON_HANG'),
(463, 180, 'KG100HK-HOTCOLD', 'KG100HK Nóng lạnh', 'Đen', '10 lõi', 'Hydrogen + Nóng lạnh', NULL, 9000000.00, 10500000.00, 8, 'CON_HANG'),
(464, 180, 'KG100HK-DISPLAY', 'KG100HK Display', 'Đen', '10 lõi', 'Hydrogen + TDS', NULL, 9200000.00, 10700000.00, 7, 'CON_HANG'),
(465, 180, 'KG100HK-PRO', 'KG100HK Pro', 'Đen', '10 lõi', 'Hydrogen + Nóng lạnh', NULL, 9500000.00, 11000000.00, 6, 'CON_HANG'),
(466, 181, 'KAROFI-OPT-STD', 'Karofi Standard', 'Trắng', '9 lõi', 'RO', NULL, 7200000.00, 8500000.00, 10, 'CON_HANG'),
(467, 181, 'KAROFI-OPT-DISPLAY', 'Karofi Display', 'Trắng', '9 lõi', 'RO + TDS', NULL, 7600000.00, 8900000.00, 8, 'CON_HANG'),
(468, 181, 'KAROFI-OPT-HOTCOLD', 'Karofi Nóng lạnh', 'Trắng', '9 lõi', 'RO + Nóng lạnh', NULL, 8000000.00, 9300000.00, 7, 'CON_HANG'),
(469, 181, 'KAROFI-OPT-PRO', 'Karofi Pro', 'Trắng', '9 lõi', 'RO + TDS', NULL, 8300000.00, 9600000.00, 6, 'CON_HANG'),
(470, 182, 'SUN-10LOI-STD', 'Sunhouse 10 lõi', 'Trắng', '10 lõi', 'RO', NULL, 5500000.00, 6500000.00, 12, 'CON_HANG'),
(471, 182, 'SUN-10LOI-DISPLAY', 'Sunhouse 10 lõi Display', 'Trắng', '10 lõi', 'RO + TDS', NULL, 5800000.00, 6800000.00, 10, 'CON_HANG'),
(472, 182, 'SUN-10LOI-HOTCOLD', 'Sunhouse 10 lõi Nóng lạnh', 'Trắng', '10 lõi', 'RO + Nóng lạnh', NULL, 6000000.00, 7000000.00, 9, 'CON_HANG'),
(473, 182, 'SUN-10LOI-PRO', 'Sunhouse 10 lõi Pro', 'Trắng', '10 lõi', 'RO', NULL, 6200000.00, 7200000.00, 8, 'CON_HANG'),
(474, 183, 'MUTOSI-8LOI-STD', 'Mutosi 8 lõi', 'Trắng', '8 lõi', 'RO', NULL, 4800000.00, 5800000.00, 12, 'CON_HANG'),
(475, 183, 'MUTOSI-8LOI-DISPLAY', 'Mutosi 8 lõi Display', 'Trắng', '8 lõi', 'RO + TDS', NULL, 5100000.00, 6100000.00, 10, 'CON_HANG'),
(476, 183, 'MUTOSI-8LOI-HOTCOLD', 'Mutosi 8 lõi Nóng lạnh', 'Trắng', '8 lõi', 'RO + Nóng lạnh', NULL, 5300000.00, 6300000.00, 9, 'CON_HANG'),
(477, 183, 'MUTOSI-8LOI-PRO', 'Mutosi 8 lõi Pro', 'Trắng', '8 lõi', 'RO', NULL, 5500000.00, 6500000.00, 8, 'CON_HANG'),
(478, 184, 'IP11-64-99', 'iPhone 11 99%', 'Đen', '64GB', '99%', NULL, 6500000.00, 8000000.00, 5, 'CON_HANG'),
(479, 184, 'IP11-64-97', 'iPhone 11 97%', 'Đen', '64GB', '97%', NULL, 6200000.00, 7800000.00, 4, 'CON_HANG'),
(480, 184, 'IP11-64-95', 'iPhone 11 95%', 'Đen', '64GB', '95%', NULL, 5900000.00, 7500000.00, 3, 'CON_HANG'),
(481, 184, 'IP11-64-NEWLIKE', 'iPhone 11 Like New', 'Trắng', '64GB', '99%', NULL, 6700000.00, 8200000.00, 4, 'CON_HANG'),
(482, 185, 'DELL-7480-I5', 'Dell 7480 i5', 'Đen', '256GB', 'i5 - 95%', NULL, 5200000.00, 6500000.00, 6, 'CON_HANG'),
(483, 185, 'DELL-7480-I7', 'Dell 7480 i7', 'Đen', '256GB', 'i7 - 97%', NULL, 5800000.00, 7000000.00, 5, 'CON_HANG'),
(484, 185, 'DELL-7480-8GB', 'Dell 7480 8GB RAM', 'Đen', '256GB', '8GB - 95%', NULL, 5400000.00, 6700000.00, 4, 'CON_HANG'),
(485, 185, 'DELL-7480-16GB', 'Dell 7480 16GB RAM', 'Đen', '512GB', '16GB - 97%', NULL, 6200000.00, 7500000.00, 3, 'CON_HANG'),
(486, 186, 'S21U-128-99', 'S21 Ultra 99%', 'Đen', '128GB', '99%', NULL, 8900000.00, 11000000.00, 5, 'CON_HANG'),
(487, 186, 'S21U-128-97', 'S21 Ultra 97%', 'Đen', '128GB', '97%', NULL, 8500000.00, 10500000.00, 4, 'CON_HANG'),
(488, 186, 'S21U-128-95', 'S21 Ultra 95%', 'Đen', '128GB', '95%', NULL, 8200000.00, 10000000.00, 3, 'CON_HANG'),
(489, 186, 'S21U-256-99', 'S21 Ultra 256GB', 'Đen', '256GB', '99%', NULL, 9500000.00, 11500000.00, 4, 'CON_HANG'),
(490, 187, 'IPAD9-64-99', 'iPad Gen 9 99%', 'Bạc', '64GB', '99%', NULL, 5800000.00, 7500000.00, 5, 'CON_HANG'),
(491, 187, 'IPAD9-64-97', 'iPad Gen 9 97%', 'Bạc', '64GB', '97%', NULL, 5500000.00, 7200000.00, 4, 'CON_HANG'),
(492, 187, 'IPAD9-64-95', 'iPad Gen 9 95%', 'Bạc', '64GB', '95%', NULL, 5200000.00, 7000000.00, 3, 'CON_HANG'),
(493, 187, 'IPAD9-256-99', 'iPad Gen 9 256GB', 'Bạc', '256GB', '99%', NULL, 6500000.00, 8200000.00, 4, 'CON_HANG'),
(533, 188, 'LG-9KG-HP-STD', 'LG 9kg Standard', 'Trắng', '9kg', 'Heat Pump', NULL, 17500000.00, 19000000.00, 10, 'CON_HANG'),
(534, 188, 'LG-9KG-HP-AI', 'LG 9kg AI', 'Trắng', '9kg', 'Heat Pump + AI', NULL, 18000000.00, 19500000.00, 8, 'CON_HANG'),
(535, 188, 'LG-9KG-HP-BLACK', 'LG 9kg Black', 'Đen', '9kg', 'Heat Pump', NULL, 17800000.00, 19300000.00, 7, 'CON_HANG'),
(536, 188, 'LG-9KG-HP-INVERTER', 'LG 9kg Inverter', 'Trắng', '9kg', 'Heat Pump + Inverter', NULL, 18200000.00, 19700000.00, 6, 'CON_HANG'),
(537, 188, 'LG-9KG-HP-PRO', 'LG 9kg Pro', 'Đen', '9kg', 'Heat Pump + AI', NULL, 18500000.00, 20000000.00, 5, 'CON_HANG'),
(538, 190, 'ELX-85KG-STD', 'Electrolux 8.5kg Standard', 'Trắng', '8.5kg', 'Thông hơi', NULL, 8500000.00, 10000000.00, 10, 'CON_HANG'),
(539, 190, 'ELX-85KG-COND', 'Electrolux 8.5kg Condenser', 'Trắng', '8.5kg', 'Condenser', NULL, 9000000.00, 10500000.00, 8, 'CON_HANG'),
(540, 190, 'ELX-85KG-BLACK', 'Electrolux 8.5kg Black', 'Đen', '8.5kg', 'Thông hơi', NULL, 8700000.00, 10200000.00, 7, 'CON_HANG'),
(541, 190, 'ELX-85KG-INVERTER', 'Electrolux 8.5kg Inverter', 'Trắng', '8.5kg', 'Condenser + Inverter', NULL, 9200000.00, 10700000.00, 6, 'CON_HANG'),
(542, 190, 'ELX-85KG-PRO', 'Electrolux 8.5kg Pro', 'Đen', '8.5kg', 'Condenser', NULL, 9500000.00, 11000000.00, 5, 'CON_HANG'),
(543, 191, 'CASPER-72KG-STD', 'Casper 7.2kg Standard', 'Trắng', '7.2kg', 'Thông hơi', NULL, 5500000.00, 6500000.00, 12, 'CON_HANG'),
(544, 191, 'CASPER-72KG-COND', 'Casper 7.2kg Condenser', 'Trắng', '7.2kg', 'Condenser', NULL, 5800000.00, 6800000.00, 10, 'CON_HANG'),
(545, 191, 'CASPER-72KG-BLACK', 'Casper 7.2kg Black', 'Đen', '7.2kg', 'Thông hơi', NULL, 5700000.00, 6700000.00, 9, 'CON_HANG'),
(546, 191, 'CASPER-72KG-INVERTER', 'Casper 7.2kg Inverter', 'Trắng', '7.2kg', 'Condenser + Inverter', NULL, 6000000.00, 7000000.00, 8, 'CON_HANG'),
(547, 191, 'CASPER-72KG-PRO', 'Casper 7.2kg Pro', 'Đen', '7.2kg', 'Condenser', NULL, 6200000.00, 7200000.00, 7, 'CON_HANG'),
(548, 189, 'SS-DRY-9KG-STD', 'Samsung 9kg Standard', 'Trắng', '9kg', 'Condenser', NULL, 12500000.00, 14000000.00, 10, 'CON_HANG'),
(549, 189, 'SS-DRY-9KG-AI', 'Samsung 9kg AI', 'Trắng', '9kg', 'AI Dry', NULL, 13000000.00, 14500000.00, 8, 'CON_HANG'),
(550, 189, 'SS-DRY-9KG-BLACK', 'Samsung 9kg Black', 'Đen', '9kg', 'Condenser', NULL, 12800000.00, 14300000.00, 7, 'CON_HANG'),
(551, 189, 'SS-DRY-9KG-INV', 'Samsung 9kg Inverter', 'Trắng', '9kg', 'AI + Inverter', NULL, 13200000.00, 14700000.00, 6, 'CON_HANG');
INSERT INTO `phien_ban_san_pham` (`id`, `san_pham_id`, `sku`, `ten_phien_ban`, `mau_sac`, `dung_luong`, `ram`, `cau_hinh`, `gia_ban`, `gia_goc`, `so_luong_ton`, `trang_thai`) VALUES
(552, 189, 'SS-DRY-9KG-PRO', 'Samsung 9kg Pro', 'Đen', '9kg', 'AI Dry', NULL, 13500000.00, 15000000.00, 5, 'CON_HANG');

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
(1, 1, 'IPhone', 'iphone', 'Apple', 'Smartphone', 999.00, 0, 'CON_BAN', 0, '2026-03-30 20:51:28', '2026-03-30 20:51:28'),
(2, 1, 'Điện thoại Samsung', 'sam-sung', 'Samsung', 'Điện thoại thông minh', NULL, 0, 'CON_BAN', 0, '2026-04-02 14:47:33', '2026-04-02 19:47:33'),
(7, 1, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'Apple', 'Siêu phẩm Apple với khung viền Titan cao cấp, chip A17 Pro mạnh mẽ.', 34990000.00, 5, 'CON_BAN', 1, '2026-04-04 09:59:16', '2026-04-04 09:59:16'),
(100, 1, 'iPhone 16 Pro Max', 'iphone-16-pro-max', 'Apple', 'Khung Titan, Chip A17 Pro.', 29990000.00, 5, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(101, 1, 'Samsung Galaxy S24 Ultra', 'samsung-s24-ultra', 'Samsung', 'AI Camera, Bút S-Pen.', 26990000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(102, 1, 'Xiaomi 14 Ultra', 'xiaomi-14-ultra', 'Xiaomi', 'Ống kính Leica thế hệ mới.', 22000000.00, 4.7, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(103, 1, 'Oppo Find N3', 'oppo-find-n3', 'Oppo', 'Màn hình gập đỉnh cao.', 41990000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(104, 1, 'Google Pixel 8 Pro', 'google-pixel-8-pro', 'Google', 'Trải nghiệm Android thuần khiết.', 19500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(105, 1, 'Sony Xperia 1 V', 'sony-xperia-1-v', 'Sony', 'Màn hình 4K HDR OLED.', 24000000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(106, 1, 'Asus ROG Phone 8', 'asus-rog-phone-8', 'Asus', 'Điện thoại chơi game mạnh nhất.', 23500000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(107, 1, 'Vivo X100 Pro', 'vivo-x100-pro', 'Vivo', 'Chip Dimensity 9300 cực mạnh.', 18000000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(108, 1, 'iPhone 13', 'iphone-13', 'Apple', 'Lựa chọn quốc dân giá tốt.', 13500000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(109, 1, 'Samsung Galaxy A55', 'samsung-a55', 'Samsung', 'Cận cao cấp, kháng nước IP67.', 9500000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(110, 2, 'iPad Pro M2 11 inch', 'ipad-pro-m2-11', 'Apple', 'Sức mạnh từ chip M2.', 20500000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(111, 2, 'Samsung Galaxy Tab S9 Ultra', 'tab-s9-ultra', 'Samsung', 'Màn hình Super AMOLED 14.6 inch.', 22000000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(112, 2, 'Xiaomi Pad 6', 'xiaomi-pad-6', 'Xiaomi', 'Máy tính bảng giải trí giá rẻ.', 7500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(113, 2, 'iPad Air 5', 'ipad-air-5', 'Apple', 'Chip M1, nhiều màu sắc.', 14500000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(114, 2, 'Lenovo Tab P11 Pro', 'lenovo-tab-p11-pro', 'Lenovo', 'Màn hình OLED sắc nét.', 9000000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(115, 3, 'MacBook Pro 14 M3', 'macbook-pro-14-m3', 'Apple', 'Màn hình Liquid Retina XDR.', 39990000.00, 5, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(116, 3, 'Dell XPS 15 9530', 'dell-xps-15-9530', 'Dell', 'Laptop Windows tốt nhất cho sáng tạo.', 45000000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(117, 3, 'ASUS TUF Gaming F15', 'asus-tuf-f15', 'Asus', 'Laptop gaming bền bỉ.', 18500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(118, 3, 'HP Pavilion 15', 'hp-pavilion-15', 'HP', 'Laptop văn phòng mỏng nhẹ.', 14000000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(119, 3, 'Lenovo Legion 5', 'lenovo-legion-5', 'Lenovo', 'Tản nhiệt cực tốt, hiệu năng cao.', 26000000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(120, 3, 'Acer Swift Go 14', 'acer-swift-go-14', 'Acer', 'Màn hình OLED giá tốt.', 16500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(121, 3, 'MSI Katana 15', 'msi-katana-15', 'MSI', 'Vũ khí cho game thủ.', 21000000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(122, 3, 'Gigabyte G5', 'gigabyte-g5', 'Gigabyte', 'Laptop gaming cấu hình cao giá rẻ.', 17500000.00, 4.1, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(123, 3, 'LG Gram 16', 'lg-gram-16', 'LG', 'Siêu nhẹ chỉ hơn 1kg.', 28000000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(124, 3, 'Surface Laptop 5', 'surface-laptop-5', 'Microsoft', 'Thiết kế cao cấp từ Microsoft.', 23000000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(125, 4, 'Dell UltraSharp U2723QE', 'dell-u2723qe', 'Dell', '4K USB-C Hub Monitor.', 15500000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(126, 4, 'LG 27UP850N-W', 'lg-27up850n', 'LG', 'Chuẩn màu đồ họa 4K.', 9500000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(127, 4, 'Samsung Odyssey G5', 'samsung-odyssey-g5', 'Samsung', 'Màn hình cong 144Hz.', 6500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(128, 4, 'ASUS ProArt PA248QV', 'asus-proart-pa248qv', 'Asus', 'Chuyên cho thiết kế cơ bản.', 5200000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(129, 4, 'Gigabyte G24F 2', 'gigabyte-g24f2', 'Gigabyte', 'Lựa chọn gaming quốc dân.', 3500000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(130, 4, 'MSI Optix G271', 'msi-optix-g271', 'MSI', 'Tấm nền IPS màu sắc đẹp.', 4800000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(131, 5, 'Apple Mac Studio', 'mac-studio', 'Apple', 'Nhỏ gọn nhưng cực mạnh.', 52000000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(132, 5, 'PC Gaming Sentinel', 'pc-gaming-sentinel', 'Custom', 'RTX 4070, Core i7-14700K.', 35000000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(133, 5, 'HP Pavilion Desktop', 'hp-pavilion-desktop', 'HP', 'Máy bộ ổn định cho văn phòng.', 12000000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(134, 5, 'Asus ROG Strix G16', 'asus-rog-strix-pc', 'Asus', 'PC Gaming đồng bộ cao cấp.', 42000000.00, 4.7, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(135, 6, 'Chuột Logitech G502 X Plus', 'logitech-g502-x', 'Logitech', 'Chuột gaming không dây tốt nhất.', 3200000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(136, 6, 'Bàn phím cơ Akko 3087', 'akko-3087', 'Akko', 'Thiết kế đẹp, gõ sướng.', 1200000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(137, 6, 'Tai nghe Sony WH-1000XM5', 'sony-wh-1000xm5', 'Sony', 'Chống ồn đỉnh cao.', 7500000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(138, 6, 'Loa Marshall Emberton II', 'marshall-emberton-2', 'Marshall', 'Âm thanh đặc trưng Marshall.', 3800000.00, 4.7, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(139, 6, 'Sạc dự phòng Anker 737', 'anker-737', 'Anker', 'Sạc nhanh 140W cho laptop.', 2500000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(140, 6, 'Webcam Logitech C922', 'logitech-c922', 'Logitech', 'Stream Full HD chuyên nghiệp.', 1900000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(141, 7, 'Sim FPT G99', 'sim-fpt-g99', 'FPT', '4GB/ngày, miễn phí gọi nội mạng.', 150000.00, 4.5, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(142, 7, 'Sim FPT G120', 'sim-fpt-g120', 'FPT', '6GB/ngày, data không giới hạn.', 250000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(143, 7, 'Sim FPT V60', 'sim-fpt-v60', 'FPT', 'Gói cước tiết kiệm cho sinh viên.', 90000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(144, 7, 'Sim FPT 12T', 'sim-fpt-12t', 'FPT', 'Trọn gói 1 năm không nạp tiền.', 1200000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(145, 8, 'Apple Watch Ultra 2', 'apple-watch-ultra-2', 'Apple', 'Bền bỉ, pin trâu nhất của Apple.', 21000000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(146, 8, 'Samsung Galaxy Watch 6 Classic', 'galaxy-watch-6-classic', 'Samsung', 'Vòng xoay vật lý độc đáo.', 7500000.00, 4.6, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(147, 8, 'Garmin Forerunner 255', 'garmin-forerunner-255', 'Garmin', 'Chuyên dụng cho chạy bộ.', 8500000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(148, 8, 'Xiaomi Watch S3', 'xiaomi-watch-s3', 'Xiaomi', 'Thiết kế thời trang, pin 15 ngày.', 3500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(149, 8, 'Huawei Watch GT 4', 'huawei-watch-gt4', 'Huawei', 'Pin cực khỏe, mặt tròn cổ điển.', 4500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(150, 8, 'Amazfit GTR 4', 'amazfit-gtr-4', 'Amazfit', 'GPS chính xác, giá cạnh tranh.', 4000000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(151, 9, 'Sony Bravia XR OLED A80L', 'sony-a80l', 'Sony', 'Âm thanh từ màn hình.', 35000000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(152, 9, 'Samsung Neo QLED QN90C', 'samsung-qn90c', 'Samsung', 'Độ sáng cực cao, chơi game mượt.', 28000000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(153, 9, 'LG OLED C3', 'lg-oled-c3', 'LG', 'Vua của tivi gaming.', 30000000.00, 4.8, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(154, 9, 'TCL Mini LED C845', 'tcl-c845', 'TCL', 'Giá rẻ, hiệu năng Mini LED.', 18000000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(155, 9, 'Xiaomi TV A Pro 65', 'xiaomi-tv-a-pro-65', 'Xiaomi', 'Tivi 4K giá rẻ nhất phân khúc.', 11000000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(156, 9, 'Casper 55 inch 4K', 'casper-55-4k', 'Casper', 'Bền bỉ, bảo hành tốt.', 7500000.00, 4.1, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(157, 10, 'Daikin Inverter 1 HP', 'daikin-inverter-1hp', 'Daikin', 'Làm lạnh nhanh, bền bỉ.', 10500000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(158, 10, 'Panasonic Inverter 1.5 HP', 'panasonic-1-5hp', 'Panasonic', 'Công nghệ lọc khí Nanoe-G.', 13000000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(159, 10, 'LG Dual Inverter 1 HP', 'lg-dual-inverter-1hp', 'LG', 'Tiết kiệm điện vượt trội.', 8500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(160, 10, 'Samsung Wind-Free 1 HP', 'samsung-wind-free', 'Samsung', 'Làm lạnh không gió buốt.', 9800000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(161, 10, 'Casper Inverter 1 HP', 'casper-1hp', 'Casper', 'Giá rẻ, làm lạnh sâu.', 6000000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(162, 11, 'Roborock S8 Pro Ultra', 'roborock-s8-pro', 'Roborock', 'Tự động giặt giẻ, sấy khô.', 25000000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(163, 11, 'Dreame L20 Ultra', 'dreame-l20-ultra', 'Dreame', 'Lực hút siêu mạnh 7000Pa.', 22000000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(164, 11, 'Ecovacs Deebot T20 Omni', 'ecovacs-t20-omni', 'Ecovacs', 'Giặt giẻ nước nóng.', 16000000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(165, 11, 'Xiaomi Mi Robot Vacuum 3', 'xiaomi-vacuum-3', 'Xiaomi', 'Giá rẻ, bản đồ laser.', 5500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(166, 12, 'Kangaroo KG50F62', 'kangaroo-kg50f62', 'Kangaroo', 'Phù hợp phòng khách lớn.', 3500000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(167, 12, 'Sunhouse SHD7727', 'sunhouse-shd7727', 'Sunhouse', 'Làm mát diện rộng.', 2800000.00, 4.1, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(168, 12, 'Boss S102', 'boss-s102', 'Boss', 'Máy bền, chạy êm.', 4200000.00, 4.5, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(169, 12, 'Climax M12', 'climax-m12', 'Climax', 'Thiết kế nhỏ gọn.', 2100000.00, 4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(170, 13, 'LG AI DD 10kg', 'lg-ai-dd-10kg', 'LG', 'Giặt hơi nước Steam.', 10500000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(171, 13, 'Samsung EcoBubble 9kg', 'samsung-ecobubble-9kg', 'Samsung', 'Giặt bong bóng siêu mịn.', 9000000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(172, 13, 'Toshiba Inverter 8.5kg', 'toshiba-8-5kg', 'Toshiba', 'Động cơ truyền động trực tiếp.', 8200000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(173, 13, 'Electrolux UltimateCare 500', 'electrolux-500', 'Electrolux', 'Chống nhăn quần áo.', 12000000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(174, 13, 'Panasonic 9kg Cửa trên', 'panasonic-9kg-top', 'Panasonic', 'Giặt sạch vết bẩn cứng đầu.', 6500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(175, 14, 'Samsung Bespoke 400L', 'samsung-bespoke-400l', 'Samsung', 'Tùy chỉnh màu sắc mặt gương.', 18000000.00, 4.8, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(176, 14, 'Panasonic Multi-Door 550L', 'panasonic-550l', 'Panasonic', 'Ngăn đông mềm Prime Fresh.', 32000000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(177, 14, 'LG Side by Side 635L', 'lg-sbs-635l', 'LG', 'Lấy nước ngoài diệt khuẩn UV.', 21000000.00, 4.7, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(178, 14, 'Toshiba 180L', 'toshiba-180l', 'Toshiba', 'Tủ lạnh nhỏ cho sinh viên.', 4500000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(179, 14, 'Sharp Inverter 322L', 'sharp-322l', 'Sharp', 'Khử mùi phân tử bạc.', 8500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(180, 15, 'Kangaroo Hydrogen KG100HK', 'kangaroo-kg100hk', 'Kangaroo', 'Nóng lạnh trực tiếp.', 8500000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(181, 15, 'Karofi Optimus Plus', 'karofi-optimus', 'Karofi', 'Màn hình hiển thị độ sạch nước.', 7200000.00, 4.6, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(182, 15, 'Sunhouse RO 10 lõi', 'sunhouse-ro-10', 'Sunhouse', 'Lọc sạch kim loại nặng.', 5500000.00, 4.4, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(183, 15, 'Mutosi 8 lõi', 'mutosi-8-loi', 'Mutosi', 'Thiết kế siêu mỏng.', 4800000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(184, 16, 'iPhone 11 64GB Cũ', 'iphone-11-cu', 'Apple', 'Ngoại hình 99%, pin tốt.', 6500000.00, 4.5, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(185, 16, 'Laptop Dell Latitude 7480 Cũ', 'dell-7480-cu', 'Dell', 'Bền bỉ cho văn phòng.', 5200000.00, 4.3, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(186, 16, 'Samsung S21 Ultra Cũ', 's21-ultra-cu', 'Samsung', 'Máy trần, xước nhẹ.', 8900000.00, 4.4, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(187, 16, 'iPad Gen 9 Cũ', 'ipad-gen-9-cu', 'Apple', 'Máy đẹp như mới.', 5800000.00, 4.6, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(188, 26, 'Máy sấy LG 9kg Bơm nhiệt', 'lg-9kg-heatpump', 'LG', 'Công nghệ Dual Inverter Heat Pump.', 17500000.00, 4.9, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(189, 26, 'Máy sấy Samsung 9kg', 'samsung-9kg-dryer', 'Samsung', 'Sấy khô thông minh AI.', 12500000.00, 4.7, 'CON_BAN', 1, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(190, 26, 'Máy sấy Electrolux 8.5kg', 'electrolux-8-5kg-dryer', 'Electrolux', 'Sấy thông hơi tiết kiệm.', 8500000.00, 4.5, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35'),
(191, 26, 'Máy sấy Casper 7.2kg', 'casper-7-2kg-dryer', 'Casper', 'Nhỏ gọn cho gia đình ít người.', 5500000.00, 4.2, 'CON_BAN', 0, '2026-04-06 17:24:35', '2026-04-06 17:24:35');

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
(1, 1),
(7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) NOT NULL,
  `nguoi_duyet_id` int(11) DEFAULT NULL COMMENT 'Admin duyệt thanh toán',
  `phuong_thuc` enum('COD','CHUYEN_KHOAN','QR','TRA_GOP','VI_DIEN_TU') DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `anh_bien_lai` varchar(500) DEFAULT NULL COMMENT 'URL ảnh biên lai chuyển khoản',
  `trang_thai_duyet` enum('CHO_DUYET','THANH_CONG','THAT_BAI','HOAN_TIEN') DEFAULT 'CHO_DUYET',
  `ghi_chu_duyet` text DEFAULT NULL COMMENT 'Admin ghi chú khi duyệt',
  `ngay_thanh_toan` datetime DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD KEY `idx_forget_token` (`forget_token`);

--
-- Indexes for table `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sku` (`sku`),
  ADD KEY `san_pham_id` (`san_pham_id`);

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
  ADD KEY `nguoi_duyet_id` (`nguoi_duyet_id`);

--
-- Indexes for table `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `chi_tiet_don`
--
ALTER TABLE `chi_tiet_don`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_gio`
--
ALTER TABLE `chi_tiet_gio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `dia_chi`
--
ALTER TABLE `dia_chi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `phien_ban_san_pham`
--
ALTER TABLE `phien_ban_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;

--
-- AUTO_INCREMENT for table `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thong_so_ky_thuat`
--
ALTER TABLE `thong_so_ky_thuat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD CONSTRAINT `yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

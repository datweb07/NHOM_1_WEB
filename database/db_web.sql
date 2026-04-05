-- =============================================
-- DATABASE: db_web (E-Commerce Website - FPT Shop)
-- Version: 2.0
-- Updated: 2026-02-24
-- Charset: utf8mb4 (hỗ trợ tiếng Việt + emoji)
-- =============================================

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

-- =========================================================
-- 1. BẢNG ĐỘC LẬP (TẠO TRƯỚC — không phụ thuộc bảng khác)
-- =========================================================

-- ---------------------------------------------------------
-- Bảng: NGUOI_DUNG (Người dùng / Tài khoản)
-- ---------------------------------------------------------
CREATE TABLE `nguoi_dung` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mat_khau` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lưu hash bằng password_hash()',
    `ho_ten` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `sdt` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `avatar_url` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện',
    `ngay_sinh` DATE DEFAULT NULL,
    `gioi_tinh` ENUM('NAM', 'NU', 'KHAC') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `loai_tai_khoan` ENUM('ADMIN', 'MEMBER') COLLATE utf8mb4_unicode_ci DEFAULT 'MEMBER',
    `trang_thai` ENUM('ACTIVE', 'BLOCKED', 'UNVERIFIED') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
    `verification_token` VARCHAR(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `forget_token` VARCHAR(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token đặt lại mật khẩu',
    `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_forget_token` (`forget_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: DANH_MUC (Danh mục sản phẩm — phân cấp)
-- Ví dụ: Điện thoại → Apple → iPhone 16 Series
-- ---------------------------------------------------------
CREATE TABLE `danh_muc` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ten` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL thân thiện: dien-thoai, laptop',
    `icon_url` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon hiển thị trên menu',
    `danh_muc_cha_id` INT DEFAULT NULL,
    `thu_tu` INT DEFAULT 0 COMMENT 'Thứ tự hiển thị trên menu',
    `trang_thai` TINYINT(1) DEFAULT 1 COMMENT '1=hiện, 0=ẩn',
    `is_noi_bat` TINYINT(1) DEFAULT 0 COMMENT '1 = Hiện ở danh mục nổi bật, 0 = Không',
    `is_goi_y` TINYINT(1) DEFAULT 0 COMMENT '1 = Hiện ở gợi ý cho bạn, 0 = Không',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_dm_slug` (`slug`),
    KEY `danh_muc_cha_id` (`danh_muc_cha_id`),
    CONSTRAINT `danh_muc_ibfk_1` FOREIGN KEY (`danh_muc_cha_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: KHUYEN_MAI (Chương trình khuyến mãi)
-- ---------------------------------------------------------
CREATE TABLE `khuyen_mai` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ten_chuong_trinh` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `loai_giam` ENUM('PHAN_TRAM', 'SO_TIEN') COLLATE utf8mb4_unicode_ci DEFAULT 'PHAN_TRAM',
    `gia_tri_giam` DECIMAL(15,2) DEFAULT NULL COMMENT '10 = 10% hoặc 500000 = 500k VND',
    `giam_toi_da` DECIMAL(15,2) DEFAULT NULL COMMENT 'Giảm tối đa (áp dụng nếu loại %)',
    `ngay_bat_dau` DATETIME DEFAULT NULL,
    `ngay_ket_thuc` DATETIME DEFAULT NULL,
    `trang_thai` ENUM('HOAT_DONG', 'DA_HET_HAN', 'TAM_DUNG') COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: MA_GIAM_GIA (Voucher / Coupon code)
-- ---------------------------------------------------------
CREATE TABLE `ma_giam_gia` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ma_code` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'VD: FPTSHOP50K, SALE10',
    `mo_ta` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `loai_giam` ENUM('PHAN_TRAM', 'SO_TIEN') COLLATE utf8mb4_unicode_ci NOT NULL,
    `gia_tri_giam` DECIMAL(15,2) NOT NULL,
    `giam_toi_da` DECIMAL(15,2) DEFAULT NULL COMMENT 'Áp dụng nếu loại PHAN_TRAM',
    `don_toi_thieu` DECIMAL(15,2) DEFAULT 0 COMMENT 'Giá trị đơn hàng tối thiểu',
    `so_luot_da_dung` INT DEFAULT 0,
    `gioi_han_su_dung` INT DEFAULT NULL COMMENT 'NULL = không giới hạn',
    `ngay_bat_dau` DATETIME NOT NULL,
    `ngay_ket_thuc` DATETIME NOT NULL,
    `trang_thai` ENUM('HOAT_DONG', 'DA_HET_HAN', 'HET_LUOT') COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ma_code` (`ma_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `vi_tri` enum('HOME_HERO','HOME_SIDE','FLOATING_BOTTOM_LEFT','POPUP','CATEGORY_TOP') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Vị trí hiển thị trên web',
  `thu_tu` int DEFAULT '0' COMMENT 'Sắp xếp thứ tự nếu có nhiều banner cùng vị trí',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT '1' COMMENT '1 = Hiển thị, 0 = Ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. BẢNG CẤP 1 (Phụ thuộc NGUOI_DUNG / DANH_MUC)
-- =========================================================

-- ---------------------------------------------------------
-- Bảng: DIA_CHI (Địa chỉ giao hàng)
-- ---------------------------------------------------------
CREATE TABLE `dia_chi` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nguoi_dung_id` INT NOT NULL,
    `ten_nguoi_nhan` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `sdt_nhan` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `so_nha_duong` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phuong_xa` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `quan_huyen` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tinh_thanh` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `mac_dinh` TINYINT(1) DEFAULT 0 COMMENT '1 = địa chỉ mặc định',
    PRIMARY KEY (`id`),
    KEY `nguoi_dung_id` (`nguoi_dung_id`),
    CONSTRAINT `dia_chi_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: LICH_SU_TIM_KIEM
-- ---------------------------------------------------------
CREATE TABLE `lich_su_tim_kiem` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nguoi_dung_id` INT NOT NULL,
    `tu_khoa` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `thoi_gian_tim` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `nguoi_dung_id` (`nguoi_dung_id`),
    CONSTRAINT `lich_su_tim_kiem_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: SAN_PHAM (Sản phẩm)
-- ---------------------------------------------------------
CREATE TABLE `san_pham` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `danh_muc_id` INT DEFAULT NULL,
    `ten_san_pham` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL: iphone-16-pro-max',
    `hang_san_xuat` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Apple, Samsung, Xiaomi...',
    `mo_ta` TEXT COLLATE utf8mb4_unicode_ci,
    `gia_hien_thi` DECIMAL(15,2) DEFAULT NULL COMMENT 'Giá "từ" hiển thị (giá thấp nhất phiên bản)',
    `diem_danh_gia` FLOAT DEFAULT 0,
    `trang_thai` ENUM('CON_BAN', 'NGUNG_BAN', 'SAP_RA_MAT', 'HET_HANG') COLLATE utf8mb4_unicode_ci DEFAULT 'CON_BAN',
    `noi_bat` TINYINT(1) DEFAULT 0 COMMENT '1 = hiện trên banner/trang chủ',
    `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_sp_slug` (`slug`),
    KEY `danh_muc_id` (`danh_muc_id`),
    KEY `idx_hang_sx` (`hang_san_xuat`),
    KEY `idx_trang_thai` (`trang_thai`),
    CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: GIO_HANG (Giỏ hàng)
-- ---------------------------------------------------------
CREATE TABLE `gio_hang` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nguoi_dung_id` INT DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
    `session_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Session cho khách vãng lai',
    `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `nguoi_dung_id` (`nguoi_dung_id`),
    CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3. BẢNG CẤP 2 (Phụ thuộc SAN_PHAM / NGUOI_DUNG)
-- =========================================================

-- ---------------------------------------------------------
-- Bảng: PHIEN_BAN_SAN_PHAM (Biến thể sản phẩm)
-- VD: iPhone 16 Pro Max - 256GB - Đen Titan
-- ---------------------------------------------------------
CREATE TABLE `phien_ban_san_pham` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `san_pham_id` INT NOT NULL,
    `sku` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã kho duy nhất',
    `ten_phien_ban` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'iPhone 16 Pro Max 256GB',
    `mau_sac` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đen Titan, Trắng, Xanh...',
    `dung_luong` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '128GB, 256GB, 512GB, 1TB',
    `ram` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '8GB, 12GB, 16GB',
    `cau_hinh` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả cấu hình khác (nếu có)',
    `gia_ban` DECIMAL(15,2) DEFAULT NULL COMMENT 'Giá bán hiện tại',
    `gia_goc` DECIMAL(15,2) DEFAULT NULL COMMENT 'Giá gốc (giá gạch ngang)',
    `so_luong_ton` INT DEFAULT 0,
    `trang_thai` ENUM('CON_HANG', 'HET_HANG', 'NGUNG_BAN') COLLATE utf8mb4_unicode_ci DEFAULT 'CON_HANG',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_sku` (`sku`),
    KEY `san_pham_id` (`san_pham_id`),
    CONSTRAINT `phien_ban_sp_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_gia_ban` CHECK (`gia_ban` > 0),
    CONSTRAINT `chk_gia_goc` CHECK (`gia_goc` IS NULL OR `gia_goc` >= `gia_ban`),
    CONSTRAINT `chk_ton_kho` CHECK (`so_luong_ton` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: HINH_ANH_SAN_PHAM (Ảnh sản phẩm — 1 SP nhiều ảnh)
-- *** BẢNG MỚI — giải quyết vấn đề thiếu ảnh ***
-- ---------------------------------------------------------
CREATE TABLE `hinh_anh_san_pham` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `san_pham_id` INT NOT NULL,
    `phien_ban_id` INT DEFAULT NULL COMMENT 'NULL = ảnh chung, có giá trị = ảnh theo phiên bản/màu',
    `url_anh` VARCHAR(500) COLLATE utf8mb4_unicode_ci NOT NULL,
    `alt_text` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả ảnh cho SEO/accessibility',
    `la_anh_chinh` TINYINT(1) DEFAULT 0 COMMENT '1 = ảnh đại diện hiển thị ở listing',
    `thu_tu` INT DEFAULT 0 COMMENT 'Thứ tự trong gallery',
    PRIMARY KEY (`id`),
    KEY `san_pham_id` (`san_pham_id`),
    KEY `phien_ban_id` (`phien_ban_id`),
    CONSTRAINT `hinh_anh_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `hinh_anh_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: THONG_SO_KY_THUAT (Key-Value linh hoạt)
-- VD: Ram = 8GB, Chip = A18 Pro, Pin = 4685mAh
-- ---------------------------------------------------------
CREATE TABLE `thong_so_ky_thuat` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `san_pham_id` INT NOT NULL,
    `ten_thong_so` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ram, Chip, Pin, Màn hình...',
    `gia_tri` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '8GB, A18 Pro, 5000mAh...',
    `thu_tu` INT DEFAULT 0 COMMENT 'Thứ tự hiển thị',
    PRIMARY KEY (`id`),
    KEY `san_pham_id` (`san_pham_id`),
    CONSTRAINT `thong_so_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: DANH_GIA (Đánh giá / Review sản phẩm)
-- ---------------------------------------------------------
CREATE TABLE `danh_gia` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nguoi_dung_id` INT NOT NULL,
    `san_pham_id` INT NOT NULL,
    `so_sao` INT DEFAULT NULL,
    `noi_dung` TEXT COLLATE utf8mb4_unicode_ci,
    `ngay_viet` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `nguoi_dung_id` (`nguoi_dung_id`),
    KEY `san_pham_id` (`san_pham_id`),
    CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
    CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_so_sao` CHECK (`so_sao` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: YEU_THICH (Wishlist — N:N giữa Người dùng & Sản phẩm)
-- ---------------------------------------------------------
CREATE TABLE `yeu_thich` (
    `nguoi_dung_id` INT NOT NULL,
    `san_pham_id` INT NOT NULL,
    `ngay_them` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`nguoi_dung_id`, `san_pham_id`),
    KEY `san_pham_id` (`san_pham_id`),
    CONSTRAINT `yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
    CONSTRAINT `yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: SAN_PHAM_KHUYEN_MAI (N:N giữa Sản phẩm & Khuyến mãi)
-- ---------------------------------------------------------
CREATE TABLE `san_pham_khuyen_mai` (
    `san_pham_id` INT NOT NULL,
    `khuyen_mai_id` INT NOT NULL,
    PRIMARY KEY (`san_pham_id`, `khuyen_mai_id`),
    KEY `khuyen_mai_id` (`khuyen_mai_id`),
    CONSTRAINT `sp_km_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sp_km_ibfk_2` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. BẢNG LIÊN QUAN ĐẾN GIAO DỊCH (Phụ thuộc nhiều bảng)
-- =========================================================

-- ---------------------------------------------------------
-- Bảng: CHI_TIET_GIO (Chi tiết giỏ hàng)
-- ---------------------------------------------------------
CREATE TABLE `chi_tiet_gio` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `gio_hang_id` INT NOT NULL,
    `phien_ban_id` INT NOT NULL,
    `so_luong` INT DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_gio_phienban` (`gio_hang_id`, `phien_ban_id`) COMMENT 'Tránh trùng SP trong giỏ',
    KEY `phien_ban_id` (`phien_ban_id`),
    CONSTRAINT `chi_tiet_gio_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chi_tiet_gio_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_sl_gio` CHECK (`so_luong` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: DON_HANG (Đơn hàng)
-- ---------------------------------------------------------
CREATE TABLE `don_hang` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ma_don_hang` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hiển thị: DH20260224001',
    `nguoi_dung_id` INT DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
    `dia_chi_id` INT DEFAULT NULL COMMENT 'NULL nếu guest (dùng thong_tin_guest)',
    `ma_giam_gia_id` INT DEFAULT NULL COMMENT 'Voucher áp dụng',
    `trang_thai` ENUM('CHO_DUYET', 'DA_XAC_NHAN', 'DANG_GIAO', 'DA_GIAO', 'HOAN_THANH', 'DA_HUY', 'TRA_HANG')
        COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
    `tong_tien` DECIMAL(15,2) DEFAULT NULL COMMENT 'Tổng tiền sản phẩm',
    `phi_van_chuyen` DECIMAL(15,2) DEFAULT 0,
    `tien_giam_gia` DECIMAL(15,2) DEFAULT 0 COMMENT 'Số tiền được giảm',
    `tong_thanh_toan` DECIMAL(15,2) DEFAULT NULL COMMENT 'tong_tien + phi_van_chuyen - tien_giam_gia',
    `thong_tin_guest` TEXT COLLATE utf8mb4_unicode_ci COMMENT 'JSON: {ten, sdt, dia_chi} cho khách vãng lai',
    `ghi_chu` TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của khách hàng',
    `ngay_giao_du_kien` DATETIME DEFAULT NULL,
    `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ma_don` (`ma_don_hang`),
    KEY `nguoi_dung_id` (`nguoi_dung_id`),
    KEY `dia_chi_id` (`dia_chi_id`),
    KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
    KEY `idx_trang_thai` (`trang_thai`),
    KEY `idx_ngay_tao` (`ngay_tao`),
    CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
    CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`dia_chi_id`) REFERENCES `dia_chi` (`id`) ON DELETE SET NULL,
    CONSTRAINT `don_hang_ibfk_3` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: CHI_TIET_DON (Chi tiết đơn hàng)
-- ---------------------------------------------------------
CREATE TABLE `chi_tiet_don` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `don_hang_id` INT NOT NULL,
    `phien_ban_id` INT NOT NULL,
    `so_luong` INT DEFAULT 1,
    `gia_tai_thoi_diem_mua` DECIMAL(15,2) DEFAULT NULL COMMENT 'Snapshot giá lúc đặt hàng',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_don_phienban` (`don_hang_id`, `phien_ban_id`) COMMENT 'Tránh trùng SP trong đơn',
    KEY `phien_ban_id` (`phien_ban_id`),
    CONSTRAINT `chi_tiet_don_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chi_tiet_don_ibfk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_sl_don` CHECK (`so_luong` >= 1),
    CONSTRAINT `chk_gia_mua` CHECK (`gia_tai_thoi_diem_mua` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Bảng: THANH_TOAN (Thông tin thanh toán)
-- ---------------------------------------------------------
CREATE TABLE `thanh_toan` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `don_hang_id` INT NOT NULL,
    `nguoi_duyet_id` INT DEFAULT NULL COMMENT 'Admin duyệt thanh toán',
    `phuong_thuc` ENUM('COD', 'CHUYEN_KHOAN', 'QR', 'TRA_GOP', 'VI_DIEN_TU')
        COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `so_tien` DECIMAL(15,2) DEFAULT NULL,
    `anh_bien_lai` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL ảnh biên lai chuyển khoản',
    `trang_thai_duyet` ENUM('CHO_DUYET', 'THANH_CONG', 'THAT_BAI', 'HOAN_TIEN')
        COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
    `ghi_chu_duyet` TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Admin ghi chú khi duyệt',
    `ngay_thanh_toan` DATETIME DEFAULT NULL,
    `ngay_duyet` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `don_hang_id` (`don_hang_id`),
    KEY `nguoi_duyet_id` (`nguoi_duyet_id`),
    CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
    CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
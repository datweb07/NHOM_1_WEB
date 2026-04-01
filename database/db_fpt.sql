SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

CREATE TABLE `vai_tro` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `danh_sach_quyen` json DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ten_vai_tro` (`ten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nguoi_dung` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_version` tinyint(1) DEFAULT '1',
  `ho_ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('NAM','NU','KHAC') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_tai_khoan` enum('ADMIN','MEMBER') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MEMBER',
  `trang_thai` enum('ACTIVE','BLOCKED','UNVERIFIED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `verification_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forget_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forget_token_het_han` datetime DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_forget_token` (`forget_token`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nguoi_dung_vai_tro` (
  `nguoi_dung_id` int NOT NULL,
  `vai_tro_id` int NOT NULL,
  `ngay_gan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nguoi_dung_id`, `vai_tro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `danh_muc` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `danh_muc_cha_id` int DEFAULT NULL,
  `thu_tu` int DEFAULT '0',
  `trang_thai` tinyint(1) DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_dm_slug` (`slug`),
  KEY `danh_muc_cha_id` (`danh_muc_cha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tinh_thanh` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma` varchar(10) NOT NULL,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ma_tinh` (`ma`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quan_huyen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tinh_thanh_id` int NOT NULL,
  `ma` varchar(10) NOT NULL,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ma_quan` (`ma`),
  KEY `tinh_thanh_id` (`tinh_thanh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `phuong_xa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quan_huyen_id` int NOT NULL,
  `ma` varchar(10) NOT NULL,
  `ten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ma_phuong` (`ma`),
  KEY `quan_huyen_id` (`quan_huyen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dia_chi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int NOT NULL,
  `ten_nguoi_nhan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdt_nhan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_nha_duong` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phuong_xa_id` int DEFAULT NULL,
  `quan_huyen_id` int DEFAULT NULL,
  `tinh_thanh_id` int DEFAULT NULL,
  `phuong_xa_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quan_huyen_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tinh_thanh_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_dinh` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `tinh_thanh_id` (`tinh_thanh_id`),
  KEY `quan_huyen_id` (`quan_huyen_id`),
  KEY `phuong_xa_id` (`phuong_xa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `san_pham` (
  `id` int NOT NULL AUTO_INCREMENT,
  `danh_muc_id` int DEFAULT NULL,
  `ten_san_pham` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hang_san_xuat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mo_ta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gia_hien_thi` decimal(15,2) DEFAULT NULL,
  `so_danh_gia` int DEFAULT '0',
  `diem_danh_gia` decimal(3,2) DEFAULT '0.00',
  `trang_thai` enum('CON_BAN','NGUNG_BAN','SAP_RA_MAT','HET_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CON_BAN',
  `noi_bat` tinyint(1) DEFAULT '0',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sp_slug` (`slug`),
  KEY `danh_muc_id` (`danh_muc_id`),
  KEY `idx_hang_sx` (`hang_san_xuat`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `phien_ban_san_pham` (
  `id` int NOT NULL AUTO_INCREMENT,
  `san_pham_id` int NOT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_phien_ban` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mau_sac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dung_luong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cau_hinh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia_ban` decimal(15,2) DEFAULT NULL,
  `gia_goc` decimal(15,2) DEFAULT NULL,
  `so_luong_ton` int DEFAULT '0',
  `trang_thai` enum('CON_HANG','HET_HANG','NGUNG_BAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CON_HANG',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku` (`sku`),
  KEY `san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bien_dong_kho` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phien_ban_id` int NOT NULL,
  `loai` enum('NHAP','XUAT_BAN','XUAT_HUY','XUAT_TRA_HANG','DIEU_CHINH') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_luong` int NOT NULL,
  `so_luong_truoc` int NOT NULL,
  `so_luong_sau` int NOT NULL,
  `don_hang_id` int DEFAULT NULL,
  `nguoi_thuc_hien` int DEFAULT NULL,
  `ghi_chu` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thoi_gian` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `phien_ban_id` (`phien_ban_id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `idx_thoi_gian` (`thoi_gian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hinh_anh_san_pham` (
  `id` int NOT NULL AUTO_INCREMENT,
  `san_pham_id` int NOT NULL,
  `phien_ban_id` int DEFAULT NULL,
  `url_anh` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `la_anh_chinh` tinyint(1) DEFAULT '0',
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `phien_ban_id` (`phien_ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `thong_so_ky_thuat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `san_pham_id` int NOT NULL,
  `ten_thong_so` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia_tri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `khuyen_mai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_chuong_trinh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'PHAN_TRAM',
  `gia_tri_giam` decimal(15,2) DEFAULT NULL,
  `giam_toi_da` decimal(15,2) DEFAULT NULL,
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `co_the_cong_them_voucher` tinyint(1) DEFAULT '0',
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','TAM_DUNG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `san_pham_khuyen_mai` (
  `san_pham_id` int NOT NULL,
  `khuyen_mai_id` int NOT NULL,
  PRIMARY KEY (`san_pham_id`,`khuyen_mai_id`),
  KEY `khuyen_mai_id` (`khuyen_mai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ma_giam_gia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_giam` enum('PHAN_TRAM','SO_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri_giam` decimal(15,2) NOT NULL,
  `giam_toi_da` decimal(15,2) DEFAULT NULL,
  `don_toi_thieu` decimal(15,2) DEFAULT '0.00',
  `so_luot_da_dung` int DEFAULT '0',
  `gioi_han_su_dung` int DEFAULT NULL,
  `gioi_han_1_user` int DEFAULT '1',
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `trang_thai` enum('HOAT_DONG','DA_HET_HAN','HET_LUOT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HOAT_DONG',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ma_code` (`ma_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lich_su_ma_giam_gia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_giam_gia_id` int NOT NULL,
  `nguoi_dung_id` int DEFAULT NULL,
  `don_hang_id` int NOT NULL,
  `tien_giam_thuc_te` decimal(15,2) NOT NULL,
  `ngay_dung` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `don_hang_id` (`don_hang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gio_hang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int DEFAULT NULL,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chi_tiet_gio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gio_hang_id` int NOT NULL,
  `phien_ban_id` int NOT NULL,
  `so_luong` int DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gio_phienban` (`gio_hang_id`,`phien_ban_id`),
  KEY `phien_ban_id` (`phien_ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `don_hang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ma_don_hang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nguoi_dung_id` int DEFAULT NULL,
  `dia_chi_id` int DEFAULT NULL,
  `guest_ho_ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_sdt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_dia_chi_day_du` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_giam_gia_id` int DEFAULT NULL,
  `trang_thai` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `tong_tien` decimal(15,2) DEFAULT NULL,
  `phi_van_chuyen` decimal(15,2) DEFAULT '0.00',
  `tien_giam_gia` decimal(15,2) DEFAULT '0.00',
  `tong_thanh_toan` decimal(15,2) DEFAULT NULL,
  `ghi_chu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngay_giao_du_kien` datetime DEFAULT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ma_don` (`ma_don_hang`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `dia_chi_id` (`dia_chi_id`),
  KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_ngay_tao` (`ngay_tao`),
  KEY `idx_guest_sdt` (`guest_sdt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chi_tiet_don` (
  `id` int NOT NULL AUTO_INCREMENT,
  `don_hang_id` int NOT NULL,
  `phien_ban_id` int NOT NULL,
  `so_luong` int DEFAULT '1',
  `gia_tai_thoi_diem_mua` decimal(15,2) DEFAULT NULL,
  `ten_phien_ban_snapshot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_snapshot` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_don_phienban` (`don_hang_id`,`phien_ban_id`),
  KEY `phien_ban_id` (`phien_ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lich_su_don_hang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `don_hang_id` int NOT NULL,
  `trang_thai_cu` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai_moi` enum('CHO_DUYET','DA_XAC_NHAN','DANG_GIAO','DA_GIAO','HOAN_THANH','DA_HUY','TRA_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nguoi_thuc_hien` int DEFAULT NULL,
  `ghi_chu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thoi_gian` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `nguoi_thuc_hien` (`nguoi_thuc_hien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `thanh_toan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `don_hang_id` int NOT NULL,
  `nguoi_duyet_id` int DEFAULT NULL,
  `phuong_thuc` enum('COD','CHUYEN_KHOAN','QR','TRA_GOP','VI_DIEN_TU') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `anh_bien_lai` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_giao_dich` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai_duyet` enum('CHO_DUYET','THANH_CONG','THAT_BAI','HOAN_TIEN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `ghi_chu_duyet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngay_thanh_toan` datetime DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `nguoi_duyet_id` (`nguoi_duyet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `danh_gia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `don_hang_id` int DEFAULT NULL,
  `so_sao` int DEFAULT NULL CHECK (`so_sao` BETWEEN 1 AND 5),
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `da_xac_minh_mua` tinyint(1) DEFAULT '0',
  `trang_thai` enum('CHO_DUYET','DA_DUYET','AN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'CHO_DUYET',
  `ngay_viet` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `don_hang_id` (`don_hang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `yeu_thich` (
  `nguoi_dung_id` int NOT NULL,
  `san_pham_id` int NOT NULL,
  `ngay_them` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nguoi_dung_id`,`san_pham_id`),
  KEY `san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `thong_bao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int NOT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `loai` enum('DON_HANG','KHUYEN_MAI','HE_THONG','TRA_HANG') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'HE_THONG',
  `lien_ket` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `da_doc` tinyint(1) DEFAULT '0',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chua_doc` (`nguoi_dung_id`, `da_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `banner_quang_cao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinh_anh_desktop` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinh_anh_mobile` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_dich` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vi_tri` enum('HOME_HERO','HOME_SIDE','FLOATING_BOTTOM_LEFT','POPUP','CATEGORY_TOP') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thu_tu` int DEFAULT '0',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_vi_tri_trang_thai` (`vi_tri`,`trang_thai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lich_su_tim_kiem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int DEFAULT NULL,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tu_khoa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_ket_qua` int DEFAULT NULL,
  `san_pham_click_id` int DEFAULT NULL,
  `thoi_gian_tim` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_tu_khoa` (`tu_khoa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `nguoi_dung_vai_tro`
  ADD CONSTRAINT `ndvt_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ndvt_fk_2` FOREIGN KEY (`vai_tro_id`) REFERENCES `vai_tro` (`id`) ON DELETE CASCADE;

ALTER TABLE `danh_muc`
  ADD CONSTRAINT `danh_muc_fk_1` FOREIGN KEY (`danh_muc_cha_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

ALTER TABLE `quan_huyen`
  ADD CONSTRAINT `qh_fk_1` FOREIGN KEY (`tinh_thanh_id`) REFERENCES `tinh_thanh` (`id`) ON DELETE CASCADE;

ALTER TABLE `phuong_xa`
  ADD CONSTRAINT `px_fk_1` FOREIGN KEY (`quan_huyen_id`) REFERENCES `quan_huyen` (`id`) ON DELETE CASCADE;

ALTER TABLE `dia_chi`
  ADD CONSTRAINT `dia_chi_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dia_chi_fk_2` FOREIGN KEY (`tinh_thanh_id`) REFERENCES `tinh_thanh` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dia_chi_fk_3` FOREIGN KEY (`quan_huyen_id`) REFERENCES `quan_huyen` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dia_chi_fk_4` FOREIGN KEY (`phuong_xa_id`) REFERENCES `phuong_xa` (`id`) ON DELETE SET NULL;

ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_fk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

ALTER TABLE `phien_ban_san_pham`
  ADD CONSTRAINT `phienban_fk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

ALTER TABLE `bien_dong_kho`
  ADD CONSTRAINT `bdk_fk_1` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bdk_fk_2` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bdk_fk_3` FOREIGN KEY (`nguoi_thuc_hien`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

ALTER TABLE `hinh_anh_san_pham`
  ADD CONSTRAINT `hinh_anh_fk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hinh_anh_fk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE SET NULL;

ALTER TABLE `thong_so_ky_thuat`
  ADD CONSTRAINT `thong_so_fk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

ALTER TABLE `san_pham_khuyen_mai`
  ADD CONSTRAINT `sp_km_fk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sp_km_fk_2` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE;

ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

ALTER TABLE `chi_tiet_gio`
  ADD CONSTRAINT `ctg_fk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ctg_fk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_fk_2` FOREIGN KEY (`dia_chi_id`) REFERENCES `dia_chi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_fk_3` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL;

ALTER TABLE `chi_tiet_don`
  ADD CONSTRAINT `ctd_fk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ctd_fk_2` FOREIGN KEY (`phien_ban_id`) REFERENCES `phien_ban_san_pham` (`id`) ON DELETE CASCADE;

ALTER TABLE `lich_su_don_hang`
  ADD CONSTRAINT `lsdh_fk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lsdh_fk_2` FOREIGN KEY (`nguoi_thuc_hien`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

ALTER TABLE `lich_su_ma_giam_gia`
  ADD CONSTRAINT `lsmgg_fk_1` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lsmgg_fk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lsmgg_fk_3` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE;

ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_fk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thanh_toan_fk_2` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_fk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_fk_3` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE SET NULL;

ALTER TABLE `yeu_thich`
  ADD CONSTRAINT `yeu_thich_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeu_thich_fk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

ALTER TABLE `lich_su_tim_kiem`
  ADD CONSTRAINT `lstk_fk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lstk_fk_2` FOREIGN KEY (`san_pham_click_id`) REFERENCES `san_pham` (`id`) ON DELETE SET NULL;

DELIMITER $$

CREATE TRIGGER `after_insert_phien_ban`
AFTER INSERT ON `phien_ban_san_pham`
FOR EACH ROW
BEGIN
  UPDATE `san_pham`
  SET `gia_hien_thi` = (
    SELECT MIN(`gia_ban`) FROM `phien_ban_san_pham`
    WHERE `san_pham_id` = NEW.`san_pham_id`
    AND `trang_thai` = 'CON_HANG'
  )
  WHERE `id` = NEW.`san_pham_id`;
END$$

CREATE TRIGGER `after_update_phien_ban`
AFTER UPDATE ON `phien_ban_san_pham`
FOR EACH ROW
BEGIN
  UPDATE `san_pham`
  SET `gia_hien_thi` = (
    SELECT MIN(`gia_ban`) FROM `phien_ban_san_pham`
    WHERE `san_pham_id` = NEW.`san_pham_id`
    AND `trang_thai` = 'CON_HANG'
  )
  WHERE `id` = NEW.`san_pham_id`;
END$$

CREATE TRIGGER `after_insert_danh_gia`
AFTER INSERT ON `danh_gia`
FOR EACH ROW
BEGIN
  UPDATE `san_pham`
  SET
    `diem_danh_gia` = (
      SELECT ROUND(AVG(`so_sao`), 2) FROM `danh_gia`
      WHERE `san_pham_id` = NEW.`san_pham_id` AND `trang_thai` = 'DA_DUYET'
    ),
    `so_danh_gia` = (
      SELECT COUNT(*) FROM `danh_gia`
      WHERE `san_pham_id` = NEW.`san_pham_id` AND `trang_thai` = 'DA_DUYET'
    )
  WHERE `id` = NEW.`san_pham_id`;
END$$

CREATE TRIGGER `after_update_danh_gia`
AFTER UPDATE ON `danh_gia`
FOR EACH ROW
BEGIN
  UPDATE `san_pham`
  SET
    `diem_danh_gia` = (
      SELECT ROUND(AVG(`so_sao`), 2) FROM `danh_gia`
      WHERE `san_pham_id` = NEW.`san_pham_id` AND `trang_thai` = 'DA_DUYET'
    ),
    `so_danh_gia` = (
      SELECT COUNT(*) FROM `danh_gia`
      WHERE `san_pham_id` = NEW.`san_pham_id` AND `trang_thai` = 'DA_DUYET'
    )
  WHERE `id` = NEW.`san_pham_id`;
END$$

DELIMITER ;
COMMIT;
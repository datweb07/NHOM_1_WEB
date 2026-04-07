-- Migration: Add OAuth Support to nguoi_dung table
-- Date: 2026-04-07
-- Description: Add supabase_id, auth_provider fields and modify mat_khau to allow NULL

ALTER TABLE `nguoi_dung`
-- 1. Cho phép mật khẩu được NULL
MODIFY `mat_khau` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cho phép NULL nếu đăng nhập bằng nền tảng khác',

-- 2. Thêm cột lưu UUID của Supabase (UUID có độ dài 36 ký tự)
ADD `supabase_id` CHAR(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã định danh duy nhất từ Supabase' AFTER `id`,

-- 3. Thêm cột phân loại nguồn đăng nhập
ADD `auth_provider` ENUM('LOCAL','GOOGLE','FACEBOOK') COLLATE utf8mb4_unicode_ci DEFAULT 'LOCAL' COMMENT 'Nguồn tạo tài khoản' AFTER `supabase_id`,

-- 4. Đánh Index cho supabase_id và email để tăng tốc độ truy vấn khi đăng nhập
ADD UNIQUE KEY `idx_supabase_id` (`supabase_id`),
ADD UNIQUE KEY `idx_email` (`email`);

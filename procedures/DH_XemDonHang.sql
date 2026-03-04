CREATE PROCEDURE DH_XemDonHang (
    IN p_don_hang_id INT,
    IN p_nguoi_dung_id INT
)
BEGIN

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

END
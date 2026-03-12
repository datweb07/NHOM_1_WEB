DELIMITER //

CREATE PROCEDURE sp_xem_hoa_don (
    IN p_don_hang_id INT
)
BEGIN
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
END //

DELIMITER ;
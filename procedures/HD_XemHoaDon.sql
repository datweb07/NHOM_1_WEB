CREATE PROCEDURE sp_xem_hoa_don (
    IN p_don_hang_id INT
)
BEGIN

    SELECT 
        dh.id AS ma_don,
        dh.ngay_tao,
        dh.trang_thai,
        dh.tong_tien,

        nd.ho_ten,
        nd.email,
        nd.sdt,

        sp.ten_san_pham,
        pb.ten_phien_ban,
        ctd.so_luong,
        ctd.gia_tai_thoi_diem_mua,

        tt.phuong_thuc,
        tt.trang_thai_duyet,
        tt.ngay_thanh_toan

    FROM don_hang dh
    LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
    JOIN chi_tiet_don ctd ON dh.id = ctd.don_hang_id
    JOIN phien_ban_san_pham pb ON ctd.phien_ban_id = pb.id
    JOIN san_pham sp ON pb.san_pham_id = sp.id
    LEFT JOIN thanh_toan tt ON dh.id = tt.don_hang_id

    WHERE dh.id = p_don_hang_id;

END
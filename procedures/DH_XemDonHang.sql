CREATE PROCEDURE DH_XemDanhSachDonHang(
    IN p_nguoi_dung_id INT
)
BEGIN
    SELECT 
        dh.id,
        dh.ngay_tao,
        dh.trang_thai,
        dh.tong_tien,
        COUNT(ct.id) AS so_san_pham
    FROM don_hang dh
    LEFT JOIN chi_tiet_don ct ON ct.don_hang_id = dh.id
    WHERE dh.nguoi_dung_id = p_nguoi_dung_id
    GROUP BY dh.id
    ORDER BY dh.ngay_tao DESC;
END
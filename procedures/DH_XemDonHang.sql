BEGIN
    SELECT 
        dh.id,
        dh.ma_don_hang,
        dh.trang_thai,
        dh.tong_tien,
        dh.tien_giam_gia,
        dh.phi_van_chuyen,
        dh.tong_thanh_toan,
        dh.ngay_tao
    FROM don_hang dh
    WHERE dh.nguoi_dung_id = p_nguoi_dung_id
    ORDER BY dh.ngay_tao DESC;
END
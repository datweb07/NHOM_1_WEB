DELIMITER //

CREATE PROCEDURE SP_TimKiemVaThongKeSanPham(IN p_tu_khoa VARCHAR(255))
BEGIN
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
END //

DELIMITER ;
DELIMITER //

CREATE PROCEDURE SP_LayChiTietPhienBanSanPham(IN p_san_pham_id INT)
BEGIN
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
END //

DELIMITER ;
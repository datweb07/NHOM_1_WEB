BEGIN
    SELECT 
        sp.ten_san_pham, 
        sp.hang_san_xuat,
        pb.sku, 
        pb.ten_phien_ban, 
        pb.mau_sac, 
        pb.cau_hinh, 
        pb.gia_ban, 
        pb.so_luong_ton
    FROM SAN_PHAM sp
    JOIN PHIEN_BAN_SAN_PHAM pb ON sp.id = pb.san_pham_id
    WHERE sp.id = p_san_pham_id;
END

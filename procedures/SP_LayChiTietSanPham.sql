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
        p.gia_goc,
        pb.gia_ban, 
        pb.so_luong_ton,
        pb.trang_thai
    FROM SAN_PHAM sp
    JOIN PHIEN_BAN_SAN_PHAM pb ON sp.id = pb.san_pham_id
    WHERE sp.id = p_san_pham_id;
END

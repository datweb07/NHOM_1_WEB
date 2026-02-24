BEGIN 
    SELECT ctg.id, sp.ten_san_pham, pb.ten_phien_ban, pb.gia_ban, ctg.so_luong 
    FROM chi_tiet_gio ctg
    JOIN phien_ban_san_pham pb ON ctg.phien_ban_id = pb.id
    JOIN san_pham sp ON pb.san_pham_id = sp.id 
    WHERE ctg.gio_hang_id = p_gio_hang_id;
END

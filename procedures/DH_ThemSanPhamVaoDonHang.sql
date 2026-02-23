BEGIN
    INSERT INTO chi_tiet_don(
        don_hang_id,
        phien_ban_id,
        so_luong,
        gia_tai_thoi_diem_mua
    )
    SELECT 
        p_don_hang_id,
        ct.phien_ban_id,
        ct.so_luong,
        pb.gia_ban
    FROM gio_hang gh
    JOIN chi_tiet_gio ct ON gh.id = ct.gio_hang_id
    JOIN phien_ban_san_pham pb ON pb.id = ct.phien_ban_id
    WHERE gh.nguoi_dung_id = p_nguoi_dung_id;
END

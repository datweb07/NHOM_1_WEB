BEGIN
    -- thêm chi tiết đơn
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
    -- cập nhật tổng tiền
    UPDATE don_hang dh
    SET tong_tien = (
        SELECT SUM(so_luong * gia_tai_thoi_diem_mua)
        FROM chi_tiet_don
        WHERE don_hang_id = p_don_hang_id
    )
    WHERE dh.id = p_don_hang_id;
    -- trừ tồn kho
    UPDATE phien_ban_san_pham pb
    JOIN chi_tiet_don ct ON pb.id = ct.phien_ban_id
    SET pb.so_luong_ton = pb.so_luong_ton - ct.so_luong
    WHERE ct.don_hang_id = p_don_hang_id;
    -- xoá giỏ
    DELETE ct FROM chi_tiet_gio ct
    JOIN gio_hang gh ON gh.id = ct.gio_hang_id
    WHERE gh.nguoi_dung_id = p_nguoi_dung_id;
END
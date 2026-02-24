BEGIN
    INSERT INTO don_hang(
        nguoi_dung_id,
        dia_chi_id,
        trang_thai,
        tong_tien,
        thong_tin_guest,
        ngay_tao
    )
    VALUES(
        p_nguoi_dung_id,
        p_dia_chi_id,
        0,
        0,
        NULL,
        NOW()
    );
    -- lấy id đơn vừa tạo
    SET p_don_hang_id = LAST_INSERT_ID();
END
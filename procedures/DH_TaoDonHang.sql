BEGIN
    INSERT INTO don_hang(
        nguoi_dung_id,
        dia_chi_id,
        trang_thai,
        tong_tien
    )
    VALUES(
        p_nguoi_dung_id,
        p_dia_chi_id,
        0
    );
END
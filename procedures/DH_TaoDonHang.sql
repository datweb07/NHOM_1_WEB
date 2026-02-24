CREATE PROCEDURE DH_Checkout(
    IN p_gio_hang_id INT,
    IN p_nguoi_dung_id INT,
    IN p_dia_chi_id INT,
    IN p_thong_tin_guest TEXT,
    OUT p_don_hang_id INT
)
BEGIN
    DECLARE v_tong_tien DECIMAL(15,2) DEFAULT 0;

    START TRANSACTION;

    -- 1. tạo đơn hàng
    INSERT INTO don_hang(
        nguoi_dung_id,
        dia_chi_id,
        trang_thai,
        tong_tien,
        thong_tin_guest,
        ngay_tao
    )
    VALUES(
        NULLIF(p_nguoi_dung_id,0),
        NULLIF(p_dia_chi_id,0),
        'CHO_DUYET',
        0,
        p_thong_tin_guest,
        NOW()
    );

    SET p_don_hang_id = LAST_INSERT_ID();


    -- 2. copy giỏ -> chi tiết đơn
    INSERT INTO chi_tiet_don(
        don_hang_id,
        phien_ban_id,
        so_luong,
        gia_tai_thoi_diem_mua
    )
    SELECT
        p_don_hang_id,
        g.phien_ban_id,
        g.so_luong,
        pb.gia_ban
    FROM chi_tiet_gio g
    JOIN phien_ban_san_pham pb ON pb.id = g.phien_ban_id
    WHERE g.gio_hang_id = p_gio_hang_id;


    -- 3. tính tổng tiền
    SELECT SUM(so_luong * gia_tai_thoi_diem_mua)
    INTO v_tong_tien
    FROM chi_tiet_don
    WHERE don_hang_id = p_don_hang_id;

    UPDATE don_hang
    SET tong_tien = v_tong_tien
    WHERE id = p_don_hang_id;


    -- 4. trừ kho
    UPDATE phien_ban_san_pham pb
    JOIN chi_tiet_gio g ON pb.id = g.phien_ban_id
    SET pb.so_luong_ton = pb.so_luong_ton - g.so_luong
    WHERE g.gio_hang_id = p_gio_hang_id;


    -- 5. xoá giỏ
    DELETE FROM chi_tiet_gio WHERE gio_hang_id = p_gio_hang_id;
    DELETE FROM gio_hang WHERE id = p_gio_hang_id;

    COMMIT;
END
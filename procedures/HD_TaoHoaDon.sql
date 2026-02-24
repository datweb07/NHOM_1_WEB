CREATE PROCEDURE sp_tao_hoa_don (
    IN p_nguoi_dung_id INT,
    IN p_dia_chi_id INT,
    IN p_phien_ban_id INT,
    IN p_so_luong INT,
    IN p_phuong_thuc VARCHAR(50)
)
BEGIN
    DECLARE v_gia DECIMAL(15,2);
    DECLARE v_tong DECIMAL(15,2);
    DECLARE v_don_hang_id INT;

    START TRANSACTION;

    -- Lấy giá sản phẩm
    SELECT gia_ban INTO v_gia
    FROM phien_ban_san_pham
    WHERE id = p_phien_ban_id;

    SET v_tong = v_gia * p_so_luong;

    -- Tạo đơn hàng
    INSERT INTO don_hang (
        nguoi_dung_id,
        dia_chi_id,
        trang_thai,
        tong_tien
    )
    VALUES (
        p_nguoi_dung_id,
        p_dia_chi_id,
        'CHO_DUYET',
        v_tong
    );

    SET v_don_hang_id = LAST_INSERT_ID();

    -- Tạo chi tiết đơn
    INSERT INTO chi_tiet_don (
        don_hang_id,
        phien_ban_id,
        so_luong,
        gia_tai_thoi_diem_mua
    )
    VALUES (
        v_don_hang_id,
        p_phien_ban_id,
        p_so_luong,
        v_gia
    );

    -- Tạo thanh toán
    INSERT INTO thanh_toan (
        don_hang_id,
        phuong_thuc,
        so_tien,
        trang_thai_duyet
    )
    VALUES (
        v_don_hang_id,
        p_phuong_thuc,
        v_tong,
        'CHO_DUYET'
    );

    COMMIT;
END
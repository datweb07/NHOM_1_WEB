CREATE PROCEDURE sp_tao_don_hang (
    IN p_nguoi_dung_id INT,
    IN p_dia_chi_id INT,
    IN p_ma_giam_gia_id INT,
    IN p_phi_van_chuyen DECIMAL(15,2),
    OUT p_don_hang_id INT
)
BEGIN
    DECLARE v_gio_hang_id INT;
    DECLARE v_tong_tien DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tien_giam DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tong_thanh_toan DECIMAL(15,2);

    -- Lấy giỏ hàng của user
    SELECT id INTO v_gio_hang_id
    FROM gio_hang
    WHERE nguoi_dung_id = p_nguoi_dung_id
    LIMIT 1;

    IF v_gio_hang_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Giỏ hàng không tồn tại';
    END IF;

    -- Tạo đơn hàng trước
    INSERT INTO don_hang (
        ma_don_hang,
        nguoi_dung_id,
        dia_chi_id,
        ma_giam_gia_id,
        phi_van_chuyen,
        trang_thai
    )
    VALUES (
        CONCAT('DH', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
        p_nguoi_dung_id,
        p_dia_chi_id,
        p_ma_giam_gia_id,
        p_phi_van_chuyen,
        'CHO_DUYET'
    );

    SET p_don_hang_id = LAST_INSERT_ID();

    --  Thêm chi tiết đơn từ giỏ hàng
    INSERT INTO chi_tiet_don (
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
    FROM chi_tiet_gio ct
    JOIN phien_ban_san_pham pb ON ct.phien_ban_id = pb.id
    WHERE ct.gio_hang_id = v_gio_hang_id;

    -- Tính tổng tiền
    SELECT SUM(so_luong * gia_tai_thoi_diem_mua)
    INTO v_tong_tien
    FROM chi_tiet_don
    WHERE don_hang_id = p_don_hang_id;

    -- Áp dụng mã giảm giá (nếu có)
    IF p_ma_giam_gia_id IS NOT NULL THEN

        SELECT
            CASE
                WHEN loai_giam = 'PHAN_TRAM'
                THEN LEAST((v_tong_tien * gia_tri_giam / 100), IFNULL(giam_toi_da, v_tong_tien))
                ELSE gia_tri_giam
            END
        INTO v_tien_giam
        FROM ma_giam_gia
        WHERE id = p_ma_giam_gia_id
        AND trang_thai = 'HOAT_DONG'
        AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
        LIMIT 1;

        IF v_tien_giam IS NULL THEN
            SET v_tien_giam = 0;
        END IF;

    END IF;

    -- Tổng thanh toán
    SET v_tong_thanh_toan = v_tong_tien + p_phi_van_chuyen - v_tien_giam;

    -- Cập nhật đơn hàng
    UPDATE don_hang
    SET tong_tien = v_tong_tien,
        tien_giam_gia = v_tien_giam,
        tong_thanh_toan = v_tong_thanh_toan
    WHERE id = p_don_hang_id;

    -- Trừ tồn kho
    UPDATE phien_ban_san_pham pb
    JOIN chi_tiet_don ct ON pb.id = ct.phien_ban_id
    SET pb.so_luong_ton = pb.so_luong_ton - ct.so_luong
    WHERE ct.don_hang_id = p_don_hang_id;

    -- Xóa giỏ hàng
    DELETE FROM chi_tiet_gio WHERE gio_hang_id = v_gio_hang_id;

    COMMIT;

END
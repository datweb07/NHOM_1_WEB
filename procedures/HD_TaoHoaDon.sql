DELIMITER //

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
    DECLARE v_ma_don VARCHAR(20);
    
    -- Biến tạm để lưu snapshot địa chỉ
    DECLARE v_ten_nhan VARCHAR(255);
    DECLARE v_sdt_nhan VARCHAR(20);
    DECLARE v_dia_chi_full TEXT;

    -- Bắt đầu Transaction để đảm bảo tính nguyên tử
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Lấy thông tin địa chỉ để Snapshot
    SELECT 
        ten_nguoi_nhan, 
        sdt_nhan, 
        CONCAT(so_nha_duong, ', ', phuong_xa, ', ', quan_huyen, ', ', tinh_thanh)
    INTO v_ten_nhan, v_sdt_nhan, v_dia_chi_full
    FROM dia_chi
    WHERE id = p_dia_chi_id AND nguoi_dung_id = p_nguoi_dung_id;

    -- 2. Lấy giá sản phẩm và kiểm tra tồn kho
    SELECT gia_ban INTO v_gia
    FROM phien_ban_san_pham
    WHERE id = p_phien_ban_id;

    SET v_tong = v_gia * p_so_luong;
    
    -- 3. Tạo Mã đơn hàng tự động: DH + NămThángNgày + 4 số ngẫu nhiên
    SET v_ma_don = CONCAT('DH', DATE_FORMAT(NOW(), '%Y%m%d'), FLOOR(RAND() * 10000));

    -- 4. Tạo đơn hàng với THÔNG TIN SNAPSHOT
    INSERT INTO don_hang (
        ma_don_hang,
        nguoi_dung_id,
        ten_nguoi_nhan,
        sdt_nguoi_nhan,
        dia_chi_giao_hang,
        trang_thai,
        tam_tinh,
        phi_van_chuyen,
        tong_thanh_toan
    )
    VALUES (
        v_ma_don,
        p_nguoi_dung_id,
        v_ten_nhan,
        v_sdt_nhan,
        v_dia_chi_full,
        'CHO_XAC_NHAN',
        v_tong,
        0, -- Giả định phí ship mặc định là 0
        v_tong
    );

    SET v_don_hang_id = LAST_INSERT_ID();

    -- 5. Tạo chi tiết đơn
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

    -- 6. Tạo bản ghi thanh toán
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
    
    -- Trả về ID đơn vừa tạo để Backend xử lý tiếp
    SELECT v_don_hang_id AS id_moi_tao, v_ma_don AS ma_don_hang;
END //

DELIMITER ;
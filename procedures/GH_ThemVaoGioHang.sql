DELIMITER //

CREATE PROCEDURE GH_ThemVaoGioHang(
    IN p_gio_hang_id INT,
    IN p_phien_ban_id INT,
    IN p_so_luong INT
)
BEGIN
    -- Kiểm tra xem phiên bản sản phẩm này đã có trong giỏ hàng chưa
    IF EXISTS (SELECT 1 FROM chi_tiet_gio WHERE gio_hang_id = p_gio_hang_id AND phien_ban_id = p_phien_ban_id) THEN
        -- Nếu có rồi thì cộng dồn số lượng
        UPDATE chi_tiet_gio 
        SET so_luong = so_luong + p_so_luong
        WHERE gio_hang_id = p_gio_hang_id AND phien_ban_id = p_phien_ban_id;
    ELSE
        -- Nếu chưa có thì chèn mới
        INSERT INTO chi_tiet_gio (gio_hang_id, phien_ban_id, so_luong)
        VALUES (p_gio_hang_id, p_phien_ban_id, p_so_luong);
    END IF;
    
    -- Cập nhật thời gian thay đổi của giỏ hàng mẹ
    UPDATE gio_hang SET ngay_cap_nhat = CURRENT_TIMESTAMP WHERE id = p_gio_hang_id;
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE GH_CapNhatSoLuongGio(
    IN p_chi_tiet_id INT,
    IN p_so_luong_moi INT
)
BEGIN
    -- Nếu số lượng mới <= 0 thì tự động xóa khỏi giỏ
    IF p_so_luong_moi <= 0 THEN
        DELETE FROM chi_tiet_gio WHERE id = p_chi_tiet_id;
    ELSE
        UPDATE chi_tiet_gio 
        SET so_luong = p_so_luong_moi
        WHERE id = p_chi_tiet_id;
    END IF;
END //

DELIMITER ;

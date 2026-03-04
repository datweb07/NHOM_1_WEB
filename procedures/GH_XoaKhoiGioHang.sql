DELIMITER //

CREATE PROCEDURE GH_XoaKhoiGioHang(
    IN p_chi_tiet_id INT
)
BEGIN
    DELETE FROM chi_tiet_gio WHERE id = p_chi_tiet_id;
END //

DELIMITER ;

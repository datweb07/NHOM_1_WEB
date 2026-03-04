DELIMITER //

CREATE PROCEDURE GH_LayChiTietGioHang(
    IN p_gio_hang_id INT
)
BEGIN
    SELECT 
        ctg.id AS chi_tiet_id,
        sp.ten_san_pham,
        pb.ten_phien_ban,
        pb.mau_sac,
        pb.dung_luong,
        pb.gia_ban,
        ctg.so_luong,
        (pb.gia_ban * ctg.so_luong) AS thanh_tien,
        -- Lấy ảnh chính của sản phẩm (nếu có ảnh phiên bản thì lấy, ko thì lấy ảnh chung)
        COALESCE(ha_pb.url_anh, ha_sp.url_anh) AS hinh_anh
    FROM chi_tiet_gio ctg
    JOIN phien_ban_san_pham pb ON ctg.phien_ban_id = pb.id
    JOIN san_pham sp ON pb.san_pham_id = sp.id
    -- Join lấy ảnh chính của sản phẩm
    LEFT JOIN hinh_anh_san_pham ha_sp ON sp.id = ha_sp.san_pham_id AND ha_sp.la_anh_chinh = 1 AND ha_sp.phien_ban_id IS NULL
    -- Join lấy ảnh của riêng phiên bản (nếu có)
    LEFT JOIN hinh_anh_san_pham ha_pb ON pb.id = ha_pb.phien_ban_id AND ha_pb.la_anh_chinh = 1
    WHERE ctg.gio_hang_id = p_gio_hang_id;
END //

DELIMITER ;
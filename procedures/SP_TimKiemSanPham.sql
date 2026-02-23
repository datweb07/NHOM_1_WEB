begin
    select 
        sp.id as ma_san_pham,
        sp.ten_san_pham,
        dm.ten as ten_danh_muc,
        sp.diem_danh_gia,
        min(pb.gia_ban) as gia_thap_nhat,
        max(pb.gia_ban) as gia_cao_nhat,
        sum(pb.so_luong_ton) as tong_ton_kho
    from SAN_PHAM sp
    left join DANH_MUC dm on sp.danh_muc_id = dm.id
    left join PHIEN_BAN_SAN_PHAM pb on sp.id = pb.san_pham_id
    where sp.ten_san_pham like concat('%', p_tu_khoa, '%')
    group by
        sp.id,
        sp.ten_san_pham,
        dm.ten,
        sp.diem_danh_gia
    order by sp.diem_danh_gia desc;
end
<?php

abstract class TrangThaiSanPham
{
    const CON_BAN = 'CON_BAN';
    const NGUNG_BAN = 'NGUNG_BAN';
    const SAP_RA_MAT = 'SAP_RA_MAT';
    const HET_HANG = 'HET_HANG';


    public static function getAll(): array
    {
        return [
            self::CON_BAN,
            self::NGUNG_BAN,
            self::SAP_RA_MAT,
            self::HET_HANG
        ];
    }


    //kiểm tra hợp lệ trạng thái sản phẩm
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }

   
    //hiển thị
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            case self::CON_BAN:
                return 'Còn bán';
            case self::NGUNG_BAN:
                return 'Ngừng bán';
            case self::SAP_RA_MAT:
                return 'Sắp ra mắt';
            case self::HET_HANG:
                return 'Hết hàng';
            default:
                return 'Không xác định';
        }
    }

   
    
    //lấy màu trạng thái
    public static function getBadgeColor(?string $value): string
    {
        switch ($value) {
            case self::CON_BAN:
                return 'success';
            case self::NGUNG_BAN:
                return 'secondary';
            case self::SAP_RA_MAT:
                return 'info';
            case self::HET_HANG:
                return 'danger';
            default:
                return 'dark';
        }
    }
}

?>
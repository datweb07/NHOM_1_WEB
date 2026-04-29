<?php

abstract class GioiTinh
{
    const NAM = 'NAM';
    const NU = 'NU';
    const KHAC = 'KHAC';

    public static function getAll(): array
    {
        return [
            self::NAM,
            self::NU,
            self::KHAC
        ];
    }

    //kiểm tra hợp lệ của value giới tính
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }
    
    //hiển thị giới tính
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            case self::NAM:
                return 'Nam';
            case self::NU:
                return 'Nữ';
            case self::KHAC:
                return 'Khác';
            default:
                return 'Không xác định';
        }
    }
}

?>
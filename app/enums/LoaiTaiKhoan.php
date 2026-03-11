<?php

abstract class LoaiTaiKhoan
{
    const ADMIN = 'ADMIN';
    const MEMBER = 'MEMBER';


    public static function getAll(): array
    {
        return [
            self::ADMIN,
            self::MEMBER
        ];
    }

    
    //kiểm tra hợp lệ của value loại tk
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }

    //hiển thị
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            case self::ADMIN:
                return 'Quản trị viên';
            case self::MEMBER:
                return 'Khách hàng';
            default:
                return 'Không xác định';    //là guest
        }
    }
}

?>
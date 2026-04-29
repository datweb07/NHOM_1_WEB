<?php

abstract class TrangThaiDon
{
    const CHO_XAC_NHAN = 'CHO_XAC_NHAN';
    const DA_XAC_NHAN = 'DA_XAC_NHAN';
    const DANG_CHUAN_BI = 'DANG_CHUAN_BI';
    const DANG_GIAO = 'DANG_GIAO';
    const DA_GIAO = 'DA_GIAO';
    const DA_HUY = 'DA_HUY';
    const TRA_HANG = 'TRA_HANG';


    public static function getAll(): array
    {
        return [
            self::CHO_XAC_NHAN,
            self::DA_XAC_NHAN,
            self::DANG_CHUAN_BI,
            self::DANG_GIAO,
            self::DA_GIAO,
            self::DA_HUY,
            self::TRA_HANG
        ];
    }

    //kiểm tra hợp lệ của trạng thái
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }
    
    //hiển thị
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            case self::CHO_XAC_NHAN:
                return 'Chờ xác nhận';
            case self::DA_XAC_NHAN:
                return 'Đã xác nhận';
            case self::DANG_CHUAN_BI:
                return 'Đang chuẩn bị';
            case self::DANG_GIAO:
                return 'Đang giao';
            case self::DA_GIAO:
                return 'Đã giao';
            case self::DA_HUY:
                return 'Đã hủy';
            case self::TRA_HANG:
                return 'Trả hàng';
            default:
                return 'Không xác định';
        }
    }

    //lấy màu trạng thái
    public static function getBadgeColor(?string $value): string
    {
        switch ($value) {
            case self::CHO_XAC_NHAN:
                return 'warning';
            case self::DA_XAC_NHAN:
                return 'info';
            case self::DANG_CHUAN_BI:
                return 'primary';
            case self::DANG_GIAO:
                return 'primary';
            case self::DA_GIAO:
                return 'success';
            case self::DA_HUY:
                return 'danger';
            case self::TRA_HANG:
                return 'secondary';
            default:
                return 'dark';
        }
    }
    
    //có thể hủy đơn hàng
    public static function canCancel(string $trangThai): bool
    {
        return in_array($trangThai, [
            self::CHO_XAC_NHAN,
            self::DA_XAC_NHAN,
            self::DANG_CHUAN_BI
        ]);
    }

    //đơn hàng đã giao
    public static function isCompleted(string $trangThai): bool
    {
        return $trangThai === self::DA_GIAO;
    }
}

?>
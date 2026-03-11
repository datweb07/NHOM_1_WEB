<?php

abstract class TrangThaiDuyetThanhToan
{
    const CHO_DUYET = 'CHO_DUYET';
    const DA_DUYET = 'DA_DUYET';
    const TU_CHOI = 'TU_CHOI';
    const HOAN_TIEN = 'HOAN_TIEN';



    public static function getAll(): array
    {
        return [
            self::CHO_DUYET,
            self::DA_DUYET,
            self::TU_CHOI,
            self::HOAN_TIEN
        ];
    }


    //kiểm tra hợp lệ của trạng thái thanh toán
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }

    
    //hiển thị
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            case self::CHO_DUYET:
                return 'Chờ duyệt';
            case self::DA_DUYET:
                return 'Đã duyệt';
            case self::TU_CHOI:
                return 'Từ chối';
            case self::HOAN_TIEN:
                return 'Hoàn tiền';
            default:
                return 'Không xác định';
        }
    }

    
    //lấy màu trạng thái
    public static function getBadgeColor(?string $value): string
    {
        switch ($value) {
            case self::CHO_DUYET:
                return 'warning';
            case self::DA_DUYET:
                return 'success';
            case self::TU_CHOI:
                return 'danger';
            case self::HOAN_TIEN:
                return 'info';
            default:
                return 'secondary';
        }
    }
}

?>
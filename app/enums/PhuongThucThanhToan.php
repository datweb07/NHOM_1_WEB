<?php

abstract class PhuongThucThanhToan
{
    // const TIEN_MAT = 'TIEN_MAT';
    const CHUYEN_KHOAN = 'CHUYEN_KHOAN';
    const THE_TIN_DUNG = 'THE_TIN_DUNG';
    const VI_DIEN_TU = 'VI_DIEN_TU';
    const COD = 'COD';


    public static function getAll(): array
    {
        return [
            // self::TIEN_MAT,
            self::CHUYEN_KHOAN,
            self::THE_TIN_DUNG,
            self::VI_DIEN_TU,
            self::COD
        ];
    }


    //kiểm tra hợp lệ của value phương thức thanh toán
    public static function isValid(?string $value): bool
    {
        return in_array($value, self::getAll());
    }


    //hiển thị 
    public static function getLabel(?string $value): string
    {
        switch ($value) {
            // case self::TIEN_MAT:
            //     return 'Tiền mặt';
            case self::CHUYEN_KHOAN:
                return 'Chuyển khoản ngân hàng';
            case self::THE_TIN_DUNG:
                return 'Thẻ tín dụng/Ghi nợ';
            case self::VI_DIEN_TU:
                return 'Ví điện tử (Momo, ZaloPay, VNPay)';
            case self::COD:
                return 'Thanh toán khi nhận hàng (COD)';
            default:
                return 'Không xác định';
        }
    }

    //yêu cầu thanh toán trước
    public static function requiresPrepayment(string $phuongThuc): bool
    {
        return in_array($phuongThuc, [
            self::CHUYEN_KHOAN,
            self::THE_TIN_DUNG,
            self::VI_DIEN_TU
        ]);
    }
}

?>
<?php

abstract class PhuongThucThanhToan
{
    // const TIEN_MAT = 'TIEN_MAT';
    const CHUYEN_KHOAN = 'CHUYEN_KHOAN';
    const THE_TIN_DUNG = 'THE_TIN_DUNG';
    const VI_DIEN_TU = 'VI_DIEN_TU';
    const ZALOPAY = 'ZALOPAY';
    const COD = 'COD';


    public static function getAll(): array
    {
        return [
            // self::TIEN_MAT,
            self::CHUYEN_KHOAN,
            self::THE_TIN_DUNG,
            self::VI_DIEN_TU,
            self::ZALOPAY,
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
                return 'Thanh toán qua VNPay';
            case self::THE_TIN_DUNG:
                return 'Thẻ tín dụng/Ghi nợ';
            case self::VI_DIEN_TU:
                return 'Thanh toán qua ví Momo';
            case self::ZALOPAY:
                return 'Thanh toán qua ZaloPay';
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
            self::VI_DIEN_TU,
            self::ZALOPAY
        ]);
    }

    public static function getGatewayClass(string $paymentMethod): ?string
    {
        $gatewayMap = [
            self::COD => 'CODHandler',
            self::CHUYEN_KHOAN => 'VNPayGateway',
            self::VI_DIEN_TU => 'MomoGateway',
            self::ZALOPAY => 'ZaloPayGateway'
        ];

        return $gatewayMap[$paymentMethod] ?? null;
    }

    public static function getGatewayName(string $paymentMethod): string
    {
        $gatewayMap = [
            self::COD => 'COD',
            self::CHUYEN_KHOAN => 'VNPAY',
            self::VI_DIEN_TU => 'MOMO',
            self::ZALOPAY => 'ZALOPAY'
        ];

        return $gatewayMap[$paymentMethod] ?? 'UNKNOWN';
    }

    public static function getIcon(string $paymentMethod): string
    {
        $iconMap = [
            self::COD => 'fa-money-bill-wave',
            self::CHUYEN_KHOAN => 'fa-university',
            self::VI_DIEN_TU => 'fa-mobile-alt',
            self::ZALOPAY => 'fa-wallet',
            self::THE_TIN_DUNG => 'fa-credit-card'
        ];

        return $iconMap[$paymentMethod] ?? 'fa-wallet';
    }
}

?>

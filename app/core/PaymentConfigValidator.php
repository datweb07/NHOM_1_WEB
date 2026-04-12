<?php

namespace App\Core;

class PaymentConfigValidator
{
    public static function validate(): array
    {
        $warnings = [];
        $errors = [];


        $vnpayResult = self::validateVNPay();
        if (!$vnpayResult['valid']) {
            $warnings[] = $vnpayResult['message'];
        }


        $momoResult = self::validateMomo();
        if (!$momoResult['valid']) {
            $warnings[] = $momoResult['message'];
        }


        if (!empty($warnings)) {
            error_log('[Payment Config] ' . implode(' | ', $warnings));
        }

        return [
            'vnpay_configured' => $vnpayResult['valid'],
            'momo_configured' => $momoResult['valid'],
            'warnings' => $warnings,
            'errors' => $errors
        ];
    }

    private static function validateVNPay(): array
    {
        $required = ['VNPAY_TMN_CODE', 'VNPAY_HASH_SECRET', 'VNPAY_URL'];
        $missing = [];

        foreach ($required as $key) {
            if (empty($_ENV[$key])) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            return [
                'valid' => false,
                'message' => 'VNPay gateway disabled: Missing configuration - ' . implode(', ', $missing)
            ];
        }


        if (!filter_var($_ENV['VNPAY_URL'], FILTER_VALIDATE_URL)) {
            return [
                'valid' => false,
                'message' => 'VNPay gateway disabled: Invalid VNPAY_URL format'
            ];
        }

        return [
            'valid' => true,
            'message' => 'VNPay gateway configured successfully'
        ];
    }

    private static function validateMomo(): array
    {
        $required = ['MOMO_PARTNER_CODE', 'MOMO_ACCESS_KEY', 'MOMO_SECRET_KEY', 'MOMO_URL'];
        $missing = [];

        foreach ($required as $key) {
            if (empty($_ENV[$key])) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            return [
                'valid' => false,
                'message' => 'Momo gateway disabled: Missing configuration - ' . implode(', ', $missing)
            ];
        }


        if (!filter_var($_ENV['MOMO_URL'], FILTER_VALIDATE_URL)) {
            return [
                'valid' => false,
                'message' => 'Momo gateway disabled: Invalid MOMO_URL format'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Momo gateway configured successfully'
        ];
    }

    public static function getStatus(): array
    {
        $validation = self::validate();

        return [
            'vnpay' => [
                'enabled' => $validation['vnpay_configured'],
                'label' => 'VNPay',
                'icon' => 'fa-university'
            ],
            'momo' => [
                'enabled' => $validation['momo_configured'],
                'label' => 'Momo',
                'icon' => 'fa-mobile-alt'
            ],
            'cod' => [
                'enabled' => true, 
                'label' => 'COD',
                'icon' => 'fa-money-bill-wave'
            ]
        ];
    }

    public static function hasAvailablePaymentMethod(): bool
    {
        $validation = self::validate();
        return true; 
    }

    public static function getAvailablePaymentMethods(): array
    {
        $validation = self::validate();
        $methods = ['COD']; // COD is always available

        if ($validation['vnpay_configured']) {
            $methods[] = 'CHUYEN_KHOAN';
        }

        if ($validation['momo_configured']) {
            $methods[] = 'VI_DIEN_TU';
        }

        return $methods;
    }
}
